-- ============================================================
-- StreamTec - Vistas
-- ============================================================

CREATE OR REPLACE VIEW vista_top_contenido_visto AS
SELECT
    c.idContenido,
    c.Titulo,
    t.Tipo AS TipoContenido,
    COUNT(*) AS VecesVisto,
    ROUND(AVG(hv.Progreso), 2) AS PromedioProgreso
FROM HistorialVisualizacion hv
JOIN Contenido c ON hv.idContenido = c.idContenido
JOIN TipoContenido t ON c.idTipoContenido = t.idTipoContenido
GROUP BY c.idContenido, c.Titulo, t.Tipo
ORDER BY VecesVisto DESC
LIMIT 10;

-- Vista para mostrar películas con su director
CREATE OR REPLACE VIEW vista_peliculas AS
SELECT
    p.idPelicula,
    c.idContenido,
    c.Titulo,
    c.Duracion,
    c.Descripcion,
    c.Imagen,
    c.TrailerURL,
    d.Nombre || ' ' || COALESCE(d.A_Paterno, '') AS Director
FROM Pelicula p
JOIN Contenido c ON p.idContenido = c.idContenido
LEFT JOIN Director d ON c.idDirector = d.idDirector;

-- Vista para mostrar series con su director
CREATE OR REPLACE VIEW vista_series AS
SELECT
    s.idSerie,
    c.idContenido,
    c.Titulo,
    c.Duracion,
    c.Descripcion,
    c.Imagen,
    c.TrailerURL,
    d.Nombre || ' ' || COALESCE(d.A_Paterno, '') AS Director
FROM Serie s
JOIN Contenido c ON s.idContenido = c.idContenido
LEFT JOIN Director d ON c.idDirector = d.idDirector;

-- Vista para mostrar documentales con su director
CREATE OR REPLACE VIEW vista_documentales AS
SELECT
    doc.idDocumental,
    c.idContenido,
    c.Titulo,
    c.Duracion,
    c.Descripcion,
    c.Imagen,
    c.TrailerURL,
    d.Nombre || ' ' || COALESCE(d.A_Paterno, '') AS Director
FROM Documental doc
JOIN Contenido c ON doc.idContenido = c.idContenido
LEFT JOIN Director d ON c.idDirector = d.idDirector;
