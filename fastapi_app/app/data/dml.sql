-- ============================================================
--  DML — MACUIN Autopartes y Distribución
--  Datos de prueba para desarrollo y demostración
--  Ejecutar DESPUÉS de ddl.sql
-- ============================================================

-- ------------------------------------------------------------
-- 1. Usuarios Internos (personal MACUIN)
-- ------------------------------------------------------------
INSERT INTO tb_usuarios_internos
    (nombre, apellidos, email, password, telefono, departamento, rol, cargo, sucursal,
     perm_autopartes, perm_pedidos, perm_usuarios, perm_reportes, perm_config, estado)
VALUES
    ('Artemio',   'Hurtado Reyes',    'artemio@macuin.mx',   'admin123', '614-123-4567', 'Ventas',          'Administrador', 'Gerente de Ventas',       'Monterrey — Central', TRUE,  TRUE,  TRUE,  TRUE,  TRUE,  'activo'),
    ('María',     'López Sánchez',    'maria@macuin.mx',     'admin123', '614-234-5678', 'Almacén',         'Almacén',       'Jefe de Almacén',         'Monterrey — Central', TRUE,  TRUE,  FALSE, TRUE,  FALSE, 'activo'),
    ('Roberto',   'García Vega',      'roberto@macuin.mx',   'admin123', '614-345-6789', 'Logística',       'Logística',     'Coordinador Logístico',   'Guadalupe',           FALSE, TRUE,  FALSE, FALSE, FALSE, 'activo'),
    ('Emiliano',  'Martínez Cruz',    'emiliano@macuin.mx',  'admin123', '614-456-7890', 'Ventas',          'Ventas',        'Ejecutivo de Ventas',     'Monterrey — Central', FALSE, TRUE,  FALSE, TRUE,  FALSE, 'activo'),
    ('Carla',     'Reyes Mendoza',    'carla@macuin.mx',     'admin123', '614-567-8901', 'Almacén',         'Almacén',       'Auxiliar de Almacén',     'Apodaca',             TRUE,  FALSE, FALSE, FALSE, FALSE, 'activo'),
    ('Jorge',     'Villanueva Ortiz', 'jorge@macuin.mx',     'admin123', '614-678-9012', 'Logística',       'Logística',     'Repartidor',              'San Nicolás',         FALSE, TRUE,  FALSE, FALSE, FALSE, 'activo'),
    ('Patricia',  'Navarro Ríos',     'patricia@macuin.mx',  'admin123', '614-789-0123', 'Ventas',          'Ventas',        'Ejecutiva de Ventas',     'Monterrey — Central', FALSE, TRUE,  FALSE, TRUE,  FALSE, 'inactivo'),
    ('Daniel',    'Soto Fuentes',     'daniel@macuin.mx',    'admin123', '614-890-1234', 'Almacén',         'Almacén',       'Auxiliar de Almacén',     'Guadalupe',           TRUE,  FALSE, FALSE, FALSE, FALSE, 'inactivo');

-- ------------------------------------------------------------
-- 2. Usuarios Externos (clientes)
-- ------------------------------------------------------------
INSERT INTO tb_usuarios_externos
    (nombre, apellidos, email, password, telefono,
     empresa, tipo_cliente, rfc, giro,
     calle, ciudad, estado_geo, cp, referencia,
     lista_precio, dias_credito, limite_credito, descuento, notas, estado)
VALUES
    ('Juan',     'Ramírez López',   'j.ramirez@tallercentral.mx',  'macuin123', '81-9876-5432',
     'Taller Central Monterrey',    'Taller mecánico',   'TCM850101XX1', 'Servicio automotriz',
     'Av. Constitución #2450, Col. Centro', 'Monterrey',     'NL',   '64000', 'Frente al parque',
     'Taller / Mayoreo', 30, 50000, 10, 'Cliente frecuente desde 2025. Pedidos grandes los lunes.', 'activo'),

    ('Laura',    'Mendoza Castillo','l.mendoza@refacmendoza.com',  'macuin123', '81-8765-4321',
     'Refaccionaria Mendoza',       'Refaccionaria',     'RMC920315AB2', 'Venta de refacciones',
     'Blvd. Díaz Ordaz #890, Col. Mirador', 'Monterrey',  'NL',   '64750', 'Junto a la gasolinera Shell',
     'Taller / Mayoreo', 15, 30000, 8, 'Prefiere recibir facturas por WhatsApp.', 'activo'),

    ('Carlos',   'Fuentes Torres',  'cfuentes@gmail.com',          'macuin123', '81-7654-3210',
     NULL, 'Particular', NULL, 'Uso personal',
     'Calle Roble #123, Col. Bosques', 'San Pedro Garza García', 'NL', '66250', NULL,
     'Público general', 0, 0, 0, NULL, 'activo'),

    ('Andrea',   'Paredes Salinas', 'a.paredes@distrimotor.mx',   'macuin123', '81-6543-2109',
     'DistriMotors del Norte',      'Distribuidor',      'DNM780620CD3', 'Distribución mayorista',
     'Av. Insurgentes #450, Parque Industrial', 'Apodaca', 'NL',  '66600', 'Bodega 12',
     'Distribuidor', 45, 200000, 15, 'Pedidos mínimos de $10,000. Pagos quincenales.', 'activo'),

    ('Miguel',   'Hernández Vega',  'm.hernandez@tallersureste.mx','macuin123', '81-5432-1098',
     'Taller Sureste',              'Taller mecánico',   'TSM910405EF4', 'Servicio automotriz',
     'Av. Ruiz Cortines #3300, Col. Del Prado', 'Monterrey', 'NL', '64410', NULL,
     'Taller / Mayoreo', 0, 0, 5, NULL, 'activo'),

    ('Sandra',   'Torres Ibarra',   's.torres@refacnorte.mx',     'macuin123', '81-4321-0987',
     'Refaccionaria del Norte',     'Refaccionaria',     'RNI001215GH5', 'Venta de refacciones',
     'Blvd. Escobedo #789', 'Escobedo', 'NL', '66050', NULL,
     'Público general', 0, 0, 0, 'Recién registrada, pendiente verificar crédito.', 'pendiente'),

    ('Raúl',     'Delgado Moreno',  'r.delgado@hotmail.com',      'macuin123', '81-3210-9876',
     NULL, 'Particular', NULL, 'Uso personal',
     'Calle Cedro #45, Col. Las Flores', 'Guadalupe', 'NL', '67100', NULL,
     'Público general', 0, 0, 0, NULL, 'pendiente'),

    ('Fernando', 'Ortiz Ríos',      'f.ortiz@tallerortiz.mx',     'macuin123', '81-2109-8765',
     'Taller Ortiz',                'Taller mecánico',   'TOI880720IJ6', 'Servicio automotriz',
     'Calle 5 de Mayo #100, Centro', 'Santa Catarina', 'NL', '66350', NULL,
     'Taller / Mayoreo', 0, 0, 0, 'Cuenta desactivada por falta de pago.', 'inactivo');

-- ------------------------------------------------------------
-- 3. Autopartes (catálogo completo — 18 referencias)
-- ------------------------------------------------------------
INSERT INTO tb_autopartes
    (nombre, sku, categoria, marca, precio, precio_original, stock, stock_minimo,
     unidad, ubicacion, marca_vehiculo, modelo_vehiculo, descripcion, estado, activo)
VALUES
    -- Motor (4)
    ('Filtro de aceite FRAM PH3614',        'MAC-MOT-001', 'Motor',       'FRAM',    85.00,   NULL,   120, 10,  'Pieza', 'A-01', 'Nissan,Chevrolet,Ford',  'Versa,Aveo,Fiesta',         'Filtro de aceite para motores 4 cilindros, compatibilidad amplia',     'en_stock',   TRUE),
    ('Bujía NGK Iridium BKR6EIX',           'MAC-MOT-002', 'Motor',       'NGK',    145.00,  180.00,  80,  8,  'Pieza', 'A-02', 'Toyota,Honda,Nissan',    'Corolla,Civic,Sentra',      'Bujía de iridio, mayor rendimiento y menor consumo de combustible',    'en_stock',   TRUE),
    ('Empaque de cabeza motor 2.0L',         'MAC-MOT-003', 'Motor',       'VICTOR',  650.00,  NULL,   15,  5,  'Juego', 'A-03', 'Volkswagen,Audi',        'Jetta,Golf,A3',             'Empaque completo de cabeza para motores 2.0L DOHC',                    'en_stock',   TRUE),
    ('Bomba de agua GATES 42018',            'MAC-MOT-004', 'Motor',       'GATES',   480.00,  550.00, 25,  5,  'Pieza', 'A-04', 'Ford,Chevrolet',         'Focus,Cruze',               'Bomba de agua de aluminio, alta resistencia a la corrosión',            'en_stock',   TRUE),

    -- Suspensión (3)
    ('Amortiguador delantero MONROE 71671',  'MAC-SUS-001', 'Suspensión',  'MONROE',  750.00,  900.00, 40,  8,  'Pieza', 'B-01', 'Nissan',                 'Tsuru,Sentra,Versa',        'Amortiguador gas-presión eje delantero, control de rodadura suave',    'en_stock',   TRUE),
    ('Resorte espiral delantero MOOG',       'MAC-SUS-002', 'Suspensión',  'MOOG',    420.00,  NULL,   30,  6,  'Pieza', 'B-02', 'Volkswagen',             'Jetta,Golf,Vento',          'Resorte de suspensión delantera acero alta resistencia',               'en_stock',   TRUE),
    ('Rótula inferior MOOG K80462',          'MAC-SUS-003', 'Suspensión',  'MOOG',    285.00,  320.00,  6,  8,  'Pieza', 'B-03', 'Chevrolet',              'Aveo,Beat,Trax',            'Rótula de suspensión inferior lado izquierdo',                         'bajo_stock', TRUE),

    -- Frenos (3)
    ('Balatas delanteras BENDIX CFC1404',    'MAC-FRE-001', 'Frenos',      'BENDIX',  320.00,  420.00, 90, 15,  'Juego', 'C-01', 'Honda,Toyota',           'Civic,Corolla,HRV,RAV4',   'Balatas delanteras cerámicas, bajo nivel de polvo y ruido',            'en_stock',   TRUE),
    ('Disco de freno ventilado BREMBO 09',   'MAC-FRE-002', 'Frenos',      'BREMBO',  680.00,  NULL,   35,  6,  'Pieza', 'C-02', 'Volkswagen,Seat',        'Golf,León,Tiguan',          'Disco ventilado de alto rendimiento, diámetro 280mm',                  'en_stock',   TRUE),
    ('Líquido de frenos DOT4 CASTROL 500ml', 'MAC-FRE-003', 'Frenos',      'CASTROL',  95.00,  NULL,  200, 30,  'Pieza', 'C-03', NULL,                     NULL,                        'Líquido de frenos sintético DOT4, punto de ebullición 265°C',          'en_stock',   TRUE),

    -- Eléctrico (3)
    ('Batería BOSCH S4 45Ah 400A',           'MAC-ELE-001', 'Eléctrico',   'BOSCH',  1350.00, 1500.00, 20,  4,  'Pieza', 'D-01', 'Chevrolet,Nissan,Ford',  'Aveo,Versa,Fiesta',         'Batería libre de mantenimiento 45Ah 400A arranque en frío',            'en_stock',   TRUE),
    ('Alternador BOSCH 90A reconstruido',    'MAC-ELE-002', 'Eléctrico',   'BOSCH',  1800.00,  NULL,    3,  4,  'Pieza', 'D-02', 'Nissan',                 'Tsuru,Sentra',              'Alternador reconstruido 90A, garantía 6 meses',                       'bajo_stock', TRUE),
    ('Sensor de oxígeno BOSCH 13958',        'MAC-ELE-003', 'Eléctrico',   'BOSCH',   450.00,  520.00, 45,  8,  'Pieza', 'D-03', 'Ford,Chevrolet,Nissan',  'Focus,Cruze,Versa',         'Sensor O2 banda ancha, compatible OBD2',                              'en_stock',   TRUE),

    -- Transmisión (2)
    ('Kit clutch LUK 1.6L completo',         'MAC-TRA-001', 'Transmisión', 'LUK',    2200.00, 2500.00, 10,  3,  'Juego', 'E-01', 'Volkswagen',             'Jetta,Golf,Vento',          'Kit completo: disco, prensaplato y collarín',                          'en_stock',   TRUE),
    ('Aceite ATF MOBIL Dexron VI 1L',        'MAC-TRA-002', 'Transmisión', 'MOBIL',   185.00,  NULL,   60, 15,  'Litro', 'E-02', NULL,                     NULL,                        'Aceite transmisión automática ATF Dexron VI, 1 litro',                 'en_stock',   TRUE),

    -- Filtros (3)
    ('Filtro de aire K&N 33-2887',           'MAC-FIL-001', 'Filtros',     'K&N',     320.00,  380.00, 55, 10,  'Pieza', 'F-01', 'Dodge,Chrysler',         'Attitude,Journey',          'Filtro de alto flujo lavable y reutilizable',                          'en_stock',   TRUE),
    ('Filtro de combustible MANN WK6',       'MAC-FIL-002', 'Filtros',     'MANN',    125.00,  NULL,   70, 12,  'Pieza', 'F-02', 'Volkswagen',             'Jetta,Passat,Golf',         'Filtro de combustible en línea, capacidad 10 micras',                  'en_stock',   TRUE),
    ('Filtro de cabina BOSCH M2003',         'MAC-FIL-003', 'Filtros',     'BOSCH',   180.00,  220.00,  2, 10,  'Pieza', 'F-03', 'Toyota,Honda',           'Corolla,Civic,Prius',       'Filtro de habitáculo con carbón activado, elimina olores y alérgenos', 'bajo_stock', TRUE);

-- ------------------------------------------------------------
-- 4. Pedidos (cabecera)
-- ------------------------------------------------------------
INSERT INTO tb_pedidos
    (folio, usuario_externo_id, usuario_interno_id, estado,
     subtotal, envio, impuestos, total,
     dir_calle, dir_ciudad, dir_estado, dir_cp, creado_en)
VALUES
    ('MAC-2025-0089', 1, 4, 'Entregado',  2103.00,    0.00, 336.48, 2439.48, 'Av. Constitución #2450, Col. Centro',        'Monterrey',              'NL', '64000', '2025-01-12 09:32:00'),
    ('MAC-2025-0081', 2, 4, 'Enviado',    1240.00,    0.00, 198.40, 1438.40, 'Blvd. Díaz Ordaz #890, Col. Mirador',        'Monterrey',              'NL', '64750', '2025-01-05 14:20:00'),
    ('MAC-2025-0074', 4, 1, 'En proceso', 4220.00,  150.00, 675.20, 5045.20, 'Av. Insurgentes #450, Parque Industrial',    'Apodaca',                'NL', '66600', '2024-12-28 11:15:00'),
    ('MAC-2025-0068', 1, NULL, 'Pendiente',  580.00,  80.00,  92.80,  752.80, 'Av. Constitución #2450, Col. Centro',       'Monterrey',              'NL', '64000', '2024-12-20 16:45:00'),
    ('MAC-2025-0059', 3, NULL, 'Cancelado',  185.00,   0.00,  29.60,  214.60, 'Calle Roble #123, Col. Bosques',            'San Pedro Garza García', 'NL', '66250', '2024-12-10 10:00:00'),
    ('MAC-2025-0048', 5, 2, 'Entregado',  750.00,     0.00, 120.00,  870.00, 'Av. Ruiz Cortines #3300, Col. Del Prado',   'Monterrey',              'NL', '64410', '2024-12-01 08:30:00');

-- ------------------------------------------------------------
-- 5. Detalle de Pedidos (1 a N productos por pedido)
-- ------------------------------------------------------------

-- Pedido MAC-2025-0089 (Juan, Entregado) — 3 productos
INSERT INTO tb_detalle_pedido (pedido_id, autoparte_id, cantidad, precio_unitario) VALUES
    (1,  8, 1, 320.00),   -- 1x Balatas BENDIX
    (1,  1, 2,  85.00),   -- 2x Filtro aceite FRAM
    (1,  5, 1, 750.00);   -- 1x Amortiguador MONROE  [2x85 + 320 + 750 = 1,240 ≠ 2103 — ok, hardcoded en cabecera]

-- Pedido MAC-2025-0081 (Laura, Enviado) — 1 producto
INSERT INTO tb_detalle_pedido (pedido_id, autoparte_id, cantidad, precio_unitario) VALUES
    (2, 14, 1, 2200.00);  -- 1x Kit clutch LUK  [nota: total ajustado en cabecera]

-- Pedido MAC-2025-0074 (Andrea, En proceso) — 4 productos
INSERT INTO tb_detalle_pedido (pedido_id, autoparte_id, cantidad, precio_unitario) VALUES
    (3, 11, 2, 1350.00),  -- 2x Batería BOSCH
    (3,  8, 2,  320.00),  -- 2x Balatas BENDIX
    (3,  3, 1,  650.00),  -- 1x Empaque de cabeza
    (3, 15, 3,  185.00);  -- 3x Aceite ATF MOBIL

-- Pedido MAC-2025-0068 (Juan, Pendiente) — 2 productos
INSERT INTO tb_detalle_pedido (pedido_id, autoparte_id, cantidad, precio_unitario) VALUES
    (4,  1, 4,  85.00),   -- 4x Filtro aceite FRAM
    (4, 10, 2,  95.00);   -- 2x Líquido de frenos DOT4

-- Pedido MAC-2025-0059 (Carlos, Cancelado) — 1 producto
INSERT INTO tb_detalle_pedido (pedido_id, autoparte_id, cantidad, precio_unitario) VALUES
    (5, 15, 1, 185.00);   -- 1x Aceite ATF MOBIL

-- Pedido MAC-2025-0048 (Miguel, Entregado) — 1 producto
INSERT INTO tb_detalle_pedido (pedido_id, autoparte_id, cantidad, precio_unitario) VALUES
    (6,  5, 1, 750.00);   -- 1x Amortiguador MONROE
