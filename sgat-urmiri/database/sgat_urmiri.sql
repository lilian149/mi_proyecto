-- ============================================================
-- SGAT-Urmiri: Sistema de Gestión y Automatización Turística
-- Base de Datos MySQL
-- ============================================================

CREATE DATABASE IF NOT EXISTS sgat_urmiri CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sgat_urmiri;

-- ------------------------------------------------------------
-- TABLA: usuarios
-- ------------------------------------------------------------
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(150) NOT NULL,
    ci VARCHAR(20) NOT NULL UNIQUE,
    correo VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    rol ENUM('administrador','operador','turista') DEFAULT 'turista',
    activo TINYINT(1) DEFAULT 1,
    token_recuperacion VARCHAR(100) DEFAULT NULL,
    token_expira DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABLA: habitaciones
-- ------------------------------------------------------------
CREATE TABLE habitaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    capacidad INT NOT NULL DEFAULT 1,
    precio_noche DECIMAL(10,2) NOT NULL,
    imagen VARCHAR(255) DEFAULT 'default.jpg',
    estado ENUM('disponible','ocupada','mantenimiento') DEFAULT 'disponible',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABLA: servicios
-- ------------------------------------------------------------
CREATE TABLE servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) DEFAULT 0.00,
    icono VARCHAR(50) DEFAULT 'bi-star',
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABLA: reservas
-- ------------------------------------------------------------
CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    usuario_id INT NOT NULL,
    habitacion_id INT NOT NULL,
    fecha_ingreso DATE NOT NULL,
    fecha_salida DATE NOT NULL,
    cantidad_personas INT NOT NULL DEFAULT 1,
    num_noches INT NOT NULL,
    precio_noche DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente','confirmada','cancelada') DEFAULT 'pendiente',
    observaciones TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABLA: reserva_servicios (relación N:M)
-- ------------------------------------------------------------
CREATE TABLE reserva_servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,
    servicio_id INT NOT NULL,
    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABLA: pagos
-- ------------------------------------------------------------
CREATE TABLE pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    metodo ENUM('efectivo','transferencia','tarjeta') DEFAULT 'efectivo',
    estado ENUM('pendiente','completado','reembolsado') DEFAULT 'pendiente',
    comprobante VARCHAR(255),
    fecha_pago DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABLA: asistencia (check-in / check-out)
-- ------------------------------------------------------------
CREATE TABLE asistencia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,
    tipo ENUM('checkin','checkout') NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    observacion TEXT,
    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- TABLA: reportes (log de actividad)
-- ------------------------------------------------------------
CREATE TABLE reportes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(50),
    descripcion TEXT,
    usuario_id INT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- DATOS INICIALES
-- ============================================================

-- Admin por defecto: admin@urmiri.bo / admin1234
INSERT INTO usuarios (nombre_completo, ci, correo, telefono, password, rol) VALUES
('Administrador Urmiri', '1234567', 'admin@urmiri.bo', '71234567',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'administrador'),
('Operador Turismo', '7654321', 'operador@urmiri.bo', '77654321',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'operador');

-- Habitaciones de ejemplo
INSERT INTO habitaciones (nombre, descripcion, capacidad, precio_noche, imagen, estado) VALUES
('Suite Termal Premium','Amplia suite con vista panorámica a las termas y jacuzzi privado. Incluye desayuno.',4,350.00,'habi3.jpg','disponible'),
('Cabaña Familiar','Acogedora cabaña con 2 habitaciones, sala y cocina equipada. Ideal para familias.',6,280.00,'habi3.jpg','disponible'),
('Habitación Doble Estándar','Habitación confortable con cama doble y baño privado. Acceso a piscinas termales.',2,150.00,'habi1.jpg','disponible'),
('Suite Romántica','Suite exclusiva con bañera de hidromasaje y decoración romántica.',2,400.00,'habi2.jpg','disponible'),
('Habitación Simple','Habitación individual acogedora con todas las comodidades básicas.',1,90.00,'habi4.jpg','disponible'),
('Cabaña Ejecutiva','Cabaña moderna con sala de reuniones, ideal para retiros corporativos.',8,550.00,'habi4.jpg','disponible');

-- Servicios turísticos
INSERT INTO servicios (nombre, descripcion, precio, icono) VALUES
('Piscinas Termales','Acceso ilimitado a piscinas de aguas termales naturales.',0.00,'bi-water'),
('Masajes Terapéuticos','Masajes relajantes y terapéuticos a cargo de especialistas.',80.00,'bi-heart-pulse'),
('Desayuno Buffet','Desayuno tipo buffet con productos locales y nacionales.',35.00,'bi-egg-fried'),
('Tour Altiplano','Excursión guiada por el altiplano paceño y salares.',120.00,'bi-map'),
('Servicio de Spa','Tratamientos de belleza y relajación completos.',150.00,'bi-stars'),
('Alquiler de Bicicletas','Recorrido en bicicleta por los alrededores.',20.00,'bi-bicycle');

-- ============================================================
-- CORRECCIÓN: Sincronizar estado de habitaciones con reservas activas
-- Ejecutar si hay habitaciones que deberían estar ocupadas
-- ============================================================
UPDATE habitaciones h
SET h.estado = 'ocupada'
WHERE h.id IN (
  SELECT DISTINCT habitacion_id FROM reservas
  WHERE estado IN ('pendiente', 'confirmada')
);

-- Las que no tienen reservas activas, marcarlas disponibles
-- (solo si están en estado ocupada sin reserva activa)
UPDATE habitaciones h
SET h.estado = 'disponible'
WHERE h.estado = 'ocupada'
  AND h.id NOT IN (
    SELECT DISTINCT habitacion_id FROM reservas
    WHERE estado IN ('pendiente', 'confirmada')
  );
