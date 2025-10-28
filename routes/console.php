<?php

use App\Models\User;
use App\Payments\Actions\Payments\PayAction;
use App\Payments\DTO\CreatePaymentDTO;
use App\Payments\Enum\PaymentMethodEnum;
use App\Payments\Enum\PaymentProvidersEnum;
use App\Services\Slots\Actions\SlotsSyncAction;
use App\ValueObjects\Id;
use Lorisleiva\Actions\Facades\Actions;

Artisan::command('tests', function () {
    PayAction::run(
        new CreatePaymentDTO(
            200,
            PaymentProvidersEnum::ONE_PLAT,
            PaymentMethodEnum::SBP,
            User::find(Id::make('f2b29388-9b6a-4e9f-9cbf-32177422ccbb')),
        )
    );
})->purpose('Display an inspiring quote');

Actions::registerCommandsForAction(SlotsSyncAction::class);

