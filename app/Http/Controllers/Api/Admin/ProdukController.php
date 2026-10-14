<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function __construct(
        protected AdminService $adminService
    ) {}

    /**
     * GET /api/admin/produk
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'q']);
        $produkList = $this->adminService->getProdukList($filters);

        return response()->json([
            'success' => true,
            'data' => $produkList,
        ]);
    }

    /**
     * GET /api/admin/produk/{id}
     */
    public function show(int $id): JsonResponse
    {
        $produk = $this->adminService->getProdukDetail($id);

        return response()->json([
            'success' => true,
            'data' => $produk,
        ]);
    }

    /**
     * POST /api/admin/produk/{id}/moderasi
     */
    public function moderasi(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak,menunggu_moderasi',
        ]);

        $produk = $this->adminService->moderasiProduk($id, $request->status);

        return response()->json([
            'success' => true,
            'message' => "Status moderasi produk berhasil diubah menjadi {$request->status}.",
            'data' => $produk,
        ]);
    }
}
