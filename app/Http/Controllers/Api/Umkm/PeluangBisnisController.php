<?php

namespace App\Http\Controllers\Api\Umkm;

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
     * GET /api/umkm/peluang-bisnis
     */
    public function index(Request $request): JsonResponse
    {
        $peluang = $this->peluangService->getPeluangForUmkm($request->user());

        return response()->json([
            'success' => true,
            'data' => $peluang,
        ]);
    }

    /**
     * POST /api/umkm/peluang-bisnis/{id}/minat
     */
    public function nyatakanMinat(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'catatan' => 'nullable|string|max:1000',
        ]);

        $minat = $this->peluangService->nyatakanMinat($request->user(), $id, $request->catatan);

        return response()->json([
            'success' => true,
            'message' => 'Pernyataan minat berhasil dikirim kepada pihak buyer.',
            'data' => $minat,
        ], 201);
    }
}
