<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Hanya data dasar (role, kategori, akun admin). Data UMKM/produk/program kosong.
     * Data contoh: php artisan db:seed --class=DemoSeeder
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            KategoriSeeder::class,
            AdminSeeder::class,
        ]);
    }
}
