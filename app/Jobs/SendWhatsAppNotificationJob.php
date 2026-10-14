<?php

namespace App\Jobs;

use App\Services\WablasService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $nomor,
        public string $pesan
    ) {}

    public function handle(WablasService $wablasService): void
    {
        try {
            $wablasService->kirimPesan($this->nomor, $this->pesan);
        } catch (\Throwable $e) {
            Log::error('SendWhatsAppNotificationJob failed: ' . $e->getMessage());
        }
    }
}
