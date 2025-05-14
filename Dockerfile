FROM php:8.1-apache

# 1) Instalamos libs del sistema
RUN apt-get update && apt-get install -y \
  libzip-dev \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  unzip \
  git \
  curl \
  && docker-php-ext-install \
  pdo_mysql \
  zip \
  mbstring \
  gd \
  xml \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/*

# 2) Habilitamos rewrite
RUN a2enmod rewrite

# 3) Instalamos Composer
RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer \
  && chmod +x /usr/local/bin/composer

# 4) Ponemos el directorio de trabajo
WORKDIR /var/www/html

# 5) Copiamos TODO el código de Laravel (src/) al contenedor
COPY src/ ./

# 6) Ahora sí instalamos las dependencias con Composer
RUN composer install --no-dev --optimize-autoloader

# 7) Cacheamos config, rutas y vistas
RUN php artisan config:cache \
  && php artisan route:cache \
  && php artisan view:cache

# 8) Preparamos storage y cache dir
RUN mkdir -p storage bootstrap/cache \
  && chown -R www-data:www-data storage bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache

# 9) Configuramos Apache para apuntar a /public
RUN printf "<VirtualHost *:80>\n\
  DocumentRoot /var/www/html/public\n\
  <Directory /var/www/html/public>\n\
  Options Indexes FollowSymLinks\n\
  AllowOverride All\n\
  Require all granted\n\
  </Directory>\n\
  </VirtualHost>" > /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["apache2-foreground"]
