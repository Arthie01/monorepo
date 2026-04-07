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
        params={"email": email, "password": password},
    )
    return resp["data"]
