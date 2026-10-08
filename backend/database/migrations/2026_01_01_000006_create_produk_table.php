<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umkm_id')->constrained('umkm')->onDelete('cascade');
            $table->foreignId('kategori_produk_id')->constrained('kategori_produk')->onDelete('restrict');
            $table->string('nama_produk');
            $table->decimal('harga', 12, 2);
            $table->text('deskripsi');
            $table->string('foto_produk')->nullable();
            $table->enum('status', ['menunggu_moderasi', 'disetujui', 'ditolak'])->default('menunggu_moderasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
