-- Tabla Usuario
CREATE TABLE tbl_usuario (
    usuario_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(50) NOT NULL,
    identidad CHAR(13) UNIQUE NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    numero_cuenta CHAR(11) UNIQUE NOT NULL,
    contrasenia VARCHAR(255) NOT NULL,
    telefono CHAR(8),
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    INDEX idx_usuario_correo (correo)
);

CREATE TABLE tbl_tipo_documento(
    tipo_documento_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(10) NOT NULL,
    id CHAR(36) NOT NULL DEFAULT (UUID())
);

CREATE TABLE tbl_documento(
    documento_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero_documento VARCHAR(13) UNIQUE NOT NULL,
    tipo_documento_id TINYINT UNSIGNED NOT NULL,
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    FOREIGN KEY (tipo_documento_id) REFERENCES tbl_tipo_documento(tipo_documento_id) 
);

-- Tabla Facultad
CREATE TABLE tbl_facultad (
    facultad_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_facultad VARCHAR(100) NOT NULL,
    id CHAR(36) NOT NULL DEFAULT (UUID()),
    INDEX idx_facultad_nombre (nombre_facultad)
);

-- Tabla Centro Regional
CREATE TABLE tbl_centro_regional (
    centro_regional_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_centro VARCHAR(100) NOT NULL,
    ubicacion VARCHAR(255) NOT NULL,
    codigo_centro VARCHAR(10) NOT NULL,
    id CHAR(36) NOT NULL DEFAULT (UUID()),    
    INDEX idx_centroregional_codigo (codigo_centro)
);

CREATE TABLE tbl_revisor (
    revisor_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id SMALLINT UNSIGNED NOT NULL,
    id CHAR(36) NOT NULL DEFAULT (UUID()),    
    FOREIGN KEY (usuario_id) REFERENCES tbl_usuario(usuario_id) ON DELETE CASCADE
);

-- Tabla Carrera
CREATE TABLE tbl_carrera (
    carrera_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_carrera VARCHAR(5) UNIQUE NOT NULL,
    nombre_carrera VARCHAR(100) NOT NULL,
    duracion DECIMAL(2,1) UNSIGNED NOT NULL,
    grado ENUM('Licenciatura', 'Técnico Universitario', 'Maestría') NOT NULL,
    facultad_id TINYINT UNSIGNED DEFAULT NULL,
    id CHAR(36) NOT NULL DEFAULT (UUID()),     
    FOREIGN KEY (facultad_id) REFERENCES tbl_facultad(facultad_id),
    INDEX idx_carrera_nombre (nombre_carrera)
);

CREATE TABLE tbl_carrera_x_centro_regional(
    carrera_id TINYINT UNSIGNED NOT NULL,
    centro_regional_id TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (carrera_id, centro_regional_id),
    FOREIGN KEY (carrera_id) REFERENCES tbl_carrera(carrera_id) ON DELETE CASCADE,
    FOREIGN KEY (centro_regional_id) REFERENCES tbl_centro_regional(centro_regional_id) ON DELETE CASCADE
);

-- Tabla Admisión
CREATE TABLE tbl_admision (
    admision_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    primer_nombre VARCHAR(50) NOT NULL,
    segundo_nombre VARCHAR(50),
    primer_apellido VARCHAR(50) NOT NULL,
    segundo_apellido VARCHAR(50),
    correo VARCHAR(100) UNIQUE NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    numero_telefono VARCHAR(15) UNIQUE NOT NULL,
    documento_id TINYINT UNSIGNED NOT NULL,
    centro_regional_id TINYINT UNSIGNED NOT NULL,
    carrera_id TINYINT UNSIGNED NOT NULL,
    carrera_secundaria_id TINYINT UNSIGNED NOT NULL,
    certificado_secundaria TEXT NOT NULL,
    id CHAR(36) NOT NULL DEFAULT (UUID()),    
    FOREIGN KEY (documento_id) REFERENCES tbl_documento(documento_id),
    FOREIGN KEY (centro_regional_id) REFERENCES tbl_centro_regional(centro_regional_id),
    FOREIGN KEY (carrera_id) REFERENCES tbl_carrera(carrera_id),
    FOREIGN KEY (carrera_secundaria_id) REFERENCES tbl_carrera(carrera_id)
);

CREATE TABLE tbl_rol(
    rol_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(20) UNIQUE NOT NULL,
    id CHAR(36) NOT NULL DEFAULT (UUID())  	
);

CREATE TABLE tbl_usuario_x_rol(
    usuario_id SMALLINT UNSIGNED NOT NULL,
    rol_id SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (usuario_id, rol_id),
    FOREIGN KEY (usuario_id) REFERENCES tbl_usuario(usuario_id) ON DELETE CASCADE,
    FOREIGN KEY (rol_id) REFERENCES tbl_rol(rol_id) ON DELETE CASCADE
);

CREATE TABLE tbl_solicitud (
    solicitud_id SMALLINT UNSIGNED PRIMARY KEY,
    estado ENUM('Pendiente', 'Aprobada', 'Rechazada') NOT NULL DEFAULT 'Pendiente',
    codigo VARCHAR(20) UNIQUE NOT NULL,
    observaciones TEXT DEFAULT NULL,
    id CHAR(36) NOT NULL DEFAULT (UUID()),    
    FOREIGN KEY (solicitud_id) REFERENCES tbl_admision(admision_id) ON DELETE CASCADE
);

CREATE TABLE tbl_departamento (
    departamento_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    facultad_id TINYINT UNSIGNED NOT NULL,
    id CHAR(36) NOT NULL DEFAULT (UUID()),    
    FOREIGN KEY (facultad_id) REFERENCES tbl_facultad(facultad_id),
    INDEX idx_departamento (nombre)
);

-- Tabla Estudiante
CREATE TABLE tbl_estudiante (
    estudiante_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id SMALLINT UNSIGNED UNIQUE,
    carrera_id TINYINT UNSIGNED NOT NULL,
    centro_regional_id TINYINT UNSIGNED NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    id CHAR(36) NOT NULL DEFAULT (UUID()),    
    FOREIGN KEY (usuario_id) REFERENCES tbl_usuario(usuario_id),
    FOREIGN KEY (carrera_id) REFERENCES tbl_carrera(carrera_id),
    FOREIGN KEY (centro_regional_id) REFERENCES tbl_centro_regional(centro_regional_id)
);

-- Tabla Docente
CREATE TABLE tbl_docente (
    docente_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id SMALLINT UNSIGNED UNIQUE,
    carrera_id TINYINT UNSIGNED NOT NULL,
    departamento_id TINYINT UNSIGNED NOT NULL,
    centro_regional_id TINYINT UNSIGNED NOT NULL,
    foto_perfil VARCHAR(100),
    descripcion VARCHAR(100),
    id CHAR(36) NOT NULL DEFAULT (UUID()),    
    FOREIGN KEY (usuario_id) REFERENCES tbl_usuario(usuario_id),
    FOREIGN KEY (centro_regional_id) REFERENCES tbl_centro_regional(centro_regional_id),
    FOREIGN KEY (departamento_id) REFERENCES tbl_departamento(departamento_id),
    FOREIGN KEY (carrera_id) REFERENCES tbl_carrera(carrera_id)
);

-- Tabla Coordinador
CREATE TABLE tbl_coordinador (
    coordinador_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    docente_id SMALLINT UNSIGNED UNIQUE,
    id CHAR(36) NOT NULL DEFAULT (UUID()),    
    FOREIGN KEY (docente_id) REFERENCES tbl_docente(docente_id)
);

CREATE TABLE tbl_jefe(
    jefe_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    docente_id SMALLINT UNSIGNED UNIQUE,
    id CHAR(36) NOT NULL DEFAULT (UUID()),    
    FOREIGN KEY (docente_id) REFERENCES tbl_docente(docente_id)
);


CREATE TABLE tbl_edificio(
    edificio_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    facultad_id TINYINT UNSIGNED NOT NULL,
    centro_regional_id TINYINT UNSIGNED NOT NULL,
    edificio varchar(50),
    id CHAR(36) NOT NULL DEFAULT (UUID()),    
    FOREIGN KEY (centro_regional_id) REFERENCES tbl_centro_regional(centro_regional_id),
    FOREIGN KEY (facultad_id) REFERENCES tbl_facultad(facultad_id)
);

CREATE TABLE tbl_clase (
    clase_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    edificio_id SMALLINT UNSIGNED NOT NULL,
    departamento_id TINYINT UNSIGNED NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    codigo VARCHAR(20) UNIQUE NOT NULL,
    UV TINYINT UNSIGNED,
    id CHAR(36) NOT NULL DEFAULT (UUID()),    
    FOREIGN KEY (edificio_id) REFERENCES tbl_edificio(edificio_id),
    FOREIGN KEY (departamento_id) REFERENCES tbl_departamento(departamento_id)
);

CREATE TABLE tbl_clase_carrera (
    clase_id SMALLINT UNSIGNED NOT NULL,
    carrera_id TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (clase_id, carrera_id), 
    FOREIGN KEY (carrera_id) REFERENCES tbl_carrera(carrera_id),
    FOREIGN KEY (clase_id) REFERENCES tbl_clase(clase_id)
);


CREATE TABLE tbl_aula (
    aula_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    aula VARCHAR(20) NOT NULL,
    edificio_id SMALLINT UNSIGNED NOT NULL,
    id CHAR(36) NOT NULL DEFAULT (UUID()),    
    FOREIGN KEY (edificio_id) REFERENCES tbl_edificio(edificio_id)
);

-- Tabla Sección
CREATE TABLE tbl_seccion (
    seccion_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clase_id SMALLINT UNSIGNED NOT NULL,
    docente_id SMALLINT UNSIGNED NOT NULL,
    aula_id SMALLINT UNSIGNED NOT NULL,
    periodo_academico VARCHAR(20) NOT NULL,
    horario VARCHAR(50),
    dias VARCHAR(50),
    cupo_maximo TINYINT UNSIGNED NOT NULL,
    centro_regional_id TINYINT UNSIGNED,
    id CHAR(36) NOT NULL DEFAULT (UUID()),    
    FOREIGN KEY (docente_id) REFERENCES tbl_docente(docente_id),
    FOREIGN KEY (aula_id) REFERENCES tbl_aula(aula_id),
    FOREIGN KEY (centro_regional_id) REFERENCES tbl_centro_regional(centro_regional_id),
    FOREIGN KEY (clase_id) REFERENCES tbl_clase(clase_id)
);

CREATE TABLE tbl_recurso(
        seccion_id SMALLINT UNSIGNED PRIMARY KEY,
        imagen_portada VARCHAR(500),
        video VARCHAR(500),
        FOREIGN KEY (seccion_id) REFERENCES tbl_seccion(seccion_id)
);


CREATE TABLE tbl_estado_matricula (
    estado_matricula_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    estado_matricula VARCHAR(9)
);

CREATE TABLE tbl_info_matricula (
    inicio DATE,
    final DATE,
    PRIMARY KEY (inicio, final),
    estado_matricula_id TINYINT UNSIGNED NOT NULL,
    FOREIGN KEY (estado_matricula_id) REFERENCES tbl_estado_matricula(estado_matricula_id)
);

CREATE TABLE tbl_info_notas (
    inicio DATE,
    final DATE,
    PRIMARY KEY (inicio, final),
    estado_matricula_id TINYINT UNSIGNED NOT NULL,
    FOREIGN KEY (estado_matricula_id) REFERENCES tbl_estado_matricula(estado_matricula_id)
);

CREATE TABLE tbl_info_add_can (
    inicio DATE,
    final DATE,
    PRIMARY KEY (inicio, final),
    estado_matricula_id TINYINT UNSIGNED NOT NULL,
    FOREIGN KEY (estado_matricula_id) REFERENCES tbl_estado_matricula(estado_matricula_id)
);


-- Tabla Matrícula
CREATE TABLE tbl_matricula (
    estudiante_id SMALLINT UNSIGNED NOT NULL,
    seccion_id SMALLINT UNSIGNED NOT NULL,
    fechaInscripcion DATE NOT NULL,
    PRIMARY KEY (estudiante_id, seccion_id),
    FOREIGN KEY (estudiante_id) REFERENCES tbl_estudiante(estudiante_id),
    FOREIGN KEY (seccion_id) REFERENCES tbl_seccion(seccion_id)
);

CREATE TABLE tbl_observacion(
    observacion_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    observacion VARCHAR(3),
    id CHAR(36) NOT NULL DEFAULT (UUID())  
);

CREATE TABLE tbl_notas (
    nota_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    estudiante_id SMALLINT UNSIGNED NOT NULL,
    seccion_id SMALLINT UNSIGNED NOT NULL,
    calificacion DECIMAL(5,2) NOT NULL,
    observacion_id TINYINT UNSIGNED NOT NULL,
    id CHAR(36) NOT NULL DEFAULT (UUID()),    
    FOREIGN KEY (estudiante_id) REFERENCES tbl_estudiante(estudiante_id),
    FOREIGN KEY (seccion_id) REFERENCES tbl_seccion(seccion_id),
    FOREIGN KEY (observacion_id) REFERENCES tbl_observacion(observacion_id)
);

CREATE TABLE tbl_asignacion_revisor (
    asignacion_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    solicitud_id SMALLINT UNSIGNED NOT NULL,
    revisor_id SMALLINT UNSIGNED NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id CHAR(36) NOT NULL DEFAULT (UUID()),    
    FOREIGN KEY (solicitud_id) REFERENCES tbl_solicitud(solicitud_id) ON DELETE CASCADE,
    FOREIGN KEY (revisor_id) REFERENCES tbl_revisor(revisor_id) ON DELETE CASCADE,
    UNIQUE KEY (solicitud_id, revisor_id)
);

CREATE TABLE tbl_lista_espera (
    lista_espera_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    seccion_id SMALLINT UNSIGNED NOT NULL,
    estudiante_id SMALLINT UNSIGNED NOT NULL,
    id CHAR(36) NOT NULL DEFAULT (UUID()),    
    FOREIGN KEY (estudiante_id) REFERENCES tbl_estudiante(estudiante_id),
    FOREIGN KEY (seccion_id) REFERENCES tbl_seccion(seccion_id)
);

CREATE TABLE tbl_lista_cancelacion(
    lista_cancelacion_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    seccion_id SMALLINT UNSIGNED NOT NULL,
    estudiante_id SMALLINT UNSIGNED NOT NULL,
    id CHAR(36) NOT NULL DEFAULT (UUID()),    
    FOREIGN KEY (estudiante_id) REFERENCES tbl_estudiante(estudiante_id),
    FOREIGN KEY (seccion_id) REFERENCES tbl_seccion(seccion_id)
);

CREATE TABLE tbl_clase_requisito (
    clase_id SMALLINT UNSIGNED NOT NULL,
    requisito_clase_id SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (clase_id, requisito_clase_id),
    FOREIGN KEY (clase_id) REFERENCES tbl_clase(clase_id) ON DELETE CASCADE,
    FOREIGN KEY (requisito_clase_id) REFERENCES tbl_clase(clase_id) ON DELETE CASCADE
);

CREATE TABLE tbl_mensajes (
    mensaje_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    remitente_id SMALLINT UNSIGNED NOT NULL,
    destinatario_id SMALLINT UNSIGNED NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_envio DATETIME NOT NULL,
    leido BOOLEAN DEFAULT FALSE,
    id CHAR(36) NOT NULL DEFAULT (UUID()),    
    FOREIGN KEY (remitente_id) REFERENCES tbl_usuario(usuario_id) ON DELETE CASCADE,
    FOREIGN KEY (destinatario_id) REFERENCES tbl_usuario(usuario_id) ON DELETE CASCADE,
    INDEX (remitente_id, destinatario_id),
    INDEX (fecha_envio)
);

CREATE TABLE tbl_evaluacion (
    estudiante_id SMALLINT UNSIGNED,
    seccion_id SMALLINT UNSIGNED,
    calificacion DECIMAL(2,0),
    comentario VARCHAR(100),
    PRIMARY KEY (estudiante_id, seccion_id),
    FOREIGN KEY (estudiante_id) REFERENCES tbl_estudiante(estudiante_id),
    FOREIGN KEY (seccion_id) REFERENCES tbl_seccion(seccion_id)
);

CREATE TABLE tbl_estado_solicitud(
    estado_solicitud_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    estado VARCHAR(20)
);

CREATE TABLE tbl_solicitud_amistad(
    usuario_emisor SMALLINT UNSIGNED,
    usuario_receptor SMALLINT UNSIGNED,
    estado_solicitud_id TINYINT UNSIGNED,
    fecha_envio DATE,
    PRIMARY KEY(usuario_emisor, usuario_receptor),
    FOREIGN KEY (usuario_emisor) REFERENCES tbl_usuario(usuario_id),
    FOREIGN KEY (usuario_receptor) REFERENCES tbl_usuario(usuario_id),
    FOREIGN KEY (estado_solicitud_id) REFERENCES tbl_estado_solicitud(estado_solicitud_id)
);

CREATE TABLE tbl_libro (
    libro_id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    portada VARCHAR(255),
    ruta_archivo VARCHAR(255) NOT NULL,
    creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    id CHAR(36) NOT NULL DEFAULT (UUID())    
);

-- Tabla de autores
CREATE TABLE tbl_autor (
    autor_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    bio TEXT,
    id CHAR(36) NOT NULL DEFAULT (UUID())
);

-- Tabla de relación libro-autores (muchos a muchos)
CREATE TABLE tbl_libro_x_autor (
    libro_id INT NOT NULL,
    autor_id INT NOT NULL,
    PRIMARY KEY (libro_id, autor_id),
    FOREIGN KEY (libro_id) REFERENCES tbl_libro(libro_id) ON DELETE CASCADE,
    FOREIGN KEY (autor_id) REFERENCES tbl_autor(autor_id) ON DELETE CASCADE
);

-- Tabla de categorías/tags
CREATE TABLE tbl_categoria (
    categoria_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    id CHAR(36) NOT NULL DEFAULT (UUID())
);

-- Tabla de relación libro-categorías (muchos a muchos)
CREATE TABLE tbl_libro_x_categorias (
    libro_id INT NOT NULL,
    categoria_id INT NOT NULL,
    PRIMARY KEY (libro_id, categoria_id),
    FOREIGN KEY (libro_id) REFERENCES tbl_libro(libro_id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES tbl_categoria(categoria_id) ON DELETE CASCADE
);

-- Tabla de favoritos
CREATE TABLE tbl_libros_favoritos (
    usuario_id SMALLINT UNSIGNED NOT NULL,
    libro_id INT NOT NULL,
    creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id, libro_id),
    FOREIGN KEY (usuario_id) REFERENCES tbl_usuario(usuario_id) ON DELETE CASCADE,
    FOREIGN KEY (libro_id) REFERENCES tbl_libro(libro_id) ON DELETE CASCADE
);

-- Tabla de libros guardados
CREATE TABLE tbl_libros_guardados (
    usuario_id SMALLINT UNSIGNED NOT NULL,
    libro_id INT NOT NULL,
    creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (usuario_id, libro_id),
    FOREIGN KEY (usuario_id) REFERENCES tbl_usuario(usuario_id) ON DELETE CASCADE,
    FOREIGN KEY (libro_id) REFERENCES tbl_libro(libro_id) ON DELETE CASCADE
);

DELIMITER $$

CREATE TRIGGER trg_create_solicitud
AFTER INSERT ON tbl_admision
FOR EACH ROW
BEGIN
    INSERT INTO tbl_solicitud (solicitud_id, estado)
    VALUES (NEW.admision_id, 'Pendiente');
END $$

DELIMITER ;

DELIMITER $$

CREATE TRIGGER trg_create_codigo_solicitud
BEFORE INSERT ON tbl_solicitud
FOR EACH ROW
BEGIN
    DECLARE random_letter CHAR(1);
    DECLARE random_number VARCHAR(5);
    DECLARE generated_code VARCHAR(6);
    SET random_letter = CHAR(FLOOR(RAND() * 26) + 65);
    SET random_number = LPAD(FLOOR(RAND() * 100000), 5, '0');
    SET NEW.codigo = CONCAT(random_letter, random_number);
END $$

DELIMITER ;

INSERT INTO tbl_categoria (nombre) VALUES 
('Programación'),
('Informática'),
('Básico'),
('Arte'),
('Historia'),
('Humanidades'),
('Matemáticas'),
('Ciencias'),
('Avanzado'),
('Física'),
('Literatura'),
('Química'),
('Derecho'),
('Psicología'),
('Economía'),
('Ciencias Sociales');

INSERT INTO tbl_autor (nombre, bio) VALUES 
('Juan Pérez', 'Experto en programación básica y enseñanza de TI'),
('María Gómez', 'Ingeniera de software con 10 años de experiencia'),
('Ana Rodríguez', 'Historiadora del arte especializada en arte moderno'),
('Carlos Sánchez', 'Matemático con PhD en álgebra avanzada'),
('Luisa Fernández', 'Profesora de matemáticas aplicadas'),
('Dr. Robert Smith', 'Físico cuántico premiado con el Nobel'),
('Prof. Laura Méndez', 'Especialista en literatura clásica europea'),
('Dra. Susan Williams', 'Química orgánica con múltiples publicaciones'),
('Dr. Manuel García', 'Constitucionalista y profesor emérito'),
('Dra. Patricia López', 'Psicóloga cognitiva con clínica propia'),
('Dr. Richard Johnson', 'Economista jefe del Banco Mundial (2010-2015)'),
('Dr. Alan Turing', 'Pionero en ciencias de la computación'),
('Dr. John McCarthy', 'Creador del lenguaje LISP y padre de la IA');

INSERT INTO tbl_libro (titulo, descripcion, portada, ruta_archivo) VALUES 
('Introducción a la Programación', 'Fundamentos de programación para principiantes', 'intro_programacion.jpg', 'libros/intro_programacion.pdf'),
('Historia del Arte Moderno', 'Evolución del arte desde 1900 hasta la actualidad', 'arte_moderno.jpg', 'libros/arte_moderno.pdf'),
('Matemáticas Avanzadas', 'Conceptos avanzados de álgebra y cálculo', 'matematicas_avanzadas.jpg', 'libros/matematicas_avanzadas.pdf'),
('Física Cuántica', 'Principios fundamentales de la mecánica cuántica', 'fisica_cuantica.jpg', 'libros/fisica_cuantica.pdf'),
('Literatura Clásica', 'Análisis de las obras maestras de la literatura universal', 'literatura_clasica.jpg', 'libros/literatura_clasica.pdf'),
('Inteligencia Artificial', 'Fundamentos y aplicaciones modernas de IA', 'inteligencia_artificial.jpg', 'libros/inteligencia_artificial.pdf'),
('Química Orgánica', 'Compuestos orgánicos y sus reacciones', 'quimica_organica.jpg', 'libros/quimica_organica.pdf'),
('Derecho Constitucional', 'Principios y jurisprudencia constitucional', 'derecho_constitucional.jpg', 'libros/derecho_constitucional.pdf'),
('Psicología Cognitiva', 'Procesos mentales y modelos cognitivos', 'psicologia_cognitiva.jpg', 'libros/psicologia_cognitiva.pdf'),
('Economía Internacional', 'Sistemas económicos globales y comercio', 'economia_internacional.jpg', 'libros/economia_internacional.pdf');

INSERT INTO tbl_libro_x_autor (libro_id, autor_id) VALUES 
(1, 1), (1, 2),    -- Introducción a la Programación: Juan Pérez, María Gómez
(2, 3),             -- Historia del Arte Moderno: Ana Rodríguez
(3, 4), (3, 5),     -- Matemáticas Avanzadas: Carlos Sánchez, Luisa Fernández
(4, 6),             -- Física Cuántica: Dr. Robert Smith
(5, 7),             -- Literatura Clásica: Prof. Laura Méndez
(6, 12), (6, 13),   -- Inteligencia Artificial: Dr. Alan Turing, Dr. John McCarthy
(7, 8),             -- Química Orgánica: Dra. Susan Williams
(8, 9),             -- Derecho Constitucional: Dr. Manuel García
(9, 10),            -- Psicología Cognitiva: Dra. Patricia López
(10, 11);           -- Economía Internacional: Dr. Richard Johnson

INSERT INTO tbl_libro_x_categorias (libro_id, categoria_id) VALUES 
-- Introducción a la Programación
(1, 1), (1, 2), (1, 3),
-- Historia del Arte Moderno
(2, 4), (2, 5), (2, 6),
-- Matemáticas Avanzadas
(3, 7), (3, 8), (3, 9),
-- Física Cuántica
(4, 8), (4, 9), (4, 10),
-- Literatura Clásica
(5, 6), (5, 11),
-- Inteligencia Artificial
(6, 1), (6, 2), (6, 9),
-- Química Orgánica
(7, 8), (7, 12),
-- Derecho Constitucional
(8, 6), (8, 13),
-- Psicología Cognitiva
(9, 14), (9, 16),
-- Economía Internacional
(10, 15), (10, 16);

INSERT INTO tbl_tipo_documento (descripcion) VALUES ("Identidad");
INSERT INTO tbl_tipo_documento (descripcion) VALUES ("Pasaporte");

INSERT INTO tbl_usuario (nombre_completo, identidad, correo, numero_cuenta, contrasenia, telefono) VALUES 
("Sofía Gabriela Mendoza Castro", "0801200412340", "sofia.mendoza@gmail.com", "20211002240", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702039"),
("Carlos Alberto Jiménez Fuentes", "0801200412341", "carlos.jimenez@gmail.com", "20211002241", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702040"),
("Isabella Fernanda López Núñez", "0801200412342", "isabella.lopez@gmail.com", "20211002242", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702041"),
("Diego Alejandro Rodríguez Mejía", "0801200412343", "diego.rodriguez@gmail.com", "20211002243", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702042"),
("Luciana Valeria Torres Pineda", "0801200412344", "luciana.torres@gmail.com", "20211002244", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702043"),
("Mateo Andrés Ramírez Vargas", "0801200412345", "mateo.ramirez@gmail.com", "20211002245", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702044"),
("Mariana Alejandra Castillo Cruz", "0801200412346", "mariana.castillo@gmail.com", "20211002246", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702045"),
("Emiliano Daniel Fernández Soto", "0801200412347", "emiliano.fernandez@gmail.com", "20211002247", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702046"),
("Victoria Natalia Herrera Peña", "0801200412348", "victoria.herrera@gmail.com", "20211002248", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702047"),
("Samuel Leonardo Morales García", "0801200412349", "samuel.morales@gmail.com", "20211002249", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702048"),
("Renata Camila Pérez Vásquez", "0801200512340", "renata.perez@gmail.com", "20211002250", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702049"),
("Joaquín Antonio Díaz Espinoza", "0801200512341", "joaquin.diaz@gmail.com", "20211002251", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702050"),
("Ximena Valeria Chávez Herrera", "0801200512342", "ximena.chavez@gmail.com", "20211002252", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702051"),
("Sebastián Esteban Guzmán Rivas", "0801200512343", "sebastian.guzman@gmail.com", "20211002253", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702052"),
("Regina Isabella Ortega Salinas", "0801200512344", "regina.ortega@gmail.com", "20211002254", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702053"),
("Maximiliano David Méndez Fuentes", "0801200512345", "maximiliano.mendez@gmail.com", "20211002255", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702054"),
("Valentina Sofía Espinoza Cárdenas", "0801200512346", "valentina.espinoza@gmail.com", "20211002256", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702055"),
("Leonardo Gabriel Ruiz Figueroa", "0801200512347", "leonardo.ruiz@gmail.com", "20211002257", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702056"),
("Camila Antonella Silva Reyes", "0801200512348", "camila.silva@gmail.com", "20211002258", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702057"),
("Dylan Matías Ríos Palacios", "0801200512349", "dylan.rios@gmail.com", "20211002259", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702058"),
("Mía Fernanda Calderón Soto", "0801200612340", "mia.calderon@gmail.com", "20211002260", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702059"),
("Alexander Emilio Peña Vargas", "0801200612341", "alexander.pena@gmail.com", "20211002261", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702060"),
("Paulina Isabella Navarro Guzmán", "0801200612342", "paulina.navarro@gmail.com", "20211002262", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702061"),
("Nicolás Esteban Herrera León", "0801200612343", "nicolas.herrera@gmail.com", "20211002263", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702062"),
("Valeria Andrea Álvarez Rosales", "0801200612344", "valeria.alvarez@gmail.com", "20211002264", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702063"),
("Luis Santiago Rojas Méndez", "0801200612345", "luis.rojas@gmail.com", "20211002265", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702064"),
("Andrea Camila Fuentes Navarro", "0801200612346", "andrea.fuentes@gmail.com", "20211002266", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702065"),
("Gabriel Emmanuel Vargas Torres", "0801200612347", "gabriel.vargas@gmail.com", "20211002267", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702066"),
("Martina Alejandra Castillo Ramírez", "0801200612348", "martina.castillo@gmail.com", "20211002268", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702067"),
("Benjamín Nicolás Sánchez Reyes", "0801200612349", "benjamin.sanchez@gmail.com", "20211002269", '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "87702068");


INSERT INTO tbl_revisor (usuario_id) VALUES (1);
INSERT INTO tbl_revisor (usuario_id) VALUES (2);

INSERT INTO tbl_rol (nombre_rol) VALUES ("Estudiante");
INSERT INTO tbl_rol (nombre_rol) VALUES ("Docente");
INSERT INTO tbl_rol (nombre_rol) VALUES ("Jefe");
INSERT INTO tbl_rol (nombre_rol) VALUES ("Coordinador");
INSERT INTO tbl_rol (nombre_rol) VALUES ("Revisor");
INSERT INTO tbl_rol (nombre_rol) VALUES ("Administrador");

INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (1, 1);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (1, 5);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (2, 2);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (2, 5);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (3, 1);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (4, 1);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (5, 1);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (6, 1);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (8, 1);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (9, 1);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (10, 1);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (11, 1);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (12, 1);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (13, 1);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (14, 1);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (15, 1);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (16, 2);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (17, 2);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (18, 2);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (19, 2);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (20, 2);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (21, 2);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (22, 2);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (23, 2);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (24, 2);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (25, 2);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (26, 2);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (27, 2);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (28, 2);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (29, 2);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (30, 2);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (1, 3);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (4, 3);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (6, 3);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (8, 3);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (10, 3);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (11, 3);

INSERT INTO tbl_facultad (nombre_facultad) VALUES ("Ciencias Jurídicas");
INSERT INTO tbl_facultad (nombre_facultad) VALUES ("Ciencias Sociales");
INSERT INTO tbl_facultad (nombre_facultad) VALUES ("Humanidades y Artes");
INSERT INTO tbl_facultad (nombre_facultad) VALUES ("Ingeniería");
INSERT INTO tbl_facultad (nombre_facultad) VALUES ("Ciencias Espaciales");
INSERT INTO tbl_facultad (nombre_facultad) VALUES ("Ciencias Médicas");
INSERT INTO tbl_facultad (nombre_facultad) VALUES ("Odontología");
INSERT INTO tbl_facultad (nombre_facultad) VALUES ("Ciencias");
INSERT INTO tbl_facultad (nombre_facultad) VALUES ("Química y Farmacia");
INSERT INTO tbl_facultad (nombre_facultad) VALUES ("Ciencias Económicas Administratias y Contables");
INSERT INTO tbl_facultad (nombre_facultad) VALUES ("Matematicas Aplicada");
INSERT INTO tbl_facultad (nombre_facultad) VALUES ("Matematicas Condensada");

INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("DEREC", "Licenciatura en Derecho", 5.0, "Licenciatura", 1);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("ANTRO", "Licenciatura en Antropología", 5.0, "Licenciatura", 2);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("PERIO", "Licenciatura en Periodismo", 4.0, "Licenciatura", 2);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("PSICO", "Licenciatura en Psicología", 4.5, "Licenciatura", 2);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("PEDAG", "Licenciatura en Pedagogía", 5.0, "Licenciatura", 3);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("TRSOC", "Licenciatura en Trabajo Social", 5.0, "Licenciatura", 2);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("HISTO", "Licenciatura en Historia", 5.0, "Licenciatura", 2);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("LETRA", "Licenciatura en Letras", 5.0, "Licenciatura", 3);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("FILOS", "Licenciatura en Filosofía", 5.0, "Licenciatura", 3);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("SOCIO", "Licenciatura en Sociología", 5.0, "Licenciatura", 2);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("EDFIS", "Licenciatura en Educación Física", 5.0, "Licenciatura", 3);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("LENEX", "Licenciatura en Lenguas Extranjeras con Orientación en Inglés y Francés", 5.5, "Licenciatura", 3);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("MUSIC", "Licenciatura en Música", 5.0, "Licenciatura", 3);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("DESLO", "Licenciatura en Desarrollo Local", 5.0, "Licenciatura", 2);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("INGCI", "Ingeniería Civil", 5.0, "Licenciatura", 4);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("INGMI", "Ingeniería Mecánica Industrial", 5.0, "Licenciatura", 4);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("INGEI", "Ingeniería Eléctrica Industrial", 5.0, "Licenciatura", 4);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("INGIN", "Ingeniería Industrial", 5.0, "Licenciatura", 4);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("INGSI", "Ingeniería en Sistemas", 5.0, "Licenciatura", 4);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("ARQ", "Arquitectura", 5.0, "Licenciatura", 3);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("MATEM", "Licenciatura en Matemática", 4.0, "Licenciatura", 8);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("FISIC", "Licenciatura en Física", 4.0, "Licenciatura", 8);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("ASTRO", "Licenciatura en Astronomía y Astrofísica", 5.0, "Licenciatura", 5);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("CTIG", "Licenciatura en Ciencia y Tecnologías de la Información Geográfica", 4.0, "Licenciatura", 5);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("MEDIC", "Medicina y Cirugía", 7.0, "Licenciatura", 6);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("ODONT", "Odontología", 6.0, "Licenciatura", 7);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("NUTRI", "Licenciatura en Nutrición", 5.0, "Licenciatura", 6);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("QUIFA", "Licenciatura en Química y Farmacia", 5.0, "Licenciatura", 9);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("ENFER", "Licenciatura en Enfermería", 5.5, "Licenciatura", 6);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("MICRO", "Licenciatura en Microbiología", 5.0, "Licenciatura", 8);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("BIOLO", "Licenciatura en Biología", 5.5, "Licenciatura", 8);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("FONOA", "Licenciatura en Fonoaudiología", 4.5, "Licenciatura", 6);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("ADGEM", "Licenciatura en Administración y Generación de Empresas", 4.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("ADPUB", "Licenciatura en Administración Pública", 5.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("ECONO", "Licenciatura en Economía", 5.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("CONFI", "Licenciatura en Contaduría Pública y Finanzas", 5.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("ADADU", "Licenciatura en Administración Aduanera", 4.5, "Licenciatura", 10);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("BANFI", "Licenciatura en Banca y Finanzas", 5.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("COMEX", "Licenciatura en Comercio Internacional", 5.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("INFAD", "Licenciatura en Informática Administrativa", 4.5, "Licenciatura", 10);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("MERCA", "Licenciatura en Mercadotecnia", 5.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("AGRON", "Ingeniería Agronómica", 5.0, "Licenciatura", 4);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("FORES", "Ingeniería Forestal", 5.0, "Licenciatura", 4);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("AGROI", "Ingeniería Agroindustrial", 5.0, "Licenciatura", 4);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("ACUIC", "Ingeniería en Ciencias Acuícolas y Recursos Marinos Costeros", 5.0, "Licenciatura", 4);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("ECOAG", "Licenciatura en Economía Agrícola", 5.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("ECOTU", "Licenciatura en Ecoturismo", 5.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("COMAG", "Licenciatura en Comercio Internacional con Orientación en Agroindustria", 5.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("TUEBE", "Técnico Universitario en Educación Básica para la Enseñanza del Español", 2.5, "Técnico Universitario", 3);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("TUMME", "Técnico Universitario Metalurgia", 2.5, "Técnico Universitario", 8);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("TUPA", "Técnico Universitario en Producción Agrícola", 2.5, "Técnico Universitario", 4);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("TUTF", "Técnico Universitario en Terapia Funcional", 2.5, "Técnico Universitario", 6);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("TURI", "Técnico Universitario en Radiotecnologías (Radiología e Imágenes)", 2.5, "Técnico Universitario", 6);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("TUMF", "Técnico Universitario en Microfinanzas", 2.5, "Técnico Universitario", 10);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("TUAB", "Técnico Universitario en Alimentos y Bebidas", 2.5, "Técnico Universitario", 10);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("TUCC", "Técnico Universitario en Control de Calidad del Café", 2.5, "Técnico Universitario", 4);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("TUEC", "Técnico Universitario en Administración de Empresas Cafetaleras", 2.5, "Técnico Universitario", 10);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("TUDM", "Técnico Universitario en Desarollo Municipal", 2.5, "Técnico Universitario", 2);
INSERT INTO tbl_carrera (codigo_carrera, nombre_carrera, duracion, grado, facultad_id) VALUES("ADAEM", "Licenciatura en Administración de Empresas Agropecuarias", 4.5, "Licenciatura", 10);

INSERT INTO tbl_centro_regional (nombre_centro, ubicacion, codigo_centro) VALUES ("UNAH Ciudad Universitaria", "Tegucigalpa", "unah_cu");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion, codigo_centro) VALUES ("UNAH Cortés", "Cortés", "unah_vs");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion, codigo_centro) VALUES ("UNAH Comayagua", "Comayagua", "unah_cmyg");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion, codigo_centro) VALUES ("UNAH Atlántida", "Atlántida", "unah_atl");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion, codigo_centro) VALUES ("UNAH Choluteca", "Choluteca", "unah_chltc");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion, codigo_centro) VALUES ("UNAH Copán", "Copán", "unah_cpn");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion, codigo_centro) VALUES ("UNAH Olancho", "Olancho", "unah_olnch");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion, codigo_centro) VALUES ("UNAH El Paraíso", "El Paraíso", "unah_prs");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion, codigo_centro) VALUES ("UNAH Yoro", "Yoro", "unah_yoro");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion, codigo_centro) VALUES ("Instituto Tecnológico Superior Tela", "Atlántida", "itst");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion, codigo_centro) VALUES ("CRAED", "A distancia", "craed");

INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (1, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (1, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (2, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (3, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (3, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (4, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (4, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (5, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (5, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (5, 11);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (6, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (7, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (8, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (8, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (9, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (10, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (10, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (11, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (12, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (13, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (14, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (14, 3);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (14, 6);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (14, 8);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (15, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (15, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (16, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (16, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (17, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (17, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (18, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (18, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (19, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (19, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (19, 3);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (19, 5);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (19, 6);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (20, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (21, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (21, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (22, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (23, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (24, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (25, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (25, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (26, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (26, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (27, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (28, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (29, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (29, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (29, 4);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (29, 7);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (29, 8);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (30, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (31, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (32, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (33, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (33, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (33, 3);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (33, 4);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (33, 5);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (33, 6);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (33, 7);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (33, 8);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (34, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (35, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (35, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (36, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (36, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (37, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (38, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (39, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (39, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (40, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (40, 2);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (40, 8);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (40, 9);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (41, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (42, 4);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (43, 4);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (44, 3);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (44, 5);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (44, 6);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (44, 7);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (44, 8);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (44, 9);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (45, 5);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (46, 4);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (47, 4);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (48, 3);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (48, 5);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (48, 6);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (48, 7);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (49, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (50, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (51, 3);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (51, 6);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (52, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (53, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (54, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (54, 9);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (54, 10);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (54, 11);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (55, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (55, 10);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (56, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (57, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (57, 3);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (57, 6);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (57, 8);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (57, 11);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (58, 1);
INSERT INTO tbl_carrera_x_centro_regional (carrera_id, centro_regional_id) VALUES (59, 11);


-- Insertando más departamentos
INSERT INTO tbl_departamento(nombre, facultad_id) VALUES 
('Matemáticas Aplicadas', 11), 
('Lenguas y Literatura', 3), 
('Ciencias Naturales', 8),
('Ingeniería en sistemas', 4),
('Ingeniería Mecánica', 4),
('Ingeniería Civil', 4),
('Ingeniería Industrial', 4),
('Ingeniería Eléctrica', 4),
('Química', 9),
('Medicina General', 6),
('Biotecnología', 8),
('Astronomía', 5),
('Economía', 10),
('Contaduría Pública', 10);

-- Insert de edificios
INSERT INTO tbl_edificio(facultad_id, centro_regional_id, edificio) VALUES 
(4,1, 'B1'), 
(4,1, 'B2'), 
(10,1, 'C1'),
(3,1, 'C2'),
(3,1, 'C3'),
(11,1, 'D1'),
(11,1, 'F1'),
(12,1, 'E1'),
(1,1, 'A1'),
(1,1, 'A2'),
(3,1, '1843'),
(2,1, 'J1'),
(9,1, 'G1'),
(6,1, 'L1'),
(5,1, 'K1'),
(5,1, 'K2');

-- Insertando más clases
INSERT INTO tbl_clase (edificio_id, departamento_id, nombre, codigo, UV) VALUES 
(6, 1, 'Matematicas I', 'MM-110', 5),
(6, 1, 'Geometria y Trigonometria', 'MM-111', 5),
(2, 4, 'Introduccion a ingenieria en sistemas', 'IS-110', 3),
(7, 3, 'Sociologia', 'SC-101', 4),
(2, 4, 'Programacion I', 'MM-314', 3),
(6, 1, 'Calculo I', 'MM-201', 5),
(6, 1, 'Vectores y matrices', 'MM-211', 5),
(11, 2, 'Ingles I', 'IN-101', 4);


INSERT INTO tbl_clase_carrera VALUES
(1,15),
(1,16),
(1,17),
(1,18),
(1,19),
(2,15),
(2,16),
(2,17),
(2,18),
(2,19),
(3,19),
(4,15),
(4,16),
(4,17),
(4,18),
(4,19),
(5,19),
(6,15),
(6,16),
(6,17),
(6,18),
(6,19),
(7,15),
(7,16),
(7,17),
(7,18),
(7,19),
(8,15),
(8,16),
(8,17),
(8,18),
(8,19);

INSERT INTO tbl_clase_requisito  VALUES
(5,1), 
(5,3), 
(6,1), 
(6,2), 
(7,1); 



-- Insertando 15 estudiantes
INSERT INTO tbl_estudiante (usuario_id, carrera_id, centro_regional_id, correo) VALUES
(1, 15, 1, 'estudiante1@example.com'),
(2, 16, 1, 'estudiante2@example.com'),
(3, 17, 1, 'estudiante3@example.com'),
(4, 18, 3, 'estudiante4@example.com'),
(5, 19, 1, 'estudiante5@example.com'),
(6, 15, 2, 'estudiante6@example.com'),
(7, 16, 3, 'estudiante7@example.com'),
(8, 17, 1, 'estudiante8@example.com'),
(9, 18, 2, 'estudiante9@example.com'),
(10, 19, 3, 'estudiante10@example.com'),
(11, 15, 1, 'estudiante11@example.com'),
(12, 16, 2, 'estudiante12@example.com'),
(13, 17, 3, 'estudiante13@example.com'),
(14, 18, 1, 'estudiante14@example.com'),
(15, 19, 2, 'estudiante15@example.com');

-- Insertando 15 docentes
INSERT INTO tbl_docente (usuario_id, carrera_id, departamento_id, centro_regional_id) VALUES
(16, 21, 1, 1),
(17, 21, 1, 1),
(18, 21, 1, 1),
(19, 19, 4, 1),
(20, 19, 4, 1),
(21, 15, 4, 1),
(22, 15, 4, 1),
(23, 16, 4, 2),
(24, 16, 4, 2),
(25, 17, 4, 1),
(26, 18, 4, 1),
(27, 19, 4, 1),
(28, 21, 4, 2),
(29, 21, 4, 2),
(30, 15, 4, 1);

insert into tbl_jefe(docente_id) values (1);
insert into tbl_jefe(docente_id) values (4);
insert into tbl_jefe(docente_id) values (6);
insert into tbl_jefe(docente_id) values (8);
insert into tbl_jefe(docente_id) values (10);
insert into tbl_jefe(docente_id) values (11);

insert into tbl_aula(aula, edificio_id) values
("Lab1", 2),
("Lab2", 2),
("Lab3", 2),
("202", 6),
("203", 6),
("404", 2),
("302", 2),
("302", 7),
("102", 7),
("103", 11),
("105", 6),
("200", 6),
("201", 2),
("202", 2),
("203", 7);

insert into tbl_seccion(clase_id, docente_id, aula_id, periodo_academico, horario, dias, cupo_maximo, centro_regional_id) values
(1,2,4,'2024-III', '11:00-12:00','Lun, Mar, Mie, Jue, Vie', 10, 1),
(1,2,4,'2024-III', '11:00-12:00','Lun, Mar, Mie, Jue, Vie', 10, 1),
(2,3,5,'2024-III', '12:00-13:00','Lun, Mar, Mie, Jue, Vie', 10, 1),
(2,3,5,'2024-III', '12:00-13:00','Lun, Mar, Mie, Jue, Vie', 10, 1),
(3,5,2,'2024-III', '13:00-14:00','Lun, Mar, Mie',10, 1);

insert into tbl_estado_matricula(estado_matricula) values 
('Activo'),
('Inactivo');

insert into tbl_info_matricula(inicio, final, estado_matricula_id) values
('2025-04-08', '2025-04-11', 1);

insert into tbl_info_notas(inicio, final, estado_matricula_id) values
('2025-06-10', '2025-06-15', 1);

insert into tbl_info_add_can(inicio, final, estado_matricula_id) values
('2025-04-14', '2025-04-21', 1);

insert into tbl_matricula(estudiante_id, seccion_id, fechaInscripcion) values
(1,1,'2024-10-03'),
(5,2,'2024-10-03'),
(1,3,'2024-10-03'),
(5,3,'2024-10-03'),
(5,5,'2024-10-03');

insert into tbl_observacion(observacion) values
('APR'),
('RPB'),
('NSP');

insert into tbl_notas(estudiante_id, seccion_id, calificacion, observacion_id) values
(1,1,66,1),
(5,2,70,1),
(1,3,78,1),
(5,3,69,1),
(5,5,89,1);

insert into tbl_evaluacion(estudiante_id, seccion_id, calificacion, comentario) values 
(1,1, 9.5, 'Clase muy entretenida y dispuesto a ayudarr a sus alumnos'),
(5,2, 8.5, 'Clase muy entretenida y dispuesto a ayudarr a sus alumnos'),
(1,3, 4.5, 'Clase muy entretenida y dispuesto a ayudarr a sus alumnos'),
(5,3, 4.5, 'Clase muy entretenida y dispuesto a ayudarr a sus alumnos'),
(5,5, 10, 'Clase muy entretenida y dispuesto a ayudarr a sus alumnos');

insert into tbl_estado_solicitud(estado) values
('ACEPTADO'),
('RECHAZADO'),
('ESPERA');

insert into tbl_solicitud_amistad(usuario_emisor, usuario_receptor, estado_solicitud_id,fecha_envio) values
(1, 2, 3, '2025-01-01'),
(2, 2, 3, '2025-01-01'),
(4, 5, 1, '2025-01-01'),
(5, 6, 1, '2025-01-01'),
(6, 7, 1, '2025-01-01'),
(7, 8, 1, '2025-01-01'),
(8, 9, 1, '2025-01-01'),
(9, 10, 3, '2025-01-01'),
(10, 11, 3, '2025-01-01'),
(11, 12, 3, '2025-01-01'),
(12, 13, 3, '2025-01-01'),
(1, 3, 2, '2025-01-01'),
(1, 4, 2, '2025-01-01'),
(1, 5, 1, '2025-01-01'),
(5, 7, 1, '2025-01-01'),
(5, 8, 1, '2025-01-01'),
(5, 9, 1, '2025-01-01'),
(5, 10, 2, '2025-01-01'),
(5, 11, 2, '2025-01-01'),
(3, 10, 1, '2025-01-01'),
(3, 12, 1, '2025-01-01'),
(3, 4, 2, '2025-01-01'),
(3, 6, 1, '2025-01-01');

INSERT INTO tbl_mensajes (remitente_id, destinatario_id, mensaje, fecha_envio, leido) VALUES
(5, 6, 'Hola, ¿cómo estás?', NOW(), 1),
(6, 5, 'Todo bien, que paso?', NOW(), 1),
(5, 6, '¿Vas a ir a clase hoy?', NOW(), 1),
(5, 6, 'Recuerda que tenemos examen mañana.', NOW(), 1),
(6, 5, 'No me la constes, no he estudiado ', NOW(), 1),
(5, 6, '¿Tienes el PDF del profesor?', NOW(), 0),
(5, 4, 'Claro, maniana te lo paso por nfc', NOW(), 0),
(5, 6, 'Gracias por la ayuda de ayer.', NOW(), 0),
(5, 6, '¿Quieres hacer el trabajo juntos?', NOW(), 0),
(5, 6, 'Te pasé el archivo por correo.', NOW(), 0),
(5, 6, '¿Qué opinas del nuevo horario?', NOW(), 0),
(5, 6, 'Hablamos más tarde.', NOW(), 0);