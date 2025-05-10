# optiPHPproducto3

## Instalación

### 1. Configurar el archivo `.env`

Antes de iniciar la máquina, configura el archivo `src/.env` con lo siguiente:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=viajes
DB_USERNAME=user
DB_PASSWORD=user_password
```

### 2. Configurar permisos

Algunos comandos necesitan permisos adecuados en ciertas carpetas. Configura los permisos ejecutando:

```bash
chmod -R 775 storage bootstrap/cache
```

### 3. Construir y levantar los servicios con Docker

Asegúrate de que el archivo `docker-compose.yml` esté configurado correctamente. Luego, construye la imagen y levanta los servicios con:

```bash
docker-compose up -d --build
```

### 4. Instalar las dependencias de Composer

Accede al contenedor del servicio `web`:

```bash
docker-compose exec -it web bash
```

Dentro del contenedor, instala las dependencias de Composer:

```bash
composer install
```

### 5. Instalar las dependencias de Node.js

Dentro del contenedor, instala las dependencias de Node.js:

```bash
npm install
```

### 6. Compilar los assets

Para compilar los assets (CSS, JS), ejecuta:

- En modo desarrollo:
  ```bash
  npm run dev
  ```

- En modo producción:
  ```bash
  npm run build
  ```

### 7. Generar la clave de la aplicación

Genera la clave de la aplicación Laravel ejecutando:

```bash
php artisan key:generate
```

### 8. Ejecutar las migraciones

Si el proyecto utiliza una base de datos, ejecuta las migraciones para crear las tablas necesarias:

```bash
php artisan migrate
```

---

## Resumen de Comandos

```bash
# Clonar el repositorio
git clone <URL_DEL_REPOSITORIO>
cd producto3Laravel

# Configurar el archivo .env
cp .env.example .env

# Configurar permisos
chmod -R 775 storage bootstrap/cache

# Construir y levantar los servicios
docker-compose up -d --build

# Acceder al contenedor
docker-compose exec -it web bash

# Instalar dependencias de Composer
composer install

# Instalar dependencias de Node.js
npm install

# Compilar los assets
npm run dev

# Generar la clave de la aplicación
php artisan key:generate

# Ejecutar las migraciones
php artisan migrate
```

---

Con este formato, el archivo `README.md` es más claro y fácil de seguir para cualquier desarrollador que colabore en el proyecto. Si necesitas más ajustes, házmelo saber. 🚀

