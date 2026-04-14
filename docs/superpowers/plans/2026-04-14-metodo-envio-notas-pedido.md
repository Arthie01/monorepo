# Método de Envío y Notas del Pedido — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Persistir el método de envío y las notas del pedido capturados en el checkout de Laravel, y mostrarlos en el detalle del pedido en ambos portales (Laravel y Flask).

**Architecture:** Se agregan 2 columnas (`metodo_envio`, `notas`) a `tb_pedidos` vía `ALTER TABLE`. FastAPI persiste y retorna los nuevos campos. El controlador Laravel lee los campos del form y los envía a la API. Ambas vistas de detalle de pedido los renderizan debajo de la tabla de artículos.

**Tech Stack:** PostgreSQL 15, FastAPI + SQLAlchemy + Pydantic, Laravel 12 + Blade, Flask + Jinja2, Docker Compose.

---

## Mapa de archivos

| Acción | Archivo |
|--------|---------|
| Modificar | `fastapi_app/app/data/ddl.sql` |
| Crear | `fastapi_app/app/data/migration_envio_notas.sql` |
| Modificar | `fastapi_app/app/data/pedido.py` |
| Modificar | `fastapi_app/app/models/pedidos.py` |
| Modificar | `fastapi_app/app/routers/pedidos.py` |
| Modificar | `laravel_app/app/Http/Controllers/CarritoController.php` |
| Modificar | `laravel_app/app/Http/Services/PedidosService.php` |
| Modificar | `laravel_app/resources/views/pedido-detalle.blade.php` |
| Modificar | `flask_app/templates/detalle_pedido.html` |

---

## Task 1: Actualizar esquema de BD

**Files:**
- Modify: `fastapi_app/app/data/ddl.sql`
- Create: `fastapi_app/app/data/migration_envio_notas.sql`

- [ ] **Step 1: Agregar columnas al DDL**

En `fastapi_app/app/data/ddl.sql`, dentro de `CREATE TABLE IF NOT EXISTS tb_pedidos`, agregar las 2 columnas nuevas **antes** de `creado_en TIMESTAMP`:

```sql
    -- Logística del pedido
    metodo_envio        VARCHAR(30)     NOT NULL DEFAULT 'estandar',
    -- Valores: 'express', 'estandar', 'recoger'
    notas               TEXT,
```

El bloque final de `tb_pedidos` debe quedar así:

```sql
    dir_calle           VARCHAR(200),
    dir_ciudad          VARCHAR(100),
    dir_estado          VARCHAR(5),
    dir_cp              VARCHAR(5),

    -- Logística del pedido
    metodo_envio        VARCHAR(30)     NOT NULL DEFAULT 'estandar',
    -- Valores: 'express', 'estandar', 'recoger'
    notas               TEXT,

    creado_en           TIMESTAMP       NOT NULL DEFAULT NOW()
);
```

- [ ] **Step 2: Crear el script de migración**

Crear el archivo `fastapi_app/app/data/migration_envio_notas.sql` con este contenido:

```sql
-- Migración: agregar metodo_envio y notas a tb_pedidos
-- Ejecutar una sola vez sobre la BD en ejecución.
-- Los pedidos existentes quedan con metodo_envio = 'estandar' y notas = NULL.

ALTER TABLE tb_pedidos
    ADD COLUMN IF NOT EXISTS metodo_envio VARCHAR(30) NOT NULL DEFAULT 'estandar',
    ADD COLUMN IF NOT EXISTS notas TEXT;
```

- [ ] **Step 3: Ejecutar la migración en Docker**

```bash
docker exec -i macuin-postgres-1 psql -U postgres -d macuin < fastapi_app/app/data/migration_envio_notas.sql
```

Si el contenedor tiene nombre diferente, verificar con:
```bash
docker ps --format "table {{.Names}}\t{{.Image}}" | grep postgres
```

- [ ] **Step 4: Verificar las columnas nuevas**

```bash
docker exec -it macuin-postgres-1 psql -U postgres -d macuin -c "\d tb_pedidos"
```

Resultado esperado: `metodo_envio` y `notas` aparecen en la lista de columnas de `tb_pedidos`.

- [ ] **Step 5: Commit**

```bash
git add fastapi_app/app/data/ddl.sql fastapi_app/app/data/migration_envio_notas.sql
git commit -m "feat: agregar metodo_envio y notas a tb_pedidos"
```

---

## Task 2: Actualizar modelos FastAPI (SQLAlchemy + Pydantic)

**Files:**
- Modify: `fastapi_app/app/data/pedido.py`
- Modify: `fastapi_app/app/models/pedidos.py`

- [ ] **Step 1: Agregar columnas al modelo SQLAlchemy**

En `fastapi_app/app/data/pedido.py`, agregar `Text` al import y las 2 columnas nuevas después de `dir_cp`:

```python
from sqlalchemy import Column, Integer, String, Numeric, ForeignKey, TIMESTAMP, Text
from sqlalchemy.sql import func
from app.data.db import Base


class Pedido(Base):
    __tablename__ = "tb_pedidos"

    id                 = Column(Integer, primary_key=True, index=True)
    folio              = Column(String(20), unique=True)

    usuario_externo_id = Column(Integer, ForeignKey("tb_usuarios_externos.id"), nullable=False)
    usuario_interno_id = Column(Integer, ForeignKey("tb_usuarios_internos.id"))

    estado             = Column(String(20), nullable=False, default="Pendiente")

    subtotal           = Column(Numeric(10, 2), nullable=False, default=0)
    envio              = Column(Numeric(10, 2), nullable=False, default=0)
    impuestos          = Column(Numeric(10, 2), nullable=False, default=0)
    total              = Column(Numeric(10, 2), nullable=False, default=0)

    dir_calle          = Column(String(200))
    dir_ciudad         = Column(String(100))
    dir_estado         = Column(String(5))
    dir_cp             = Column(String(5))

    metodo_envio       = Column(String(30), nullable=False, default="estandar")
    notas              = Column(Text, nullable=True)

    creado_en          = Column(TIMESTAMP, nullable=False, server_default=func.now())
```

- [ ] **Step 2: Agregar campos al modelo Pydantic**

En `fastapi_app/app/models/pedidos.py`, agregar `metodo_envio` y `notas` a `Crear_Pedido`:

```python
from pydantic import BaseModel, Field
from typing import Optional, List


class ItemPedido(BaseModel):
    autoparte_id: int = Field(..., gt=0)
    cantidad:     int = Field(..., ge=1)


class Crear_Pedido(BaseModel):
    usuario_externo_id: int              = Field(..., gt=0)
    metodo_pago:        str              = Field(..., pattern="^(tarjeta|transferencia|credito_macuin)$")
    metodo_envio:       str              = Field("estandar", pattern="^(express|estandar|recoger)$")
    notas:              Optional[str]    = None
    items:              List[ItemPedido] = Field(..., min_length=1)
    dir_calle:          Optional[str]    = None
    dir_ciudad:         Optional[str]    = None
    dir_estado:         Optional[str]    = None
    dir_cp:             Optional[str]    = None


class CambiarEstado(BaseModel):
    estado: str = Field(...)
```

- [ ] **Step 3: Commit**

```bash
git add fastapi_app/app/data/pedido.py fastapi_app/app/models/pedidos.py
git commit -m "feat: agregar metodo_envio y notas a modelos FastAPI"
```

---

## Task 3: Actualizar router de pedidos en FastAPI

**Files:**
- Modify: `fastapi_app/app/routers/pedidos.py`

- [ ] **Step 1: Persistir los nuevos campos en `crear()`**

En `fastapi_app/app/routers/pedidos.py`, dentro del endpoint `POST /` (`crear`), actualizar la creación del objeto `Pedido` (alrededor de la línea 209) para incluir `metodo_envio` y `notas`:

```python
    nuevo_pedido = Pedido(
        folio              = folio,
        usuario_externo_id = data.usuario_externo_id,
        estado             = "Pendiente",
        subtotal           = subtotal_final,
        impuestos          = impuestos,
        envio              = envio,
        total              = total,
        dir_calle          = data.dir_calle,
        dir_ciudad         = data.dir_ciudad,
        dir_estado         = data.dir_estado,
        dir_cp             = data.dir_cp,
        metodo_envio       = data.metodo_envio,
        notas              = data.notas,
    )
```

- [ ] **Step 2: Retornar los nuevos campos en `consultar_uno()`**

En el endpoint `GET /{pedido_id}` (`consultar_uno`), dentro del `return`, agregar los dos campos al dict del pedido:

```python
    return {
        "status": "200",
        "data": {
            "id":                pedido.id,
            "folio":             pedido.folio,
            "usuario_externo_id": pedido.usuario_externo_id,
            "estado":            pedido.estado,
            "subtotal":          float(pedido.subtotal),
            "impuestos":         float(pedido.impuestos),
            "envio":             float(pedido.envio),
            "total":             float(pedido.total),
            "dir_calle":         pedido.dir_calle,
            "dir_ciudad":        pedido.dir_ciudad,
            "dir_estado":        pedido.dir_estado,
            "dir_cp":            pedido.dir_cp,
            "metodo_envio":      pedido.metodo_envio,
            "notas":             pedido.notas,
            "creado_en":         pedido.creado_en,
            "items":             items
        }
    }
```

- [ ] **Step 3: Reiniciar el contenedor FastAPI**

```bash
docker restart macuin-fastapi-1
```

Verificar que levantó sin errores:

```bash
docker logs macuin-fastapi-1 --tail 20
```

Resultado esperado: `Application startup complete.` sin traceback.

- [ ] **Step 4: Verificar el endpoint con curl**

Crear un pedido de prueba (ajustar `usuario_externo_id` y `autoparte_id` a IDs que existan en tu BD):

```bash
curl -s -X POST http://localhost:8001/v1/pedidos/ \
  -H "Content-Type: application/json" \
  -d '{
    "usuario_externo_id": 1,
    "metodo_pago": "tarjeta",
    "metodo_envio": "express",
    "notas": "Entregar antes de las 2pm",
    "items": [{"autoparte_id": 1, "cantidad": 1}]
  }' | python -m json.tool
```

Resultado esperado: `"status": "201"` con el folio del pedido.

Luego consultar el detalle con el ID retornado (ej. si el id es 10):

```bash
curl -s http://localhost:8001/v1/pedidos/10 | python -m json.tool
```

Resultado esperado: el campo `"metodo_envio": "express"` y `"notas": "Entregar antes de las 2pm"` presentes en `data`.

- [ ] **Step 5: Commit**

```bash
git add fastapi_app/app/routers/pedidos.py
git commit -m "feat: persistir y retornar metodo_envio y notas en pedidos"
```

---

## Task 4: Actualizar capa Laravel (controlador + servicio)

**Files:**
- Modify: `laravel_app/app/Http/Controllers/CarritoController.php`
- Modify: `laravel_app/app/Http/Services/PedidosService.php`

- [ ] **Step 1: Leer los nuevos campos en el controlador**

En `laravel_app/app/Http/Controllers/CarritoController.php`, dentro del método `checkout()`, agregar la lectura de `shipping` y `notes` después de la línea que lee `$metodoPago`:

```php
        $direccion  = $request->only(['calle', 'ciudad', 'estado', 'cp']);
        $usuarioId  = session('usuario.id');
        $metodoPago = $request->input('metodo_pago');
        $metodoEnvio = $request->input('shipping', 'estandar');
        $notas       = $request->input('notes');
```

Y actualizar la llamada al servicio para pasar los nuevos parámetros:

```php
        try {
            $this->pedidosService->crear($usuarioId, $items, $direccion, $metodoPago, $metodoEnvio, $notas);
            session()->forget('carrito');
            return redirect('/pedidos')->with('success', 'Pedido realizado exitosamente.');
        } catch (ApiException $e) {
            return back()->withErrors(['api' => $e->getMessage()])->withInput();
        }
```

- [ ] **Step 2: Actualizar la firma y body del servicio**

En `laravel_app/app/Http/Services/PedidosService.php`, reemplazar el método `crear()` completo:

```php
    /**
     * Crea un pedido.
     * $usuarioId:   id del cliente externo logueado.
     * $items:       [['autoparte_id' => int, 'cantidad' => int], ...]
     * $direccion:   ['calle' => str, 'ciudad' => str, 'estado' => str, 'cp' => str]
     * $metodoPago:  'tarjeta' | 'transferencia' | 'credito_macuin'
     * $metodoEnvio: 'express' | 'estandar' | 'recoger'
     * $notas:       string|null
     */
    public function crear(int $usuarioId, array $items, array $direccion, string $metodoPago, string $metodoEnvio = 'estandar', ?string $notas = null): array
    {
        return $this->client->post('/v1/pedidos/', [
            'usuario_externo_id' => $usuarioId,
            'metodo_pago'        => $metodoPago,
            'metodo_envio'       => $metodoEnvio,
            'notas'              => $notas,
            'items'              => $items,
            'dir_calle'          => $direccion['calle']  ?? '',
            'dir_ciudad'         => $direccion['ciudad'] ?? '',
            'dir_estado'         => $direccion['estado'] ?? '',
            'dir_cp'             => $direccion['cp']     ?? '',
        ]);
    }
```

- [ ] **Step 3: Commit**

```bash
git add laravel_app/app/Http/Controllers/CarritoController.php laravel_app/app/Http/Services/PedidosService.php
git commit -m "feat: enviar metodo_envio y notas desde checkout Laravel a la API"
```

---

## Task 5: Mostrar datos en pedido-detalle.blade.php (Laravel)

**Files:**
- Modify: `laravel_app/resources/views/pedido-detalle.blade.php`

- [ ] **Step 1: Agregar sección debajo de la tabla de artículos**

En `laravel_app/resources/views/pedido-detalle.blade.php`, después del cierre del div de la tabla de artículos (línea `</div>` que cierra el `background:#fff;border:1px solid...` de la tabla, alrededor de la línea 157), agregar:

```blade
                {{-- Sección: Método de envío y notas --}}
                @if(!empty($pedido['metodo_envio']) || !empty($pedido['notas']))
                <div style="background:#fff;border:1px solid var(--macuin-gray);border-radius:8px;padding:20px;margin-top:20px;">
                    @if(!empty($pedido['metodo_envio']))
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--macuin-gray);">
                        <span style="font-size:13px;color:var(--macuin-muted);">Método de envío</span>
                        <span style="font-size:13px;font-weight:600;color:var(--macuin-text);">{{ $pedido['metodo_envio'] }}</span>
                    </div>
                    @endif
                    @if(!empty($pedido['notas']))
                    <div style="padding-top:12px;">
                        <div style="font-size:13px;color:var(--macuin-muted);margin-bottom:6px;">Notas del pedido</div>
                        <div style="font-size:13px;color:var(--macuin-text);line-height:1.6;">{{ $pedido['notas'] }}</div>
                    </div>
                    @endif
                </div>
                @endif
```

El bloque se inserta entre el cierre de la tabla de artículos y el cierre del div de la columna principal (`</div>` que cierra `{{-- Columna principal --}}`).

- [ ] **Step 2: Commit**

```bash
git add laravel_app/resources/views/pedido-detalle.blade.php
git commit -m "feat: mostrar metodo_envio y notas en detalle de pedido Laravel"
```

---

## Task 6: Mostrar datos en detalle_pedido.html (Flask)

**Files:**
- Modify: `flask_app/templates/detalle_pedido.html`

- [ ] **Step 1: Agregar card debajo de "Productos del Pedido"**

En `flask_app/templates/detalle_pedido.html`, después del cierre del `div.detail-card` de "Productos del Pedido" (la que contiene `items-table`, cierra con `</div>` alrededor de la línea 196), agregar dentro de `detail-left`:

```html
            <!-- Card: Método de envío y notas -->
            <div class="detail-card">
              <div class="card-header">
                <h2 class="card-header__title">
                  <i class="fas fa-truck"></i> Envío y Notas
                </h2>
              </div>
              <div class="card-body">
                <div class="info-row">
                  <i class="fas fa-truck"></i>
                  <div>
                    <div class="info-label">Método de envío</div>
                    <div class="info-value">{{ pedido.metodo_envio or '—' }}</div>
                  </div>
                </div>
                <div class="info-row">
                  <i class="fas fa-sticky-note"></i>
                  <div>
                    <div class="info-label">Notas del pedido</div>
                    <div class="info-value">{{ pedido.notas or '—' }}</div>
                  </div>
                </div>
              </div>
            </div>
```

- [ ] **Step 2: Commit**

```bash
git add flask_app/templates/detalle_pedido.html
git commit -m "feat: mostrar metodo_envio y notas en detalle de pedido Flask"
```

---

## Task 7: Verificación end-to-end

- [ ] **Step 1: Levantar todos los servicios**

```bash
docker compose up -d
```

Verificar que los 4 contenedores están `Up`:

```bash
docker compose ps
```

- [ ] **Step 2: Realizar un pedido completo desde el checkout de Laravel**

1. Iniciar sesión en `http://localhost:8090/login` con un usuario externo existente.
2. Agregar al menos un producto al carrito desde `/catalogo`.
3. Ir a `/checkout`.
4. En la sección "Método de Envío" seleccionar **Envío Express (24 hrs)**.
5. En "Notas del Pedido" escribir: `Dejar en recepción`.
6. Completar dirección, seleccionar método de pago y confirmar pedido.
7. Verificar que redirige a `/pedidos` con mensaje de éxito.

- [ ] **Step 3: Verificar en el portal del cliente (Laravel)**

Ir a `/pedidos`, hacer clic en el pedido recién creado. Confirmar que debajo de la tabla de artículos aparece:
- Método de envío: `express`
- Notas del pedido: `Dejar en recepción`

- [ ] **Step 4: Verificar en el portal interno (Flask)**

En `http://localhost:5000/gestion-pedidos`, buscar el pedido creado y abrir su detalle. Confirmar que la card "Envío y Notas" muestra los mismos valores.

- [ ] **Step 5: Verificar directamente en la API**

```bash
curl -s http://localhost:8001/v1/pedidos/{ID_DEL_PEDIDO} | python -m json.tool
```

Reemplazar `{ID_DEL_PEDIDO}` con el ID del pedido creado. Confirmar que `metodo_envio` y `notas` aparecen en el campo `data`.
