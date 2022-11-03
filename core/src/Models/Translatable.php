<?php

namespace Jamstackvietnam\Core\Models;

use Astrotomic\Translatable\Translatable as AstrotomicTranslatable;

trait Translatable
{
    use AstrotomicTranslatable;

    public function getDefaultLocale(): ?string
    {
        return session()->get('locale', config('app.locale'));
    }

    public function scopeWhereSlug($query, $slug)
    {
        return $query->whereHas('translations', function ($query) use ($slug) {
            $query->where('slug', $slug)->orWhere('custom_slug', $slug);
        });
    }
}
