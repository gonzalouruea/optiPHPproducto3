# Usar una imagen base de PHP 8.2 con Apache
FROM php:8.2-apache

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Instalar dependencias del sistema y extensiones PHP
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install \
    pdo_mysql \
    zip \
    mbstring \
    gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Habilitar el módulo de reescritura de Apache
RUN a2enmod rewrite

# Copiar los archivos del proyecto al contenedor
COPY src/ /var/www/html/

# Asegurar permisos adecuados
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Exponer el puerto 80
EXPOSE 80

# Comando por defecto: iniciar Apache en primer plano
CMD ["apache2-foreground"]