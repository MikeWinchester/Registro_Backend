-- Tabla Usuario
CREATE TABLE Usuario (
    UsuarioID SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    NombreCompleto VARCHAR(50) NOT NULL,
    Identidad CHAR(13) UNIQUE NOT NULL,
    Correo VARCHAR(100) UNIQUE NOT NULL,
    Pass VARCHAR(100) NOT NULL,
    Rol ENUM('Estudiante', 'Docente') NOT NULL,
    NumeroCuenta CHAR(11) UNIQUE NOT NULL,
    Telefono CHAR(8),
    Es_Revisor TINYINT(1) NOT NULL DEFAULT 0,
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
    AdmisionID SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    UsuarioID SMALLINT UNSIGNED UNIQUE,
    FechaSolicitud DATE NOT NULL,
    Estado ENUM('Pendiente', 'Aprobada', 'Rechazada') NOT NULL,
    CarreraID TINYINT UNSIGNED NOT NULL,
    CarreraAlternativaID TINYINT UNSIGNED,
    CentroRegionalID TINYINT UNSIGNED NOT NULL,
    CertificadoSecundaria TEXT,
    Observaciones TEXT,
    FOREIGN KEY (UsuarioID) REFERENCES Usuario(UsuarioID),
    FOREIGN KEY (CarreraID) REFERENCES Carrera(CarreraID),
    FOREIGN KEY (CarreraAlternativaID) REFERENCES Carrera(CarreraID)
    FOREIGN KEY (CentroRegionalID) REFERENCES CentroRegional(CentroRegionalID)
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
    NumeroCuenta CHAR(10) UNIQUE NOT NULL,
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

