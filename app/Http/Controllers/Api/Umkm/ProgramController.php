<?php

namespace App\Http\Controllers\Api\Umkm;

use App\Http\Controllers\Controller;
use App\Services\ProgramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function __construct(
        protected ProgramService $programService
    ) {}

    /**
     * GET /api/umkm/program
     */
    public function index(Request $request): JsonResponse
    {
        $programs = $this->programService->getProgramForUmkm($request->user());

        return response()->json([
            'success' => true,
            'data' => $programs,
        ]);
    }

    /**
     * POST /api/umkm/program/{id}/daftar
     */
    public function daftar(Request $request, int $id): JsonResponse
    {
        $peserta = $this->programService->daftarProgram($request->user(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mendaftar program pembinaan KADIN.',
            'data' => $peserta,
        ], 201);
    }

    /**
     * GET /api/umkm/rekomendasi-program
     */
    public function rekomendasi(Request $request): JsonResponse
    {
        $hasil = $this->programService->getRekomendasiProgram($request->user());

        return response()->json([
            'success' => true,
            'data' => $hasil['rekomendasi'],
            'meta' => [
                'source' => $hasil['source'],
                'skor_lestari' => $hasil['skor_lestari_summary'] ?? null,
            ],
        ]);
    }
}
