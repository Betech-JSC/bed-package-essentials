<?php

use Illuminate\Support\Facades\Route;
use JamstackVietnam\Core\Controllers\FileController;

if (!str_contains(config('app.static_url'), config('app.url')) && config('app.url') !== config('app.frontend_url')) {
    Route::get('static/{path?}', [FileController::class, 'show'])
        ->where('path', '(.*)');

    Route::domain(config('app.static_url'))
        ->get('{path?}', [FileController::class, 'show'])
        ->where('path', '(.*)')
        ->name('files.show');
} else {
    Route::get('static/{path?}', [FileController::class, 'show'])
        ->where('path', '(.*)')
        ->name('files.show');
}
