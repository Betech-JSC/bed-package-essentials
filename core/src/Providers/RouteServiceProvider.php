<?php

namespace Jamstackvietnam\Core\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Artisan;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::addNamespace('frontend', resource_path('Frontend/views'));
        View::addNamespace('backend', resource_path('Backend/views'));

        Model::preventLazyLoading(!app()->isProduction());

        $this->registerRouteModuleMacro();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware(['web', 'frontend'])
                ->namespace($this->namespace)
                ->group(base_path('routes/frontend.php'));

            Route::prefix('admin')
                ->middleware(['web', 'backend'])
                ->namespace($this->namespace)
                ->group(base_path('routes/backend.php'));
        });
    }

    private function registerRouteModuleMacro()
    {
        Router::macro('module', function ($controller, array $options = []) {
            $actions = ['index', 'form', 'store', 'destroy', 'restore'];

            if (isset($options['only'])) {
                $actions = array_intersect($actions, (array) $options['only']);
            }

            if (isset($options['except'])) {
                $actions = array_diff($actions, (array) $options['except']);
            }

            $resource = Str::plural(Str::kebab((str_replace('Controller', '', class_basename($controller)))));

            if (in_array('index', $actions)) {
                Route::get($resource, "$controller@index")->name("$resource.index");
            }

            if (in_array('form', $actions)) {
                Route::get("$resource/form/{id?}", [$controller, 'form'])->name("$resource.form");
            }

            if (in_array('store', $actions)) {
                Route::post("$resource/store/{id?}", "$controller@store")->name("$resource.store");
            }

            if (in_array('destroy', $actions)) {
                Route::post("$resource/destroy/{id}", "$controller@destroy")->name("$resource.destroy");
            }

            if (in_array('restore', $actions)) {
                Route::post("$resource/restore/{id}", [$controller, 'restore'])->name("$resource.restore");
            }

            if (isset($options['appends'])) {
                foreach ($options['appends'] as $action) {
                    Route::any("$resource/$action", "$controller@$action")->name("$resource.$action");
                }
            }
        });
    }
}
