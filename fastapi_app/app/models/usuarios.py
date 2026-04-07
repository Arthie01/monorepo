from pydantic import BaseModel, Field
from typing import Optional


class Crear_UsuarioExterno(BaseModel):
    nombre:    str = Field(..., min_length=2, max_length=50,  description="Nombre del cliente")
    apellidos: str = Field(..., min_length=2, max_length=100, description="Apellidos del cliente")
    email:     str = Field(..., min_length=5, max_length=120, description="Correo electrónico único")
    password:  str = Field(..., min_length=4, max_length=255, description="Contraseña")


class Actualizar_UsuarioExterno(BaseModel):
    nombre:        Optional[str]   = Field(None, min_length=2, max_length=50)
    apellidos:     Optional[str]   = Field(None, min_length=2, max_length=100)
    email:         Optional[str]   = Field(None, min_length=5, max_length=120)
    password:      Optional[str]   = Field(None, min_length=4, max_length=255)
    telefono:      Optional[str]   = Field(None, max_length=15)
    empresa:       Optional[str]   = Field(None, max_length=150)
    tipo_cliente:  Optional[str]   = Field(None, max_length=30)
    rfc:           Optional[str]   = Field(None, max_length=13)
    giro:          Optional[str]   = Field(None, max_length=60)
    calle:         Optional[str]   = Field(None, max_length=200)
    ciudad:        Optional[str]   = Field(None, max_length=100)
    estado_geo:    Optional[str]   = Field(None, max_length=5)
    cp:            Optional[str]   = Field(None, max_length=5)
    referencia:    Optional[str]   = Field(None, max_length=200)
    lista_precio:  Optional[str]   = Field(None, max_length=30)
    dias_credito:  Optional[int]   = Field(None, ge=0)
    limite_credito: Optional[float] = Field(None, ge=0)
    descuento:     Optional[float]  = Field(None, ge=0, le=100)
    notas:         Optional[str]   = None
    estado:        Optional[str]   = Field(None, max_length=20)
