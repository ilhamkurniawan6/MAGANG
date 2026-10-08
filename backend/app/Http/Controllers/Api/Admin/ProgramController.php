<?php

namespace App\Http\Controllers\Api\Admin;

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
     * GET /api/admin/program
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'jenis']);
        $programs = $this->programService->getProgramAdmin($filters);

        return response()->json([
            'success' => true,
            'data' => $programs,
        ]);
    }

    /**
     * POST /api/admin/program
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'jenis' => 'required|in:pelatihan,seminar,pendampingan',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:draft,published,selesai',
        ]);

        $program = $this->programService->createProgram($request->user(), $validated);

        return response()->json([
            'success' => true,
            'message' => 'Program berhasil dibuat.',
            'data' => $program,
        ], 201);
    }

    /**
     * PUT /api/admin/program/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'jenis' => 'required|in:pelatihan,seminar,pendampingan',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'status' => 'required|in:draft,published,selesai',
        ]);

        $program = $this->programService->updateProgram($id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Program berhasil diperbarui.',
            'data' => $program,
        ]);
    }

    /**
     * DELETE /api/admin/program/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $this->programService->deleteProgram($id);

        return response()->json([
            'success' => true,
            'message' => 'Program berhasil dihapus.',
        ]);
    }

    /**
     * GET /api/admin/program/{id}/peserta
     */
    public function peserta(int $id): JsonResponse
    {
        $peserta = $this->programService->getPesertaProgram($id);

        return response()->json([
            'success' => true,
            'data' => $peserta,
        ]);
    }

    /**
     * POST /api/admin/program/{id}/peserta/{pesertaId}/kehadiran
     */
    public function updateKehadiran(Request $request, int $id, int $pesertaId): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:terdaftar,hadir,tidak_hadir',
        ]);

        $peserta = $this->programService->updateKehadiranPeserta($id, $pesertaId, $request->status);

        return response()->json([
            'success' => true,
            'message' => 'Status kehadiran peserta berhasil diperbarui.',
            'data' => $peserta,
        ]);
    }
}
