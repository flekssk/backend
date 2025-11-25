<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\ValueObjects\Id;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class ApiKeyAuthenticationMiddleware
{
    public function handle(Request $request, Closure $next): Response|JsonResponse
    {
        $provided = (string) $request->headers->get('X-Bypass-Token', '');

        if ($provided === '') {
            $auth = (string) $request->headers->get('Authorization', '');
            if (Str::startsWith($auth, 'Bearer ')) {
                $provided = trim(Str::substr($auth, 7));
            }
        }

        $expected = (string) config('api_bypass.token', '');

        if ($provided !== '' && $expected !== '' && hash_equals($expected, $provided)) {
            $userId = Id::makeNullable(config('api_bypass.system_user_id'));

            if ($userId !== null) {
                $user = User::find($userId);
                Auth::setUser($user);
            }

            $request->attributes->set('auth_bypassed', true);
            return $next($request);
        }

        // 2) Sanctum — делаем sanctum активным guard'ом, чтобы $request->user() работал далее
        $guard = Auth::guard('sanctum');
        if ($guard->check()) {
            Auth::shouldUse('sanctum');
            $request->setUserResolver(fn () => $guard->user());
            return $next($request);
        }

        throw new AuthenticationException('Unauthenticated.');
    }
}

