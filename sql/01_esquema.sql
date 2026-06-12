-- ============================================================
-- StreamTec - Base de datos reducida
-- ============================================================

DROP TABLE IF EXISTS HistorialVisualizacion CASCADE;
DROP TABLE IF EXISTS Episodios CASCADE;
DROP TABLE IF EXISTS Temporada CASCADE;
DROP TABLE IF EXISTS Pelicula CASCADE;
DROP TABLE IF EXISTS Serie CASCADE;
DROP TABLE IF EXISTS Documental CASCADE;
DROP TABLE IF EXISTS Contenido CASCADE;
DROP TABLE IF EXISTS TipoContenido CASCADE;
DROP TABLE IF EXISTS Director CASCADE;
DROP TABLE IF EXISTS Perfil CASCADE;
DROP TABLE IF EXISTS Usuario CASCADE;

-- ---------------------------------------------------------
-- Usuario (incluye credenciales para login)
-- ---------------------------------------------------------
CREATE TABLE Usuario (
    idUsuario   SERIAL PRIMARY KEY,
    Nombre      VARCHAR(30) NOT NULL,
    A_Paterno   VARCHAR(30),
    A_Materno   VARCHAR(30),
    Correo      VARCHAR(100) NOT NULL UNIQUE,
    Contrasena  VARCHAR(255) NOT NULL
);

-- ---------------------------------------------------------
-- TipoContenido
-- ---------------------------------------------------------
CREATE TABLE TipoContenido (
    idTipoContenido SERIAL PRIMARY KEY,
    Tipo            VARCHAR(50) NOT NULL UNIQUE
);

-- ---------------------------------------------------------
-- Director
-- ---------------------------------------------------------
CREATE TABLE Director (
    idDirector  SERIAL PRIMARY KEY,
    Nombre      VARCHAR(30) NOT NULL,
    A_Paterno   VARCHAR(30),
    A_Materno   VARCHAR(30),
    CONSTRAINT unique_nombre_director UNIQUE (Nombre, A_Paterno, A_Materno)
);

-- ---------------------------------------------------------
-- Contenido (tabla base de Pelicula / Serie / Documental)
-- ---------------------------------------------------------
CREATE TABLE Contenido (
    idContenido     SERIAL PRIMARY KEY,
    Titulo          VARCHAR(100) NOT NULL UNIQUE,
    Duracion        TIME NOT NULL CHECK (Duracion > TIME '00:00:00'),
    Descripcion     TEXT,
    Imagen          VARCHAR(255),
    TrailerURL      VARCHAR(255),
    idTipoContenido INTEGER NOT NULL REFERENCES TipoContenido(idTipoContenido),
    idDirector      INTEGER REFERENCES Director(idDirector)
);

-- ---------------------------------------------------------
-- Pelicula
-- ---------------------------------------------------------
CREATE TABLE Pelicula (
    idPelicula  SERIAL PRIMARY KEY,
    idContenido INTEGER NOT NULL UNIQUE REFERENCES Contenido(idContenido) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- Serie
-- ---------------------------------------------------------
CREATE TABLE Serie (
    idSerie     SERIAL PRIMARY KEY,
    idContenido INTEGER NOT NULL UNIQUE REFERENCES Contenido(idContenido) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- Documental
-- ---------------------------------------------------------
CREATE TABLE Documental (
    idDocumental SERIAL PRIMARY KEY,
    idContenido  INTEGER NOT NULL UNIQUE REFERENCES Contenido(idContenido) ON DELETE CASCADE
);

-- ---------------------------------------------------------
-- HistorialVisualizacion
-- ---------------------------------------------------------
CREATE TABLE HistorialVisualizacion (
    idUsuario   INTEGER NOT NULL REFERENCES Usuario(idUsuario) ON DELETE CASCADE,
    idContenido INTEGER NOT NULL REFERENCES Contenido(idContenido) ON DELETE CASCADE,
    Progreso    INTEGER CHECK (Progreso BETWEEN 0 AND 100),
    Fecha       DATE DEFAULT CURRENT_DATE,
    Hora        TIME DEFAULT CURRENT_TIME,
    PRIMARY KEY (idUsuario, idContenido)
);

-- ---------------------------------------------------------
-- MiLista (favoritos del usuario)
-- ---------------------------------------------------------
CREATE TABLE MiLista (
    idUsuario   INTEGER NOT NULL REFERENCES Usuario(idUsuario) ON DELETE CASCADE,
    idContenido INTEGER NOT NULL REFERENCES Contenido(idContenido) ON DELETE CASCADE,
    FechaAgregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (idUsuario, idContenido)
);
