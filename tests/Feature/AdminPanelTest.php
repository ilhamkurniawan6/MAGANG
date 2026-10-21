<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Umkm;
use App\Models\Produk;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DemoSeeder::class);
    }

    public function test_admin_can_access_dashboard_statistics(): void
    {
        $admin = User::where('email', 'admin@kadin-jogja.id')->first();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'umkm_stats' => ['total', 'pending', 'verified', 'rejected'],
                    'produk_stats' => ['total', 'menunggu_moderasi', 'disetujui', 'ditolak'],
                ],
            ]);
    }

    public function test_admin_can_verify_pending_umkm(): void
    {
        $admin = User::where('email', 'admin@kadin-jogja.id')->first();
        $token = $admin->createToken('test-token')->plainTextToken;

        $pendingUmkm = Umkm::where('status_verifikasi', 'pending')->first();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/admin/umkm/{$pendingUmkm->id}/verifikasi", [
                'status_verifikasi' => 'verified',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $pendingUmkm->id,
                    'status_verifikasi' => 'verified',
                ],
            ]);

        $this->assertDatabaseHas('umkm', [
            'id' => $pendingUmkm->id,
            'status_verifikasi' => 'verified',
        ]);
    }

    public function test_admin_can_moderate_product_to_disetujui(): void
    {
        $admin = User::where('email', 'admin@kadin-jogja.id')->first();
        $token = $admin->createToken('test-token')->plainTextToken;

        $pendingProduk = Produk::where('status', 'menunggu_moderasi')->first();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/admin/produk/{$pendingProduk->id}/moderasi", [
                'status' => 'disetujui',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $pendingProduk->id,
                    'status' => 'disetujui',
                ],
            ]);

        $this->assertDatabaseHas('produk', [
            'id' => $pendingProduk->id,
            'status' => 'disetujui',
        ]);
    }

    public function test_non_admin_cannot_access_admin_endpoints(): void
    {
        $umkmUser = User::where('email', 'umkm.batik@example.com')->first();
        $token = $umkmUser->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/admin/dashboard');

        $response->assertStatus(403);
    }
}
