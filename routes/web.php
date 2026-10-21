<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Setiap halaman berada di resources/views/*.blade.php.
| Setiap halaman bisa diakses lewat URL bersih (/katalog) maupun URL lama
| berakhiran .html (/katalog.html), karena link di HTML & JS masih memakai .html.
|
*/

Route::get('/', fn () => view('index'));

// uri => nama view
$halaman = [
    // Publik
    'index' => 'index',
    'katalog' => 'katalog',
    'login' => 'login',
    'register-umkm' => 'register-umkm',
    'program' => 'program',
    'produk-detail' => 'produk-detail',
    'umkm-detail' => 'umkm-detail',

    // Admin
    'admin/dashboard' => 'admin.dashboard',
    'admin/moderasi-produk' => 'admin.moderasi-produk',
    'admin/peluang-bisnis' => 'admin.peluang-bisnis',
    'admin/program' => 'admin.program',
    'admin/skor-lestari' => 'admin.skor-lestari',
    'admin/verifikasi-umkm' => 'admin.verifikasi-umkm',
    'admin/wa-bot-simulator' => 'admin.wa-bot-simulator',

    // Buyer
    'buyer/dashboard' => 'buyer.dashboard',
    'buyer/peluang-bisnis' => 'buyer.peluang-bisnis',

    // UMKM
    'umkm/dashboard' => 'umkm.dashboard',
    'umkm/produk' => 'umkm.produk',
    'umkm/profil' => 'umkm.profil',
    'umkm/program' => 'umkm.program',
    'umkm/skor-lestari' => 'umkm.skor-lestari',
    'umkm/peluang-bisnis' => 'umkm.peluang-bisnis',
];

foreach ($halaman as $uri => $view) {
    Route::view($uri, $view);
    Route::view($uri.'.html', $view);
}

// Pintasan ke dashboard tiap peran
Route::redirect('/admin', '/admin/dashboard.html');
Route::redirect('/buyer', '/buyer/dashboard.html');
Route::redirect('/umkm', '/umkm/dashboard.html');
