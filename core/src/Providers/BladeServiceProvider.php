<?php

namespace Jamstackvietnam\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class BladeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::directive('seo', function ($expression) {
            return "hello world";
        });
    }
}
