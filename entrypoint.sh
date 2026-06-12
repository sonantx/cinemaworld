#!/bin/bash
set -e

# Render asigna el puerto en la variable de entorno $PORT.
# Si no existe (ej. ejecución local con docker run), usamos 10000.
PORT="${PORT:-10000}"

sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

exec apache2-foreground
