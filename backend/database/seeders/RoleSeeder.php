<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nama_role' => 'admin',
                'deskripsi' => 'Administrator KADIN dengan hak akses penuh ke verifikasi UMKM dan moderasi produk.',
            ],
            [
                'nama_role' => 'umkm',
                'deskripsi' => 'Pelaku usaha binaan KADIN yang dapat mengelola profil usaha dan katalog produk.',
            ],
            [
                'nama_role' => 'buyer',
                'deskripsi' => 'Mitra bisnis & buyer korporat/ritel (Fase 1: Placeholder dashboard).',
            ],
        ];

        foreach ($roles as $r) {
            Role::updateOrCreate(
                ['nama_role' => $r['nama_role']],
                $r
            );
        }
    }
}
