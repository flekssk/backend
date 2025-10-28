<?php

declare(strict_types=1);

namespace App\Services\Users\Actions;

use App\Models\User;
use App\Services\Users\Repositories\UserRepository;
use FKS\Actions\Action;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserGetAction extends Action
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function handle(string $id): ?User
    {
        $user = $this->userRepository->findById($id);

        if ($user === null) {
            throw new NotFoundHttpException();
        }

        return $user;
    }

    public function asController(Request $request): Response|JsonResponse
    {
        return response()->json([
            'user' => $request->user()->load(['playerProfile', 'wallets'])
        ]);
    }
}
