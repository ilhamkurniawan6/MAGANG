<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\KategoriUmkm;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DemoSeeder::class);
    }

    public function test_user_can_login_with_valid_credentials_and_receive_sanctum_token(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@kadin-jogja.id',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'token',
                'user' => [
                    'id',
                    'nama',
                    'email',
                    'role' => ['id', 'nama_role'],
                ],
            ]);

        $this->assertEquals('admin', $response->json('user.role.nama_role'));
    }

    public function test_login_fails_with_invalid_password(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => 'admin@kadin-jogja.id',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_umkm_self_registration_creates_user_and_pending_umkm_record(): void
    {
        $kategori = KategoriUmkm::first();

        $payload = [
            'nama' => 'Pengrajin Kulit Baru',
            'email' => 'kulit.baru@example.com',
            'password' => 'secret12345',
            'no_wa' => '081234567890',
            'nama_usaha' => 'Mandiri Leather Craft',
            'kategori_id' => $kategori->id,
            'alamat' => 'Jl. Prawirotaman No. 10 Yogyakarta',
            'kontak_wa' => '081234567890',
            'email_usaha' => 'info@mandirileather.id',
            'deskripsi' => 'Kerajinan tas dan dompet kulit asli sapi lokal.',
        ];

        $response = $this->postJson('/api/register-umkm', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'token',
                'user',
                'umkm',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'kulit.baru@example.com',
            'nama' => 'Pengrajin Kulit Baru',
        ]);

        $this->assertDatabaseHas('umkm', [
            'nama_usaha' => 'Mandiri Leather Craft',
            'status_verifikasi' => 'pending',
        ]);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::where('email', 'admin@kadin-jogja.id')->first();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
