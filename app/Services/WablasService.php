<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WablasService
{
    protected string $driver;
    
    // WAHA config
    protected string $wahaBaseUrl;
    protected ?string $wahaApiKey;
    protected ?string $wahaSecret;
    protected string $wahaSession;

    // n8n config
    protected ?string $n8nWebhookUrl;
    protected ?string $n8nApiKey;

    // Wablas config
    protected ?string $token;
    protected ?string $secret;
    protected string $domain;

    public function __construct()
    {
        $this->driver = config('services.whatsapp.driver', env('WA_GATEWAY_DRIVER', 'waha'));

        // WAHA configuration
        $this->wahaBaseUrl = rtrim(config('services.waha.base_url', env('WAHA_BASE_URL', 'http://localhost:3000')), '/');
        $this->wahaApiKey = config('services.waha.api_key', env('WAHA_API_KEY', 'kadin_api_hub'));
        $this->wahaSecret = config('services.waha.secret', env('WAHA_SECRET', 'qwertyuiop'));
        $this->wahaSession = config('services.waha.session', env('WAHA_SESSION', 'default'));

        // n8n configuration
        $this->n8nWebhookUrl = config('services.n8n.webhook_url', env('N8N_WEBHOOK_URL'));
        $this->n8nApiKey = config('services.n8n.api_key', env('N8N_API_KEY'));

        // Wablas configuration
        $this->token = config('services.wablas.token', env('WABLAS_TOKEN'));
        $this->secret = config('services.wablas.secret', env('WABLAS_SECRET'));
        $this->domain = rtrim(config('services.wablas.domain', 'https://kudus.wablas.com'), '/');
    }

    /**
     * Kirim pesan WhatsApp universal (Mendukung WAHA, n8n, & Wablas API)
     */
    public function kirimPesan(string $nomor, string $pesan): bool
    {
        $cleanNumber = $this->normalisasiNomor($nomor);

        if (empty($cleanNumber)) {
            Log::warning("WhatsApp Gateway: Nomor telepon tidak valid ({$nomor}).");
            return false;
        }

        if ($this->driver === 'waha') {
            return $this->kirimViaWaha($cleanNumber, $pesan);
        }

        if ($this->driver === 'n8n') {
            return $this->kirimViaN8n($cleanNumber, $pesan);
        }

        return $this->kirimViaWablas($cleanNumber, $pesan);
    }

    /**
     * Kirim pesan WhatsApp melalui n8n Webhook
     */
    protected function kirimViaN8n(string $cleanNumber, string $pesan): bool
    {
        if (empty($this->n8nWebhookUrl)) {
            Log::info("n8n (Demo Mode): Webhook URL belum diset. Pesan ke {$cleanNumber}:\n{$pesan}");
            return true;
        }

        try {
            $client = Http::timeout(10)->acceptJson();
            if (!empty($this->n8nApiKey)) {
                $client = $client->withHeaders(['X-N8N-API-KEY' => $this->n8nApiKey]);
            }

            $response = $client->post($this->n8nWebhookUrl, [
                'action' => 'send_message',
                'phone' => $cleanNumber,
                'chatId' => $cleanNumber . '@c.us',
                'message' => $pesan,
                'session' => $this->wahaSession,
                'timestamp' => time(),
            ]);

            if ($response->successful()) {
                Log::info("n8n: Berhasil dispatch webhook pesan ke {$cleanNumber}");
                return true;
            }

            Log::warning("n8n Webhook failure [{$response->status()}]: {$response->body()}");
            return false;
        } catch (\Throwable $e) {
            Log::error("n8n exception saat dispatch webhook: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim pesan WhatsApp melalui WAHA (WhatsApp HTTP API)
     */
    protected function kirimViaWaha(string $cleanNumber, string $pesan): bool
    {
        $chatId = $cleanNumber . '@c.us';
        $url = $this->wahaBaseUrl . '/api/sendText';

        try {
            $client = Http::timeout(10)->acceptJson();

            // Set Header X-Api-Key
            if (!empty($this->wahaApiKey)) {
                $client = $client->withHeaders([
                    'X-Api-Key' => $this->wahaApiKey,
                ]);
            }

            // Set Basic Auth jika WAHA dikonfigurasi dengan username & password
            if (!empty($this->wahaApiKey) && !empty($this->wahaSecret)) {
                $client = $client->withBasicAuth($this->wahaApiKey, $this->wahaSecret);
            }

            $response = $client->post($url, [
                'session' => $this->wahaSession,
                'chatId' => $chatId,
                'text' => $pesan,
            ]);

            if ($response->successful()) {
                Log::info("WAHA: Berhasil mengirim pesan WA ke {$chatId}");
                return true;
            }

            Log::warning("WAHA API failure [{$response->status()}]: {$response->body()} untuk nomor {$chatId}");
            return false;
        } catch (\Throwable $e) {
            Log::error("WAHA exception saat kirim ke {$chatId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim pesan WhatsApp melalui Wablas API
     */
    protected function kirimViaWablas(string $cleanNumber, string $pesan): bool
    {
        if (empty($this->token)) {
            Log::info("Wablas (Demo Mode): Notifikasi ke {$cleanNumber} disimulasikan (Token belum diset):\n{$pesan}");
            return true;
        }

        try {
            $authHeader = $this->token;
            if (!empty($this->secret) && !str_contains($this->token, '.')) {
                $authHeader = $this->token . '.' . $this->secret;
            }

            $url = $this->domain . '/api/send-message';
            $response = Http::withHeaders([
                'Authorization' => $authHeader,
                'Accept' => 'application/json',
            ])->timeout(10)->post($url, [
                'phone' => $cleanNumber,
                'message' => $pesan,
            ]);

            if ($response->successful()) {
                Log::info("Wablas: Berhasil mengirim notifikasi WA ke {$cleanNumber}");
                return true;
            }

            Log::warning("Wablas API failure [{$response->status()}]: {$response->body()} untuk nomor {$cleanNumber}");
            return false;
        } catch (\Throwable $e) {
            Log::error("Wablas exception saat kirim ke {$cleanNumber}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Normalisasi format nomor WhatsApp ke format 628xxx murni (tanpa @c.us, +, spasi, dsb)
     */
    public function normalisasiNomor(string $nomor): string
    {
        $cleaned = explode('@', $nomor)[0];
        $digits = preg_replace('/[^0-9]/', '', $cleaned);

        if (str_starts_with($digits, '08')) {
            return '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62' . $digits;
        }

        return $digits;
    }
}
