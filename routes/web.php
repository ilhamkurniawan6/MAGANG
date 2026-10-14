<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (KAADIN Unified Frontend & Backend)
|--------------------------------------------------------------------------
|
| Rute web menyajikan portal publik, dashboard UMKM, Buyer, dan Admin
| yang kini telah disatukan ke dalam direktori public/.
|
*/

// Halaman Beranda (Landing Page)
Route::get('/', function () {
    return response()->file(public_path('index.html'));
});

// Pemetaan Clean URL ke file HTML statis
$cleanRoutes = [
    // Publik
    'katalog' => 'katalog.html',
    'login' => 'login.html',
    'register-umkm' => 'register-umkm.html',
    'program' => 'program.html',
    'produk-detail' => 'produk-detail.html',
    'umkm-detail' => 'umkm-detail.html',

    // Admin
    'admin' => 'admin/dashboard.html',
    'admin/dashboard' => 'admin/dashboard.html',
    'admin/moderasi-produk' => 'admin/moderasi-produk.html',
    'admin/peluang-bisnis' => 'admin/peluang-bisnis.html',
    'admin/program' => 'admin/program.html',
    'admin/skor-lestari' => 'admin/skor-lestari.html',
    'admin/verifikasi-umkm' => 'admin/verifikasi-umkm.html',
    'admin/wa-bot-simulator' => 'admin/wa-bot-simulator.html',

    // Buyer
    'buyer' => 'buyer/dashboard.html',
    'buyer/dashboard' => 'buyer/dashboard.html',
    'buyer/peluang-bisnis' => 'buyer/peluang-bisnis.html',

    // UMKM
    'umkm' => 'umkm/dashboard.html',
    'umkm/dashboard' => 'umkm/dashboard.html',
    'umkm/produk' => 'umkm/produk.html',
    'umkm/profil' => 'umkm/profil.html',
    'umkm/program' => 'umkm/program.html',
    'umkm/skor-lestari' => 'umkm/skor-lestari.html',
    'umkm/peluang-bisnis' => 'umkm/peluang-bisnis.html',
];

foreach ($cleanRoutes as $uri => $file) {
    Route::get($uri, function () use ($file) {
        $path = public_path($file);
        if (file_exists($path)) {
            return response()->file($path);
        }
        abort(404);
    });
}
