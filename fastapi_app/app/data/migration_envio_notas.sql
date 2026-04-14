-- Migración: agregar metodo_envio y notas a tb_pedidos
-- Ejecutar una sola vez sobre la BD en ejecución.
-- Los pedidos existentes quedan con metodo_envio = 'estandar' y notas = NULL.

ALTER TABLE tb_pedidos
    ADD COLUMN IF NOT EXISTS metodo_envio VARCHAR(30) NOT NULL DEFAULT 'estandar',
    ADD COLUMN IF NOT EXISTS notas TEXT;
