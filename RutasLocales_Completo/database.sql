CREATE DATABASE rutas_locales;
USE rutas_locales;

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('turista', 'guia', 'admin') DEFAULT 'turista',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE experiencias (
    id_experiencia INT AUTO_INCREMENT PRIMARY KEY,
    id_guia INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    ubicacion VARCHAR(100) NOT NULL,
    cupos_disponibles INT NOT NULL,
    FOREIGN KEY (id_guia) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

CREATE TABLE reservas (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    id_turista INT NOT NULL,
    id_experiencia INT NOT NULL,
    cantidad_personas INT NOT NULL,
    fecha_reserva DATE NOT NULL,
    estado ENUM('pendiente', 'confirmada', 'cancelada') DEFAULT 'pendiente',
    FOREIGN KEY (id_turista) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_experiencia) REFERENCES experiencias(id_experiencia) ON DELETE CASCADE
);
