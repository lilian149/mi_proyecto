-- ============================================================
-- MIGRACIÓN: Tabla pedidos (comidas del menú)
-- Ejecutar en phpMyAdmin sobre la BD sgat_urmiri
-- ============================================================

CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    items JSON NOT NULL COMMENT 'Lista de ítems: [{id, nombre, precio, qty, tipo}]',
    total DECIMAL(10,2) NOT NULL,
    metodo_pago ENUM('efectivo','qr') DEFAULT 'efectivo',
    estado ENUM('pendiente','pagado','cancelado') DEFAULT 'pagado',
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Índice para consultas de reportes por fecha
CREATE INDEX IF NOT EXISTS idx_pedidos_fecha ON pedidos(fecha);
CREATE INDEX IF NOT EXISTS idx_pedidos_estado ON pedidos(estado);
