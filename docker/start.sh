#!/bin/sh
set -e
cd /var/www/html

# Render memberi port lewat $PORT (default 10000)
PORT="${PORT:-10000}"
sed -ri "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

php artisan package:discover --ansi || true
php artisan migrate --force

# Isi data awal hanya jika RUN_SEED=true (jalankan sekali saja, lalu ubah ke false)
if [ "${RUN_SEED}" = "true" ]; then
  php artisan db:seed --force
fi

php artisan config:cache
php artisan route:cache || true
php artisan view:cache || true

exec apache2-foreground
