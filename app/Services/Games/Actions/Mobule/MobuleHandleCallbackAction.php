<?php

declare(strict_types=1);

namespace App\Services\Games\Actions\Mobule;

use App\Currencies\Enums\CurrenciesEnum;
use App\Models\GameLog;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Games\Enums\GameTypeEnum;
use App\Services\Games\Events\SlotWinEvent;
use App\Services\Games\Models\GameSession;
use App\ValueObjects\Id;
use Exception;
use FKS\Actions\Action;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class MobuleHandleCallbackAction extends Action
{
    public function asController(string $method, Request $request): JsonResponse|Response
    {
        Log::info('Module callback ' . $method);
        try {
            return match ($method) {
                'trx.cancel' => $this->trxCancel($request),
                'trx.complete' => $this->trxComplete($request),
                'check.session' => $this->checkSession($request),
                'check.balance' => $this->checkBalance($request),
                'withdraw.bet' => $this->userBet($request),
                'deposit.win' => $this->userWin($request),
                default => throw new Exception("Unknown method"),
            };
        } catch (Throwable $e) {
            Log::error('Ошибка в callback: ' . $e->getMessage());
            return response()->json(['message' => 'Внутренняя ошибка сервера'], 500);
        }
    }

    private function trxCancel($data)
    {
        return response()->json(['status' => 200]);
    }

    private function trxComplete($data): JsonResponse
    {
        return response()->json(['status' => 200]);
    }

    private function checkSession($data): JsonResponse
    {
        Log::info('Check session ' . $data->session);
        if (!$data->session) {
            return response()->json(['status' => 404, 'method' => 'check.session', 'message' => 'Unknown session']);
        }
        $slotSession = GameSession::find(Id::make($data->session));
        if (!$slotSession) {
            Log::error('Session not found ' . $data->session);
            return response()->json(['status' => 404, 'method' => 'check.session', 'message' => 'Unknown session']);
        }
        $user = User::find(Id::make($slotSession->user_id));
        if (!$user) {
            Log::error('Session user not found ' . $data->session);
            return response()->json(['status' => 404, 'method' => 'check.session', 'message' => 'Unknown user']);
        }

        return response()->json(
            [
                'status' => 200,
                'method' => 'check.session',
                'response' => [
                    'id_player' => $user->id->uuid(),
                    'id_group' => 'default',
                    'balance' => round($user->getWallet()->balance * 100)
                ]
            ]
        );
    }

    private function checkBalance($data): JsonResponse
    {
        if (!$data->session) {
            return response()->json(['status' => 404, 'method' => 'check.balance', 'message' => 'Unknown session']);
        }
        $slotSession = GameSession::find($data->session);
        if (!$slotSession) {
            return response()->json(['status' => 404, 'method' => 'check.balance', 'message' => 'Unknown session']);
        }
        $user = User::find($slotSession->user_id);
        if (!$user) {
            return response()->json(['status' => 404, 'method' => 'check.balance', 'message' => 'Unknown user']);
        }
        return response()->json(
            [
                'status' => 200,
                'method' => 'check.balance',
                'response' => ['currency' => 'RUB', 'balance' => round($user->getWallet()->balance * 100)]
            ]
        );
    }

    public function userBet($data): JsonResponse
    {
        if (!$data->session) {
            return response()->json(['status' => 404, 'method' => 'withdraw.bet', 'message' => 'Unknown session']);
        }
        $slotSession = GameSession::find($data->session);

        if (!$slotSession) {
            return response()->json(['status' => 404, 'method' => 'withdraw.bet', 'message' => 'Unknown session']);
        }
        $user = User::find($slotSession->user_id);

        if (!$user) {
            return response()->json(['status' => 404, 'method' => 'withdraw.bet', 'message' => 'Unknown user']);
        }
        $amount = $data->amount / 100;
        $wallet = $user->getWallet();

        if ($wallet->balance < ($amount)) {
            return response()->json(['status' => 404, 'method' => 'withdraw.bet', 'message' => 'Fail balance']);
        }

        $balance = $this->userDebitUpdateAmount($user, $wallet, $slotSession, $amount);

        return response()->json(
            [
                'status' => 200,
                'method' => 'withdraw.bet',
                'response' => ['currency' => 'RUB', 'balance' => round($balance * 100)]
            ]
        );
    }

    public function userWin($data)
    {
        if (!$data->session) {
            return response()->json(['status' => 404, 'method' => 'deposit.win', 'message' => 'Unknown session']);
        }
        $slotSession = GameSession::find(Id::make($data->session));

        if (!$slotSession) {
            return response()->json(['status' => 404, 'method' => 'deposit.win', 'message' => 'Unknown session']);
        }
        $user = User::find($slotSession->user_id);
        if (!$user) {
            return response()->json(['status' => 404, 'method' => 'deposit.win', 'message' => 'Unknown user']);
        }
        $amount = $data->amount / 100;
        Log::info('user win '. $amount .' '. $user->id->uuid() .' '. $slotSession->id);
        $balance = $this->userCreditUpdateAmount($user, $user->getWallet(), $slotSession, $amount);

        if ($amount > 0) {
            SlotWinEvent::dispatch(
                $slotSession->game->getName(),
                $slotSession->game->getImage(),
                $amount,
                $user->name,
            );
        }

        return response()->json(
            [
                'status' => 200,
                'method' => 'deposit.win',
                'response' => ['currency' => 'RUB', 'balance' => round($balance * 100)]
            ]
        );
    }

    public function userDebitUpdateAmount(User $user, Wallet $wallet, GameSession $session, $debitAmount)
    {
        GameLog::create([
            'user_id' => $wallet->user_id,
            'game_session_id' => $session->id,
            'profit' => -$debitAmount,
        ]);

        if ($user->playerProfile->wager > 0) {
            $user->playerProfile->wager -= $debitAmount;
        }

        if ($user->playerProfile->wager < 0) {
            $user->playerProfile->wager = 0;
        }

        $wallet->balance -= $debitAmount;

        $user->save();
        $wallet->save();
        $wallet->refresh();
        $user->refresh();

        return $wallet->balance;
    }

    public function userCreditUpdateAmount(User $user, Wallet $wallet, GameSession $session, $credit_amount)
    {
        if ($credit_amount > 0) {
            GameLog::create([
                'user_id' => $wallet->user_id,
                'game_session_id' => $session->id,
                'profit' => $credit_amount
            ]);

            if (!(Cache::has('user.' . $user->id . '.historyBalance'))) {
                Cache::put('user.' . $user->id . '.historyBalance', '[]');
            }

            $hist_balance = array(
                'user_id' => $user->id,
                'profit' => -$credit_amount,
            );

//            $casheHistUser = Cache::get('user.' . $user->id . '.historyBalance');
//            $casheHistUser = json_decode($casheHistUser);
//            $casheHistUser[] = $hist_balance;
//            $casheHistUser = json_encode($casheHistUser);
//            Cache::put('user.' . $user->id . '.historyBalance', $casheHistUser);
//
//            $slot = Slot::where('external_id', (string) $user->current_id)->first();
//            Redis::publish('slotsHistory', json_encode([
//                'id' => $slot->id,
//                'game_id' => $user->current_id,
//                'image' => SlotServiceFacade::getImage($slot) ?? '/assets/image/slots/' . implode('', explode(' ', $slot->title)) . '.jpg',
//                'slot_name' => $slot->title,
//                'username' => $user->username,
//                'coef' => number_format(($credit_amount / $user->current_bet), 2),
//                'win' => $credit_amount
//            ]));

        }

        $wallet->balance += $credit_amount;
        $user->save();
        $wallet->save();
        $wallet->refresh();

        return $wallet->balance;
    }

}
