<?php

namespace App\Http\Controllers\Api\Umkm;

use App\Http\Controllers\Controller;
use App\Services\SkorLestariService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkorLestariController extends Controller
{
    public function __construct(
        protected SkorLestariService $skorService
    ) {}

    /**
     * GET /api/umkm/skor-lestari
     */
    public function index(Request $request): JsonResponse
    {
        $umkm = $request->user()->umkm;

        if (!$umkm) {
            return response()->json([
                'success' => false,
                'message' => 'Profil UMKM tidak ditemukan.',
            ], 404);
        }

        $hasil = $this->skorService->hitungSkor($umkm);

        return response()->json([
            'success' => true,
            'data' => [
                'umkm' => $umkm,
                'skor_lestari' => $hasil['skor_akhir'],
                'level' => $hasil['level'],
                'breakdown' => $hasil['breakdown'],
                'calculated_at' => $hasil['calculated_at'],
            ],
        ]);
    }

    /**
     * POST /api/umkm/praktik-ramah-lingkungan
     */
    public function updatePraktik(Request $request): JsonResponse
    {
        $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'string|in:bahan_baku_lokal,kemasan_minim_plastik,hemat_energi,pengolahan_limbah,pewarna_alami,organik_alami,zero_waste',
        ]);

        $umkm = $request->user()->umkm;

        if (!$umkm) {
            return response()->json([
                'success' => false,
                'message' => 'Profil UMKM tidak ditemukan.',
            ], 404);
        }

        $updatedUmkm = $this->skorService->updatePraktikRamahLingkungan($umkm, $request->tags);

        return response()->json([
            'success' => true,
            'message' => 'Klaim praktik ramah lingkungan berhasil disimpan dan skor telah diperbarui.',
            'data' => $updatedUmkm,
        ]);
    }
}
