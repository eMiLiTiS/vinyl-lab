# Vinyl Lab

Proyecto web (PHP + MySQL/MariaDB) para gestionar y visualizar un catálogo de vinilos.

## Requisitos

- XAMPP / WAMP / LAMP
- PHP 8.x
- MariaDB/MySQL
- (Opcional) phpMyAdmin / Adminer

## Estructura del proyecto

- `public/` → assets públicos (css, imágenes)
- `uploads/` → imágenes subidas
- `src/pages/` → páginas públicas (index, catálogo, login, logout)
- `src/admin/` → panel admin (CRUD vinilos)
- `src/config/` → configuración
- `src/database/` → conexión a BD
- `sql/` → scripts de base de datos
- `adminer.php` → acceso rápido a BD (opcional)

## Instalación (local)

1. Copia el proyecto dentro de:
   - `C:\xampp\htdocs\vinyl-lab` (Windows)
   - `/var/www/html/vinyl-lab` (Linux)

2. Crea la base de datos:
   - Nombre: `vinyl_lab`

3. Importa el SQL:
   - Archivo: `sql/vinyl_lab.sql`

4. Configura la conexión:
   - Archivo: `src/config/config.php`
   - Ajusta host, user, password y dbname según tu entorno.

5. Abre en el navegador:
   - Página principal: `/vinyl-lab/index.php`
   - Login: `/vinyl-lab/src/pages/login.html`
   - Adminer (opcional): `/vinyl-lab/adminer.php`

## Usuarios de prueba

El SQL incluye usuarios seed:

- `admin` / `admin123`
- `iker` / `123`

> Las contraseñas están almacenadas con `password_hash()` y se verifican con `password_verify()`.

## Funcionalidades

- Login / logout
- Catálogo público de vinilos
- Panel admin:
  - añadir vinilo
  - editar/guardar vinilo
  - buscar vinilos
  - activar/desactivar visibilidad
  - eliminar vinilo
