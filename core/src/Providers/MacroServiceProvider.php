<?php

namespace Jamstackvietnam\Core\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Illuminate\Database\Schema\Blueprint;

class MacroServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $this->registerMacroBlueprint();
        $this->registerMacroRoute();
    }

    private function registerMacroBlueprint()
    {
        Blueprint::macro('addTime', function () {
            $this->unsignedBigInteger('created_by')->index();
            $this->unsignedBigInteger('updated_by')->nullable()->index();
            $this->unsignedBigInteger('deleted_by')->nullable()->index();

            $this->timestamps();
            $this->softDeletes();
        });

        Blueprint::macro('addSeo', function () {
            $this->text('custom_slug')->nullable();
            $this->string('meta_title')->nullable();
            $this->string('meta_description')->nullable();
            $this->string('meta_keywords')->nullable();
            $this->string('meta_robots')->nullable();
            $this->text('meta_graph')->nullable();
        });

        Blueprint::macro('addStatus', function ($default = 'ACTIVE') {
            $this->string('status')->default($default);
        });
    }

    private function registerMacroRoute()
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
