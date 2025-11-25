<?php

use App\Payments\Actions\Payments\PaymentsInvalidateAction;
use App\Services\Games\Actions\SlotsSyncAction;
use Firebase\JWT\JWT;
use Lorisleiva\Actions\Facades\Actions;

Artisan::command('tests', function () {

})->purpose('Display an inspiring quote');

Actions::registerCommandsForAction(PaymentsInvalidateAction::class);
Actions::registerCommandsForAction(SlotsSyncAction::class);

\Illuminate\Support\Facades\Schedule::command('payments:invalidate')
    ->everyMinute();

