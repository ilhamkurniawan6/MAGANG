<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SkorLestariService;

class HitungUlangSkorLestari extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skor-lestari:hitung-ulang';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghitung ulang Skor KADIN Lestari untuk seluruh UMKM terverifikasi';

    /**
     * Execute the console command.
     */
    public function handle(SkorLestariService $service): int
    {
        $this->info('Memulai perhitungan ulang Skor KADIN Lestari...');

        $count = $service->hitungUlangSemua();

        $this->info("Berhasil memperbarui Skor KADIN Lestari untuk {$count} UMKM terverifikasi.");

        return Command::SUCCESS;
    }
}
