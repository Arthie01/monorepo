# 🐍 Flask App

Proyecto Flask 3 — API base mínima sin interfaces ni lógica de negocio.

> **¿Por qué `venv` y no Poetry?**
> `venv` es stdlib de Python (sin instalación extra), ideal para APIs simples donde Poetry añadiría complejidad innecesaria. Poetry se usará cuando haya gestión avanzada de dependencias/publicación de paquetes.

---

## Requisitos del sistema

| Herramienta | Versión mínima |
|-------------|---------------|
| Python      | 3.10+         |
| pip         | 23+           |

---

## 🐧 Linux (Ubuntu 24.04)

### 1. Verificar Python

```bash
python3 --version   # debe ser >= 3.10
pip3 --version
```

### 2. Clonar/posicionarse en el proyecto

```bash
cd flask_app
```

### 3. Crear y activar entorno virtual

```bash
python3 -m venv venv
source venv/bin/activate
# El prompt cambiará a: (venv) ...
```

### 4. Instalar dependencias

```bash
pip install -r requirements.txt
```

### 5. Levantar el servidor de desarrollo

```bash
flask run
# Disponible en: http://127.0.0.1:5000
```

### Alternativa directa

```bash
python app.py
```

---

## 🪟 Windows (PowerShell)

### 1. Verificar Python

```powershell
python --version    # debe ser >= 3.10
pip --version
# Si no tienes Python: https://www.python.org/downloads/
```

### 2. Posicionarse en el proyecto

```powershell
cd flask_app
```

### 3. Crear y activar entorno virtual

```powershell
python -m venv venv
.\venv\Scripts\Activate.ps1
# Si hay error de política de ejecución:
# Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
# El prompt cambiará a: (venv) ...
```

### 4. Instalar dependencias

```powershell
pip install -r requirements.txt
```

### 5. Levantar el servidor de desarrollo

```powershell
flask run
# Disponible en: http://127.0.0.1:5000
```

### Alternativa directa

```powershell
python app.py
```

---

## 📁 Estructura del proyecto

```
flask_app/
├── venv/              ← entorno virtual (NO commitear)
├── app.py             ← punto de entrada, rutas principales
├── .flaskenv          ← variables de entorno para Flask CLI
├── .gitignore
├── requirements.txt   ← dependencias pinneadas
└── README.md
```

---

## ⚙️ Comandos útiles

```bash
# Activar entorno (Linux/Mac)
source venv/bin/activate

# Desactivar entorno
deactivate

# Regenerar requirements.txt tras instalar nuevos paquetes
pip freeze > requirements.txt

# Instalar un paquete nuevo
pip install nombre-paquete
pip freeze > requirements.txt   # actualizar

# Ver rutas registradas (equivalente a artisan route:list)
flask routes
```

---

## 🔗 Endpoint de prueba

```
GET http://127.0.0.1:5000/
→ {"status": "OK"}  [HTTP 200]
```

---

## Variables de entorno (.flaskenv)

```
FLASK_APP=app.py
FLASK_ENV=development
FLASK_RUN_HOST=127.0.0.1
FLASK_RUN_PORT=5000
```

> **Siguiente paso:** crear Blueprints para organizar rutas y añadir la lógica de negocio.
