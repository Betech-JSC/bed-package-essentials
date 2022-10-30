<?php

namespace Jamstackvietnam\MetaPages\Models;

use Jamstackvietnam\Sitemap\Sitemap;
use Illuminate\Database\Eloquent\Model;

class MetaPage extends Model
{
    public $fillable = [
        'url',

        'seo_meta_title',
        'seo_slug',
        'seo_meta_description',
        'seo_meta_keywords',
        'seo_meta_robots',
        'seo_canonical',
        'seo_image',
        'seo_schemas',

        'inject_head',
        'inject_body_start',
        'inject_body_end'
    ];

    public function modelRules()
    {
        return [
            'all' => [
                'seo_meta_title' => 'required',
                'seo_meta_description' => 'required',
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
                'seo_meta_title' => $page?->seo_meta_title,
                'seo_meta_description' => $page?->seo_meta_description,
            ]);
        });
    }
}