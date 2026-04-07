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
