<?php

use App\Http\Controllers\Auth\VKAuthController;
use App\Services\Users\Actions\TGAuthAction;
use App\Services\Users\Actions\VKAuthAction;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('slots', function () {
    return Inertia::render('Slots');
})->name('slots');

Route::get('slots/{provider}', function () {
    return Inertia::render('Slots', ['provider' => request('provider')]);
})->name('slots-provider');

Route::get('play/{slotId}', function () {
    return Inertia::render('Play', ['slotId' => request('slotId')]);
})->name('play');

Route::get('/auth/vk/redirect', [VKAuthController::class, 'redirect'])
    ->name('auth.vk.redirect');

Route::get('/auth/vk/callback', [VKAuthController::class, 'callback'])
    ->name('auth.vk.callback');
