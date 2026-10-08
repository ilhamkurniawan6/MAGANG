<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Services\PeluangBisnisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PeluangBisnisController extends Controller
{
    public function __construct(
        protected PeluangBisnisService $peluangService
    ) {}

    /**
     * GET /api/buyer/dashboard
     */
    public function dashboard(Request $request): JsonResponse
    {
        $dashboard = $this->peluangService->getBuyerDashboard($request->user());

        return response()->json([
            'success' => true,
            'data' => $dashboard,
        ]);
    }

    /**
     * GET /api/buyer/peluang-bisnis
     */
    public function index(Request $request): JsonResponse
    {
        $list = $this->peluangService->getPeluangByBuyer($request->user());

        return response()->json([
            'success' => true,
            'data' => $list,
        ]);
    }

    /**
     * POST /api/buyer/peluang-bisnis
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kategori_produk_id' => 'required|exists:kategori_produk,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kapasitas' => 'nullable|string|max:255',
            'target_volume' => 'nullable|string|max:255',
        ]);

        $peluang = $this->peluangService->createPeluang($request->user(), $validated);

        return response()->json([
            'success' => true,
            'message' => 'Kebutuhan pengadaan bisnis (RFQ) berhasil dipublikasikan ke mitra UMKM.',
            'data' => $peluang,
        ], 201);
    }

    /**
     * GET /api/buyer/peluang-bisnis/{id}/minat
     */
    public function peminat(Request $request, int $id): JsonResponse
    {
        $peminat = $this->peluangService->getPeminatPeluang($request->user(), $id);

        return response()->json([
            'success' => true,
            'data' => $peminat,
        ]);
    }

    /**
     * POST /api/buyer/peluang-bisnis/{id}/status
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:terbuka,ditutup',
        ]);

        $peluang = $this->peluangService->updateStatus($request->user(), $id, $request->status);

        return response()->json([
            'success' => true,
            'message' => "Status peluang bisnis berhasil diubah menjadi {$request->status}.",
            'data' => $peluang,
        ]);
    }

    /**
     * GET /api/buyer/peluang-bisnis/{id}/rekomendasi-umkm
     */
    public function rekomendasiUmkm(Request $request, int $id): JsonResponse
    {
        $hasil = $this->peluangService->getRekomendasiUmkm($request->user(), $id);

        return response()->json([
            'success' => true,
            'data' => $hasil['rekomendasi'],
            'meta' => [
                'source' => $hasil['source'],
                'total_kandidat_disaring' => $hasil['total_kandidat_disaring'] ?? 0,
            ],
        ]);
    }
}

