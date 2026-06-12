-- ============================================================
-- StreamTec - Usuarios y permisos
-- ============================================================

-- Usuario para la aplicación web (PHP usará este usuario)
DROP USER IF EXISTS app_streamtec;
CREATE USER app_streamtec WITH PASSWORD 'streamtec2026';

-- Permisos sobre todas las tablas y secuencias del esquema public
GRANT CONNECT ON DATABASE streamtec TO app_streamtec;
GRANT USAGE ON SCHEMA public TO app_streamtec;
GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO app_streamtec;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO app_streamtec;

-- Para que las tablas/vistas/secuencias creadas en el futuro también tengan permisos
ALTER DEFAULT PRIVILEGES IN SCHEMA public
    GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO app_streamtec;
ALTER DEFAULT PRIVILEGES IN SCHEMA public
    GRANT USAGE, SELECT ON SEQUENCES TO app_streamtec;
