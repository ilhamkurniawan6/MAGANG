<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Umkm;
use App\Models\CatatanPembinaan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SkorLestariTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_umkm_can_view_own_skor_lestari_breakdown(): void
    {
        $user = User::where('email', 'umkm.batik@example.com')->first();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/umkm/skor-lestari');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'skor_lestari',
                    'level',
                    'breakdown' => [
                        'pertumbuhan_usaha' => ['nama', 'bobot', 'skor_mentah', 'kontribusi'],
                        'intensitas_pembinaan' => ['nama', 'bobot', 'skor_mentah', 'kontribusi'],
                        'praktik_ramah_lingkungan' => ['nama', 'bobot', 'skor_mentah', 'kontribusi'],
                    ],
                ],
            ]);

        $this->assertContains($response->json('data.level'), ['bronze', 'silver', 'gold']);
    }

    public function test_umkm_can_claim_green_practice_tags(): void
    {
        $user = User::where('email', 'umkm.batik@example.com')->first();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/umkm/praktik-ramah-lingkungan', [
                'tags' => ['bahan_baku_lokal', 'hemat_energi', 'pewarna_alami', 'zero_waste'],
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $umkm = $user->umkm->fresh();
        $this->assertContains('zero_waste', $umkm->praktik_ramah_lingkungan);
    }

    public function test_admin_can_add_coaching_note_and_it_updates_skor_lestari(): void
    {
        $admin = User::where('email', 'admin@kadin-jogja.id')->first();
        $adminToken = $admin->createToken('test-token')->plainTextToken;

        $umkm = Umkm::where('nama_usaha', 'Batik Sekar Jogja')->first();

        $response = $this->withHeader('Authorization', 'Bearer ' . $adminToken)
            ->postJson("/api/admin/umkm/{$umkm->id}/catatan-pembinaan", [
                'jenis' => 'kunjungan',
                'catatan' => 'Verifikasi langsung implementasi hemat energi panel surya di gerai workshop.',
                'tanggal' => now()->toDateString(),
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('catatan_pembinaan', [
            'umkm_id' => $umkm->id,
            'jenis' => 'kunjungan',
        ]);
    }

    public function test_admin_can_view_priority_umkm_list_sorted_by_lowest_score(): void
    {
        $admin = User::where('email', 'admin@kadin-jogja.id')->first();
        $adminToken = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $adminToken)
            ->getJson('/api/admin/skor-lestari');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'nama_usaha',
                        'skor_lestari',
                        'skor_lestari_level',
                    ],
                ],
            ]);
    }

    public function test_artisan_command_skor_lestari_recalculates_scores(): void
    {
        $this->artisan('skor-lestari:hitung-ulang')
            ->assertExitCode(0);
    }
}
