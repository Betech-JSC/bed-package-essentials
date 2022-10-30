<?php

namespace Jamstackvietnam\MetaPages\Models;

use Jamstackvietnam\Sitemap\Sitemap;
use Illuminate\Database\Eloquent\Model;

class MetaPage extends Model
{
    public $fillable = [
        'url',
        'meta_title',
        'meta_description'
    ];

    public function modelRules()
    {
        return [
            'all' => [
                'meta_title' => 'required',
                'meta_description' => 'required',
            ],
        ];
    }

    public static function getAll()
    {
        $pages = self::all();

        $storedRoutes = $pages->pluck('url');
        $routes = collect(Sitemap::create()->addStaticRoutes()->tags)
            ->transform(fn ($item) => str_replace(env('APP_URL'), '', $item['url']));

        $diff = $routes->diff($storedRoutes);

        $allPages = $storedRoutes;
        if ($diff->count()) {
            self::insert($diff->transform(fn ($item) => ['url' => $item])->toArray());
            $allPages->merge($routes);
        }

        return $allPages->transform(function ($item) use ($pages) {
            $page = $pages->firstWhere('url', $item);
            return collect([
                'id' => $item,
                'url' => env('APP_URL') . $item,
                'meta_title' => $page?->meta_title,
                'meta_description' => $page?->meta_description,
            ]);
        });
    }
}
