from sqlalchemy import Column, Integer, String, Numeric, ForeignKey, TIMESTAMP, Text
from sqlalchemy.sql import func
from app.data.db import Base


class Pedido(Base):
    __tablename__ = "tb_pedidos"

    id                 = Column(Integer, primary_key=True, index=True)
    folio              = Column(String(20), unique=True)

    usuario_externo_id = Column(Integer, ForeignKey("tb_usuarios_externos.id"), nullable=False)
    usuario_interno_id = Column(Integer, ForeignKey("tb_usuarios_internos.id"))

    estado             = Column(String(20), nullable=False, default="Pendiente")

    subtotal           = Column(Numeric(10, 2), nullable=False, default=0)
    envio              = Column(Numeric(10, 2), nullable=False, default=0)
    impuestos          = Column(Numeric(10, 2), nullable=False, default=0)
    total              = Column(Numeric(10, 2), nullable=False, default=0)

    dir_calle          = Column(String(200))
    dir_ciudad         = Column(String(100))
    dir_estado         = Column(String(5))
    dir_cp             = Column(String(5))

    metodo_envio       = Column(String(30), nullable=False, default="estandar")
    notas              = Column(Text, nullable=True)

    creado_en          = Column(TIMESTAMP, nullable=False, server_default=func.now())
