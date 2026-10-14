<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    protected ?string $apiKey;
    protected string $baseUrl;
    protected string $defaultModel;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
        $this->baseUrl = config('services.openrouter.base_url', 'https://openrouter.ai/api/v1');
        $this->defaultModel = config('services.openrouter.model', 'google/gemini-2.0-flash-lite-preview-02-05:free');
    }

    /**
     * Kirim chat completion request ke OpenRouter
     */
    public function chatCompletion(array $messages, ?string $model = null, float $temperature = 0.2): array
    {
        $chosenModel = $model ?: $this->defaultModel;

        if (empty($this->apiKey)) {
            Log::warning('OpenRouter API key is not configured.');
            return [
                'success' => false,
                'error' => 'API_KEY_MISSING',
                'content' => null,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'HTTP-Referer' => 'https://kadin-diy.or.id',
                'X-Title' => 'KADIN Digital Hub',
                'Content-Type' => 'application/json',
            ])->timeout(20)->post($this->baseUrl . '/chat/completions', [
                'model' => $chosenModel,
                'messages' => $messages,
                'temperature' => $temperature,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';

                return [
                    'success' => true,
                    'content' => $content,
                    'raw' => $data,
                ];
            }

            $statusCode = $response->status();
            $errorBody = $response->body();
            Log::error("OpenRouter API error [{$statusCode}]: {$errorBody}");

            return [
                'success' => false,
                'status_code' => $statusCode,
                'error' => $statusCode === 429 ? 'RATE_LIMIT_EXCEEDED' : 'API_ERROR',
                'message' => $errorBody,
                'content' => null,
            ];
        } catch (\Throwable $e) {
            Log::error('OpenRouter request exception: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => 'EXCEPTION',
                'message' => $e->getMessage(),
                'content' => null,
            ];
        }
    }

    /**
     * Ekstraksi nama produk dan harga dari teks atau URL audio
     */
    public function ekstrakDataProduk(?string $teks, ?string $urlAudio = null): array
    {
        $systemPrompt = 'Ekstrak nama produk dan harga dalam Rupiah dari pesan berikut. Balas HANYA dalam format JSON: {"nama_produk": string, "harga": number}. Jika informasi tidak lengkap, isi null pada field yang tidak ditemukan.';

        // Format pesan untuk AI
        $userMessageContent = [];
        if (!empty($teks)) {
            $userMessageContent[] = [
                'type' => 'text',
                'text' => $teks,
            ];
        }

        if (!empty($urlAudio)) {
            $userMessageContent[] = [
                'type' => 'input_audio',
                'input_audio' => [
                    'data' => $urlAudio,
                    'format' => 'mp3',
                ],
            ];
        }

        // Jika hanya teks biasa, kirim teks langsung
        $userPayload = (count($userMessageContent) === 1 && $userMessageContent[0]['type'] === 'text')
            ? $teks
            : $userMessageContent;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPayload],
        ];

        $response = $this->chatCompletion($messages);

        if ($response['success'] && !empty($response['content'])) {
            $parsed = $this->parseJsonClean($response['content']);
            if (is_array($parsed) && isset($parsed['nama_produk']) && isset($parsed['harga'])) {
                return [
                    'nama_produk' => $parsed['nama_produk'],
                    'harga' => is_numeric($parsed['harga']) ? (float) $parsed['harga'] : null,
                    'source' => 'ai',
                ];
            }
        }

        // Fallback Heuristik Lokal jika AI offline / rate-limit / missing key
        return $this->fallbackEkstraksiLokal($teks);
    }

    /**
     * Helper pembersih JSON dari respon AI (mengatasi markdown code blocks)
     */
    public function parseJsonClean(string $content): ?array
    {
        $cleaned = trim($content);
        // Hapus ```json dan ``` jika ada
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/i', $cleaned, $matches)) {
            $cleaned = trim($matches[1]);
        }

        $decoded = json_decode($cleaned, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // Coba cari substring JSON {...}
        if (preg_match('/\{[\s\S]*\}/', $cleaned, $matchJson)) {
            $decoded = json_decode($matchJson[0], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        // Coba cari substring array JSON [...]
        if (preg_match('/\[[\s\S]*\]/', $cleaned, $matchArr)) {
            $decoded = json_decode($matchArr[0], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Fallback ekstraksi lokal cerdas berbasis regex
     */
    protected function fallbackEkstraksiLokal(?string $teks): array
    {
        if (empty($teks)) {
            return ['nama_produk' => null, 'harga' => null, 'source' => 'fallback'];
        }

        $namaProduk = null;
        $harga = null;

        // Cari pola harga: e.g. "Rp 25.000", "Rp15000", "harga 25000", "25rb", "50k", "15000"
        if (preg_match('/(?:rp\.?|harga|sebesar)\s*([0-9\.\,]+)\s*(?:rb|k|ribu)?/i', $teks, $m) ||
            preg_match('/([0-9\.\,]+)\s*(?:rb|k|ribu)/i', $teks, $m) ||
            preg_match('/(?:^|\s)(\d{4,9})(?:\s|$)/', $teks, $m)) {
            
            $numStr = str_replace(['.', ','], '', $m[1]);
            $multiplier = 1;
            if (preg_match('/(?:rb|k|ribu)/i', $m[0])) {
                $multiplier = 1000;
            }
            $harga = (float) $numStr * $multiplier;
            
            // Hapus bagian harga untuk mendapatkan nama produk
            $sisaTeks = trim(str_ireplace($m[0], '', $teks));
            $sisaTeks = preg_replace('/^(?:nama\s*produk\s*:\s*|jual\s+|produk\s+)/i', '', $sisaTeks);
            $sisaTeks = trim($sisaTeks, " \t\n\r\0\x0B,.-:");
            if (!empty($sisaTeks)) {
                $namaProduk = ucwords($sisaTeks);
            }
        }

        return [
            'nama_produk' => $namaProduk,
            'harga' => $harga,
            'source' => 'fallback',
        ];
    }
}
