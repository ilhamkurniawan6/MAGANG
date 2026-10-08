<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\ProgramPeserta;
use App\Models\Umkm;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@kadin-jogja.id')->first();
        if (!$admin) return;

        $umkmBatik = Umkm::where('nama_usaha', 'Batik Sekar Jogja')->first();
        $umkmKopi = Umkm::where('nama_usaha', 'Kopi Merapi Lestari')->first();

        // 1. Program Pelatihan Ekspor
        $prog1 = Program::updateOrCreate(
            ['judul' => 'Akselerasi Ekspor Produk Kriya & Wastra Jogja ke Pasar ASEAN'],
            [
                'created_by' => $admin->id,
                'deskripsi' => "Program pendampingan intensif bagi UMKM sektor kerajinan tangan dan batik dalam memenuhi standar kurasi ekspor, sertifikasi asal barang, dan negosiasi buyer internasional.\n\nFasilitas: Konsultasi 1-on-1 dengan praktisi ekspor, bedah katalog produk, dan matching buyer.",
                'jenis' => 'pelatihan',
                'tanggal_mulai' => Carbon::now()->addDays(7)->toDateString(),
                'tanggal_selesai' => Carbon::now()->addDays(9)->toDateString(),
                'status' => 'published',
            ]
        );

        // 2. Program Seminar Standarisasi Pangan
        $prog2 = Program::updateOrCreate(
            ['judul' => 'Workshop Standarisasi Mutu & Sertifikasi Halal/HACCP Olahan Pangan'],
            [
                'created_by' => $admin->id,
                'deskripsi' => 'Bimbingan teknis pengurusan izin edar BPOM, sertifikasi Halal, dan implementasi sanitasi higienis standar industri horeka.',
                'jenis' => 'seminar',
                'tanggal_mulai' => Carbon::now()->addDays(14)->toDateString(),
                'tanggal_selesai' => Carbon::now()->addDays(15)->toDateString(),
                'status' => 'published',
            ]
        );

        // 3. Program Selesai (Historis untuk skor)
        $prog3 = Program::updateOrCreate(
            ['judul' => 'Klinik Bisnis: Literasi Keuangan & Tata Kelola UMKM Lestari'],
            [
                'created_by' => $admin->id,
                'deskripsi' => 'Pembekalan pembukuan digital dan tata kelola bisnis berkelanjutan bersama KADIN Kota Yogyakarta.',
                'jenis' => 'pendampingan',
                'tanggal_mulai' => Carbon::now()->subDays(30)->toDateString(),
                'tanggal_selesai' => Carbon::now()->subDays(29)->toDateString(),
                'status' => 'selesai',
            ]
        );

        // Daftarkan UMKM ke Program Historis
        if ($umkmBatik && $prog3) {
            ProgramPeserta::updateOrCreate(
                [
                    'program_id' => $prog3->id,
                    'umkm_id' => $umkmBatik->id,
                ],
                [
                    'status' => 'hadir',
                ]
            );
        }

        if ($umkmKopi && $prog3) {
            ProgramPeserta::updateOrCreate(
                [
                    'program_id' => $prog3->id,
                    'umkm_id' => $umkmKopi->id,
                ],
                [
                    'status' => 'hadir',
                ]
            );
        }
    }
}
