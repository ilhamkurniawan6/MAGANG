<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UmkmController extends Controller
{
    public function __construct(
        protected AdminService $adminService
    ) {}

    /**
     * GET /api/admin/dashboard
     */
    public function dashboard(): JsonResponse
    {
        $dashboardData = $this->adminService->getDashboard();

        return response()->json([
            'success' => true,
            'data' => $dashboardData,
        ]);
    }

    /**
     * GET /api/admin/umkm
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'q']);
        $umkmList = $this->adminService->getUmkmList($filters);

        return response()->json([
            'success' => true,
            'data' => $umkmList,
        ]);
    }

    /**
     * GET /api/admin/umkm/{id}
     */
    public function show(int $id): JsonResponse
    {
        $umkm = $this->adminService->getUmkmDetail($id);

        return response()->json([
            'success' => true,
            'data' => $umkm,
        ]);
    }

    /**
     * POST /api/admin/umkm/{id}/verifikasi
     */
    public function verifikasi(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status_verifikasi' => 'required|in:verified,rejected,pending',
        ]);

        $umkm = $this->adminService->verifikasiUmkm($id, $request->status_verifikasi);

        return response()->json([
            'success' => true,
            'message' => "Status verifikasi UMKM berhasil diperbarui menjadi {$request->status_verifikasi}.",
            'data' => $umkm,
        ]);
    }
}
