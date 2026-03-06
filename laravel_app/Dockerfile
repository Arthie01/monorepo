# ─────────────────────────────────────────────
#  Laravel App — Dockerfile (Multi-stage)
#
#  Stage 1: node-builder  → compila los assets con Vite
#  Stage 2: php-app       → PHP 8.2-FPM + Nginx + Supervisor
#  Puerto: 80
# ─────────────────────────────────────────────

# ── Stage 1: Build de assets (Node/Vite) ──────
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.js ./
COPY resources/ ./resources/
COPY public/ ./public/

RUN npm run build

# ── Stage 2: PHP-FPM + Nginx + Supervisor ─────
FROM php:8.2-fpm-alpine AS php-app

# Instalar dependencias del sistema y extensiones PHP necesarias
RUN apk add --no-cache \
        nginx \
        supervisor \
        bash \
        curl \
        libpng-dev \
        libjpeg-turbo-dev \
        libwebp-dev \
        zip \
        unzip \
        oniguruma-dev \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
    && rm -rf /var/cache/apk/* \
    # Limpiar configs por defecto de Nginx que entrarían en conflicto
    && rm -f /etc/nginx/conf.d/default.conf /etc/nginx/http.d/default.conf \
    # Crear directorios de logs y PID de Nginx
    && mkdir -p /var/log/nginx /run/nginx \
    && touch /var/log/nginx/access.log /var/log/nginx/error.log

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Copiar composer.json/lock primero (permite caché de Docker)
COPY composer.json composer.lock ./

# Instalar dependencias PHP (sin devDeps en producción)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copiar el resto del código fuente
COPY . .

# Copiar assets compilados desde el stage de Node
COPY --from=node-builder /app/public/build ./public/build

# Crear el archivo SQLite de la base de datos si no existe
RUN mkdir -p database && \
    touch database/database.sqlite

# Permisos de storage y cache
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data /var/www/html

# Copiar configuración de Nginx
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copiar configuración de Supervisor
COPY docker/supervisord.conf /etc/supervisord.conf

# Entrypoint: genera KEY, migra y arranca servicios
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
