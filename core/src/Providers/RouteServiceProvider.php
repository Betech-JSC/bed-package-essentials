<?php

namespace Jamstackvietnam\Core\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
