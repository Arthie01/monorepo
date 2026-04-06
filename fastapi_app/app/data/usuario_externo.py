from sqlalchemy import Column, Integer, String, Numeric, Text, TIMESTAMP
from sqlalchemy.sql import func
from app.data.db import Base


class UsuarioExterno(Base):
    __tablename__ = "tb_usuarios_externos"

    id             = Column(Integer, primary_key=True, index=True)
    nombre         = Column(String(50), nullable=False)
    apellidos      = Column(String(100), nullable=False)
    email          = Column(String(120), nullable=False, unique=True)
    password       = Column(String(255), nullable=False)
    telefono       = Column(String(15))

    empresa        = Column(String(150))
    tipo_cliente   = Column(String(30), default="Particular")
    rfc            = Column(String(13))
    giro           = Column(String(60))

    calle          = Column(String(200))
    ciudad         = Column(String(100))
    estado_geo     = Column(String(5))
    cp             = Column(String(5))
    referencia     = Column(String(200))

    lista_precio   = Column(String(30), default="Público general")
    dias_credito   = Column(Integer, nullable=False, default=0)
    limite_credito = Column(Numeric(10, 2), nullable=False, default=0)
    descuento      = Column(Numeric(5, 2), nullable=False, default=0)

    notas          = Column(Text)
    estado         = Column(String(20), nullable=False, default="activo")
    creado_en      = Column(TIMESTAMP, nullable=False, server_default=func.now())
