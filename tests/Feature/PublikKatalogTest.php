<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Produk;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublikKatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DemoSeeder::class);
    }

    public function test_public_can_access_katalog_without_authentication(): void
    {
        $response = $this->getJson('/api/publik/katalog');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'nama_produk',
                        'harga',
                        'status',
                        'umkm' => ['id', 'nama_usaha'],
                        'kategori' => ['id', 'nama_kategori'],
                    ],
                ],
                'meta',
            ]);

        // Pastikan hanya produk berstatus disetujui yang muncul
        $items = $response->json('data');
        foreach ($items as $item) {
            $this->assertEquals('disetujui', $item['status']);
        }
    }

    public function test_public_can_search_katalog_by_keyword(): void
    {
        $response = $this->getJson('/api/publik/katalog?q=Batik');

        $response->assertStatus(200);
        $items = $response->json('data');
        $this->assertNotEmpty($items);
    }

    public function test_public_can_view_detail_of_approved_product(): void
    {
        $produk = Produk::where('status', 'disetujui')->first();

        $response = $this->getJson("/api/publik/produk/{$produk->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $produk->id,
                    'nama_produk' => $produk->nama_produk,
                ],
            ]);
    }

    public function test_public_cannot_view_unapproved_product_detail(): void
    {
        $unapproved = Produk::where('status', 'menunggu_moderasi')->first();

        $response = $this->getJson("/api/publik/produk/{$unapproved->id}");

        $response->assertStatus(404);
    }

    public function test_public_can_view_verified_umkm_profile_and_its_approved_products(): void
    {
        $verifiedUmkm = Umkm::where('status_verifikasi', 'verified')->first();

        $response = $this->getJson("/api/publik/umkm/{$verifiedUmkm->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $verifiedUmkm->id,
                    'nama_usaha' => $verifiedUmkm->nama_usaha,
                    'status_verifikasi' => 'verified',
                ],
            ]);
    }
}
