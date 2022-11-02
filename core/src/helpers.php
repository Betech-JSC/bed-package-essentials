<?php

if (!function_exists('setupSeo')) {
    function setupSeo()
    {
        $seo = settings()->group('general')->all();
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
        $keywords = $metaPage?->seo_meta_keywords ?? $seo['seo_meta_keywords'] ?? null;
        $image = $metaPage?->seo_image ?? $seo['seo_image'] ?? null;

        SEOMeta::setDescription($description);
        SEOMeta::setCanonical($url);
        SEOMeta::setTitle($title);
        SEOMeta::setTitleDefault($businessName);
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
