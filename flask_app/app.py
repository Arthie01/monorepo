"""
Flask App — punto de entrada principal.
Entorno base sin interfaces ni lógica de negocio.
"""

from flask import Flask, jsonify, render_template

app = Flask(__name__)


@app.route("/", methods=["GET"])
def health_check():
    """Endpoint raíz de verificación de estado."""
    return jsonify({"status": "OK"}), 200


@app.route("/login", methods=["GET"])
def login():
    """Pantalla de inicio de sesión (solo UI)."""
    return render_template("login.html")


@app.route("/gestion-autopartes", methods=["GET"])
def gestion_autopartes():
    """Pantalla de gestión de autopartes (solo UI)."""
    return render_template("gestion_autopartes.html")


@app.route("/gestion-pedidos", methods=["GET"])
def gestion_pedidos():
    """Pantalla de gestión de pedidos (solo UI)."""
    return render_template("gestion_pedidos.html")


@app.route("/editar-autoparte", methods=["GET"])
def editar_autoparte():
    """Pantalla de edición de autoparte (solo UI)."""
    return render_template("editar_autoparte.html")


@app.route("/agregar-autoparte", methods=["GET"])
def agregar_autoparte():
    """Pantalla de registro de nueva autoparte (solo UI)."""
    return render_template("agregar_autoparte.html")


@app.route("/detalle-pedido", methods=["GET"])
def detalle_pedido():
    """Pantalla de detalle de pedido (solo UI)."""
    return render_template("detalle_pedido.html")


@app.route("/reportes", methods=["GET"])
def reportes():
    """Pantalla de reportes y analíticas (solo UI)."""
    return render_template("reportes.html")


@app.route("/perfil", methods=["GET"])
def perfil():
    """Pantalla de perfil del usuario interno (solo UI)."""
    return render_template("perfil.html")


@app.route("/gestion-usuarios-internos", methods=["GET"])
def gestion_usuarios_internos():
    """Pantalla de gestión de usuarios internos (solo UI)."""
    return render_template("gestion_usuarios_internos.html")


@app.route("/gestion-usuarios-externos", methods=["GET"])
def gestion_usuarios_externos():
    """Pantalla de gestión de usuarios externos (solo UI)."""
    return render_template("gestion_usuarios_externos.html")


@app.route("/editar-usuario-interno", methods=["GET"])
def editar_usuario_interno():
    """Pantalla de edición de usuario interno (solo UI)."""
    return render_template("editar_usuario_interno.html")


@app.route("/editar-usuario-externo", methods=["GET"])
def editar_usuario_externo():
    """Pantalla de edición de usuario externo (solo UI)."""
    return render_template("editar_usuario_externo.html")


@app.route("/agregar-usuario-interno", methods=["GET"])
def agregar_usuario_interno():
    """Pantalla de registro de usuario interno (solo UI)."""
    return render_template("agregar_usuario_interno.html")


@app.route("/agregar-usuario-externo", methods=["GET"])
def agregar_usuario_externo():
    """Pantalla de registro de usuario externo (solo UI)."""
    return render_template("agregar_usuario_externo.html")


if __name__ == "__main__":
    app.run(debug=True)
