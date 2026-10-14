<?php

namespace Tests\Feature;

use App\Models\KategoriProduk;
use App\Models\KategoriUmkm;
use App\Models\PeluangBisnis;
use App\Models\Produk;
use App\Models\Role;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BusinessMatchingAiTest extends TestCase
{
    use RefreshDatabase;

    protected User $buyerUser;
    protected PeluangBisnis $peluang;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['nama_role' => 'buyer', 'deskripsi' => 'Buyer']);
        Role::create(['nama_role' => 'umkm', 'deskripsi' => 'UMKM']);
        Role::create(['nama_role' => 'admin', 'deskripsi' => 'Admin']);

        $this->buyerUser = User::create([
            'nama' => 'PT Pengadaan Hotel Bintang 5',
            'email' => 'buyer@hotel.com',
            'password' => bcrypt('password'),
            'role_id' => 1,
        ]);

        $katUmkm = KategoriUmkm::create(['nama_kategori' => 'Kuliner & Pangan']);
        $katProduk = KategoriProduk::create(['nama_kategori' => 'Makanan & Minuman']);

        $this->peluang = PeluangBisnis::create([
            'created_by' => $this->buyerUser->id,
            'kategori_produk_id' => $katProduk->id,
            'judul' => 'Pengadaan 1000 Box Bakpia & Snack Tradisional',
            'deskripsi' => 'Dibutuhkan suplai reguler snack tradisional dengan sertifikat halal & kemasan eco-friendly.',
            'kapasitas' => '250 box/minggu',
            'target_volume' => '1000 Box',
            'status' => 'terbuka',
        ]);

        // Buat 3 UMKM verified dengan skor lestari berbeda
        for ($i = 1; $i <= 3; $i++) {
            $u = User::create([
                'nama' => "Owner Snack {$i}",
                'email' => "snack{$i}@umkm.com",
                'password' => bcrypt('password'),
                'role_id' => 2,
            ]);

            $umkm = Umkm::create([
                'user_id' => $u->id,
                'kategori_id' => $katUmkm->id,
                'nama_usaha' => "Bakpia Lestari {$i}",
                'deskripsi' => "Spesialis oleh-oleh bakpia Jogja nomor {$i}",
                'alamat' => 'Yogyakarta',
                'kontak_wa' => "08123456780{$i}",
                'status_verifikasi' => 'verified',
                'skor_lestari' => 60 + ($i * 10),
                'skor_lestari_level' => $i === 3 ? 'gold' : 'silver',
            ]);

            Produk::create([
                'umkm_id' => $umkm->id,
                'kategori_produk_id' => $katProduk->id,
                'nama_produk' => "Bakpia Premium {$i}",
                'harga' => 35000,
                'deskripsi' => 'Bakpia basah aneka rasa',
                'status' => 'disetujui',
            ]);
        }
    }

    public function test_rekomendasi_umkm_menyaring_kandidat_dan_memberikan_ranking(): void
    {
        Sanctum::actingAs($this->buyerUser);

        $response = $this->getJson("/api/buyer/peluang-bisnis/{$this->peluang->id}/rekomendasi-umkm");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'umkm' => [
                            'id',
                            'nama_usaha',
                        ],
                        'skor_kecocokan',
                        'alasan',
                    ],
                ],
                'meta' => [
                    'source',
                    'total_kandidat_disaring',
                ],
            ]);

        $this->assertNotEmpty($response->json('data'));
        $this->assertTrue(count($response->json('data')) <= 5);
        $this->assertGreaterThan(0, $response->json('meta.total_kandidat_disaring'));
    }
}
