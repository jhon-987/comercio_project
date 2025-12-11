CREATE DATABASE IF NOT EXISTS comercio_db;
USE comercio_db;

-- ==========================================================
-- 1. TABLA DE CATEGORIAS
-- ==========================================================
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) UNIQUE NOT NULL,
    descripcion TEXT
);

-- ==========================================================
-- 2. TABLA DE USUARIOS
-- ==========================================================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('cliente', 'admin', 'empleado') NOT NULL DEFAULT 'cliente',
    direccion TEXT,
    telefono VARCHAR(20),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================================
-- 3. TABLA DE PRODUCTOS
-- ==========================================================
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    categoria_id INT,
    sku VARCHAR(50) UNIQUE,
    imagen_url VARCHAR(255),
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL 
);

-- ==========================================================
-- 4. TABLA DE PEDIDOS (CORREGIDA: ON DELETE CASCADE)
-- ==========================================================
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('pendiente', 'procesando', 'enviado', 'entregado', 'cancelado') NOT NULL DEFAULT 'pendiente',
    total DECIMAL(10, 2) NOT NULL,
    nombre_cliente VARCHAR(100),
    direccion_envio TEXT, 
    metodo_pago VARCHAR(50) NOT NULL,
    referencia_pago VARCHAR(100), 

    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- ==========================================================
-- 5. TABLA DE DETALLE DE PEDIDOS (CORREGIDA: ON DELETE CASCADE)
-- ==========================================================
CREATE TABLE detalle_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL, 
    
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- ==========================================================
-- 6. TABLA DE CARRITO (CORREGIDA: ON DELETE CASCADE)
-- ==========================================================
CREATE TABLE carrito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNIQUE NOT NULL, -- Un carrito por usuario logueado
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE item_carrito (
    id INT AUTO_INCREMENT PRIMARY KEY,
    carrito_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    

    FOREIGN KEY (carrito_id) REFERENCES carrito(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    UNIQUE KEY uk_carrito_producto (carrito_id, producto_id)
);

select* from usuarios;

INSERT INTO categorias (id, nombre) VALUES
(1, 'Electrónica'),
(2, 'Ropa'),
(3, 'Hogar'),
(4, 'Libros'),
(5, 'Deportes');

INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id, sku, imagen_url, activo) VALUES
-- Electrónica (ID 1)
('Smartphone X10 Pro', 'Teléfono móvil de alta gama con cámara 108MP y 12GB RAM.', 799.99, 50, 1, 'SMARTX10P', 'url_imagen/smartx10p.jpg', TRUE),
('Audífonos Bluetooth A30', 'Auriculares inalámbricos con cancelación de ruido activa y 20 horas de batería.', 69.50, 120, 1, 'AUDIOA30', 'url_imagen/audioa30.jpg', TRUE),
('Laptop Gamer R-Force', 'Portátil con procesador i7 de última generación, ideal para juegos y diseño.', 1350.00, 30, 1, 'LAPTOPRF', 'url_imagen/laptoprf.jpg', TRUE),
('Mouse Inalámbrico Ergo', 'Mouse ergonómico recargable con conexión USB y Bluetooth.', 18.99, 200, 1, 'MOUSEERGO', 'url_imagen/mouseergo.jpg', TRUE),
('Teclado Mecánico RGB', 'Teclado mecánico con switches táctiles y retroiluminación RGB personalizable.', 45.75, 80, 1, 'TECLARGB', 'url_imagen/teclargb.jpg', TRUE),

-- Ropa (ID 2)
('Camisa Casual Lino', 'Camisa de lino 100% transpirable, ideal para el verano.', 35.00, 150, 2, 'CAMISALN', 'url_imagen/camisaln.jpg', TRUE),
('Jeans Slim Fit Negro', 'Pantalón denim corte slim fit, 98% algodón.', 49.99, 90, 2, 'JEANSLF', 'url_imagen/jeanslf.jpg', TRUE),
('Zapatillas Deportivas Ultra', 'Zapatillas ligeras con suela de amortiguación avanzada para correr.', 85.50, 60, 2, 'ZAPULTRA', 'url_imagen/zapultra.jpg', TRUE),
('Chaqueta Impermeable Explorer', 'Chaqueta ligera y resistente al agua, perfecta para senderismo.', 62.00, 45, 2, 'CHAIMPER', 'url_imagen/chaimper.jpg', TRUE),

-- Hogar (ID 3)
('Juego de Sábanas Queen', 'Juego de sábanas de algodón egipcio de 400 hilos, color blanco.', 59.90, 75, 3, 'SABANASQ', 'url_imagen/sabanasq.jpg', TRUE),
('Cafetera Programable Digital', 'Cafetera con capacidad para 12 tazas y temporizador programable.', 39.95, 110, 3, 'CAFEDIGI', 'url_imagen/cafedigi.jpg', TRUE),
('Aspiradora Robot Inteligente', 'Aspiradora que mapea la casa y se recarga automáticamente.', 250.00, 25, 3, 'ASPIROB', 'url_imagen/aspirob.jpg', TRUE),
('Set de Cuchillos Chef (10 pzas)', 'Juego profesional de cuchillos de acero inoxidable con base de madera.', 95.50, 40, 3, 'SETCUCHI', 'url_imagen/setcuchi.jpg', TRUE),

-- Libros (ID 4)
('La Guía de Programación', 'Libro sobre patrones de diseño y desarrollo de software moderno.', 25.99, 300, 4, 'LIBROPROG', 'url_imagen/libroprog.jpg', TRUE),
('Novela Clásica "El Viento"', 'Edición de tapa dura de una novela atemporal.', 15.00, 180, 4, 'LIBRNOVE', 'url_imagen/librnove.jpg', TRUE),

-- Deportes (ID 5)
('Mancuernas Ajustables (Par)', 'Pesas de 2.5kg a 15kg, ajustables con sistema de dial rápido.', 120.00, 35, 5, 'MANCUAJ', 'url_imagen/mancuaj.jpg', TRUE),
('Colchoneta Yoga Pro', 'Colchoneta de alta densidad, antideslizante de 6mm.', 22.50, 95, 5, 'YOGAPRO', 'url_imagen/yogapro.jpg', TRUE),

-- Productos Variados / Nuevos
('Webcam HD 1080p', 'Cámara web con micrófono incorporado, ideal para videollamadas.', 29.99, 130, 1, 'WEBCHD', 'url_imagen/webchd.jpg', TRUE),
('Mesa Auxiliar Madera', 'Mesa pequeña de madera maciza, estilo nórdico.', 75.00, 20, 3, 'MESAMAD', 'url_imagen/mesamad.jpg', TRUE),
('Termo de Acero Inoxidable (1L)', 'Mantiene la temperatura hasta por 12 horas, ideal para viajes.', 14.75, 250, 3, 'TERMO1L', 'url_imagen/termo1l.jpg', TRUE);
