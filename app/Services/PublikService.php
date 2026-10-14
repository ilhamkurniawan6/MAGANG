<?php

namespace App\Services;

use App\Models\KategoriUmkm;
use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\Umkm;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PublikService
{
    /**
     * Dapatkan semua kategori UMKM
     */
    public function getKategoriUmkm(): Collection
    {
        return KategoriUmkm::withCount(['umkm' => function ($q) {
            $q->where('status_verifikasi', 'verified');
        }])->get();
    }

    /**
     * Dapatkan semua kategori Produk beserta hitungan produk disetujui
     */
    public function getKategoriProduk(): Collection
    {
        return KategoriProduk::withCount(['produk' => function ($q) {
            $q->where('status', 'disetujui')
              ->whereHas('umkm', function ($u) {
                  $u->where('status_verifikasi', 'verified');
              });
        }])->get();
    }

    /**
     * Dapatkan katalog produk publik (Hanya produk disetujui & UMKM terverifikasi)
     */
    public function getKatalog(array $filters = []): LengthAwarePaginator
    {
        $query = Produk::with(['kategori', 'umkm.kategori'])
            ->where('status', 'disetujui')
            ->whereHas('umkm', function ($u) {
                $u->where('status_verifikasi', 'verified');
            });

        // Filter search keyword
        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_produk', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%")
                    ->orWhereHas('umkm', function ($u) use ($q) {
                        $u->where('nama_usaha', 'like', "%{$q}%");
                    });
            });
        }

        // Filter kategori produk
        if (!empty($filters['kategori_id'])) {
            $query->where('kategori_produk_id', $filters['kategori_id']);
        }

        // Filter kategori UMKM
        if (!empty($filters['kategori_umkm_id'])) {
            $query->whereHas('umkm', function ($u) use ($filters) {
                $u->where('kategori_id', $filters['kategori_umkm_id']);
            });
        }

        // Sorting
        $sort = $filters['sort'] ?? 'terbaru';
        switch ($sort) {
            case 'harga_asc':
                $query->orderBy('harga', 'asc');
                break;
            case 'harga_desc':
                $query->orderBy('harga', 'desc');
                break;
            case 'nama_asc':
                $query->orderBy('nama_produk', 'asc');
                break;
            case 'terbaru':
            default:
                $query->latest();
                break;
        }

        $perPage = (int) ($filters['limit'] ?? 12);

        return $query->paginate($perPage);
    }

    /**
     * Dapatkan detail produk untuk publik (Hanya jika disetujui & UMKM verified)
     */
    public function getProdukDetail(int $id): ?Produk
    {
        return Produk::with(['kategori', 'umkm.kategori', 'umkm.user'])
            ->where('status', 'disetujui')
            ->whereHas('umkm', function ($u) {
                $u->where('status_verifikasi', 'verified');
            })
            ->where('id', $id)
            ->first();
    }

    /**
     * Dapatkan detail profil UMKM publik (Hanya jika status_verifikasi = verified)
     */
    public function getUmkmDetail(int $id): ?Umkm
    {
        return Umkm::with([
            'kategori',
            'user',
            'produk' => function ($q) {
                $q->where('status', 'disetujui')->with('kategori')->latest();
            }
        ])
        ->where('status_verifikasi', 'verified')
        ->where('id', $id)
        ->first();
    }
}
