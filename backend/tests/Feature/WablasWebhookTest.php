<?php

namespace Tests\Feature;

use App\Models\KategoriProduk;
use App\Models\KategoriUmkm;
use App\Models\Produk;
use App\Models\Role;
use App\Models\Umkm;
use App\Models\User;
use App\Models\WaSession;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WablasWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected Umkm $umkm;
    protected string $nomorWa = '081234567890';
    protected string $cleanNomor = '6281234567890';

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['nama_role' => 'umkm', 'deskripsi' => 'UMKM']);
        Role::create(['nama_role' => 'admin', 'deskripsi' => 'Admin']);

        $user = User::create([
            'nama' => 'Owner UMKM Test',
            'email' => 'umkm@test.com',
            'password' => bcrypt('password'),
            'role_id' => 1,
        ]);

        $katUmkm = KategoriUmkm::create(['nama_kategori' => 'Kuliner & Pangan']);
        KategoriProduk::create(['nama_kategori' => 'Makanan & Minuman']);

        $this->umkm = Umkm::create([
            'user_id' => $user->id,
            'kategori_id' => $katUmkm->id,
            'nama_usaha' => 'Kerupuk Berkah Jogja',
            'deskripsi' => 'Produsen kerupuk udang renyah khas Bantul',
            'alamat' => 'Bantul, DIY',
            'kontak_wa' => $this->nomorWa,
            'status_verifikasi' => 'verified',
        ]);
    }

    public function test_nomor_belum_terdaftar_dapat_pesan_penolakan(): void
    {
        $response = $this->postJson('/api/wablas/webhook', [
            'phone' => '089999999999',
            'message' => 'Halo KADIN',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'unregistered',
                'reply' => 'Nomor Anda belum terdaftar sebagai UMKM Binaan KADIN.',
            ]);
    }

    public function test_alur_lengkap_fsm_wa_bot_tambah_produk_berhasil(): void
    {
        // STEP 1: Idle -> Kirim Foto Produk
        $resStep1 = $this->postJson('/api/wablas/webhook', [
            'phone' => $this->nomorWa,
            'url' => 'https://example.com/foto_produk.jpg',
            'type' => 'image',
        ]);

        $resStep1->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'session_step' => 'menunggu_detail',
            ]);

        $session = WaSession::where('nomor_wa', $this->cleanNomor)->first();
        $this->assertNotNull($session);
        $this->assertEquals('menunggu_detail', $session->step);

        // STEP 2: Menunggu Detail -> Kirim Teks Nama & Harga
        $resStep2 = $this->postJson('/api/wablas/webhook', [
            'phone' => $this->nomorWa,
            'message' => 'Kerupuk Udang Gurih Khas Bantul harga 15000',
        ]);

        $resStep2->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'session_step' => 'menunggu_konfirmasi',
            ]);

        $session->refresh();
        $this->assertEquals('menunggu_konfirmasi', $session->step);
        $this->assertNotNull($session->data_ekstraksi);
        $this->assertEquals(15000, $session->data_ekstraksi['harga']);

        // STEP 3: Menunggu Konfirmasi -> Kirim "OK"
        $resStep3 = $this->postJson('/api/wablas/webhook', [
            'phone' => $this->nomorWa,
            'message' => 'OK',
        ]);

        $resStep3->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'session_step' => 'idle',
            ]);

        // Verifikasi record produk baru tersimpan dengan status WAJIB menunggu_moderasi
        $this->assertDatabaseHas('produk', [
            'umkm_id' => $this->umkm->id,
            'harga' => 15000,
            'status' => 'menunggu_moderasi',
        ]);

        $session->refresh();
        $this->assertEquals('idle', $session->step);
    }

    public function test_perintah_batal_mereset_sesi(): void
    {
        // Set sesi ke menunggu_detail
        WaSession::create([
            'nomor_wa' => $this->cleanNomor,
            'umkm_id' => $this->umkm->id,
            'step' => 'menunggu_detail',
            'foto_path' => 'produk/foto.jpg',
        ]);

        $response = $this->postJson('/api/wablas/webhook', [
            'phone' => $this->nomorWa,
            'message' => 'BATAL',
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'cancelled']);

        $session = WaSession::where('nomor_wa', $this->cleanNomor)->first();
        $this->assertEquals('idle', $session->step);
    }
}
