import os
import uuid
from typing import Optional

from fastapi import APIRouter, HTTPException, Depends, status, Form, File, UploadFile
from sqlalchemy.orm import Session

from app.data.db import get_db
from app.data.autoparte import Autoparte
from app.models.autopartes import PatchAutoparte
from app.security.auth import verificar_peticion

router = APIRouter(
    prefix="/v1/autopartes",
    tags=["Autopartes"]
)

UPLOAD_DIR = "/app/uploads/autopartes"


@router.get("/", status_code=status.HTTP_200_OK)
async def consultar_todos(
    categoria: Optional[str] = None,
    marca_vehiculo: Optional[str] = None,
    modelo_vehiculo: Optional[str] = None,
    db: Session = Depends(get_db)
):
    query = db.query(Autoparte)
    if categoria:
        query = query.filter(Autoparte.categoria.ilike(f"%{categoria}%"))
    if marca_vehiculo:
        query = query.filter(Autoparte.marca_vehiculo.ilike(f"%{marca_vehiculo}%"))
    if modelo_vehiculo:
        query = query.filter(Autoparte.modelo_vehiculo.ilike(f"%{modelo_vehiculo}%"))
    autopartes = query.all()
    return {
        "status": "200",
        "total":  len(autopartes),
        "data":   autopartes
    }


@router.get("/{id}", status_code=status.HTTP_200_OK)
async def consultar_uno(id: int, db: Session = Depends(get_db)):
    autoparte = db.query(Autoparte).filter(Autoparte.id == id).first()
    if not autoparte:
        raise HTTPException(status_code=404, detail=f"Autoparte con id {id} no encontrada")
    return {
        "status": "200",
        "data":   autoparte
    }


@router.post("/", status_code=status.HTTP_201_CREATED)
async def crear(
    nombre:          str            = Form(...),
    sku:             str            = Form(...),
    categoria:       str            = Form(...),
    precio:          float          = Form(...),
    stock:           int            = Form(0),
    stock_minimo:    int            = Form(0),
    unidad:          str            = Form("Pieza"),
    estado:          str            = Form("en_stock"),
    activo:          bool           = Form(True),
    marca:           Optional[str]  = Form(None),
    precio_original: Optional[float] = Form(None),
    ubicacion:       Optional[str]  = Form(None),
    marca_vehiculo:  Optional[str]  = Form(None),
    modelo_vehiculo: Optional[str]  = Form(None),
    aplicacion:      Optional[str]  = Form(None),
    descripcion:     Optional[str]  = Form(None),
    notas:           Optional[str]  = Form(None),
    imagen:          Optional[UploadFile] = File(None),
    db:              Session        = Depends(get_db)
):
    existe = db.query(Autoparte).filter(Autoparte.sku == sku).first()
    if existe:
        raise HTTPException(status_code=400, detail=f"El SKU '{sku}' ya está registrado")

    imagen_url = None
    if imagen:
        os.makedirs(UPLOAD_DIR, exist_ok=True)
        filename = f"{uuid.uuid4().hex}_{imagen.filename}"
        ruta = os.path.join(UPLOAD_DIR, filename)
        with open(ruta, "wb") as f:
            f.write(await imagen.read())
        imagen_url = f"http://localhost:8001/uploads/autopartes/{filename}"

    nueva = Autoparte(
        nombre=nombre,
        sku=sku,
        categoria=categoria,
        precio=precio,
        stock=stock,
        stock_minimo=stock_minimo,
        unidad=unidad,
        estado=estado,
        activo=activo,
        marca=marca,
        precio_original=precio_original,
        ubicacion=ubicacion,
        marca_vehiculo=marca_vehiculo,
        modelo_vehiculo=modelo_vehiculo,
        aplicacion=aplicacion,
        descripcion=descripcion,
        notas=notas,
        imagen=imagen_url
    )
    db.add(nueva)
    db.commit()
    db.refresh(nueva)
    return {
        "status": "201",
        "mensaje": "Autoparte creada",
        "data":    nueva
    }


@router.put("/{id}", status_code=status.HTTP_200_OK)
async def actualizar(
    id:              int,
    nombre:          str            = Form(...),
    sku:             str            = Form(...),
    categoria:       str            = Form(...),
    precio:          float          = Form(...),
    stock:           int            = Form(0),
    stock_minimo:    int            = Form(0),
    unidad:          str            = Form("Pieza"),
    estado:          str            = Form("en_stock"),
    activo:          bool           = Form(True),
    marca:           Optional[str]  = Form(None),
    precio_original: Optional[float] = Form(None),
    ubicacion:       Optional[str]  = Form(None),
    marca_vehiculo:  Optional[str]  = Form(None),
    modelo_vehiculo: Optional[str]  = Form(None),
    aplicacion:      Optional[str]  = Form(None),
    descripcion:     Optional[str]  = Form(None),
    notas:           Optional[str]  = Form(None),
    imagen:          Optional[UploadFile] = File(None),
    db:              Session        = Depends(get_db)
):
    autoparte = db.query(Autoparte).filter(Autoparte.id == id).first()
    if not autoparte:
        raise HTTPException(status_code=404, detail=f"Autoparte con id {id} no encontrada")

    imagen_url = autoparte.imagen
    if imagen:
        os.makedirs(UPLOAD_DIR, exist_ok=True)
        filename = f"{uuid.uuid4().hex}_{imagen.filename}"
        ruta = os.path.join(UPLOAD_DIR, filename)
        with open(ruta, "wb") as f:
            f.write(await imagen.read())
        imagen_url = f"http://localhost:8001/uploads/autopartes/{filename}"

    autoparte.nombre          = nombre
    autoparte.sku             = sku
    autoparte.categoria       = categoria
    autoparte.precio          = precio
    autoparte.stock           = stock
    autoparte.stock_minimo    = stock_minimo
    autoparte.unidad          = unidad
    autoparte.estado          = estado
    autoparte.activo          = activo
    autoparte.marca           = marca
    autoparte.precio_original = precio_original
    autoparte.ubicacion       = ubicacion
    autoparte.marca_vehiculo  = marca_vehiculo
    autoparte.modelo_vehiculo = modelo_vehiculo
    autoparte.aplicacion      = aplicacion
    autoparte.descripcion     = descripcion
    autoparte.notas           = notas
    autoparte.imagen          = imagen_url

    db.commit()
    db.refresh(autoparte)
    return {
        "status": "200",
        "mensaje": "Autoparte actualizada",
        "data":    autoparte
    }


@router.patch("/{id}", status_code=status.HTTP_200_OK)
async def actualizar_parcial(id: int, autoparteP: PatchAutoparte, db: Session = Depends(get_db)):
    autoparte = db.query(Autoparte).filter(Autoparte.id == id).first()
    if not autoparte:
        raise HTTPException(status_code=404, detail=f"Autoparte con id {id} no encontrada")

    campos = autoparteP.model_dump(exclude_unset=True)
    for campo, valor in campos.items():
        setattr(autoparte, campo, valor)

    db.commit()
    db.refresh(autoparte)
    return {
        "status": "200",
        "mensaje": "Autoparte actualizada parcialmente",
        "data":    autoparte
    }


@router.delete("/{id}", status_code=status.HTTP_200_OK)
async def eliminar(
    id: int,
    db: Session = Depends(get_db),
    usuario_auth: str = Depends(verificar_peticion)
):
    autoparte = db.query(Autoparte).filter(Autoparte.id == id).first()
    if not autoparte:
        raise HTTPException(status_code=404, detail=f"Autoparte con id {id} no encontrada")

    db.delete(autoparte)
    db.commit()
    return {
        "status": "200",
        "mensaje": f"Autoparte eliminada por {usuario_auth}",
        "id":      id
    }
