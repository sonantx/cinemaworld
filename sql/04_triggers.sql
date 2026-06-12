-- ============================================================
-- StreamTec - Triggers
-- ============================================================

-- Cuando se inserta un Contenido, según su TipoContenido se crea
-- automáticamente el registro correspondiente en Pelicula, Serie o Documental.

CREATE OR REPLACE FUNCTION fn_crear_subtipo_contenido()
RETURNS TRIGGER AS $$
BEGIN
    IF NEW.idTipoContenido = 1 THEN
        INSERT INTO Pelicula (idContenido) VALUES (NEW.idContenido);
    ELSIF NEW.idTipoContenido = 2 THEN
        INSERT INTO Serie (idContenido) VALUES (NEW.idContenido);
    ELSIF NEW.idTipoContenido = 3 THEN
        INSERT INTO Documental (idContenido) VALUES (NEW.idContenido);
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_crear_subtipo_contenido ON Contenido;

CREATE TRIGGER trg_crear_subtipo_contenido
AFTER INSERT ON Contenido
FOR EACH ROW
EXECUTE FUNCTION fn_crear_subtipo_contenido();
