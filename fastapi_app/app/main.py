import os
from datetime import datetime
from fastapi import FastAPI
from fastapi.staticfiles import StaticFiles
from app.data.db import engine, sessionLocal
from app.data import usuario_externo, usuario_interno, autoparte, pedido, detalle_pedido
from app.data.usuario_interno import UsuarioInterno
from app.data.usuario_externo import UsuarioExterno
from app.data.autoparte import Autoparte
from app.data.pedido import Pedido
from app.data.detalle_pedido import DetallePedido
from app.routers import auth, usuarios_internos, usuarios_externos, autopartes, pedidos, reportes

# Crear directorio de uploads si no existe
os.makedirs("/app/uploads/autopartes", exist_ok=True)

# Crear tablas si no existen
usuario_externo.Base.metadata.create_all(bind=engine)


def seed_db():
    """Inserta datos de prueba solo si la BD está vacía."""
    db = sessionLocal()
    try:
        if db.query(UsuarioInterno).count() > 0:
            return  # Ya tiene datos, no reinserta

        # ── Usuarios Internos ─────────────────────────────────────
        db.add_all([
            UsuarioInterno(nombre='Artemio',  apellidos='Hurtado Reyes',    email='artemio@macuin.mx',   password='admin123', telefono='614-123-4567', departamento='Ventas',     rol='Administrador', cargo='Gerente de Ventas',       sucursal='Monterrey — Central', perm_autopartes=True,  perm_pedidos=True,  perm_usuarios=True,  perm_reportes=True,  perm_config=True,  estado='activo'),
            UsuarioInterno(nombre='María',    apellidos='López Sánchez',     email='maria@macuin.mx',     password='admin123', telefono='614-234-5678', departamento='Almacén',    rol='Almacén',       cargo='Jefe de Almacén',          sucursal='Monterrey — Central', perm_autopartes=True,  perm_pedidos=True,  perm_usuarios=False, perm_reportes=True,  perm_config=False, estado='activo'),
            UsuarioInterno(nombre='Roberto',  apellidos='García Vega',       email='roberto@macuin.mx',   password='admin123', telefono='614-345-6789', departamento='Logística',  rol='Logística',     cargo='Coordinador Logístico',    sucursal='Guadalupe',           perm_autopartes=False, perm_pedidos=True,  perm_usuarios=False, perm_reportes=False, perm_config=False, estado='activo'),
            UsuarioInterno(nombre='Emiliano', apellidos='Martínez Cruz',     email='emiliano@macuin.mx',  password='admin123', telefono='614-456-7890', departamento='Ventas',     rol='Ventas',        cargo='Ejecutivo de Ventas',      sucursal='Monterrey — Central', perm_autopartes=False, perm_pedidos=True,  perm_usuarios=False, perm_reportes=True,  perm_config=False, estado='activo'),
            UsuarioInterno(nombre='Carla',    apellidos='Reyes Mendoza',     email='carla@macuin.mx',     password='admin123', telefono='614-567-8901', departamento='Almacén',    rol='Almacén',       cargo='Auxiliar de Almacén',      sucursal='Apodaca',             perm_autopartes=True,  perm_pedidos=False, perm_usuarios=False, perm_reportes=False, perm_config=False, estado='activo'),
            UsuarioInterno(nombre='Jorge',    apellidos='Villanueva Ortiz',  email='jorge@macuin.mx',     password='admin123', telefono='614-678-9012', departamento='Logística',  rol='Logística',     cargo='Repartidor',               sucursal='San Nicolás',         perm_autopartes=False, perm_pedidos=True,  perm_usuarios=False, perm_reportes=False, perm_config=False, estado='activo'),
            UsuarioInterno(nombre='Patricia', apellidos='Navarro Ríos',      email='patricia@macuin.mx',  password='admin123', telefono='614-789-0123', departamento='Ventas',     rol='Ventas',        cargo='Ejecutiva de Ventas',      sucursal='Monterrey — Central', perm_autopartes=False, perm_pedidos=True,  perm_usuarios=False, perm_reportes=True,  perm_config=False, estado='inactivo'),
            UsuarioInterno(nombre='Daniel',   apellidos='Soto Fuentes',      email='daniel@macuin.mx',    password='admin123', telefono='614-890-1234', departamento='Almacén',    rol='Almacén',       cargo='Auxiliar de Almacén',      sucursal='Guadalupe',           perm_autopartes=True,  perm_pedidos=False, perm_usuarios=False, perm_reportes=False, perm_config=False, estado='inactivo'),
        ])
        db.flush()

        # ── Usuarios Externos ─────────────────────────────────────
        db.add_all([
            UsuarioExterno(nombre='Juan',     apellidos='Ramírez López',    email='j.ramirez@tallercentral.mx',  password='macuin123', telefono='81-9876-5432', empresa='Taller Central Monterrey', tipo_cliente='Taller mecánico',   rfc='TCM850101XX1', giro='Servicio automotriz',    calle='Av. Constitución #2450, Col. Centro',          ciudad='Monterrey',              estado_geo='NL', cp='64000', referencia='Frente al parque',          lista_precio='Taller / Mayoreo', dias_credito=30, limite_credito=50000, descuento=10, notas='Cliente frecuente desde 2025. Pedidos grandes los lunes.', estado='activo'),
            UsuarioExterno(nombre='Laura',    apellidos='Mendoza Castillo', email='l.mendoza@refacmendoza.com',  password='macuin123', telefono='81-8765-4321', empresa='Refaccionaria Mendoza',    tipo_cliente='Refaccionaria',     rfc='RMC920315AB2', giro='Venta de refacciones',   calle='Blvd. Díaz Ordaz #890, Col. Mirador',          ciudad='Monterrey',              estado_geo='NL', cp='64750', referencia='Junto a la gasolinera Shell', lista_precio='Taller / Mayoreo', dias_credito=15, limite_credito=30000, descuento=8,  notas='Prefiere recibir facturas por WhatsApp.', estado='activo'),
            UsuarioExterno(nombre='Carlos',   apellidos='Fuentes Torres',   email='cfuentes@gmail.com',          password='macuin123', telefono='81-7654-3210', empresa=None,                       tipo_cliente='Particular',        rfc=None,           giro='Uso personal',           calle='Calle Roble #123, Col. Bosques',               ciudad='San Pedro Garza García', estado_geo='NL', cp='66250', referencia=None,                         lista_precio='Público general',  dias_credito=0,  limite_credito=0,     descuento=0,  notas=None, estado='activo'),
            UsuarioExterno(nombre='Andrea',   apellidos='Paredes Salinas',  email='a.paredes@distrimotor.mx',    password='macuin123', telefono='81-6543-2109', empresa='DistriMotors del Norte',   tipo_cliente='Distribuidor',      rfc='DNM780620CD3', giro='Distribución mayorista', calle='Av. Insurgentes #450, Parque Industrial',      ciudad='Apodaca',                estado_geo='NL', cp='66600', referencia='Bodega 12',                  lista_precio='Distribuidor',     dias_credito=45, limite_credito=200000,descuento=15, notas='Pedidos mínimos de $10,000. Pagos quincenales.', estado='activo'),
            UsuarioExterno(nombre='Miguel',   apellidos='Hernández Vega',   email='m.hernandez@tallersureste.mx',password='macuin123', telefono='81-5432-1098', empresa='Taller Sureste',           tipo_cliente='Taller mecánico',   rfc='TSM910405EF4', giro='Servicio automotriz',    calle='Av. Ruiz Cortines #3300, Col. Del Prado',      ciudad='Monterrey',              estado_geo='NL', cp='64410', referencia=None,                         lista_precio='Taller / Mayoreo', dias_credito=0,  limite_credito=0,     descuento=5,  notas=None, estado='activo'),
            UsuarioExterno(nombre='Sandra',   apellidos='Torres Ibarra',    email='s.torres@refacnorte.mx',      password='macuin123', telefono='81-4321-0987', empresa='Refaccionaria del Norte',  tipo_cliente='Refaccionaria',     rfc='RNI001215GH5', giro='Venta de refacciones',   calle='Blvd. Escobedo #789',                          ciudad='Escobedo',               estado_geo='NL', cp='66050', referencia=None,                         lista_precio='Público general',  dias_credito=0,  limite_credito=0,     descuento=0,  notas='Recién registrada, pendiente verificar crédito.', estado='pendiente'),
            UsuarioExterno(nombre='Raúl',     apellidos='Delgado Moreno',   email='r.delgado@hotmail.com',       password='macuin123', telefono='81-3210-9876', empresa=None,                       tipo_cliente='Particular',        rfc=None,           giro='Uso personal',           calle='Calle Cedro #45, Col. Las Flores',             ciudad='Guadalupe',              estado_geo='NL', cp='67100', referencia=None,                         lista_precio='Público general',  dias_credito=0,  limite_credito=0,     descuento=0,  notas=None, estado='pendiente'),
            UsuarioExterno(nombre='Fernando', apellidos='Ortiz Ríos',       email='f.ortiz@tallerortiz.mx',      password='macuin123', telefono='81-2109-8765', empresa='Taller Ortiz',             tipo_cliente='Taller mecánico',   rfc='TOI880720IJ6', giro='Servicio automotriz',    calle='Calle 5 de Mayo #100, Centro',                 ciudad='Santa Catarina',         estado_geo='NL', cp='66350', referencia=None,                         lista_precio='Taller / Mayoreo', dias_credito=0,  limite_credito=0,     descuento=0,  notas='Cuenta desactivada por falta de pago.', estado='inactivo'),
        ])
        db.flush()

        # ── Autopartes ────────────────────────────────────────────
        db.add_all([
            Autoparte(nombre='Filtro de aceite FRAM PH3614',        sku='MAC-MOT-001', categoria='Motor',       marca='FRAM',    precio=85.00,   precio_original=None,    stock=120, stock_minimo=10, unidad='Pieza', ubicacion='A-01', marca_vehiculo='Nissan,Chevrolet,Ford',  modelo_vehiculo='Versa,Aveo,Fiesta',         descripcion='Filtro de aceite para motores 4 cilindros, compatibilidad amplia',     estado='en_stock',   activo=True),
            Autoparte(nombre='Bujía NGK Iridium BKR6EIX',           sku='MAC-MOT-002', categoria='Motor',       marca='NGK',     precio=145.00,  precio_original=180.00,  stock=80,  stock_minimo=8,  unidad='Pieza', ubicacion='A-02', marca_vehiculo='Toyota,Honda,Nissan',    modelo_vehiculo='Corolla,Civic,Sentra',      descripcion='Bujía de iridio, mayor rendimiento y menor consumo de combustible',    estado='en_stock',   activo=True),
            Autoparte(nombre='Empaque de cabeza motor 2.0L',         sku='MAC-MOT-003', categoria='Motor',       marca='VICTOR',  precio=650.00,  precio_original=None,    stock=15,  stock_minimo=5,  unidad='Juego', ubicacion='A-03', marca_vehiculo='Volkswagen,Audi',        modelo_vehiculo='Jetta,Golf,A3',             descripcion='Empaque completo de cabeza para motores 2.0L DOHC',                    estado='en_stock',   activo=True),
            Autoparte(nombre='Bomba de agua GATES 42018',            sku='MAC-MOT-004', categoria='Motor',       marca='GATES',   precio=480.00,  precio_original=550.00,  stock=25,  stock_minimo=5,  unidad='Pieza', ubicacion='A-04', marca_vehiculo='Ford,Chevrolet',         modelo_vehiculo='Focus,Cruze',               descripcion='Bomba de agua de aluminio, alta resistencia a la corrosión',            estado='en_stock',   activo=True),
            Autoparte(nombre='Amortiguador delantero MONROE 71671',  sku='MAC-SUS-001', categoria='Suspensión',  marca='MONROE',  precio=750.00,  precio_original=900.00,  stock=40,  stock_minimo=8,  unidad='Pieza', ubicacion='B-01', marca_vehiculo='Nissan',                 modelo_vehiculo='Tsuru,Sentra,Versa',        descripcion='Amortiguador gas-presión eje delantero, control de rodadura suave',    estado='en_stock',   activo=True),
            Autoparte(nombre='Resorte espiral delantero MOOG',       sku='MAC-SUS-002', categoria='Suspensión',  marca='MOOG',    precio=420.00,  precio_original=None,    stock=30,  stock_minimo=6,  unidad='Pieza', ubicacion='B-02', marca_vehiculo='Volkswagen',             modelo_vehiculo='Jetta,Golf,Vento',          descripcion='Resorte de suspensión delantera acero alta resistencia',               estado='en_stock',   activo=True),
            Autoparte(nombre='Rótula inferior MOOG K80462',          sku='MAC-SUS-003', categoria='Suspensión',  marca='MOOG',    precio=285.00,  precio_original=320.00,  stock=6,   stock_minimo=8,  unidad='Pieza', ubicacion='B-03', marca_vehiculo='Chevrolet',              modelo_vehiculo='Aveo,Beat,Trax',            descripcion='Rótula de suspensión inferior lado izquierdo',                         estado='bajo_stock', activo=True),
            Autoparte(nombre='Balatas delanteras BENDIX CFC1404',    sku='MAC-FRE-001', categoria='Frenos',      marca='BENDIX',  precio=320.00,  precio_original=420.00,  stock=90,  stock_minimo=15, unidad='Juego', ubicacion='C-01', marca_vehiculo='Honda,Toyota',           modelo_vehiculo='Civic,Corolla,HRV,RAV4',   descripcion='Balatas delanteras cerámicas, bajo nivel de polvo y ruido',            estado='en_stock',   activo=True),
            Autoparte(nombre='Disco de freno ventilado BREMBO 09',   sku='MAC-FRE-002', categoria='Frenos',      marca='BREMBO',  precio=680.00,  precio_original=None,    stock=35,  stock_minimo=6,  unidad='Pieza', ubicacion='C-02', marca_vehiculo='Volkswagen,Seat',        modelo_vehiculo='Golf,León,Tiguan',          descripcion='Disco ventilado de alto rendimiento, diámetro 280mm',                  estado='en_stock',   activo=True),
            Autoparte(nombre='Líquido de frenos DOT4 CASTROL 500ml', sku='MAC-FRE-003', categoria='Frenos',      marca='CASTROL', precio=95.00,   precio_original=None,    stock=200, stock_minimo=30, unidad='Pieza', ubicacion='C-03', marca_vehiculo=None,                    modelo_vehiculo=None,                        descripcion='Líquido de frenos sintético DOT4, punto de ebullición 265°C',          estado='en_stock',   activo=True),
            Autoparte(nombre='Batería BOSCH S4 45Ah 400A',           sku='MAC-ELE-001', categoria='Eléctrico',   marca='BOSCH',   precio=1350.00, precio_original=1500.00, stock=20,  stock_minimo=4,  unidad='Pieza', ubicacion='D-01', marca_vehiculo='Chevrolet,Nissan,Ford',  modelo_vehiculo='Aveo,Versa,Fiesta',         descripcion='Batería libre de mantenimiento 45Ah 400A arranque en frío',            estado='en_stock',   activo=True),
            Autoparte(nombre='Alternador BOSCH 90A reconstruido',    sku='MAC-ELE-002', categoria='Eléctrico',   marca='BOSCH',   precio=1800.00, precio_original=None,    stock=3,   stock_minimo=4,  unidad='Pieza', ubicacion='D-02', marca_vehiculo='Nissan',                 modelo_vehiculo='Tsuru,Sentra',              descripcion='Alternador reconstruido 90A, garantía 6 meses',                       estado='bajo_stock', activo=True),
            Autoparte(nombre='Sensor de oxígeno BOSCH 13958',        sku='MAC-ELE-003', categoria='Eléctrico',   marca='BOSCH',   precio=450.00,  precio_original=520.00,  stock=45,  stock_minimo=8,  unidad='Pieza', ubicacion='D-03', marca_vehiculo='Ford,Chevrolet,Nissan',  modelo_vehiculo='Focus,Cruze,Versa',         descripcion='Sensor O2 banda ancha, compatible OBD2',                              estado='en_stock',   activo=True),
            Autoparte(nombre='Kit clutch LUK 1.6L completo',         sku='MAC-TRA-001', categoria='Transmisión', marca='LUK',     precio=2200.00, precio_original=2500.00, stock=10,  stock_minimo=3,  unidad='Juego', ubicacion='E-01', marca_vehiculo='Volkswagen',             modelo_vehiculo='Jetta,Golf,Vento',          descripcion='Kit completo: disco, prensaplato y collarín',                          estado='en_stock',   activo=True),
            Autoparte(nombre='Aceite ATF MOBIL Dexron VI 1L',        sku='MAC-TRA-002', categoria='Transmisión', marca='MOBIL',   precio=185.00,  precio_original=None,    stock=60,  stock_minimo=15, unidad='Litro', ubicacion='E-02', marca_vehiculo=None,                    modelo_vehiculo=None,                        descripcion='Aceite transmisión automática ATF Dexron VI, 1 litro',                 estado='en_stock',   activo=True),
            Autoparte(nombre='Filtro de aire K&N 33-2887',           sku='MAC-FIL-001', categoria='Filtros',     marca='K&N',     precio=320.00,  precio_original=380.00,  stock=55,  stock_minimo=10, unidad='Pieza', ubicacion='F-01', marca_vehiculo='Dodge,Chrysler',         modelo_vehiculo='Attitude,Journey',          descripcion='Filtro de alto flujo lavable y reutilizable',                          estado='en_stock',   activo=True),
            Autoparte(nombre='Filtro de combustible MANN WK6',       sku='MAC-FIL-002', categoria='Filtros',     marca='MANN',    precio=125.00,  precio_original=None,    stock=70,  stock_minimo=12, unidad='Pieza', ubicacion='F-02', marca_vehiculo='Volkswagen',             modelo_vehiculo='Jetta,Passat,Golf',         descripcion='Filtro de combustible en línea, capacidad 10 micras',                  estado='en_stock',   activo=True),
            Autoparte(nombre='Filtro de cabina BOSCH M2003',         sku='MAC-FIL-003', categoria='Filtros',     marca='BOSCH',   precio=180.00,  precio_original=220.00,  stock=2,   stock_minimo=10, unidad='Pieza', ubicacion='F-03', marca_vehiculo='Toyota,Honda',           modelo_vehiculo='Corolla,Civic,Prius',       descripcion='Filtro de habitáculo con carbón activado, elimina olores y alérgenos', estado='bajo_stock', activo=True),
        ])
        db.flush()

        # ── Pedidos ───────────────────────────────────────────────
        db.add_all([
            Pedido(folio='MAC-2025-0089', usuario_externo_id=1, usuario_interno_id=4,    estado='Entregado',  subtotal=2103.00, envio=0.00,   impuestos=336.48, total=2439.48, dir_calle='Av. Constitución #2450, Col. Centro',       dir_ciudad='Monterrey',              dir_estado='NL', dir_cp='64000', creado_en=datetime(2025, 1, 12, 9,  32)),
            Pedido(folio='MAC-2025-0081', usuario_externo_id=2, usuario_interno_id=4,    estado='Enviado',    subtotal=1240.00, envio=0.00,   impuestos=198.40, total=1438.40, dir_calle='Blvd. Díaz Ordaz #890, Col. Mirador',       dir_ciudad='Monterrey',              dir_estado='NL', dir_cp='64750', creado_en=datetime(2025, 1,  5, 14, 20)),
            Pedido(folio='MAC-2025-0074', usuario_externo_id=4, usuario_interno_id=1,    estado='En proceso', subtotal=4220.00, envio=150.00, impuestos=675.20, total=5045.20, dir_calle='Av. Insurgentes #450, Parque Industrial',   dir_ciudad='Apodaca',                dir_estado='NL', dir_cp='66600', creado_en=datetime(2024, 12, 28, 11, 15)),
            Pedido(folio='MAC-2025-0068', usuario_externo_id=1, usuario_interno_id=None, estado='Pendiente',  subtotal=580.00,  envio=80.00,  impuestos=92.80,  total=752.80,  dir_calle='Av. Constitución #2450, Col. Centro',       dir_ciudad='Monterrey',              dir_estado='NL', dir_cp='64000', creado_en=datetime(2024, 12, 20, 16, 45)),
            Pedido(folio='MAC-2025-0059', usuario_externo_id=3, usuario_interno_id=None, estado='Cancelado',  subtotal=185.00,  envio=0.00,   impuestos=29.60,  total=214.60,  dir_calle='Calle Roble #123, Col. Bosques',            dir_ciudad='San Pedro Garza García', dir_estado='NL', dir_cp='66250', creado_en=datetime(2024, 12, 10, 10,  0)),
            Pedido(folio='MAC-2025-0048', usuario_externo_id=5, usuario_interno_id=2,    estado='Entregado',  subtotal=750.00,  envio=0.00,   impuestos=120.00, total=870.00,  dir_calle='Av. Ruiz Cortines #3300, Col. Del Prado',   dir_ciudad='Monterrey',              dir_estado='NL', dir_cp='64410', creado_en=datetime(2024, 12,  1,  8, 30)),
        ])
        db.flush()

        # ── Detalle de Pedidos ────────────────────────────────────
        db.add_all([
            # Pedido 1 — MAC-2025-0089 (Juan, Entregado)
            DetallePedido(pedido_id=1, autoparte_id=8,  cantidad=1, precio_unitario=320.00),
            DetallePedido(pedido_id=1, autoparte_id=1,  cantidad=2, precio_unitario=85.00),
            DetallePedido(pedido_id=1, autoparte_id=5,  cantidad=1, precio_unitario=750.00),
            # Pedido 2 — MAC-2025-0081 (Laura, Enviado)
            DetallePedido(pedido_id=2, autoparte_id=14, cantidad=1, precio_unitario=2200.00),
            # Pedido 3 — MAC-2025-0074 (Andrea, En proceso)
            DetallePedido(pedido_id=3, autoparte_id=11, cantidad=2, precio_unitario=1350.00),
            DetallePedido(pedido_id=3, autoparte_id=8,  cantidad=2, precio_unitario=320.00),
            DetallePedido(pedido_id=3, autoparte_id=3,  cantidad=1, precio_unitario=650.00),
            DetallePedido(pedido_id=3, autoparte_id=15, cantidad=3, precio_unitario=185.00),
            # Pedido 4 — MAC-2025-0068 (Juan, Pendiente)
            DetallePedido(pedido_id=4, autoparte_id=1,  cantidad=4, precio_unitario=85.00),
            DetallePedido(pedido_id=4, autoparte_id=10, cantidad=2, precio_unitario=95.00),
            # Pedido 5 — MAC-2025-0059 (Carlos, Cancelado)
            DetallePedido(pedido_id=5, autoparte_id=15, cantidad=1, precio_unitario=185.00),
            # Pedido 6 — MAC-2025-0048 (Miguel, Entregado)
            DetallePedido(pedido_id=6, autoparte_id=5,  cantidad=1, precio_unitario=750.00),
        ])

        db.commit()
        print("==> Seed completado: usuarios, autopartes y pedidos insertados.")

    except Exception as e:
        db.rollback()
        print(f"==> Error en seed: {e}")
    finally:
        db.close()


seed_db()

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
app.include_router(reportes.router)


@app.get("/")
def health_check():
    return {"status": "OK", "service": "MACUIN FastAPI"}
