<?php

use Illuminate\Support\Facades\Route;
use JamstackVietnam\Core\Controllers\RoleController;
use JamstackVietnam\Core\Controllers\AdminController;
use JamstackVietnam\Core\Controllers\SettingController;
use JamstackVietnam\Core\Controllers\FileController;
use JamstackVietnam\Core\Controllers\HelperController;
use JamstackVietnam\MetaPage\Controllers\MetaPageController;
use JamstackVietnam\Redirect\Controllers\RedirectController;
use JamstackVietnam\Translation\Controllers\TranslationController;

Route::middleware(['auth:admin'])->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });

    Route::get('/dashboard', function () {
        return inertia('Dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::module(AdminController::class);
    Route::put('admins', [AdminController::class, 'updateInformation'])->name('updateInformation');
    Route::module(RoleController::class);
    Route::module(SettingController::class);
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::module(MetaPageController::class);
        Route::module(RedirectController::class);
    });
    Route::module(TranslationController::class);

    Route::module(FileController::class, ['only' => ['index', 'form', 'store', 'destroy']]);
    Route::post('folders/create', [FileController::class, 'folderCreate'])->name('files.folders.create');

    Route::post('model-data', [HelperController::class, 'getModelData'])->name('helper.model-data');
    Route::get('logs', [HelperController::class, 'getLogs'])->name('helper.logs');
});

require __DIR__ . '/auth.php';