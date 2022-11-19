<?php

use App\Models\Admin;
use Illuminate\Support\Facades\Route;
use JamstackVietnam\Core\Controllers\Auth\NewPasswordController;
use JamstackVietnam\Core\Controllers\Auth\VerifyEmailController;
use JamstackVietnam\Core\Controllers\Auth\RegisteredUserController;
use JamstackVietnam\Core\Controllers\Auth\PasswordResetLinkController;
use JamstackVietnam\Core\Controllers\Auth\ConfirmablePasswordController;
use JamstackVietnam\Core\Controllers\Auth\AuthenticatedSessionController;
use JamstackVietnam\Core\Controllers\Auth\EmailVerificationPromptController;
use JamstackVietnam\Core\Controllers\Auth\EmailVerificationNotificationController;
use Illuminate\Http\Request;

Route::middleware('guest:admin')->name('admin.')->group(function () {
    Route::post('local-login', function (Request $request) {
        auth()->guard('admin')->loginUsingId(Admin::first()->id);
        $request->session()->regenerate();
        return redirect()->route('admin.dashboard');
    })->name('local-login');

    // Route::get('register', [RegisteredUserController::class, 'create'])
    //     ->name('register');

    // Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');
});

Route::middleware('auth:admin')->name('admin.')->group(function () {
    Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});