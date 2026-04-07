# M6 — Integración Frontends ↔ API — Plan de Implementación

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Conectar Flask (panel interno) y Laravel (portal externo) a la API FastAPI central, eliminando todos los datos hardcodeados mediante patrón ApiClient + Services + Controllers con sesión nativa.

**Architecture:** Dos capas en cada frontend — `ApiClient` (único punto de contacto HTTP con FastAPI, maneja errores) + `Services` (encapsulan endpoints por recurso). Flask usa rutas + `session[]`. Laravel usa Controllers + `session()` + Middleware. Ningún frontend toca PostgreSQL.

**Tech Stack:** Flask 3 + `requests`, Laravel 12 + `Http::` facade, FastAPI en `http://fastapi:8000` (Docker) / `http://localhost:8001` (local)

**Spec:** `docs/superpowers/specs/2026-04-07-m6-integracion-design.md`

**NO hacer commits** — el usuario los hará manualmente.

---

## FASE 1 — Flask (Panel Interno)

---

### Task 1: Agregar `requests` y crear estructura de módulos Flask

**Files:**
- Modify: `flask_app/requirements.txt`
- Create: `flask_app/api/__init__.py`
- Create: `flask_app/services/__init__.py`

- [ ] Agregar `requests==2.32.3` al final de `flask_app/requirements.txt`

- [ ] Crear `flask_app/api/__init__.py` (archivo vacío)

- [ ] Crear `flask_app/services/__init__.py` (archivo vacío)

- [ ] Verificar que la estructura existe:
```
flask_app/
├── api/__init__.py
└── services/__init__.py
```

---

### Task 2: ApiClient Flask

**Files:**
- Create: `flask_app/api/client.py`

- [ ] Crear `flask_app/api/client.py` con el siguiente contenido:

```python
"""
Capa de acceso HTTP a la API central FastAPI.
Un único punto de contacto: BASE_URL, headers y errores centralizados.
"""

import os
import requests as http


class ApiException(Exception):
    """Se lanza cuando FastAPI responde con status >= 400."""

    def __init__(self, status_code: int, detail: str):
        self.status_code = status_code
        self.detail = str(detail)
        super().__init__(self.detail)


class ApiClient:
    BASE_URL = os.getenv("API_URL", "http://localhost:8001")

    @staticmethod
    def _raise_for_status(resp: http.Response) -> dict:
        if resp.status_code >= 400:
            try:
                detail = resp.json().get("detail", resp.text)
            except Exception:
                detail = resp.text
            raise ApiException(resp.status_code, detail)
        return resp.json()

    @staticmethod
    def get(path: str, params: dict = None) -> dict:
        resp = http.get(f"{ApiClient.BASE_URL}{path}", params=params)
        return ApiClient._raise_for_status(resp)

    @staticmethod
    def post(path: str, json: dict = None, data: dict = None, files=None) -> dict:
        resp = http.post(
            f"{ApiClient.BASE_URL}{path}",
            json=json,
            data=data,
            files=files,
        )
        return ApiClient._raise_for_status(resp)

    @staticmethod
    def put(path: str, json: dict = None, data: dict = None, files=None) -> dict:
        resp = http.put(
            f"{ApiClient.BASE_URL}{path}",
            json=json,
            data=data,
            files=files,
        )
        return ApiClient._raise_for_status(resp)

    @staticmethod
    def patch(path: str, json: dict = None) -> dict:
        resp = http.patch(f"{ApiClient.BASE_URL}{path}", json=json)
        return ApiClient._raise_for_status(resp)

    @staticmethod
    def delete(path: str, auth: tuple = ("macuin", "123456")) -> dict:
        resp = http.delete(f"{ApiClient.BASE_URL}{path}", auth=auth)
        return ApiClient._raise_for_status(resp)

    @staticmethod
    def get_raw(path: str, auth: tuple = None) -> http.Response:
        """Descarga de archivos — devuelve Response sin procesar (para reportes)."""
        kwargs = {}
        if auth:
            kwargs["auth"] = auth
        return http.get(f"{ApiClient.BASE_URL}{path}", **kwargs)
```

---

### Task 3: Service Flask — auth

**Files:**
- Create: `flask_app/services/auth.py`

- [ ] Crear `flask_app/services/auth.py`:

```python
"""Service de autenticación — login de usuarios internos."""

from api.client import ApiClient


def login(email: str, password: str) -> dict:
    """
    Autentica un usuario interno contra la API.
    Retorna dict con: id, nombre, apellidos, email, rol, departamento,
    perm_autopartes, perm_pedidos, perm_usuarios, perm_reportes, perm_config.
    Lanza ApiException si las credenciales son incorrectas.
    """
    resp = ApiClient.post(
        "/v1/auth/login/interno",
        json={"email": email, "password": password},
    )
    return resp["data"][0]
```

---

### Task 4: Service Flask — autopartes

**Files:**
- Create: `flask_app/services/autopartes.py`

**Nota importante:** El form HTML usa `name="codigo"` pero la API espera `sku`.
En `crear()` y `editar()` se renombra el campo antes de enviarlo. Los templates
se corregirán en Task 8 para usar `name="sku"` directamente.

- [ ] Crear `flask_app/services/autopartes.py`:

```python
"""Service CRUD de autopartes."""

from api.client import ApiClient


def listar(categoria: str = None) -> list:
    """Retorna lista de autopartes. Si categoria se pasa, filtra por ella."""
    params = {"categoria": categoria} if categoria else None
    resp = ApiClient.get("/v1/autopartes/", params=params)
    return resp["data"]


def obtener(id: int) -> dict:
    """Retorna una autoparte por id."""
    resp = ApiClient.get(f"/v1/autopartes/{id}")
    return resp["data"]


def crear(form: dict, file=None) -> dict:
    """
    Crea una autoparte.
    form: dict con campos del formulario HTML.
    file: objeto FileStorage de Flask (request.files) o None.
    """
    # El endpoint FastAPI usa multipart/form-data
    data = {k: v for k, v in form.items() if k != "imagen"}
    files = None
    if file and file.filename:
        files = {"imagen": (file.filename, file.stream, file.content_type)}
    return ApiClient.post("/v1/autopartes/", data=data, files=files)


def editar(id: int, form: dict, file=None) -> dict:
    """Actualiza una autoparte completa (PUT multipart/form-data)."""
    data = {k: v for k, v in form.items() if k != "imagen"}
    files = None
    if file and file.filename:
        files = {"imagen": (file.filename, file.stream, file.content_type)}
    return ApiClient.put(f"/v1/autopartes/{id}", data=data, files=files)


def patch(id: int, campos: dict) -> dict:
    """Actualización parcial: precio, stock, stock_minimo, estado, ubicacion."""
    return ApiClient.patch(f"/v1/autopartes/{id}", json=campos)


def eliminar(id: int) -> dict:
    """Elimina una autoparte (requiere HTTPBasic macuin/123456)."""
    return ApiClient.delete(f"/v1/autopartes/{id}")
```

---

### Task 5: Service Flask — pedidos

**Files:**
- Create: `flask_app/services/pedidos.py`

- [ ] Crear `flask_app/services/pedidos.py`:

```python
"""Service de pedidos para el panel interno."""

from api.client import ApiClient

ESTADOS_VALIDOS = ["Pendiente", "En proceso", "Enviado", "Completado", "Cancelado"]


def listar(estado: str = None) -> list:
    """Lista todos los pedidos. Filtra por estado si se proporciona."""
    params = {"estado": estado} if estado else None
    resp = ApiClient.get("/v1/pedidos/", params=params)
    return resp["data"]


def obtener(id: int) -> dict:
    """Retorna un pedido con sus líneas de detalle (nombre, SKU, imagen)."""
    resp = ApiClient.get(f"/v1/pedidos/{id}")
    return resp["data"]


def cambiar_estado(id: int, estado: str) -> dict:
    """
    Cambia el estado de un pedido.
    estado debe ser uno de: Pendiente, En proceso, Enviado, Completado, Cancelado.
    """
    return ApiClient.patch(f"/v1/pedidos/{id}/estado", json={"estado": estado})
```

---

### Task 6: Service Flask — usuarios

**Files:**
- Create: `flask_app/services/usuarios.py`

- [ ] Crear `flask_app/services/usuarios.py`:

```python
"""Service CRUD de usuarios internos y externos."""

from api.client import ApiClient


# ── Usuarios Internos ──────────────────────────────────────────────────────────

def listar_internos() -> list:
    return ApiClient.get("/v1/usuarios/internos/")["data"]


def obtener_interno(id: int) -> dict:
    return ApiClient.get(f"/v1/usuarios/internos/{id}")["data"]


def crear_interno(data: dict) -> dict:
    """
    data debe incluir: nombre, apellidos, email, password, departamento, rol.
    Opcionales: telefono, cargo, sucursal, perm_*, estado.
    """
    return ApiClient.post("/v1/usuarios/internos/", json=data)


def editar_interno(id: int, data: dict) -> dict:
    """PUT — actualización completa. Acepta los mismos campos que crear_interno."""
    return ApiClient.put(f"/v1/usuarios/internos/{id}", json=data)


def eliminar_interno(id: int) -> dict:
    return ApiClient.delete(f"/v1/usuarios/internos/{id}")


# ── Usuarios Externos ──────────────────────────────────────────────────────────

def listar_externos(estado: str = None) -> list:
    params = {"estado": estado} if estado else None
    return ApiClient.get("/v1/usuarios/externos/", params=params)["data"]


def obtener_externo(id: int) -> dict:
    return ApiClient.get(f"/v1/usuarios/externos/{id}")["data"]


def crear_externo(data: dict) -> dict:
    """
    data debe incluir: nombre, apellidos, email, password.
    Opcionales: tipo_cliente, empresa, telefono, rfc, giro, calle,
    ciudad, estado_geo, cp, lista_precio, dias_credito, limite_credito,
    descuento, estado.
    """
    return ApiClient.post("/v1/usuarios/externos/", json=data)


def editar_externo(id: int, data: dict) -> dict:
    """PUT — actualización completa."""
    return ApiClient.put(f"/v1/usuarios/externos/{id}", json=data)


def patch_externo(id: int, campos: dict) -> dict:
    """PATCH — actualización parcial: tipo_cliente, empresa, lista_precio,
    dias_credito, limite_credito, descuento, estado."""
    return ApiClient.patch(f"/v1/usuarios/externos/{id}", json=campos)


def eliminar_externo(id: int) -> dict:
    return ApiClient.delete(f"/v1/usuarios/externos/{id}")
```

---

### Task 7: Actualizar app.py — sesión, rutas POST y protección

**Files:**
- Modify: `flask_app/app.py`

- [ ] Reemplazar **todo el contenido** de `flask_app/app.py` con:

```python
"""
Flask App — Panel interno MACUIN.
Todas las rutas llaman a services/ que consumen la API FastAPI.
Ninguna ruta accede a la BD directamente.
"""

import os
from io import BytesIO

from flask import (
    Flask,
    jsonify,
    render_template,
    request,
    redirect,
    session,
    flash,
    send_file,
)

import services.auth as AuthService
import services.autopartes as AutopartesService
import services.pedidos as PedidosService
import services.usuarios as UsuariosService
from api.client import ApiClient, ApiException

app = Flask(__name__)
app.secret_key = os.getenv("FLASK_SECRET_KEY", "macuin-dev-secret-2026")


# ── Helper ────────────────────────────────────────────────────────────────────

def requiere_sesion():
    """Retorna redirect al login si no hay sesión activa, None si está OK."""
    if "usuario" not in session:
        return redirect("/login")
    return None


# ── Health ────────────────────────────────────────────────────────────────────

@app.route("/", methods=["GET"])
def health_check():
    return jsonify({"status": "OK"}), 200


# ── Autenticación ─────────────────────────────────────────────────────────────

@app.route("/login", methods=["GET"])
def login_form():
    if "usuario" in session:
        return redirect("/gestion-autopartes")
    return render_template("login.html")


@app.route("/login", methods=["POST"])
def login_submit():
    email    = request.form.get("email", "").strip()
    password = request.form.get("password", "").strip()
    try:
        usuario = AuthService.login(email, password)
        session["usuario"] = usuario
        return redirect("/gestion-autopartes")
    except ApiException as e:
        flash(e.detail, "error")
        return redirect("/login")


@app.route("/logout")
def logout():
    session.clear()
    return redirect("/login")


# ── Autopartes ────────────────────────────────────────────────────────────────

@app.route("/gestion-autopartes", methods=["GET"])
def gestion_autopartes():
    r = requiere_sesion()
    if r: return r
    try:
        autopartes = AutopartesService.listar()
    except ApiException:
        autopartes = []
    return render_template("gestion_autopartes.html", autopartes=autopartes)


@app.route("/agregar-autoparte", methods=["GET"])
def agregar_autoparte_form():
    r = requiere_sesion()
    if r: return r
    return render_template("agregar_autoparte.html")


@app.route("/agregar-autoparte", methods=["POST"])
def agregar_autoparte_submit():
    r = requiere_sesion()
    if r: return r
    try:
        AutopartesService.crear(request.form.to_dict(), request.files.get("imagen"))
        flash("Autoparte creada correctamente.", "success")
        return redirect("/gestion-autopartes")
    except ApiException as e:
        flash(e.detail, "error")
        return redirect("/agregar-autoparte")


@app.route("/editar-autoparte/<int:id>", methods=["GET"])
def editar_autoparte_form(id):
    r = requiere_sesion()
    if r: return r
    try:
        autoparte = AutopartesService.obtener(id)
    except ApiException as e:
        flash(e.detail, "error")
        return redirect("/gestion-autopartes")
    return render_template("editar_autoparte.html", autoparte=autoparte)


@app.route("/editar-autoparte/<int:id>", methods=["POST"])
def editar_autoparte_submit(id):
    r = requiere_sesion()
    if r: return r
    try:
        AutopartesService.editar(id, request.form.to_dict(), request.files.get("imagen"))
        flash("Autoparte actualizada correctamente.", "success")
        return redirect("/gestion-autopartes")
    except ApiException as e:
        flash(e.detail, "error")
        return redirect(f"/editar-autoparte/{id}")


@app.route("/eliminar-autoparte/<int:id>")
def eliminar_autoparte(id):
    r = requiere_sesion()
    if r: return r
    try:
        AutopartesService.eliminar(id)
        flash("Autoparte eliminada.", "success")
    except ApiException as e:
        flash(e.detail, "error")
    return redirect("/gestion-autopartes")


# ── Pedidos ───────────────────────────────────────────────────────────────────

@app.route("/gestion-pedidos", methods=["GET"])
def gestion_pedidos():
    r = requiere_sesion()
    if r: return r
    estado = request.args.get("estado")
    try:
        pedidos = PedidosService.listar(estado)
    except ApiException:
        pedidos = []
    return render_template("gestion_pedidos.html", pedidos=pedidos)


@app.route("/detalle-pedido/<int:id>", methods=["GET"])
def detalle_pedido(id):
    r = requiere_sesion()
    if r: return r
    try:
        pedido = PedidosService.obtener(id)
    except ApiException as e:
        flash(e.detail, "error")
        return redirect("/gestion-pedidos")
    return render_template("detalle_pedido.html", pedido=pedido)


@app.route("/pedidos/<int:id>/estado", methods=["POST"])
def cambiar_estado_pedido(id):
    r = requiere_sesion()
    if r: return r
    nuevo_estado = request.form.get("estado", "").strip()
    try:
        PedidosService.cambiar_estado(id, nuevo_estado)
        flash(f"Estado actualizado a '{nuevo_estado}'.", "success")
    except ApiException as e:
        flash(e.detail, "error")
    return redirect(f"/detalle-pedido/{id}")


# ── Usuarios Internos ─────────────────────────────────────────────────────────

@app.route("/gestion-usuarios-internos", methods=["GET"])
def gestion_usuarios_internos():
    r = requiere_sesion()
    if r: return r
    try:
        usuarios = UsuariosService.listar_internos()
    except ApiException:
        usuarios = []
    return render_template("gestion_usuarios_internos.html", usuarios=usuarios)


@app.route("/agregar-usuario-interno", methods=["GET"])
def agregar_usuario_interno_form():
    r = requiere_sesion()
    if r: return r
    return render_template("agregar_usuario_interno.html")


@app.route("/agregar-usuario-interno", methods=["POST"])
def agregar_usuario_interno_submit():
    r = requiere_sesion()
    if r: return r
    data = request.form.to_dict()
    try:
        UsuariosService.crear_interno(data)
        flash("Usuario interno creado.", "success")
        return redirect("/gestion-usuarios-internos")
    except ApiException as e:
        flash(e.detail, "error")
        return redirect("/agregar-usuario-interno")


@app.route("/editar-usuario-interno/<int:id>", methods=["GET"])
def editar_usuario_interno_form(id):
    r = requiere_sesion()
    if r: return r
    try:
        usuario = UsuariosService.obtener_interno(id)
    except ApiException as e:
        flash(e.detail, "error")
        return redirect("/gestion-usuarios-internos")
    return render_template("editar_usuario_interno.html", usuario=usuario)


@app.route("/editar-usuario-interno/<int:id>", methods=["POST"])
def editar_usuario_interno_submit(id):
    r = requiere_sesion()
    if r: return r
    data = request.form.to_dict()
    try:
        UsuariosService.editar_interno(id, data)
        flash("Usuario interno actualizado.", "success")
        return redirect("/gestion-usuarios-internos")
    except ApiException as e:
        flash(e.detail, "error")
        return redirect(f"/editar-usuario-interno/{id}")


@app.route("/eliminar-usuario-interno/<int:id>")
def eliminar_usuario_interno(id):
    r = requiere_sesion()
    if r: return r
    try:
        UsuariosService.eliminar_interno(id)
        flash("Usuario interno eliminado.", "success")
    except ApiException as e:
        flash(e.detail, "error")
    return redirect("/gestion-usuarios-internos")


# ── Usuarios Externos ─────────────────────────────────────────────────────────

@app.route("/gestion-usuarios-externos", methods=["GET"])
def gestion_usuarios_externos():
    r = requiere_sesion()
    if r: return r
    try:
        usuarios = UsuariosService.listar_externos()
    except ApiException:
        usuarios = []
    return render_template("gestion_usuarios_externos.html", usuarios=usuarios)


@app.route("/agregar-usuario-externo", methods=["GET"])
def agregar_usuario_externo_form():
    r = requiere_sesion()
    if r: return r
    return render_template("agregar_usuario_externo.html")


@app.route("/agregar-usuario-externo", methods=["POST"])
def agregar_usuario_externo_submit():
    r = requiere_sesion()
    if r: return r
    data = request.form.to_dict()
    try:
        UsuariosService.crear_externo(data)
        flash("Usuario externo creado.", "success")
        return redirect("/gestion-usuarios-externos")
    except ApiException as e:
        flash(e.detail, "error")
        return redirect("/agregar-usuario-externo")


@app.route("/editar-usuario-externo/<int:id>", methods=["GET"])
def editar_usuario_externo_form(id):
    r = requiere_sesion()
    if r: return r
    try:
        usuario = UsuariosService.obtener_externo(id)
    except ApiException as e:
        flash(e.detail, "error")
        return redirect("/gestion-usuarios-externos")
    return render_template("editar_usuario_externo.html", usuario=usuario)


@app.route("/editar-usuario-externo/<int:id>", methods=["POST"])
def editar_usuario_externo_submit(id):
    r = requiere_sesion()
    if r: return r
    data = request.form.to_dict()
    try:
        UsuariosService.editar_externo(id, data)
        flash("Usuario externo actualizado.", "success")
        return redirect("/gestion-usuarios-externos")
    except ApiException as e:
        flash(e.detail, "error")
        return redirect(f"/editar-usuario-externo/{id}")


@app.route("/eliminar-usuario-externo/<int:id>")
def eliminar_usuario_externo(id):
    r = requiere_sesion()
    if r: return r
    try:
        UsuariosService.eliminar_externo(id)
        flash("Usuario externo eliminado.", "success")
    except ApiException as e:
        flash(e.detail, "error")
    return redirect("/gestion-usuarios-externos")


# ── Perfil ────────────────────────────────────────────────────────────────────

@app.route("/perfil", methods=["GET"])
def perfil():
    r = requiere_sesion()
    if r: return r
    return render_template("perfil.html", usuario=session["usuario"])


# ── Reportes ──────────────────────────────────────────────────────────────────

@app.route("/reportes", methods=["GET"])
def reportes():
    r = requiere_sesion()
    if r: return r
    return render_template("reportes.html")


@app.route("/reportes/descargar/<tipo>/<formato>")
def descargar_reporte(tipo, formato):
    r = requiere_sesion()
    if r: return r
    try:
        resp = ApiClient.get_raw(
            f"/v1/reportes/{tipo}/{formato}",
            auth=("macuin", "123456"),
        )
        if resp.status_code >= 400:
            flash(f"Error al generar reporte: {resp.status_code}", "error")
            return redirect("/reportes")
        return send_file(
            BytesIO(resp.content),
            download_name=f"reporte_{tipo}.{formato}",
            as_attachment=True,
            mimetype=resp.headers.get("Content-Type", "application/octet-stream"),
        )
    except Exception as e:
        flash(str(e), "error")
        return redirect("/reportes")


if __name__ == "__main__":
    app.run(debug=True)
```

---

### Task 8: Actualizar templates Flask — vistas de lista

Leer cada template antes de editarlo. Reemplazar filas/datos hardcodeados con loops Jinja2.

**Files:**
- Modify: `flask_app/templates/gestion_autopartes.html`
- Modify: `flask_app/templates/gestion_pedidos.html`
- Modify: `flask_app/templates/gestion_usuarios_internos.html`
- Modify: `flask_app/templates/gestion_usuarios_externos.html`
- Modify: `flask_app/templates/detalle_pedido.html`
- Modify: `flask_app/templates/perfil.html`

#### gestion_autopartes.html

- [ ] Leer el archivo completo
- [ ] Reemplazar los KPIs hardcodeados:
```html
<!-- ANTES -->
<span class="kpi-val">1</span>
<span class="kpi-lbl">Total</span>
...
<span class="kpi-val">1</span>
<span class="kpi-lbl">En Stock</span>

<!-- DESPUÉS -->
<span class="kpi-val">{{ autopartes | length }}</span>
<span class="kpi-lbl">Total</span>
...
<span class="kpi-val">{{ autopartes | selectattr('estado', 'equalto', 'en_stock') | list | length }}</span>
<span class="kpi-lbl">En Stock</span>
```

- [ ] Reemplazar el `<tr class="row-example">` hardcodeado con loop real.
  Borrar desde `<!-- Fila de ejemplo con copa_piston -->` hasta el `</tr>` que lo cierra.
  En su lugar insertar:
```html
{% for a in autopartes %}
<tr>
  <td class="td-id">{{ a.id }}</td>
  <td>
    <div class="tbl-img-wrap">
      {% if a.imagen %}
      <img src="{{ a.imagen }}" alt="{{ a.nombre }}" class="tbl-img" />
      {% else %}
      <i class="fas fa-image" style="font-size:24px;color:#ccc;"></i>
      {% endif %}
    </div>
  </td>
  <td>
    <span class="td-name">{{ a.nombre }}</span>
    <span class="td-unit">{{ a.stock }} — {{ a.unidad }}</span>
  </td>
  <td><span class="td-muted">{{ a.categoria or '—' }}</span></td>
  <td><span class="td-muted">{{ a.marca or '—' }}</span></td>
  <td><span class="td-muted">{{ a.stock }}</span></td>
  <td>
    <span class="badge {% if a.estado == 'en_stock' %}badge--ok{% elif a.estado == 'bajo_stock' %}badge--warn{% else %}badge--err{% endif %}">
      {{ a.estado | replace('_', ' ') | title }}
    </span>
  </td>
  <td>
    <div class="tbl-actions">
      <a href="/editar-autoparte/{{ a.id }}" class="act-btn act-btn--edit" aria-label="Editar">
        <i class="fas fa-edit"></i>
      </a>
      <a href="/eliminar-autoparte/{{ a.id }}"
         class="act-btn act-btn--del"
         aria-label="Eliminar"
         onclick="return confirm('¿Eliminar {{ a.nombre }}?')">
        <i class="fas fa-trash"></i>
      </a>
    </div>
  </td>
</tr>
{% else %}
<tr><td colspan="8" style="text-align:center;padding:32px;color:#aaa;">No hay autopartes registradas.</td></tr>
{% endfor %}
```

- [ ] Agregar bloque de mensajes flash justo antes del `<main>` o al inicio del `<body>`:
```html
{% with messages = get_flashed_messages(with_categories=true) %}
  {% for category, message in messages %}
    <div class="flash flash--{{ category }}" style="
      position:fixed;top:20px;right:20px;z-index:9999;
      padding:12px 20px;border-radius:6px;font-size:14px;
      background:{% if category == 'error' %}#C41230{% else %}#16a34a{% endif %};
      color:#fff;box-shadow:0 4px 12px rgba(0,0,0,.2);
    ">{{ message }}</div>
  {% endfor %}
{% endwith %}
```

#### gestion_pedidos.html

- [ ] Leer el archivo completo
- [ ] Reemplazar KPIs hardcodeados:
```html
<!-- ANTES -->
<p class="kpi-value">&mdash;</p>
<p class="kpi-label">Completados</p>

<p class="kpi-value">1</p>
<p class="kpi-label">Pendientes</p>

<p class="kpi-value">&mdash;</p>
<p class="kpi-label">Cancelados</p>

<p class="kpi-value">1</p>
<p class="kpi-label">Total de Pedidos</p>

<!-- DESPUÉS -->
<p class="kpi-value">{{ pedidos | selectattr('estado', 'equalto', 'Completado') | list | length }}</p>
<p class="kpi-label">Completados</p>

<p class="kpi-value">{{ pedidos | selectattr('estado', 'equalto', 'Pendiente') | list | length }}</p>
<p class="kpi-label">Pendientes</p>

<p class="kpi-value">{{ pedidos | selectattr('estado', 'equalto', 'Cancelado') | list | length }}</p>
<p class="kpi-label">Cancelados</p>

<p class="kpi-value">{{ pedidos | length }}</p>
<p class="kpi-label">Total de Pedidos</p>
```

- [ ] Reemplazar fila simulada `<!-- ── Pedido simulado ── -->` con loop real:
```html
{% for p in pedidos %}
<tr>
  <td class="td-id">{{ p.folio }}</td>
  <td>{{ p.usuario_externo_id or '—' }}</td>
  <td>{{ p.creado_en[:10] if p.creado_en else '—' }}</td>
  <td>
    <span class="badge {% if p.estado == 'Completado' %}badge--ok
      {% elif p.estado == 'Cancelado' %}badge--err
      {% elif p.estado == 'Pendiente' %}badge--warn
      {% else %}badge--neutral{% endif %}">
      {{ p.estado }}
    </span>
  </td>
  <td>${{ '%.2f' | format(p.total) }}</td>
  <td>
    <a href="/detalle-pedido/{{ p.id }}" class="act-btn act-btn--edit">
      <i class="fas fa-eye"></i>
    </a>
  </td>
</tr>
{% else %}
<tr><td colspan="6" style="text-align:center;padding:32px;color:#aaa;">No hay pedidos.</td></tr>
{% endfor %}
```

- [ ] Agregar bloque flash igual al de gestion_autopartes.html

#### gestion_usuarios_internos.html

- [ ] Leer el archivo completo
- [ ] Localizar la tabla con `<tbody>` y reemplazar filas hardcodeadas con:
```html
{% for u in usuarios %}
<tr>
  <td class="td-id">{{ u.id }}</td>
  <td>{{ u.nombre }} {{ u.apellidos }}</td>
  <td>{{ u.email }}</td>
  <td>{{ u.rol }}</td>
  <td>{{ u.departamento }}</td>
  <td>
    <span class="badge {% if u.estado == 'activo' %}badge--ok{% else %}badge--err{% endif %}">
      {{ u.estado | title }}
    </span>
  </td>
  <td>
    <a href="/editar-usuario-interno/{{ u.id }}" class="act-btn act-btn--edit"><i class="fas fa-edit"></i></a>
    <a href="/eliminar-usuario-interno/{{ u.id }}" class="act-btn act-btn--del"
       onclick="return confirm('¿Eliminar a {{ u.nombre }}?')"><i class="fas fa-trash"></i></a>
  </td>
</tr>
{% else %}
<tr><td colspan="7" style="text-align:center;padding:32px;color:#aaa;">Sin usuarios internos.</td></tr>
{% endfor %}
```
- [ ] Agregar bloque flash

#### gestion_usuarios_externos.html

- [ ] Leer el archivo completo
- [ ] Reemplazar filas hardcodeadas con loop similar:
```html
{% for u in usuarios %}
<tr>
  <td class="td-id">{{ u.id }}</td>
  <td>{{ u.nombre }} {{ u.apellidos }}</td>
  <td>{{ u.email }}</td>
  <td>{{ u.tipo_cliente or '—' }}</td>
  <td>{{ u.empresa or '—' }}</td>
  <td>
    <span class="badge {% if u.estado == 'activo' %}badge--ok{% else %}badge--err{% endif %}">
      {{ u.estado | title }}
    </span>
  </td>
  <td>
    <a href="/editar-usuario-externo/{{ u.id }}" class="act-btn act-btn--edit"><i class="fas fa-edit"></i></a>
    <a href="/eliminar-usuario-externo/{{ u.id }}" class="act-btn act-btn--del"
       onclick="return confirm('¿Eliminar a {{ u.nombre }}?')"><i class="fas fa-trash"></i></a>
  </td>
</tr>
{% else %}
<tr><td colspan="7" style="text-align:center;padding:32px;color:#aaa;">Sin usuarios externos.</td></tr>
{% endfor %}
```
- [ ] Agregar bloque flash

#### detalle_pedido.html

- [ ] Leer el archivo completo
- [ ] Reemplazar datos hardcodeados del pedido con variables. Los campos del objeto `pedido`
  (tal como los devuelve `GET /v1/pedidos/{id}`):
  `pedido['id']`, `pedido['folio']`, `pedido['estado']`, `pedido['subtotal']`,
  `pedido['impuestos']`, `pedido['envio']`, `pedido['total']`, `pedido['creado_en']`,
  `pedido['items']` (lista de líneas).
  Cada línea de `pedido['items']` tiene: `nombre`, `sku`, `cantidad`, `precio_unitario`, `subtotal`, `imagen`.
```html
<!-- Encabezado del pedido -->
<h2>{{ pedido['folio'] }}</h2>
<span class="badge ...">{{ pedido['estado'] }}</span>
<p>Fecha: {{ pedido['creado_en'][:10] if pedido['creado_en'] else '—' }}</p>

<!-- Tabla de líneas -->
{% for d in pedido['items'] %}
<tr>
  <td>{{ d['nombre'] }}</td>
  <td>{{ d['sku'] }}</td>
  <td>{{ d['cantidad'] }}</td>
  <td>${{ '%.2f' | format(d['precio_unitario']) }}</td>
  <td>${{ '%.2f' | format(d['subtotal']) }}</td>
</tr>
{% endfor %}

<!-- Totales -->
<p>Subtotal: ${{ '%.2f' | format(pedido['subtotal']) }}</p>
<p>Impuestos: ${{ '%.2f' | format(pedido['impuestos']) }}</p>
<p>Total: ${{ '%.2f' | format(pedido['total']) }}</p>
```

- [ ] Agregar formulario para cambiar estado:
```html
<form action="/pedidos/{{ pedido.id }}/estado" method="post">
  <select name="estado">
    <option value="Pendiente" {% if pedido.estado == 'Pendiente' %}selected{% endif %}>Pendiente</option>
    <option value="En proceso" {% if pedido.estado == 'En proceso' %}selected{% endif %}>En proceso</option>
    <option value="Enviado" {% if pedido.estado == 'Enviado' %}selected{% endif %}>Enviado</option>
    <option value="Completado" {% if pedido.estado == 'Completado' %}selected{% endif %}>Completado</option>
    <option value="Cancelado" {% if pedido.estado == 'Cancelado' %}selected{% endif %}>Cancelado</option>
  </select>
  <button type="submit">Actualizar estado</button>
</form>
```

#### perfil.html

- [ ] Leer el archivo completo
- [ ] Reemplazar datos hardcodeados con variables de sesión:
```html
{{ usuario.nombre }} {{ usuario.apellidos }}
{{ usuario.email }}
{{ usuario.rol }}
{{ usuario.departamento }}
```

---

### Task 9: Actualizar templates Flask — formularios

**Files:**
- Modify: `flask_app/templates/agregar_autoparte.html`
- Modify: `flask_app/templates/editar_autoparte.html`
- Modify: `flask_app/templates/agregar_usuario_interno.html`
- Modify: `flask_app/templates/editar_usuario_interno.html`
- Modify: `flask_app/templates/agregar_usuario_externo.html`
- Modify: `flask_app/templates/editar_usuario_externo.html`
- Modify: `flask_app/templates/login.html`

#### login.html

- [ ] Leer el archivo completo
- [ ] Localizar el `<form>` y cambiar:
  - `action` → `action="/login"`
  - `method` → `method="post"`
- [ ] Verificar que los campos tienen `name="email"` y `name="password"`
- [ ] Agregar bloque de mensajes flash

#### agregar_autoparte.html

- [ ] Leer el archivo completo
- [ ] Cambiar el `<form>`:
  - `action="/agregar-autoparte"` `method="post"` `enctype="multipart/form-data"`
- [ ] Cambiar `name="codigo"` → `name="sku"` (la API espera `sku`, no `codigo`)
- [ ] Agregar bloque flash

#### editar_autoparte.html

- [ ] Leer el archivo completo
- [ ] Cambiar el `<form>`:
  - `action="/editar-autoparte/{{ autoparte.id }}"` `method="post"` `enctype="multipart/form-data"`
- [ ] Pre-llenar todos los campos con `value="{{ autoparte.CAMPO }}"`:
  - `nombre`, `sku`, `categoria`, `marca`, `precio`, `precio_original`
  - `stock`, `stock_minimo`, `unidad`, `ubicacion`
  - `marca_vehiculo`, `modelo_vehiculo`, `aplicacion`
  - `estado` (radio button: `checked` en el que coincida)
- [ ] Agregar bloque flash

#### agregar_usuario_interno.html

- [ ] Leer el archivo completo
- [ ] Cambiar form: `action="/agregar-usuario-interno"` `method="post"`
- [ ] Verificar que los campos tienen los `name` correctos:
  `nombre`, `apellidos`, `email`, `password`, `departamento`, `rol`,
  `telefono` (opcional), `cargo` (opcional), `sucursal` (opcional)
- [ ] Agregar bloque flash

#### editar_usuario_interno.html

- [ ] Leer el archivo completo
- [ ] Cambiar form: `action="/editar-usuario-interno/{{ usuario.id }}"` `method="post"`
- [ ] Pre-llenar campos con `value="{{ usuario.CAMPO }}"`:
  `nombre`, `apellidos`, `email`, `password`, `departamento`, `rol`,
  `telefono`, `cargo`, `sucursal`, `estado`
- [ ] Agregar bloque flash

#### agregar_usuario_externo.html

- [ ] Leer el archivo completo
- [ ] Cambiar form: `action="/agregar-usuario-externo"` `method="post"`
- [ ] Verificar campos: `nombre`, `apellidos`, `email`, `password`,
  `tipo_cliente`, `empresa`, `telefono`, `rfc`, `giro`,
  `calle`, `ciudad`, `estado_geo`, `cp`
- [ ] Agregar bloque flash

#### editar_usuario_externo.html

- [ ] Leer el archivo completo
- [ ] Cambiar form: `action="/editar-usuario-externo/{{ usuario.id }}"` `method="post"`
- [ ] Pre-llenar todos los campos de usuario
- [ ] Agregar bloque flash

---

## FASE 2 — Laravel (Portal Externo)

---

### Task 10: ApiClient Laravel + configuración

**Files:**
- Create: `laravel_app/app/Http/Client/ApiClient.php`
- Modify: `laravel_app/config/services.php`
- Modify: `laravel_app/.env`

- [ ] Crear directorio `laravel_app/app/Http/Client/`

- [ ] Crear `laravel_app/app/Http/Client/ApiClient.php`:

```php
<?php

namespace App\Http\Client;

use Illuminate\Support\Facades\Http;

class ApiException extends \RuntimeException
{
    public function __construct(public int $statusCode, string $message)
    {
        parent::__construct($message);
    }
}

class ApiClient
{
    private string $base;

    public function __construct()
    {
        $this->base = config('services.api.url', 'http://localhost:8001');
    }

    private function handle(\Illuminate\Http\Client\Response $resp): array
    {
        if ($resp->failed()) {
            $detail = $resp->json('detail') ?? $resp->body();
            if (is_array($detail)) {
                $detail = collect($detail)->pluck('msg')->implode('; ');
            }
            throw new ApiException($resp->status(), (string) $detail);
        }
        return $resp->json() ?? [];
    }

    public function get(string $path, array $query = []): array
    {
        return $this->handle(Http::get($this->base . $path, $query));
    }

    public function post(string $path, array $data = []): array
    {
        return $this->handle(Http::post($this->base . $path, $data));
    }

    public function patch(string $path, array $data = []): array
    {
        return $this->handle(Http::patch($this->base . $path, $data));
    }

    public function delete(string $path, string $user = 'macuin', string $pass = '123456'): array
    {
        return $this->handle(
            Http::withBasicAuth($user, $pass)->delete($this->base . $path)
        );
    }
}
```

- [ ] Abrir `laravel_app/config/services.php` y agregar al array retornado:
```php
'api' => [
    'url' => env('API_URL', 'http://localhost:8001'),
],
```

- [ ] Agregar al final de `laravel_app/.env`:
```
API_URL=http://localhost:8001
```

---

### Task 11: Services Laravel — Auth

**Files:**
- Create: `laravel_app/app/Http/Services/AuthService.php`

- [ ] Crear directorio `laravel_app/app/Http/Services/`

- [ ] Crear `laravel_app/app/Http/Services/AuthService.php`:

```php
<?php

namespace App\Http\Services;

use App\Http\Client\ApiClient;

class AuthService
{
    public function __construct(private ApiClient $client) {}

    /**
     * Registra un usuario externo.
     * $data: nombre, apellidos, email, password
     */
    public function registro(array $data): array
    {
        $resp = $this->client->post('/v1/auth/registro', $data);
        return $resp['data'][0] ?? $resp;
    }

    /**
     * Autentica un cliente externo.
     * Retorna: id, nombre, apellidos, email, tipo_cliente, descuento, lista_precio
     */
    public function login(string $email, string $password): array
    {
        $resp = $this->client->post('/v1/auth/login/externo', [
            'email'    => $email,
            'password' => $password,
        ]);
        return $resp['data'][0];
    }
}
```

---

### Task 12: Services Laravel — Autopartes y Pedidos

**Files:**
- Create: `laravel_app/app/Http/Services/AutopartesService.php`
- Create: `laravel_app/app/Http/Services/PedidosService.php`

- [ ] Crear `laravel_app/app/Http/Services/AutopartesService.php`:

```php
<?php

namespace App\Http\Services;

use App\Http\Client\ApiClient;

class AutopartesService
{
    public function __construct(private ApiClient $client) {}

    /** Lista todas las autopartes. Si $categoria se pasa, filtra. */
    public function listar(?string $categoria = null): array
    {
        $query = $categoria ? ['categoria' => $categoria] : [];
        $resp = $this->client->get('/v1/autopartes/', $query);
        return $resp['data'] ?? [];
    }

    /** Retorna una autoparte por id. */
    public function obtener(int $id): array
    {
        $resp = $this->client->get("/v1/autopartes/{$id}");
        return $resp['data'] ?? [];
    }
}
```

- [ ] Crear `laravel_app/app/Http/Services/PedidosService.php`:

```php
<?php

namespace App\Http\Services;

use App\Http\Client\ApiClient;

class PedidosService
{
    public function __construct(private ApiClient $client) {}

    /**
     * Crea un pedido.
     * $usuarioId: id del cliente externo logueado.
     * $items: [['autoparte_id' => int, 'cantidad' => int], ...]
     * $direccion: ['calle' => str, 'ciudad' => str, 'estado' => str, 'cp' => str]
     */
    public function crear(int $usuarioId, array $items, array $direccion): array
    {
        return $this->client->post('/v1/pedidos/', [
            'usuario_externo_id' => $usuarioId,
            'items'              => $items,
            'dir_calle'          => $direccion['calle']  ?? '',
            'dir_ciudad'         => $direccion['ciudad'] ?? '',
            'dir_estado'         => $direccion['estado'] ?? '',
            'dir_cp'             => $direccion['cp']     ?? '',
        ]);
    }

    /** Lista pedidos de un cliente externo específico. */
    public function listarPorUsuario(int $usuarioId): array
    {
        $resp = $this->client->get("/v1/pedidos/usuario/{$usuarioId}");
        return $resp['data'] ?? [];
    }

    /** Detalle completo de un pedido con líneas. */
    public function obtener(int $id): array
    {
        $resp = $this->client->get("/v1/pedidos/{$id}");
        return $resp['data'] ?? [];
    }
}
```

---

### Task 13: Middleware CheckSession Laravel

**Files:**
- Create: `laravel_app/app/Http/Middleware/CheckSession.php`
- Modify: `laravel_app/bootstrap/app.php`

- [ ] Crear `laravel_app/app/Http/Middleware/CheckSession.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSession
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('usuario')) {
            return redirect('/login');
        }
        return $next($request);
    }
}
```

- [ ] Registrar el middleware en `laravel_app/bootstrap/app.php`.
  Leer el archivo primero. Buscar `->withMiddleware(function (Middleware $middleware) {` y agregar dentro:
```php
$middleware->alias([
    'check.session' => \App\Http\Middleware\CheckSession::class,
]);
```

---

### Task 14: Controllers Laravel — Auth

**Files:**
- Create: `laravel_app/app/Http/Controllers/AuthController.php`

- [ ] Crear `laravel_app/app/Http/Controllers/AuthController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\AuthService;
use App\Http\Client\ApiException;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function showLogin()
    {
        if (session('usuario')) {
            return redirect('/dashboard');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        try {
            $usuario = $this->authService->login(
                $request->input('email'),
                $request->input('password')
            );
            session(['usuario' => $usuario]);
            return redirect('/dashboard');
        } catch (ApiException $e) {
            return back()->withErrors(['api' => $e->getMessage()])->withInput();
        }
    }

    public function showRegistro()
    {
        if (session('usuario')) {
            return redirect('/dashboard');
        }
        return view('register');
    }

    public function registro(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|min:2',
            'apellidos' => 'required|min:2',
            'email'     => 'required|email',
            'password'  => 'required|min:4',
        ]);

        try {
            $this->authService->registro($request->only([
                'nombre', 'apellidos', 'email', 'password'
            ]));
            return redirect('/login')->with('success', 'Cuenta creada. Inicia sesión.');
        } catch (ApiException $e) {
            return back()->withErrors(['api' => $e->getMessage()])->withInput();
        }
    }

    public function logout()
    {
        session()->forget('usuario');
        session()->forget('carrito');
        return redirect('/login');
    }
}
```

---

### Task 15: Controllers Laravel — Catálogo

**Files:**
- Create: `laravel_app/app/Http/Controllers/CatalogoController.php`

- [ ] Crear `laravel_app/app/Http/Controllers/CatalogoController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\AutopartesService;
use App\Http\Client\ApiException;

class CatalogoController extends Controller
{
    public function __construct(private AutopartesService $autopartesService) {}

    /** Dashboard — muestra autopartes destacadas (primeras 8). */
    public function dashboard()
    {
        try {
            $autopartes = array_slice($this->autopartesService->listar(), 0, 8);
        } catch (ApiException $e) {
            $autopartes = [];
        }
        return view('dashboard', compact('autopartes'));
    }

    /** Catálogo completo con filtro opcional por categoría. */
    public function index(Request $request)
    {
        $categoria = $request->query('categoria');
        try {
            $autopartes = $this->autopartesService->listar($categoria);
        } catch (ApiException $e) {
            $autopartes = [];
        }
        return view('catalogo', compact('autopartes'));
    }

    /** Detalle de una autoparte. */
    public function show(int $id)
    {
        try {
            $autoparte = $this->autopartesService->obtener($id);
        } catch (ApiException $e) {
            return redirect('/catalogo')->withErrors(['api' => $e->getMessage()]);
        }
        return view('detalle-producto', compact('autoparte'));
    }
}
```

---

### Task 16: Controllers Laravel — Carrito, Pedidos y Perfil

**Files:**
- Create: `laravel_app/app/Http/Controllers/CarritoController.php`
- Create: `laravel_app/app/Http/Controllers/PedidoController.php`
- Create: `laravel_app/app/Http/Controllers/PerfilController.php`

- [ ] Crear `laravel_app/app/Http/Controllers/CarritoController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\PedidosService;
use App\Http\Client\ApiException;

class CarritoController extends Controller
{
    public function __construct(private PedidosService $pedidosService) {}

    /** Muestra el carrito actual desde session. */
    public function index()
    {
        $carrito = session('carrito', []);
        $subtotal = collect($carrito)->sum(fn($i) => $i['precio'] * $i['cantidad']);
        return view('carrito', compact('carrito', 'subtotal'));
    }

    /** Agrega o actualiza item en el carrito (session). */
    public function agregar(Request $request)
    {
        $id       = $request->input('autoparte_id');
        $nombre   = $request->input('nombre');
        $precio   = (float) $request->input('precio');
        $cantidad = max(1, (int) $request->input('cantidad', 1));
        $imagen   = $request->input('imagen', '');

        $carrito = session('carrito', []);
        if (isset($carrito[$id])) {
            $carrito[$id]['cantidad'] += $cantidad;
        } else {
            $carrito[$id] = compact('id', 'nombre', 'precio', 'cantidad', 'imagen');
        }
        session(['carrito' => $carrito]);

        return redirect('/carrito')->with('success', 'Producto agregado al carrito.');
    }

    /** Actualiza cantidades desde el carrito. */
    public function actualizar(Request $request)
    {
        $carrito = session('carrito', []);
        foreach ($request->input('cantidades', []) as $id => $qty) {
            $qty = (int) $qty;
            if ($qty <= 0) {
                unset($carrito[$id]);
            } else {
                $carrito[$id]['cantidad'] = $qty;
            }
        }
        session(['carrito' => $carrito]);
        return redirect('/carrito');
    }

    /** Muestra form de checkout con datos del carrito. */
    public function showCheckout()
    {
        $carrito  = session('carrito', []);
        $subtotal = collect($carrito)->sum(fn($i) => $i['precio'] * $i['cantidad']);
        return view('checkout', compact('carrito', 'subtotal'));
    }

    /** Procesa checkout → llama API → limpia carrito. */
    public function checkout(Request $request)
    {
        $request->validate([
            'calle'   => 'required',
            'ciudad'  => 'required',
            'estado'  => 'required',
            'cp'      => 'required',
        ]);

        $carrito = session('carrito', []);
        if (empty($carrito)) {
            return redirect('/carrito')->withErrors(['api' => 'El carrito está vacío.']);
        }

        $items = collect($carrito)->map(fn($i) => [
            'autoparte_id' => (int) $i['id'],
            'cantidad'     => (int) $i['cantidad'],
        ])->values()->all();

        $direccion = $request->only(['calle', 'ciudad', 'estado', 'cp']);
        $usuarioId = session('usuario.id');

        try {
            $pedido = $this->pedidosService->crear($usuarioId, $items, $direccion);
            session()->forget('carrito');
            return redirect('/pedidos')->with('success', 'Pedido realizado exitosamente.');
        } catch (ApiException $e) {
            return back()->withErrors(['api' => $e->getMessage()])->withInput();
        }
    }
}
```

- [ ] Crear `laravel_app/app/Http/Controllers/PedidoController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Services\PedidosService;
use App\Http\Client\ApiException;

class PedidoController extends Controller
{
    public function __construct(private PedidosService $pedidosService) {}

    /** Lista los pedidos del usuario logueado. */
    public function index()
    {
        $usuarioId = session('usuario.id');
        try {
            $pedidos = $this->pedidosService->listarPorUsuario($usuarioId);
        } catch (ApiException $e) {
            $pedidos = [];
        }
        return view('pedidos', compact('pedidos'));
    }

    /** Detalle de un pedido. */
    public function show(int $id)
    {
        try {
            $pedido = $this->pedidosService->obtener($id);
        } catch (ApiException $e) {
            return redirect('/pedidos')->withErrors(['api' => $e->getMessage()]);
        }
        return view('pedido-detalle', compact('pedido'));
    }
}
```

- [ ] Crear `laravel_app/app/Http/Controllers/PerfilController.php`:

```php
<?php

namespace App\Http\Controllers;

class PerfilController extends Controller
{
    /** Muestra el perfil del usuario logueado (datos de session). */
    public function index()
    {
        $usuario = session('usuario');
        return view('perfil', compact('usuario'));
    }
}
```

---

### Task 17: Rutas Laravel + templates Blade

**Files:**
- Modify: `laravel_app/routes/web.php`
- Modify: `laravel_app/resources/views/login.blade.php`
- Modify: `laravel_app/resources/views/register.blade.php`
- Modify: `laravel_app/resources/views/catalogo.blade.php`
- Modify: `laravel_app/resources/views/detalle-producto.blade.php`
- Modify: `laravel_app/resources/views/carrito.blade.php`
- Modify: `laravel_app/resources/views/checkout.blade.php`
- Modify: `laravel_app/resources/views/pedidos.blade.php`
- Modify: `laravel_app/resources/views/pedido-detalle.blade.php`
- Modify: `laravel_app/resources/views/perfil.blade.php`
- Modify: `laravel_app/resources/views/dashboard.blade.php`

#### routes/web.php

- [ ] Leer el archivo actual
- [ ] Reemplazar todo el contenido con:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PerfilController;

// ── Raíz ──────────────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect('/login'));

// ── Autenticación (públicas) ──────────────────────────────────────────────────
Route::get('/login',          [AuthController::class, 'showLogin']);
Route::post('/login',         [AuthController::class, 'login']);
Route::get('/registro',       [AuthController::class, 'showRegistro']);
Route::post('/registro',      [AuthController::class, 'registro']);
Route::get('/logout',         [AuthController::class, 'logout']);
Route::get('/forgot-password', fn() => view('forgot-password'));

// ── Portal del Cliente (protegidas) ───────────────────────────────────────────
Route::middleware('check.session')->group(function () {
    Route::get('/dashboard',           [CatalogoController::class,  'dashboard']);
    Route::get('/catalogo',            [CatalogoController::class,  'index']);
    Route::get('/catalogo/{id}',       [CatalogoController::class,  'show']);

    Route::get('/carrito',             [CarritoController::class,   'index']);
    Route::post('/carrito/agregar',    [CarritoController::class,   'agregar']);
    Route::post('/carrito/actualizar', [CarritoController::class,   'actualizar']);
    Route::get('/checkout',            [CarritoController::class,   'showCheckout']);
    Route::post('/checkout',           [CarritoController::class,   'checkout']);

    Route::get('/pedidos',             [PedidoController::class,    'index']);
    Route::get('/pedido/{id}',         [PedidoController::class,    'show']);

    Route::get('/perfil',              [PerfilController::class,    'index']);
});

// Legacy
Route::get('/register', fn() => redirect('/registro'));
```

#### Blade templates — patrón de actualización

Para cada vista Blade, leer el archivo completo primero y luego reemplazar los datos hardcodeados.

**login.blade.php**
- [ ] Leer el archivo
- [ ] Localizar el `<form>` y cambiar `action` al route: `action="{{ route('login') }}"` o `action="/login"` `method="POST"`
- [ ] Agregar `@csrf` dentro del form
- [ ] Agregar alerta de error bajo el `<form>`:
```blade
@if($errors->has('api'))
    <div class="auth-error" style="color:#C41230;font-size:13px;margin-bottom:12px;">
        {{ $errors->first('api') }}
    </div>
@endif
@if(session('success'))
    <div style="color:#16a34a;font-size:13px;margin-bottom:12px;">{{ session('success') }}</div>
@endif
```

**register.blade.php**
- [ ] Leer el archivo
- [ ] Cambiar form: `action="/registro"` `method="POST"` + `@csrf`
- [ ] Verificar campos: `nombre`, `apellidos`, `email`, `password`
- [ ] Agregar mensajes de error como en login

**catalogo.blade.php**
- [ ] Leer el archivo
- [ ] Eliminar el bloque `@php $productos = [...]` y la variable `$imgs`
- [ ] Reemplazar el `@foreach($productos as $i => $p)` con:
```blade
@forelse($autopartes as $a)
<div class="mac-product-card">
    <a href="/catalogo/{{ $a['id'] }}" style="display:block;">
        <div class="mac-product-card__image">
            @if(!empty($a['imagen']))
                <img src="{{ $a['imagen'] }}" alt="{{ $a['nombre'] }}" loading="lazy">
            @else
                <div style="background:#eee;height:200px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-image" style="font-size:40px;color:#ccc;"></i>
                </div>
            @endif
        </div>
    </a>
    <div class="mac-product-card__body">
        <div class="mac-product-card__sku">SKU: {{ $a['sku'] }} · {{ $a['categoria'] }}</div>
        <a href="/catalogo/{{ $a['id'] }}" style="text-decoration:none;">
            <div class="mac-product-card__name">{{ $a['nombre'] }}</div>
        </a>
        <div style="display:flex;align-items:baseline;gap:10px;">
            <div class="mac-product-card__price">${{ number_format($a['precio'], 2) }}</div>
            @if(!empty($a['precio_original']))
                <div style="font-size:13px;color:var(--macuin-muted);text-decoration:line-through;">
                    ${{ number_format($a['precio_original'], 2) }}
                </div>
            @endif
        </div>
    </div>
    <div class="mac-product-card__footer">
        @php
            $badge = match($a['estado']) {
                'en_stock'   => ['label' => 'Disponible',  'class' => 'mac-badge--available'],
                'bajo_stock' => ['label' => 'Poco stock',  'class' => 'mac-badge--low'],
                default      => ['label' => 'Sin stock',   'class' => 'mac-badge--out'],
            };
        @endphp
        <span class="mac-badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
        <form action="/carrito/agregar" method="POST" style="display:inline;">
            @csrf
            <input type="hidden" name="autoparte_id" value="{{ $a['id'] }}">
            <input type="hidden" name="nombre" value="{{ $a['nombre'] }}">
            <input type="hidden" name="precio" value="{{ $a['precio'] }}">
            <input type="hidden" name="imagen" value="{{ $a['imagen'] ?? '' }}">
            <button type="submit" class="mac-btn mac-btn-primary mac-btn-sm"
                {{ $a['estado'] === 'sin_stock' ? 'disabled' : '' }}>
                <i class="fas fa-cart-plus"></i> Agregar
            </button>
        </form>
    </div>
</div>
@empty
<p style="text-align:center;padding:40px;color:var(--macuin-muted);">Sin productos disponibles.</p>
@endforelse
```
- [ ] Reemplazar el conteo hardcodeado `1–12 de 248` con: `Mostrando <strong>{{ count($autopartes) }}</strong> resultados`

**detalle-producto.blade.php**
- [ ] Leer el archivo completo
- [ ] Reemplazar datos hardcodeados del producto con variables:
```blade
{{ $autoparte['nombre'] }}
{{ $autoparte['sku'] }}
{{ $autoparte['categoria'] }}
{{ $autoparte['marca'] ?? '—' }}
${{ number_format($autoparte['precio'], 2) }}
{{ $autoparte['stock'] }} en stock
{{ $autoparte['descripcion'] ?? '' }}
{{ $autoparte['aplicacion'] ?? '' }}
```
- [ ] Agregar formulario "Agregar al carrito":
```blade
<form action="/carrito/agregar" method="POST">
    @csrf
    <input type="hidden" name="autoparte_id" value="{{ $autoparte['id'] }}">
    <input type="hidden" name="nombre" value="{{ $autoparte['nombre'] }}">
    <input type="hidden" name="precio" value="{{ $autoparte['precio'] }}">
    <input type="hidden" name="imagen" value="{{ $autoparte['imagen'] ?? '' }}">
    <input type="number" name="cantidad" value="1" min="1" max="{{ $autoparte['stock'] }}">
    <button type="submit" class="mac-btn mac-btn-primary">
        <i class="fas fa-cart-plus"></i> Agregar al carrito
    </button>
</form>
```

**carrito.blade.php**
- [ ] Leer el archivo completo
- [ ] Reemplazar items hardcodeados con:
```blade
@forelse($carrito as $id => $item)
<tr>
    <td>{{ $item['nombre'] }}</td>
    <td>${{ number_format($item['precio'], 2) }}</td>
    <td>
        <form action="/carrito/actualizar" method="POST" style="display:inline;">
            @csrf
            <input type="number" name="cantidades[{{ $id }}]"
                   value="{{ $item['cantidad'] }}" min="0" style="width:60px;">
            <button type="submit">Actualizar</button>
        </form>
    </td>
    <td>${{ number_format($item['precio'] * $item['cantidad'], 2) }}</td>
</tr>
@empty
<tr><td colspan="4" style="text-align:center;padding:32px;">El carrito está vacío.</td></tr>
@endforelse
```
- [ ] Reemplazar subtotal hardcodeado: `${{ number_format($subtotal, 2) }}`
- [ ] Agregar mensajes de éxito/error de session

**checkout.blade.php**
- [ ] Leer el archivo completo
- [ ] Cambiar form: `action="/checkout"` `method="POST"` + `@csrf`
- [ ] Mostrar resumen del carrito con `$carrito` y `$subtotal`
- [ ] Asegurar campos: `calle`, `ciudad`, `estado`, `cp`
- [ ] Agregar error de API: `@error('api') <p class="text-red">{{ $message }}</p> @enderror`

**pedidos.blade.php**
- [ ] Leer el archivo completo
- [ ] Reemplazar filas hardcodeadas con:
```blade
@forelse($pedidos as $p)
<tr>
    <td>{{ $p['folio'] }}</td>
    <td>{{ \Carbon\Carbon::parse($p['creado_en'])->format('d/m/Y') }}</td>
    <td>
        @php
            $cls = match($p['estado']) {
                'Completado' => 'mac-badge--available',
                'Cancelado'  => 'mac-badge--out',
                'Pendiente'  => 'mac-badge--low',
                default      => 'mac-badge--neutral',
            };
        @endphp
        <span class="mac-badge {{ $cls }}">{{ $p['estado'] }}</span>
    </td>
    <td>${{ number_format($p['total'], 2) }}</td>
    <td><a href="/pedido/{{ $p['id'] }}" class="mac-btn mac-btn-sm">Ver detalle</a></td>
</tr>
@empty
<tr><td colspan="5" style="text-align:center;padding:32px;color:var(--macuin-muted);">Sin pedidos aún.</td></tr>
@endforelse
```
- [ ] Agregar mensajes flash

**pedido-detalle.blade.php**
- [ ] Leer el archivo completo
- [ ] Reemplazar datos hardcodeados:
```blade
{{ $pedido['folio'] }}
{{ $pedido['estado'] }}
${{ number_format($pedido['subtotal'], 2) }}
${{ number_format($pedido['impuestos'], 2) }}
${{ number_format($pedido['total'], 2) }}

@foreach($pedido['items'] as $d)
<tr>
    <td>{{ $d['nombre'] }}</td>
    <td>{{ $d['sku'] }}</td>
    <td>{{ $d['cantidad'] }}</td>
    <td>${{ number_format($d['precio_unitario'], 2) }}</td>
    <td>${{ number_format($d['subtotal'], 2) }}</td>
</tr>
@endforeach
```

**perfil.blade.php**
- [ ] Leer el archivo completo
- [ ] Reemplazar datos hardcodeados:
```blade
{{ $usuario['nombre'] }} {{ $usuario['apellidos'] }}
{{ $usuario['email'] }}
{{ $usuario['tipo_cliente'] ?? '—' }}
{{ $usuario['empresa'] ?? '—' }}
```

**dashboard.blade.php**
- [ ] Leer el archivo completo
- [ ] Localizar sección de productos destacados y reemplazar con loop real usando `$autopartes`

---

## FASE 3 — Docker

---

### Task 18: Actualizar docker-compose.yml

**Files:**
- Modify: `docker-compose.yml`

- [ ] Leer el archivo actual
- [ ] En el servicio `flask`, agregar `FLASK_SECRET_KEY` al bloque `environment`:
```yaml
flask:
  environment:
    - FLASK_ENV=production
    - API_URL=http://fastapi:8000
    - FLASK_SECRET_KEY=macuin-secret-2026
```

- [ ] El servicio `laravel` ya tiene `API_URL=http://fastapi:8000` — verificar que existe, no cambiar nada más.

- [ ] Verificar que `SESSION_DRIVER=file` está en el bloque environment de laravel (ya está).

---

## Verificación Final

- [ ] Levantar todos los servicios: `docker compose up --build`
- [ ] Flask en `http://localhost:5001`: login con credenciales de usuario interno de la BD
- [ ] Verificar que `/gestion-autopartes` muestra datos reales de la API
- [ ] Verificar que crear/editar/eliminar autoparte funciona
- [ ] Verificar descarga de reportes PDF
- [ ] Laravel en `http://localhost:8080`: registro de nuevo usuario externo
- [ ] Verificar que `/catalogo` muestra productos reales
- [ ] Agregar producto al carrito → checkout → verificar pedido en `/pedidos`
- [ ] Verificar que ninguna ruta usa Eloquent ni PDO directamente
