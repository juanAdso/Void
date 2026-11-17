# VOID

VOID (Virtual Organization Inventory and Design) es una aplicación web multiempresa para publicar catálogos y administrar productos, inventario, clientes, empleados, facturas y reportes.

## Requisitos

- PHP 8.4.1 o superior y extensiones requeridas por Laravel.
- Composer.
- Node.js 22 o superior y npm.
- MySQL/MariaDB o SQLite.
- Una cuenta de correo SMTP para enviar códigos de recuperación.

## Instalación local

1. Clona el repositorio e instala las dependencias:

    ```bash
    composer install
    npm ci
    ```

2. Crea el archivo de entorno. En PowerShell:

    ```powershell
    Copy-Item .env.example .env
    ```

3. Genera la clave de la aplicación:

    ```bash
    php artisan key:generate
    ```

4. Configura la base de datos en `.env`. El ejemplo utiliza MySQL con una base de datos llamada `void`; créala antes de migrar. También puedes configurar SQLite cambiando `DB_CONNECTION` y `DB_DATABASE`.

5. Ejecuta las migraciones y los seeders:

    ```bash
    php artisan migrate --seed
    ```

6. Configura el correo SMTP como se explica en la sección correspondiente y compila los recursos:

    ```bash
    npm run build
    ```

7. Inicia el servidor local:

    ```bash
    php artisan serve
    ```

No ejecutes `php artisan migrate:fresh --seed` en una base de datos que contenga información que necesites conservar: elimina todas las tablas antes de recrearlas.

## Funciones principales

- Portal público con directorio de tiendas, catálogos independientes, perfiles de tienda y fichas de producto.
- Registro público de tiendas con creación de su cuenta administradora.
- Paneles diferenciados para administrador y empleado, y panel global de superadministración.
- Aislamiento de datos por tienda mediante `id_tienda`, validaciones, políticas y middleware.
- Gestión de categorías, productos, clientes y empleados.
- Carga y administración de imágenes y logos.
- Inventario con movimientos auditables, ajustes manuales, entradas, ventas, devoluciones y anulaciones.
- Facturación con detalles, control de existencias y cálculo del IVA.
- Reportes comerciales e inventario; el acceso a los reportes completos corresponde al administrador.
- Búsqueda global con resultados limitados por permisos y tienda.
- Recuperación de contraseña mediante código de seis dígitos con caducidad y límites de intentos.

## Roles

- **Superadministrador:** administra globalmente las empresas y las cuentas desde VOID.
- **Administrador:** gestiona la información, los empleados, inventario, facturación y reportes de su tienda.
- **Empleado:** consulta los módulos permitidos y registra operaciones de venta, sin acceso a la administración de seguridad ni a los reportes generales.

Los clientes son registros comerciales asociados a una tienda, no roles de acceso.

## Cuentas de demostración

En entornos distintos de producción, los seeders crean dos tiendas de ejemplo y estas cuentas:

| Rol                        | Correo                         |
| -------------------------- | ------------------------------ |
| Administrador de Horizonte | `admin.horizonte@void.test`    |
| Empleado de Horizonte      | `empleado.horizonte@void.test` |
| Administrador de Nova      | `admin.nova@void.test`         |
| Empleado de Nova           | `empleado.nova@void.test`      |

La contraseña predeterminada de demostración es `VoidDemo2026!`. Puede modificarse mediante `VOID_DEMO_PASSWORD`. Estas credenciales son exclusivamente para desarrollo local; no habilites datos de demostración ni conserves estas contraseñas en producción.

Para crear la cuenta global, configura `VOID_SUPERADMIN_NOMBRE`, `VOID_SUPERADMIN_APELLIDO`, `VOID_SUPERADMIN_DOCUMENTO`, `VOID_SUPERADMIN_CORREO` y `VOID_SUPERADMIN_PASSWORD` en `.env`. La contraseña debe tener al menos 12 caracteres. Después ejecuta:

```bash
php artisan db:seed --class=SuperadministradorSeeder
```

## Correo de recuperación

La aplicación usa SMTP para enviar códigos. El archivo `.env.example` incluye valores de referencia para Gmail; configura credenciales propias en el archivo local `.env`:

- `MAIL_MAILER=smtp`
- `MAIL_HOST=smtp.gmail.com`
- `MAIL_PORT=587`
- `MAIL_USERNAME`: dirección remitente.
- `MAIL_PASSWORD`: contraseña de aplicación SMTP.
- `MAIL_FROM_ADDRESS`: dirección remitente autorizada.
- `MAIL_FROM_NAME=VOID`

Para Gmail debes utilizar una contraseña de aplicación, no la contraseña normal de la cuenta. El puerto 587 utiliza STARTTLS. Como alternativa, puedes configurar `MAIL_SCHEME=smtps` y `MAIL_PORT=465`.

Después de cambiar el entorno ejecuta:

```bash
php artisan optimize:clear
```

No compartas ni subas el archivo `.env`, contraseñas de aplicación, claves ni credenciales SMTP. El transporte `log` sirve para desarrollo y no entrega correos a las bandejas de entrada.

## Pruebas y calidad

Ejecuta la suite completa:

```bash
php artisan test
```

Formatea los archivos PHP con Laravel Pint:

```bash
composer format
```

Comprueba el formato sin modificar archivos:

```bash
composer format:check
```

Compila los recursos del frontend:

```bash
npm run build
```
