<?php

declare(strict_types=1);

namespace App\Payments\Actions\Payments;

use App\Models\User;
use App\Payments\Enum\PaymentSourceEnum;
use App\Payments\Enum\PaymentStatusEnum;
use App\Payments\Models\Payment;
use App\ValueObjects\Id;
use Exception;
use FKS\Actions\Action;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class PaymentReleaseAction extends Action
{
    public $commandSignature = 'accept:payment {paymentId : ID платежа для подтверждения}';

    public function handle(Id|string $paymentId): void
    {
        $payment = Payment::find(Id::make($paymentId));

        if (!$payment) {
            throw new Exception("Платёж #{$paymentId} не найден.");
        }

//        if (
//            $payment->payment_source === PaymentSourceEnum::STIMULE
//        ) {
//            $stimulePaymentId = $payment === null
//                ? $paymentId
//                : $payment->metadata->where('metadata_key', 'stimule_payment_id')->first()?->metadata_value;
//
//            if ($stimulePaymentId !== null) {
//                $client = new Client([
//                    'verify' => false,
//                    'base_uri' => 'https://fks-test.online/api/',
//                ]);
//
//                $client->post("v1/stimule/payments/{$stimulePaymentId}/release");
//            }
//
//            return;
//        }

        $user = User::find($payment->user_id);
        if (!$user) {
            throw new Exception("Пользователь #{$payment->user_id} не найден.");
        }

        $payment->status = PaymentStatusEnum::SUCCESS;
        $payment->save();

        $payment->user->playerProfile->wager += $payment->amount + config('services.base_wager', 5);
        $payment->user->playerProfile->save();

        $payment->user->getWallet()->increment('balance', $payment->amount + $payment->amount * 0.5);
    }

    public function asController(Request $request): Response
    {
        $this->handle((int) $request->route('paymentId'));

        return response()->noContent();
    }
}
