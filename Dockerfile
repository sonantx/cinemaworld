FROM php:8.2-apache

# Instalar la extensión pgsql para PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pgsql pdo_pgsql

# Habilitar mod_rewrite (por si se necesita en el futuro)
RUN a2enmod rewrite

# Copiar el código del sitio a la carpeta pública de Apache
COPY public/ /var/www/html/
COPY includes/ /var/www/includes/

# Permisos para que PHP pueda guardar imágenes subidas
RUN chown -R www-data:www-data /var/www/html/img \
    && chmod -R 775 /var/www/html/img

# Script que ajusta el puerto de Apache al $PORT que asigna Render
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 10000
ENV PORT=10000

CMD ["/entrypoint.sh"]
