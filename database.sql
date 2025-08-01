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

-- Insertar algunos datos de ejemplo
INSERT INTO toners (modelo, detalle, modelo_impresora, implementada, cantidad_actual, cantidad_minima) VALUES
('HP LaserJet Pro M404', 'Toner negro para HP LaserJet Pro M404', 'HP LaserJet Pro M404dn', 'Oficina Central, Recepción', 10, 2),
('Canon ImageCLASS MF644', 'Toner negro para Canon ImageCLASS MF644', 'Canon imageCLASS MF644Cdw', 'Oficina Administrativa', 5, 1),
('Brother HL-L2350DW', 'Toner negro para Brother HL-L2350DW', 'Brother HL-L2350DW', 'Oficina de Ventas, Sala de Reuniones', 8, 2);
