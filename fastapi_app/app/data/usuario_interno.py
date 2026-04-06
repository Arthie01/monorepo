from sqlalchemy import Column, Integer, String, Boolean, TIMESTAMP
from sqlalchemy.sql import func
from app.data.db import Base


class UsuarioInterno(Base):
    __tablename__ = "tb_usuarios_internos"

    id                = Column(Integer, primary_key=True, index=True)
    nombre            = Column(String(50), nullable=False)
    apellidos         = Column(String(100), nullable=False)
    email             = Column(String(120), nullable=False, unique=True)
    password          = Column(String(255), nullable=False)
    telefono          = Column(String(15))

    departamento      = Column(String(30), nullable=False, default="Ventas")
    rol               = Column(String(30), nullable=False, default="Ventas")
    cargo             = Column(String(80))
    sucursal          = Column(String(80))

    perm_autopartes   = Column(Boolean, nullable=False, default=False)
    perm_pedidos      = Column(Boolean, nullable=False, default=False)
    perm_usuarios     = Column(Boolean, nullable=False, default=False)
    perm_reportes     = Column(Boolean, nullable=False, default=False)
    perm_config       = Column(Boolean, nullable=False, default=False)

    estado            = Column(String(20), nullable=False, default="activo")
    ultima_actividad  = Column(TIMESTAMP)
    creado_en         = Column(TIMESTAMP, nullable=False, server_default=func.now())
