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


if __name__ == "__main__":
    app.run(debug=True)
