FROM php:8.1-apache

# Instalamos extensiones necesarias para PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Habilitamos la reescritura de URLs para que index.php maneje todo
RUN a2enmod rewrite

# Copiamos los archivos (opcional si usas volumes en compose)
COPY . /var/www/html/

# Exponemos el puerto 80
EXPOSE 80