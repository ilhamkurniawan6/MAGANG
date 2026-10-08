# 🏛️ KADIN UMKM PLATFORM (KAADIN) v0.1

> **Platform Digital Terpadu Pemberdayaan UMKM, Ekosistem Keberlanjutan (Skor Lestari / ESG), Matchmaking B2B Buyer, dan Otomasi WhatsApp Bot Berbasis AI.**

---

## 📌 1. Ringkasan Eksekutif (Executive Summary)

**KAADIN v0.1** adalah platform ekosistem bisnis digital yang dirancang untuk Kamar Dagang dan Industri (KADIN) guna mendigitalisasi, menginkubasi, memvalidasi, dan menghubungkan pelaku Usaha Mikro, Kecil, dan Menengah (UMKM) dengan pasar institusional (Buyer/Mitra Korporat).

Platform ini mengintegrasikan 4 pilar inovasi:
1. **Katalog Digital & Profil Terverifikasi UMKM**: Showcase produk lokal terkurasi lengkap dengan legalitas dan verifikasi administratif.
2. **Skor Lestari (ESG Rating & Pembinaan Ramah Lingkungan)**: Sistem penilaian dan bimbingan kepatuhan ramah lingkungan (pengelolaan limbah, efisiensi energi, kemasan organik, dsb).
3. **B2B Procurement & AI Smart Matchmaking**: Pasar peluang pengadaan korporat di mana algoritma AI (OpenRouter) secara cerdas mencocokkan kriteria kebutuhan Buyer dengan kapasitas UMKM yang relevan.
4. **WhatsApp Bot & Notification Gateway (Wablas)**: Interaksi conversational bot WhatsApp untuk layanan mandiri UMKM dan pengiriman notifikasi real-time.

---

## 🏗️ 2. Arsitektur Sistem & Tech Stack

Sistem dibangun menggunakan **Decoupled Architecture** (Frontend & Backend terpisah) yang ringan, cepat, dan mudah di-maintain.

```
+-------------------------------------------------------------------------+
|                              FRONTEND                                   |
|   - Vanilla JavaScript Modern (Fetch API, Token Auth, Reusable UI Hub)  |
|   - Vanilla CSS Modern (Design Tokens, Glassmorphism, Responsive UI)    |
|   - Multi-Portal: Publik, UMKM Hub, Buyer Portal, Admin KADIN Panel     |
+------------------------------------+------------------------------------+
                                     |  REST API (JSON) + Bearer Token
                                     v
+------------------------------------+------------------------------------+
|                               BACKEND                                   |
|   - Framework : Laravel (PHP 8.2+)                                      |
|   - Auth      : Laravel Sanctum (Token-Based Auth + Role Middleware)    |
|   - Database  : SQLite / MySQL (Eloquent ORM, Migrations, Seeders)      |
|   - AI Engine : OpenRouter API (LLM Smart Recommendation & Matching)    |
|   - WA Gateway: Wablas API (Conversational Bot & Push Notifications)    |
+-------------------------------------------------------------------------+
```

### 💻 Rincian Teknologi:
- **Backend**: Laravel 11/12 (PHP), Laravel Sanctum, Eloquent ORM.
- **Frontend**: HTML5 Semantik, Vanilla CSS3 (Custom Design System, Flexbox/Grid, Glassmorphism), Modern ES6+ JavaScript.
- **Database**: SQLite (Development) / MySQL / PostgreSQL (Production).
- **External Services**:
  - OpenRouter AI (LLM / Generative AI Recommender).
  - Wablas WhatsApp API (WhatsApp Gateway).

---

## 👥 3. Peran Pengguna & Modul Fitur

### 🌐 A. Portal Publik (Tanpa Login)
- **Katalog Terpadu**: Jelajahi etalase produk dan profil UMKM dengan pencarian dinamis dan filter kategori.
- **Detail Produk & UMKM**: Informasi lengkap spesifikasi produk, harga, legalitas NIB/PIRT, galeri, serta tombol kontak WhatsApp instan ke penjual.
- **Informasi Program KADIN**: Informasi program inkubasi, pelatihan, workshop, dan sertifikasi yang terbuka untuk umum.

---

### 🏪 B. Portal UMKM (`/umkm/`)
- **Dashboard UMKM**: Ringkasan performa toko, total produk terdaftar, status verifikasi akun, dan status Skor Lestari.
- **Manajemen Profil**: Lengkapi data legalitas (NIB, PIRT, Halal, Sertifikat Ekspor), media sosial, alamat, dan narasi bisnis.
- **Manajemen Produk (CRUD)**: Tambah, edit, hapus, dan upload foto produk beserta manajemen stok & kategori.
- **Asesmen Mandiri Skor Lestari**: Input dan pembaruan bukti penerapan praktik ramah lingkungan (Limbah, Energi, Bahan Baku, Kemasan).
- **Rekomendasi Program AI**: Mendapatkan rekomendasi program pembinaan personal dari AI sesuai kebutuhan profil & skor UMKM.
- **Katalog & Pendaftaran Program**: Mendaftar ke program-program KADIN dan memantau status keikutsertaan.
- **Peluang Bisnis B2B**: Melihat daftar kebutuhan pengadaan korporat/Buyer dan mengajukan surat minat kerjasama (*Apply Interest*).

---

### 🏢 C. Portal Buyer / Mitra Pengadaan (`/buyer/`)
- **Dashboard Pengadaan**: Ikhtisar posting pengadaan aktif, jumlah proposal minat UMKM, dan progress matching.
- **Manajemen Peluang Bisnis (RFQ / Procurement)**: Buat tender/kebutuhan pengadaan baru dengan spesifikasi produk, estimasi nilai, dan tenggat waktu.
- **AI Matchmaking & Rekomendasi UMKM**: Algoritma AI otomatis memindai dan merekomendasikan UMKM terverifikasi yang paling sesuai dengan spesifikasi tender.
- **Kurasi & Follow-up Peminat**: Tinjau daftar UMKM yang menyatakan minat dan langsung hubungi pemilik via WhatsApp.

---

### 🛡️ D. Admin KADIN Panel (`/admin/`)
- **Dashboard Ekosistem**: Statistik metrik UMKM terdaftar, produk aktif, total program pembinaan, transaksi peluang bisnis, dan persebaran Skor Lestari.
- **Verifikasi UMKM**: Validasi dokumen legalitas UMKM (Status: *Pending*, *Verified*, *Rejected*).
- **Moderasi Produk**: Tinjau produk baru dari UMKM sebelum tayang di katalog publik (*Published*, *Draft*, *Archived*).
- **Verifikasi Skor Lestari & Catatan Pembinaan**: Tinjau bukti praktik hijau UMKM, berikan checklist verifikasi, dan tulis catatan pendampingan dari mentor KADIN.
- **Manajemen Program Pembinaan**: Buat & kelola program, kelola kuota, verifikasi pendaftar, dan rekap kehadiran/absensi peserta.
- **Manajemen Peluang Bisnis**: Monitoring seluruh pengadaan yang diajukan oleh buyer dan update status tender.
- **Simulator & Pemantau WhatsApp Bot**: Simulator interaktif pengujian webhook pesan masuk WA, sesi navigasi bot, dan broadcast pesan.

---

## 🤖 4. Integrasi Khusus (AI & WhatsApp)

### 1. OpenRouter AI Smart Recommender (`OpenRouterService.php`)
- **Rekomendasi Program untuk UMKM**: AI menganalisis legalitas, kategori usaha, kendala, dan Skor Lestari UMKM untuk merekomendasikan program pelatihan KADIN yang paling berdampak.
- **Smart B2B Matchmaking**: AI mencocokkan deskripsi pengadaan Buyer dengan profil UMKM di database (kapasitas produksi, kepatuhan ramah lingkungan, kategori) dan memberikan skor kecocokan beserta analisis keunggulan.

### 2. Wablas WhatsApp Bot & Notification Gateway (`WablasService.php` & `WablasWebhookController.php`)
- **Notifikasi Otomatis**: Pengiriman notifikasi WA otomatis saat UMKM terverifikasi, produk disetujui, atau pendaftaran program diterima.
- **Conversational Interactive Bot**: Menu interaktif berbasis teks WhatsApp dengan state session:
  - Cek Status Akun & Verifikasi.
  - Informasi Program KADIN Berjalan.
  - Pencarian Katalog Produk Terverifikasi.
  - Hubungi Admin/Konsultasi.

---

## 🗄️ 5. Skema Database & Relasi (Database Schema)

| Tabel | Deskripsi & Relasi Kunci |
| :--- | :--- |
| `roles` | Daftar peran hak akses: `admin`, `umkm`, `buyer`. |
| `users` | Akun pengguna sistem (terhubung dengan `roles` melalui `role_id`). |
| `kategori_umkm` | Klasifikasi bidang UMKM (Kuliner, Kriya, Fashion, Agribisnis, Jasa, dll). |
| `umkm` | Data detail profil bisnis UMKM, legalitas, alamat, koordinat, dan data Skor Lestari. Berelasi dengan `users` (`user_id`) & `kategori_umkm` (`kategori_umkm_id`). |
| `kategori_produk` | Kategori produk yang dijual. |
| `produk` | Data katalog barang/jasa UMKM, harga, stok, foto, dan status moderasi. Berelasi dengan `umkm` (`umkm_id`) & `kategori_produk` (`kategori_produk_id`). |
| `catatan_pembinaan` | Riwayat catatan bimbingan/monitoring dari mentor Admin KADIN kepada UMKM. |
| `programs` | Program bimbingan, pelatihan, kurasi, atau workshop yang diselenggarakan KADIN. |
| `program_peserta` | Data pendaftaran UMKM pada program (Status: *terdaftar*, *diterima*, *ditolak*, *hadir*, *lulus*). |
| `peluang_bisnis` | Kebutuhan tender/pengadaan yang diposting oleh Buyer/Mitra. |
| `peluang_bisnis_minat`| Data pengajuan minat dari UMKM terhadap peluang bisnis Buyer. |
| `wa_sessions` | Menyimpan *state step navigation* sesi percakapan bot WhatsApp per nomor telepon. |
| `personal_access_tokens`| Token autentikasi API Laravel Sanctum. |

---

## 📁 6. Struktur Direktori Proyek

```
KAADIN_V0.1/
├── README.md                      # Dokumentasi komprehensif proyek (File ini)
│
├── backend/                       # REST API Backend Laravel 11/12
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/   # API Controllers (Auth, Publik, UMKM, Admin, Buyer, Webhook)
│   │   │   └── Middleware/        # CheckRoleMiddleware, Sanctum Authenticate
│   │   ├── Models/                # Eloquent Models (12 Model Terintegrasi)
│   │   └── Services/              # Business Logic (AI OpenRouter, Wablas, SkorLestari, dsb)
│   ├── database/
│   │   ├── migrations/            # 14 File Migrasi Database
│   │   └── seeders/               # 9 File Database Seeder Data Master & Dummy
│   ├── routes/
│   │   ├── api.php                # Seluruh Endpoint REST API (Publik, UMKM, Buyer, Admin)
│   │   └── web.php                # Route Web dasar & status check
│   ├── .env                       # Konfigurasi Environment (DB, APP_KEY, OpenRouter, Wablas)
│   └── composer.json              # Dependency backend PHP
│
└── frontend/                      # Web Client (Vanilla JS & CSS)
    ├── index.html                 # Homepage Landing Page KADIN
    ├── katalog.html               # Halaman Katalog Produk & UMKM Publik
    ├── produk-detail.html         # Detail Informasi Produk
    ├── umkm-detail.html           # Detail Profil & Portofolio UMKM
    ├── program.html               # Halaman Informasi Program KADIN Publik
    ├── login.html                 # Form Login Multi-Role
    ├── register-umkm.html         # Form Pendaftaran UMKM Baru
    │
    ├── admin/                     # Dashboard & Portal Admin KADIN
    │   ├── dashboard.html         # Analytics & Overview Admin
    │   ├── verifikasi-umkm.html   # Validasi Legalitas UMKM
    │   ├── moderasi-produk.html   # Kurasi & Moderasi Produk
    │   ├── skor-lestari.html      # Penilaian & Catatan Pembinaan Hijau
    │   ├── program.html           # Manajemen Program & Kehadiran
    │   ├── peluang-bisnis.html    # Manajemen Tender Pengadaan
    │   └── wa-bot-simulator.html  # Simulator Interaktif WhatsApp Bot
    │
    ├── umkm/                      # Dashboard & Portal UMKM Hub
    │   ├── dashboard.html         # Statistik Toko & Notifikasi
    │   ├── profil.html            # Kelola Data Bisnis & Legalitas
    │   ├── produk.html            # CRUD & Galeri Produk
    │   ├── skor-lestari.html      # Asesmen Praktik Ramah Lingkungan
    │   ├── program.html           # Rekomendasi AI & Pendaftaran Program
    │   └── peluang-bisnis.html    # Eksplorasi & Pengajuan B2B Tender
    │
    ├── buyer/                     # Dashboard & Portal Buyer / Mitra
    │   ├── dashboard.html         # Status Pengadaan & Minat Masuk
    │   └── peluang-bisnis.html    # Buat Tender, AI Matchmaking, & Kontak UMKM
    │
    ├── css/
    │   └── app.css                # Master Stylesheet (Design System & Glassmorphism)
    └── js/
        ├── api.js                 # API Fetch Wrapper, Base URL, & Error Handler
        ├── auth.js                # Token Manager, Role Guard, & Session Handling
        └── ui.js                  # Global UI Components (Toast, Modal, Loader, Nav)
```

---

## 🚀 7. Panduan Instalasi & Menjalankan Aplikasi

### 📋 Prasyarat:
- **PHP** >= 8.2 (dengan ekstensi `pdo`, `sqlite`/`mysql`, `curl`, `mbstring`, `fileinfo`)
- **Composer**
- Web Browser Modern (Chrome, Edge, Firefox, Safari)

---

### ⚙️ A. Menjalankan Backend (Laravel)

1. Buka terminal dan masuk ke folder `backend`:
   ```bash
   cd backend
   ```

2. Salin file `.env` (jika belum ada):
   ```bash
   cp .env.example .env
   ```

3. Install dependensi composer:
   ```bash
   composer install
   ```

4. Generate Application Key:
   ```bash
   php artisan key:generate
   ```

5. Buat file database SQLite (jika menggunakan driver SQLite):
   ```bash
   # Windows PowerShell
   New-Item -ItemType File -Path database\database.sqlite -Force
   ```

6. Jalankan migrasi dan seeding data default:
   ```bash
   php artisan migrate:fresh --seed
   ```

7. Buat symbolic link untuk upload gambar publik:
   ```bash
   php artisan storage:link
   ```

8. Jalankan development server backend:
   ```bash
   php artisan serve --port=8000
   ```
   > Backend API akan aktif di: `http://127.0.0.1:8000`

---

### 🌐 B. Menjalankan Frontend

Jalankan web server sederhana untuk folder `frontend`. Anda dapat menggunakan salah satu cara berikut:

**Opsi 1: Menggunakan PHP Built-in Server (Rekomendasi)**
```bash
# Dari root proyek (KAADIN_V0.1):
php -S 127.0.0.1:5500 -t frontend
```

**Opsi 2: Menggunakan VS Code Live Server**
- Buka folder `frontend` di VS Code / IDE.
- Klik kanan `index.html` -> *Open with Live Server* (Port default `5500`).

> Akses Frontend di browser melalui: `http://127.0.0.1:5500/`

---

## 🔑 8. Kredensial Akun Default untuk Pengujian

Semua akun pengujian di bawah ini memiliki password default: **`password`**

| Role | Nama Pengguna | Email | No. WhatsApp | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| **Admin** | Administrator KADIN Hub | `admin@kadin-jogja.id` | `081223344556` | Akses penuh seluruh modul Admin Panel. |
| **UMKM** | Ibu Sekar Wardani | `umkm.batik@example.com` | `081399887766` | UMKM Batik terverifikasi & Skor Lestari tinggi. |
| **UMKM** | Bapak Joko Susilo | `umkm.kopi@example.com` | `081244556677` | UMKM Kopi Merapi terverifikasi. |
| **UMKM** | Budi Santoso | `umkm.baru@example.com` | `087711223344` | UMKM Gerabah status registrasi baru (*Pending*). |
| **Buyer** | Manager Pengadaan Hotel Malioboro | `buyer@partner.com` | `081988776655` | Mitra Buyer korporat untuk tender pengadaan. |

---

## 📡 9. Ringkasan Endpoint API (API Reference)

### 1. Autentikasi & Webhook
- `POST /api/login` - Login pengguna (mengembalikan Sanctum Bearer Token & data Role).
- `POST /api/register-umkm` - Registrasi mandiri akun UMKM baru.
- `POST /api/logout` - Logout & revoke token aktif (`auth:sanctum`).
- `GET /api/me` - Ambil profil akun yang sedang login.
- `POST /api/wablas/webhook` - Webhook penanganan pesan masuk bot WhatsApp.

### 2. Publik (Tanpa Token)
- `GET /api/publik/katalog` - Daftar produk terpublikasi dengan filter & pencarian.
- `GET /api/publik/produk/{id}` - Detail produk lengkap.
- `GET /api/publik/umkm/{id}` - Detail profil UMKM dan daftar produknya.
- `GET /api/publik/kategori-umkm` - Daftar kategori bidang UMKM.
- `GET /api/publik/kategori-produk` - Daftar kategori produk.
- `GET /api/publik/program` - Daftar program pembinaan KADIN yang aktif.

### 3. UMKM Hub (`auth:sanctum` & `role:umkm`)
- `GET /api/umkm/dashboard` - Statistik & ikhtisar UMKM.
- `GET /api/umkm/profil` & `POST/PUT /api/umkm/profil` - Baca & update data profil/legalitas UMKM.
- `GET /api/umkm/produk` - Daftar produk milik UMKM.
- `POST /api/umkm/produk` - Tambah produk baru (Multi-part form data dengan upload gambar).
- `GET|PUT|DELETE /api/umkm/produk/{id}` - Detail, update, dan hapus produk.
- `GET /api/umkm/skor-lestari` - Asesmen & rincian Skor Lestari UMKM.
- `POST /api/umkm/praktik-ramah-lingkungan` - Simpan asesmen praktik ramah lingkungan.
- `GET /api/umkm/program` - Daftar program KADIN.
- `GET /api/umkm/rekomendasi-program` - Rekomendasi program terpersonalisasi dari AI.
- `POST /api/umkm/program/{id}/daftar` - Mendaftar ke program KADIN.
- `GET /api/umkm/peluang-bisnis` - Eksplorasi daftar peluang bisnis B2B.
- `POST /api/umkm/peluang-bisnis/{id}/minat` - Ajukan minat kerjasama pada peluang bisnis.

### 4. Buyer Portal (`auth:sanctum` & `role:buyer`)
- `GET /api/buyer/dashboard` - Ringkasan aktivitas pengadaan Buyer.
- `GET /api/buyer/peluang-bisnis` - Daftar peluang pengadaan yang diposting Buyer.
- `POST /api/buyer/peluang-bisnis` - Publikasikan peluang pengadaan baru.
- `GET /api/buyer/peluang-bisnis/{id}/minat` - Lihat daftar UMKM yang mengajukan minat.
- `POST /api/buyer/peluang-bisnis/{id}/status` - Update status tender (*buka*, *proses*, *selesai*, *tutup*).
- `GET /api/buyer/peluang-bisnis/{id}/rekomendasi-umkm` - AI Matchmaking untuk merekomendasikan UMKM paling cocok.

### 5. Admin Panel (`auth:sanctum` & `role:admin`)
- `GET /api/admin/dashboard` - Statistik KPI ekosistem KADIN.
- `GET /api/admin/umkm` & `GET /api/admin/umkm/{id}` - Daftar & detail UMKM.
- `POST /api/admin/umkm/{id}/verifikasi` - Verifikasi/tolak status UMKM.
- `GET /api/admin/produk` & `POST /api/admin/produk/{id}/moderasi` - Kurasi & moderasi produk.
- `GET /api/admin/skor-lestari` - Monitoring Skor Lestari seluruh UMKM.
- `POST /api/admin/umkm/{id}/catatan-pembinaan` - Tambah catatan bimbingan pendamping.
- `POST /api/admin/umkm/{id}/verifikasi-praktik` - Verifikasi kebenaran praktik hijau UMKM.
- `GET|POST|PUT|DELETE /api/admin/program` - CRUD Program Pembinaan KADIN.
- `GET /api/admin/program/{id}/peserta` - Kelola pendaftar program.
- `POST /api/admin/program/{id}/peserta/{pesertaId}/kehadiran` - Update presensi/kelulusan peserta.
- `GET /api/admin/peluang-bisnis` & `POST /api/admin/peluang-bisnis/{id}/status` - Kelola tender B2B.

---

## 🔒 10. Konfigurasi Environment Tambahan (`.env`)

Untuk mengaktifkan fitur AI dan WhatsApp Gateway secara penuh di lingkungan produksi, tambahkan variabel berikut di `backend/.env`:

```env
# OpenRouter AI API Configuration
OPENROUTER_API_KEY=sk-or-v1-xxxxxxxxxxxxxxxxxxxx
OPENROUTER_MODEL=meta-llama/llama-3.3-70b-instruct:free

# WhatsApp Gateway (WAHA - WhatsApp HTTP API)
WA_GATEWAY_DRIVER=waha
WAHA_BASE_URL=http://localhost:3000
WAHA_API_KEY=kadin_api_hub
WAHA_SECRET=qwertyuiop
WAHA_SESSION=default

# Wablas Gateway (Opsional / Alternatif)
WABLAS_TOKEN=kadin_api_hub
WABLAS_SECRET=qwertyuiop
WABLAS_DOMAIN=https://kudus.wablas.com
```

### 🔗 Setup Webhook di WAHA:
Di dashboard atau konfigurasi container WAHA, arahkan webhook event ke endpoint:
- **Webhook URL**: `http://localhost:8000/api/waha/webhook` (atau via tunnel Ngrok / domain publik)
- **Events**: `message`, `message.any`

---

## 📄 11. Lisensi & Tim Pengembang

- **Proyek**: KAADIN (KADIN UMKM Platform) v0.1
- **Organisasi**: KADIN (Kamar Dagang dan Industri Indonesia)
- **Status**: Active Development (Version 0.1)
- **Lisensi**: Proprietary / MIT License
#   M A G A N G  
 