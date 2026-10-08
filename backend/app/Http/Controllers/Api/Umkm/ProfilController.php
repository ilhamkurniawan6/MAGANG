<?php

namespace App\Http\Controllers\Api\Umkm;

use App\Http\Controllers\Controller;
use App\Http\Requests\UmkmProfilRequest;
use App\Services\UmkmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function __construct(
        protected UmkmService $umkmService
    ) {}

    /**
     * GET /api/umkm/dashboard
     */
    public function dashboard(Request $request): JsonResponse
    {
        $dashboardData = $this->umkmService->getDashboard($request->user());

        return response()->json([
            'success' => true,
            'data' => $dashboardData,
        ]);
    }

    /**
     * GET /api/umkm/profil
     */
    public function show(Request $request): JsonResponse
    {
        $profil = $this->umkmService->getProfil($request->user());

        return response()->json([
            'success' => true,
            'data' => $profil,
        ]);
    }

    /**
     * POST/PUT /api/umkm/profil
     */
    public function update(UmkmProfilRequest $request): JsonResponse
    {
        $logoFile = $request->file('logo');
        $umkm = $this->umkmService->updateProfil($request->user(), $request->validated(), $logoFile);

        return response()->json([
            'success' => true,
            'message' => 'Profil UMKM berhasil diperbarui.',
            'data' => $umkm,
        ]);
    }
}
