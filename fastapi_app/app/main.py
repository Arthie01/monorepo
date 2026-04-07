import os
from fastapi import FastAPI
from fastapi.staticfiles import StaticFiles
from app.data.db import engine
from app.data import usuario_externo, usuario_interno, autoparte, pedido, detalle_pedido
from app.routers import auth, usuarios_internos, usuarios_externos, autopartes, pedidos

# Crear directorio de uploads si no existe
os.makedirs("/app/uploads/autopartes", exist_ok=True)

# Crear tablas si no existen
usuario_externo.Base.metadata.create_all(bind=engine)

app = FastAPI(
    title="MACUIN API",
    description="API Central — MACUIN Autopartes y Distribución",
    version="1.0"
)

# Archivos estáticos (imágenes de autopartes)
app.mount("/uploads", StaticFiles(directory="/app/uploads"), name="uploads")

# Routers
app.include_router(auth.router)
app.include_router(usuarios_internos.router)
app.include_router(usuarios_externos.router)
app.include_router(autopartes.router)
app.include_router(pedidos.router)


@app.get("/")
def health_check():
    return {"status": "OK", "service": "MACUIN FastAPI"}
