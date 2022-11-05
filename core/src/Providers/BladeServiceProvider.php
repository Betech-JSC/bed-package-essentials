<?php

namespace Jamstackvietnam\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class BladeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::directive('seo', function ($seo) {
            $seo = $seo ?? settings()->group('general')->all();
            $url = url()->current();

            $relativeUrl = str_replace(env('APP_URL'), '', $url);
            $metaPage = MetaPage::where('url', $relativeUrl)->first();

            $businessName = $seo['general_business_name'] ?? null;
            $separator = $seo['seo_title_separator'] ?? null;

            $site = $seo['seo_site_name'] ?? null;
            $canonical = $seo['seo_canonical'] ?? null;
            $title = $metaPage?->seo_meta_title ?? $seo['seo_meta_title'] ?? null;
            $description = $metaPage?->seo_meta_description ?? $seo['seo_meta_description'] ?? null;
            $robots = $metaPage?->seo_meta_robots ?? $seo['seo_meta_robots'] ?? null;
            $image = $metaPage?->seo_image ?? $seo['seo_image'] ?? null;

            $keywords = $seo['seo_meta_keywords'] ?? null;
            if ($metaPage?->seo_meta_keywords) {
                $keywords .= ',' . $metaPage?->seo_meta_keywords;
            }

            SEOMeta::setDescription($description);
            SEOMeta::setCanonical($url);
            SEOMeta::setTitle($title);
            SEOMeta::setTitleDefault($businessName);
            SEOMeta::setTitleSeparator($separator);
            $keywords && SEOMeta::addKeyword($keywords);
            SEOMeta::setRobots($robots);
            SEOMeta::setCanonical($url);

            SEO::setTitle($title);
            SEO::setDescription($description);
            SEO::setCanonical($canonical);
            SEO::addImages([$image]);

            SEO::metatags()->setRobots($robots);
            $keywords && SEO::metatags()->setKeywords($keywords);

            SEO::opengraph()->addImage($image);
            SEO::opengraph()->setUrl($url);
            SEO::opengraph()->setSiteName($site);

            SEO::twitter()->setSite($site);

            SEO::jsonLd()->setTitle($title);
            SEO::jsonLd()->setSite($site);
            SEO::jsonLd()->setDescription($description);
            SEO::jsonLd()->setUrl($url);
            SEO::jsonLd()->setImage($image);
        });
    }
}
