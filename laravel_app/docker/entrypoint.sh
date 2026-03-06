#!/bin/bash
set -e

# Crear .env desde .env.example si no existe
if [ ! -f /var/www/html/.env ]; then
    echo "==> Creando .env desde .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Sobrescribir variables críticas para el entorno Docker
echo "==> Configurando variables de entorno..."
sed -i "s|DB_CONNECTION=.*|DB_CONNECTION=sqlite|" /var/www/html/.env
sed -i "s|# DB_DATABASE=.*||" /var/www/html/.env
grep -q "^DB_DATABASE=" /var/www/html/.env || echo "DB_DATABASE=/var/www/html/database/database.sqlite" >> /var/www/html/.env

echo "==> Generando APP_KEY..."
php artisan key:generate --force --no-interaction

echo "==> Creando base de datos SQLite si no existe..."
touch /var/www/html/database/database.sqlite

echo "==> Ejecutando migraciones..."
php artisan migrate --force --no-interaction

echo "==> Cacheando configuración, rutas y vistas..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Iniciando servicios (PHP-FPM + Nginx) con Supervisor..."
exec supervisord -c /etc/supervisord.conf
