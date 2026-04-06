from sqlalchemy import Column, Integer, Numeric, ForeignKey, UniqueConstraint
from app.data.db import Base


class DetallePedido(Base):
    __tablename__ = "tb_detalle_pedido"

    id              = Column(Integer, primary_key=True, index=True)
    pedido_id       = Column(Integer, ForeignKey("tb_pedidos.id", ondelete="CASCADE"), nullable=False)
    autoparte_id    = Column(Integer, ForeignKey("tb_autopartes.id"), nullable=False)
    cantidad        = Column(Integer, nullable=False)
    precio_unitario = Column(Numeric(10, 2), nullable=False)
    # subtotal se calcula en la aplicacion (PostgreSQL GENERATED ALWAYS AS no es soportado por SQLAlchemy ORM directamente)

    __table_args__ = (
        UniqueConstraint("pedido_id", "autoparte_id", name="uq_pedido_autoparte"),
    )
