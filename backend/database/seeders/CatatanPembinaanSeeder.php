<?php

namespace Database\Seeders;

use App\Models\CatatanPembinaan;
use App\Models\Umkm;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CatatanPembinaanSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@kadin-jogja.id')->first();
        $umkmBatik = Umkm::where('nama_usaha', 'Batik Sekar Jogja')->first();
        $umkmKopi = Umkm::where('nama_usaha', 'Kopi Merapi Lestari')->first();

        if (!$admin) return;

        // 1. Pembinaan untuk Batik Sekar Jogja
        if ($umkmBatik) {
            $umkmBatik->update([
                'praktik_ramah_lingkungan' => ['bahan_baku_lokal', 'pewarna_alami', 'kemasan_minim_plastik'],
                'praktik_terverifikasi' => true,
            ]);

            CatatanPembinaan::updateOrCreate(
                [
                    'umkm_id' => $umkmBatik->id,
                    'catatan' => 'Kunjungan kurasi workshop pewarnaan alami indigofera. Kualitas motif parang barong dan canting malam memenuhi standar ekspor KADIN.',
                ],
                [
                    'admin_id' => $admin->id,
                    'jenis' => 'kunjungan',
                    'tanggal' => Carbon::now()->subDays(20)->toDateString(),
                ]
            );

            CatatanPembinaan::updateOrCreate(
                [
                    'umkm_id' => $umkmBatik->id,
                    'catatan' => 'Follow up pendampingan pengemasan bebas plastik dan persiapan katalog B2B untuk hotel rekanan KADIN.',
                ],
                [
                    'admin_id' => $admin->id,
                    'jenis' => 'follow_up',
                    'tanggal' => Carbon::now()->subDays(5)->toDateString(),
                ]
            );
        }

        // 2. Pembinaan untuk Kopi Merapi Lestari
        if ($umkmKopi) {
            $umkmKopi->update([
                'praktik_ramah_lingkungan' => ['bahan_baku_lokal', 'organik_alami', 'pengolahan_limbah'],
                'praktik_terverifikasi' => true,
            ]);

            CatatanPembinaan::updateOrCreate(
                [
                    'umkm_id' => $umkmKopi->id,
                    'catatan' => 'Pelatihan uji cita rasa (cupping test) dan standarisasi roasting profile bersama Asosiasi Kopi KADIN DIY.',
                ],
                [
                    'admin_id' => $admin->id,
                    'jenis' => 'pelatihan',
                    'tanggal' => Carbon::now()->subDays(15)->toDateString(),
                ]
            );
        }
    }
}
