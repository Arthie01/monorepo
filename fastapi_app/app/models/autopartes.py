from typing import Optional
from pydantic import BaseModel, Field


class Crear_Autoparte(BaseModel):
    nombre: str = Field(...)
    sku: str = Field(...)
    categoria: str = Field(...)
    precio: float = Field(...)

    stock: int = Field(0)
    stock_minimo: int = Field(0)
    unidad: str = Field("Pieza")
    estado: str = Field("en_stock")
    activo: bool = Field(True)

    marca: Optional[str] = None
    precio_original: Optional[float] = None
    ubicacion: Optional[str] = None
    marca_vehiculo: Optional[str] = None
    modelo_vehiculo: Optional[str] = None
    aplicacion: Optional[str] = None
    descripcion: Optional[str] = None
    notas: Optional[str] = None
    imagen: Optional[str] = None

    class Config:
        from_attributes = True


class Actualizar_Autoparte(BaseModel):
    nombre: Optional[str] = None
    sku: Optional[str] = None
    categoria: Optional[str] = None
    precio: Optional[float] = None

    stock: Optional[int] = None
    stock_minimo: Optional[int] = None
    unidad: Optional[str] = None
    estado: Optional[str] = None
    activo: Optional[bool] = None

    marca: Optional[str] = None
    precio_original: Optional[float] = None
    ubicacion: Optional[str] = None
    marca_vehiculo: Optional[str] = None
    modelo_vehiculo: Optional[str] = None
    aplicacion: Optional[str] = None
    descripcion: Optional[str] = None
    notas: Optional[str] = None
    imagen: Optional[str] = None

    class Config:
        from_attributes = True


class PatchAutoparte(BaseModel):
    precio: Optional[float] = None
    stock: Optional[int] = None
    stock_minimo: Optional[int] = None
    estado: Optional[str] = None
    ubicacion: Optional[str] = None

    class Config:
        from_attributes = True
