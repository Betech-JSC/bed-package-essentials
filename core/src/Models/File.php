<?php

namespace JamstackVietnam\Core\Models;

use Image;
use Illuminate\Support\Str;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Support\Facades\Storage;

class File
{
    protected $path;
    protected $disk;
    protected $storage;
    protected $contents;

    public const MAX_SIZE_LIST = [
        'image' => 5,
        'video' => 50,
        'application' => 100,
        'others' => 10,
    ];

    public function __construct($path = '/', $disk = null)
    {
        $this->path = $path;
        $this->disk = $disk ?? 'uploads';
        $this->storage = Storage::disk($this->disk);
    }

    public function items()
    {
        $this->contents = collect($this->storage->listContents($this->path));

        $tree = $this->tree();
        $directories = $this->directories();
        $files = $this->files();

        return compact('tree', 'directories', 'files');
    }

    public function tree()
    {
        $rootPath = $this->storage->path('/');
        $flatItems = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        $tree = [];
        foreach ($flatItems as $item) {
            if (
                !$item->isDir() ||
                $this->firstCharIs($item->getFilename(), '.')
            ) continue;

            $path = [$item->getFilename() => []];

            for ($depth = $flatItems->getDepth() - 1; $depth >= 0; $depth--) {
                $path = [$flatItems->getSubIterator($depth)->current()->getFilename() => $path];
            }
            $tree = array_merge_recursive($tree, $path);
        }

        return $this->transformTree($tree);
    }

    public function transformTree($item, $name = null, $path = null)
    {
        $itemChildren = collect($item)
            ->map(fn ($subItem, $subKey) => $this->transformTree($subItem, $subKey, implode('/', [$path, $subKey])))
            ->filter()
            ->sortBy('path')
            ->keyBy('path')
            ->values()
            ->toArray();

        if (is_null($path)) {
            return [[
                'slug' => Str::slug('/') .  '-' . generate_code(5),
                'name' => 'File Manager',
                'label' => 'File Manager',
                'path' => '/',
                'children' => $itemChildren,
            ]];
        }

        return [
            'slug' => Str::slug($path) .  '-' . generate_code(5),
            'name' => $name,
            'label' => $name,
            'path' => $path,
            'children' => $itemChildren,
        ];
    }

    public function directories()
    {
        return $this->contents
            ->filter(fn ($item) => $item->isDir())
            ->values()
            ->toArray();
    }

    public function files()
    {
        return $this->contents
            ->filter(fn ($item) => $item->isFile())
            ->values()
            ->map(fn ($item) => $this->transformFile($item))
            ->reject(fn ($item) => $this->firstCharIs($item['filename'], '.'))
            ->sortBy('search_name')
            ->keyBy('path');
    }

    public function findOrFail($options = [])
    {
        try {
            $filePath = $this->storage->get($this->path);
            $mimeType = $this->storage->mimeType($this->path);

            if (str_contains($mimeType, 'image/') && $mimeType !== 'image/heic') {
                if (isset($options['cache'])) {
                    $image = Image::cache(function ($image) use ($filePath, $options) {
                        $image->make($filePath);
                        if ($width = $options['w']) {
                            $image->resize($width, null, function ($constraint) {
                                $constraint->aspectRatio();
                            });
                        }
                    });
                    return response()->make(
                        $image,
                        200,
                        ['Content-Type' => $mimeType]
                    );
                }

                $image = Image::make($filePath);

                $format = $options['fm'] ?? 'webp';

                if (isset($options['w'])) {
                    $image->resize($options['w'], null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }

                return $image
                    ->encode($format)
                    ->response();
            }

            return response()->make(
                $filePath,
                200,
                ['Content-Type' => $mimeType]
            );
        } catch (\Exception $exception) {
            logger()->info($exception->getMessage());
            abort(404);
        }
    }

    public function store($files)
    {
        $successFiles = [];
        $failureFiles = [];

        foreach ($files as $file) {
            if ($this->fileValidation($file)) {
                $filePath = $this->storage->putFileAs(
                    $this->path,
                    $file,
                    $file->getClientOriginalName()
                );
                $successFiles[] = static_url($filePath, [], false);
            } else {
                $failureFiles[] = $file->getClientOriginalName();
            }
        }
        return [
            'successFiles' => $successFiles,
            'failureFiles' => $failureFiles,
        ];
    }

    public function storeFromUrl($url)
    {
        try {
            $file = file_get_contents($url);
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($file);

            $extension = explode('/', $mime)[1] ?? 'png';
            if (!in_array($extension, ['png', 'jpeg', 'jpg', 'webp', 'gif', 'tiff'])) {
                logger()->error('Can not store image: ' . $url);
                return false;
            }

            $filename = Str::slug(urldecode(pathinfo($url)['filename'])) . '.' . $extension;

            $this->storage->put($filename, $file);
            return parse_url($this->storage->url($filename))['path'];
        } catch (\Throwable $th) {
            logger()->error('Can not store image: ' . $url);
            logger()->error($th->getMessage());
            return false;
        }
    }

    public function delete($items)
    {
        $deletedItems = [];

        foreach ($items as $item) {
            if (!$this->storage->exists($item['path'])) {
                continue;
            } else {
                if ($item['type'] === 'dir') {
                    $this->storage->deleteDirectory($item['path']);
                } else {
                    $this->storage->delete($item['path']);
                }
            }

            $deletedItems[] = $item;
        }

        return $deletedItems;
    }

    public function folderCreate($name)
    {
        if ($this->storage->exists($name)) {
            return false;
        }

        return (bool) $this->storage->makeDirectory($name);
    }

    private function formatBytes($size)
    {
        $base = log($size) / log(1024);
        $suffix = array("bytes", "KB", "MB", "GB", "TB")[floor($base)];
        return round(pow(1024, $base - floor($base)), 2) .  ' ' . $suffix;
    }

    private function firstCharIs($string, $char)
    {
        return mb_substr($string, 0, 1) === $char;
    }

    private function fileValidation($file)
    {
        $mimeType = $file->getMimeType();
        $maxSize = self::MAX_SIZE_LIST['others'];
        foreach (self::MAX_SIZE_LIST as $key => $size) {
            if (str_contains($mimeType, $key)) {
                $maxSize = $size;
            }
        }

        return $file->getSize() / 1024 / 1024 <= $maxSize;
    }

    private function transformFile($item)
    {
        $metadata = $item->jsonSerialize();
        $filename = basename($metadata['path']);

        return array_merge($metadata, [
            'search_name' => str_replace('-', ' ', Str::slug($filename)),
            'filename' => $filename,
            'extension' => pathinfo($metadata['path'], PATHINFO_EXTENSION),
            'static_url' => $this->storage->url($metadata['path']),
            'formatted_file_size' => $this->formatBytes($metadata['file_size']),
        ]);
    }
}
