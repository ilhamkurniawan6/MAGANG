<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use App\Models\PeluangBisnis;
use App\Models\PeluangBisnisMinat;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Database\Seeder;

class PeluangBisnisSeeder extends Seeder
{
    public function run(): void
    {
        $buyer = User::where('email', 'buyer@partner.com')->first();
        $umkmBatik = Umkm::where('nama_usaha', 'Batik Sekar Jogja')->first();
        $umkmKopi = Umkm::where('nama_usaha', 'Kopi Merapi Lestari')->first();

        $katBatik = KategoriProduk::where('nama_kategori', 'like', '%Batik%')->first();
        $katPangan = KategoriProduk::where('nama_kategori', 'like', '%Makanan%')->first();

        if (!$buyer) return;

        // 1. Peluang Pengadaan Seragam Batik Hotel
        $peluang1 = PeluangBisnis::updateOrCreate(
            ['judul' => 'Pengadaan Seragam Batik Front Office & Staf Hotel Bintang 4 (500 Pcs)'],
            [
                'created_by' => $buyer->id,
                'kategori_produk_id' => $katBatik?->id ?? 1,
                'deskripsi' => "Dibutuhkan suplier UMKM binaan KADIN untuk pengadaan kemeja batik pria dan wanita dengan motif khas Jogja kontemporer.\n\nKriteria:\n- Bahan katun primissima mori adem dan tahan cuci mesin\n- Kapasitas produksi sanggup menyelesaikan 500 pcs dalam waktu 45 hari kalender\n- Memiliki legalitas usaha terverifikasi",
                'kapasitas' => 'Minimal 100 pcs/bulan',
                'target_volume' => 'Total 500 pcs',
                'status' => 'terbuka',
            ]
        );

        // 2. Peluang Pasokan Biji Kopi untuk Jaringan Kafe
        $peluang2 = PeluangBisnis::updateOrCreate(
            ['judul' => 'Pasokan Rutin Biji Kopi Arabika Merapi Roasting Medium (150 Kg / Bulan)'],
            [
                'created_by' => $buyer->id,
                'kategori_produk_id' => $katPangan?->id ?? 2,
                'deskripsi' => "Mencari roastery / kelompok tani kopi lokal Yogyakarta untuk pasokan rutin espresso blend ke 4 outlet kafe di Malioboro dan Prawirotaman.\n\nKriteria:\n- Kopi arabika single origin lereng Merapi grade 1\n- Konsistensi roasting terjaga dengan report profil sangrai\n- Kontrak kerja sama tahunan (12 bulan)",
                'kapasitas' => 'Minimal 150 kg/bulan',
                'target_volume' => 'Kontrak 1.800 kg/tahun',
                'status' => 'terbuka',
            ]
        );

        // Minat dari UMKM
        if ($umkmBatik && $peluang1) {
            PeluangBisnisMinat::updateOrCreate(
                [
                    'peluang_bisnis_id' => $peluang1->id,
                    'umkm_id' => $umkmBatik->id,
                ],
                [
                    'catatan' => 'Batik Sekar Jogja siap menyuplai kemeja batik cap primissima mori kualitas hotel. Kapasitas workshop kami mencapai 250 pcs/bulan.',
                ]
            );
        }

        if ($umkmKopi && $peluang2) {
            PeluangBisnisMinat::updateOrCreate(
                [
                    'peluang_bisnis_id' => $peluang2->id,
                    'umkm_id' => $umkmKopi->id,
                ],
                [
                    'catatan' => 'Kopi Merapi Lestari memiliki kapasitas sangrai hingga 500 kg/bulan dengan mesin roaster Probat dan jaminan cupping score 84+.',
                ]
            );
        }
    }
}
