# Despliegue del backend Laravel con MySQL/phpMyAdmin

Este backend necesita un hosting con PHP, MySQL y phpMyAdmin. GitHub Pages no sirve para Laravel porque solo publica archivos estaticos.

## 1. Crear hosting

Usa un hosting con:

- PHP 8.2 o superior.
- MySQL.
- phpMyAdmin.
- Soporte para `.htaccess`.
- Acceso a terminal/SSH recomendado.

En hosting gratis con phpMyAdmin, InfinityFree puede servir para pruebas, pero suele ser mas limitado para Laravel porque no siempre permite correr Composer, `php artisan migrate` o crear symlinks comodamente. En cPanel pago o hosting Laravel el despliegue es mas simple.

## 2. Crear base de datos en phpMyAdmin

En el panel del hosting:

1. Crea una base de datos MySQL.
2. Crea o copia el usuario y password de MySQL.
3. Abre phpMyAdmin y confirma que puedes entrar a esa base.

Guarda estos datos:

```txt
DB_HOST=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

## 3. Configurar `.env`

Copia `.env.production.example` como `.env` en el servidor y rellena:

```txt
APP_URL=https://TU-DOMINIO-DEL-BACKEND.com
FRONTEND_URL=https://dayanarojasdrp.github.io
SANCTUM_STATEFUL_DOMAINS=dayanarojasdrp.github.io

DB_CONNECTION=mysql
DB_HOST=TU_HOST_MYSQL
DB_PORT=3306
DB_DATABASE=TU_NOMBRE_DE_BASE_DE_DATOS
DB_USERNAME=TU_USUARIO_MYSQL
DB_PASSWORD=TU_PASSWORD_MYSQL
```

Genera `APP_KEY` antes de subir o desde el servidor:

```bash
php artisan key:generate --show
```

Pega el valor generado en `APP_KEY`.

## 4. Instalar dependencias

Si el hosting tiene SSH:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --seed
php artisan storage:link
php artisan config:cache
php artisan route:cache
```

Si el hosting no tiene SSH:

1. Ejecuta localmente `composer install --no-dev --optimize-autoloader`.
2. Sube el proyecto completo con la carpeta `vendor`.
3. Exporta la base local o genera las tablas en otro entorno y luego importa el `.sql` desde phpMyAdmin.
4. Si no puedes crear `storage:link`, crea manualmente un enlace/carpeta publica para que `/storage` apunte a `storage/app/public`.

## 5. Configurar document root

El dominio del backend debe apuntar a la carpeta `public` de Laravel.

Correcto:

```txt
/ruta-del-proyecto/public
```

Incorrecto:

```txt
/ruta-del-proyecto
```

Si tu hosting solo permite subir a `public_html`, sube el contenido de `public` dentro de `public_html` y ajusta los `require` de `index.php` para apuntar al resto del proyecto fuera de `public_html`.

## 6. Probar endpoints

Cuando el backend este online, prueba:

```txt
https://TU-DOMINIO-DEL-BACKEND.com/api/ministerios
https://TU-DOMINIO-DEL-BACKEND.com/api/paginas?per_page=50
https://TU-DOMINIO-DEL-BACKEND.com/api/eventos
```

Luego en el frontend usa:

```txt
VITE_API_URL=https://TU-DOMINIO-DEL-BACKEND.com/api
VITE_PUBLIC_ASSET_URL=https://TU-DOMINIO-DEL-BACKEND.com
```
