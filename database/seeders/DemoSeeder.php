<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\SkorLestariService;

/**
 * Data contoh (dummy). Tidak dipanggil otomatis.
 * Jalankan manual: php artisan db:seed --class=DemoSeeder
 * Dipakai juga oleh automated test.
 */
class DemoSeeder extends Seeder
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

        app(SkorLestariService::class)->hitungUlangSemua();
    }
}
