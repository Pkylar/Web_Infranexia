# ===== Stage 1: (opsional) build asset Vite =====
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci || true
COPY . .
# Kalau script build tidak ada / gagal, tetap buat folder agar COPY tidak error
RUN npm run build || mkdir -p public/build

# ===== Stage 2: runtime PHP 8.3 + extensions + composer =====
FROM php:8.3-cli-bookworm

# Ekstensi yang dibutuhkan Laravel + PhpSpreadsheet (gd)
RUN apt-get update && apt-get install -y \
    libzip-dev libicu-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libcurl4-openssl-dev zlib1g-dev git unzip \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo_mysql mbstring zip intl gd exif bcmath dom \
 && rm -rf /var/lib/apt/lists/*

# Composer CLI
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Jalankan composer SETELAH ext gd terpasang
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts
COPY . .
# Copy hasil build (folder pasti ada berkat mkdir di stage frontend)
COPY --from=frontend /app/public/build ./public/build
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permission minimal
RUN mkdir -p storage bootstrap/cache && chmod -R 775 storage bootstrap/cache

ENV PORT=8080
EXPOSE 8080
CMD ["php","-S","0.0.0.0:8080","-t","public","public/index.php"]
