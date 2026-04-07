from pydantic import BaseModel, Field
from typing import Optional


class Crear_UsuarioInterno(BaseModel):
    nombre:       str = Field(..., min_length=2, max_length=50,  description="Nombre del empleado")
    apellidos:    str = Field(..., min_length=2, max_length=100, description="Apellidos del empleado")
    email:        str = Field(..., min_length=5, max_length=120, description="Correo institucional único")
    password:     str = Field(..., min_length=4, max_length=255, description="Contraseña")
    departamento: str = Field(..., max_length=30, description="Ej: Ventas, Almacén, Logística, Administración")
    rol:          str = Field(..., max_length=30, description="Ej: Administrador, Ventas, Almacén, Logística")


class Actualizar_UsuarioInterno(BaseModel):
    nombre:           Optional[str]  = Field(None, min_length=2, max_length=50)
    apellidos:        Optional[str]  = Field(None, min_length=2, max_length=100)
    email:            Optional[str]  = Field(None, min_length=5, max_length=120)
    password:         Optional[str]  = Field(None, min_length=4, max_length=255)
    telefono:         Optional[str]  = Field(None, max_length=15)
    departamento:     Optional[str]  = Field(None, max_length=30)
    rol:              Optional[str]  = Field(None, max_length=30)
    cargo:            Optional[str]  = Field(None, max_length=80)
    sucursal:         Optional[str]  = Field(None, max_length=80)
    perm_autopartes:  Optional[bool] = None
    perm_pedidos:     Optional[bool] = None
    perm_usuarios:    Optional[bool] = None
    perm_reportes:    Optional[bool] = None
    perm_config:      Optional[bool] = None
    estado:           Optional[str]  = Field(None, max_length=20)
