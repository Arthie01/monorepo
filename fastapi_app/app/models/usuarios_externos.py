from pydantic import BaseModel, Field
from typing import Optional
from decimal import Decimal


class Crear_UsuarioExterno(BaseModel):
    nombre:         str = Field(..., min_length=2, max_length=50,  description="Nombre del cliente")
    apellidos:      str = Field(..., min_length=2, max_length=100, description="Apellidos del cliente")
    email:          str = Field(..., min_length=5, max_length=120, description="Correo electrónico único")
    password:       str = Field(..., min_length=4, max_length=255, description="Contraseña")

    tipo_cliente:   Optional[str]     = Field("Particular",       max_length=30,  description="Ej: Particular, Taller, Refaccionaria")
    lista_precio:   Optional[str]     = Field("Público general",  max_length=30,  description="Lista de precios asignada")
    dias_credito:   Optional[int]     = Field(0,                                  description="Días de crédito otorgados")
    limite_credito: Optional[Decimal] = Field(Decimal("0"),                       description="Límite de crédito en MXN")
    descuento:      Optional[Decimal] = Field(Decimal("0"),                       description="Porcentaje de descuento aplicado")
    estado:         Optional[str]     = Field("activo",           max_length=20,  description="activo / inactivo")

    telefono:       Optional[str]     = Field(None, max_length=15)
    empresa:        Optional[str]     = Field(None, max_length=150)
    rfc:            Optional[str]     = Field(None, max_length=13)
    giro:           Optional[str]     = Field(None, max_length=60)
    calle:          Optional[str]     = Field(None, max_length=200)
    ciudad:         Optional[str]     = Field(None, max_length=100)
    estado_geo:     Optional[str]     = Field(None, max_length=5)
    cp:             Optional[str]     = Field(None, max_length=5)
    referencia:     Optional[str]     = Field(None, max_length=200)
    notas:          Optional[str]     = None


class Actualizar_UsuarioExterno(BaseModel):
    nombre:         Optional[str]     = Field(None, min_length=2, max_length=50)
    apellidos:      Optional[str]     = Field(None, min_length=2, max_length=100)
    email:          Optional[str]     = Field(None, min_length=5, max_length=120)
    password:       Optional[str]     = Field(None, min_length=4, max_length=255)

    tipo_cliente:   Optional[str]     = Field(None, max_length=30)
    lista_precio:   Optional[str]     = Field(None, max_length=30)
    dias_credito:   Optional[int]     = None
    limite_credito: Optional[Decimal] = None
    descuento:      Optional[Decimal] = None
    estado:         Optional[str]     = Field(None, max_length=20)

    telefono:       Optional[str]     = Field(None, max_length=15)
    empresa:        Optional[str]     = Field(None, max_length=150)
    rfc:            Optional[str]     = Field(None, max_length=13)
    giro:           Optional[str]     = Field(None, max_length=60)
    calle:          Optional[str]     = Field(None, max_length=200)
    ciudad:         Optional[str]     = Field(None, max_length=100)
    estado_geo:     Optional[str]     = Field(None, max_length=5)
    cp:             Optional[str]     = Field(None, max_length=5)
    referencia:     Optional[str]     = Field(None, max_length=200)
    notas:          Optional[str]     = None


class PatchUsuarioExterno(BaseModel):
    tipo_cliente:   Optional[str]     = Field(None, max_length=30,  description="Ej: Particular, Taller, Refaccionaria")
    empresa:        Optional[str]     = Field(None, max_length=150, description="Nombre de la empresa")
    lista_precio:   Optional[str]     = Field(None, max_length=30,  description="Lista de precios asignada")
    dias_credito:   Optional[int]     = None
    limite_credito: Optional[Decimal] = None
    descuento:      Optional[Decimal] = None
    estado:         Optional[str]     = Field(None, max_length=20,  description="activo / inactivo")
