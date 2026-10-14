<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roleAdmin = Role::where('nama_role', 'admin')->firstOrFail();
        $roleUmkm = Role::where('nama_role', 'umkm')->firstOrFail();
        $roleBuyer = Role::where('nama_role', 'buyer')->firstOrFail();

        // 1. Admin KADIN
        User::updateOrCreate(
            ['email' => 'admin@kadin-jogja.id'],
            [
                'role_id' => $roleAdmin->id,
                'nama' => 'Administrator KADIN Hub',
                'password' => Hash::make('password'),
                'no_wa' => '081223344556',
                'status' => 'aktif',
            ]
        );

        // 2. UMKM Verified 1: Batik Sekar Jogja
        User::updateOrCreate(
            ['email' => 'umkm.batik@example.com'],
            [
                'role_id' => $roleUmkm->id,
                'nama' => 'Ibu Sekar Wardani',
                'password' => Hash::make('password'),
                'no_wa' => '081399887766',
                'status' => 'aktif',
            ]
        );

        // 3. UMKM Verified 2: Kopi Merapi Lestari
        User::updateOrCreate(
            ['email' => 'umkm.kopi@example.com'],
            [
                'role_id' => $roleUmkm->id,
                'nama' => 'Bapak Joko Susilo',
                'password' => Hash::make('password'),
                'no_wa' => '081244556677',
                'status' => 'aktif',
            ]
        );

        // 4. UMKM Pending: Gerabah Kasongan Berkah
        User::updateOrCreate(
            ['email' => 'umkm.baru@example.com'],
            [
                'role_id' => $roleUmkm->id,
                'nama' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'no_wa' => '087711223344',
                'status' => 'aktif',
            ]
        );

        // 5. Buyer Partner
        User::updateOrCreate(
            ['email' => 'buyer@partner.com'],
            [
                'role_id' => $roleBuyer->id,
                'nama' => 'Manager Pengadaan Hotel Malioboro',
                'password' => Hash::make('password'),
                'no_wa' => '081988776655',
                'status' => 'aktif',
            ]
        );
    }
}
