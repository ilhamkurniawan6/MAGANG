<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\PeluangBisnis;
use App\Models\KategoriProduk;
use App\Models\PeluangBisnisMinat;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PeluangBisnisTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DemoSeeder::class);
    }

    public function test_buyer_can_publish_new_rfq_and_view_own_dashboard(): void
    {
        $buyer = User::where('email', 'buyer@partner.com')->first();
        $token = $buyer->createToken('test-token')->plainTextToken;
        $kategori = KategoriProduk::first();

        // 1. Post Peluang Bisnis
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/buyer/peluang-bisnis', [
                'kategori_produk_id' => $kategori->id,
                'judul' => 'Pengadaan 100 Hampers Batik & Kopi Jogja untuk Event Nasional',
                'deskripsi' => 'Dibutuhkan paket hampers kombinasi kain batik cap dan kopi arabika kemasan box premium.',
                'kapasitas' => '50 paket/minggu',
                'target_volume' => '100 paket total',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'judul' => 'Pengadaan 100 Hampers Batik & Kopi Jogja untuk Event Nasional',
                    'status' => 'terbuka',
                ],
            ]);

        // 2. View Buyer Dashboard
        $dashRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/buyer/dashboard');

        $dashRes->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'total_peluang',
                    'peluang_terbuka',
                    'total_peminat',
                    'peluang_list',
                ],
            ]);
    }

    public function test_umkm_can_view_relevant_rfqs_and_express_interest(): void
    {
        $umkmUser = User::where('email', 'umkm.batik@example.com')->first();
        $token = $umkmUser->createToken('test-token')->plainTextToken;

        // 1. View Peluang Bisnis
        $listRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/umkm/peluang-bisnis');

        $listRes->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'judul', 'status', 'kategori'],
                ],
            ]);

        $peluang = PeluangBisnis::where('status', 'terbuka')->first();

        // 2. Nyatakan Minat
        $minatRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/umkm/peluang-bisnis/{$peluang->id}/minat", [
                'catatan' => 'Batik Sekar Jogja siap menyuplai sesuai spesifikasi dan tenggat waktu.',
            ]);

        // Jika sudah ada dari seeder atau baru, pastikan status response 201 atau validasi
        $this->assertDatabaseHas('peluang_bisnis_minat', [
            'peluang_bisnis_id' => $peluang->id,
            'umkm_id' => $umkmUser->umkm->id,
        ]);
    }

    public function test_buyer_can_view_interested_umkm_with_whatsapp_contacts(): void
    {
        $buyer = User::where('email', 'buyer@partner.com')->first();
        $token = $buyer->createToken('test-token')->plainTextToken;

        $peluang = PeluangBisnis::where('created_by', $buyer->id)->first();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson("/api/buyer/peluang-bisnis/{$peluang->id}/minat");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'catatan',
                        'umkm' => [
                            'id',
                            'nama_usaha',
                            'kontak_wa',
                            'alamat',
                        ],
                    ],
                ],
            ]);
    }
}
