<?php

if (!function_exists('setupSeo')) {
    function setupSeo()
    {
        $seo = settings()->group('general')->all();

        $site = $seo['seo_site_name'] ?: null;
        $title = $seo['seo_meta_title'] ?: null;
        $description = $seo['seo_meta_description'] ?: null;
        $canonical = $seo['seo_canonical'] ?: null;
        $robots = $seo['seo_meta_robots'] ?: null;
        $keywords = $seo['seo_meta_keywords'] ?: null;
        $image = $seo['seo_image'] ?: null;
        $url = url()->current();

        SEO::setTitle($title);
        SEO::setDescription($description);
        SEO::setCanonical($canonical);
        SEO::addImages([$image]);

        SEO::metatags()->setTitleDefault($title);
        SEO::metatags()->setRobots($robots);
        SEO::metatags()->setKeywords($keywords);

        SEO::opengraph()->addImage($image);
        SEO::opengraph()->setUrl($url);
        SEO::opengraph()->setSiteName($site);

        SEO::twitter()->setSite($site);

        SEO::jsonLd()->setTitle($type);
        SEO::jsonLd()->setSite($type);
        SEO::jsonLd()->setDescription($description);
        SEO::jsonLd()->setUrl($url);
        SEO::jsonLd()->setImage($image);
    }
}
