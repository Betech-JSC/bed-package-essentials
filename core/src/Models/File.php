<?php

namespace Jamstackvietnam\Core\Models;

use App\Models\Admin;
use Illuminate\Support\Facades\Storage;
use Jamstackvietnam\Core\Models\BaseModel;
use Nicolaslopezj\Searchable\SearchableTrait;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File as FileFacades;

class File extends BaseModel
{
    use SearchableTrait;

    public $fillable = [
        'disk',
        'path',
        'filename',
        'extension',
        'mime',
        'size',
        'width',
        'height',
        'creator_id',
        'creator_type',
        'editor_id',
        'editor_type',
        'created_at',
        'updated_at',
    ];

    public $deploy = false;

    protected $hidden = [];

    protected $appends = ['image_id', 'image_url', 'static_url'];

    protected $searchable = [
        'columns' => [
            'files.filename' => 10,
        ],
    ];

    protected static function booted()
    {
        static::deleted(function ($file) {
            $path = $file->getRawOriginal('path');
            Storage::disk($file->disk)->delete($path);
            Storage::deleteDirectory($file->disk . '/cache/' . $path);
        });
    }

    public function getImageIdAttribute()
    {
        return $this->id;
    }

    public function getImageUrlAttribute()
    {
        if (!is_null($this->path)) {
            return $this->id . '/' . $this->path;
        }
        return null;
    }

    public function getStaticUrlAttribute()
    {
        if (!is_null($this->image_url)) {
            return static_url($this->image_url);
        }
        return null;
    }

    public function getAbsoluteUrlAttribute()
    {
        if (!is_null($this->image_url)) {
            return Storage::disk($this->disk)->url($this->image_url);
        }
        return null;
    }

    public static function storeMedia($file, $uploader)
    {
        $filename = $file->getClientOriginalName();
        $path = Storage::disk('uploads')->putFileAs(date('Y/m/d'), $file, $filename);

        $media = [
            'disk' => 'uploads',
            'path' => $path,
            'filename' => $filename,
            'extension' => $file->guessClientExtension(),
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'creator_id' => $uploader->id,
            'creator_type' => get_class($uploader),
            'editor_id' => $uploader->id,
            'editor_type' => get_class($uploader)
        ];

        if (
            substr($file->getMimeType(), 0, 5) == 'image' &&
            $media['extension'] !== 'svg'
        ) {
            $imageSize = getimagesize($file);
            $media['width'] = $imageSize[0];
            $media['height'] = $imageSize[1];
        }

        return self::create($media);
    }

    public static function storeMediaFromUrl($url, $uploader = null, $filename = null)
    {
        try {
            $uploader = $uploader ? $uploader : Admin::first();

            $file = file_get_contents($url);
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($file);
            $extension = explode('/', $mime)[1] ?? 'png';
            if (!in_array($extension, ['png', 'jpeg', 'jpg', 'webp', 'gif', 'tiff'])) {
                logger('-------------------');
                logger($url);
                logger('-------------------');
                return;
            }
            $filename = Str::slug($filename ?? urldecode(pathinfo($url)['filename'])) . '.' . $extension;
            $path = date('Y/m/d') . "/$filename";
            $fullpath = config('filesystems.disks.uploads.root') .  "/" . $path;

            $dirname = pathinfo($fullpath)['dirname'];
            if (!FileFacades::isDirectory($dirname)) {
                FileFacades::makeDirectory($dirname, 0777, true, true);
            }

            file_put_contents($fullpath, $file);

            $media = [
                'disk' => 'uploads',
                'path' => $path,
                'filename' => $filename,
                'extension' => $extension,
                'mime' => $mime,
                'size' => null,
                'creator_id' => $uploader->id,
                'creator_type' => get_class($uploader),
                'editor_id' => $uploader->id,
                'editor_type' => get_class($uploader)
            ];

            return static::create($media);
        } catch (\Exception $e) {
            logger('-------- storeMediaFromUrl --------');
            logger($url);
            throw $e;
            return null;
        }
    }
}
