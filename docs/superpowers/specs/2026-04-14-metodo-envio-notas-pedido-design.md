# Diseño: Método de Envío y Notas del Pedido
**Fecha:** 2026-04-14
**Proyecto:** MACUIN Autopartes y Distribución
**Alcance:** Full stack — BD → FastAPI → Laravel → Flask

---

## Contexto

El checkout de Laravel (`checkout.blade.php`) presenta al cliente dos campos:
- **Método de envío** (`name="shipping"`): radio con valores `express`, `estandar`, `recoger`
- **Notas del pedido** (`name="notes"`): textarea libre, opcional

Actualmente `CarritoController::checkout()` ignora ambos campos al construir el pedido. Como resultado, no se persisten en la BD y no aparecen en el detalle del pedido ni en el portal interno (Flask) ni en el portal del cliente (Laravel).

---

## Objetivo

Persistir `metodo_envio` y `notas` al crear un pedido y mostrarlos en el detalle del pedido en ambos portales.

---

## Archivos Afectados

| Capa | Archivo | Cambio |
|------|---------|--------|
| BD | `fastapi_app/app/data/ddl.sql` | Agregar 2 columnas a `tb_pedidos` |
| BD | `fastapi_app/app/data/migration_envio_notas.sql` | Script `ALTER TABLE` para BD en ejecución |
| FastAPI | `fastapi_app/app/data/pedido.py` | Agregar columnas al modelo SQLAlchemy |
| FastAPI | `fastapi_app/app/models/pedidos.py` | Agregar campos a `Crear_Pedido` Pydantic |
| FastAPI | `fastapi_app/app/routers/pedidos.py` | Persistir y retornar en `crear()` y `consultar_uno()` |
| Laravel | `laravel_app/app/Http/Controllers/CarritoController.php` | Leer `shipping` y `notes` del request |
| Laravel | `laravel_app/app/Http/Services/PedidosService.php` | Agregar campos al POST body |
| Laravel | `laravel_app/resources/views/pedido-detalle.blade.php` | Mostrar debajo de la tabla de artículos |
| Flask | `flask_app/templates/detalle_pedido.html` | Mostrar en card debajo de artículos del pedido |

---

## Sección 1: Base de Datos

### Columnas nuevas en `tb_pedidos`

```sql
metodo_envio  VARCHAR(30)  NOT NULL DEFAULT 'estandar'
notas         TEXT
```

**Valores válidos de `metodo_envio`:** `'express'`, `'estandar'`, `'recoger'`

**Razonamiento del DEFAULT:** Los pedidos históricos no capturaron método de envío. El valor `'estandar'` es neutral y no rompe ninguna validación futura.

**`notas`** es nullable — el cliente puede no dejar ninguna.

### `ddl.sql`

Actualizar `CREATE TABLE tb_pedidos` para incluir las dos columnas antes del campo `creado_en`.

### `migration_envio_notas.sql` (archivo nuevo)

```sql
ALTER TABLE tb_pedidos
    ADD COLUMN IF NOT EXISTS metodo_envio VARCHAR(30) NOT NULL DEFAULT 'estandar',
    ADD COLUMN IF NOT EXISTS notas TEXT;
```

Se ejecuta una sola vez sobre la BD en ejecución dentro del contenedor Docker.

---

## Sección 2: FastAPI

### `data/pedido.py` — Modelo SQLAlchemy

Agregar al modelo `Pedido`:

```python
metodo_envio = Column(String(30), nullable=False, default="estandar")
notas        = Column(Text, nullable=True)
```

### `models/pedidos.py` — Pydantic

Agregar a `Crear_Pedido`:

```python
metodo_envio: str           = Field("estandar", pattern="^(express|estandar|recoger)$")
notas:        Optional[str] = None
```

- `metodo_envio` tiene default `"estandar"` para mantener retrocompatibilidad con llamadas existentes.
- El `pattern` rechaza valores fuera de los 3 permitidos con `422 Unprocessable Entity`.

### `routers/pedidos.py` — Endpoints

**`POST /` (`crear`):** guardar `metodo_envio` y `notas` en el objeto `Pedido`:

```python
nuevo_pedido = Pedido(
    ...campos existentes...,
    metodo_envio = data.metodo_envio,
    notas        = data.notas,
)
```

**`GET /{pedido_id}` (`consultar_uno`):** incluir en el dict de respuesta:

```python
"metodo_envio": pedido.metodo_envio,
"notas":        pedido.notas,
```

**`GET /` y `GET /usuario/{id}`:** sin cambios — el listado no necesita estos campos.

---

## Sección 3: Laravel

### `CarritoController.php` — método `checkout()`

Leer los nuevos campos del request (sin agregar validación — ambos son opcionales desde el form):

```php
$metodoEnvio = $request->input('shipping', 'estandar');
$notas       = $request->input('notes');
```

Pasar al servicio junto con los argumentos existentes.

### `PedidosService.php` — método `crear()`

Agregar 2 parámetros y enviarlos en el body:

```php
public function crear(int $usuarioId, array $items, array $direccion, string $metodoPago, string $metodoEnvio, ?string $notas): array
{
    return $this->client->post('/v1/pedidos/', [
        ...campos existentes...,
        'metodo_envio' => $metodoEnvio,
        'notas'        => $notas,
    ]);
}
```

### `checkout.blade.php`

**Sin cambios.** El form ya tiene `name="shipping"` y `name="notes"` definidos correctamente.

### `pedido-detalle.blade.php`

Agregar una sección debajo de la tabla de artículos del pedido. Muestra:
- **Método de envío:** valor de `$pedido['metodo_envio']` en texto plano
- **Notas:** valor de `$pedido['notas']` si existe; la fila no se renderiza si es null/vacío

Mismo estilo visual que el resto de la vista. Sin íconos ni etiquetas decorativas adicionales.

---

## Sección 4: Flask

### `detalle_pedido.html`

Agregar una nueva `detail-card` en la columna izquierda (`detail-left`), inmediatamente debajo de la card de "Artículos del Pedido". Sigue el patrón `detail-card` + `card-header` + `card-body` + `info-row` ya existente en la plantilla.

Muestra:
- **Método de envío:** `pedido.metodo_envio` en texto plano
- **Notas:** `pedido.notas` si existe, o `—` si es null/vacío

La API retorna estos campos en `GET /v1/pedidos/{id}` (tras los cambios de FastAPI). Flask los recibe en el dict `pedido` que ya se pasa a la template — **no requiere cambios en `app.py`**.

---

## Flujo Completo

```
Cliente en checkout
  └─ Selecciona metodo_envio + escribe notas
       └─ POST form → CarritoController::checkout()
            └─ Lee shipping + notes del request
                 └─ PedidosService::crear() → POST /v1/pedidos/
                      └─ FastAPI persiste en tb_pedidos
                           └─ GET /v1/pedidos/{id}
                                ├─ Laravel pedido-detalle.blade.php → cliente ve sus datos
                                └─ Flask detalle_pedido.html → operador interno ve los datos
```

---

## Restricciones

- Sin endpoints nuevos — todo fluye por los existentes.
- Sin cambios en `app.py` de Flask.
- Sin cambios en `checkout.blade.php`.
- Los endpoints `GET /` y `GET /usuario/{id}` de pedidos no cambian.
- Retrocompatible: `metodo_envio` tiene default, `notas` es nullable.
