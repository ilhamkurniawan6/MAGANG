<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\Umkm;
use App\Models\WaSession;
use App\Services\OpenRouterService;
use App\Services\StorageService;
use App\Services\WablasService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WablasWebhookController extends Controller
{
    public function __construct(
        protected OpenRouterService $openRouterService,
        protected WablasService $wablasService,
        protected StorageService $storageService
    ) {}

    /**
     * Handle incoming webhook dari WAHA / Wablas / Simulator
     */
    public function handle(Request $request): JsonResponse
    {
        Log::info('WhatsApp Webhook received payload:', $request->all());

        // 0. Abaikan pesan keluar (fromMe = true) dari WAHA agar bot tidak membalas pesannya sendiri
        $isFromMe = $request->input('payload.fromMe') === true 
            || $request->input('fromMe') === true 
            || $request->input('data.fromMe') === true;

        if ($isFromMe) {
            return response()->json([
                'status' => 'ignored',
                'message' => 'Ignoring message sent from bot itself (fromMe=true)',
            ]);
        }

        // 1. Ekstraksi Nomor Pengirim (Mendukung format WAHA, Wablas, Simulator)
        $rawPhone = $request->input('payload.from')
            ?? $request->input('payload.sender')
            ?? $request->input('payload.participant')
            ?? $request->input('phone')
            ?? $request->input('sender')
            ?? $request->input('from')
            ?? $request->input('wa_number')
            ?? '';

        $cleanPhone = $this->wablasService->normalisasiNomor($rawPhone);
        if (empty($cleanPhone)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nomor WhatsApp tidak ditemukan pada payload webhook.',
            ], 400);
        }

        // 2. Ekstraksi Pesan Teks & Media
        $pesanTeks = trim((string) (
            $request->input('payload.body')
            ?? $request->input('payload.caption')
            ?? $request->input('payload._data.caption')
            ?? $request->input('message')
            ?? $request->input('text')
            ?? $request->input('caption')
            ?? $request->input('body')
            ?? ''
        ));

        $mediaUrl = $request->input('payload.media.url')
            ?? $request->input('payload.mediaUrl')
            ?? $request->input('file')
            ?? $request->input('url')
            ?? $request->input('media')
            ?? $request->input('image')
            ?? null;

        $messageType = strtolower((string) (
            $request->input('payload.type') 
            ?? $request->input('type') 
            ?? $request->input('messageType') 
            ?? ''
        ));

        // Deteksi apakah pesan mengandung media gambar
        $hasMedia = $request->input('payload.hasMedia') === true
            || in_array($messageType, ['image', 'photo', 'picture'])
            || $request->hasFile('file')
            || !empty($mediaUrl);

        // 3. Verifikasi Apakah Nomor Terdaftar sebagai UMKM Binaan
        $altPhone08 = '08' . substr($cleanPhone, 2);
        $umkm = Umkm::where('kontak_wa', $cleanPhone)
            ->orWhere('kontak_wa', $altPhone08)
            ->orWhere('kontak_wa', 'like', "%" . substr($cleanPhone, 4) . "%")
            ->first();

        if (!$umkm) {
            $balasan = "Halo! Nomor Anda ({$cleanPhone}) belum terdaftar sebagai UMKM Binaan di KADIN Hub.\nSilakan daftarkan usaha Anda di portal web terlebih dahulu.";
            $this->wablasService->kirimPesan($cleanPhone, $balasan);

            return response()->json([
                'status' => 'unregistered',
                'reply' => $balasan,
            ], 200);
        }

        // 4. Kelola Sesi WhatsApp (FSM)
        $session = WaSession::firstOrCreate(
            ['nomor_wa' => $cleanPhone],
            [
                'umkm_id' => $umkm->id,
                'step' => 'idle',
            ]
        );

        // Update umkm_id jika belum ada
        if (!$session->umkm_id) {
            $session->umkm_id = $umkm->id;
            $session->save();
        }

        // 5. Cek Timeout Sesi (15 menit tanpa aktivitas -> reset ke idle)
        if ($session->step !== 'idle' && $session->updated_at->diffInMinutes(Carbon::now()) >= 15) {
            $session->update([
                'step' => 'idle',
                'foto_path' => null,
                'data_ekstraksi' => null,
            ]);
        }

        // 6. Cek Perintah "BATAL"
        if (strtolower($pesanTeks) === 'batal' && $session->step !== 'idle') {
            $session->update([
                'step' => 'idle',
                'foto_path' => null,
                'data_ekstraksi' => null,
            ]);

            $balasan = "Sesi penambahan produk telah dibatalkan.\nKirim foto produk baru kapan saja untuk memulai kembali.";
            $this->wablasService->kirimPesan($cleanPhone, $balasan);

            return response()->json([
                'status' => 'cancelled',
                'reply' => $balasan,
            ]);
        }

        // 7. Finite State Machine (FSM) Percakapan Bot
        $balasan = '';

        switch ($session->step) {
            case 'idle':
                if ($hasMedia) {
                    $fotoPath = $this->simpanMediaFoto($request, $mediaUrl);
                    $session->update([
                        'step' => 'menunggu_detail',
                        'foto_path' => $fotoPath,
                        'data_ekstraksi' => null,
                    ]);

                    $balasan = "📸 Foto produk berhasil diterima!\nSekarang silakan ceritakan *Nama Produk* dan *Harganya* ya (Contoh: 'Batik Parang Tulis, harga 250000').";
                } else {
                    $balasan = "Halo *{$umkm->nama_usaha}*! 🌿\nSelamat datang di WhatsApp Bot KADIN Hub.\n\nKirimkan *foto produk* terlebih dahulu untuk memulai penambahan produk baru ke katalog.";
                }
                break;

            case 'menunggu_detail':
                $audioUrl = in_array($messageType, ['audio', 'voice', 'ptt']) ? $mediaUrl : null;
                $teksAnalisis = !empty($pesanTeks) ? $pesanTeks : null;

                $ekstraksi = $this->openRouterService->ekstrakDataProduk($teksAnalisis, $audioUrl);

                if (!empty($ekstraksi['nama_produk']) && !empty($ekstraksi['harga']) && $ekstraksi['harga'] > 0) {
                    $session->update([
                        'step' => 'menunggu_konfirmasi',
                        'data_ekstraksi' => [
                            'nama_produk' => $ekstraksi['nama_produk'],
                            'harga' => (float) $ekstraksi['harga'],
                        ],
                    ]);

                    $hargaFormat = number_format($ekstraksi['harga'], 0, ',', '.');
                    $balasan = "📦 *{$ekstraksi['nama_produk']}*\n💰 Rp {$hargaFormat}\n\nApakah detail di atas sudah sesuai?\nBalas *OK* untuk menyimpan ke antrean moderasi, atau *ULANG* untuk memperbaiki.";
                } else {
                    $balasan = "Maaf, sistem belum dapat mengenali nama produk dan harga dengan jelas.\nSilakan sebutkan kembali, misalnya: 'Keripik Tempe Renyah, harga 15000'.";
                }
                break;

            case 'menunggu_konfirmasi':
                $cmd = strtolower($pesanTeks);

                if ($cmd === 'ok' || str_contains($cmd, 'ya') || str_contains($cmd, 'benar') || str_contains($cmd, 'simpan')) {
                    $dataEkstraksi = $session->data_ekstraksi ?? [];
                    $namaProduk = $dataEkstraksi['nama_produk'] ?? 'Produk Baru';
                    $harga = $dataEkstraksi['harga'] ?? 0;

                    // Tentukan kategori produk default
                    $kategoriProduk = KategoriProduk::first();
                    $kategoriId = $kategoriProduk ? $kategoriProduk->id : 1;

                    // Buat Record Produk Baru (Status: menunggu_moderasi)
                    Produk::create([
                        'umkm_id' => $umkm->id,
                        'kategori_produk_id' => $kategoriId,
                        'nama_produk' => $namaProduk,
                        'harga' => $harga,
                        'deskripsi' => 'Produk didaftarkan secara otomatis melalui WhatsApp Bot KADIN Hub.',
                        'foto_produk' => $session->foto_path,
                        'status' => 'menunggu_moderasi',
                    ]);

                    // Reset sesi ke idle
                    $session->update([
                        'step' => 'idle',
                        'foto_path' => null,
                        'data_ekstraksi' => null,
                    ]);

                    $balasan = "✅ Sukses! Produk *{$namaProduk}* telah tersimpan dan sedang menunggu persetujuan Admin KADIN.\nKirim foto produk lainnya kapan saja untuk menambah produk baru.";
                } elseif ($cmd === 'ulang' || str_contains($cmd, 'bukan') || str_contains($cmd, 'salah') || str_contains($cmd, 'edit')) {
                    $session->update([
                        'step' => 'menunggu_detail',
                        'data_ekstraksi' => null,
                    ]);

                    $balasan = "Baik, silakan ketik ulang nama produk beserta harganya.";
                } else {
                    $balasan = "Mohon balas *OK* untuk menyimpan produk ke katalog, atau *ULANG* untuk memperbaiki detailnya.";
                }
                break;
        }

        // 8. Kirim Balasan ke WhatsApp User
        $this->wablasService->kirimPesan($cleanPhone, $balasan);

        return response()->json([
            'status' => 'success',
            'session_step' => $session->fresh()->step,
            'reply' => $balasan,
        ]);
    }

    /**
     * Helper simpan media foto dari Multipart Upload atau URL Download
     */
    protected function simpanMediaFoto(Request $request, ?string $mediaUrl): string
    {
        if ($request->hasFile('file')) {
            /** @var UploadedFile $file */
            $file = $request->file('file');
            return $this->storageService->uploadFile($file, 'produk');
        }

        $disk = $this->storageService->getActiveDisk();
        $fileName = 'produk/' . Str::uuid() . '.jpg';

        if (!empty($mediaUrl) && filter_var($mediaUrl, FILTER_VALIDATE_URL)) {
            try {
                $response = Http::timeout(10)->get($mediaUrl);
                if ($response->successful()) {
                    Storage::disk($disk)->put($fileName, $response->body(), 'public');
                    return $fileName;
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal download foto media dari URL: ' . $e->getMessage());
            }
        }

        return $mediaUrl ?: 'produk/wa_bot_photo.jpg';
    }
}
