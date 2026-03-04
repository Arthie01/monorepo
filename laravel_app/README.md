# 🚀 Laravel App

Proyecto Laravel 12 — API base sin interfaces ni lógica de negocio.

---

## Requisitos del sistema

| Herramienta | Versión mínima |
|-------------|---------------|
| PHP         | 8.2+          |
| Composer    | 2.x           |

---

## 🐧 Linux (Ubuntu 24.04)

### 1. Instalar PHP y extensiones

```bash
sudo apt-get update
sudo apt-get install -y php8.3 php8.3-cli php8.3-mbstring php8.3-xml \
  php8.3-curl php8.3-zip php8.3-tokenizer php8.3-bcmath php8.3-sqlite3 unzip
```

### 2. Instalar Composer

```bash
mkdir -p $HOME/.local/bin
curl -sS https://getcomposer.org/installer | php -- --install-dir=$HOME/.local/bin --filename=composer
echo 'export PATH="$HOME/.local/bin:$PATH"' >> ~/.bashrc
source ~/.bashrc
composer --version
```

### 3. Instalar dependencias del proyecto

```bash
cd laravel_app
composer install
```

### 4. Configurar entorno

```bash
cp .env.example .env          # solo si .env no existe
php artisan key:generate
```

### 5. Levantar el servidor de desarrollo

```bash
php artisan serve
# Disponible en: http://127.0.0.1:8000
```

---

## 🪟 Windows (PowerShell)

### 1. Instalar PHP (con Chocolatey)

```powershell
# Instalar Chocolatey (si no lo tienes):
Set-ExecutionPolicy Bypass -Scope Process -Force
[System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072
iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))

# Instalar PHP 8.3
choco install php --version=8.3.0 -y
php --version
```

### 2. Instalar Composer

```powershell
# Descarga e instalación con asistente gráfico:
Invoke-WebRequest -Uri "https://getcomposer.org/Composer-Setup.exe" -OutFile "Composer-Setup.exe"
.\Composer-Setup.exe
composer --version
```

### 3. Instalar dependencias del proyecto

```powershell
cd laravel_app
composer install
```

### 4. Configurar entorno

```powershell
Copy-Item .env.example .env     # solo si .env no existe
php artisan key:generate
```

### 5. Levantar el servidor de desarrollo

```powershell
php artisan serve
# Disponible en: http://127.0.0.1:8000
```

---

## 📁 Estructura del proyecto

```
laravel_app/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   └── Providers/
├── bootstrap/
├── config/
├── database/
│   └── migrations/
├── public/           ← document root (apunta aquí en producción)
├── resources/
├── routes/
│   ├── web.php       ← rutas web
│   └── api.php       ← rutas API
├── storage/
├── tests/
├── .env              ← variables de entorno (NO commitear)
├── .env.example
├── artisan
├── composer.json
└── composer.lock
```

---

## ⚙️ Comandos de desarrollo útiles

```bash
php artisan route:list          # listar rutas registradas
php artisan config:clear        # limpiar caché de config
php artisan cache:clear         # limpiar caché general
php artisan migrate             # ejecutar migraciones
php artisan make:controller NombreController
php artisan make:model NombreModelo
php artisan tinker              # REPL interactivo
```

---

## 🔗 Endpoint de prueba

`GET http://127.0.0.1:8000/` → Welcome page de Laravel.

> **Siguiente paso:** configurar base de datos en `.env` y crear rutas/controladores de la API.
