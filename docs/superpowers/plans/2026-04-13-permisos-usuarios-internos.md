# Permisos Usuarios Internos — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Hacer los toggles de permisos funcionales al crear/editar usuarios internos, y bloquear el acceso a secciones del panel interno según el rol del usuario autenticado.

**Architecture:** FastAPI acepta permisos al crear usuario y aplica defaults por rol en el router. Flask convierte los checkboxes HTML a booleans antes de enviar a la API, y una nueva función `requiere_permiso(clave)` reemplaza `requiere_sesion()` en las rutas relevantes. Los templates leen los valores reales del usuario y auto-configuran los toggles con JS según el rol elegido.

**Tech Stack:** FastAPI (Pydantic v2, SQLAlchemy), Flask (Jinja2, sesiones), vanilla JS

---

## Task 1: FastAPI — agregar perm_* a Crear_UsuarioInterno y aplicar defaults en router

**Files:**
- Modify: `fastapi_app/app/models/usuarios_internos.py`
- Modify: `fastapi_app/app/routers/usuarios_internos.py`

- [ ] **Step 1: Actualizar el modelo Crear_UsuarioInterno**

En `fastapi_app/app/models/usuarios_internos.py`, reemplazar la clase `Crear_UsuarioInterno`:

```python
class Crear_UsuarioInterno(BaseModel):
    """POST — Crear usuario interno (todos los campos básicos requeridos)."""
    nombre:          str = Field(..., min_length=2, max_length=50,  description="Nombre del empleado")
    apellidos:       str = Field(..., min_length=2, max_length=100, description="Apellidos del empleado")
    email:           str = Field(..., min_length=5, max_length=120, description="Correo institucional único")
    password:        str = Field(..., min_length=4, max_length=255, description="Contraseña")
    departamento:    str = Field(..., max_length=30, description="Ej: Ventas, Almacén, Logística, Administración")
    rol:             str = Field(..., max_length=30, description="Ej: admin, ventas, almacen, logistica")
    perm_autopartes: Optional[bool] = None
    perm_pedidos:    Optional[bool] = None
    perm_usuarios:   Optional[bool] = None
    perm_reportes:   Optional[bool] = None
    perm_config:     Optional[bool] = None
```

Verificar que `Optional` ya está importado desde `typing` (línea 3 del archivo actual).

- [ ] **Step 2: Actualizar el endpoint POST en el router**

En `fastapi_app/app/routers/usuarios_internos.py`, reemplazar la función `crear` completa:

```python
@router.post("/", status_code=status.HTTP_201_CREATED)
async def crear(usuarioP: Crear_UsuarioInterno, db: Session = Depends(get_db)):
    existe = db.query(UsuarioInterno).filter(UsuarioInterno.email == usuarioP.email).first()
    if existe:
        raise HTTPException(status_code=400, detail="El email ya está registrado")

    # Defaults de permisos según rol si no se enviaron explícitamente
    defaults = {
        "admin":    dict(perm_autopartes=True,  perm_pedidos=True, perm_usuarios=True,  perm_reportes=True,  perm_config=True),
        "ventas":   dict(perm_autopartes=False, perm_pedidos=True, perm_usuarios=False, perm_reportes=True,  perm_config=False),
        "almacen":  dict(perm_autopartes=True,  perm_pedidos=True, perm_usuarios=False, perm_reportes=False, perm_config=False),
        "logistica":dict(perm_autopartes=False, perm_pedidos=True, perm_usuarios=False, perm_reportes=False, perm_config=False),
    }
    rol_key = usuarioP.rol.lower()
    d = defaults.get(rol_key, dict(perm_autopartes=False, perm_pedidos=False, perm_usuarios=False, perm_reportes=False, perm_config=False))

    nuevo = UsuarioInterno(
        nombre=usuarioP.nombre,
        apellidos=usuarioP.apellidos,
        email=usuarioP.email,
        password=usuarioP.password,
        departamento=usuarioP.departamento,
        rol=usuarioP.rol,
        perm_autopartes=usuarioP.perm_autopartes if usuarioP.perm_autopartes is not None else d["perm_autopartes"],
        perm_pedidos=   usuarioP.perm_pedidos    if usuarioP.perm_pedidos    is not None else d["perm_pedidos"],
        perm_usuarios=  usuarioP.perm_usuarios   if usuarioP.perm_usuarios   is not None else d["perm_usuarios"],
        perm_reportes=  usuarioP.perm_reportes   if usuarioP.perm_reportes   is not None else d["perm_reportes"],
        perm_config=    usuarioP.perm_config      if usuarioP.perm_config     is not None else d["perm_config"],
    )
    db.add(nuevo)
    db.commit()
    db.refresh(nuevo)
    return {
        "status": "201",
        "mensaje": "Usuario interno creado",
        "data":    nuevo
    }
```

- [ ] **Step 3: Reiniciar FastAPI y verificar**

```bash
docker compose restart fastapi
```

Probar con curl o Swagger UI en `http://localhost:8001/docs` → POST `/v1/usuarios/internos/` con rol `admin` sin enviar permisos → verificar que el usuario creado tenga todos los permisos en `true`.

- [ ] **Step 4: Commit**

```bash
git add fastapi_app/app/models/usuarios_internos.py fastapi_app/app/routers/usuarios_internos.py
git commit -m "feat: Crear_UsuarioInterno acepta perm_* con defaults por rol en FastAPI"
```

---

## Task 2: Flask — agregar requiere_permiso y aplicar guards en todas las rutas

**Files:**
- Modify: `flask_app/app.py`

- [ ] **Step 1: Agregar la función requiere_permiso después de requiere_sesion**

En `flask_app/app.py`, después de la función `requiere_sesion` (línea ~37), agregar:

```python
def requiere_permiso(clave):
    """Retorna redirect/render 403 si el usuario no tiene el permiso requerido."""
    r = requiere_sesion()
    if r: return r
    if not session["usuario"].get(clave):
        return render_template("sin_permisos.html"), 403
    return None
```

- [ ] **Step 2: Aplicar guards en rutas de Autopartes**

Reemplazar `requiere_sesion()` por `requiere_permiso("perm_autopartes")` en estas 5 funciones:
- `gestion_autopartes` (línea ~79)
- `agregar_autoparte_form` (línea ~90)
- `agregar_autoparte_submit` (línea ~97)
- `editar_autoparte_form` (línea ~110)
- `editar_autoparte_submit` (línea ~122)
- `eliminar_autoparte` (línea ~135)

Ejemplo de cómo queda cada función (misma estructura, solo cambia el guard):
```python
def gestion_autopartes():
    r = requiere_permiso("perm_autopartes")
    if r: return r
    # ... resto igual
```

- [ ] **Step 3: Aplicar guards en rutas de Pedidos**

Reemplazar `requiere_sesion()` por `requiere_permiso("perm_pedidos")` en:
- `gestion_pedidos` (línea ~149)
- `detalle_pedido` (línea ~161)
- `cambiar_estado_pedido` (línea ~174)

- [ ] **Step 4: Aplicar guards en rutas de Usuarios (internos y externos)**

Reemplazar `requiere_sesion()` por `requiere_permiso("perm_usuarios")` en:
- `gestion_usuarios_internos` (línea ~189)
- `agregar_usuario_interno_form` (línea ~209)
- `agregar_usuario_interno_submit` (línea ~216)
- `editar_usuario_interno_form` (línea ~230)
- `editar_usuario_interno_submit` (línea ~242)
- `eliminar_usuario_interno` (línea ~261)
- `gestion_usuarios_externos` (línea ~275)
- `agregar_usuario_externo_form` (línea ~293)
- `agregar_usuario_externo_submit` (línea ~300)
- `editar_usuario_externo_form` (línea ~314)
- `editar_usuario_externo_submit` (línea ~331)
- `eliminar_usuario_externo` (línea ~353)

- [ ] **Step 5: Aplicar guards en rutas de Reportes**

Reemplazar `requiere_sesion()` por `requiere_permiso("perm_reportes")` en:
- `reportes` (línea ~376)
- `descargar_reporte` (o el nombre exacto de la función de descarga que venga después)

- [ ] **Step 6: Verificar que /perfil conserva solo requiere_sesion**

La función `perfil` debe mantener `requiere_sesion()` sin cambios — el perfil es accesible para cualquier usuario autenticado independientemente de sus permisos.

- [ ] **Step 7: Commit**

```bash
git add flask_app/app.py
git commit -m "feat: agregar requiere_permiso y guards por sección en Flask"
```

---

## Task 3: Flask — convertir checkboxes a bool en crear y editar usuario interno

**Files:**
- Modify: `flask_app/app.py`

Los checkboxes HTML solo se incluyen en el `form` cuando están marcados. Sin esta conversión, los permisos desmarcados no se envían a la API y esta interpreta su ausencia de forma incorrecta.

- [ ] **Step 1: Actualizar agregar_usuario_interno_submit**

Reemplazar el cuerpo de la función `agregar_usuario_interno_submit` en `flask_app/app.py`:

```python
@app.route("/agregar-usuario-interno", methods=["POST"])
def agregar_usuario_interno_submit():
    r = requiere_permiso("perm_usuarios")
    if r: return r
    data = request.form.to_dict()
    for perm in ["perm_autopartes", "perm_pedidos", "perm_usuarios", "perm_reportes", "perm_config"]:
        data[perm] = perm in request.form
    try:
        UsuariosService.crear_interno(data)
        flash("Usuario interno creado.", "success")
        return redirect("/gestion-usuarios-internos")
    except ApiException as e:
        flash(e.detail, "error")
        return redirect("/agregar-usuario-interno")
```

- [ ] **Step 2: Actualizar editar_usuario_interno_submit**

Reemplazar el cuerpo de la función `editar_usuario_interno_submit`:

```python
@app.route("/editar-usuario-interno/<int:id>", methods=["POST"])
def editar_usuario_interno_submit(id):
    r = requiere_permiso("perm_usuarios")
    if r: return r
    data = request.form.to_dict()
    if "password" in data and not data["password"].strip():
        del data["password"]
    for perm in ["perm_autopartes", "perm_pedidos", "perm_usuarios", "perm_reportes", "perm_config"]:
        data[perm] = perm in request.form
    try:
        UsuariosService.editar_interno(id, data)
        flash("Usuario interno actualizado.", "success")
        return redirect("/gestion-usuarios-internos")
    except ApiException as e:
        flash(e.detail, "error")
        return redirect(f"/editar-usuario-interno/{id}")
```

- [ ] **Step 3: Commit**

```bash
git add flask_app/app.py
git commit -m "feat: convertir checkboxes de permisos a bool antes de enviar a FastAPI"
```

---

## Task 4: Crear la página sin_permisos.html

**Files:**
- Create: `flask_app/templates/sin_permisos.html`

- [ ] **Step 1: Crear el template**

Crear `flask_app/templates/sin_permisos.html` con el siguiente contenido:

```html
<!doctype html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Acceso restringido — Macuin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;600&family=DM+Sans:opsz,wght@9..40,400;9..40,500&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    />
    <style>
      *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
      body {
        font-family: 'DM Sans', sans-serif;
        background: #0D0D0D;
        color: #F5F5F0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1.5rem;
        text-align: center;
        padding: 2rem;
      }
      .lock-icon {
        font-size: 4rem;
        color: #C41230;
      }
      .title {
        font-family: 'Oswald', sans-serif;
        font-size: 2rem;
        font-weight: 600;
        letter-spacing: 0.02em;
      }
      .message {
        color: #8B949E;
        font-size: 1rem;
        max-width: 420px;
        line-height: 1.6;
      }
      .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #C41230;
        color: #fff;
        text-decoration: none;
        padding: 0.65rem 1.5rem;
        border-radius: 6px;
        font-family: 'DM Sans', sans-serif;
        font-weight: 500;
        font-size: 0.95rem;
        transition: background 0.2s;
      }
      .btn-back:hover { background: #8B0D21; }
    </style>
  </head>
  <body>
    <i class="fas fa-lock lock-icon"></i>
    <h1 class="title">Acceso restringido</h1>
    <p class="message">
      No tienes permisos para acceder a esta sección.<br />
      Contacta a un administrador si crees que esto es un error.
    </p>
    <a href="/perfil" class="btn-back">
      <i class="fas fa-arrow-left"></i>
      Regresar a mi perfil
    </a>
  </body>
</html>
```

- [ ] **Step 2: Verificar que Flask sirve correctamente un 403**

Iniciar sesión con un usuario sin `perm_reportes` → navegar a `/reportes` → debe aparecer la página de sin_permisos con código HTTP 403.

- [ ] **Step 3: Commit**

```bash
git add flask_app/templates/sin_permisos.html
git commit -m "feat: agregar página de acceso restringido (403) con diseño MACUIN"
```

---

## Task 5: Template agregar_usuario_interno.html — JS reactivo al rol

**Files:**
- Modify: `flask_app/templates/agregar_usuario_interno.html`

- [ ] **Step 1: Agregar id al select de rol**

En el `<select name="rol">` (línea ~227), agregar `id="fRol"` si no lo tiene ya:

```html
<select id="fRol" name="rol" class="field-select" required>
```

- [ ] **Step 2: Agregar ids a los checkboxes de permisos**

Agregar `id` a cada checkbox para que JS los localice:

```html
<input type="checkbox" id="permAutopartes" name="perm_autopartes" />
<input type="checkbox" id="permPedidos"    name="perm_pedidos" />
<input type="checkbox" id="permUsuarios"   name="perm_usuarios" />
<input type="checkbox" id="permReportes"   name="perm_reportes" />
<input type="checkbox" id="permConfig"     name="perm_config" />
```

- [ ] **Step 3: Agregar el bloque script antes de </body>**

Agregar antes del cierre `</body>`:

```html
<script>
  const PERMISOS_POR_ROL = {
    admin:    { permAutopartes: true,  permPedidos: true, permUsuarios: true,  permReportes: true,  permConfig: true },
    ventas:   { permAutopartes: false, permPedidos: true, permUsuarios: false, permReportes: true,  permConfig: false },
    almacen:  { permAutopartes: true,  permPedidos: true, permUsuarios: false, permReportes: false, permConfig: false },
    logistica:{ permAutopartes: false, permPedidos: true, permUsuarios: false, permReportes: false, permConfig: false },
  };

  document.getElementById('fRol').addEventListener('change', function () {
    const defaults = PERMISOS_POR_ROL[this.value];
    const ids = ['permAutopartes', 'permPedidos', 'permUsuarios', 'permReportes', 'permConfig'];
    ids.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.checked = defaults ? defaults[id] : false;
    });
  });
</script>
```

- [ ] **Step 4: Verificar manualmente**

Abrir `/agregar-usuario-interno` → seleccionar `Administrador` en el dropdown de rol → los 5 toggles deben activarse. Seleccionar `Ventas` → solo `pedidos` y `reportes` quedan activos.

- [ ] **Step 5: Commit**

```bash
git add flask_app/templates/agregar_usuario_interno.html
git commit -m "feat: toggles de permisos reactivos al rol al crear usuario interno"
```

---

## Task 6: Template editar_usuario_interno.html — valores reales + JS reactivo

**Files:**
- Modify: `flask_app/templates/editar_usuario_interno.html`

- [ ] **Step 1: Reemplazar los checkboxes hardcodeados por valores dinámicos**

Reemplazar el bloque de la sección Permisos (líneas ~297-354) con valores leídos del objeto `usuario`:

```html
{# Sección: Permisos #}
<section class="fields-section">
  <h2 class="section-title">
    <i class="fas fa-lock"></i> Permisos del Sistema
  </h2>
  <div class="fields-grid">
    <div class="field-group field-group--full">
      <div class="toggle-group">
        <label class="toggle-switch">
          <input type="checkbox" id="permAutopartes" name="perm_autopartes" {% if usuario.perm_autopartes %}checked{% endif %} />
          <span class="toggle-slider"></span>
        </label>
        <span class="toggle-label">Gestión de autopartes (agregar, editar, eliminar)</span>
      </div>
    </div>
    <div class="field-group field-group--full">
      <div class="toggle-group">
        <label class="toggle-switch">
          <input type="checkbox" id="permPedidos" name="perm_pedidos" {% if usuario.perm_pedidos %}checked{% endif %} />
          <span class="toggle-slider"></span>
        </label>
        <span class="toggle-label">Gestión de pedidos (ver, cambiar estado)</span>
      </div>
    </div>
    <div class="field-group field-group--full">
      <div class="toggle-group">
        <label class="toggle-switch">
          <input type="checkbox" id="permUsuarios" name="perm_usuarios" {% if usuario.perm_usuarios %}checked{% endif %} />
          <span class="toggle-slider"></span>
        </label>
        <span class="toggle-label">Gestión de usuarios (crear, editar, desactivar)</span>
      </div>
    </div>
    <div class="field-group field-group--full">
      <div class="toggle-group">
        <label class="toggle-switch">
          <input type="checkbox" id="permReportes" name="perm_reportes" {% if usuario.perm_reportes %}checked{% endif %} />
          <span class="toggle-slider"></span>
        </label>
        <span class="toggle-label">Acceso a reportes y analíticas</span>
      </div>
    </div>
    <div class="field-group field-group--full">
      <div class="toggle-group">
        <label class="toggle-switch">
          <input type="checkbox" id="permConfig" name="perm_config" {% if usuario.perm_config %}checked{% endif %} />
          <span class="toggle-slider"></span>
        </label>
        <span class="toggle-label">Configuración del sistema</span>
      </div>
    </div>
  </div>
</section>
```

- [ ] **Step 2: Agregar id al select de rol si no lo tiene**

Verificar que el `<select name="rol">` tenga `id="fRol"`.

- [ ] **Step 3: Agregar el mismo bloque script antes de </body>**

```html
<script>
  const PERMISOS_POR_ROL = {
    admin:    { permAutopartes: true,  permPedidos: true, permUsuarios: true,  permReportes: true,  permConfig: true },
    ventas:   { permAutopartes: false, permPedidos: true, permUsuarios: false, permReportes: true,  permConfig: false },
    almacen:  { permAutopartes: true,  permPedidos: true, permUsuarios: false, permReportes: false, permConfig: false },
    logistica:{ permAutopartes: false, permPedidos: true, permUsuarios: false, permReportes: false, permConfig: false },
  };

  document.getElementById('fRol').addEventListener('change', function () {
    const defaults = PERMISOS_POR_ROL[this.value];
    const ids = ['permAutopartes', 'permPedidos', 'permUsuarios', 'permReportes', 'permConfig'];
    ids.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.checked = defaults ? defaults[id] : false;
    });
  });
</script>
```

- [ ] **Step 4: Verificar manualmente**

Abrir `/editar-usuario-interno/{id}` para un usuario existente → los toggles deben mostrar sus valores reales de la BD. Cambiar el rol en el dropdown → los toggles deben reconfigurarse automáticamente. Guardar → verificar en la BD (o volver a abrir el form) que los valores persistieron.

- [ ] **Step 5: Commit**

```bash
git add flask_app/templates/editar_usuario_interno.html
git commit -m "feat: editar usuario interno muestra permisos reales y aplica defaults por rol"
```
