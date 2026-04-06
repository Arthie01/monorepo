from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker, declarative_base
import os

# 1. URL de conexion
DATABASE_URL = os.getenv(
    "DATABASE_URL",
    "postgresql://admin:123456@postgres:5432/DB_macuin"
)

# 2. Motor de conexion
engine = create_engine(DATABASE_URL)

# 3. Gestion de sesiones
sessionLocal = sessionmaker(
    autocommit=False,
    autoflush=False,
    bind=engine
)

# 4. Base declarativa para Modelos
Base = declarative_base()

# 5. Funcion de sesion por peticion
def get_db():
    db = sessionLocal()
    try:
        yield db
    finally:
        db.close()
