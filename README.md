# Monorepo — Primer Parcial

Repositorio del primer parcial. Contiene dos aplicaciones web independientes dentro de un mismo monorepo:

| App | Stack | Puerto |
|-----|-------|--------|
| `flask_app/` | Python 3 · Flask 3 · Jinja2 | `5000` |
| `laravel_app/` | PHP 8.2 · Laravel 12 | `8000` |

---

## Estructura del proyecto

```
monorepo/
├── flask_app/
│   ├── app.py                  # Punto de entrada Flask
│   ├── requirements.txt        # Dependencias Python
│   ├── templates/
│   │   └── login.html          # UI de inicio de sesión (Jinja2)
│   └── static/
│       ├── css/
│       │   └── login.css       # Estilos de la pantalla login
│       └── images/
│           ├── logo2.jpeg
│           ├── correo.jpg
│           └── candado.jpg
└── laravel_app/
    ├── app/
    ├── routes/
    ├── resources/
    └── public/
        └── images/
```

---

## Requisitos del sistema

| Herramienta | Versión mínima | Verificar |
|-------------|---------------|-----------|
| Python      | 3.10+         | `python3 --version` |
| pip         | 23+           | `pip3 --version` |
| PHP         | 8.2+          | `php --version` |
| Composer    | 2.x           | `composer --version` |
| Git         | cualquier     | `git --version` |

---

## 🐍 Flask App — Pantalla de Login

### 1. Ir a la carpeta

```bash
cd flask_app
```

### 2. Crear y activar entorno virtual

```bash
python3 -m venv venv
source venv/bin/activate
```

> El prompt cambiará a `(venv) ...` cuando esté activo.

### 3. Instalar dependencias

```bash
pip install -r requirements.txt
```

### 4. Levantar el servidor

```bash
python3 app.py
```

### 5. Abrir en el navegador

| Ruta | Descripción |
|------|-------------|
| [http://127.0.0.1:5000/](http://127.0.0.1:5000/) | Health check (`{"status": "OK"}`) |
| [http://127.0.0.1:5000/login](http://127.0.0.1:5000/login) | Pantalla de inicio de sesión |

### 6. Detener el servidor

```bash
# Ctrl + C en la terminal donde corre Flask
```

### 7. Desactivar el entorno virtual (cuando termines)

```bash
deactivate
```

---

## 🚀 Laravel App

### 1. Ir a la carpeta

```bash
cd laravel_app
```

### 2. Instalar dependencias PHP

```bash
composer install
```

### 3. Copiar variables de entorno

```bash
cp .env.example .env
```

### 4. Generar clave de aplicación

```bash
php artisan key:generate
```

### 5. Levantar el servidor de desarrollo

```bash
php artisan serve
```

### 6. Abrir en el navegador

| Ruta | Descripción |
|------|-------------|
| [http://127.0.0.1:8000/](http://127.0.0.1:8000/) | Página de bienvenida Laravel |

---

## Clonar el repositorio desde cero

```bash
git clone https://github.com/Arthie01/monorepo.git
cd monorepo
```

Luego sigue los pasos de cada app por separado según lo necesites.

---

## Flujo de trabajo con Git

```bash
# Ver cambios pendientes
git status

# Agregar todos los cambios
git add .

# Hacer commit
git commit -m "descripción del cambio"

# Subir a GitHub
git push
```

---

## Lo que lleva la pantalla de login (solo UI)

- Campo **correo electrónico** con ícono
- Campo **contraseña** con ícono
- Checkbox **Recuérdame**
- Botón **Iniciar sesión**
- Link **¿Olvidaste tu contraseña?**
- Link **Regístrate**
- Diseño responsivo (mobile + desktop)
- Fondo con triángulos rojos en esquinas (CSS puro, sin frameworks)
