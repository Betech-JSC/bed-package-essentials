<?php

if (! function_exists('setupSeo')) {
    function setupSeo()
    {
        $seo = settings()->group('general')->all();

        $title = $seo->seo_meta_title;
        $description = $seo->seo_meta_description;
        $canonical = $seo->seo_canonical;
        $robots = $seo->seo_meta_robots;
        $keywords = $seo->seo_meta_keywords;
        $image = $seo->seo_image;

        SEO::setTitle($title);
        SEO::setDescription($description);
        SEO::setCanonical($canonical);
        SEO::setRobots($robots);
        SEO::setKeywords($keywords);
        SEO::opengraph()->addImage($image);
    }
}
