# Plan de Acción — MACUIN 3er Parcial

## Estado General
| Componente | Estado |
|------------|--------|
| Laravel (Frontend Externo) | ✅ Terminado — 10 vistas Blade estáticas |
| Flask (Panel Interno) | ✅ Terminado — 14 vistas Jinja2 estáticas |
| FastAPI (API Central) | 🔄 En progreso — M1+M2+M3 completos, faltan pedidos/reportes |
| PostgreSQL (Base de Datos) | ✅ Lista — ddl.sql + dml.sql + modelos SQLAlchemy |
| Docker (Todos los servicios) | ✅ Completo — 4 servicios con healthcheck en docker-compose.yml |
| Integración Frontends ↔ API | ⬜ Por conectar |

---

## Arquitectura Final Objetivo

```
docker-compose.yml
├── laravel   → http://localhost:8080  (Frontend externo)
├── flask     → http://localhost:5001  (Panel interno)
├── fastapi   → http://localhost:8000  (API Central)
└── postgres  → localhost:5432         (Base de datos)
```

Patrón de carpetas FastAPI — **igual que miAPI**:
```
fastapi_app/
├── requirements.txt
├── dockerfile
└── app/
    ├── main.py
    ├── data/
    │   ├── db.py                  ← Conexión SQLAlchemy + get_db()
    │   ├── ddl.sql                ← DDL PostgreSQL (CREATE TABLE)
    │   ├── dml.sql                ← DML PostgreSQL (INSERT datos prueba)
    │   ├── usuario_externo.py     ← Modelo SQLAlchemy tb_usuarios_externos
    │   ├── usuario_interno.py     ← Modelo SQLAlchemy tb_usuarios_internos
    │   ├── autoparte.py           ← Modelo SQLAlchemy tb_autopartes
    │   ├── pedido.py              ← Modelo SQLAlchemy tb_pedidos
    │   └── detalle_pedido.py      ← Modelo SQLAlchemy tb_detalle_pedido
    ├── models/
    │   ├── usuarios.py            ← Pydantic: Crear_UsuarioExterno, Actualizar_UsuarioExterno
    │   ├── usuarios_internos.py   ← Pydantic: Crear_UsuarioInterno, Actualizar_UsuarioInterno
    │   ├── autopartes.py          ← Pydantic: Crear_Autoparte, Actualizar_Autoparte
    │   └── pedidos.py             ← Pydantic: Crear_Pedido (con lista de productos)
    ├── routers/
    │   ├── auth.py                ← POST /v1/auth/registro
    │   ├── usuarios_internos.py   ← CRUD /v1/usuarios-internos
    │   ├── autopartes.py          ← CRUD /v1/autopartes
    │   ├── pedidos.py             ← POST+GET /v1/pedidos
    │   └── reportes.py            ← GET /v1/reportes/{tipo}/{formato}
    └── security/
        └── auth.py                ← HTTPBasic (mismo patrón que miAPI)
```

---

## Milestone 1 — Base FastAPI + Docker + BD
> **Objetivo**: API levantando con Docker, conectada a PostgreSQL, tablas creadas automáticamente.

- [ ] Crear `fastapi_app/requirements.txt`
- [ ] Crear `fastapi_app/dockerfile` (igual que miAPI, puerto 8000)
- [ ] Crear `fastapi_app/app/data/db.py` (ENGINE + sessionLocal + Base + get_db)
- [x] ~~Crear `fastapi_app/app/data/ddl.sql`~~ ✅ **HECHO** — 5 tablas PostgreSQL completas con todos los campos del frontend
- [x] ~~Crear `fastapi_app/app/data/dml.sql`~~ ✅ **HECHO** — 8 internos, 8 externos, 18 autopartes, 6 pedidos con líneas
- [ ] Crear modelos SQLAlchemy: `usuario_externo.py`, `usuario_interno.py`, `autoparte.py`, `pedido.py`, `detalle_pedido.py`
- [ ] Crear `fastapi_app/app/main.py` con `Base.metadata.create_all()`
- [ ] Actualizar `docker-compose.yml`: agregar servicios `fastapi` y `postgres` con healthcheck
- [ ] Verificar: `docker compose up --build` levanta los 4 servicios sin errores

**Rúbrica cubierta**: Criterios 1 (2 frontends), 3 (routers), 4 (SQLAlchemy), 5 (solo API accede BD), 6 (Docker)

---

## Milestone 2 — Autenticación + CRUD Usuarios Internos ✅ COMPLETADO
> **Objetivo**: Registro real de clientes + login independiente por portal + administración de personal interno.

- [x] ~~Crear `fastapi_app/app/security/auth.py`~~ ✅ — HTTPBasic `macuin`/`123456`
- [x] ~~Crear `fastapi_app/app/models/usuarios.py`~~ ✅ — Pydantic `Crear_UsuarioExterno`, `Actualizar_UsuarioExterno`
- [x] ~~Crear `fastapi_app/app/models/usuarios_internos.py`~~ ✅ — Pydantic `Crear_UsuarioInterno`, `Actualizar_UsuarioInterno`
- [x] ~~Crear `fastapi_app/app/routers/auth.py`~~ ✅ — 3 endpoints:
  - `POST /v1/auth/registro` — Crea usuario externo en BD (campos mínimos: nombre, apellidos, email, password)
  - `POST /v1/auth/login/externo` — Valida credenciales en `tb_usuarios_externos` (para Laravel)
  - `POST /v1/auth/login/interno` — Valida credenciales en `tb_usuarios_internos` (para Flask)
- [x] ~~Crear `fastapi_app/app/routers/usuarios_internos.py`~~ ✅ — CRUD completo:
  - `GET    /v1/usuarios/internos/`     — Listar todos
  - `GET    /v1/usuarios/internos/{id}` — Consultar uno
  - `POST   /v1/usuarios/internos/`     — Crear
  - `PUT    /v1/usuarios/internos/{id}` — Actualizar completo
  - `PATCH  /v1/usuarios/internos/{id}` — Actualizar parcial
  - `DELETE /v1/usuarios/internos/{id}` — Eliminar (requiere HTTPBasic `macuin`/`123456`)
- [x] ~~Registrar ambos routers en `main.py`~~ ✅
- [ ] Probar con Swagger UI en `http://localhost:8001/docs`

**Rúbrica cubierta**: Criterios 7 (registro), 10 (CRUD usuarios internos)

---

## Milestone 3 — CRUD Autopartes ✅ COMPLETADO
> **Objetivo**: Gestión completa del catálogo de autopartes desde la API.

- [x] ~~Crear `fastapi_app/app/models/autopartes.py`~~ ✅ — Pydantic `Crear_Autoparte`, `Actualizar_Autoparte`, `PatchAutoparte`
- [x] ~~Crear `fastapi_app/app/routers/autopartes.py`~~ ✅ — CRUD completo con upload de imagen:
  - `GET    /v1/autopartes/`       — Listar todas (con filtro `?categoria=` case-insensitive)
  - `GET    /v1/autopartes/{id}`   — Consultar una
  - `POST   /v1/autopartes/`       — Crear (multipart/form-data + imagen opcional)
  - `PUT    /v1/autopartes/{id}`   — Actualizar completa (multipart/form-data + imagen opcional)
  - `PATCH  /v1/autopartes/{id}`   — Actualizar parcial JSON: precio, stock, stock_minimo, estado, ubicacion
  - `DELETE /v1/autopartes/{id}`   — Eliminar (requiere HTTPBasic `macuin`/`123456`)
- [x] ~~Registrar router en `main.py`~~ ✅
- [x] ~~Agregar `python-multipart` a `requirements.txt`~~ ✅
- [x] ~~Agregar volumen `uploads` en `docker-compose.yml`~~ ✅ — imágenes en `fastapi_app/uploads/autopartes/`
- [x] ~~Montar `StaticFiles` en `/uploads` en `main.py`~~ ✅

**Rúbrica cubierta**: Criterio 11 (CRUD autopartes)

---

## Milestone 4 — Pedidos (1 a N productos)
> **Objetivo**: Crear pedidos con múltiples productos y consultarlos por usuario.

- [ ] Crear `fastapi_app/app/models/pedidos.py`:
  - `ItemPedido` — (autoparte_id, cantidad)
  - `Crear_Pedido` — (usuario_externo_id, lista de ItemPedido)
- [ ] Crear `fastapi_app/app/routers/pedidos.py`:
  - `POST /v1/pedidos/`                        — Crear pedido con N productos (calcula total automático)
  - `GET  /v1/pedidos/usuario/{usuario_id}`     — Todos los pedidos de un usuario
  - `GET  /v1/pedidos/{pedido_id}`              — Detalle de pedido (con líneas)
  - `PATCH /v1/pedidos/{pedido_id}/estado`      — Cambiar estado (Pendiente→En proceso→Completado)
- [ ] Registrar router en `main.py`

**Rúbrica cubierta**: Criterios 8 (pedidos 1-N), 9 (consultar pedidos usuario)

---

## Milestone 5 — Reportes (4 tipos + 3 formatos)
> **Objetivo**: Endpoints que generan reportes descargables en PDF, xlsx y docx.

- [ ] Crear `fastapi_app/app/routers/reportes.py` con 4 tipos de reporte:
  - `GET /v1/reportes/ventas/{formato}`      — Total de ventas por período
  - `GET /v1/reportes/inventario/{formato}`  — Stock actual de autopartes
  - `GET /v1/reportes/pedidos/{formato}`     — Pedidos por estado
  - `GET /v1/reportes/usuarios/{formato}`    — Usuarios externos registrados
  - `{formato}` acepta: `pdf`, `xlsx`, `docx`
- [ ] Usar `reportlab` para PDF, `openpyxl` para xlsx, `python-docx` para docx
- [ ] Registrar router en `main.py`

**Rúbrica cubierta**: Criterios 12 (4 reportes), 13 (PDF/xlsx/docx)

---

## Milestone 6 — Integración Frontends ↔ API
> **Objetivo**: Flask y Laravel dejan de usar datos hardcodeados y consumen la API.

### Flask (Panel Interno)
- [ ] Agregar `requests` a `flask_app/requirements.txt`
- [ ] Conectar `gestion_autopartes` → `GET /v1/autopartes/`
- [ ] Conectar `agregar_autoparte` → `POST /v1/autopartes/`
- [ ] Conectar `editar_autoparte` → `PUT /v1/autopartes/{id}`
- [ ] Conectar `gestion_usuarios_internos` → `GET /v1/usuarios-internos/`
- [ ] Conectar `gestion_pedidos` → `GET /v1/pedidos/`
- [ ] Conectar `reportes` → `GET /v1/reportes/{tipo}/{formato}`

### Laravel (Frontend Externo)
- [ ] Conectar `POST /registro` → `POST /v1/auth/registro`
- [ ] Conectar `GET /catalogo` → `GET /v1/autopartes/`
- [ ] Conectar `POST /checkout` → `POST /v1/pedidos/`
- [ ] Conectar `GET /pedidos` → `GET /v1/pedidos/usuario/{id}`

---

## Resumen de Progreso

| Milestone | Descripción | Rúbrica | Estado |
|-----------|-------------|---------|--------|
| 1 | Base FastAPI + Docker + BD | 1, 3, 4, 5, 6 | ✅ Completo |
| 2 | Registro + CRUD Usuarios Internos | 7, 10 | ✅ Completo |
| 3 | CRUD Autopartes | 11 | ✅ Completo |
| 4 | Pedidos (1 a N productos) | 8, 9 | ⬜ Siguiente |
| 5 | Reportes (4 tipos, 3 formatos) | 12, 13 | ⬜ |
| 6 | Integración Frontends ↔ API | 2 | ⬜ |

---

## Convenciones FastAPI (basadas en miAPI)

| Patrón | Ejemplo |
|--------|---------|
| Prefix de router | `/v1/autopartes` |
| Tag de router | `CRUD HTTP` o nombre del módulo |
| Respuesta estándar | `{"status": "200", "total": n, "data": [...]}` |
| Modelo SQLAlchemy | clase en `data/`, hereda de `Base`, tabla `tb_nombre` |
| Pydantic schema | clase `Crear_X` y `Actualizar_X` con `Field(...)` |
| Auth | HTTPBasic solo en DELETE — `Depends(verificar_peticion)` |
| DB session | `db: Session = Depends(get_db)` |
| Passwords | Texto plano — proyecto institucional |

---

## Archivos de Referencia

| Recurso | Ubicación | Para qué usarlo |
|---------|-----------|-----------------|
| **miAPI** | `C:\Users\Emiliano\Documents\UPQ_SISTEMAS\8vo_Cuatrimestre\Isay\TAI204\miAPI\` | Patrón exacto de estructura, convenciones y estilo de código |
| **SWAY POO** | `C:\Users\Emiliano\Videos\SWAY POO\` | Referencia de pedidos con N productos (`routers/pedidos.py`), reportes, docker-compose con healthcheck |
| **Rúbrica 3er Parcial** | `C:\Users\Emiliano\Downloads\Rubrica 3P.pdf` | 13 criterios de evaluación, 100 pts total |
| **SQL original (con errores)** | `C:\Users\Emiliano\Downloads\DBAUTOMACUIN - copia.sql` | Solo referencia — usar el ddl.sql corregido en fastapi_app/ |
| **CLAUDE.md** | Raíz del repo | Directrices, esquema BD, convenciones completas |
