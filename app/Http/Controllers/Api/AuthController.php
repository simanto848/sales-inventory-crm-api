<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Traits\ApiResponser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponser;

    public function __construct(
        protected AuthService $authService
    ) {}

    public function login(Request $request): JsonResponse
    {
        return $this->success(
            $this->authService->login($request->all()),
            'Successfully logged in'
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());
        return $this->success(null, 'Successfully logged out.');
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->success(
            $request->user(),
            'Profile retrieved successfully'
        );
    }
}
