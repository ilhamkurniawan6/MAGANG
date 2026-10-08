<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Services\SkorLestariService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkorLestariController extends Controller
{
    public function __construct(
        protected SkorLestariService $skorService
    ) {}

    /**
     * GET /api/admin/skor-lestari
     * Daftar UMKM diurutkan dari skor terendah untuk prioritas pembinaan
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['level', 'q']);
        $list = $this->skorService->getPrioritasPendampingan($filters);

        return response()->json([
            'success' => true,
            'data' => $list,
        ]);
    }

    /**
     * POST /api/admin/umkm/{id}/catatan-pembinaan
     */
    public function storeCatatan(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'jenis' => 'required|in:kunjungan,follow_up,pelatihan',
            'catatan' => 'required|string',
            'tanggal' => 'nullable|date',
        ]);

        $umkm = Umkm::findOrFail($id);
        $catatan = $this->skorService->tambahCatatanPembinaan($umkm, $request->user(), $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Catatan pembinaan berhasil ditambahkan dan Skor Lestari telah diperbarui.',
            'data' => $catatan,
            'umkm' => $umkm->fresh(),
        ], 201);
    }

    /**
     * GET /api/admin/umkm/{id}/catatan-pembinaan
     */
    public function getCatatan(int $id): JsonResponse
    {
        $umkm = Umkm::with(['catatanPembinaan.admin' => function ($q) {
            $q->latest();
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $umkm->catatanPembinaan,
        ]);
    }

    /**
     * POST /api/admin/umkm/{id}/verifikasi-praktik
     */
    public function verifikasiPraktik(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $umkm = Umkm::findOrFail($id);
        $updatedUmkm = $this->skorService->verifikasiPraktik($umkm, (bool) $request->status);

        return response()->json([
            'success' => true,
            'message' => 'Status verifikasi praktik ramah lingkungan berhasil diperbarui.',
            'data' => $updatedUmkm,
        ]);
    }
}
