from sqlalchemy import Column, Integer, String, Numeric, Text, Boolean
from app.data.db import Base


class Autoparte(Base):
    __tablename__ = "tb_autopartes"

    id              = Column(Integer, primary_key=True, index=True)
    nombre          = Column(String(150), nullable=False)
    sku             = Column(String(50), nullable=False, unique=True)
    categoria       = Column(String(50), nullable=False)
    marca           = Column(String(80))

    precio          = Column(Numeric(10, 2), nullable=False)
    precio_original = Column(Numeric(10, 2))

    stock           = Column(Integer, nullable=False, default=0)
    stock_minimo    = Column(Integer, nullable=False, default=0)
    unidad          = Column(String(20), nullable=False, default="Pieza")
    ubicacion       = Column(String(50))

    marca_vehiculo  = Column(String(100))
    modelo_vehiculo = Column(String(200))
    aplicacion      = Column(Text)

    descripcion     = Column(Text)
    imagen          = Column(String(255))
    notas           = Column(Text)

    estado          = Column(String(20), nullable=False, default="en_stock")
    activo          = Column(Boolean, nullable=False, default=True)
