<?php

use Illuminate\Support\Facades\Route;
use Modules\Todo\Http\Controllers\TodoController;
use Modules\Todo\Http\Middlewares\TodoBelongsToUser;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('v1/todos')->group(function () {
        Route::get('stats', [TodoController::class, 'stats']);
        Route::get('', [TodoController::class, 'index']);
        Route::post('', [TodoController::class, 'store']);

        Route::group(['prefix' => '{todo}', 'middleware' => TodoBelongsToUser::class], function () {
            Route::get('', [TodoController::class, 'show']);
            Route::put('', [TodoController::class, 'update']);
            Route::delete('', [TodoController::class, 'destroy']);
            Route::patch('toggle', [TodoController::class, 'toggle']);
        });
    });
});
