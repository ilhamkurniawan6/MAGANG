<?php

namespace App\Services;

use App\Models\User;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class ProdukService
{
    public function __construct(
        protected StorageService $storageService
    ) {}

    /**
     * Dapatkan semua produk milik UMKM yang login
     */
    public function getProdukByUmkm(User $user, array $filters = []): Collection
    {
        $umkm = $user->umkm;
        if (!$umkm) {
            return new Collection();
        }

        $query = Produk::with(['kategori', 'umkm'])
            ->where('umkm_id', $umkm->id)
            ->latest();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['kategori_id'])) {
            $query->where('kategori_produk_id', $filters['kategori_id']);
        }

        return $query->get();
    }

    /**
     * Dapatkan 1 produk milik UMKM
     */
    public function getProdukById(User $user, int $id): Produk
    {
        $umkm = $user->umkm;
        if (!$umkm) {
            throw ValidationException::withMessages(['umkm' => ['Profil UMKM tidak ditemukan.']]);
        }

        return Produk::with(['kategori', 'umkm'])
            ->where('umkm_id', $umkm->id)
            ->where('id', $id)
            ->firstOrFail();
    }

    /**
     * Tambah produk baru (Otomatis status: menunggu_moderasi)
     */
    public function createProduk(User $user, array $data, ?UploadedFile $fotoFile = null): Produk
    {
        $umkm = $user->umkm;
        if (!$umkm) {
            throw ValidationException::withMessages(['umkm' => ['Lengkapi data profil UMKM terlebih dahulu sebelum menambah produk.']]);
        }

        if ($fotoFile) {
            $data['foto_produk'] = $this->storageService->uploadFile($fotoFile, 'produk');
        }

        $data['umkm_id'] = $umkm->id;
        $data['status'] = 'menunggu_moderasi'; // Wajib moderasi

        return Produk::create($data)->load(['kategori', 'umkm']);
    }

    /**
     * Update produk milik UMKM (Otomatis di-reset status ke menunggu_moderasi)
     */
    public function updateProduk(User $user, int $id, array $data, ?UploadedFile $fotoFile = null): Produk
    {
        $produk = $this->getProdukById($user, $id);

        if ($fotoFile) {
            if ($produk->foto_produk) {
                $this->storageService->deleteFile($produk->foto_produk);
            }
            $data['foto_produk'] = $this->storageService->uploadFile($fotoFile, 'produk');
        }

        // Setiap edit produk, reset status ke menunggu_moderasi
        $data['status'] = 'menunggu_moderasi';

        $produk->update($data);

        return $produk->fresh(['kategori', 'umkm']);
    }

    /**
     * Hapus produk milik UMKM
     */
    public function deleteProduk(User $user, int $id): bool
    {
        $produk = $this->getProdukById($user, $id);

        if ($produk->foto_produk) {
            $this->storageService->deleteFile($produk->foto_produk);
        }

        return $produk->delete();
    }
}
