<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->string('judul');
            $table->text('deskripsi');
            $table->enum('jenis', ['pelatihan', 'seminar', 'pendampingan']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status', ['draft', 'published', 'selesai'])->default('draft');
            $table->timestamps();
        });

        Schema::create('program_peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('program')->onDelete('cascade');
            $table->foreignId('umkm_id')->constrained('umkm')->onDelete('cascade');
            $table->enum('status', ['terdaftar', 'hadir', 'tidak_hadir'])->default('terdaftar');
            $table->timestamps();

            $table->unique(['program_id', 'umkm_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_peserta');
        Schema::dropIfExists('program');
    }
};
