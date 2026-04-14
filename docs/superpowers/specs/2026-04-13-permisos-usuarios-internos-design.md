# Diseño: Sistema de Permisos — Usuarios Internos

**Fecha:** 2026-04-13
**Proyecto:** Macuin — Panel Interno Flask
**Estado:** Aprobado

---

## Contexto

El panel interno Flask tiene un formulario de crear/editar usuarios internos con 5 toggles de permisos (`perm_autopartes`, `perm_pedidos`, `perm_usuarios`, `perm_reportes`, `perm_config`). Actualmente:

- En **crear**: los toggles no se envían a la API (el modelo Pydantic `Crear_UsuarioInterno` no los acepta).
- En **editar**: los checkboxes están hardcodeados en el HTML, no reflejan los valores reales de la BD.
- No existe ningún guard de permisos en Flask — cualquier usuario autenticado accede a todo.

---

## Objetivo

1. Hacer los toggles reactivos al rol seleccionado (JS auto-set con defaults por rol).
2. Corregir el flujo de crear y editar para que los permisos se persistan y lean correctamente.
3. Agregar guards de permisos en Flask que bloqueen el acceso a secciones no autorizadas.

---

## Sección 1: Formulario Crear/Editar — Toggles reactivos al rol

### Defaults por rol

| Rol | perm_autopartes | perm_pedidos | perm_usuarios | perm_reportes | perm_config |
|-----|----------------|--------------|---------------|---------------|-------------|
| `admin` | ✓ | ✓ | ✓ | ✓ | ✓ |
| `ventas` | — | ✓ | — | ✓ | — |
| `almacen` | ✓ | ✓ | — | — | — |
| `logistica` | — | ✓ | — | — | — |

### Comportamiento JS (agregar_usuario_interno.html)

- Listener `change` en `<select name="rol">`.
- Al cambiar el rol, se setean los checkboxes según la tabla anterior.
- Los toggles permanecen editables manualmente después del auto-set.
- Si el select vuelve a `""` (sin seleccionar), todos los toggles se apagan.

### Fix formulario editar (editar_usuario_interno.html)

- Los checkboxes leen `usuario.perm_autopartes`, `usuario.perm_pedidos`, etc. desde el objeto pasado por Flask.
- Ejemplo: `{% if usuario.perm_autopartes %}checked{% endif %}`.
- El mismo listener JS aplica para auto-set al cambiar el rol en edición.

### Fix FastAPI — Crear_UsuarioInterno

Agregar los 5 permisos como opcionales con defaults por rol al modelo `Crear_UsuarioInterno` en `fastapi_app/app/models/usuarios_internos.py`:

```python
perm_autopartes: Optional[bool] = None
perm_pedidos:    Optional[bool] = None
perm_usuarios:   Optional[bool] = None
perm_reportes:   Optional[bool] = None
perm_config:     Optional[bool] = None
```

En el router `fastapi_app/app/routers/usuarios_internos.py`, al crear, si los permisos vienen como `None`, aplicar los defaults según el `rol` recibido. Esto garantiza que aunque el frontend no envíe permisos, la BD quede con valores coherentes.

### Fix Flask — envío de checkboxes

En `app.py`, la ruta `agregar_usuario_interno_submit` convierte checkboxes a booleanos antes de enviar a la API, ya que los checkboxes HTML solo se incluyen en el `form` cuando están marcados:

```python
for perm in ["perm_autopartes", "perm_pedidos", "perm_usuarios", "perm_reportes", "perm_config"]:
    data[perm] = perm in request.form
```

Lo mismo aplica en `editar_usuario_interno_submit`.

---

## Sección 2: Guards de permisos en Flask

### Nueva función `requiere_permiso`

Se agrega en `app.py` junto a `requiere_sesion()`:

```python
def requiere_permiso(clave):
    r = requiere_sesion()
    if r: return r
    if not session["usuario"].get(clave):
        return render_template("sin_permisos.html"), 403
```

### Aplicación por ruta

| Rutas afectadas | Permiso requerido |
|-----------------|-------------------|
| `/gestion-autopartes`, `/agregar-autoparte`, `/editar-autoparte/*`, `/eliminar-autoparte/*` | `perm_autopartes` |
| `/gestion-pedidos`, `/detalle-pedido/*`, `/cambiar-estado-pedido/*` | `perm_pedidos` |
| `/gestion-usuarios-internos`, `/agregar-usuario-interno`, `/editar-usuario-interno/*`, `/eliminar-usuario-interno/*`, `/gestion-usuarios-externos`, `/agregar-usuario-externo`, `/editar-usuario-externo/*`, `/eliminar-usuario-externo/*` | `perm_usuarios` |
| `/reportes`, `/descargar-reporte` | `perm_reportes` |

El `/perfil` no requiere permiso especial — solo sesión activa.

### Nueva vista sin_permisos.html

Página con diseño MACUIN que muestra:
- Ícono de candado (`fas fa-lock`)
- Título: "Acceso restringido"
- Mensaje: "No tienes permisos para acceder a esta sección. Contacta a un administrador."
- Botón "Regresar" que lleva a `/gestion-autopartes` (primera sección disponible)

---

## Archivos a modificar

| Archivo | Cambio |
|---------|--------|
| `fastapi_app/app/models/usuarios_internos.py` | Agregar 5 `perm_*` opcionales a `Crear_UsuarioInterno` |
| `fastapi_app/app/routers/usuarios_internos.py` | Aplicar defaults de permisos según rol al crear |
| `flask_app/app.py` | Agregar `requiere_permiso()`, aplicar guards, convertir checkboxes a bool |
| `flask_app/templates/agregar_usuario_interno.html` | Agregar JS listener de rol → toggles |
| `flask_app/templates/editar_usuario_interno.html` | Leer `perm_*` reales + JS listener |
| `flask_app/templates/sin_permisos.html` | Nueva vista 403 con diseño MACUIN |

---

## Restricciones

- No se usa ninguna dependencia nueva.
- El JS es vanilla (sin librerías).
- Los permisos se leen de `session["usuario"]` que ya los almacena desde el login.
- `perm_config` existe en la BD pero no tiene rutas que lo usen actualmente — se incluye en los toggles pero no se guarda ningún guard para esa clave por ahora.
