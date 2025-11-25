<?php

declare(strict_types=1);

use App\Http\Controllers\Users\AuthController;
use App\Payments\Actions\Payments\ActivePaymentsGetAction;
use App\Payments\Actions\Payments\PayAction;
use App\Payments\Actions\Payments\PayCallbackAction;
use App\Payments\Actions\Payments\PaymentAwaitAction;
use App\Payments\Actions\Payments\PaymentCancelAction;
use App\Payments\Actions\Payments\PaymentPayedAction;
use App\Payments\Actions\UserPaymentProvidersGetAction;
use App\Payments\Actions\Withdraws\WithdrawAction;
use App\Services\Games\Actions\GameLastWinAction;
use App\Services\Games\Actions\Mobule\MobuleHandleCallbackAction;
use App\Services\Games\Actions\SlotsGetAction;
use App\Services\Games\Actions\SlotsListAction;
use App\Services\Games\Actions\SlotsProvidersListAction;
use App\Services\Games\Actions\UserLatestGamesAction;
use App\Services\Users\Actions\TGAuthAction;
use App\Services\Users\Actions\UserAuthenticateAction;
use App\Services\Users\Actions\UserGetAction;
use App\Services\Users\Actions\VKAuthAction;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/authenticate', UserAuthenticateAction::class);
        Route::post('/telegram', TGAuthAction::class)->name('auth.telegram');
        Route::post('/vk', VKAuthAction::class)->name('auth.vk');

        Route::middleware('api.token_or_sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    Route::prefix('slots')->group(function () {
        Route::post('list', SlotsListAction::class);
        Route::post('last-win-logs', GameLastWinAction::class);
        Route::prefix('providers')->group(function () {
            Route::post('list', SlotsProvidersListAction::class);
        });
        Route::middleware('api.token_or_sanctum')->group(function () {
            Route::get('{slotId}', SlotsGetAction::class);
        });
        Route::prefix('callback')->group(function () {
            Route::post('mobule/{method}', MobuleHandleCallbackAction::class);
        });
    });

    Route::middleware('api.token_or_sanctum')->group(function () {
        Route::prefix('user')->group(function () {
            Route::prefix('slots')->group(function () {
                Route::post('latest', UserLatestGamesAction::class);
            });
            Route::get('payment-providers', UserPaymentProvidersGetAction::class);
            Route::get('', UserGetAction::class);

            Route::prefix('payments')->group(function () {
                Route::post('{paymentId}/payed', PaymentPayedAction::class);
                Route::post('{paymentId}/cancel', PaymentCancelAction::class);
                Route::post('{paymentId}/await', PaymentAwaitAction::class);
            });

        });
        Route::prefix('payments')->group(function () {
            Route::post('active', ActivePaymentsGetAction::class);
            Route::post('', PayAction::class);
        });
        Route::prefix('withdraw')->group(function () {
            Route::post('', WithdrawAction::class);
        });
    });
    Route::post('payments/webhook/{provider}', PayCallbackAction::class);
});
