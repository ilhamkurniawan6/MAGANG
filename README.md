# KADIN Digital Hub (Laravel)

## Menjalankan

```bash
composer install
cp .env.example .env
php artisan key:generate
# buat database `kadin_digital_hub` di MySQL, lalu isi DB_PASSWORD di .env
php artisan migrate --seed
php artisan storage:link
npm install
npm run dev      # terminal kedua; untuk produksi: npm run build
php artisan serve
```

- Halaman UI ada di `resources/views/` (Blade); CSS di `resources/css/kadin.css`, JS di `resources/js/` (dimuat lewat Vite, jadi `npm run dev` wajib berjalan).
- Login admin awal: `admin@kadin-jogja.id` / `password` (segera ganti).
- Data contoh: `php artisan db:seed --class=DemoSeeder`
- MinIO, OpenRouter, dan Wablas opsional. Jika kosong, upload memakai disk lokal dan WA berjalan mode demo.
