# Rangkuman Flow dan Scope KAADIN v0.1

Dokumen ini merangkum tujuan, aktor, alur bisnis, fitur utama, batasan, dan
prioritas pengembangan proyek **KAADIN (KADIN UMKM Platform) v0.1**. Dokumen
ini dapat digunakan sebagai dasar pembuatan flowchart, use case, product
backlog, dan pembahasan scope proyek.

## 1. Gambaran Umum

KAADIN adalah platform digital terpadu untuk:

- mendata dan memvalidasi UMKM;
- menampilkan katalog produk UMKM terverifikasi;
- membantu UMKM meningkatkan praktik usaha berkelanjutan melalui **Skor
  Lestari**;
- menghubungkan UMKM dengan program pembinaan KADIN;
- menghubungkan Buyer/Mitra Korporat dengan UMKM melalui peluang pengadaan
  B2B;
- memberikan rekomendasi berbasis AI; dan
- mengirimkan notifikasi serta menyediakan layanan mandiri melalui WhatsApp
  Bot.

Masalah utama yang ingin diselesaikan adalah data UMKM yang belum terpusat,
visibilitas produk yang terbatas, proses verifikasi yang belum terdigitalisasi,
serta sulitnya mempertemukan kebutuhan pengadaan korporat dengan UMKM yang
sesuai.

## 2. Tujuan Proyek

### Tujuan Bisnis

1. Membangun database UMKM yang terstruktur dan terverifikasi.
2. Meningkatkan akses UMKM ke pasar, program pembinaan, dan peluang kerja sama.
3. Menyediakan kanal pengadaan B2B yang lebih terarah bagi Buyer.
4. Mendorong penerapan praktik usaha ramah lingkungan.
5. Membantu KADIN memantau dan mengelola ekosistem UMKM secara terpusat.

### Tujuan Sistem

1. Menyediakan portal publik dan portal khusus berdasarkan peran pengguna.
2. Menjamin akses fitur menggunakan autentikasi token dan pembatasan role.
3. Menyediakan alur verifikasi legalitas, moderasi produk, dan verifikasi
   praktik ramah lingkungan.
4. Menyediakan integrasi AI untuk rekomendasi program dan matchmaking B2B.
5. Menyediakan notifikasi otomatis dan interaksi dasar melalui WhatsApp.

## 3. Aktor dan Hak Akses

| Aktor | Tujuan Utama | Akses Utama |
|---|---|---|
| Pengunjung publik | Mencari produk, UMKM, dan program KADIN | Katalog, detail produk, detail UMKM, informasi program |
| UMKM | Mengelola usaha dan mencari peluang | Profil, legalitas, produk, Skor Lestari, program, peluang B2B |
| Buyer/Mitra | Mencari pemasok dan mengelola kebutuhan pengadaan | Membuat RFQ/tender, melihat peminat, matchmaking, mengubah status |
| Admin KADIN | Mengelola dan memvalidasi ekosistem | Verifikasi UMKM, moderasi produk, program, ESG, tender, bot |
| AI Service | Membantu analisis dan rekomendasi | Rekomendasi program dan UMKM yang sesuai |
| WhatsApp Gateway | Menyampaikan layanan dan notifikasi | Notifikasi status serta percakapan berbasis menu |

## 4. Flow Utama Sistem

### 4.1 Flow Pengunjung Publik

```text
Pengunjung membuka platform
        |
        v
Melihat katalog / kategori / program
        |
        v
Mencari dan memfilter produk atau UMKM
        |
        v
Melihat detail produk/UMKM
        |
        +--> Menghubungi UMKM melalui WhatsApp
        |
        +--> Mendaftar sebagai UMKM
```

Produk yang tampil di area publik harus sudah dipublikasikan atau disetujui
melalui proses moderasi Admin.

### 4.2 Flow Registrasi dan Verifikasi UMKM

```text
UMKM mengisi formulir registrasi
        |
        v
Sistem membuat akun dengan status pending
        |
        v
UMKM melengkapi profil, legalitas, dan informasi usaha
        |
        v
Admin meninjau data dan dokumen
        |
        +--> Ditolak: UMKM memperbaiki data dan mengajukan kembali
        |
        +--> Diverifikasi: UMKM dapat menggunakan fitur utama
                              |
                              v
                     Notifikasi status dikirim
```

Status inti yang perlu diperhatikan:

- akun atau verifikasi UMKM: `pending`, `verified`, `rejected`;
- data legalitas: NIB, PIRT, Halal, sertifikat ekspor, dan dokumen terkait;
- notifikasi perubahan status dikirim melalui kanal yang tersedia.

### 4.3 Flow Pengelolaan Produk dan Katalog

```text
UMKM menambahkan produk
        |
        v
Mengisi nama, kategori, harga, stok, deskripsi, dan foto
        |
        v
Produk masuk ke proses moderasi
        |
        +--> Draft/ditolak: UMKM memperbaiki data
        |
        +--> Disetujui/dipublikasikan: tampil di katalog publik
                                      |
                                      v
                              Publik dapat melihat detail
```

Admin dapat meninjau, mempublikasikan, mengarsipkan, atau menolak produk.

### 4.4 Flow Skor Lestari dan Pembinaan

```text
UMKM mengisi asesmen praktik ramah lingkungan
        |
        v
Sistem menghitung atau memperbarui Skor Lestari
        |
        v
Admin meninjau bukti praktik
        |
        +--> Belum sesuai: Admin memberi catatan pembinaan
        |
        +--> Sesuai: praktik diverifikasi
        |
        v
UMKM melihat skor, status, dan rekomendasi perbaikan
```

Area asesmen meliputi limbah, energi, bahan baku, dan kemasan. Riwayat
pendampingan disimpan sebagai catatan pembinaan.

### 4.5 Flow Program Pembinaan KADIN

```text
Admin membuat program
        |
        v
Program dipublikasikan dengan kuota dan jadwal
        |
        v
UMKM melihat daftar program
        |
        +--> Melihat rekomendasi program dari AI
        |
        v
UMKM mendaftar
        |
        v
Admin meninjau dan menerima/menolak pendaftar
        |
        v
Admin mencatat kehadiran dan kelulusan
        |
        v
Status keikutsertaan tersimpan dan dapat dipantau
```

Status peserta dapat mencakup `terdaftar`, `diterima`, `ditolak`, `hadir`, dan
`lulus`.

### 4.6 Flow Pengadaan B2B dan AI Matchmaking

```text
Buyer membuat peluang pengadaan/RFQ
        |
        v
Mengisi spesifikasi, kebutuhan, estimasi nilai, dan tenggat
        |
        v
Peluang dipublikasikan
        |
        +--> UMKM melihat peluang
        |        |
        |        v
        |   Mengajukan minat kerja sama
        |
        v
Buyer melihat peminat dan rekomendasi AI
        |
        v
Buyer meninjau kecocokan, kapasitas, dan profil UMKM
        |
        v
Buyer menghubungi UMKM dan memperbarui status pengadaan
```

Status peluang pengadaan yang direncanakan adalah `buka`, `proses`, `selesai`,
dan `tutup`. AI menggunakan deskripsi kebutuhan Buyer dan atribut UMKM seperti
kategori, kapasitas, verifikasi, serta praktik berkelanjutan untuk menghasilkan
rekomendasi dan skor kecocokan.

### 4.7 Flow WhatsApp Bot dan Notifikasi

```text
Event sistem atau pesan masuk WhatsApp
        |
        +--> Event sistem: buat notifikasi status
        |                    |
        |                    v
        |              Kirim melalui gateway
        |
        +--> Pesan masuk: baca nomor dan state sesi
                             |
                             v
                    Tampilkan menu atau jawaban
                             |
                             v
                    Simpan state percakapan
```

Fitur bot yang direncanakan:

- mengecek status akun dan verifikasi;
- melihat informasi program berjalan;
- mencari katalog produk terverifikasi;
- menghubungi Admin atau meminta konsultasi.

## 5. Scope In-Scope

### Modul Publik

- landing page KADIN;
- katalog produk dengan pencarian dan filter;
- detail produk dan profil UMKM;
- informasi program KADIN;
- kontak UMKM melalui WhatsApp.

### Modul Autentikasi

- login multi-role;
- registrasi UMKM;
- token authentication menggunakan Laravel Sanctum;
- logout, pengambilan profil akun, dan role guard.

### Modul UMKM

- dashboard;
- profil bisnis dan legalitas;
- CRUD produk, foto, stok, dan kategori;
- asesmen serta pemantauan Skor Lestari;
- rekomendasi dan pendaftaran program;
- eksplorasi peluang B2B dan pengajuan minat.

### Modul Buyer

- dashboard pengadaan;
- CRUD atau publikasi peluang pengadaan;
- pengelolaan peminat;
- rekomendasi UMKM berbasis AI;
- perubahan status tender dan komunikasi dengan UMKM.

### Modul Admin KADIN

- dashboard metrik ekosistem;
- verifikasi UMKM;
- moderasi produk;
- verifikasi praktik ramah lingkungan dan catatan pembinaan;
- pengelolaan program, kuota, peserta, kehadiran, dan kelulusan;
- monitoring peluang bisnis;
- simulator WhatsApp Bot.

### Integrasi

- OpenRouter untuk rekomendasi AI;
- WAHA atau Wablas sebagai gateway WhatsApp;
- webhook untuk pesan masuk;
- penyimpanan file foto produk.

## 6. Scope Out-of-Scope atau Batasan v0.1

Hal-hal berikut belum menjadi fokus utama versi v0.1:

- pembayaran online dan payment gateway;
- keranjang belanja serta checkout B2C;
- pengelolaan pesanan, invoice, dan logistik end-to-end;
- kontrak digital atau tanda tangan elektronik;
- penilaian vendor setelah transaksi selesai;
- integrasi ERP, marketplace, atau sistem procurement eksternal;
- proses sertifikasi legalitas ke instansi pemerintah;
- perhitungan ESG yang bersifat audit atau sertifikasi resmi;
- chatbot AI bebas tanpa batas di luar menu layanan yang ditentukan;
- aplikasi mobile native Android/iOS;
- analitik bisnis lanjutan dan predictive analytics;
- multi-organisasi atau multi-tenant selain ekosistem KADIN yang ditargetkan.

Batasan ini dapat berubah pada versi berikutnya berdasarkan kebutuhan bisnis,
kesiapan integrasi, dan validasi pengguna.

## 7. Data Inti dan Relasi Bisnis

```text
Role
  └── User
        └── UMKM ─── Kategori UMKM
              ├── Produk ─── Kategori Produk
              ├── Skor Lestari / praktik hijau
              ├── Catatan pembinaan
              └── Pendaftaran program

Buyer/User
  └── Peluang Bisnis
        └── Minat UMKM

WhatsApp
  └── Sesi percakapan per nomor telepon
```

Entitas utama yang perlu dijaga konsistensinya:

- `users`, `roles`, dan `umkm`;
- `produk`, `kategori_produk`, dan status moderasi;
- `programs`, `program_peserta`, dan status peserta;
- `peluang_bisnis`, `peluang_bisnis_minat`, dan status tender;
- skor/praktik lestari serta `catatan_pembinaan`;
- `wa_sessions` untuk state bot.

## 8. Arsitektur dan Teknologi

| Bagian | Teknologi |
|---|---|
| Frontend | HTML5, Vanilla JavaScript ES6+, Vanilla CSS |
| Backend | Laravel 11/12, PHP 8.2+ |
| API | REST API dengan format JSON |
| Auth | Laravel Sanctum dan middleware role |
| ORM/Database | Eloquent, SQLite untuk development, MySQL/PostgreSQL untuk production |
| AI | OpenRouter API |
| WhatsApp | WAHA dan/atau Wablas |
| Storage | Laravel public storage untuk foto produk |

Frontend dan backend dipisahkan. Frontend berkomunikasi dengan backend melalui
REST API, sedangkan business logic utama berada di controller, service, model,
dan middleware Laravel.

## 9. Prioritas Implementasi yang Disarankan

### Prioritas 1 - Fondasi MVP

1. Autentikasi, role, dan proteksi endpoint.
2. Registrasi serta profil UMKM.
3. Verifikasi UMKM oleh Admin.
4. CRUD produk dan moderasi.
5. Katalog publik dan detail produk/UMKM.

### Prioritas 2 - Pembinaan dan Pengadaan

1. Skor Lestari dan catatan pembinaan.
2. Program KADIN dan pendaftaran peserta.
3. Peluang pengadaan Buyer.
4. Pengajuan minat UMKM.
5. Dashboard Admin, UMKM, dan Buyer.

### Prioritas 3 - Otomasi dan Optimasi

1. Rekomendasi program berbasis AI.
2. AI matchmaking Buyer-UMKM.
3. Notifikasi WhatsApp.
4. WhatsApp Bot berbasis state sesi.
5. Monitoring, logging, dan peningkatan kualitas data.

## 10. Indikator Keberhasilan

- UMKM dapat mendaftar dan melengkapi profil tanpa bantuan teknis.
- Admin dapat memverifikasi UMKM dan memoderasi produk.
- Produk terverifikasi dapat ditemukan oleh publik melalui katalog.
- UMKM dapat mengikuti program dan memantau statusnya.
- Buyer dapat membuat peluang pengadaan dan menerima pengajuan minat.
- Sistem menghasilkan rekomendasi UMKM atau program yang relevan.
- Perubahan status penting dapat diinformasikan melalui WhatsApp.
- Hak akses antar-role tidak saling menembus.

## 11. Risiko dan Hal yang Perlu Divalidasi

1. **Kualitas data UMKM**: rekomendasi AI bergantung pada profil, legalitas,
   kategori, dan kapasitas yang lengkap.
2. **Validitas Skor Lestari**: klaim praktik hijau perlu bukti dan proses
   verifikasi Admin yang jelas.
3. **Ketergantungan layanan eksternal**: OpenRouter dan gateway WhatsApp
   memerlukan API key, konfigurasi, kuota, dan koneksi yang tersedia.
4. **Privasi data**: dokumen legalitas dan nomor WhatsApp perlu dibatasi sesuai
   hak akses dan kebijakan penyimpanan data.
5. **Definisi proses bisnis**: perlu disepakati apakah platform hanya
   memfasilitasi matchmaking atau juga mencatat hasil transaksi/kontrak.
6. **Kesiapan produksi**: konfigurasi development seperti SQLite dan kredensial
   dummy harus dipisahkan dari deployment produksi.

## 12. Kesimpulan

KAADIN v0.1 adalah platform penghubung antara KADIN, UMKM, dan Buyer dengan
fokus pada katalog terverifikasi, pembinaan berkelanjutan, serta peluang
pengadaan B2B. Flow inti platform dimulai dari pendataan dan verifikasi UMKM,
dilanjutkan publikasi produk/program, pengembangan melalui Skor Lestari dan
program pembinaan, lalu matchmaking dengan Buyer. AI dan WhatsApp berfungsi
sebagai lapisan pendukung untuk mempercepat rekomendasi, komunikasi, dan
notifikasi; keduanya bukan pengganti proses validasi dan keputusan Admin.

