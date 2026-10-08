<?php

namespace Tests\Feature;

use App\Jobs\SendWhatsAppNotificationJob;
use App\Models\KategoriProduk;
use App\Models\KategoriUmkm;
use App\Models\Produk;
use App\Models\Role;
use App\Models\Umkm;
use App\Models\User;
use App\Services\AdminService;
use App\Services\WablasService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class WhatsAppNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected Umkm $umkm;
    protected Produk $produk;
    protected AdminService $adminService;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create(['nama_role' => 'umkm', 'deskripsi' => 'UMKM']);
        Role::create(['nama_role' => 'admin', 'deskripsi' => 'Admin']);

        $user = User::create([
            'nama' => 'Owner UMKM Notif',
            'email' => 'notif@umkm.com',
            'password' => bcrypt('password'),
            'role_id' => 1,
        ]);

        $katUmkm = KategoriUmkm::create(['nama_kategori' => 'Kriya & Kerajinan']);
        $katProduk = KategoriProduk::create(['nama_kategori' => 'Kriya & Dekorasi']);

        $this->umkm = Umkm::create([
            'user_id' => $user->id,
            'kategori_id' => $katUmkm->id,
            'nama_usaha' => 'Kerajinan Perak Jogja',
            'alamat' => 'Kotagede, Yogyakarta',
            'kontak_wa' => '081298765432',
            'status_verifikasi' => 'pending',
        ]);

        $this->produk = Produk::create([
            'umkm_id' => $this->umkm->id,
            'kategori_produk_id' => $katProduk->id,
            'nama_produk' => 'Cincin Perak Filigree',
            'harga' => 350000,
            'deskripsi' => 'Cincin perak murni buatan tangan pengrajin Kotagede',
            'status' => 'menunggu_moderasi',
        ]);

        $this->adminService = app(AdminService::class);
    }

    public function test_verifikasi_umkm_memicu_notifikasi_wa(): void
    {
        Queue::fake();

        $this->adminService->verifikasiUmkm($this->umkm->id, 'verified');

        $this->assertDatabaseHas('umkm', [
            'id' => $this->umkm->id,
            'status_verifikasi' => 'verified',
        ]);

        Queue::assertPushed(SendWhatsAppNotificationJob::class, function ($job) {
            return $job->nomor === $this->umkm->kontak_wa && str_contains($job->pesan, 'DIVERIFIKASI');
        });
    }

    public function test_moderasi_produk_memicu_notifikasi_wa(): void
    {
        Queue::fake();

        $this->adminService->moderasiProduk($this->produk->id, 'disetujui');

        $this->assertDatabaseHas('produk', [
            'id' => $this->produk->id,
            'status' => 'disetujui',
        ]);

        Queue::assertPushed(SendWhatsAppNotificationJob::class, function ($job) {
            return $job->nomor === $this->umkm->kontak_wa && str_contains($job->pesan, 'DISETUJUI');
        });
    }

    public function test_wablas_service_tetap_aman_walau_token_salah_atau_gateway_down(): void
    {
        // Sengaja gunakan token salah dan domain offline
        config([
            'services.wablas.token' => 'invalid_token_123',
            'services.wablas.domain' => 'https://invalid-nonexistent-domain.wablas.com',
        ]);

        $wablas = new WablasService();
        // Pastikan tidak throw unhandled exception
        $hasil = $wablas->kirimPesan('081234567890', 'Testing message');
        $this->assertIsBool($hasil);
    }
}
