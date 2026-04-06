from fastapi import FastAPI
from app.data.db import engine
from app.data import usuario_externo, usuario_interno, autoparte, pedido, detalle_pedido

# Crear tablas si no existen
usuario_externo.Base.metadata.create_all(bind=engine)

app = FastAPI(
    title="MACUIN API",
    description="API Central — MACUIN Autopartes y Distribución",
    version="1.0"
)


@app.get("/")
def health_check():
    return {"status": "OK", "service": "MACUIN FastAPI"}
