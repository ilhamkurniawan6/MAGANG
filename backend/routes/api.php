<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Publik\KatalogController as PublikKatalogController;
use App\Http\Controllers\Api\Publik\ProgramController as PublikProgramController;
use App\Http\Controllers\Api\Umkm\ProfilController as UmkmProfilController;
use App\Http\Controllers\Api\Umkm\ProdukController as UmkmProdukController;
use App\Http\Controllers\Api\Umkm\SkorLestariController as UmkmSkorLestariController;
use App\Http\Controllers\Api\Umkm\ProgramController as UmkmProgramController;
use App\Http\Controllers\Api\Umkm\PeluangBisnisController as UmkmPeluangBisnisController;
use App\Http\Controllers\Api\Admin\UmkmController as AdminUmkmController;
use App\Http\Controllers\Api\Admin\ProdukController as AdminProdukController;
use App\Http\Controllers\Api\Admin\SkorLestariController as AdminSkorLestariController;
use App\Http\Controllers\Api\Admin\ProgramController as AdminProgramController;
use App\Http\Controllers\Api\Admin\PeluangBisnisController as AdminPeluangBisnisController;
use App\Http\Controllers\Api\Buyer\PeluangBisnisController as BuyerPeluangBisnisController;
use App\Http\Controllers\Api\WablasWebhookController;

/*
|--------------------------------------------------------------------------
| 1. Endpoint Autentikasi & Webhook Publik
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register-umkm', [AuthController::class, 'registerUmkm']);

// Fase 3: WhatsApp Bot Webhook (Mendukung WAHA, n8n, Wablas & Simulator)
Route::post('/wablas/webhook', [WablasWebhookController::class, 'handle']);
Route::post('/waha/webhook', [WablasWebhookController::class, 'handle']);
Route::post('/n8n/webhook', [WablasWebhookController::class, 'handle']);
Route::post('/whatsapp/webhook', [WablasWebhookController::class, 'handle']);

/*
|--------------------------------------------------------------------------
| 2. Endpoint Publik (Tanpa Autentikasi)
|--------------------------------------------------------------------------
*/
Route::prefix('publik')->group(function () {
    Route::get('/kategori-umkm', [PublikKatalogController::class, 'kategoriUmkm']);
    Route::get('/kategori-produk', [PublikKatalogController::class, 'kategoriProduk']);
    Route::get('/katalog', [PublikKatalogController::class, 'katalog']);
    Route::get('/produk/{id}', [PublikKatalogController::class, 'detailProduk']);
    Route::get('/umkm/{id}', [PublikKatalogController::class, 'detailUmkm']);
    Route::get('/program', [PublikProgramController::class, 'index']);
});

/*
|--------------------------------------------------------------------------
| 3. Endpoint Terproteksi Sanctum (Wajib Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    /*
    |--------------------------------------------------------------------------
    | Modul UMKM Hub (Role: umkm)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:umkm')->prefix('umkm')->group(function () {
        Route::get('/dashboard', [UmkmProfilController::class, 'dashboard']);
        Route::get('/profil', [UmkmProfilController::class, 'show']);
        Route::post('/profil', [UmkmProfilController::class, 'update']);
        Route::put('/profil', [UmkmProfilController::class, 'update']);

        Route::get('/produk', [UmkmProdukController::class, 'index']);
        Route::post('/produk', [UmkmProdukController::class, 'store']);
        Route::get('/produk/{id}', [UmkmProdukController::class, 'show']);
        Route::post('/produk/{id}', [UmkmProdukController::class, 'update']);
        Route::put('/produk/{id}', [UmkmProdukController::class, 'update']);
        Route::delete('/produk/{id}', [UmkmProdukController::class, 'destroy']);

        // Fase 2 & 3: Skor Lestari, Program & Rekomendasi AI, Peluang Bisnis
        Route::get('/skor-lestari', [UmkmSkorLestariController::class, 'index']);
        Route::post('/praktik-ramah-lingkungan', [UmkmSkorLestariController::class, 'updatePraktik']);
        Route::get('/program', [UmkmProgramController::class, 'index']);
        Route::get('/rekomendasi-program', [UmkmProgramController::class, 'rekomendasi']);
        Route::post('/program/{id}/daftar', [UmkmProgramController::class, 'daftar']);
        Route::get('/peluang-bisnis', [UmkmPeluangBisnisController::class, 'index']);
        Route::post('/peluang-bisnis/{id}/minat', [UmkmPeluangBisnisController::class, 'nyatakanMinat']);
    });

    /*
    |--------------------------------------------------------------------------
    | Modul Admin Panel (Role: admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminUmkmController::class, 'dashboard']);
        Route::get('/umkm', [AdminUmkmController::class, 'index']);
        Route::get('/umkm/{id}', [AdminUmkmController::class, 'show']);
        Route::post('/umkm/{id}/verifikasi', [AdminUmkmController::class, 'verifikasi']);

        Route::get('/produk', [AdminProdukController::class, 'index']);
        Route::get('/produk/{id}', [AdminProdukController::class, 'show']);
        Route::post('/produk/{id}/moderasi', [AdminProdukController::class, 'moderasi']);

        // Fase 2: Skor Lestari & Pembinaan, Program, Peluang Bisnis
        Route::get('/skor-lestari', [AdminSkorLestariController::class, 'index']);
        Route::post('/umkm/{id}/catatan-pembinaan', [AdminSkorLestariController::class, 'storeCatatan']);
        Route::get('/umkm/{id}/catatan-pembinaan', [AdminSkorLestariController::class, 'getCatatan']);
        Route::post('/umkm/{id}/verifikasi-praktik', [AdminSkorLestariController::class, 'verifikasiPraktik']);

        Route::get('/program', [AdminProgramController::class, 'index']);
        Route::post('/program', [AdminProgramController::class, 'store']);
        Route::put('/program/{id}', [AdminProgramController::class, 'update']);
        Route::delete('/program/{id}', [AdminProgramController::class, 'destroy']);
        Route::get('/program/{id}/peserta', [AdminProgramController::class, 'peserta']);
        Route::post('/program/{id}/peserta/{pesertaId}/kehadiran', [AdminProgramController::class, 'updateKehadiran']);

        Route::get('/peluang-bisnis', [AdminPeluangBisnisController::class, 'index']);
        Route::post('/peluang-bisnis/{id}/status', [AdminPeluangBisnisController::class, 'updateStatus']);
        Route::get('/peluang-bisnis/{id}/rekomendasi-umkm', [BuyerPeluangBisnisController::class, 'rekomendasiUmkm']);
    });

    /*
    |--------------------------------------------------------------------------
    | Modul Buyer (Role: buyer)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:buyer')->prefix('buyer')->group(function () {
        Route::get('/dashboard', [BuyerPeluangBisnisController::class, 'dashboard']);
        Route::get('/peluang-bisnis', [BuyerPeluangBisnisController::class, 'index']);
        Route::post('/peluang-bisnis', [BuyerPeluangBisnisController::class, 'store']);
        Route::get('/peluang-bisnis/{id}/minat', [BuyerPeluangBisnisController::class, 'peminat']);
        Route::post('/peluang-bisnis/{id}/status', [BuyerPeluangBisnisController::class, 'updateStatus']);
        Route::get('/peluang-bisnis/{id}/rekomendasi-umkm', [BuyerPeluangBisnisController::class, 'rekomendasiUmkm']);
    });
});

