<?php

if (! function_exists('setupSeo')) {
    function setupSeo()
    {
        $seo = settings()->group('general')->all();

        $title = $seo['seo_meta_title'] ?: null;
        $description = $seo['seo_meta_description'] ?: null;
        $canonical = $seo['seo_canonical'] ?: null;
        $robots = $seo['seo_meta_robots'] ?: null;
        $keywords = $seo['seo_meta_keywords'] ?: null;
        $image = $seo['seo_image'] ?: null;

        SEO::setTitle($title);
        SEO::setDescription($description);
        SEO::setCanonical($canonical);
        SEO::setRobots($robots);
        SEO::setKeywords($keywords);
        SEO::opengraph()->addImage($image);
    }
}
