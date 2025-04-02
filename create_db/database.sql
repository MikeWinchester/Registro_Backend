DROP DATABASE IF EXISTS bd_registro;
CREATE DATABASE bd_registro;
USE bd_registro;

-- Tabla Usuario
CREATE TABLE tbl_usuario (
    usuario_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(50) NOT NULL,
    identidad CHAR(13) UNIQUE NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    numero_cuenta CHAR(11) UNIQUE NOT NULL,
    contrasenia VARCHAR(255) NOT NULL,
    telefono CHAR(8),
    INDEX idx_usuario_correo (correo)
);

CREATE TABLE tbl_tipo_documento(
    tipo_documento_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    descripcion VARCHAR(10) NOT NULL 
);

CREATE TABLE tbl_documento(
    documento_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero_documento VARCHAR(13) UNIQUE NOT NULL,
    tipo_documento_id TINYINT UNSIGNED NOT NULL,
    FOREIGN KEY (tipo_documento_id) REFERENCES tbl_tipo_documento(tipo_documento_id) 
);

-- Tabla Facultad
CREATE TABLE tbl_facultad (
    facultad_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_facultad VARCHAR(100) NOT NULL,
    INDEX idx_facultad_nombre (nombre_facultad)
);

-- Tabla Centro Regional
CREATE TABLE tbl_centro_regional (
    centro_regional_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_centro VARCHAR(100) NOT NULL,
    ubicacion VARCHAR(255) NOT NULL,
    codigo_centro VARCHAR(10) NOT NULL,
    INDEX idx_centroregional_codigo (codigo_centro)
);

CREATE TABLE tbl_revisor (
    revisor_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id SMALLINT UNSIGNED NOT NULL,
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
    FOREIGN KEY (documento_id) REFERENCES tbl_documento(documento_id),
    FOREIGN KEY (centro_regional_id) REFERENCES tbl_centro_regional(centro_regional_id),
    FOREIGN KEY (carrera_id) REFERENCES tbl_carrera(carrera_id),
    FOREIGN KEY (carrera_secundaria_id) REFERENCES tbl_carrera(carrera_id)
);

CREATE TABLE tbl_rol(
    rol_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(20) UNIQUE NOT NULL	
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
    FOREIGN KEY (solicitud_id) REFERENCES tbl_admision(admision_id) ON DELETE CASCADE
);

CREATE TABLE tbl_departamento (
    departamento_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    facultad_id TINYINT UNSIGNED NOT NULL,
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
    FOREIGN KEY (usuario_id) REFERENCES tbl_usuario(usuario_id),
    FOREIGN KEY (centro_regional_id) REFERENCES tbl_centro_regional(centro_regional_id),
    FOREIGN KEY (departamento_id) REFERENCES tbl_departamento(departamento_id),
    FOREIGN KEY (carrera_id) REFERENCES tbl_carrera(carrera_id)
);

-- Tabla Coordinador
CREATE TABLE tbl_coordinador (
    coordinador_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    docente_id SMALLINT UNSIGNED UNIQUE,
    FOREIGN KEY (docente_id) REFERENCES tbl_docente(docente_id)
);

CREATE TABLE tbl_jefe(
    jefe_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    docente_id SMALLINT UNSIGNED UNIQUE,
    FOREIGN KEY (docente_id) REFERENCES tbl_docente(docente_id)
);


-- Tabla Categoría Libro
CREATE TABLE tbl_categoria_libro (
    categoria_libro_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    INDEX idx_categoria_nombre (nombre)
);

-- Tabla Biblioteca
CREATE TABLE tbl_biblioteca (
    libro_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    autor VARCHAR(100) NOT NULL,
    categoria_libro_id TINYINT UNSIGNED NOT NULL,
    archivo_PDF TEXT,
    FOREIGN KEY (categoria_libro_id) REFERENCES tbl_categoria_libro(categoria_libro_id)
);

CREATE TABLE tbl_edificio(
    edificio_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    facultad_id TINYINT UNSIGNED NOT NULL,
    centro_regional_id TINYINT UNSIGNED NOT NULL,
    edificio varchar(50),
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
    FOREIGN KEY (docente_id) REFERENCES tbl_docente(docente_id),
    FOREIGN KEY (aula_id) REFERENCES tbl_aula(aula_id),
    FOREIGN KEY (clase_id) REFERENCES tbl_clase(clase_id)
);

-- Tabla Matrícula
CREATE TABLE tbl_matricula (
    matricula_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    estudiante_id SMALLINT UNSIGNED NOT NULL,
    seccion_id SMALLINT UNSIGNED NOT NULL,
    fechaInscripcion DATE NOT NULL,
    FOREIGN KEY (estudiante_id) REFERENCES tbl_estudiante(estudiante_id),
    FOREIGN KEY (seccion_id) REFERENCES tbl_seccion(seccion_id)
);

CREATE TABLE tbl_observacion(
    observacion_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    observacion VARCHAR(3)
);

CREATE TABLE tbl_notas (
    nota_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    estudiante_id SMALLINT UNSIGNED NOT NULL,
    seccion_id SMALLINT UNSIGNED NOT NULL,
    calificacion DECIMAL(5,2) NOT NULL,
    observacion_id TINYINT UNSIGNED NOT NULL,
    FOREIGN KEY (estudiante_id) REFERENCES tbl_estudiante(estudiante_id),
    FOREIGN KEY (seccion_id) REFERENCES tbl_seccion(seccion_id),
    FOREIGN KEY (observacion_id) REFERENCES tbl_observacion(observacion_id)
);

CREATE TABLE tbl_asignacion_revisor (
    asignacion_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    solicitud_id SMALLINT UNSIGNED NOT NULL,
    revisor_id SMALLINT UNSIGNED NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (solicitud_id) REFERENCES tbl_solicitud(solicitud_id) ON DELETE CASCADE,
    FOREIGN KEY (revisor_id) REFERENCES tbl_revisor(revisor_id) ON DELETE CASCADE,
    UNIQUE KEY (solicitud_id, revisor_id)
);

CREATE TABLE tbl_lista_espera (
    lista_espera_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    seccion_id SMALLINT UNSIGNED NOT NULL,
    estudiante_id SMALLINT UNSIGNED NOT NULL,
    FOREIGN KEY (estudiante_id) REFERENCES tbl_estudiante(estudiante_id),
    FOREIGN KEY (seccion_id) REFERENCES tbl_seccion(seccion_id)
);

CREATE TABLE tbl_lista_cancelacion(
    lista_cancelacion_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    seccion_id SMALLINT UNSIGNED NOT NULL,
    estudiante_id SMALLINT UNSIGNED NOT NULL,
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

alter table tbl_matricula
add column EstadoMatricula ENUM('Activo', 'Inactivo') NOT NULL;

CREATE TABLE tbl_mensajes (
    mensaje_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    remitente_id SMALLINT UNSIGNED NOT NULL,
    destinatario_id SMALLINT UNSIGNED NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_envio DATETIME NOT NULL,
    leido BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (remitente_id) REFERENCES tbl_usuario(usuario_id) ON DELETE CASCADE,
    FOREIGN KEY (destinatario_id) REFERENCES tbl_usuario(usuario_id) ON DELETE CASCADE,
    INDEX (remitente_id, destinatario_id),
    INDEX (fecha_envio)
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

INSERT INTO tbl_tipo_documento (descripcion) VALUES ("Identidad");
INSERT INTO tbl_tipo_documento (descripcion) VALUES ("Pasaporte");

INSERT INTO tbl_usuario (nombre_completo, identidad, correo, numero_cuenta, contrasenia, telefono) VALUES 
("Sofía Gabriela Mendoza Castro", "0801200412340", "sofia.mendoza@gmail.com", "20211002240", "12345", "87702039"),
("Carlos Alberto Jiménez Fuentes", "0801200412341", "carlos.jimenez@gmail.com", "20211002241", "12345", "87702040"),
("Isabella Fernanda López Núñez", "0801200412342", "isabella.lopez@gmail.com", "20211002242", "12345", "87702041"),
("Diego Alejandro Rodríguez Mejía", "0801200412343", "diego.rodriguez@gmail.com", "20211002243", "12345", "87702042"),
("Luciana Valeria Torres Pineda", "0801200412344", "luciana.torres@gmail.com", "20211002244", "12345", "87702043"),
("Mateo Andrés Ramírez Vargas", "0801200412345", "mateo.ramirez@gmail.com", "20211002245", "12345", "87702044"),
("Mariana Alejandra Castillo Cruz", "0801200412346", "mariana.castillo@gmail.com", "20211002246", "12345", "87702045"),
("Emiliano Daniel Fernández Soto", "0801200412347", "emiliano.fernandez@gmail.com", "20211002247", "12345", "87702046"),
("Victoria Natalia Herrera Peña", "0801200412348", "victoria.herrera@gmail.com", "20211002248", "12345", "87702047"),
("Samuel Leonardo Morales García", "0801200412349", "samuel.morales@gmail.com", "20211002249", "12345", "87702048"),
("Renata Camila Pérez Vásquez", "0801200512340", "renata.perez@gmail.com", "20211002250", "12345", "87702049"),
("Joaquín Antonio Díaz Espinoza", "0801200512341", "joaquin.diaz@gmail.com", "20211002251", "12345", "87702050"),
("Ximena Valeria Chávez Herrera", "0801200512342", "ximena.chavez@gmail.com", "20211002252", "12345", "87702051"),
("Sebastián Esteban Guzmán Rivas", "0801200512343", "sebastian.guzman@gmail.com", "20211002253", "12345", "87702052"),
("Regina Isabella Ortega Salinas", "0801200512344", "regina.ortega@gmail.com", "20211002254", "12345", "87702053"),
("Maximiliano David Méndez Fuentes", "0801200512345", "maximiliano.mendez@gmail.com", "20211002255", "12345", "87702054"),
("Valentina Sofía Espinoza Cárdenas", "0801200512346", "valentina.espinoza@gmail.com", "20211002256", "12345", "87702055"),
("Leonardo Gabriel Ruiz Figueroa", "0801200512347", "leonardo.ruiz@gmail.com", "20211002257", "12345", "87702056"),
("Camila Antonella Silva Reyes", "0801200512348", "camila.silva@gmail.com", "20211002258", "12345", "87702057"),
("Dylan Matías Ríos Palacios", "0801200512349", "dylan.rios@gmail.com", "20211002259", "12345", "87702058"),
("Mía Fernanda Calderón Soto", "0801200612340", "mia.calderon@gmail.com", "20211002260", "12345", "87702059"),
("Alexander Emilio Peña Vargas", "0801200612341", "alexander.pena@gmail.com", "20211002261", "12345", "87702060"),
("Paulina Isabella Navarro Guzmán", "0801200612342", "paulina.navarro@gmail.com", "20211002262", "12345", "87702061"),
("Nicolás Esteban Herrera León", "0801200612343", "nicolas.herrera@gmail.com", "20211002263", "12345", "87702062"),
("Valeria Andrea Álvarez Rosales", "0801200612344", "valeria.alvarez@gmail.com", "20211002264", "12345", "87702063"),
("Luis Santiago Rojas Méndez", "0801200612345", "luis.rojas@gmail.com", "20211002265", "12345", "87702064"),
("Andrea Camila Fuentes Navarro", "0801200612346", "andrea.fuentes@gmail.com", "20211002266", "12345", "87702065"),
("Gabriel Emmanuel Vargas Torres", "0801200612347", "gabriel.vargas@gmail.com", "20211002267", "12345", "87702066"),
("Martina Alejandra Castillo Ramírez", "0801200612348", "martina.castillo@gmail.com", "20211002268", "12345", "87702067"),
("Benjamín Nicolás Sánchez Reyes", "0801200612349", "benjamin.sanchez@gmail.com", "20211002269", "12345", "87702068");


INSERT INTO tbl_revisor (usuario_id) VALUES (1);
INSERT INTO tbl_revisor (usuario_id) VALUES (2);

INSERT INTO tbl_rol (nombre_rol) VALUES ("Estudiante");
INSERT INTO tbl_rol (nombre_rol) VALUES ("Docente");
INSERT INTO tbl_rol (nombre_rol) VALUES ("Jefe");
INSERT INTO tbl_rol (nombre_rol) VALUES ("Coordinador");
INSERT INTO tbl_rol (nombre_rol) VALUES ("Revisor");

INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (1, 1);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (1, 5);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (2, 2);
INSERT INTO tbl_usuario_x_rol (usuario_id, rol_id) VALUES (2, 5);

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


-- Insertando más departamentos
INSERT INTO tbl_departamento(nombre, facultad_id) VALUES 
('Matemáticas Aplicadas', 11), 
('Lenguas y Literatura', 3), 
('Ciencias Naturales', 8),
('Ingeniería de Software', 4),
('Ingeniería Mecánica', 4),
('Ingeniería Eléctrica', 4),
('Química', 9),
('Medicina General', 6),
('Biotecnología', 8),
('Astronomía', 5),
('Economía', 10),
('Contaduría Pública', 10);

-- Insertando más clases
INSERT INTO tbl_clase (edificio_id, departamento_id, nombre, codigo, UV) VALUES 
(1, 1, 'Álgebra Lineal', 'MAT103', 4),
(2, 2, 'Literatura Clásica', 'LEN102', 3),
(3, 3, 'Química General', 'CIE102', 4),
(4, 4, 'Algoritmos y Estructuras de Datos', 'IS200', 5),
(5, 5, 'Mecánica de Fluidos', 'ME200', 4),
(6, 6, 'Electromagnetismo', 'EE300', 5),
(7, 7, 'Química Orgánica', 'QF201', 4),
(8, 8, 'Anatomía Humana', 'MED300', 5),
(9, 9, 'Biología Molecular', 'BIO101', 4),
(10, 10, 'Astrofísica', 'AST400', 5),
(11, 11, 'Microeconomía', 'ECO101', 3),
(12, 12, 'Contabilidad Avanzada', 'CON200', 4),
(1, 1, 'Geometría Analítica', 'MAT104', 4),
(2, 2, 'Lingüística Aplicada', 'LEN201', 3),
(3, 3, 'Física Cuántica', 'CIE300', 5),
(4, 4, 'Bases de Datos', 'IS300', 4),
(5, 5, 'Termodinámica', 'ME201', 4),
(6, 6, 'Circuitos Eléctricos', 'EE200', 4),
(7, 7, 'Bioquímica', 'QF301', 4),
(8, 8, 'Fisiología Humana', 'MED400', 5),
(9, 9, 'Genética', 'BIO200', 4),
(10, 10, 'Mecánica Celeste', 'AST500', 5),
(11, 11, 'Macroeconomía', 'ECO102', 3),
(12, 12, 'Auditoría', 'CON300', 4),
(1, 1, 'Cálculo Diferencial', 'MAT105', 4),
(1, 1, 'Cálculo Integral', 'MAT106', 4),
(2, 2, 'Teoría Literaria', 'LEN202', 3),
(2, 2, 'Redacción Académica', 'LEN203', 3),
(3, 3, 'Biofísica', 'CIE201', 4),
(3, 3, 'Ecología General', 'CIE202', 4),
(4, 4, 'Programación Orientada a Objetos', 'IS201', 5),
(4, 4, 'Desarrollo Web', 'IS202', 4),
(5, 5, 'Dinámica de Fluidos', 'ME202', 4),
(5, 5, 'Resistencia de Materiales', 'ME203', 4),
(6, 6, 'Máquinas Eléctricas', 'EE301', 5),
(6, 6, 'Electrónica Digital', 'EE302', 4),
(7, 7, 'Farmacología', 'QF302', 4),
(7, 7, 'Química Analítica', 'QF303', 4),
(8, 8, 'Patología General', 'MED401', 5),
(8, 8, 'Neuroanatomía', 'MED402', 5),
(9, 9, 'Biotecnología', 'BIO300', 4),
(9, 9, 'Microbiología', 'BIO301', 4),
(10, 10, 'Cosmología', 'AST600', 5),
(10, 10, 'Ondas Gravitacionales', 'AST601', 5),
(11, 11, 'Econometría', 'ECO201', 3),
(11, 11, 'Historia del Pensamiento Económico', 'ECO202', 3),
(12, 12, 'Finanzas Corporativas', 'CON301', 4),
(12, 12, 'Gestión de Riesgos', 'CON302', 4);


INSERT INTO tbl_clase_carrera VALUES
(3, 2),
(3, 3),
(4, 3),
(31, 4),
(32, 4),
(1, 4),
(16, 4),
(5, 4),
(6, 5),
(7, 6),
(8, 7),
(9, 8),
(10, 9),
(11, 10),
(12, 11),
(13, 12),
(14, 1),
(15, 2),
(16, 3),
(17, 4),
(18, 5),
(19, 6),
(20, 7),
(21, 8),
(22, 9),
(23, 10),
(24, 11),
(25, 12),
(26, 1),
(27, 2),
(28, 3),
(29, 4),
(30, 5),
(31, 6),
(32, 7),
(33, 8),
(34, 9),
(35, 10),
(36, 11),
(37, 12),
(38, 1),
(39, 2),
(40, 3),
(41, 4),
(42, 5),
(43, 6),
(44, 7),
(45, 8),
(46, 9),
(47, 10),
(48, 11);


INSERT INTO tbl_clase_requisito  VALUES
(4, 1),  -- Algoritmos y Estructuras de Datos requiere Álgebra Lineal
(16, 4), -- Bases de Datos requiere Algoritmos y Estructuras de Datos
(32, 16), -- Desarrollo Web requiere Bases de Datos

(26, 25), -- Cálculo Integral requiere Cálculo Diferencial
(13, 1), -- Geometría Analítica requiere Álgebra Lineal
(25, 13), -- Cálculo Diferencial requiere Geometría Analítica

(18, 6),  -- Circuitos Eléctricos requiere Electromagnetismo
(36, 18), -- Electrónica Digital requiere Circuitos Eléctricos
(35, 18), -- Máquinas Eléctricas requiere Circuitos Eléctricos

(19, 7),  -- Bioquímica requiere Química Orgánica
(38, 19), -- Farmacología requiere Bioquímica

(24, 10), -- Mecánica Celeste requiere Astrofísica
(44, 24), -- Cosmología requiere Mecánica Celeste
(45, 24), -- Ondas Gravitacionales requiere Mecánica Celeste

(30, 3),  -- Biofísica requiere Química General
(40, 30), -- Biotecnología requiere Biofísica
(41, 30), -- Microbiología requiere Biofísica

(47, 11), -- Econometría requiere Microeconomía
(48, 47); -- Historia del Pensamiento Económico requiere Econometría



-- Insertando 15 estudiantes
INSERT INTO tbl_estudiante (usuario_id, carrera_id, centro_regional_id, correo) VALUES
(1, 5, 1, 'estudiante1@example.com'),
(2, 3, 1, 'estudiante2@example.com'),
(3, 7, 1, 'estudiante3@example.com'),
(4, 19, 3, 'estudiante4@example.com'),
(5, 19, 1, 'estudiante5@example.com'),
(6, 8, 2, 'estudiante6@example.com'),
(7, 4, 3, 'estudiante7@example.com'),
(8, 9, 1, 'estudiante8@example.com'),
(9, 11, 2, 'estudiante9@example.com'),
(10, 14, 3, 'estudiante10@example.com'),
(11, 1, 1, 'estudiante11@example.com'),
(12, 2, 2, 'estudiante12@example.com'),
(13, 10, 3, 'estudiante13@example.com'),
(14, 13, 1, 'estudiante14@example.com'),
(15, 15, 2, 'estudiante15@example.com');

-- Insertando 15 docentes
INSERT INTO tbl_docente (usuario_id, carrera_id, departamento_id, centro_regional_id) VALUES
(16, 5, 1, 1),
(17, 3, 2, 2),
(18, 7, 3, 1),
(19, 19, 4, 3),
(20, 6, 5, 1),
(21, 8, 6, 2),
(22, 4, 7, 3),
(23, 9, 8, 1),
(24, 11, 9, 2),
(25, 14, 10, 3),
(26, 1, 11, 1),
(27, 2, 12, 2),
(28, 10, 3, 3),
(29, 13, 5, 1),
(30, 15, 7, 2);



insert into tbl_jefe(docente_id) values (1);
insert into tbl_jefe(docente_id) values (2);
insert into tbl_jefe(docente_id) values (7);
insert into tbl_jefe(docente_id) values (4);

insert into tbl_aula(aula, edificio_id) values
("Lab1", 1),
("Lab2", 1),
("Lab3", 1),
("202", 5),
("203", 5),
("404", 2),
("302", 4),
("302", 7),
("102", 5),
("103", 5),
("105", 8),
("200", 1),
("201", 4),
("202", 1),
("203", 2);

