<?php

namespace Database\Seeders;

use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\Umkm;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $umkmBatik = Umkm::where('nama_usaha', 'Batik Sekar Jogja')->first();
        $umkmKopi = Umkm::where('nama_usaha', 'Kopi Merapi Lestari')->first();
        $umkmPending = Umkm::where('nama_usaha', 'Gerabah Kasongan Berkah')->first();

        $katBatik = KategoriProduk::where('nama_kategori', 'like', '%Batik%')->first();
        $katPangan = KategoriProduk::where('nama_kategori', 'like', '%Makanan%')->first();
        $katKriya = KategoriProduk::where('nama_kategori', 'like', '%Kriya%')->first();

        $produkList = [
            // 1. Produk Disetujui (Batik)
            [
                'umkm_id' => $umkmBatik?->id ?? 1,
                'kategori_produk_id' => $katBatik?->id ?? 1,
                'nama_produk' => 'Kemeja Batik Tulis Sutra Motif Parang Barong',
                'harga' => 485000,
                'deskripsi' => "Kemeja batik pria bahan sutra ATBM kualitas ekspor dengan furing katun halus. Dibuat dengan teknik canting malam dingin oleh pengrajin senior Kota Gede.\n\nSpesifikasi:\n- Bahan: Sutra ATBM Premium\n- Proses: Batik Tulis 100%\n- Ukuran: M, L, XL, XXL\n- Pewarna: Indigofera Alami",
                'status' => 'disetujui',
                'foto_produk' => null,
            ],
            // 2. Produk Disetujui (Batik)
            [
                'umkm_id' => $umkmBatik?->id ?? 1,
                'kategori_produk_id' => $katBatik?->id ?? 1,
                'nama_produk' => 'Kain Panjang Batik Cap Ceplok Kasunanan',
                'harga' => 275000,
                'deskripsi' => "Kain jarik batik cap motif klasik Ceplok cocok untuk bawahan kebaya atau sarung upacara adat. Warna tahan luntur dan bahan katun primissima mori halus.",
                'status' => 'disetujui',
                'foto_produk' => null,
            ],
            // 3. Produk Disetujui (Kopi)
            [
                'umkm_id' => $umkmKopi?->id ?? 2,
                'kategori_produk_id' => $katPangan?->id ?? 2,
                'nama_produk' => 'Kopi Arabika Merapi Honey Process 250gr',
                'harga' => 95000,
                'deskripsi' => "Biji kopi sangrai arabika varietas Kartika ditanam di ketinggian 1.300 mdpl lereng Merapi. Memiliki profil rasa sweet honey, citrusy, dengan aftertaste karamel yang lembut.\n\nTersedia dalam bentuk Biji Utuh (Whole Bean) atau Bubuk (Ground).",
                'status' => 'disetujui',
                'foto_produk' => null,
            ],
            // 4. Produk Disetujui (Kopi)
            [
                'umkm_id' => $umkmKopi?->id ?? 2,
                'kategori_produk_id' => $katPangan?->id ?? 2,
                'nama_produk' => 'Drip Bag Coffee Box (Isi 5 Sachet)',
                'harga' => 65000,
                'deskripsi' => "Kopi praktis saring tetes siap seduh di kantor atau saat bepergian. Menggunakan blend Arabika & Robusta Merapi roasted medium dark.",
                'status' => 'disetujui',
                'foto_produk' => null,
            ],
            // 5. Produk Menunggu Moderasi (Pending Moderasi)
            [
                'umkm_id' => $umkmBatik?->id ?? 1,
                'kategori_produk_id' => $katBatik?->id ?? 1,
                'nama_produk' => 'Outer Kimono Batik Cap Modern Indigo',
                'harga' => 320000,
                'deskripsi' => "Outer kasual model kimono Jepang dengan sentuhan motif mega mendung dan pewarna alam indigo.",
                'status' => 'menunggu_moderasi',
                'foto_produk' => null,
            ],
            // 6. Produk UMKM Baru (Menunggu Moderasi)
            [
                'umkm_id' => $umkmPending?->id ?? 3,
                'kategori_produk_id' => $katKriya?->id ?? 3,
                'nama_produk' => 'Vas Bunga Terracotta Rustik Kasongan',
                'harga' => 125000,
                'deskripsi' => "Vas hias meja dari gerabah tanah liat alami Kasongan dengan finishing matte rustic.",
                'status' => 'menunggu_moderasi',
                'foto_produk' => null,
            ],
        ];

        foreach ($produkList as $p) {
            Produk::updateOrCreate(
                [
                    'umkm_id' => $p['umkm_id'],
                    'nama_produk' => $p['nama_produk']
                ],
                $p
            );
        }
    }
}
