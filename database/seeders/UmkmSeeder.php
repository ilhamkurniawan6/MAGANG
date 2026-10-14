<?php

namespace Database\Seeders;

use App\Models\KategoriUmkm;
use App\Models\User;
use App\Models\Umkm;
use Illuminate\Database\Seeder;

class UmkmSeeder extends Seeder
{
    public function run(): void
    {
        $katFashion = KategoriUmkm::where('nama_kategori', 'like', '%Fashion%')->first();
        $katKuliner = KategoriUmkm::where('nama_kategori', 'like', '%Kuliner%')->first();
        $katKriya = KategoriUmkm::where('nama_kategori', 'like', '%Kriya%')->first();

        $userBatik = User::where('email', 'umkm.batik@example.com')->first();
        $userKopi = User::where('email', 'umkm.kopi@example.com')->first();
        $userBaru = User::where('email', 'umkm.baru@example.com')->first();

        // 1. UMKM Verified: Batik Sekar Jogja
        if ($userBatik) {
            Umkm::updateOrCreate(
                ['user_id' => $userBatik->id],
                [
                    'kategori_id' => $katFashion?->id ?? 1,
                    'nama_usaha' => 'Batik Sekar Jogja',
                    'deskripsi' => 'Pengrajin batik tulis dan cap premium khas motif Mataraman Yogyakarta dengan pewarna alami ramah lingkungan.',
                    'alamat' => 'Jl. Tirtodipuran No. 45, Mantrijeron, Kota Yogyakarta',
                    'kontak_wa' => '081399887766',
                    'email_usaha' => 'kontak@batiksekarjogja.id',
                    'status_verifikasi' => 'verified',
                ]
            );
        }

        // 2. UMKM Verified: Kopi Merapi Lestari
        if ($userKopi) {
            Umkm::updateOrCreate(
                ['user_id' => $userKopi->id],
                [
                    'kategori_id' => $katKuliner?->id ?? 2,
                    'nama_usaha' => 'Kopi Merapi Lestari',
                    'deskripsi' => 'Produsen biji kopi single origin Arabika & Robusta lereng Gunung Merapi dengan sertifikasi uji cita rasa cupping score 84+.',
                    'alamat' => 'Jl. Kaliurang KM 12, Sleman - DIY',
                    'kontak_wa' => '081244556677',
                    'email_usaha' => 'sales@kopimerapi.com',
                    'status_verifikasi' => 'verified',
                ]
            );
        }

        // 3. UMKM Pending: Gerabah Kasongan Berkah
        if ($userBaru) {
            Umkm::updateOrCreate(
                ['user_id' => $userBaru->id],
                [
                    'kategori_id' => $katKriya?->id ?? 3,
                    'nama_usaha' => 'Gerabah Kasongan Berkah',
                    'deskripsi' => 'Home decor gerabah tanah liat tradisional untuk hotel dan kafe modern.',
                    'alamat' => 'Desa Wisata Kasongan, Bantul - Yogyakarta',
                    'kontak_wa' => '087711223344',
                    'email_usaha' => 'kasonganberkah@gmail.com',
                    'status_verifikasi' => 'pending',
                ]
            );
        }
    }
}
