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
