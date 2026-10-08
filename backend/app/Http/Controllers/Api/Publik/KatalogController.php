<?php

namespace App\Http\Controllers\Api\Publik;

use App\Http\Controllers\Controller;
use App\Services\PublikService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function __construct(
        protected PublikService $publikService
    ) {}

    /**
     * GET /api/publik/kategori-umkm
     */
    public function kategoriUmkm(): JsonResponse
    {
        $data = $this->publikService->getKategoriUmkm();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * GET /api/publik/kategori-produk
     */
    public function kategoriProduk(): JsonResponse
    {
        $data = $this->publikService->getKategoriProduk();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * GET /api/publik/katalog
     */
    public function katalog(Request $request): JsonResponse
    {
        $filters = $request->only(['q', 'kategori_id', 'kategori_umkm_id', 'sort', 'limit', 'page']);
        $paginator = $this->publikService->getKatalog($filters);

        return response()->json([
            'success' => true,
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    /**
     * GET /api/publik/produk/{id}
     */
    public function detailProduk(int $id): JsonResponse
    {
        $produk = $this->publikService->getProdukDetail($id);

        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan atau belum disetujui.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $produk,
        ]);
    }

    /**
     * GET /api/publik/umkm/{id}
     */
    public function detailUmkm(int $id): JsonResponse
    {
        $umkm = $this->publikService->getUmkmDetail($id);

        if (!$umkm) {
            return response()->json([
                'success' => false,
                'message' => 'Profil UMKM tidak ditemukan atau belum terverifikasi.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $umkm,
        ]);
    }
}
