<?php

namespace App\Http\Controllers\Api\Admin;

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
     * GET /api/admin/peluang-bisnis
     */
    public function index(): JsonResponse
    {
        $peluangList = $this->peluangService->getAllPeluangAdmin();

        return response()->json([
            'success' => true,
            'data' => $peluangList,
        ]);
    }

    /**
     * POST /api/admin/peluang-bisnis/{id}/status
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:terbuka,ditutup',
        ]);

        $peluang = $this->peluangService->updateStatus($request->user(), $id, $request->status);

        return response()->json([
            'success' => true,
            'message' => "Status peluang bisnis diubah menjadi {$request->status}.",
            'data' => $peluang,
        ]);
    }
}
