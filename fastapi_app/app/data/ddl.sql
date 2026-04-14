-- ============================================================
--  DDL — MACUIN Autopartes y Distribución
--  Motor: PostgreSQL 15+
--  Ejecutar en orden (respeta dependencias entre tablas)
-- ============================================================

-- ------------------------------------------------------------
-- 1. Usuarios Externos
--    Clientes: talleres mecánicos, refaccionarias, particulares, distribuidores
--    Registrados desde Laravel (/registro) o creados por personal interno (Flask)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tb_usuarios_externos (
    id              SERIAL          PRIMARY KEY,

    -- Datos personales
    nombre          VARCHAR(50)     NOT NULL,
    apellidos       VARCHAR(100)    NOT NULL,
    email           VARCHAR(120)    NOT NULL UNIQUE,
    password        VARCHAR(255)    NOT NULL,
    telefono        VARCHAR(15),

    -- Datos empresariales (gestionados desde Flask)
    empresa         VARCHAR(150),
    tipo_cliente    VARCHAR(30)     DEFAULT 'Particular',
    -- Valores: 'Taller mecánico', 'Refaccionaria', 'Particular', 'Distribuidor'
    rfc             VARCHAR(13),
    giro            VARCHAR(60),
    -- Valores: 'Servicio automotriz', 'Venta de refacciones',
    --          'Distribución mayorista', 'Uso personal', 'Otro'

    -- Dirección de envío principal
    calle           VARCHAR(200),
    ciudad          VARCHAR(100),
    estado_geo      VARCHAR(5),
    -- Valores: 'NL', 'COAH', 'TAM', 'CHIH', 'SON', 'CDMX', 'JAL', 'QRO', etc.
    cp              VARCHAR(5),
    referencia      VARCHAR(200),

    -- Condiciones comerciales (para clientes B2B)
    lista_precio    VARCHAR(30)     DEFAULT 'Público general',
    -- Valores: 'Público general', 'Taller / Mayoreo', 'Distribuidor', 'Precio especial'
    dias_credito    INTEGER         NOT NULL DEFAULT 0 CHECK (dias_credito >= 0),
    limite_credito  NUMERIC(10, 2)  NOT NULL DEFAULT 0 CHECK (limite_credito >= 0),
    descuento       NUMERIC(5, 2)   NOT NULL DEFAULT 0 CHECK (descuento >= 0 AND descuento <= 100),

    -- Control
    notas           TEXT,
    estado          VARCHAR(20)     NOT NULL DEFAULT 'activo',
    -- Valores: 'activo', 'pendiente', 'inactivo'
    creado_en       TIMESTAMP       NOT NULL DEFAULT NOW()
);

-- ------------------------------------------------------------
-- 2. Usuarios Internos
--    Personal MACUIN: ventas, almacén, logística, administración
--    Gestionados exclusivamente desde Flask
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tb_usuarios_internos (
    id                  SERIAL          PRIMARY KEY,

    -- Datos personales
    nombre              VARCHAR(50)     NOT NULL,
    apellidos           VARCHAR(100)    NOT NULL,
    email               VARCHAR(120)    NOT NULL UNIQUE,
    password            VARCHAR(255)    NOT NULL,
    telefono            VARCHAR(15),

    -- Rol y organización
    departamento        VARCHAR(30)     NOT NULL DEFAULT 'Ventas',
    -- Valores: 'Ventas', 'Almacén', 'Logística', 'Administración'
    rol                 VARCHAR(30)     NOT NULL DEFAULT 'Ventas',
    -- Valores: 'Administrador', 'Ventas', 'Almacén', 'Logística'
    cargo               VARCHAR(80),
    sucursal            VARCHAR(80),

    -- Permisos granulares (checkboxes del formulario Flask)
    perm_autopartes     BOOLEAN         NOT NULL DEFAULT FALSE,
    perm_pedidos        BOOLEAN         NOT NULL DEFAULT FALSE,
    perm_usuarios       BOOLEAN         NOT NULL DEFAULT FALSE,
    perm_reportes       BOOLEAN         NOT NULL DEFAULT FALSE,
    perm_config         BOOLEAN         NOT NULL DEFAULT FALSE,

    -- Control
    estado              VARCHAR(20)     NOT NULL DEFAULT 'activo',
    -- Valores: 'activo', 'suspendido', 'inactivo'
    ultima_actividad    TIMESTAMP,
    creado_en           TIMESTAMP       NOT NULL DEFAULT NOW()
);

-- ------------------------------------------------------------
-- 3. Autopartes
--    Catálogo de productos — gestionado desde Flask, consumido en Laravel
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tb_autopartes (
    id                  SERIAL          PRIMARY KEY,

    -- Identificación
    nombre              VARCHAR(150)    NOT NULL,
    sku                 VARCHAR(50)     NOT NULL UNIQUE,
    categoria           VARCHAR(50)     NOT NULL,
    -- Valores: 'Motor', 'Suspensión', 'Frenos', 'Eléctrico',
    --          'Transmisión', 'Filtros', 'Carrocería', 'Climatización'
    marca               VARCHAR(80),
    -- Valores: 'Bosch', 'NGK', 'Gates', 'Monroe', 'Mann', 'Brembo', 'FRAM',
    --          'LUK', 'MOOG', 'Castrol', 'K&N', 'MACUIN', 'Otra'

    -- Precios (precio_original para mostrar descuento en Laravel)
    precio              NUMERIC(10, 2)  NOT NULL CHECK (precio >= 0),
    precio_original     NUMERIC(10, 2)  CHECK (precio_original >= 0),
    -- Si precio_original IS NOT NULL, se muestra tachado en el catálogo

    -- Inventario
    stock               INTEGER         NOT NULL DEFAULT 0 CHECK (stock >= 0),
    stock_minimo        INTEGER         NOT NULL DEFAULT 0 CHECK (stock_minimo >= 0),
    unidad              VARCHAR(20)     NOT NULL DEFAULT 'Pieza',
    -- Valores: 'Pieza', 'Par', 'Juego', 'Litro', 'Metro'
    ubicacion           VARCHAR(50),
    -- Ubicación física en almacén, ej: 'A-03', 'B-12'

    -- Compatibilidad de vehículos (para el selector Año→Marca→Modelo de Laravel)
    marca_vehiculo      VARCHAR(100),
    modelo_vehiculo     VARCHAR(200),
    aplicacion          TEXT,

    -- Otros
    descripcion         TEXT,
    imagen              VARCHAR(255),
    notas               TEXT,

    -- Control
    estado              VARCHAR(20)     NOT NULL DEFAULT 'en_stock',
    -- Valores: 'en_stock', 'bajo_stock', 'sin_stock'
    activo              BOOLEAN         NOT NULL DEFAULT TRUE
);

-- ------------------------------------------------------------
-- 4. Pedidos (cabecera)
--    Creados desde Laravel (checkout) o desde Flask (gestión interna)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tb_pedidos (
    id                  SERIAL          PRIMARY KEY,

    -- Folio visible: MAC-2024-0089 (generado en la aplicación)
    folio               VARCHAR(20)     UNIQUE,

    -- Relaciones
    usuario_externo_id  INTEGER         NOT NULL REFERENCES tb_usuarios_externos(id),
    usuario_interno_id  INTEGER         REFERENCES tb_usuarios_internos(id),
    -- usuario_interno_id = quien gestionó/creó desde Flask (nullable si viene de Laravel)

    -- Estado
    estado              VARCHAR(20)     NOT NULL DEFAULT 'Pendiente',
    -- Valores: 'Pendiente', 'En proceso', 'Enviado', 'Entregado', 'Cancelado'

    -- Totales
    subtotal            NUMERIC(10, 2)  NOT NULL DEFAULT 0 CHECK (subtotal >= 0),
    envio               NUMERIC(10, 2)  NOT NULL DEFAULT 0 CHECK (envio >= 0),
    impuestos           NUMERIC(10, 2)  NOT NULL DEFAULT 0 CHECK (impuestos >= 0),
    total               NUMERIC(10, 2)  NOT NULL DEFAULT 0 CHECK (total >= 0),

    -- Dirección de entrega (copiada del checkout, no FK para preservar histórico)
    dir_calle           VARCHAR(200),
    dir_ciudad          VARCHAR(100),
    dir_estado          VARCHAR(5),
    dir_cp              VARCHAR(5),

    -- Logística del pedido
    metodo_envio        VARCHAR(30)     NOT NULL DEFAULT 'estandar',
    -- Valores: 'express', 'estandar', 'recoger'
    notas               TEXT,

    creado_en           TIMESTAMP       NOT NULL DEFAULT NOW()
);

-- ------------------------------------------------------------
-- 5. Detalle Pedido (líneas — 1 a N productos por pedido)
--    Cubre el requisito: "solicitar de 1 a N productos"
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tb_detalle_pedido (
    id                  SERIAL          PRIMARY KEY,
    pedido_id           INTEGER         NOT NULL REFERENCES tb_pedidos(id) ON DELETE CASCADE,
    autoparte_id        INTEGER         NOT NULL REFERENCES tb_autopartes(id),
    cantidad            INTEGER         NOT NULL CHECK (cantidad >= 1),
    precio_unitario     NUMERIC(10, 2)  NOT NULL CHECK (precio_unitario >= 0),
    subtotal            NUMERIC(10, 2)  GENERATED ALWAYS AS (cantidad * precio_unitario) STORED,

    -- Evitar duplicar la misma autoparte en un pedido
    UNIQUE (pedido_id, autoparte_id)
);
