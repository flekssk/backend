<?php

namespace App\Services\Users\Actions;

use App\Http\Controllers\Users\VKAuthRequest;
use App\Models\PlayerProfile;
use App\Models\User;
use Exception;
use FKS\Actions\Action;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Throwable;
use VK\OAuth\VKOAuth;

class VKAuthAction extends Action
{
    public function asController(VKAuthRequest $request): RedirectResponse
    {
        try {
            $accessToken = $request->access_token;
            if (!$accessToken) {
                throw new Exception('Не получен токен доступа (VK ID).');
            }

            $vk = new \VK\Client\VKApiClient();
            $response = $vk->users()->get($accessToken, [
                'user_ids'  => [$request->user_id],
                'fields'    => ['city', 'photo'],
            ]);

            dd($response);

            if ($userInfoResp->failed()) {
                throw new Exception('Не удалось получить информацию о пользователе (VK ID).');
            }

            $info = $userInfoResp->json();
            // Стандартные поля OIDC:
            $vkId = $info['sub'] ?? null; // уникальный ID пользователя
            $email = $info['email'] ?? null;
            $firstName = $info['given_name'] ?? '';
            $lastName = $info['family_name'] ?? '';
            $name = trim($firstName . ' ' . $lastName) ?: 'VK User';
            $avatar = $info['picture'] ?? null;
        } catch (Throwable $e) {
            dd($e);
        }
        dd($vkId);
        $user = PlayerProfile::where('vk_id', $vkId)->first()?->user;

        if (!$user) {
            if ($email) {
                $user = User::where('email', $email)->first();
            }

            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt(Str::random(32)), // одноразовый случайный пароль
                ]);

                $user->playerProfile()
                    ->update([
                        'vk_id' => $vkId,
                        'avatar' => $avatar,
                    ]);
            }
        } else {
            $user->name = $user->name ?: $name;
            if (!$user->email && $email) {
                $user->email = $email;
            }
            $user->save();
        }

        Auth::login($user, remember: true);

        return redirect()->intended('/');
    }
}
