<?php

namespace App\Http\Controllers\Api\Publik;

use App\Http\Controllers\Controller;
use App\Services\ProgramService;
use Illuminate\Http\JsonResponse;

class ProgramController extends Controller
{
    public function __construct(
        protected ProgramService $programService
    ) {}

    /**
     * GET /api/publik/program
     */
    public function index(): JsonResponse
    {
        $programs = $this->programService->getProgramPublik();

        return response()->json([
            'success' => true,
            'data' => $programs,
        ]);
    }
}
