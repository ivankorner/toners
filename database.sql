-- Base de datos para el sistema de inventario de toners
CREATE DATABASE IF NOT EXISTS inventario_toners;
USE inventario_toners;

-- Tabla para los modelos de toners
CREATE TABLE toners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    modelo VARCHAR(100) NOT NULL UNIQUE,
    detalle TEXT,
    modelo_impresora VARCHAR(100),
    implementada VARCHAR(255),
    cantidad_actual INT DEFAULT 0,
    cantidad_minima INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para los ingresos de toners
CREATE TABLE ingresos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_ingreso DATE NOT NULL,
    modelo_id INT,
    cantidad INT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (modelo_id) REFERENCES toners(id)
);

-- Tabla para los egresos de toners
CREATE TABLE egresos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_egreso DATE NOT NULL,
    modelo_id INT,
    cantidad INT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (modelo_id) REFERENCES toners(id)
);

-- Tabla para los drums vinculados a los toners
CREATE TABLE drums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    toner_id INT NOT NULL,
    modelo VARCHAR(100),
    cantidad_actual INT DEFAULT 0,
    cantidad_minima INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (toner_id) REFERENCES toners(id) ON DELETE CASCADE
);

-- Tabla para los ingresos de drums
CREATE TABLE ingresos_drums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_ingreso DATE NOT NULL,
    drum_id INT,
    cantidad INT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (drum_id) REFERENCES drums(id)
);

-- Tabla para los egresos de drums
CREATE TABLE egresos_drums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_egreso DATE NOT NULL,
    drum_id INT,
    cantidad INT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (drum_id) REFERENCES drums(id)
);

-- Insertar algunos datos de ejemplo
INSERT INTO toners (modelo, detalle, modelo_impresora, implementada, cantidad_actual, cantidad_minima) VALUES
('HP LaserJet Pro M404', 'Toner negro para HP LaserJet Pro M404', 'HP LaserJet Pro M404dn', 'Oficina Central, Recepción', 10, 2),
('Canon ImageCLASS MF644', 'Toner negro para Canon ImageCLASS MF644', 'Canon imageCLASS MF644Cdw', 'Oficina Administrativa', 5, 1),
('Brother HL-L2350DW', 'Toner negro para Brother HL-L2350DW', 'Brother HL-L2350DW', 'Oficina de Ventas, Sala de Reuniones', 8, 2);

-- Insertar drums de ejemplo para los toners
INSERT INTO drums (toner_id, modelo, cantidad_actual, cantidad_minima) VALUES
(1, 'DR-1663', 3, 1),
(2, 'DR-2255', 2, 1),
(3, 'DR-3479', 4, 1);

-- Tabla para los usuarios del sistema
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL
);

-- Insertar usuario administrador por defecto
INSERT INTO usuarios (nombre_usuario, contrasena) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password
