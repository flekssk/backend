<?php

declare(strict_types=1);

namespace App\Payments\Actions\Payments;

use App\Models\User;
use App\Payments\Enum\PaymentStatusEnum;
use App\Payments\Models\Payment;
use App\Payments\Repositories\PaymentsRepository;
use App\ValueObjects\Id;
use FKS\Actions\Action;
use Illuminate\Contracts\Queue\EntityNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ActivePaymentsGetAction extends Action
{
    public function __construct(private readonly PaymentsRepository $paymentsRepository)
    {
    }

    public function handle(User $user): ?Payment
    {
        $payments = $this->paymentsRepository->getQuery()
            ->select('*')
            ->where('user_id', $user->id)
            ->whereIn('status', [PaymentStatusEnum::PENDING, PaymentStatusEnum::PAYED])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($payments->count() > 1) {
            Log::error(
                'More than one active payment',
                [
                    'user_id' => $user->id->uuid(),
                    'payments' => $payments->pluck('id')->map(fn(Id $id) => $id->uuid()),
                ]
            );
        }

        return $payments->first() ?? throw new NotFoundHttpException();
    }

    public function asController(Request $request): Response|JsonResponse
    {
        return response()->json(
            $this->handle($request->user())?->only() ?? [],
        );
    }
}
