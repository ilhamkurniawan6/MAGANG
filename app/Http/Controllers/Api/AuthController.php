<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUmkmRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * POST /api/login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->email,
            $request->password
        );

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'token' => $result['token'],
            'user' => $result['user'],
        ]);
    }

    /**
     * POST /api/register-umkm
     */
    public function registerUmkm(RegisterUmkmRequest $request): JsonResponse
    {
        $result = $this->authService->registerUmkm($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran UMKM berhasil dikirim. Akun Anda sedang menunggu verifikasi tim KADIN.',
            'token' => $result['token'],
            'user' => $result['user'],
            'umkm' => $result['umkm'],
        ], 201);
    }

    /**
     * POST /api/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * GET /api/me
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['role', 'umkm.kategori']);

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }
}
