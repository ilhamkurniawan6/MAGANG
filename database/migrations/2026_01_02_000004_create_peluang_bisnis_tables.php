<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peluang_bisnis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('kategori_produk_id')->constrained('kategori_produk')->onDelete('restrict');
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('kapasitas')->nullable(); // e.g. "Minimal 50 pcs/minggu"
            $table->string('target_volume')->nullable(); // e.g. "Total 500 pcs"
            $table->enum('status', ['terbuka', 'ditutup'])->default('terbuka');
            $table->timestamps();
        });

        Schema::create('peluang_bisnis_minat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peluang_bisnis_id')->constrained('peluang_bisnis')->onDelete('cascade');
            $table->foreignId('umkm_id')->constrained('umkm')->onDelete('cascade');
            $table->text('catatan')->nullable(); // Catatan kesiapan / penawaran dari UMKM
            $table->timestamps();

            $table->unique(['peluang_bisnis_id', 'umkm_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peluang_bisnis_minat');
        Schema::dropIfExists('peluang_bisnis');
    }
};
