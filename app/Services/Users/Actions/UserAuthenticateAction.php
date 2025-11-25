<?php

declare(strict_types=1);

namespace App\Services\Users\Actions;

use App\Http\Requests\Api\v1\Auth\AuthenticationRequest;
use App\Models\User;
use FKS\Actions\Action;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserAuthenticateAction extends Action
{
    public function handle(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Неверные учетные данные.'],
            ]);
        }

        $token = $user->createToken($request->device_name ?? 'web')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    public function asController(AuthenticationRequest $request): JsonResponse
    {
        return response()->json($this->handle($request->email, $request->password));
    }
}
