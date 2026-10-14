<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\SkorLestariService;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            KategoriSeeder::class,
            UserSeeder::class,
            UmkmSeeder::class,
            ProdukSeeder::class,
            CatatanPembinaanSeeder::class,
            ProgramSeeder::class,
            PeluangBisnisSeeder::class,
        ]);

        // Hitung ulang Skor KADIN Lestari untuk semua UMKM binaan
        app(SkorLestariService::class)->hitungUlangSemua();
    }
}
