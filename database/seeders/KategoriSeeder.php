<?php

namespace Database\Seeders;

use App\Models\KategoriUmkm;
use App\Models\KategoriProduk;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kategori UMKM (Sektor Usaha)
        $kategoriUmkm = [
            ['nama_kategori' => 'Fashion & Batik Jogja', 'deskripsi' => 'Busana tradisional, batik tulis/cap, tenun lurik, dan apparel modern bernuansa lokal.'],
            ['nama_kategori' => 'Kuliner & Olahan Pangan', 'deskripsi' => 'Makanan khas, kudapan oleh-oleh, kopi lokal, dan produk pangan olahan sehat.'],
            ['nama_kategori' => 'Kriya & Kerajinan Tangan', 'deskripsi' => 'Kerajinan kayu, perak Kotagede, kulit, gerabah Kasongan, dan dekorasi rumah.'],
            ['nama_kategori' => 'Herbal & Wellness', 'deskripsi' => 'Jamu tradisional, produk perawatan tubuh alami, dan minyak esensial herbal.'],
            ['nama_kategori' => 'Jasa Kreatif & Digital', 'deskripsi' => 'Desain grafis, fotografi, percetakan, dan solusi digital lokal.'],
        ];

        foreach ($kategoriUmkm as $ku) {
            KategoriUmkm::updateOrCreate(
                ['nama_kategori' => $ku['nama_kategori']],
                $ku
            );
        }

        // 2. Kategori Produk
        $kategoriProduk = [
            ['nama_kategori' => 'Batik & Busana', 'deskripsi' => 'Kemeja batik, kain panjang, dress etnik, sarung lurik, dan selendang.'],
            ['nama_kategori' => 'Makanan & Minuman', 'deskripsi' => 'Bakpia, kopi robusta/arabika, keripik, sambal kemasan, dan olahan herbal.'],
            ['nama_kategori' => 'Kriya & Dekorasi', 'deskripsi' => 'Pajangan kayu ukir, keramik hias, wayang, dan anyaman bambu.'],
            ['nama_kategori' => 'Aksesoris & Perhiasan', 'deskripsi' => 'Perhiasan perak filigree, tas kulit nabati, dan dompet motif etnik.'],
            ['nama_kategori' => 'Kebugaran & Herbal', 'deskripsi' => 'Ekstrak jamu bubuk, sabun rempah, lulur alami, dan aromaterapi.'],
        ];

        foreach ($kategoriProduk as $kp) {
            KategoriProduk::updateOrCreate(
                ['nama_kategori' => $kp['nama_kategori']],
                $kp
            );
        }
    }
}
