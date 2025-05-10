# optiPHPproducto3

# Instalación

Antes de iniciar la maquina configurar el archivo src/.env con lo siguiente:

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=viajes
DB_USERNAME=user
DB_PASSWORD=user_password

Configurar permisos ya que algunos comandos necesitan hacer cosas en algunas carpetas:

chmod -R 775 storage bootstrap/cache

Ajustar a lo que tenga el docker-compose.yml, luego se construye la imagen y se pone a correr los servicos usando:

docker-compose up -d --build

# Instalar los paquetes de npm

docker-compose exec -it web  bash

Instalar las dependencias del compose:

/var/www/html> composer install

Luego instalar las dependecias de node:

/var/www/html> npm install
Para compilar las dependencias de 

Generar la llave de artisan:

php artisan key:generate

Ejecutar las migraciones:

php artisan migrate

