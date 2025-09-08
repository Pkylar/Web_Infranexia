# ===== Stage 1: install PHP deps (composer) =====
FROM composer:2 AS backend
WORKDIR /app
COPY composer.json composer.lock ./
# abaikan cek ext-gd di stage ini
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --ignore-platform-req=ext-gd
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-req=ext-gd

# ===== Stage 2: (opsional) build asset Vite =====
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci || true
COPY . .
RUN npm run build || true

# ===== Stage 3: runtime PHP 8.3 + extensions =====
FROM php:8.3-cli-bookworm

RUN apt-get update && apt-get install -y \
    libzip-dev libicu-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libcurl4-openssl-dev zlib1g-dev git unzip \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo_mysql mbstring zip intl gd exif bcmath dom \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY --from=backend /app /app
COPY --from=frontend /app/public/build /app/public/build 2>/dev/null || true

RUN mkdir -p storage bootstrap/cache && chmod -R 775 storage bootstrap/cache

ENV PORT=8080
EXPOSE 8080
CMD ["php","-S","0.0.0.0:8080","-t","public","public/index.php"]
