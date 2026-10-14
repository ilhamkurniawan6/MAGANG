<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Program;
use App\Models\ProgramPeserta;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProgramTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_public_can_view_published_programs(): void
    {
        $response = $this->getJson('/api/publik/program');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'judul', 'jenis', 'status', 'peserta_count'],
                ],
            ]);
    }

    public function test_umkm_can_register_for_published_program(): void
    {
        $user = User::where('email', 'umkm.batik@example.com')->first();
        Sanctum::actingAs($user);

        $program = Program::where('status', 'published')->first();

        $response = $this->postJson("/api/umkm/program/{$program->id}/daftar");

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'program_id' => $program->id,
                    'umkm_id' => $user->umkm->id,
                    'status' => 'terdaftar',
                ],
            ]);

        $this->assertDatabaseHas('program_peserta', [
            'program_id' => $program->id,
            'umkm_id' => $user->umkm->id,
        ]);
    }

    public function test_umkm_cannot_register_twice_for_same_program(): void
    {
        $user = User::where('email', 'umkm.batik@example.com')->first();
        Sanctum::actingAs($user);

        $program = Program::where('status', 'published')->first();

        // Pendaftaran 1
        $this->postJson("/api/umkm/program/{$program->id}/daftar");

        // Pendaftaran 2
        $response = $this->postJson("/api/umkm/program/{$program->id}/daftar");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['program']);
    }

    public function test_admin_can_crud_program_and_update_attendance(): void
    {
        $admin = User::where('email', 'admin@kadin-jogja.id')->first();
        Sanctum::actingAs($admin);

        // 1. Create Program
        $createRes = $this->postJson('/api/admin/program', [
            'judul' => 'Pelatihan Transformasi Digital UMKM Jogja 2026',
            'deskripsi' => 'Pendampingan onboarding marketplace dan digital marketing.',
            'jenis' => 'pelatihan',
            'tanggal_mulai' => now()->addDays(5)->toDateString(),
            'tanggal_selesai' => now()->addDays(7)->toDateString(),
            'status' => 'published',
        ]);

        $createRes->assertStatus(201);
        $programId = $createRes->json('data.id');

        // 2. UMKM daftar
        $umkmUser = User::where('email', 'umkm.kopi@example.com')->first();
        Sanctum::actingAs($umkmUser);

        $daftarRes = $this->postJson("/api/umkm/program/{$programId}/daftar");
        $daftarRes->assertStatus(201);
        $pesertaId = $daftarRes->json('data.id');

        // 3. Admin update kehadiran
        Sanctum::actingAs($admin);
        $updateRes = $this->postJson("/api/admin/program/{$programId}/peserta/{$pesertaId}/kehadiran", [
            'status' => 'hadir',
        ]);

        $updateRes->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $pesertaId,
                    'status' => 'hadir',
                ],
            ]);
    }
}
