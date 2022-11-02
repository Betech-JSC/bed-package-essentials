<?php

if (!function_exists('setupSeo')) {
    function setupSeo()
    {
        $seo = settings()->group('general')->all();


        $businessName = $seo['general_business_name'] ?? null;
        $separator = $seo['seo_title_separator'] ?? null;

        $site = $seo['seo_site_name'] ?? null;
        $title = $seo['seo_meta_title'] ?? null;
        $description = $seo['seo_meta_description'] ?? null;
        $canonical = $seo['seo_canonical'] ?? null;
        $robots = $seo['seo_meta_robots'] ?? null;
        $keywords = $seo['seo_meta_keywords'] ?? null;
        $image = $seo['seo_image'] ?? null;
        $url = url()->current();

        SEOMeta::setTitleDefault($businessName);
        SEOMeta::setDescription($description);
        SEOMeta::setCanonical($url);
        SEOMeta::setTitleSeparator($separator);
        SEOMeta::addKeyword($keywords);
        SEOMeta::setRobots($robots);
        SEOMeta::setCanonical($url);
        // SEOMeta::addAlternateLanguage($lang, $url);
        // SEOMeta::addAlternateLanguages(array $languages);

        SEO::setTitle($title);
        SEO::setDescription($description);
        SEO::setCanonical($canonical);
        SEO::addImages([$image]);

        SEO::metatags()->setRobots($robots);
        SEO::metatags()->setKeywords($keywords);

        SEO::opengraph()->addImage($image);
        SEO::opengraph()->setUrl($url);
        SEO::opengraph()->setSiteName($site);

        SEO::twitter()->setSite($site);

        SEO::jsonLd()->setTitle($title);
        SEO::jsonLd()->setSite($site);
        SEO::jsonLd()->setDescription($description);
        SEO::jsonLd()->setUrl($url);
        SEO::jsonLd()->setImage($image);
    }
}
