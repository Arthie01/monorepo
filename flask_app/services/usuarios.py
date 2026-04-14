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


def patch_interno(id: int, campos: dict) -> dict:
    return ApiClient.patch(f"/v1/usuarios/internos/{id}", json=campos)


def eliminar_interno(id: int) -> dict:
    return ApiClient.delete(f"/v1/usuarios/internos/{id}")


# ── Usuarios Externos ──────────────────────────────────────────────────────────

def listar_externos(estado: str = None) -> list:
    params = {"estado": estado} if estado else None
    return ApiClient.get("/v1/usuarios/externos/", params=params)["data"]


def obtener_externo(id: int) -> dict:
    return ApiClient.get(f"/v1/usuarios/externos/{id}")["data"]


def crear_externo(data: dict) -> dict:
    return ApiClient.post("/v1/usuarios/externos/", json=data)


def editar_externo(id: int, data: dict) -> dict:
    """PUT — actualización completa."""
    return ApiClient.put(f"/v1/usuarios/externos/{id}", json=data)


def patch_externo(id: int, campos: dict) -> dict:
    return ApiClient.patch(f"/v1/usuarios/externos/{id}", json=campos)


def eliminar_externo(id: int) -> dict:
    return ApiClient.delete(f"/v1/usuarios/externos/{id}")
