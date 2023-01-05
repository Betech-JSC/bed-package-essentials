<?php

use Illuminate\Support\Facades\Route;
use JamstackVietnam\Core\Controllers\FileController;

if (config('app.url') !== config('app.frontend_url')) {
    Route::domain(config('app.static_url'))->get('{path?}', [FileController::class, 'show'])
        ->where('path', '(.*)')->name('files.show');
} else {
    Route::get('static/{path?}', [FileController::class, 'show'])
        ->where('path', '(.*)')->name('files.show');
}
