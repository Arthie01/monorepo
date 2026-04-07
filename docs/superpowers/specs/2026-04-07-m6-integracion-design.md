# M6 — Integración Frontends ↔ API

**Fecha:** 2026-04-07
**Proyecto:** MACUIN Autopartes y Distribución
**Milestone:** M6 — Conectar Flask y Laravel a la API FastAPI

---

## Objetivo

Conectar ambos frontends (Flask y Laravel) a la API central FastAPI, eliminando todos los datos
hardcodeados y placeholders. Ningún frontend accede a PostgreSQL directamente — toda la lógica
de datos pasa por un endpoint de FastAPI.

---

## Arquitectura General

```
Flask (Panel Interno)         Laravel (Portal Externo)
       ↓                              ↓
   services/                    Controllers/
       ↓                              ↓
 api/client.py               Http/Services/
 (ApiClient)                       ↓
       ↓                    Http/Client/ApiClient.php
       └──────────┬─────────────────┘
                  ↓
        http://fastapi:8000
         FastAPI — API Central
                  ↓
            PostgreSQL 15
```

**Reglas:**
- `app.py` / `web.php` solo registran rutas, sin lógica de negocio.
- Controllers/rutas orquestan: llaman al service, manejan session, renderizan vista.
- Services encapsulan qué endpoints llamar y qué campos retornar.
- `ApiClient` es el único punto de contacto con FastAPI: BASE_URL, headers, errores HTTP.
- Ningún frontend tiene credenciales de PostgreSQL ni usa ORM propio.

---

## Sección 1: Flask — Panel Interno

### Estructura de archivos nuevos

```
flask_app/
├── api/
│   └── client.py             ← ApiClient + ApiException
├── services/
│   ├── auth.py               ← login interno
│   ├── autopartes.py         ← CRUD autopartes
│   ├── pedidos.py            ← listar, obtener, cambiar_estado
│   └── usuarios.py           ← CRUD internos + externos
```

### ApiClient (flask_app/api/client.py)

Clase con métodos estáticos que envuelven `requests`:

```python
class ApiException(Exception):
    def __init__(self, status_code: int, detail: str): ...

class ApiClient:
    BASE_URL = os.getenv("API_BASE_URL", "http://localhost:8001")

    @staticmethod
    def get(path: str, params: dict = None) -> dict

    @staticmethod
    def post(path: str, json: dict = None, files=None) -> dict

    @staticmethod
    def patch(path: str, json: dict = None) -> dict

    @staticmethod
    def delete(path: str, auth: tuple = ("macuin", "123456")) -> dict
```

Todos los métodos lanzan `ApiException(status_code, detail)` si la respuesta es >= 400.

### Services Flask

| Módulo | Métodos |
|---|---|
| `auth.py` | `login(email, password) → dict` |
| `autopartes.py` | `listar(categoria=None)`, `obtener(id)`, `crear(form, file)`, `editar(id, form, file)`, `patch(id, campos)`, `eliminar(id)` |
| `pedidos.py` | `listar(estado=None)`, `obtener(id)`, `cambiar_estado(id, estado)` |
| `usuarios.py` | `listar_internos()`, `obtener_interno(id)`, `crear_interno(data)`, `editar_interno(id, data)`, `eliminar_interno(id)` + equivalentes para externos |

### Sesión Flask

```python
# Login exitoso
session["usuario"] = {
    "id": 1, "nombre": "Ana", "rol": "admin",
    "perm_autopartes": True, "perm_pedidos": True,
    "perm_usuarios": True, "perm_reportes": True
}

# Verificación en cada ruta protegida
if "usuario" not in session:
    return redirect("/login")

# Logout
session.clear()
return redirect("/login")
```

`app.secret_key` se lee desde `os.getenv("FLASK_SECRET_KEY", "dev-secret")`.

### Rutas POST nuevas en app.py

| Método | Ruta | Acción |
|---|---|---|
| POST | `/login` | login → session → redirect /gestion-autopartes |
| GET | `/logout` | session.clear() → redirect /login |
| POST | `/agregar-autoparte` | crear autoparte → redirect /gestion-autopartes |
| POST | `/editar-autoparte/<id>` | editar autoparte → redirect /gestion-autopartes |
| GET | `/eliminar-autoparte/<id>` | eliminar autoparte → redirect /gestion-autopartes |
| POST | `/agregar-usuario-interno` | crear usuario interno |
| POST | `/editar-usuario-interno/<id>` | editar usuario interno |
| GET | `/eliminar-usuario-interno/<id>` | eliminar usuario interno |
| POST | `/agregar-usuario-externo` | crear usuario externo |
| POST | `/editar-usuario-externo/<id>` | editar usuario externo |
| GET | `/eliminar-usuario-externo/<id>` | eliminar usuario externo |
| GET | `/detalle-pedido/<id>` | detalle pedido → PedidosService.obtener(id) |
| POST | `/pedidos/<id>/estado` | cambiar estado pedido |
| GET | `/reportes/descargar/<tipo>/<formato>` | proxy descarga → FastAPI |

### Descarga de reportes desde Flask

```python
# Flask actúa como proxy: llama a FastAPI con HTTPBasic y reenvía el archivo
response = requests.get(
    f"{API_BASE_URL}/v1/reportes/{tipo}/{formato}",
    auth=("macuin", "123456"),
    stream=True
)
return send_file(
    BytesIO(response.content),
    download_name=f"reporte_{tipo}.{formato}",
    as_attachment=True
)
```

### Manejo de errores Flask

```python
try:
    AutopartesService.crear(form, file)
    flash("Autoparte creada correctamente", "success")
    return redirect("/gestion-autopartes")
except ApiException as e:
    flash(e.detail, "error")
    return redirect("/agregar-autoparte")
```

Los templates muestran los mensajes flash en el área de notificaciones existente.

---

## Sección 2: Laravel — Portal Externo

### Estructura de archivos nuevos

```
laravel_app/app/Http/
├── Client/
│   └── ApiClient.php             ← wraps Http::, BASE_URL, ApiException
├── Services/
│   ├── AuthService.php           ← registro + login externo
│   ├── AutopartesService.php     ← listar + obtener
│   └── PedidosService.php        ← crear, listar, obtener
├── Controllers/
│   ├── AuthController.php        ← login, registro, logout
│   ├── CatalogoController.php    ← dashboard, index, show
│   ├── CarritoController.php     ← carrito + checkout
│   ├── PedidoController.php      ← index, show
│   └── PerfilController.php      ← index (session + GET /v1/usuarios/externos/{id})
└── Middleware/
    └── CheckSession.php          ← verifica session('usuario')
```

### ApiClient (app/Http/Client/ApiClient.php)

```php
class ApiClient {
    private string $base;  // config('services.api.url')

    public function get(string $path, array $query = []): array
    public function post(string $path, array $data = []): array
    public function postMultipart(string $path, array $fields, $file = null): array
    public function patch(string $path, array $data = []): array
    // Todos lanzan ApiException si status >= 400
}

class ApiException extends \RuntimeException {
    public function __construct(public int $statusCode, string $message) {
        parent::__construct($message);
    }
}
```

### Services Laravel

| Clase | Métodos |
|---|---|
| `AuthService` | `registro(array $data): array`, `login(string $email, string $password): array` |
| `AutopartesService` | `listar(?string $categoria = null): array`, `obtener(int $id): array` |
| `PedidosService` | `crear(int $usuarioId, array $items, array $direccion): array`, `listar(int $usuarioId): array`, `obtener(int $id): array` |

### Sesión Laravel

```php
// Login exitoso
session([
    'usuario' => [
        'id' => 1, 'nombre' => 'Carlos',
        'tipo_cliente' => 'Taller', 'descuento' => 10
    ]
]);

// Middleware CheckSession
if (!session('usuario')) {
    return redirect('/login');
}

// Logout
session()->forget('usuario');
return redirect('/login');
```

### Carrito en sesión Laravel

El carrito no tiene endpoint en FastAPI — es estado temporal del cliente:

```php
// Agregar item
$carrito = session('carrito', []);
$carrito[$id] = [
    'autoparte_id' => $id,
    'nombre'       => $nombre,
    'precio'       => $precio,
    'cantidad'     => $cantidad,
    'imagen'       => $imagen,
];
session(['carrito' => $carrito]);

// Checkout → llamar API
$items = collect(session('carrito'))->map(fn($i) => [
    'autoparte_id' => $i['autoparte_id'],
    'cantidad'     => $i['cantidad'],
])->values()->all();

$this->pedidosService->crear(session('usuario.id'), $items, $direccion);
session()->forget('carrito');
```

### Rutas actualizadas (web.php)

```php
// Públicas
Route::get('/login',    [AuthController::class, 'showLogin']);
Route::post('/login',   [AuthController::class, 'login']);
Route::get('/registro', [AuthController::class, 'showRegistro']);
Route::post('/registro',[AuthController::class, 'registro']);
Route::get('/logout',   [AuthController::class, 'logout']);

// Protegidas (middleware CheckSession)
Route::middleware('check.session')->group(function () {
    Route::get('/dashboard',          [CatalogoController::class,  'dashboard']);
    Route::get('/catalogo',           [CatalogoController::class,  'index']);
    Route::get('/catalogo/{id}',      [CatalogoController::class,  'show']);
    Route::get('/carrito',            [CarritoController::class,   'index']);
    Route::post('/carrito/agregar',   [CarritoController::class,   'agregar']);
    Route::post('/carrito/actualizar',[CarritoController::class,   'actualizar']);
    Route::post('/checkout',          [CarritoController::class,   'checkout']);
    Route::get('/pedidos',            [PedidoController::class,    'index']);
    Route::get('/pedido/{id}',        [PedidoController::class,    'show']);
    Route::get('/perfil',             [PerfilController::class,    'index']);
});

// Estática — sin integración API (no hay endpoint de recuperación de contraseña)
Route::get('/forgot-password', fn() => view('forgot-password'));
```

### Manejo de errores Laravel

```php
try {
    $this->pedidosService->crear(...);
    return redirect('/pedidos')->with('success', 'Pedido creado exitosamente');
} catch (ApiException $e) {
    return back()->withErrors(['api' => $e->getMessage()])->withInput();
}
```

---

## Sección 3: Configuración y Docker

### Variables de entorno

**Flask** (env Docker o `.env`):
```
API_BASE_URL=http://fastapi:8000
FLASK_SECRET_KEY=macuin-secret-2026
```

**Laravel** (`.env`):
```
API_BASE_URL=http://fastapi:8000
```

**laravel_app/config/services.php:**
```php
'api' => [
    'url' => env('API_BASE_URL', 'http://localhost:8001'),
],
```

### docker-compose.yml — cambios mínimos

Solo agregar variables de entorno a los servicios `flask` y `laravel`:

```yaml
flask:
  environment:
    - API_BASE_URL=http://fastapi:8000
    - FLASK_SECRET_KEY=macuin-secret-2026

laravel:
  environment:
    - API_BASE_URL=http://fastapi:8000
```

Sin cambios en FastAPI ni PostgreSQL.

---

## Sección 4: Archivos Modificados

### Templates Flask — cambios tipo

Reemplazar datos hardcodeados por variables Jinja2:
```html
<!-- ANTES -->
<span class="kpi-val">1</span>

<!-- DESPUÉS -->
<span class="kpi-val">{{ autopartes | length }}</span>
```

### Vistas Laravel — cambios tipo

```blade
{{-- ANTES --}}
<div class="product-name">Filtro de Aceite Premium</div>

{{-- DESPUÉS --}}
<div class="product-name">{{ $autoparte['nombre'] }}</div>
```

---

## Sección 5: Orden de Implementación

```
Fase 1 — Flask
  1. flask_app/api/client.py           (ApiClient + ApiException)
  2. flask_app/services/auth.py        (login)
  3. flask_app/services/autopartes.py  (CRUD)
  4. flask_app/services/pedidos.py     (listar, obtener, estado)
  5. flask_app/services/usuarios.py    (CRUD internos + externos)
  6. flask_app/app.py                  (rutas POST + session + secret_key)
  7. flask_app/templates/*.html        (variables Jinja2)

Fase 2 — Laravel
  8.  app/Http/Client/ApiClient.php
  9.  app/Http/Services/AuthService.php
  10. app/Http/Services/AutopartesService.php
  11. app/Http/Services/PedidosService.php
  12. app/Http/Middleware/CheckSession.php
  13. app/Http/Controllers/ (5 controllers)
  14. routes/web.php (closures → controllers)
  15. config/services.php + .env
  16. resources/views/*.blade.php (variables Blade)

Fase 3 — Docker
  17. docker-compose.yml (env vars)
  18. Prueba end-to-end en Docker
```

---

## Criterios de Éxito

- [ ] Login Flask → session activa → panel con datos reales de la API
- [ ] CRUD autopartes desde Flask (crear con imagen, editar, eliminar)
- [ ] Cambio de estado de pedidos desde Flask
- [ ] CRUD usuarios internos y externos desde Flask
- [ ] Descarga de reportes PDF/xlsx/docx desde Flask
- [ ] Registro y login Laravel → session activa → catálogo con productos reales
- [ ] Carrito funcional en session → checkout → `POST /v1/pedidos/`
- [ ] Mis pedidos con datos reales por usuario logueado
- [ ] Ningún frontend accede a PostgreSQL directamente

---

## Lo que NO Cambia

- FastAPI — cero cambios
- PostgreSQL — cero cambios
- CSS, assets, estructura de templates
- Identidad visual MACUIN
