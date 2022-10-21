<?php

namespace Jamstackvietnam\Core\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Jamstackvietnam\Core\Models\File;
use Jamstackvietnam\Core\Traits\ApiResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MediaController extends Controller
{
    use ApiResponse;

    public $model = File::class;

    public function index()
    {
        if (request()->wantsJson()) {
            return $this->getData();
        }
        return Inertia::render('Media');
    }

    private function getData()
    {
        $query = $this->model::query()
            ->where('creator_type', Admin::class);

        if (request()->has('search')) {
            $query->search(request()->input('search'));
        }
        $query = $query->orderBy('created_at', 'desc');
        $items = $query->paginate(40);
        $items = $items->withQueryString();

        return $items;
    }

    public function store(Request $request)
    {
        $files = $request->file('files');
        $uploader = auth()->guard('admin')->user();
        $fileUploaded = [];
        foreach ($files as $file) {
            $fileUploaded[] = $this->model::storeMedia($file, $uploader);
        }

        return $this->success($fileUploaded);
    }

    public function destroy()
    {
        try {
            DB::beginTransaction();
            $resource = $this->model::findOrFail(request()->input('id'));
            $resource->delete();
            DB::commit();

            return $this->success();
        } catch (\Exception $e) {
            DB::rollBack();
            if (env('app.debug')) throw $e;
            return back()->withError($e->getMessage());
        }
    }

    public function cache($static_id, $year, $month, $day, $filename)
    {
        try {
            $fullPath = $year . '/' . $month . '/' . $day . '/' . $filename;
            $extension = $this->getExtension($filename);
            if ($extension['is_image']) {
                $server = ServerFactory::create([
                    'response' => new LaravelResponseFactory(),
                    'source' => config('filesystems.disks.uploads.root'),
                    'cache' => config('filesystems.disks.uploads.root') . "/cache",
                    'max_image_size' => 1600 * 1600
                ]);

                return $server->getImageResponse($fullPath, request()->all());
            }
            $fullPath = config('filesystems.disks.uploads.root') . '/' . $fullPath;
            if ($extension['is_svg']) {
                $mine = 'image/svg+xml';
            } elseif ($extension['is_video']) {
                $mine = 'video/mp4';
            } elseif ($extension['is_pdf']) {
                $mine = 'application/pdf';
            } else {
                $media = $this->model::where('id', $static_id)->firstOrFail();
                $mine = $media->mime;
            }

            header("Content-Type: $mine");
            header('Accept-Ranges: bytes');
            readfile($fullPath);
        } catch (\Exception $e) {
            throw new NotFoundHttpException();
        }
    }

    private function getExtension($filename)
    {
        $extension = pathinfo($filename)['extension'];
        $isImage =  (bool) in_array($extension, ['jpg', 'jpeg', 'png', 'webp']);
        $isSvg  = (bool) in_array($extension, ['svg']);
        $isVideo =  (bool) in_array($extension, ['mp4']);
        $isPdf =  (bool) in_array($extension, ['pdf']);
        return [
            'is_image' => $isImage,
            'is_svg' => $isSvg,
            'is_video' => $isVideo,
            'is_pdf' => $isPdf,
            'is_valid' => $isImage || $isSvg || $isVideo,
        ];
    }
}
