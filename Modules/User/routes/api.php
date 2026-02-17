<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\LoginController;
use Modules\User\Http\Controllers\LogoutController;
use Modules\User\Http\Controllers\RefreshController;
use Modules\User\Http\Controllers\RegisterController;
use Modules\User\Http\Controllers\AuthController;

Route::group(['prefix' => 'v1/auth'], function () {
    Route::post('registration', [RegisterController::class, 'register'])->middleware('throttle:auth-register');
    Route::post('login', [LoginController::class, 'login'])->middleware('throttle:auth-login');
    Route::post('refresh', [RefreshController::class, 'refresh'])->middleware('throttle:auth-refresh');

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('info', [AuthController::class, 'show']);
        Route::post('logout', [LogoutController::class, 'logout'])->middleware('throttle:auth-logout');
        Route::get('check', [RefreshController::class, 'check']);
        Route::post('revoke-refresh-tokens', [RefreshController::class, 'revoke'])->middleware('throttle:auth-revoke');
    });
});

