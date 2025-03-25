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
    INDEX idx_centroregional_nombre (nombre_centro)
);

CREATE TABLE tbl_revisor (
    revisor_id SMALLINT UNSIGNED PRIMARY KEY,
    usuario_id SMALLINT UNSIGNED NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES tbl_usuario(usuario_id) ON DELETE CASCADE
);

-- Tabla Carrera
CREATE TABLE tbl_carrera (
    carrera_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
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
    numero_identidad VARCHAR(20) UNIQUE NOT NULL,
    numero_telefono VARCHAR(20) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    carrera_id TINYINT UNSIGNED NOT NULL,
    carrera_secundaria_id TINYINT UNSIGNED,
    certificado_Secundaria VARCHAR(255),
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
    observaciones TEXT,
    FOREIGN KEY (solicitud_id) REFERENCES tbl_admision(admision_id) ON DELETE CASCADE
);

CREATE TABLE tbl_departamento (
    departamento_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
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
    departamento_id INT UNSIGNED NOT NULL,
    carrera_id TINYINT UNSIGNED NOT NULL,
    centro_regional_id TINYINT UNSIGNED NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES tbl_usuario(usuario_id),
    FOREIGN KEY (centro_regional_id) REFERENCES tbl_centro_regional(centro_regional_id),
    FOREIGN KEY (carrera_id) REFERENCES tbl_carrera(carrera_id),
    FOREIGN KEY (departamento_id) REFERENCES tbl_departamento(departamento_id)
);

-- Tabla Coordinador
CREATE TABLE tbl_coordinador (
    coordinador_id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    docente_id SMALLINT UNSIGNED UNIQUE,
    carrera_id TINYINT UNSIGNED NOT NULL,
    FOREIGN KEY (docente_id) REFERENCES tbl_docente(docente_id),
    FOREIGN KEY (carrera_id) REFERENCES tbl_carrera(carrera_id)
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
    departamento_id INT UNSIGNED NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    codigo VARCHAR(20) UNIQUE NOT NULL,
    UV TINYINT UNSIGNED,
    FOREIGN KEY (edificio_id) REFERENCES tbl_edificio(edificio_id),
    FOREIGN KEY (departamento_id) REFERENCES tbl_departamento(departamento_id)
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
    dias varchar(20),
    cupo_maximo TINYINT UNSIGNED NOT NULL,
    FOREIGN KEY (docente_id) REFERENCES tbl_docente(docente_id),
    FOREIGN KEY (clase_id) REFERENCES tbl_clase(clase_id),
    FOREIGN KEY (aula_id) REFERENCES tbl_aula(aula_id)
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

CREATE TABLE tbl_notas (
    nota_id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    estudiante_id SMALLINT UNSIGNED NOT NULL,
    seccion_id SMALLINT UNSIGNED NOT NULL,
    calificacion DECIMAL(4,2) NOT NULL,
    FOREIGN KEY (estudiante_id) REFERENCES tbl_estudiante(estudiante_id),
    FOREIGN KEY (seccion_id) REFERENCES tbl_seccion(seccion_id)
);




DELIMITER $$

CREATE TRIGGER trg_create_solicitud
AFTER INSERT ON tbl_admision
FOR EACH ROW
BEGIN
    INSERT INTO tbl_solicitud (solicitud_id, estado)
    VALUES (NEW.admision_id);
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

INSERT INTO tbl_usuario (nombre_completo, identidad, correo, numero_cuenta, contrasenia, telefono) VALUES ("Miguel Alejandro Sánchez Pavón", "0801200212345", "miguel@gmail.com", "20211002227", "12345", "87702024");
INSERT INTO tbl_usuario (nombre_completo, identidad, correo, numero_cuenta, contrasenia, telefono) VALUES ("Gabriel Antonio Sánchez Pavón", "0801200212346", "gabriel@gmail.com", "20211002228", "12345", "87702025");
INSERT INTO tbl_usuario (nombre_completo, identidad, correo, numero_cuenta, contrasenia, telefono) VALUES ("Yeymi Gabriela Sánchez Pavón", "0801200212347", "yeymi@gmail.com", "20211002229", "12345", "87702026");
INSERT INTO tbl_usuario (nombre_completo, identidad, correo, numero_cuenta, contrasenia, telefono) VALUES ("Rafael Armando Sánchez Pavón", "0801200212348", "rafael@gmail.com", "20211002220", "12345", "87702027");
INSERT INTO tbl_usuario (nombre_completo, identidad, correo, numero_cuenta, contrasenia, telefono) VALUES ("Carlos Fernando Sánchez Pavón", "0801200212349", "carlos30@gmail.com", "20211002221", "12345", "87702028");

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

INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Derecho", 5.0, "Licenciatura", 1);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Antropología", 5.0, "Licenciatura", 2);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Periodismo", 4.0, "Licenciatura", 2);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Psicología", 4.5, "Licenciatura", 2);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Pedagogía", 5.0, "Licenciatura", 3);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Trabajo Social", 5.0, "Licenciatura", 2);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Historia", 5.0, "Licenciatura", 2);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Letras", 5.0, "Licenciatura", 3);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Filosofía", 5.0, "Licenciatura", 3);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Sociología", 5.0, "Licenciatura", 2);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Educación Física", 5.0, "Licenciatura", 3);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Lenguas Extranjeras con Orientación en Inglés y Francés", 5.5, "Licenciatura", 3);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Música", 5.0, "Licenciatura", 3);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Desarrollo Local", 5.0, "Licenciatura", 2);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Ingeniería Civil", 5.0, "Licenciatura", 4);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Ingeniería Mecánica Industrial", 5.0, "Licenciatura", 4);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Ingeniería Eléctrica Industrial", 5.0, "Licenciatura", 4);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Ingeniería Industrial", 5.0, "Licenciatura", 4);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Ingeniería en Sistemas", 5.0, "Licenciatura", 4);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Arquitectura", 5.0, "Licenciatura", 3);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Matemática", 4.0, "Licenciatura", 8);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Física", 4.0, "Licenciatura", 8);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Astronomía y Astrofísica", 5.0, "Licenciatura", 5);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Ciencia y Tecnologías de la Información Geográfica", 4.0, "Licenciatura", 5);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Medicina y Cirugía", 7.0, "Licenciatura", 6);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Odontología", 6.0, "Licenciatura", 7);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Nutrición", 5.0, "Licenciatura", 6);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Química y Farmacia", 5.0, "Licenciatura", 9);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Enfermería", 5.5, "Licenciatura", 6);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Microbiología", 5.0, "Licenciatura", 8);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Biología", 5.5, "Licenciatura", 8);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Fonoaudiología", 4.5, "Licenciatura", 6);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Administración y Generación de Empresas", 4.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Administración Pública", 5.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Economía", 5.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Contaduría Pública y Finanzas", 5.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Administración Aduanera", 4.5, "Licenciatura", 10);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Banca y Finanzas", 5.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Comercio Internacional", 5.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Informática Administrativa", 4.5, "Licenciatura", 10);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Mercadotecnia", 5.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Ingeniería Agronómica", 5.0, "Licenciatura", 4);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Ingeniería Forestal", 5.0, "Licenciatura", 4);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Ingeniería Agroindustrial", 5.0, "Licenciatura", 4);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Ingeniería en Ciencias Acuícolas y Recursos Marinos Costeros", 5.0, "Licenciatura", 4);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Economía Agrícola", 5.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Ecoturismo", 5.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Comercio Internacional con Orientación en Agroindustria", 5.0, "Licenciatura", 10);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Técnico Universitario en Educación Básica para la Enseñanza del Español", 2.5, "Técnico Universitario", 3);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Técnico Universitario Metalurgia", 2.5, "Técnico Universitario", 8);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Técnico Universitario en Producción Agrícola", 2.5, "Técnico Universitario", 4);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Técnico Universitario en Terapia Funcional", 2.5, "Técnico Universitario", 6);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Técnico Universitario en Radiotecnologías (Radiología e Imágenes)", 2.5, "Técnico Universitario", 6);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Técnico Universitario en Microfinanzas", 2.5, "Técnico Universitario", 10);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Técnico Universitario en Alimentos y Bebidas", 2.5, "Técnico Universitario", 10);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Técnico Universitario en Control de Calidad del Café", 2.5, "Técnico Universitario", 4);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Técnico Universitario en Administración de Empresas Cafetaleras", 2.5, "Técnico Universitario", 10);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Técnico Universitario en Desarollo Municipal", 2.5, "Técnico Universitario", 2);
INSERT INTO tbl_carrera (nombre_carrera, duracion, grado, facultad_id) VALUES("Licenciatura en Administración de Empresas Agropecuarias", 4.5, "Licenciatura", 10);

INSERT INTO tbl_centro_regional (nombre_centro, ubicacion) VALUES ("UNAH Ciudad Universitaria", "Tegucigalpa");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion) VALUES ("UNAH Cortés", "Cortés");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion) VALUES ("UNAH Comayagua", "Comayagua");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion) VALUES ("UNAH Atlántida", "Atlántida");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion) VALUES ("UNAH Choluteca", "Choluteca");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion) VALUES ("UNAH Copán", "Copán");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion) VALUES ("UNAH Olancho", "Olancho");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion) VALUES ("UNAH El Paraíso", "El Paraíso");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion) VALUES ("UNAH Yoro", "Yoro");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion) VALUES ("Instituto Tecnológico Superior Tela", "Atlántida");
INSERT INTO tbl_centro_regional (nombre_centro, ubicacion) VALUES ("CRAED", "A distancia");

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

INSERT INTO tbl_departamento (nombre, facultad_id) VALUES
("Derecho", 1),
("Psicología", 2),
("Ingeniería en sistemas", 3);

-- Insertar en tbl_estudiante
INSERT INTO tbl_estudiante (usuario_id, carrera_id, centro_regional_id, correo) VALUES
(1, 1, 1, "miguelestudiante@gmail.com"),
(2, 2, 2, "gabrielestudiante@gmail.com");

-- Insertar en tbl_docente
INSERT INTO tbl_docente (usuario_id, departamento_id, carrera_id, centro_regional_id) VALUES
(2, 1, 1, 1),
(3, 3, 1, 1);

-- Coordinador
INSERT INTO tbl_coordinador (docente_id, carrera_id) VALUES
(2, 1);

-- Edificio
INSERT INTO tbl_edificio (facultad_id, centro_regional_id, edificio) VALUES
(1, 1, "B2"),
(2, 2, "A1");

-- Insertar en tbl_aula
INSERT INTO tbl_aula (aula, edificio_id) VALUES
("Aula 101", 1),
("Aula 102", 2);

-- Insertar en tbl_clase
INSERT INTO tbl_clase (edificio_id, departamento_id, nombre, codigo, UV) VALUES
(1, 1, "Introducción al Derecho", "DERE001", 5),
(2, 2, "Psicología General", "PSI001", 4);

INSERT INTO tbl_seccion (clase_id, docente_id, aula_id, periodo_academico, horario, dias, cupo_maximo) VALUES
(1, 2, 1, "2025-1", "08:00-10:00", "Lunes, Miércoles", 30),
(2, 2, 2, "2025-1", "10:00-12:00", "Martes, Jueves", 25);

-- Insertar en tbl_matricula
INSERT INTO tbl_matricula (estudiante_id, seccion_id, fechaInscripcion) VALUES
(1, 1, "2025-03-20"),
(2, 2, "2025-03-22");

