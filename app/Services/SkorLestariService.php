<?php

namespace App\Services;

use App\Models\Umkm;
use App\Models\User;
use App\Models\CatatanPembinaan;
use App\Models\ProgramPeserta;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class SkorLestariService
{
    /**
     * Hitung Skor KADIN Lestari (0-100) dan tentukan level Bronze/Silver/Gold
     */
    public function hitungSkor(Umkm $umkm): array
    {
        $now = Carbon::now();
        $cutoff90Days = $now->copy()->subDays(90);

        // -------------------------------------------------------------
        // 1. Komponen Pertumbuhan Usaha (Bobot 40%)
        // -------------------------------------------------------------
        $totalProduk = $umkm->produk()->count();
        $produk90Hari = $umkm->produk()->where('created_at', '>=', $cutoff90Days)->count();
        $produkDisetujui = $umkm->produk()->where('status', 'disetujui')->count();
        $produkDitolak = $umkm->produk()->where('status', 'ditolak')->count();

        // Skor penambahan produk (maks 40)
        $skorProdukBaru = 10;
        if ($produk90Hari >= 3) {
            $skorProdukBaru = 40;
        } elseif ($produk90Hari >= 1) {
            $skorProdukBaru = 25;
        }

        // Skor rasio persetujuan (maks 40)
        $skorRasio = 20;
        if ($totalProduk > 0) {
            $rasio = $produkDisetujui / $totalProduk;
            $skorRasio = round($rasio * 40);
        }

        // Skor kelengkapan data usaha (maks 20)
        $skorKelengkapan = 0;
        if (!empty($umkm->deskripsi)) $skorKelengkapan += 8;
        if (!empty($umkm->logo)) $skorKelengkapan += 6;
        if (!empty($umkm->email_usaha)) $skorKelengkapan += 6;

        $rawKomp1 = min(100, $skorProdukBaru + $skorRasio + $skorKelengkapan);

        // -------------------------------------------------------------
        // 2. Komponen Intensitas Pembinaan (Bobot 30%)
        // Catatan: Skor rendah di sini adalah SINYAL bagi Admin untuk
        // memberikan pendampingan lebih, BUKAN vonis buruk ke UMKM.
        // -------------------------------------------------------------
        $catatan90Hari = $umkm->catatanPembinaan()
            ->where('tanggal', '>=', $cutoff90Days->toDateString())
            ->count();

        $kehadiranProgram = ProgramPeserta::where('umkm_id', $umkm->id)
            ->where('status', 'hadir')
            ->count();

        $skorCatatan = 20; // Default baseline perhatian
        if ($catatan90Hari >= 3) {
            $skorCatatan = 60;
        } elseif ($catatan90Hari >= 1) {
            $skorCatatan = 40;
        }

        $skorProgram = min(40, $kehadiranProgram * 20);
        $rawKomp2 = min(100, $skorCatatan + $skorProgram);

        // -------------------------------------------------------------
        // 3. Komponen Praktik Ramah Lingkungan (Bobot 30%)
        // -------------------------------------------------------------
        $tags = is_array($umkm->praktik_ramah_lingkungan) ? $umkm->praktik_ramah_lingkungan : [];
        $jumlahTag = count($tags);
        $isVerified = (bool) $umkm->praktik_terverifikasi;

        $rawKomp3 = 0;
        if ($jumlahTag > 0) {
            if ($isVerified) {
                // Terverifikasi admin
                if ($jumlahTag >= 3) {
                    $rawKomp3 = 100;
                } elseif ($jumlahTag === 2) {
                    $rawKomp3 = 75;
                } else {
                    $rawKomp3 = 50;
                }
            } else {
                // Klaim mandiri belum diverifikasi (maks 40)
                $rawKomp3 = min(40, $jumlahTag * 15);
            }
        }

        // -------------------------------------------------------------
        // Hitung Total Skor Akhir (0 - 100)
        // -------------------------------------------------------------
        $finalScore = round(($rawKomp1 * 0.40) + ($rawKomp2 * 0.30) + ($rawKomp3 * 0.30));
        $finalScore = max(0, min(100, (int) $finalScore));

        // Pemetaan Level KADIN Lestari:
        // Bronze: 0 - 49
        // Silver: 50 - 79
        // Gold:   80 - 100
        $level = 'bronze';
        if ($finalScore >= 80) {
            $level = 'gold';
        } elseif ($finalScore >= 50) {
            $level = 'silver';
        }

        return [
            'skor_akhir' => $finalScore,
            'level' => $level,
            'breakdown' => [
                'pertumbuhan_usaha' => [
                    'nama' => 'Pertumbuhan Usaha',
                    'bobot' => '40%',
                    'skor_mentah' => $rawKomp1,
                    'kontribusi' => round($rawKomp1 * 0.40, 1),
                    'detail' => "Total produk: {$totalProduk} ({$produkDisetujui} disetujui, {$produk90Hari} baru dalam 90 hari)",
                ],
                'intensitas_pembinaan' => [
                    'nama' => 'Intensitas Pembinaan & Partisipasi',
                    'bobot' => '30%',
                    'skor_mentah' => $rawKomp2,
                    'kontribusi' => round($rawKomp2 * 0.30, 1),
                    'sinyal_admin' => $rawKomp2 < 50 ? 'Prioritas pendampingan aktif oleh kurator KADIN' : 'Pendampingan berkala berjalan baik',
                    'detail' => "Catatan pembinaan: {$catatan90Hari} dalam 90 hari | Program dihadiri: {$kehadiranProgram}",
                ],
                'praktik_ramah_lingkungan' => [
                    'nama' => 'Praktik Ramah Lingkungan',
                    'bobot' => '30%',
                    'skor_mentah' => $rawKomp3,
                    'kontribusi' => round($rawKomp3 * 0.30, 1),
                    'terverifikasi' => $isVerified,
                    'tags' => $tags,
                    'detail' => "{$jumlahTag} praktik diklaim (" . ($isVerified ? 'Terverifikasi KADIN' : 'Menunggu verifikasi admin') . ")",
                ],
            ],
            'calculated_at' => $now->toIso8601String(),
        ];
    }

    /**
     * Hitung dan simpan langsung ke database untuk satu UMKM
     */
    public function updateSkorUmkm(Umkm $umkm): array
    {
        $hasil = $this->hitungSkor($umkm);

        $umkm->skor_lestari = $hasil['skor_akhir'];
        $umkm->skor_lestari_level = $hasil['level'];
        $umkm->skor_lestari_updated_at = Carbon::now();
        $umkm->save();

        return $hasil;
    }

    /**
     * Hitung ulang skor untuk semua UMKM yang verified
     */
    public function hitungUlangSemua(): int
    {
        $umkmList = Umkm::where('status_verifikasi', 'verified')->get();
        $count = 0;

        foreach ($umkmList as $umkm) {
            $this->updateSkorUmkm($umkm);
            $count++;
        }

        return $count;
    }

    /**
     * Update tag praktik ramah lingkungan yang diklaim UMKM
     */
    public function updatePraktikRamahLingkungan(Umkm $umkm, array $tags): Umkm
    {
        $allowedTags = [
            'bahan_baku_lokal',
            'kemasan_minim_plastik',
            'hemat_energi',
            'pengolahan_limbah',
            'pewarna_alami',
            'organik_alami',
            'zero_waste',
        ];

        $validTags = array_values(array_intersect($tags, $allowedTags));

        $umkm->praktik_ramah_lingkungan = $validTags;
        // Jika tag berubah, tandai verifikasi perlu ditinjau ulang
        $umkm->save();

        // Hitung ulang skor
        $this->updateSkorUmkm($umkm);

        return $umkm->fresh();
    }

    /**
     * Admin verifikasi ringan praktik ramah lingkungan
     */
    public function verifikasiPraktik(Umkm $umkm, bool $status): Umkm
    {
        $umkm->praktik_terverifikasi = $status;
        $umkm->save();

        // Hitung ulang skor setelah verifikasi
        $this->updateSkorUmkm($umkm);

        return $umkm->fresh();
    }

    /**
     * Admin mencatat pembinaan baru
     */
    public function tambahCatatanPembinaan(Umkm $umkm, User $admin, array $data): CatatanPembinaan
    {
        $catatan = CatatanPembinaan::create([
            'umkm_id' => $umkm->id,
            'admin_id' => $admin->id,
            'jenis' => $data['jenis'],
            'catatan' => $data['catatan'],
            'tanggal' => $data['tanggal'] ?? Carbon::now()->toDateString(),
        ]);

        // Hitung ulang skor UMKM
        $this->updateSkorUmkm($umkm);

        return $catatan->load('admin');
    }

    /**
     * Dapatkan daftar UMKM diurutkan dari skor terendah untuk prioritas pendampingan admin
     */
    public function getPrioritasPendampingan(array $filters = []): Collection
    {
        $query = Umkm::with(['kategori', 'user', 'catatanPembinaan' => function ($q) {
            $q->latest()->take(3);
        }])
        ->where('status_verifikasi', 'verified');

        if (!empty($filters['level'])) {
            $query->where('skor_lestari_level', $filters['level']);
        }

        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_usaha', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('nama', 'like', "%{$q}%");
                    });
            });
        }

        // Urutkan dari skor terendah (paling butuh atensi pembinaan)
        return $query->orderByRaw('COALESCE(skor_lestari, 0) ASC')->get();
    }
}
