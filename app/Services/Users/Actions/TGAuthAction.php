<?php

namespace App\Services\Users\Actions;

use App\Http\Controllers\Users\TGAuthRequest;
use App\Models\PlayerProfile;
use App\Models\User;
use Exception;
use FKS\Actions\Action;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TGAuthAction extends Action
{
    public function asController(TGAuthRequest $request): JsonResponse
    {
        return response()->json($this->handle($request->all()));
    }

    public function handle(array $data): array
    {
        $botToken = config('services.telegram.bot_token');

        if (!$this->isValidTelegramAuth($data, $botToken)) {
            return throw new Exception('Invalid auth', 403);
        }

        // 4) Найти/создать пользователя по telegram_id
        $telegramId = (string)$data['id'];
        $name = trim(
            ($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')
        ) ?: ($data['username'] ?? ('tg_' . $telegramId));

        $email = 'tg_' . $telegramId . '@socia.win';

        $payerProfile = PlayerProfile::where('tg_id', $telegramId)->first();

        if (!$payerProfile) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt(Str::random(32)),
            ]);
            $user->playerProfile()
                ->update([
                    'tg_id' => $telegramId,
                ]);
        } else {
            $payerProfile->avatar = $data['photo_url'] ?? null;

            $payerProfile->save();

            $user = $payerProfile->user;
        }

        Auth::login($user, true);

        $token = $user->createToken('web')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    private function isValidTelegramAuth(array $data, string $botToken): bool
    {
        $checkHash = $data['hash'] ?? '';
        unset($data['hash']);

        ksort($data);
        $dataCheckString = collect($data)
            ->map(fn($v, $k) => $k . '=' . $v)
            ->implode("\n");

        $secretKey = hash('sha256', $botToken, true);
        $calculated = hash_hmac('sha256', $dataCheckString, $secretKey);

        return hash_equals($calculated, $checkHash);
    }
}
