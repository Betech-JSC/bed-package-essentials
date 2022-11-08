<?php

namespace Jamstackvietnam\Core\Models;

use Astrotomic\Translatable\Translatable as AstrotomicTranslatable;
use Illuminate\Database\Eloquent\Builder;

trait Translatable
{
    use AstrotomicTranslatable;

    public function getDefaultLocale(): ?string
    {
        return current_locale();
    }

    public function getTranslationStatusesAttribute()
    {
        $translations = [];
        foreach (config('app.locales') as $locale) {
            $translations[$locale] = $this->hasTranslation($locale);
        }
        return $translations;
    }

    public function scopeActive($query)
    {
        $query = $query->where('status', 'ACTIVE');

        if (!empty(request()->route()->getName())) {
            $locale = current_locale();
            $default_locale = config('app.locale');

            $query = $query->whereHas('translations', function (Builder $q) use ($locale, $default_locale) {
                $q->where('locale', '=', $locale)
                    ->orWhere('locale', '=', $default_locale);
            });
        }

        return $query;
    }

    public function scopeWhereSlug($query, $slug)
    {
        return $query->whereHas('translations', function ($query) use ($slug) {
            $query->where('seo_slug', $slug)->orWhere('slug', $slug);
        });
    }
}
