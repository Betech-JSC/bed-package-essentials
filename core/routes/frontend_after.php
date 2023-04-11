<?php

use Illuminate\Support\Facades\Route;
use JamstackVietnam\Contact\Controllers\ContactController;

Route::get('error', fn () => inertia('Error'))->name('error');
if (!app()->environment('production')) {
    Route::get('demo', fn () => inertia('Demo'))->name('demo');
    Route::get('routes', function () {
        $routes = collect(Route::getRoutes())->map(function ($route) {
            return [
                'methods' => $route->methods(),
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'action' => $route->getActionName()
            ];
        });
        return inertia('Route', ['routes' => $routes]);
    })->name('routes');
}

Route::localized(function () {
    Route::get('/{path}', fn () => abort(404))->where('path', '^(?!admin).*$');
});

Route::get('contacts/reverse2/{id}', [ContactController::class, 'reverse'])->name('contacts.reverse');
