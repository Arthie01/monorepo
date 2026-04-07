from fastapi import APIRouter, HTTPException, Depends, status
from sqlalchemy.orm import Session

from app.data.db import get_db
from app.data.usuario_interno import UsuarioInterno
from app.models.usuarios_internos import Crear_UsuarioInterno, Actualizar_UsuarioInterno
from app.security.auth import verificar_peticion

router = APIRouter(
    prefix="/v1/usuarios/internos",
    tags=["Usuarios Internos"]
)


@router.get("/", status_code=status.HTTP_200_OK)
async def consultar_todos(db: Session = Depends(get_db)):
    usuarios = db.query(UsuarioInterno).all()
    return {
        "status": "200",
        "total":  len(usuarios),
        "data":   usuarios
    }


@router.get("/{id}", status_code=status.HTTP_200_OK)
async def consultar_uno(id: int, db: Session = Depends(get_db)):
    usuario = db.query(UsuarioInterno).filter(UsuarioInterno.id == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail=f"Usuario interno con id {id} no encontrado")
    return {
        "status": "200",
        "data":   usuario
    }


@router.post("/", status_code=status.HTTP_201_CREATED)
async def crear(usuarioP: Crear_UsuarioInterno, db: Session = Depends(get_db)):
    existe = db.query(UsuarioInterno).filter(UsuarioInterno.email == usuarioP.email).first()
    if existe:
        raise HTTPException(status_code=400, detail="El email ya está registrado")

    nuevo = UsuarioInterno(
        nombre=usuarioP.nombre,
        apellidos=usuarioP.apellidos,
        email=usuarioP.email,
        password=usuarioP.password,
        departamento=usuarioP.departamento,
        rol=usuarioP.rol
    )
    db.add(nuevo)
    db.commit()
    db.refresh(nuevo)
    return {
        "status": "201",
        "mensaje": "Usuario interno creado",
        "data":    nuevo
    }


@router.put("/{id}", status_code=status.HTTP_200_OK)
async def actualizar(id: int, usuarioP: Crear_UsuarioInterno, db: Session = Depends(get_db)):
    usuario = db.query(UsuarioInterno).filter(UsuarioInterno.id == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail=f"Usuario interno con id {id} no encontrado")

    usuario.nombre       = usuarioP.nombre
    usuario.apellidos    = usuarioP.apellidos
    usuario.email        = usuarioP.email
    usuario.password     = usuarioP.password
    usuario.departamento = usuarioP.departamento
    usuario.rol          = usuarioP.rol

    db.commit()
    db.refresh(usuario)
    return {
        "status": "200",
        "mensaje": "Usuario interno actualizado",
        "data":    usuario
    }


@router.patch("/{id}", status_code=status.HTTP_200_OK)
async def actualizar_parcial(id: int, usuarioP: Actualizar_UsuarioInterno, db: Session = Depends(get_db)):
    usuario = db.query(UsuarioInterno).filter(UsuarioInterno.id == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail=f"Usuario interno con id {id} no encontrado")

    campos = usuarioP.model_dump(exclude_unset=True)
    for campo, valor in campos.items():
        setattr(usuario, campo, valor)

    db.commit()
    db.refresh(usuario)
    return {
        "status": "200",
        "mensaje": "Usuario interno actualizado parcialmente",
        "data":    usuario
    }


@router.delete("/{id}", status_code=status.HTTP_200_OK)
async def eliminar(
    id: int,
    db: Session = Depends(get_db),
    usuario_auth: str = Depends(verificar_peticion)
):
    usuario = db.query(UsuarioInterno).filter(UsuarioInterno.id == id).first()
    if not usuario:
        raise HTTPException(status_code=404, detail=f"Usuario interno con id {id} no encontrado")

    db.delete(usuario)
    db.commit()
    return {
        "status": "200",
        "mensaje": f"Usuario interno eliminado por {usuario_auth}",
        "id":      id
    }
