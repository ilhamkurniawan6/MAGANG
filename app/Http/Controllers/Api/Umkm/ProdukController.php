<?php

namespace App\Http\Controllers\Api\Umkm;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProdukRequest;
use App\Services\ProdukService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function __construct(
        protected ProdukService $produkService
    ) {}

    /**
     * GET /api/umkm/produk
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'kategori_id']);
        $produk = $this->produkService->getProdukByUmkm($request->user(), $filters);

        return response()->json([
            'success' => true,
            'data' => $produk,
        ]);
    }

    /**
     * POST /api/umkm/produk
     */
    public function store(ProdukRequest $request): JsonResponse
    {
        $fotoFile = $request->file('foto_produk');
        $produk = $this->produkService->createProduk($request->user(), $request->validated(), $fotoFile);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan dan diajukan ke antrean moderasi.',
            'data' => $produk,
        ], 201);
    }

    /**
     * GET /api/umkm/produk/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $produk = $this->produkService->getProdukById($request->user(), $id);

        return response()->json([
            'success' => true,
            'data' => $produk,
        ]);
    }

    /**
     * PUT/POST /api/umkm/produk/{id}
     */
    public function update(ProdukRequest $request, int $id): JsonResponse
    {
        $fotoFile = $request->file('foto_produk');
        $produk = $this->produkService->updateProduk($request->user(), $id, $request->validated(), $fotoFile);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui dan diajukan ulang ke antrean moderasi.',
            'data' => $produk,
        ]);
    }

    /**
     * DELETE /api/umkm/produk/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->produkService->deleteProduk($request->user(), $id);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus.',
        ]);
    }
}
