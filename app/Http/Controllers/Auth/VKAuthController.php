<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PlayerProfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class VKAuthController extends Controller
{
    public function redirect()
    {
        // Можно добавить scope: email, photos и т.п.
        return Socialite::driver('vkontakte')
            ->scopes(['email'])
            ->redirect();
    }

    public function callback()
    {
        try {
            /** @var \Laravel\Socialite\Contracts\User $vkUser */
            $vkUser = Socialite::driver('vkontakte')->user();
        } catch (Throwable $e) {
            // Можно логировать
            report($e);
            return redirect('/')->with('error', 'Ошибка авторизации через VK');
        }

        $vkId    = $vkUser->getId();
        $email   = $vkUser->getEmail();          // может быть null!
        $name    = $vkUser->getName() ?: 'VK User';
        $avatar  = $vkUser->getAvatar();

        // Ищем по vk_id в PlayerProfile (как у тебя в логике)
        $user = PlayerProfile::where('vk_id', $vkId)->first()?->user;

        if (!$user && $email) {
            // Если есть email — пробуем найти существующего юзера
            $user = User::where('email', $email)->first();
        }

        if (!$user) {
            // Создаём нового
            $user = User::create([
                'name'     => $name,
                'email'    => $email,
                'password' => bcrypt(Str::random(32)),
            ]);

            // Если у тебя profile создаётся через observer/factory —
            // тут либо create, либо updateOrCreate
            $user->playerProfile()->updateOrCreate([], [
                'vk_id'  => $vkId,
                'avatar' => $avatar,
            ]);
        } else {
            // Обновляем аккуратно
            $dirty = false;

            if (!$user->name && $name) {
                $user->name = $name;
                $dirty = true;
            }

            if (!$user->email && $email) {
                $user->email = $email;
                $dirty = true;
            }

            if ($dirty) {
                $user->save();
            }

            $user->playerProfile()->updateOrCreate([], [
                'vk_id'  => $vkId,
                'avatar' => $avatar,
            ]);
        }

        Auth::login($user, remember: true);

        return redirect()->intended('/');
    }
}
