from typing import Optional

from fastapi import APIRouter, HTTPException, Depends, status
from sqlalchemy.orm import Session

from app.data.db import get_db
from app.data.usuario_externo import UsuarioExterno
from app.models.usuarios_externos import Crear_UsuarioExterno, Actualizar_UsuarioExterno, PatchUsuarioExterno
from app.security.auth import verificar_peticion

router = APIRouter(
    prefix="/v1/usuarios/externos",
    tags=["Usuarios Externos"]
)


@router.get("/", status_code=status.HTTP_200_OK)
async def consultar_todos(estado: Optional[str] = None, db: Session = Depends(get_db)):
    query = db.query(UsuarioExterno)
    if estado:
        query = query.filter(UsuarioExterno.estado.ilike(estado))
    usuarios = query.all()
    return {
        "status": "200",
        "total":  len(usuarios),
        "data":   usuarios
    }


@router.get("/{id}", status_code=status.HTTP_200_OK)
async def consultar_uno(id: int, db: Session = Depends(get_db)):
    usuario = db.query(UsuarioExterno).filter(UsuarioExterno.id == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail=f"Usuario externo con id {id} no encontrado")
    return {
        "status": "200",
        "data":   usuario
    }


@router.post("/", status_code=status.HTTP_201_CREATED)
async def crear(usuarioP: Crear_UsuarioExterno, db: Session = Depends(get_db)):
    existe = db.query(UsuarioExterno).filter(UsuarioExterno.email == usuarioP.email).first()
    if existe:
        raise HTTPException(status_code=400, detail="El email ya está registrado")

    nuevo = UsuarioExterno(
        nombre=usuarioP.nombre,
        apellidos=usuarioP.apellidos,
        email=usuarioP.email,
        password=usuarioP.password,
        tipo_cliente=usuarioP.tipo_cliente,
        lista_precio=usuarioP.lista_precio,
        dias_credito=usuarioP.dias_credito,
        limite_credito=usuarioP.limite_credito,
        descuento=usuarioP.descuento,
        estado=usuarioP.estado,
        telefono=usuarioP.telefono,
        empresa=usuarioP.empresa,
        rfc=usuarioP.rfc,
        giro=usuarioP.giro,
        calle=usuarioP.calle,
        ciudad=usuarioP.ciudad,
        estado_geo=usuarioP.estado_geo,
        cp=usuarioP.cp,
        referencia=usuarioP.referencia,
        notas=usuarioP.notas
    )
    db.add(nuevo)
    db.commit()
    db.refresh(nuevo)
    return {
        "status":  "201",
        "mensaje": "Usuario externo creado",
        "data":    nuevo
    }


@router.put("/{id}", status_code=status.HTTP_200_OK)
async def actualizar(id: int, usuarioP: Crear_UsuarioExterno, db: Session = Depends(get_db)):
    usuario = db.query(UsuarioExterno).filter(UsuarioExterno.id == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail=f"Usuario externo con id {id} no encontrado")

    email_en_uso = db.query(UsuarioExterno).filter(
        UsuarioExterno.email == usuarioP.email,
        UsuarioExterno.id != id
    ).first()
    if email_en_uso:
        raise HTTPException(status_code=400, detail="El email ya está en uso por otro usuario")

    usuario.nombre         = usuarioP.nombre
    usuario.apellidos      = usuarioP.apellidos
    usuario.email          = usuarioP.email
    usuario.password       = usuarioP.password
    usuario.tipo_cliente   = usuarioP.tipo_cliente
    usuario.lista_precio   = usuarioP.lista_precio
    usuario.dias_credito   = usuarioP.dias_credito
    usuario.limite_credito = usuarioP.limite_credito
    usuario.descuento      = usuarioP.descuento
    usuario.estado         = usuarioP.estado
    usuario.telefono       = usuarioP.telefono
    usuario.empresa        = usuarioP.empresa
    usuario.rfc            = usuarioP.rfc
    usuario.giro           = usuarioP.giro
    usuario.calle          = usuarioP.calle
    usuario.ciudad         = usuarioP.ciudad
    usuario.estado_geo     = usuarioP.estado_geo
    usuario.cp             = usuarioP.cp
    usuario.referencia     = usuarioP.referencia
    usuario.notas          = usuarioP.notas

    db.commit()
    db.refresh(usuario)
    return {
        "status":  "200",
        "mensaje": "Usuario externo actualizado",
        "data":    usuario
    }


@router.patch("/{id}", status_code=status.HTTP_200_OK)
async def actualizar_parcial(id: int, usuarioP: PatchUsuarioExterno, db: Session = Depends(get_db)):
    usuario = db.query(UsuarioExterno).filter(UsuarioExterno.id == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail=f"Usuario externo con id {id} no encontrado")

    campos = usuarioP.model_dump(exclude_unset=True)
    for campo, valor in campos.items():
        setattr(usuario, campo, valor)

    db.commit()
    db.refresh(usuario)
    return {
        "status":  "200",
        "mensaje": "Usuario externo actualizado parcialmente",
        "data":    usuario
    }


@router.delete("/{id}", status_code=status.HTTP_200_OK)
async def eliminar(
    id: int,
    db: Session = Depends(get_db),
    usuario_auth: str = Depends(verificar_peticion)
):
    usuario = db.query(UsuarioExterno).filter(UsuarioExterno.id == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail=f"Usuario externo con id {id} no encontrado")

    db.delete(usuario)
    db.commit()
    return {
        "status":  "200",
        "mensaje": f"Usuario externo eliminado por {usuario_auth}",
        "id":      id
    }
