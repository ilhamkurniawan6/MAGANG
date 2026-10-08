<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Models\Umkm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Login kredensial & kembalikan Sanctum token beserta data role
     */
    public function login(string $email, string $password): array
    {
        $user = User::with(['role', 'umkm.kategori'])->where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Alamat email atau kata sandi tidak cocok.'],
            ]);
        }

        if ($user->status !== 'aktif') {
            throw ValidationException::withMessages([
                'email' => ['Akun Anda berstatus nonaktif. Silakan hubungi administrator.'],
            ]);
        }

        // Generate Sanctum token
        $token = $user->createToken('kadin-auth-token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    /**
     * Registrasi mandiri UMKM (buat User role UMKM + record UMKM status pending)
     */
    public function registerUmkm(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $roleUmkm = Role::where('nama_role', 'umkm')->firstOrFail();

            // 1. Buat User
            $user = User::create([
                'role_id' => $roleUmkm->id,
                'nama' => $data['nama'],
                'email' => $data['email'],
                'password' => $data['password'],
                'no_wa' => $data['no_wa'] ?? null,
                'status' => 'aktif',
            ]);

            // 2. Buat Record UMKM (Status pending)
            $umkm = Umkm::create([
                'user_id' => $user->id,
                'kategori_id' => $data['kategori_id'],
                'nama_usaha' => $data['nama_usaha'],
                'deskripsi' => $data['deskripsi'] ?? null,
                'alamat' => $data['alamat'],
                'kontak_wa' => $data['kontak_wa'] ?? $data['no_wa'],
                'email_usaha' => $data['email_usaha'] ?? $data['email'],
                'status_verifikasi' => 'pending',
            ]);

            $token = $user->createToken('kadin-auth-token')->plainTextToken;

            return [
                'token' => $token,
                'user' => $user->load(['role', 'umkm.kategori']),
                'umkm' => $umkm,
            ];
        });
    }

    /**
     * Revoke token user saat ini
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
