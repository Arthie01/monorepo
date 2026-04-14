from pydantic import BaseModel, Field
from typing import Optional, List


class ItemPedido(BaseModel):
    autoparte_id: int = Field(..., gt=0)
    cantidad:     int = Field(..., ge=1)


class Crear_Pedido(BaseModel):
    usuario_externo_id: int              = Field(..., gt=0)
    metodo_pago:        str              = Field(..., pattern="^(tarjeta|transferencia|credito_macuin)$")
    metodo_envio:       str              = Field("estandar", pattern="^(express|estandar|recoger)$")
    notas:              Optional[str]    = None
    items:              List[ItemPedido] = Field(..., min_length=1)
    dir_calle:          Optional[str]    = None
    dir_ciudad:         Optional[str]    = None
    dir_estado:         Optional[str]    = None
    dir_cp:             Optional[str]    = None


class CambiarEstado(BaseModel):
    estado: str = Field(...)
