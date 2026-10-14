<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('umkm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->foreignId('kategori_id')->constrained('kategori_umkm')->onDelete('restrict');
            $table->string('nama_usaha');
            $table->text('deskripsi')->nullable();
            $table->text('alamat');
            $table->string('kontak_wa');
            $table->string('email_usaha')->nullable();
            $table->string('logo')->nullable();
            $table->enum('status_verifikasi', ['pending', 'verified', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umkm');
    }
};
