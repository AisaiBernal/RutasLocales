-- RutasLocales · Base de datos (MySQL 5.7+ / MariaDB)
-- Importar desde phpMyAdmin o:  mysql -u root < database.sql
CREATE DATABASE IF NOT EXISTS rutas_locales CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rutas_locales;

DROP TABLE IF EXISTS reservas;
DROP TABLE IF EXISTS experiencias;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('turista','guia','admin') NOT NULL DEFAULT 'turista',
    telefono VARCHAR(20) NULL,
    bio VARCHAR(300) NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL UNIQUE,
    icono VARCHAR(40) NOT NULL DEFAULT 'fa-compass'
);

CREATE TABLE experiencias (
    id_experiencia INT AUTO_INCREMENT PRIMARY KEY,
    id_guia INT NOT NULL,
    id_categoria INT NULL,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    ubicacion VARCHAR(100) NOT NULL,
    duracion_horas INT NOT NULL DEFAULT 3,
    cupos_disponibles INT NOT NULL,
    imagen_url VARCHAR(400) NULL,
    activa TINYINT(1) NOT NULL DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_guia) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria) ON DELETE SET NULL
);

CREATE TABLE reservas (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    id_turista INT NOT NULL,
    id_experiencia INT NOT NULL,
    cantidad_personas INT NOT NULL,
    fecha_reserva DATE NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('pendiente','confirmada','cancelada') NOT NULL DEFAULT 'pendiente',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_turista) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_experiencia) REFERENCES experiencias(id_experiencia) ON DELETE CASCADE
);

-- Datos de prueba · contraseña de los 3 usuarios: Demo1234
INSERT INTO usuarios (nombre,email,password,rol,telefono) VALUES
('Administrador','admin@rutaslocales.com','$2b$12$h6.94tsN4zd/ycD9JvqkLe2Wdo6Xe6Y2xjEm2/ykKS/7MwEhwICSu','admin','3000000000'),
('Camila Rojas (guía)','guia@rutaslocales.com','$2b$12$h6.94tsN4zd/ycD9JvqkLe2Wdo6Xe6Y2xjEm2/ykKS/7MwEhwICSu','guia','3011112222'),
('Andrés Pérez','turista@rutaslocales.com','$2b$12$h6.94tsN4zd/ycD9JvqkLe2Wdo6Xe6Y2xjEm2/ykKS/7MwEhwICSu','turista',NULL);

INSERT INTO categorias (nombre,icono) VALUES
('Ecoturismo','fa-leaf'),('Historia y cultura','fa-landmark'),('Aventura','fa-person-hiking'),('Gastronomía','fa-utensils'),('Paisajismo','fa-camera');

INSERT INTO experiencias (id_guia,id_categoria,titulo,descripcion,precio,ubicacion,duracion_horas,cupos_disponibles,imagen_url) VALUES
(2,1,'Caminata Páramo de Sumapaz','Recorrido guiado por el páramo más grande del mundo: frailejones, lagunas de altura y fauna andina. Incluye hidratación y seguro de viaje.',50000,'Bogotá, Colombia',5,12,'https://images.unsplash.com/photo-1590479773265-7464e5d48118?auto=format&fit=crop&q=80&w=800'),
(2,2,'Salto del Tequendama y Museo','Visita al mirador del salto y a la Casa Museo del Salto del Tequendama, con historia del lugar y la leyenda muisca.',35000,'Soacha, Cundinamarca',3,15,'https://images.unsplash.com/photo-1549891823-c28bc74a6f7b?auto=format&fit=crop&q=80&w=800'),
(2,4,'Ruta del café y la panela','Visita a una finca familiar, proceso del grano a la taza y degustación de panela artesanal.',65000,'Sasaima, Cundinamarca',6,8,NULL),
(2,3,'Kayak en la laguna de Guatavita','Remo suave con guía certificado y explicación del origen de la leyenda de El Dorado.',80000,'Guatavita, Cundinamarca',4,6,NULL);

INSERT INTO reservas (id_turista,id_experiencia,cantidad_personas,fecha_reserva,total,estado) VALUES
(3,1,2,DATE_ADD(CURDATE(),INTERVAL 10 DAY),100000,'confirmada'),
(3,2,1,DATE_ADD(CURDATE(),INTERVAL 20 DAY),35000,'pendiente'),
(3,3,3,DATE_ADD(CURDATE(),INTERVAL 5 DAY),195000,'pendiente');
