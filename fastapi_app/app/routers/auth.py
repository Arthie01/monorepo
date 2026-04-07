from fastapi import APIRouter, HTTPException, Depends, status
from sqlalchemy.orm import Session

from app.data.db import get_db
from app.data.usuario_externo import UsuarioExterno
from app.data.usuario_interno import UsuarioInterno
from app.models.usuarios import Crear_UsuarioExterno

router = APIRouter(
    prefix="/v1/auth",
    tags=["Autenticación"]
)


# ── Registro de usuario externo ────────────────────────────────────────────────
@router.post("/registro", status_code=status.HTTP_201_CREATED)
async def registro(usuario: Crear_UsuarioExterno, db: Session = Depends(get_db)):
    existe = db.query(UsuarioExterno).filter(UsuarioExterno.email == usuario.email).first()
    if existe:
        raise HTTPException(status_code=400, detail="El email ya está registrado")

    nuevo = UsuarioExterno(
        nombre=usuario.nombre,
        apellidos=usuario.apellidos,
        email=usuario.email,
        password=usuario.password
    )
    db.add(nuevo)
    db.commit()
    db.refresh(nuevo)

    return {
        "status": "201",
        "mensaje": "Usuario registrado correctamente",
        "data": {
            "id":        nuevo.id,
            "nombre":    nuevo.nombre,
            "apellidos": nuevo.apellidos,
            "email":     nuevo.email,
            "estado":    nuevo.estado
        }
    }


# ── Login portal externo (Laravel) ─────────────────────────────────────────────
@router.post("/login/externo", status_code=status.HTTP_200_OK)
async def login_externo(email: str, password: str, db: Session = Depends(get_db)):
    usuario = db.query(UsuarioExterno).filter(
        UsuarioExterno.email == email,
        UsuarioExterno.password == password
    ).first()

    if not usuario:
        raise HTTPException(status_code=401, detail="Credenciales incorrectas")

    if usuario.estado != "activo":
        raise HTTPException(status_code=403, detail="Cuenta inactiva o pendiente de aprobación")

    return {
        "status": "200",
        "mensaje": "Login exitoso",
        "data": {
            "id":           usuario.id,
            "nombre":       usuario.nombre,
            "apellidos":    usuario.apellidos,
            "email":        usuario.email,
            "tipo_cliente": usuario.tipo_cliente,
            "empresa":      usuario.empresa,
            "lista_precio": usuario.lista_precio,
            "descuento":    float(usuario.descuento),
            "estado":       usuario.estado
        }
    }


# ── Login panel interno (Flask) ────────────────────────────────────────────────
@router.post("/login/interno", status_code=status.HTTP_200_OK)
async def login_interno(email: str, password: str, db: Session = Depends(get_db)):
    usuario = db.query(UsuarioInterno).filter(
        UsuarioInterno.email == email,
        UsuarioInterno.password == password
    ).first()

    if not usuario:
        raise HTTPException(status_code=401, detail="Credenciales incorrectas")

    if usuario.estado != "activo":
        raise HTTPException(status_code=403, detail="Cuenta suspendida o inactiva")

    return {
        "status": "200",
        "mensaje": "Login exitoso",
        "data": {
            "id":              usuario.id,
            "nombre":          usuario.nombre,
            "apellidos":       usuario.apellidos,
            "email":           usuario.email,
            "departamento":    usuario.departamento,
            "rol":             usuario.rol,
            "cargo":           usuario.cargo,
            "sucursal":        usuario.sucursal,
            "perm_autopartes": usuario.perm_autopartes,
            "perm_pedidos":    usuario.perm_pedidos,
            "perm_usuarios":   usuario.perm_usuarios,
            "perm_reportes":   usuario.perm_reportes,
            "perm_config":     usuario.perm_config,
            "estado":          usuario.estado
        }
    }
