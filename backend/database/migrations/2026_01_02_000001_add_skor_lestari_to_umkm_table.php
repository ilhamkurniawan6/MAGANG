<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('umkm', function (Blueprint $table) {
            $table->json('praktik_ramah_lingkungan')->nullable()->after('status_verifikasi');
            $table->boolean('praktik_terverifikasi')->default(false)->after('praktik_ramah_lingkungan');
            $table->integer('skor_lestari')->nullable()->after('praktik_terverifikasi');
            $table->enum('skor_lestari_level', ['bronze', 'silver', 'gold'])->nullable()->after('skor_lestari');
            $table->timestamp('skor_lestari_updated_at')->nullable()->after('skor_lestari_level');
        });
    }

    public function down(): void
    {
        Schema::table('umkm', function (Blueprint $table) {
            $table->dropColumn([
                'praktik_ramah_lingkungan',
                'praktik_terverifikasi',
                'skor_lestari',
                'skor_lestari_level',
                'skor_lestari_updated_at',
            ]);
        });
    }
};
