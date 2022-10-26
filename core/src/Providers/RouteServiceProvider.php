<?php

namespace Jamstackvietnam\Core\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\View;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Model::preventLazyLoading(!app()->isProduction());

        View::addNamespace('frontend', resource_path('Frontend/views'));
        View::addNamespace('backend', resource_path('Backend/views'));

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware(['web', 'frontend'])
                ->namespace($this->namespace)
                ->group(base_path('routes/frontend.php'));

            Route::prefix('admin')->name('admin.')
                ->middleware(['web', 'backend'])
                ->namespace($this->namespace)
                ->group(base_path('routes/backend.php'));
        });
    }
}
