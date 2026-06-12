-- ============================================================
-- StreamTec - Funciones
-- ============================================================

-- Cuenta cuántos contenidos ha visto un usuario
CREATE OR REPLACE FUNCTION contar_contenidos_vistos(idusuarioinput INT)
RETURNS INT AS $$
DECLARE
    total INT;
BEGIN
    SELECT COUNT(*) INTO total
    FROM HistorialVisualizacion
    WHERE idUsuario = idusuarioinput;

    RETURN total;
END;
$$ LANGUAGE plpgsql;

-- Ejemplo de uso:
-- SELECT contar_contenidos_vistos(1);
