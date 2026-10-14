<?php

namespace Tests\Feature;

use App\Models\KategoriUmkm;
use App\Models\Program;
use App\Models\Role;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RekomendasiProgramAiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Umkm $umkm;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['nama_role' => 'umkm', 'deskripsi' => 'UMKM']);
        Role::create(['nama_role' => 'admin', 'deskripsi' => 'Admin']);

        $this->user = User::create([
            'nama' => 'Owner Batik Rekomendasi',
            'email' => 'batik@rekomendasi.com',
            'password' => bcrypt('password'),
            'role_id' => 1,
        ]);

        $kat = KategoriUmkm::create(['nama_kategori' => 'Fashion & Batik Jogja']);

        $this->umkm = Umkm::create([
            'user_id' => $this->user->id,
            'kategori_id' => $kat->id,
            'nama_usaha' => 'Batik Tulis Alami Jogja',
            'alamat' => 'Yogyakarta',
            'kontak_wa' => '081234567891',
            'status_verifikasi' => 'verified',
            'skor_lestari' => 65,
            'skor_lestari_level' => 'silver',
        ]);

        // Buat program yang published
        Program::create([
            'created_by' => 1,
            'judul' => 'Akselerasi Ekspor Fashion Ramah Lingkungan 2026',
            'deskripsi' => 'Pelatihan intensif kurasi ekspor untuk UMKM Batik dan Eco-fashion.',
            'jenis' => 'pelatihan',
            'tanggal_mulai' => now()->addDays(7),
            'tanggal_selesai' => now()->addDays(14),
            'status' => 'published',
        ]);

        Program::create([
            'created_by' => 1,
            'judul' => 'Pendampingan Standar Hijau KADIN',
            'deskripsi' => 'Pendampingan penerapan efisiensi energi dan kemasan ramah lingkungan.',
            'jenis' => 'pendampingan',
            'tanggal_mulai' => now()->addDays(15),
            'tanggal_selesai' => now()->addDays(20),
            'status' => 'published',
        ]);
    }

    public function test_endpoint_rekomendasi_program_mengembalikan_struktur_valid(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/umkm/rekomendasi-program');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'program' => [
                            'id',
                            'judul',
                            'jenis',
                        ],
                        'alasan',
                    ],
                ],
                'meta' => [
                    'source',
                    'skor_lestari',
                ],
            ]);

        $this->assertTrue(count($response->json('data')) <= 3);
        $this->assertNotEmpty($response->json('data')[0]['alasan']);
    }

    public function test_fallback_rekomendasi_program_saat_api_key_kosong(): void
    {
        config(['services.openrouter.api_key' => null]);
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/umkm/rekomendasi-program');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'meta' => [
                    'source' => 'fallback',
                ],
            ]);

        $this->assertNotEmpty($response->json('data'));
    }
}
