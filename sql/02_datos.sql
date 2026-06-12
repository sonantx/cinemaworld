-- ============================================================
-- StreamTec - Datos iniciales
-- ============================================================

-- TipoContenido
INSERT INTO TipoContenido (Tipo) VALUES
('Película'),
('Serie'),
('Documental');

-- Director
INSERT INTO Director (Nombre, A_Paterno, A_Materno) VALUES
('Christopher', 'Nolan', 'Smith'),
('Alfonso', 'Cuarón', 'Martinez'),
('Greta', 'Gerwig', 'Walker'),
('Vince', 'Gilligan', NULL),
('Alastair', 'Fothergill', NULL);

-- Usuario (contraseñas guardadas como hash bcrypt de "123456")
-- Hash generado con password_hash('123456', PASSWORD_DEFAULT)
INSERT INTO Usuario (Nombre, A_Paterno, A_Materno, Correo, Contrasena) VALUES
('Norma', 'Lopez', 'Hernandez', 'norma@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.YeYmJTzzlSuGW7nZqB.qaY8x6lp5IXEFa'),
('Hugo', 'Lopez', 'Hernandez', 'hugo@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.YeYmJTzzlSuGW7nZqB.qaY8x6lp5IXEFa'),
('Kenia', 'Flores', 'Ozuna', 'kenia@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.YeYmJTzzlSuGW7nZqB.qaY8x6lp5IXEFa'),
('Cesar', 'Huerta', 'Magallan', 'cesar@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.YeYmJTzzlSuGW7nZqB.qaY8x6lp5IXEFa'),
('Sofía', 'Ortega', 'Ponce', 'sofia@correo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.YeYmJTzzlSuGW7nZqB.qaY8x6lp5IXEFa');
-- Contraseña de todos los usuarios de ejemplo: 123456

-- Contenido + Pelicula
INSERT INTO Contenido (Titulo, Duracion, Descripcion, Imagen, TrailerURL, idTipoContenido, idDirector) VALUES
('Inception', '02:28:00', 'Un ladrón roba secretos del subconsciente mediante sueños compartidos.', 'inception.svg', 'https://www.youtube.com/embed/YoHD9XEInc0', 1, 1),
('Interstellar', '02:49:00', 'Un grupo de exploradores viaja a través de un agujero de gusano en busca de un nuevo hogar para la humanidad.', 'interstellar.svg', 'https://www.youtube.com/embed/2LqzF5WauAw', 1, 1),
('Roma', '02:15:00', 'La vida de una familia de clase media en la Ciudad de México de los años 70.', 'roma.svg', 'https://www.youtube.com/embed/6BS27ngZtxg', 1, 2),
('Lady Bird', '01:34:00', 'Una adolescente navega su último año de preparatoria y su relación con su madre.', 'ladybird.svg', 'https://www.youtube.com/embed/cNi_HC839Wo', 1, 3);

INSERT INTO Pelicula (idContenido) VALUES
(1), (2), (3), (4);

-- Contenido + Serie
INSERT INTO Contenido (Titulo, Duracion, Descripcion, Imagen, TrailerURL, idTipoContenido, idDirector) VALUES
('Breaking Bad', '00:47:00', 'Un profesor de química se convierte en fabricante de metanfetaminas.', 'breakingbad.svg', 'https://www.youtube.com/embed/HhesaQXLuRY', 2, 4);

INSERT INTO Serie (idContenido) VALUES
(5);

-- Contenido + Documental
INSERT INTO Contenido (Titulo, Duracion, Descripcion, Imagen, TrailerURL, idTipoContenido, idDirector) VALUES
('Planet Earth', '01:00:00', 'Una serie documental que explora la diversidad de hábitats en la Tierra.', 'planetearth.svg', 'https://www.youtube.com/embed/c8aFcHFu8QM', 3, 5);

INSERT INTO Documental (idContenido) VALUES
(6);

-- HistorialVisualizacion
INSERT INTO HistorialVisualizacion (idUsuario, idContenido, Progreso, Fecha, Hora) VALUES
(1, 1, 100, CURRENT_DATE, CURRENT_TIME),
(1, 5, 45,  CURRENT_DATE, CURRENT_TIME),
(2, 2, 70,  CURRENT_DATE, CURRENT_TIME),
(3, 3, 30,  CURRENT_DATE, CURRENT_TIME),
(4, 6, 80,  CURRENT_DATE, CURRENT_TIME),
(5, 4, 20,  CURRENT_DATE, CURRENT_TIME);

-- MiLista (favoritos de ejemplo)
INSERT INTO MiLista (idUsuario, idContenido) VALUES
(1, 2),
(1, 6),
(2, 1);
