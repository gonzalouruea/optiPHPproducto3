# Usar una imagen base de PHP 8.1 con Apache
FROM php:8.1.2-apache

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Instalar dependencias del sistema y extensiones PHP necesarias para Laravel
RUN apt-get update && apt-get install -y \
  libzip-dev \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  unzip \
  git \
  curl \
  && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
  && apt-get install -y nodejs \
  && docker-php-ext-install \
  pdo_mysql \
  zip \
  mbstring \
  gd \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/*

# Habilitar el módulo de reescritura de Apache
RUN a2enmod rewrite

# Instalar Composer manualmente
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Copiar el contenido del proyecto al contenedor
COPY . /var/www/html/


# Crear los directorios necesarios para Laravel
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache

# Configuración personalizada de Apache para permitir acceso a /public
RUN echo "<VirtualHost *:80>\n\
  DocumentRoot /var/www/html/public\n\
  <Directory /var/www/html/public>\n\
  Options Indexes FollowSymLinks\n\
  AllowOverride All\n\
  Require all granted\n\
  </Directory>\n\
  </VirtualHost>" > /etc/apache2/sites-available/000-default.conf

# Asegurar permisos adecuados para Laravel
RUN chown -R www-data:www-data /var/www/html \
  && chmod -R 755 /var/www/html \
  && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer el puerto 80
EXPOSE 80

# Comando por defecto: iniciar Apache en primer plano
CMD ["apache2-foreground"]
