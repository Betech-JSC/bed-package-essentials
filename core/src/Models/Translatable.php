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
}
