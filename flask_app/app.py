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


def requiere_permiso(clave):
    """Retorna redirect/render 403 si el usuario no tiene el permiso requerido."""
    r = requiere_sesion()
    if r: return r
    if not session["usuario"].get(clave):
        return render_template("sin_permisos.html"), 403
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
    r = requiere_permiso("perm_autopartes")
    if r: return r
    try:
        autopartes = AutopartesService.listar()
    except ApiException:
        autopartes = []
    return render_template("gestion_autopartes.html", autopartes=autopartes)


@app.route("/agregar-autoparte", methods=["GET"])
def agregar_autoparte_form():
    r = requiere_permiso("perm_autopartes")
    if r: return r
    return render_template("agregar_autoparte.html")


@app.route("/agregar-autoparte", methods=["POST"])
def agregar_autoparte_submit():
    r = requiere_permiso("perm_autopartes")
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
    r = requiere_permiso("perm_autopartes")
    if r: return r
    try:
        autoparte = AutopartesService.obtener(id)
    except ApiException as e:
        flash(e.detail, "error")
        return redirect("/gestion-autopartes")
    return render_template("editar_autoparte.html", autoparte=autoparte)


@app.route("/editar-autoparte/<int:id>", methods=["POST"])
def editar_autoparte_submit(id):
    r = requiere_permiso("perm_autopartes")
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
    r = requiere_permiso("perm_autopartes")
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
    r = requiere_permiso("perm_pedidos")
    if r: return r
    estado = request.args.get("estado")
    try:
        pedidos = PedidosService.listar(estado)
    except ApiException:
        pedidos = []
    return render_template("gestion_pedidos.html", pedidos=pedidos)


@app.route("/detalle-pedido/<int:id>", methods=["GET"])
def detalle_pedido(id):
    r = requiere_permiso("perm_pedidos")
    if r: return r
    try:
        pedido = PedidosService.obtener(id)
        cliente = UsuariosService.obtener_externo(pedido["usuario_externo_id"])
    except ApiException as e:
        flash(e.detail, "error")
        return redirect("/gestion-pedidos")
    return render_template("detalle_pedido.html", pedido=pedido, cliente=cliente)


@app.route("/pedidos/<int:id>/estado", methods=["POST"])
def cambiar_estado_pedido(id):
    r = requiere_permiso("perm_pedidos")
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
    r = requiere_permiso("perm_usuarios")
    if r: return r
    try:
        usuarios = sorted(UsuariosService.listar_internos(), key=lambda u: u.get('id', 0))
    except ApiException:
        usuarios = []
    try:
        total_externos = len(UsuariosService.listar_externos())
    except ApiException:
        total_externos = 0
    total    = len(usuarios)
    activos  = sum(1 for u in usuarios if u.get('estado', '').lower() == 'activo')
    inactivos = total - activos
    return render_template("gestion_usuarios_internos.html",
                           usuarios=usuarios,
                           total=total, activos=activos, inactivos=inactivos,
                           total_externos=total_externos)


@app.route("/agregar-usuario-interno", methods=["GET"])
def agregar_usuario_interno_form():
    r = requiere_permiso("perm_usuarios")
    if r: return r
    return render_template("agregar_usuario_interno.html")


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


@app.route("/editar-usuario-interno/<int:id>", methods=["GET"])
def editar_usuario_interno_form(id):
    r = requiere_permiso("perm_usuarios")
    if r: return r
    try:
        usuario = UsuariosService.obtener_interno(id)
    except ApiException as e:
        flash(e.detail, "error")
        return redirect("/gestion-usuarios-internos")
    return render_template("editar_usuario_interno.html", usuario=usuario)


@app.route("/editar-usuario-interno/<int:id>", methods=["POST"])
def editar_usuario_interno_submit(id):
    r = requiere_permiso("perm_usuarios")
    if r: return r
    data = request.form.to_dict()
    # Si password está vacío, quitarlo del payload (no cambiar contraseña)
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


@app.route("/desactivar-usuario-interno/<int:id>")
def desactivar_usuario_interno(id):
    r = requiere_permiso("perm_usuarios")
    if r: return r
    try:
        UsuariosService.patch_interno(id, {"estado": "inactivo"})
        flash("Cuenta de usuario desactivada.", "success")
    except ApiException as e:
        flash(e.detail, "error")
    return redirect("/gestion-usuarios-internos")


@app.route("/eliminar-usuario-interno/<int:id>")
def eliminar_usuario_interno(id):
    r = requiere_permiso("perm_usuarios")
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
    r = requiere_permiso("perm_usuarios")
    if r: return r
    try:
        usuarios = sorted(UsuariosService.listar_externos(), key=lambda u: u.get('id', 0))
    except ApiException:
        usuarios = []
    total      = len(usuarios)
    activos    = sum(1 for u in usuarios if u.get('estado', '').lower() == 'activo')
    pendientes = sum(1 for u in usuarios if u.get('estado', '').lower() == 'pendiente')
    inactivos  = sum(1 for u in usuarios if u.get('estado', '').lower() == 'inactivo')
    return render_template("gestion_usuarios_externos.html",
                           usuarios=usuarios,
                           total=total, activos=activos,
                           pendientes=pendientes, inactivos=inactivos)


@app.route("/agregar-usuario-externo", methods=["GET"])
def agregar_usuario_externo_form():
    r = requiere_permiso("perm_usuarios")
    if r: return r
    return render_template("agregar_usuario_externo.html")


@app.route("/agregar-usuario-externo", methods=["POST"])
def agregar_usuario_externo_submit():
    r = requiere_permiso("perm_usuarios")
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
    r = requiere_permiso("perm_usuarios")
    if r: return r
    try:
        usuario = UsuariosService.obtener_externo(id)
    except ApiException as e:
        flash(e.detail, "error")
        return redirect("/gestion-usuarios-externos")
    try:
        pedidos_resp = ApiClient.get(f"/v1/pedidos/usuario/{id}")
        total_pedidos = pedidos_resp.get("total", 0)
    except Exception:
        total_pedidos = 0
    return render_template("editar_usuario_externo.html", usuario=usuario, total_pedidos=total_pedidos)


@app.route("/editar-usuario-externo/<int:id>", methods=["POST"])
def editar_usuario_externo_submit(id):
    r = requiere_permiso("perm_usuarios")
    if r: return r
    data = request.form.to_dict()
    try:
        descuento = float(data.get("descuento", 0))
        if descuento > 100:
            flash("El descuento no puede ser mayor a 100%.", "error")
            return redirect(f"/editar-usuario-externo/{id}")
    except (ValueError, TypeError):
        flash("El descuento debe ser un número válido.", "error")
        return redirect(f"/editar-usuario-externo/{id}")
    try:
        UsuariosService.editar_externo(id, data)
        flash("Usuario externo actualizado.", "success")
        return redirect("/gestion-usuarios-externos")
    except ApiException as e:
        flash(e.detail, "error")
        return redirect(f"/editar-usuario-externo/{id}")


@app.route("/desactivar-usuario-externo/<int:id>")
def desactivar_usuario_externo(id):
    r = requiere_permiso("perm_usuarios")
    if r: return r
    try:
        UsuariosService.patch_externo(id, {"estado": "inactivo"})
        flash("Cuenta de cliente desactivada.", "success")
    except ApiException as e:
        flash(e.detail, "error")
    return redirect("/gestion-usuarios-externos")


@app.route("/eliminar-usuario-externo/<int:id>")
def eliminar_usuario_externo(id):
    r = requiere_permiso("perm_usuarios")
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
    r = requiere_permiso("perm_reportes")
    if r: return r
    
    # Obtener filtros de la URL
    fecha_inicio  = request.args.get("fecha_inicio")
    fecha_fin     = request.args.get("fecha_fin")
    estado        = request.args.get("estado")
    categoria     = request.args.get("categoria")
    tipo_cliente  = request.args.get("tipo_cliente")
    solo_alertas  = request.args.get("solo_alertas", "false")

    auth_macuin = ("macuin", "123456")

    params_ventas = {k: v for k, v in {
        "fecha_inicio": fecha_inicio, "fecha_fin": fecha_fin, "categoria": categoria
    }.items() if v}

    params_inventario = {k: v for k, v in {
        "categoria": categoria, "solo_alertas": solo_alertas
    }.items() if v and v != "false"}

    params_pedidos = {k: v for k, v in {
        "fecha_inicio": fecha_inicio, "fecha_fin": fecha_fin, "estado": estado
    }.items() if v}

    params_usuarios = {k: v for k, v in {
        "tipo_cliente": tipo_cliente, "estado": estado
    }.items() if v}

    try:
        datos_ventas = ApiClient.get("/v1/reportes/datos/ventas", params=params_ventas, auth=auth_macuin)["data"]
    except Exception:
        datos_ventas = None

    try:
        datos_inventario = ApiClient.get("/v1/reportes/datos/inventario", params=params_inventario, auth=auth_macuin)["data"]
    except Exception:
        datos_inventario = None

    try:
        datos_pedidos = ApiClient.get("/v1/reportes/datos/pedidos", params=params_pedidos, auth=auth_macuin)["data"]
    except Exception:
        datos_pedidos = None

    try:
        datos_usuarios = ApiClient.get("/v1/reportes/datos/usuarios", params=params_usuarios, auth=auth_macuin)["data"]
    except Exception:
        datos_usuarios = None

    return render_template(
        "reportes.html",
        ventas=datos_ventas,
        inventario=datos_inventario,
        pedidos=datos_pedidos,
        usuarios=datos_usuarios,
        filtro_inicio=fecha_inicio,
        filtro_fin=fecha_fin,
        filtro_estado=estado,
        filtro_categoria=categoria,
        filtro_tipo_cliente=tipo_cliente,
        filtro_solo_alertas=solo_alertas,
    )


@app.route("/reportes/descargar/<tipo>/<formato>")
def descargar_reporte(tipo, formato):
    r = requiere_permiso("perm_reportes")
    if r: return r
    # Pasar todos los query params al endpoint de FastAPI
    params = {k: v for k, v in {
        "fecha_inicio": request.args.get("fecha_inicio"),
        "fecha_fin":    request.args.get("fecha_fin"),
        "estado":       request.args.get("estado"),
        "categoria":    request.args.get("categoria"),
        "tipo_cliente": request.args.get("tipo_cliente"),
        "solo_alertas": request.args.get("solo_alertas"),
    }.items() if v}
    try:
        resp = ApiClient.get_raw(
            f"/v1/reportes/{tipo}/{formato}",
            auth=("macuin", "123456"),
            params=params or None,
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
