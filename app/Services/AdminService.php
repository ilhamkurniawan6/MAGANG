<?php

namespace App\Services;

use App\Models\Umkm;
use App\Models\Produk;
use Illuminate\Database\Eloquent\Collection;

class AdminService
{
    /**
     * Dashboard statistik Admin
     */
    public function getDashboard(): array
    {
        $umkmStats = [
            'total' => Umkm::count(),
            'pending' => Umkm::where('status_verifikasi', 'pending')->count(),
            'verified' => Umkm::where('status_verifikasi', 'verified')->count(),
            'rejected' => Umkm::where('status_verifikasi', 'rejected')->count(),
        ];

        $produkStats = [
            'total' => Produk::count(),
            'menunggu_moderasi' => Produk::where('status', 'menunggu_moderasi')->count(),
            'disetujui' => Produk::where('status', 'disetujui')->count(),
            'ditolak' => Produk::where('status', 'ditolak')->count(),
        ];

        $recentUmkm = Umkm::with(['user', 'kategori'])
            ->latest()
            ->take(5)
            ->get();

        $recentProduk = Produk::with(['umkm', 'kategori'])
            ->latest()
            ->take(5)
            ->get();

        return [
            'umkm_stats' => $umkmStats,
            'produk_stats' => $produkStats,
            'recent_umkm' => $recentUmkm,
            'recent_produk' => $recentProduk,
        ];
    }

    /**
     * List UMKM dengan filter status dan pencarian
     */
    public function getUmkmList(array $filters = []): Collection
    {
        $query = Umkm::with(['user', 'kategori'])->latest();

        if (!empty($filters['status'])) {
            $query->where('status_verifikasi', $filters['status']);
        }

        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_usaha', 'like', "%{$q}%")
                    ->orWhere('kontak_wa', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('nama', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        return $query->get();
    }

    /**
     * Detail UMKM untuk verifikasi
     */
    public function getUmkmDetail(int $id): Umkm
    {
        return Umkm::with(['user', 'kategori', 'produk.kategori'])->findOrFail($id);
    }

    /**
     * Verifikasi UMKM (verified / rejected)
     */
    public function verifikasiUmkm(int $id, string $status): Umkm
    {
        $umkm = Umkm::findOrFail($id);
        $umkm->status_verifikasi = $status;
        $umkm->save();

        // Kirim Notifikasi WhatsApp via Queue/Job (Best Effort)
        try {
            if (!empty($umkm->kontak_wa)) {
                $namaUsaha = $umkm->nama_usaha;
                if ($status === 'verified') {
                    $pesan = "Halo *{$namaUsaha}*! 🌿\nSelamat, profil UMKM Anda telah berhasil *DIVERIFIKASI* oleh Tim KADIN DIY.\n\nKini Anda dapat mengelola produk, mengikuti program pembinaan, dan mengakses peluang kemitraan bisnis.";
                } else {
                    $pesan = "Halo *{$namaUsaha}*,\nMohon maaf, permohonan verifikasi profil UMKM Anda belum dapat disetujui saat ini. Silakan periksa kembali kelengkapan profil Anda di KADIN Digital Hub.";
                }
                \App\Jobs\SendWhatsAppNotificationJob::dispatch($umkm->kontak_wa, $pesan);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal dispatch notifikasi WA UMKM ID {$id}: " . $e->getMessage());
        }

        return $umkm->load(['user', 'kategori']);
    }

    /**
     * List produk untuk moderasi admin
     */
    public function getProdukList(array $filters = []): Collection
    {
        $query = Produk::with(['umkm.user', 'kategori'])->latest();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_produk', 'like', "%{$q}%")
                    ->orWhereHas('umkm', function ($u) use ($q) {
                        $u->where('nama_usaha', 'like', "%{$q}%");
                    });
            });
        }

        return $query->get();
    }

    /**
     * Detail produk untuk moderasi
     */
    public function getProdukDetail(int $id): Produk
    {
        return Produk::with(['umkm.user', 'umkm.kategori', 'kategori'])->findOrFail($id);
    }

    /**
     * Moderasi produk (disetujui / ditolak)
     */
    public function moderasiProduk(int $id, string $status): Produk
    {
        $produk = Produk::with('umkm')->findOrFail($id);
        $produk->status = $status;
        $produk->save();

        // Kirim Notifikasi WhatsApp ke pemilik produk (Best Effort)
        try {
            if ($produk->umkm && !empty($produk->umkm->kontak_wa)) {
                $namaProduk = $produk->nama_produk;
                $kontak = $produk->umkm->kontak_wa;
                if ($status === 'disetujui') {
                    $pesan = "Halo UMKM Binaan KADIN! 📦\nProduk Anda *\"{$namaProduk}\"* telah *DISETUJUI* oleh Kurator KADIN dan kini tayang di Katalog Publik.";
                } else {
                    $pesan = "Halo UMKM Binaan KADIN,\nProduk Anda *\"{$namaProduk}\"* belum disetujui kurator. Silakan periksa kembali foto dan deskripsi produk Anda.";
                }
                \App\Jobs\SendWhatsAppNotificationJob::dispatch($kontak, $pesan);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal dispatch notifikasi WA Produk ID {$id}: " . $e->getMessage());
        }

        return $produk->load(['umkm', 'kategori']);
    }
}
