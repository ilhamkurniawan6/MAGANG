<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Produk;
use App\Models\KategoriProduk;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UmkmHubTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_umkm_user_can_view_own_profile_and_dashboard(): void
    {
        $user = User::where('email', 'umkm.batik@example.com')->first();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/umkm/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'umkm',
                    'produk_stats' => ['total', 'menunggu_moderasi', 'disetujui', 'ditolak'],
                ],
            ]);
    }

    public function test_umkm_user_can_update_profile(): void
    {
        $user = User::where('email', 'umkm.batik@example.com')->first();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/umkm/profil', [
                'nama_usaha' => 'Batik Sekar Mataram Updated',
                'kategori_id' => $user->umkm->kategori_id,
                'alamat' => 'Jl. Baru No. 99 Yogyakarta',
                'kontak_wa' => '081399887766',
                'deskripsi' => 'Deskripsi usaha yang baru diperbarui.',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('umkm', [
            'id' => $user->umkm->id,
            'nama_usaha' => 'Batik Sekar Mataram Updated',
        ]);
    }

    public function test_umkm_can_create_product_and_it_automatically_becomes_menunggu_moderasi(): void
    {
        $user = User::where('email', 'umkm.batik@example.com')->first();
        $token = $user->createToken('test-token')->plainTextToken;
        $kategori = KategoriProduk::first();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/umkm/produk', [
                'kategori_produk_id' => $kategori->id,
                'nama_produk' => 'Kain Batik Tulis Naga Raja',
                'harga' => 850000,
                'deskripsi' => 'Kain batik tulis motif naga raja eksklusif.',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'nama_produk' => 'Kain Batik Tulis Naga Raja',
                    'status' => 'menunggu_moderasi',
                ],
            ]);

        $this->assertDatabaseHas('produk', [
            'nama_produk' => 'Kain Batik Tulis Naga Raja',
            'status' => 'menunggu_moderasi',
            'umkm_id' => $user->umkm->id,
        ]);
    }

    public function test_umkm_cannot_view_or_edit_products_belonging_to_other_umkm(): void
    {
        $userBatik = User::where('email', 'umkm.batik@example.com')->first();
        $userKopi = User::where('email', 'umkm.kopi@example.com')->first();

        $kopiProduk = Produk::where('umkm_id', $userKopi->umkm->id)->first();
        $tokenBatik = $userBatik->createToken('test-token')->plainTextToken;

        // Coba akses produk milik UMKM Kopi
        $response = $this->withHeader('Authorization', 'Bearer ' . $tokenBatik)
            ->getJson('/api/umkm/produk/' . $kopiProduk->id);

        $response->assertStatus(404);
    }
}
