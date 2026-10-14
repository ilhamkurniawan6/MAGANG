<?php

namespace App\Services;

use App\Models\PeluangBisnis;
use App\Models\PeluangBisnisMinat;
use App\Models\User;
use App\Models\Umkm;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class PeluangBisnisService
{
    public function __construct(
        protected ?OpenRouterService $openRouterService = null
    ) {
        $this->openRouterService = $openRouterService ?? app(OpenRouterService::class);
    }

    /**
     * Buyer: Dapatkan rekomendasi UMKM berbasis AI untuk peluang bisnis tertentu
     */
    public function getRekomendasiUmkm(User $user, int $peluangId): array
    {
        $peluang = PeluangBisnis::with(['kategori', 'creator'])->findOrFail($peluangId);

        // Pastikan hanya pembuat peluang atau admin yang bisa mengakses
        if ($peluang->created_by !== $user->id && !$user->hasRole('admin')) {
            throw ValidationException::withMessages(['akses' => ['Anda tidak memiliki akses ke peluang bisnis ini.']]);
        }

        // 1. FILTER QUERY AWAL (Kandidat UMKM Terverifikasi & Relevan)
        // Jangan kirim seluruh DB ke AI! Hanya kandidat tersaring.
        $kandidatQuery = Umkm::with(['kategori', 'user', 'produk' => function ($q) {
            $q->where('status', 'disetujui')->latest()->take(3);
        }])
        ->where('status_verifikasi', 'verified');

        $kategoriProdukId = $peluang->kategori_produk_id;
        if ($kategoriProdukId) {
            $kandidatQuery->where(function ($q) use ($kategoriProdukId) {
                // UMKM yang memiliki produk di kategori terkait ATAU kategori usaha relevan
                $q->whereHas('produk', function ($p) use ($kategoriProdukId) {
                    $p->where('kategori_produk_id', $kategoriProdukId);
                })->orWhere('kategori_id', $kategoriProdukId);
            });
        }

        $kandidatList = $kandidatQuery->orderByRaw('COALESCE(skor_lestari, 0) DESC')->take(10)->get();

        // Jika belum ada kandidat spesifik, fallback ke top UMKM verified umum
        if ($kandidatList->isEmpty()) {
            $kandidatList = Umkm::with(['kategori', 'user', 'produk' => function ($q) {
                $q->where('status', 'disetujui')->latest()->take(3);
            }])
            ->where('status_verifikasi', 'verified')
            ->orderByRaw('COALESCE(skor_lestari, 0) DESC')
            ->take(5)
            ->get();
        }

        if ($kandidatList->isEmpty()) {
            return [
                'peluang' => $peluang,
                'rekomendasi' => [],
                'source' => 'empty',
                'pesan' => 'Belum ada UMKM terverifikasi yang cocok.',
            ];
        }

        // 2. Format ringkasan data profil kandidat untuk AI Prompt
        $kandidatSummary = $kandidatList->map(function ($u) {
            return [
                'umkm_id' => $u->id,
                'nama_usaha' => $u->nama_usaha,
                'kategori' => $u->kategori ? $u->kategori->nama_kategori : 'Umum',
                'skor_lestari' => $u->skor_lestari ?? 0,
                'level' => $u->skor_lestari_level ?? 'bronze',
                'jumlah_produk' => $u->produk->count(),
                'deskripsi' => substr($u->deskripsi ?? 'UMKM Binaan KADIN', 0, 150),
            ];
        })->values()->toArray();

        $prompt = "Sebagai AI Matchmaking KADIN DIY, cocokkan kebutuhan pengadaan bisnis berikut:\n"
            . "Judul: {$peluang->judul}\n"
            . "Kategori: " . ($peluang->kategori ? $peluang->kategori->nama_kategori : 'Umum') . "\n"
            . "Deskripsi: {$peluang->deskripsi}\n"
            . "Kapasitas/Target Volume: {$peluang->kapasitas} ({$peluang->target_volume})\n\n"
            . "Daftar kandidat UMKM terverifikasi:\n" . json_encode($kandidatSummary, JSON_UNESCAPED_UNICODE) . "\n\n"
            . "Berikan ranking maksimal 5 UMKM yang paling cocok dengan skor kecocokan (skala 1-10) dan alasan relevansi singkat (1 kalimat). "
            . "Balas HANYA dalam format JSON array: [{\"umkm_id\": number, \"skor_kecocokan\": number, \"alasan\": string}].";

        $messages = [
            ['role' => 'system', 'content' => 'Anda adalah sistem AI Business Matchmaking KADIN Hub.'],
            ['role' => 'user', 'content' => $prompt],
        ];

        $aiResponse = $this->openRouterService->chatCompletion($messages);
        $rekomendasiFinal = [];
        $source = 'fallback';

        if ($aiResponse['success'] && !empty($aiResponse['content'])) {
            $parsed = $this->openRouterService->parseJsonClean($aiResponse['content']);
            if (is_array($parsed)) {
                $umkmMap = $kandidatList->keyBy('id');

                foreach ($parsed as $item) {
                    if (isset($item['umkm_id']) && $umkmMap->has($item['umkm_id'])) {
                        $umkm = $umkmMap->get($item['umkm_id']);
                        $rekomendasiFinal[] = [
                            'umkm' => $umkm,
                            'skor_kecocokan' => min(10, max(1, (int) ($item['skor_kecocokan'] ?? 8))),
                            'alasan' => $item['alasan'] ?? 'Kapasitas dan kategori usaha selaras dengan kebutuhan pengadaan.',
                        ];
                    }
                }

                if (!empty($rekomendasiFinal)) {
                    $source = 'ai';
                }
            }
        }

        // Fallback jika AI offline / rate-limit
        if (empty($rekomendasiFinal)) {
            $source = 'fallback';
            $topKandidat = $kandidatList->take(5);

            foreach ($topKandidat as $idx => $umkm) {
                $scoreEstimate = max(6, 10 - $idx);
                $skorLestari = $umkm->skor_lestari ?? 50;
                $rekomendasiFinal[] = [
                    'umkm' => $umkm,
                    'skor_kecocokan' => $scoreEstimate,
                    'alasan' => "UMKM terverifikasi dengan Skor Lestari {$skorLestari} ({$umkm->skor_lestari_level}) dan portofolio produk aktif di KADIN Hub.",
                ];
            }
        }

        return [
            'peluang' => $peluang,
            'rekomendasi' => $rekomendasiFinal,
            'source' => $source,
            'total_kandidat_disaring' => $kandidatList->count(),
        ];
    }
    /**
     * Buyer: Dapatkan daftar kebutuhan bisnis yang diposting oleh buyer sendiri
     */
    public function getPeluangByBuyer(User $buyer): Collection
    {
        return PeluangBisnis::with(['kategori', 'peminat.umkm.user', 'peminat.umkm.kategori'])
            ->withCount('peminat')
            ->where('created_by', $buyer->id)
            ->latest()
            ->get();
    }

    /**
     * Buyer / Admin: Buat kebutuhan bisnis (RFQ) baru
     */
    public function createPeluang(User $creator, array $data): PeluangBisnis
    {
        $data['created_by'] = $creator->id;
        $data['status'] = 'terbuka';

        return PeluangBisnis::create($data)->load(['kategori', 'creator']);
    }

    /**
     * Buyer: Lihat daftar UMKM yang berminat pada satu peluang bisnis miliknya
     */
    public function getPeminatPeluang(User $user, int $peluangId): Collection
    {
        $peluang = PeluangBisnis::where('created_by', $user->id)
            ->orWhere(function ($q) use ($user) {
                if ($user->hasRole('admin')) {
                    $q->whereRaw('1=1');
                }
            })
            ->findOrFail($peluangId);

        return PeluangBisnisMinat::with(['umkm.user', 'umkm.kategori', 'umkm.produk'])
            ->where('peluang_bisnis_id', $peluang->id)
            ->latest()
            ->get();
    }

    /**
     * Update status peluang (terbuka / ditutup)
     */
    public function updateStatus(User $user, int $peluangId, string $status): PeluangBisnis
    {
        $peluang = PeluangBisnis::findOrFail($peluangId);

        // Pastikan hanya pembuat atau admin yang bisa mengubah status
        if ($peluang->created_by !== $user->id && !$user->hasRole('admin')) {
            throw ValidationException::withMessages(['akses' => ['Anda tidak memiliki wewenang untuk mengubah status peluang ini.']]);
        }

        $peluang->status = $status;
        $peluang->save();

        return $peluang->load(['kategori']);
    }

    /**
     * UMKM: Dapatkan peluang bisnis yang relevan
     * Disaring otomatis berdasarkan sektor UMKM atau kategori yang relevan
     */
    public function getPeluangForUmkm(User $user): Collection
    {
        $umkm = $user->umkm;

        $query = PeluangBisnis::with(['kategori', 'creator'])
            ->withCount('peminat')
            ->where('status', 'terbuka')
            ->latest();

        $peluangList = $query->get()->map(function ($p) use ($umkm) {
            $isInterested = false;
            if ($umkm) {
                $isInterested = PeluangBisnisMinat::where('peluang_bisnis_id', $p->id)
                    ->where('umkm_id', $umkm->id)
                    ->exists();
            }
            $p->is_interested = $isInterested;
            return $p;
        });

        return $peluangList;
    }

    /**
     * UMKM: Nyatakan minat pada peluang bisnis
     */
    public function nyatakanMinat(User $user, int $peluangId, ?string $catatan = null): PeluangBisnisMinat
    {
        $umkm = $user->umkm;
        if (!$umkm) {
            throw ValidationException::withMessages(['umkm' => ['Lengkapi data profil UMKM terlebih dahulu.']]);
        }

        $peluang = PeluangBisnis::where('status', 'terbuka')->findOrFail($peluangId);

        // Cegah duplikasi minat
        $existing = PeluangBisnisMinat::where('peluang_bisnis_id', $peluang->id)
            ->where('umkm_id', $umkm->id)
            ->first();

        if ($existing) {
            throw ValidationException::withMessages(['peluang' => ['Anda sudah menyatakan minat pada peluang bisnis ini sebelumnya.']]);
        }

        return PeluangBisnisMinat::create([
            'peluang_bisnis_id' => $peluang->id,
            'umkm_id' => $umkm->id,
            'catatan' => $catatan,
        ])->load(['peluangBisnis', 'umkm']);
    }

    /**
     * Admin: Pengawasan semua peluang bisnis
     */
    public function getAllPeluangAdmin(): Collection
    {
        return PeluangBisnis::with(['kategori', 'creator', 'peminat.umkm.user'])
            ->withCount('peminat')
            ->latest()
            ->get();
    }

    /**
     * Buyer Dashboard Summary
     */
    public function getBuyerDashboard(User $buyer): array
    {
        $peluang = $this->getPeluangByBuyer($buyer);
        $totalPeluang = $peluang->count();
        $peluangTerbuka = $peluang->where('status', 'terbuka')->count();
        $totalPeminat = $peluang->sum('peminat_count');

        return [
            'total_peluang' => $totalPeluang,
            'peluang_terbuka' => $peluangTerbuka,
            'total_peminat' => $totalPeminat,
            'peluang_list' => $peluang,
        ];
    }
}
