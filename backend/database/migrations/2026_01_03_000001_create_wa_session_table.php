<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wa_session', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_wa')->index();
            $table->foreignId('umkm_id')->nullable()->constrained('umkm')->onDelete('cascade');
            $table->enum('step', ['idle', 'menunggu_detail', 'menunggu_konfirmasi'])->default('idle');
            $table->string('foto_path')->nullable();
            $table->json('data_ekstraksi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wa_session');
    }
};
