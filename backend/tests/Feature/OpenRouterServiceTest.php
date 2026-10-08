<?php

namespace Tests\Feature;

use App\Services\OpenRouterService;
use Tests\TestCase;

class OpenRouterServiceTest extends TestCase
{
    protected OpenRouterService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OpenRouterService();
    }

    public function test_ekstraksi_berbagai_variasi_kalimat_produk(): void
    {
        // Variasi 1: "Keripik tempe 20rb"
        $hasil1 = $this->service->ekstrakDataProduk('Keripik tempe renyah harga 20rb');
        $this->assertNotNull($hasil1['nama_produk']);
        $this->assertEquals(20000, $hasil1['harga']);

        // Variasi 2: "Batik Tulis Jogja Rp 250.000"
        $hasil2 = $this->service->ekstrakDataProduk('Batik Tulis Jogja Rp 250.000');
        $this->assertNotNull($hasil2['nama_produk']);
        $this->assertEquals(250000, $hasil2['harga']);

        // Variasi 3: "Kopi robusta Merapi seharga 35000"
        $hasil3 = $this->service->ekstrakDataProduk('Kopi robusta Merapi seharga 35000');
        $this->assertNotNull($hasil3['nama_produk']);
        $this->assertEquals(35000, $hasil3['harga']);
    }

    public function test_json_cleaner_dapat_membersihkan_markdown_code_block(): void
    {
        $rawMarkdown = "Berikut hasilnya:\n```json\n{\"nama_produk\": \"Bakpia Kukus\", \"harga\": 35000}\n```";
        $parsed = $this->service->parseJsonClean($rawMarkdown);

        $this->assertIsArray($parsed);
        $this->assertEquals('Bakpia Kukus', $parsed['nama_produk']);
        $this->assertEquals(35000, $parsed['harga']);
    }

    public function test_resilience_saat_api_key_kosong_menggunakan_fallback(): void
    {
        config(['services.openrouter.api_key' => null]);
        $serviceNoKey = new OpenRouterService();

        $hasil = $serviceNoKey->ekstrakDataProduk('Sambal Bawang Botol Rp 18.000');
        $this->assertNotNull($hasil['nama_produk']);
        $this->assertEquals(18000, $hasil['harga']);
    }
}
