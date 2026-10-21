# ---------- Tahap 1: build asset (Vite + Tailwind) ----------
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build

# ---------- Tahap 2: aplikasi Laravel ----------
FROM php:8.3-apache
RUN apt-get update && apt-get install -y --no-install-recommends git unzip libpq-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql pgsql zip gd bcmath opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && sed -ri -e 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
COPY --from=assets /app/public/build ./public/build
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/start.sh /usr/local/bin/start.sh
RUN sed -i "s/\r$//" /usr/local/bin/start.sh && chmod +x /usr/local/bin/start.sh
EXPOSE 10000
CMD ["start.sh"]
