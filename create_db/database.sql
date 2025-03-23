DROP DATABASE IF EXISTS BD_UNI;
CREATE DATABASE BD_UNI;
USE BD_UNI;

-- Tabla Usuario
CREATE TABLE Usuario (
    UsuarioID SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    NombreCompleto VARCHAR(50) NOT NULL,
    Identidad CHAR(13) UNIQUE NOT NULL,
    Correo VARCHAR(100) UNIQUE NOT NULL,
    Pass VARCHAR(50) NOT NULL,
    Rol ENUM('Estudiante', 'Docente') NOT NULL,
    NumeroCuenta VARCHAR(50) UNIQUE NOT NULL,
    Telefono CHAR(8),
    Es_Revisor TINYINT(1) NOT NULL,
    INDEX idx_usuario_correo (Correo)
);

-- Tabla Facultad
CREATE TABLE Facultad (
    FacultadID TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    NombreFacultad VARCHAR(100) NOT NULL,
    Decano SMALLINT UNSIGNED,
    FOREIGN KEY (Decano) REFERENCES Usuario(UsuarioID),
    INDEX idx_facultad_nombre (NombreFacultad)
);

-- Tabla Centro Regional
CREATE TABLE CentroRegional (
    CentroRegionalID TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    NombreCentro VARCHAR(100) NOT NULL,
    Ubicacion VARCHAR(255) NOT NULL,
    Telefono CHAR(8),
    Correo VARCHAR(100),
    INDEX idx_centroregional_nombre (NombreCentro)
);

-- Tabla Carrera
CREATE TABLE Carrera (
    CarreraID TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    NombreCarrera VARCHAR(100) NOT NULL,
    Duracion TINYINT UNSIGNED NOT NULL,
    Nivel ENUM('Licenciatura', 'Ingeniería', 'Técnico') NOT NULL,
    FacultadID TINYINT UNSIGNED NOT NULL,
    CentroRegionalID TINYINT UNSIGNED NOT NULL,
    FOREIGN KEY (FacultadID) REFERENCES Facultad(FacultadID),
    FOREIGN KEY (CentroRegionalID) REFERENCES CentroRegional(CentroRegionalID),
    INDEX idx_carrera_nombre (NombreCarrera)
);

-- Tabla Admisión
CREATE TABLE Admision (
    ID SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Primer_nombre VARCHAR(50) NOT NULL,
    Segundo_nombre VARCHAR(50),
    Primer_apellido VARCHAR(50) NOT NULL,
    Pegundo_apellido VARCHAR(50),
    Correo VARCHAR(100) UNIQUE NOT NULL,
    Numero_identidad VARCHAR(20) UNIQUE NOT NULL,
    Numero_telefono VARCHAR(20) NOT NULL,
    Fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CarreraID TINYINT UNSIGNED NOT NULL,
    CarreraAlternativaID TINYINT UNSIGNED,
    CertificadoSecundaria TEXT,
    FOREIGN KEY (CarreraID) REFERENCES Carrera(CarreraID),
    FOREIGN KEY (CarreraAlternativaID) REFERENCES Carrera(CarreraID)
);

CREATE TABLE Solicitud (
    ID SMALLINT UNSIGNED PRIMARY KEY,
    Estado ENUM('Pendiente', 'Aprobada', 'Rechazada') NOT NULL DEFAULT 'Pendiente',
    Observaciones TEXT,
    FOREIGN KEY (ID) REFERENCES Admision(ID) ON DELETE CASCADE
);

-- Tabla Estudiante
CREATE TABLE Estudiante (
    EstudianteID SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    UsuarioID SMALLINT UNSIGNED UNIQUE,
    CarreraID TINYINT UNSIGNED NOT NULL,
    CentroRegionalID TINYINT UNSIGNED NOT NULL,
    CorreoInstitucional VARCHAR(100) UNIQUE NOT NULL,
    NumeroCuenta CHAR(10) UNIQUE NOT NULL,
    FOREIGN KEY (UsuarioID) REFERENCES Usuario(UsuarioID),
    FOREIGN KEY (CarreraID) REFERENCES Carrera(CarreraID),
    FOREIGN KEY (CentroRegionalID) REFERENCES CentroRegional(CentroRegionalID)
);

-- Tabla Docente
CREATE TABLE Docente (
    DocenteID SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    UsuarioID SMALLINT UNSIGNED UNIQUE,
    CentroRegionalID TINYINT UNSIGNED NOT NULL,
    FOREIGN KEY (UsuarioID) REFERENCES Usuario(UsuarioID),
    FOREIGN KEY (CentroRegionalID) REFERENCES CentroRegional(CentroRegionalID)
);

-- Tabla Coordinador
CREATE TABLE Coordinador (
    CoordinadorID TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    DocenteID SMALLINT UNSIGNED UNIQUE,
    DepartamentoID TINYINT UNSIGNED NOT NULL,
    FOREIGN KEY (DocenteID) REFERENCES Docente(DocenteID)
);

-- Tabla Categoría Libro
CREATE TABLE CategoriaLibro (
    CategoriaID TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(50) NOT NULL,
    INDEX idx_categoria_nombre (Nombre)
);

-- Tabla Biblioteca
CREATE TABLE Biblioteca (
    LibroID SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Titulo VARCHAR(150) NOT NULL,
    Autor VARCHAR(100) NOT NULL,
    CategoriaLibroID TINYINT UNSIGNED NOT NULL,
    ArchivoPDF TEXT,
    FOREIGN KEY (CategoriaLibroID) REFERENCES CategoriaLibro(CategoriaID)
);

-- Tabla Sección
CREATE TABLE Seccion (
    SeccionID SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Asignatura VARCHAR(100) NOT NULL,
    DocenteID SMALLINT UNSIGNED NOT NULL,
    PeriodoAcademico VARCHAR(20) NOT NULL,
    Aula VARCHAR(20),
    Horario VARCHAR(50),
    CupoMaximo TINYINT UNSIGNED NOT NULL,
    FOREIGN KEY (DocenteID) REFERENCES Docente(DocenteID)
);

-- Tabla Matrícula
CREATE TABLE Matricula (
    MatriculaID SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    EstudianteID SMALLINT UNSIGNED NOT NULL,
    SeccionID SMALLINT UNSIGNED NOT NULL,
    FechaInscripcion DATE NOT NULL,
    EstadoMatricula ENUM('Activo', 'Inactivo') NOT NULL,
    FOREIGN KEY (EstudianteID) REFERENCES Estudiante(EstudianteID),
    FOREIGN KEY (SeccionID) REFERENCES Seccion(SeccionID)
);

-- Tabla Notas
CREATE TABLE Notas (
    NotaID SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    EstudianteID SMALLINT UNSIGNED NOT NULL,
    SeccionID SMALLINT UNSIGNED NOT NULL,
    Calificacion DECIMAL(4,2) NOT NULL,
    FOREIGN KEY (EstudianteID) REFERENCES Estudiante(EstudianteID),
    FOREIGN KEY (SeccionID) REFERENCES Seccion(SeccionID)
);

-- Inserts para Usuario
INSERT INTO Usuario (NombreCompleto, Identidad, Correo, Pass,Es_Revisor,NumeroCuenta, Telefono) VALUES
('Juan Pérez', '0801199901234', 'juan.perez@gmail.com', 'clave123',1, '123456789','98765432'),
('María López', '0802199505678', 'maria.lopez@gmail.com', 'pass456',1,'987654321', '99887766');

-- Inserts para Centro Regional
INSERT INTO CentroRegional (NombreCentro, Ubicacion, Telefono, Correo) VALUES
('Centro Regional Tegucigalpa', 'Tegucigalpa, Honduras', '22334455', 'info@uniteg.hn');

-- Inserts para Facultad
-- Inserts para Facultad
INSERT INTO Facultad (NombreFacultad, Decano) VALUES
('Facultad de Ingeniería', 2),
('Facultad de Ciencias Económicas', NULL),
('Facultad de Ciencias de la Salud', NULL),
('Facultad de Ciencias Sociales', NULL),
('Facultad de Humanidades y Artes', NULL),
('Facultad de Ciencias Jurídicas', NULL),
('Facultad de Ciencias Exactas y Naturales', NULL);

-- Inserts para Carrera
INSERT INTO Carrera (NombreCarrera, Duracion, Nivel, FacultadID, CentroRegionalID) VALUES
-- Ingeniería
('Ingeniería en Sistemas', 5, 'Ingeniería', 1, 1),
('Ingeniería Civil', 5, 'Ingeniería', 1, 1),
('Ingeniería Industrial', 5, 'Ingeniería', 1, 1),
('Ingeniería Mecánica', 5, 'Ingeniería', 1, 1),
('Ingeniería Eléctrica', 5, 'Ingeniería', 1, 1),
('Ingeniería Electrónica', 5, 'Ingeniería', 1, 1),
('Administración de Empresas', 4, 'Licenciatura', 2, 1),
('Contaduría Pública', 4, 'Licenciatura', 2, 1),
('Economía', 4, 'Licenciatura', 2, 1),
('Medicina', 7, 'Licenciatura', 3, 1),
('Odontología', 5, 'Licenciatura', 3, 1),
('Enfermería', 5, 'Licenciatura', 3, 1),
('Psicología', 4, 'Licenciatura', 4, 1),
('Trabajo Social', 4, 'Licenciatura', 4, 1),
('Ciencias de la Educación', 4, 'Licenciatura', 5, 1),
('Artes Plásticas', 4, 'Licenciatura', 5, 1),
('Derecho', 5, 'Licenciatura', 6, 1),
('Matemáticas', 4, 'Licenciatura', 7, 1),
('Biología', 4, 'Licenciatura', 7, 1),
('Física', 4, 'Licenciatura', 7, 1);

DELIMITER $$

CREATE TRIGGER after_admision_insert
AFTER INSERT ON Admision
FOR EACH ROW
BEGIN
    INSERT INTO Solicitud (id, estado)
    VALUES (NEW.id, 'Pendiente');
END $$

DELIMITER ;

ALTER TABLE Usuario MODIFY Pass VARCHAR(255) NOT NULL;

ALTER TABLE Docente 
ADD COLUMN CodigoEmpleado CHAR(10) UNIQUE NOT NULL;

ALTER TABLE Docente 
ADD COLUMN CarreraID TINYINT UNSIGNED NOT NULL AFTER CentroRegionalID, 
ADD FOREIGN KEY (CarreraID) REFERENCES Carrera(CarreraID);

ALTER TABLE Solicitud
ADD COLUMN nota SMALLINT NULL,
ADD COLUMN codigo VARCHAR(20) NOT NULL;

ALTER TABLE Solicitud
ADD CONSTRAINT unique_codigo UNIQUE (codigo);

CREATE TABLE Clase (
    ClaseID SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    Codigo VARCHAR(20) UNIQUE NOT NULL
);

ALTER TABLE Seccion
ADD COLUMN ClaseID SMALLINT UNSIGNED NOT NULL,
ADD CONSTRAINT FK_Seccion_Clase FOREIGN KEY (ClaseID) REFERENCES Clase(ClaseID);

ALTER TABLE Seccion
DROP COLUMN Asignatura;

ALTER TABLE Carrera
DROP FOREIGN KEY Carrera_ibfk_2,
DROP COLUMN CentroRegionalID;

CREATE TABLE CentroRegional_Carrera (
    CentroRegionalID TINYINT UNSIGNED NOT NULL,
    CarreraID TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (CentroRegionalID, CarreraID),
    FOREIGN KEY (CentroRegionalID) REFERENCES CentroRegional(CentroRegionalID),
    FOREIGN KEY (CarreraID) REFERENCES Carrera(CarreraID)
);

CREATE TABLE Departamento (
    DepartamentoID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Departamento VARCHAR(50) NOT NULL,
    FacultadID TINYINT UNSIGNED NOT NULL,
    CONSTRAINT fk_facultad FOREIGN KEY (FacultadID) REFERENCES Facultad(FacultadID),
    INDEX idx_departamento (Departamento)
);

ALTER TABLE Clase 
ADD COLUMN DepartamentoID INT UNSIGNED NOT NULL,
ADD CONSTRAINT fk_clase_departamento FOREIGN KEY (DepartamentoID) REFERENCES Departamento(DepartamentoID);




