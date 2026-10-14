<?php

namespace App\Services;

use App\Models\Program;
use App\Models\ProgramPeserta;
use App\Models\User;
use App\Models\Umkm;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class ProgramService
{
    public function __construct(
        protected SkorLestariService $skorLestariService,
        protected OpenRouterService $openRouterService
    ) {}

    /**
     * Dapatkan daftar program publik (status: published)
     */
    public function getProgramPublik(): Collection
    {
        return Program::withCount('peserta')
            ->where('status', 'published')
            ->orderBy('tanggal_mulai', 'asc')
            ->get();
    }

    /**
     * Dapatkan rekomendasi program AI untuk UMKM berdasarkan profil & Skor Lestari
     */
    public function getRekomendasiProgram(User $user): array
    {
        $umkm = $user->umkm;
        if (!$umkm) {
            return [
                'rekomendasi' => [],
                'source' => 'none',
                'catatan' => 'Profil UMKM belum lengkap.',
            ];
        }

        $kategoriNama = $umkm->kategori ? $umkm->kategori->nama_kategori : 'Umum';
        $skorData = $this->skorLestariService->hitungSkor($umkm);

        $skorPertumbuhan = $skorData['breakdown']['pertumbuhan_usaha']['skor_mentah'] ?? 50;
        $skorPembinaan = $skorData['breakdown']['intensitas_pembinaan']['skor_mentah'] ?? 30;
        $skorLingkungan = $skorData['breakdown']['praktik_ramah_lingkungan']['skor_mentah'] ?? 20;

        // Ambil program published yang belum didaftari oleh UMKM
        $programTersedia = Program::where('status', 'published')
            ->whereDoesntHave('peserta', function ($q) use ($umkm) {
                $q->where('umkm_id', $umkm->id);
            })
            ->orderBy('tanggal_mulai', 'asc')
            ->get();

        if ($programTersedia->isEmpty()) {
            return [
                'rekomendasi' => [],
                'source' => 'empty',
                'catatan' => 'Tidak ada program baru yang tersedia saat ini.',
            ];
        }

        $programListSimple = $programTersedia->map(function ($p) {
            return [
                'id' => $p->id,
                'judul' => $p->judul,
                'jenis' => $p->jenis,
                'deskripsi' => substr($p->deskripsi, 0, 150),
            ];
        })->values()->toArray();

        $prompt = "Berdasarkan profil UMKM kategori {$kategoriNama} dengan skor keberlanjutan: pertumbuhan={$skorPertumbuhan}, pembinaan={$skorPembinaan}, lingkungan={$skorLingkungan}, dan daftar program tersedia berikut: " . json_encode($programListSimple, JSON_UNESCAPED_UNICODE) . ". Rekomendasikan maksimal 3 program yang paling relevan untuk UMKM ini beserta alasan singkat (1 kalimat per program). Balas HANYA dalam format JSON array: [{\"program_id\": id, \"alasan\": \"...\"}].";

        $messages = [
            ['role' => 'system', 'content' => 'Anda adalah konsultan kurasi & pendampingan UMKM KADIN DIY.'],
            ['role' => 'user', 'content' => $prompt],
        ];

        $aiResponse = $this->openRouterService->chatCompletion($messages);
        $rekomendasiFinal = [];
        $source = 'fallback';

        if ($aiResponse['success'] && !empty($aiResponse['content'])) {
            $parsed = $this->openRouterService->parseJsonClean($aiResponse['content']);
            if (is_array($parsed)) {
                $programMap = $programTersedia->keyBy('id');

                foreach ($parsed as $item) {
                    if (isset($item['program_id']) && $programMap->has($item['program_id'])) {
                        $p = $programMap->get($item['program_id']);
                        $rekomendasiFinal[] = [
                            'program' => $p,
                            'alasan' => $item['alasan'] ?? 'Sangat sesuai dengan kebutuhan peningkatan kapasitas usaha Anda.',
                        ];
                    }
                }

                if (!empty($rekomendasiFinal)) {
                    $source = 'ai';
                }
            }
        }

        // Fallback cerdas jika AI gagal/offline
        if (empty($rekomendasiFinal)) {
            $source = 'fallback';
            $pilihanProgram = $programTersedia->take(3);

            foreach ($pilihanProgram as $p) {
                $alasan = "Direkomendasikan untuk memperluas jejaring dan peningkatan kapasitas usaha {$kategoriNama}.";
                if ($skorPembinaan < 50) {
                    $alasan = "Prioritas pendampingan KADIN untuk meningkatkan partisipasi & pembinaan berkala.";
                } elseif ($skorLingkungan < 40) {
                    $alasan = "Membantu adaptasi praktik ramah lingkungan dan sertifikasi standar hijau KADIN.";
                }

                $rekomendasiFinal[] = [
                    'program' => $p,
                    'alasan' => $alasan,
                ];
            }
        }

        return [
            'rekomendasi' => $rekomendasiFinal,
            'source' => $source,
            'skor_lestari_summary' => [
                'total' => $skorData['skor_akhir'],
                'level' => $skorData['level'],
            ],
        ];
    }

    /**
     * Dapatkan daftar program untuk UMKM termasuk status keikutsertaannya
     */
    public function getProgramForUmkm(User $user): Collection
    {
        $umkm = $user->umkm;

        return Program::withCount('peserta')
            ->with(['peserta' => function ($q) use ($umkm) {
                if ($umkm) {
                    $q->where('umkm_id', $umkm->id);
                }
            }])
            ->whereIn('status', ['published', 'selesai'])
            ->orderBy('tanggal_mulai', 'desc')
            ->get()
            ->map(function ($program) use ($umkm) {
                $isRegistered = false;
                $statusPendaftaran = null;

                if ($umkm && $program->peserta->isNotEmpty()) {
                    $peserta = $program->peserta->first();
                    $isRegistered = true;
                    $statusPendaftaran = $peserta->status;
                }

                $program->is_registered = $isRegistered;
                $program->status_pendaftaran = $statusPendaftaran;
                return $program;
            });
    }

    /**
     * UMKM mendaftar ke suatu program
     */
    public function daftarProgram(User $user, int $programId): ProgramPeserta
    {
        $umkm = $user->umkm;
        if (!$umkm) {
            throw ValidationException::withMessages(['umkm' => ['Profil UMKM belum lengkap.']]);
        }

        $program = Program::where('status', 'published')->findOrFail($programId);

        // Cegah pendaftaran ganda
        $existing = ProgramPeserta::where('program_id', $program->id)
            ->where('umkm_id', $umkm->id)
            ->first();

        if ($existing) {
            throw ValidationException::withMessages(['program' => ['UMKM Anda sudah terdaftar pada program ini.']]);
        }

        return ProgramPeserta::create([
            'program_id' => $program->id,
            'umkm_id' => $umkm->id,
            'status' => 'terdaftar',
        ])->load(['program', 'umkm']);
    }

    /**
     * Admin: List semua program
     */
    public function getProgramAdmin(array $filters = []): Collection
    {
        $query = Program::withCount('peserta')->with('creator')->latest();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['jenis'])) {
            $query->where('jenis', $filters['jenis']);
        }

        return $query->get();
    }

    /**
     * Admin: Buat program baru
     */
    public function createProgram(User $admin, array $data): Program
    {
        $data['created_by'] = $admin->id;
        return Program::create($data)->load('creator');
    }

    /**
     * Admin: Update program
     */
    public function updateProgram(int $id, array $data): Program
    {
        $program = Program::findOrFail($id);
        $program->update($data);
        return $program->fresh(['creator']);
    }

    /**
     * Admin: Hapus program
     */
    public function deleteProgram(int $id): bool
    {
        $program = Program::findOrFail($id);
        return $program->delete();
    }

    /**
     * Admin: List peserta terdaftar pada program
     */
    public function getPesertaProgram(int $programId): Collection
    {
        $program = Program::findOrFail($programId);
        return ProgramPeserta::with(['umkm.user', 'umkm.kategori'])
            ->where('program_id', $program->id)
            ->get();
    }

    /**
     * Admin: Update status presensi peserta (terdaftar, hadir, tidak_hadir)
     */
    public function updateKehadiranPeserta(int $programId, int $pesertaId, string $status): ProgramPeserta
    {
        $peserta = ProgramPeserta::where('program_id', $programId)->findOrFail($pesertaId);
        $peserta->status = $status;
        $peserta->save();

        // Jika hadir, hitung ulang skor lestari UMKM terkait
        if ($status === 'hadir' && $peserta->umkm) {
            app(SkorLestariService::class)->updateSkorUmkm($peserta->umkm);
        }

        return $peserta->load(['umkm.user']);
    }
}
