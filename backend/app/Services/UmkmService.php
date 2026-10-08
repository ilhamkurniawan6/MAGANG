<?php

namespace App\Services;

use App\Models\User;
use App\Models\Umkm;
use App\Models\Produk;
use Illuminate\Http\UploadedFile;

class UmkmService
{
    public function __construct(
        protected StorageService $storageService
    ) {}

    /**
     * Dapatkan profil UMKM milik user yang sedang login
     */
    public function getProfil(User $user): ?Umkm
    {
        return $user->umkm()->with('kategori')->first();
    }

    /**
     * Update data profil UMKM milik user
     */
    public function updateProfil(User $user, array $data, ?UploadedFile $logoFile = null): Umkm
    {
        $umkm = $user->umkm;

        if (!$umkm) {
            $umkm = new Umkm();
            $umkm->user_id = $user->id;
            $umkm->status_verifikasi = 'pending';
        }

        if ($logoFile) {
            // Hapus logo lama jika ada
            if ($umkm->logo) {
                $this->storageService->deleteFile($umkm->logo);
            }
            $data['logo'] = $this->storageService->uploadFile($logoFile, 'logos');
        }

        $umkm->fill($data);
        $umkm->save();

        return $umkm->load('kategori');
    }

    /**
     * Dapatkan ringkasan statistik dashboard UMKM
     */
    public function getDashboard(User $user): array
    {
        $umkm = $this->getProfil($user);

        if (!$umkm) {
            return [
                'umkm' => null,
                'produk_stats' => [
                    'total' => 0,
                    'menunggu_moderasi' => 0,
                    'disetujui' => 0,
                    'ditolak' => 0,
                ],
            ];
        }

        $produkStats = [
            'total' => Produk::where('umkm_id', $umkm->id)->count(),
            'menunggu_moderasi' => Produk::where('umkm_id', $umkm->id)->where('status', 'menunggu_moderasi')->count(),
            'disetujui' => Produk::where('umkm_id', $umkm->id)->where('status', 'disetujui')->count(),
            'ditolak' => Produk::where('umkm_id', $umkm->id)->where('status', 'ditolak')->count(),
        ];

        return [
            'umkm' => $umkm,
            'produk_stats' => $produkStats,
        ];
    }
}
