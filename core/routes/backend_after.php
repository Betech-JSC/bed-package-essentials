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
        return redirect()->route('admin.dashboard.index');
    });

    Route::get('/dashboard', function () {
        return inertia('Dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard.index');

    Route::module(AdminController::class);
    Route::put('admins', [AdminController::class, 'changePassword'])->name('admins.changePassword');
    Route::module(RoleController::class);

    if (config('core.setting.enable')) {
        Route::module(SettingController::class);

        Route::prefix('settings')->name('settings.')->group(function () {
            if (config('core.setting.meta_page.enable')) {
                Route::module(MetaPageController::class);
            }
            if (config('core.setting.redirect.enable')) {
                Route::module(RedirectController::class);
            }
        });
    }

    if (config('core.translation.enable')) {
        Route::module(TranslationController::class);
    }

    Route::module(FileController::class, ['only' => ['index', 'form', 'store', 'destroy']]);
    Route::post('folders/create', [FileController::class, 'folderCreate'])->name('files.folders.create');

    Route::post('model-data', [HelperController::class, 'getModelData'])->name('helper.model-data');

    Route::get('logs', [HelperController::class, 'getLogs'])->name('helper.logs');
});

require __DIR__ . '/auth.php';
