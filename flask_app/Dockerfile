# ─────────────────────────────────────────────
#  Flask App — Dockerfile
#  Base: python:3.12-slim
#  Servidor: Gunicorn (WSGI productivo)
#  Puerto: 5000
# ─────────────────────────────────────────────

FROM python:3.12-slim

# Variables de entorno para Python
ENV PYTHONDONTWRITEBYTECODE=1 \
    PYTHONUNBUFFERED=1

# Directorio de trabajo dentro del contenedor
WORKDIR /app

# Instalar dependencias primero (permite caché de Docker)
COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt && \
    pip install --no-cache-dir gunicorn

# Copiar el código fuente
COPY . .

# Exponer el puerto de la app
EXPOSE 5000

# Comando de inicio con Gunicorn
CMD ["gunicorn", "--bind", "0.0.0.0:5000", "--workers", "2", "app:app"]
