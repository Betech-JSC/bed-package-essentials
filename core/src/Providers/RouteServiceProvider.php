<?php

namespace JamstackVietnam\Core\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Model;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Model::preventLazyLoading();

        if (app()->isProduction()) {
            Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
                $class = get_class($model);

                info("Attempted to lazy load [{$relation}] on model [{$class}].");
            });
        }

        View::addNamespace('frontend', resource_path('Frontend/views'));
        View::addNamespace('backend', resource_path('Backend/views'));

        $this->routes(function () {
            // Route::prefix('api')
            //     ->middleware('api')
            //     ->namespace($this->namespace)
            //     ->group(base_path('routes/api.php'));

            Route::domain(env('app.api_url'))
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::domain(env('app.static_url'))
                ->namespace($this->namespace)
                ->group(base_path('routes/static.php'));

            Route::middleware(['web', 'frontend'])
                ->namespace($this->namespace)
                ->group(package_path('core/routes/frontend.php'));

            Route::prefix('admin')
                ->middleware(['web', 'backend'])
                ->namespace($this->namespace)
                ->group(package_path('core/routes/backend.php'));
        });
    }
}
