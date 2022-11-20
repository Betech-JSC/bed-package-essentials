<?php

use Illuminate\Support\Facades\Route;
use JamstackVietnam\Core\Controllers\FileController;

Route::get('robots.txt', function () {
    return response(
        settings()->group('robots_txt')->get('robots_txt'),
        200
    )
        ->header('Content-Type', 'text/plain');
});
Route::dynamicRedirect();

Route::get('static/{path?}', [FileController::class, 'show'])->where('path', '(.*)');
