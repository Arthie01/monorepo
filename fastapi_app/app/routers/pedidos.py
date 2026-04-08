import uuid
from datetime import datetime
from typing import Optional

from fastapi import APIRouter, HTTPException, Depends, status
from sqlalchemy.orm import Session

from app.data.db import get_db
from app.data.pedido import Pedido
from app.data.detalle_pedido import DetallePedido
from app.data.autoparte import Autoparte
from app.data.usuario_externo import UsuarioExterno
from app.models.pedidos import Crear_Pedido, CambiarEstado

router = APIRouter(
    prefix="/v1/pedidos",
    tags=["Pedidos"]
)

ESTADOS_VALIDOS = ["Pendiente", "En proceso", "Enviado", "Completado", "Cancelado"]


# 1. GET / — Lista todos los pedidos
@router.get("/", status_code=status.HTTP_200_OK)
async def consultar_todos(estado: Optional[str] = None, db: Session = Depends(get_db)):
    query = db.query(Pedido).outerjoin(UsuarioExterno, Pedido.usuario_externo_id == UsuarioExterno.id)
    if estado:
        query = query.filter(Pedido.estado.ilike(estado))
    
    # Ordenar por fecha de creación descendente (más recientes primero)
    query = query.order_by(Pedido.creado_en.desc())
    pedidos = query.all()
    
    # Agregar nombre del cliente y cantidad de items
    result = []
    for p in pedidos:
        usuario = db.query(UsuarioExterno).filter(UsuarioExterno.id == p.usuario_externo_id).first()
        # Contar items en el pedido
        items_count = db.query(DetallePedido).filter(DetallePedido.pedido_id == p.id).count()
        
        pedido_dict = {
            "id": p.id,
            "folio": p.folio,
            "usuario_externo_id": p.usuario_externo_id,
            "usuario_interno_id": p.usuario_interno_id,
            "estado": p.estado,
            "subtotal": float(p.subtotal),
            "envio": float(p.envio),
            "impuestos": float(p.impuestos),
            "total": float(p.total),
            "creado_en": str(p.creado_en),
            "dir_calle": p.dir_calle,
            "dir_ciudad": p.dir_ciudad,
            "dir_estado": p.dir_estado,
            "dir_cp": p.dir_cp,
            "cliente_nombre": f"{usuario.nombre} {usuario.apellidos}" if usuario else "—",
            "items_count": items_count
        }
        result.append(pedido_dict)
    
    return {
        "status": "200",
        "total":  len(result),
        "data":   result
    }


# 2. GET /usuario/{usuario_id} — Pedidos de un usuario externo
@router.get("/usuario/{usuario_id}", status_code=status.HTTP_200_OK)
async def consultar_por_usuario(usuario_id: int, db: Session = Depends(get_db)):
    usuario = db.query(UsuarioExterno).filter(UsuarioExterno.id == usuario_id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail=f"Usuario externo con id {usuario_id} no encontrado")

    pedidos = db.query(Pedido).filter(Pedido.usuario_externo_id == usuario_id).all()
    return {
        "status": "200",
        "total":  len(pedidos),
        "data":   pedidos
    }


# 3. GET /{pedido_id} — Detalle de pedido con líneas
@router.get("/{pedido_id}", status_code=status.HTTP_200_OK)
async def consultar_uno(pedido_id: int, db: Session = Depends(get_db)):
    pedido = db.query(Pedido).filter(Pedido.id == pedido_id).first()
    if not pedido:
        raise HTTPException(status_code=404, detail=f"Pedido con id {pedido_id} no encontrado")

    detalles = db.query(DetallePedido).filter(DetallePedido.pedido_id == pedido_id).all()

    items = []
    for detalle in detalles:
        autoparte = db.query(Autoparte).filter(Autoparte.id == detalle.autoparte_id).first()
        precio_u  = float(detalle.precio_unitario)
        items.append({
            "autoparte_id":    detalle.autoparte_id,
            "nombre":          autoparte.nombre   if autoparte else None,
            "sku":             autoparte.sku      if autoparte else None,
            "imagen":          autoparte.imagen   if autoparte else None,
            "cantidad":        detalle.cantidad,
            "precio_unitario": precio_u,
            "subtotal":        round(precio_u * detalle.cantidad, 2)
        })

    return {
        "status": "200",
        "data": {
            "id":                pedido.id,
            "folio":             pedido.folio,
            "usuario_externo_id": pedido.usuario_externo_id,
            "estado":            pedido.estado,
            "subtotal":          float(pedido.subtotal),
            "impuestos":         float(pedido.impuestos),
            "envio":             float(pedido.envio),
            "total":             float(pedido.total),
            "dir_calle":         pedido.dir_calle,
            "dir_ciudad":        pedido.dir_ciudad,
            "dir_estado":        pedido.dir_estado,
            "dir_cp":            pedido.dir_cp,
            "creado_en":         pedido.creado_en,
            "items":             items
        }
    }


# 4. POST / — Crear pedido
@router.post("/", status_code=status.HTTP_201_CREATED)
async def crear(data: Crear_Pedido, db: Session = Depends(get_db)):
    # 1. Validar usuario existe
    usuario = db.query(UsuarioExterno).filter(UsuarioExterno.id == data.usuario_externo_id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail="Usuario no encontrado")

    # 2. Validar cada autoparte y stock (carga anticipada para reutilizar en paso 6)
    autopartes_map = {}
    for item in data.items:
        autoparte = db.query(Autoparte).filter(Autoparte.id == item.autoparte_id).first()
        if not autoparte:
            raise HTTPException(status_code=404, detail=f"Autoparte {item.autoparte_id} no encontrada")
        if autoparte.stock < item.cantidad:
            raise HTTPException(
                status_code=400,
                detail=f"Stock insuficiente para '{autoparte.nombre}': disponible {autoparte.stock}, solicitado {item.cantidad}"
            )
        autopartes_map[item.autoparte_id] = autoparte

    # 3. Calcular totales con descuento del usuario
    subtotal_bruto  = sum(float(autopartes_map[item.autoparte_id].precio) * item.cantidad for item in data.items)
    descuento_pct   = float(usuario.descuento)            # 0-100
    subtotal_desc   = subtotal_bruto * (1 - descuento_pct / 100)
    impuestos       = round(subtotal_desc * 0.16, 2)
    envio           = 0.0
    total           = round(subtotal_desc + impuestos + envio, 2)
    subtotal_final  = round(subtotal_desc, 2)

    # 4. Generar folio único
    folio = f"MACUIN-{datetime.now().year}-{uuid.uuid4().hex[:8].upper()}"

    # 5. Crear Pedido
    nuevo_pedido = Pedido(
        folio              = folio,
        usuario_externo_id = data.usuario_externo_id,
        estado             = "Pendiente",
        subtotal           = subtotal_final,
        impuestos          = impuestos,
        envio              = envio,
        total              = total,
        dir_calle          = data.dir_calle,
        dir_ciudad         = data.dir_ciudad,
        dir_estado         = data.dir_estado,
        dir_cp             = data.dir_cp
    )
    db.add(nuevo_pedido)
    db.flush()  # obtener id sin commit

    # 6. Crear DetallePedido y descontar stock
    for item in data.items:
        autoparte = autopartes_map[item.autoparte_id]
        db.add(DetallePedido(
            pedido_id       = nuevo_pedido.id,
            autoparte_id    = item.autoparte_id,
            cantidad        = item.cantidad,
            precio_unitario = float(autoparte.precio)
        ))
        autoparte.stock -= item.cantidad

    db.commit()
    db.refresh(nuevo_pedido)

    return {
        "status":  "201",
        "mensaje": "Pedido creado",
        "folio":   nuevo_pedido.folio,
        "total":   float(nuevo_pedido.total),
        "data":    nuevo_pedido
    }


# 5. PATCH /{pedido_id}/estado — Cambiar estado
@router.patch("/{pedido_id}/estado", status_code=status.HTTP_200_OK)
async def cambiar_estado(pedido_id: int, body: CambiarEstado, db: Session = Depends(get_db)):
    pedido = db.query(Pedido).filter(Pedido.id == pedido_id).first()
    if not pedido:
        raise HTTPException(status_code=404, detail=f"Pedido con id {pedido_id} no encontrado")

    if body.estado not in ESTADOS_VALIDOS:
        raise HTTPException(
            status_code=400,
            detail=f"Estado '{body.estado}' no válido. Opciones: {', '.join(ESTADOS_VALIDOS)}"
        )

    pedido.estado = body.estado
    db.commit()
    db.refresh(pedido)

    return {
        "status":  "200",
        "mensaje": "Estado actualizado",
        "data":    pedido
    }
