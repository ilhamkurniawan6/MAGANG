<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Akun admin awal. Segera ganti password setelah login.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $roleAdmin = Role::where('nama_role', 'admin')->firstOrFail();

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
    }
}
