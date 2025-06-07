# Instrucciones para instalar y ejecutar el sistema

## 1. Requisitos
- PHP >= 7.4
- MySQL/MariaDB
- Composer

## 2. Instalación
1. Clona o descarga el repositorio en tu servidor local.
2. Crea una base de datos llamada, por ejemplo, `inventario_facturacion`.
3. Importa el archivo `database.sql` en tu base de datos.
4. Copia y configura `config/db.php` con tus datos de conexión.
5. Da permisos de escritura a la carpeta `/uploads`.
6. Instala dependencias para código de barras:
   ```
   composer require picqer/php-barcode-generator
   ```
7. Accede a `register_empresa.php` para crear la primera empresa y usuario administrador.

## 3. Uso
- Ingresa con el usuario administrador creado para cargar productos, usuarios, etc.
- El usuario superadmin puede administrar empresas y roles.
- El usuario admin solo gestiona su empresa.

## 4. Seguridad
- Cambia las contraseñas por defecto tras la instalación.
- No expongas el sistema a internet sin protegerlo.