<?php

declare(strict_types=1);

use App\Http\Controllers\Users\AuthController;
use App\Payments\Actions\Payments\PayAction;
use App\Payments\Actions\Payments\PayCallbackAction;
use App\Payments\Actions\UserPaymentProvidersGetAction;
use App\Services\Slots\Actions\SlotsListAction;
use App\Services\Slots\Actions\SlotsProvidersListAction;
use App\Services\Users\Actions\UserGetAction;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'authenticate']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    Route::prefix('slots')->group(function () {
        Route::post('list', SlotsListAction::class);
        Route::prefix('providers')->group(function () {
            Route::post('list', SlotsProvidersListAction::class);
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('payment-providers', UserPaymentProvidersGetAction::class);
            Route::get('', UserGetAction::class);
        });
        Route::prefix('payments')->group(function () {
            Route::post('', PayAction::class);
        });
    });
    Route::post('payments/webhook/{provider}', PayCallbackAction::class);
});
