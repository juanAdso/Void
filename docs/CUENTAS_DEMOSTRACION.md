# Cuentas de demostración de VOID

Estas cuentas son datos persistentes creados por los seeders para navegar por la interfaz de cada rol. No son usuarios temporales de PHPUnit.

## Empresas y cuentas

| Empresa | Rol | Correo de acceso | Contraseña inicial |
|---|---|---|---|
| Comercial Horizonte S.A.S. | Administrador | admin.horizonte@void.test | VoidDemo2026! |
| Comercial Horizonte S.A.S. | Empleado | empleado.horizonte@void.test | VoidDemo2026! |
| Tecnología Nova S.A.S. | Administrador | admin.nova@void.test | VoidDemo2026! |
| Tecnología Nova S.A.S. | Empleado | empleado.nova@void.test | VoidDemo2026! |

Cada empresa tiene sus propias categorías, productos, clientes, facturas y movimientos de inventario. Los dos empleados pertenecen a empresas distintas y sus permisos se aplican dentro de su propia tienda.

Las cuentas se crean con `firstOrCreate`: volver a ejecutar los seeders no duplica cuentas ni restablece contraseñas que ya hayan sido cambiadas.

## Crear la cuenta global superadministradora

La cuenta global no usa una contraseña pública ni se crea desde el registro de empresas. Configura estos valores privados en el archivo `.env`:

```dotenv
VOID_SUPERADMIN_NOMBRE="Nombre"
VOID_SUPERADMIN_APELLIDO="Apellido"
VOID_SUPERADMIN_DOCUMENTO="DOCUMENTO_EXCLUSIVO"
VOID_SUPERADMIN_CORREO="correo-global@tudominio.com"
VOID_SUPERADMIN_PASSWORD="Una contraseña única de al menos 12 caracteres"
```

Después ejecuta:

```bash
php artisan db:seed --class=SeguridadSeeder
php artisan db:seed --class=SuperadministradorSeeder
php artisan optimize:clear
```

También puedes ejecutar `php artisan db:seed`, que ejecuta ambos seeders en el orden correcto junto con los datos demostrativos.

La cuenta global se crea sin `id_tienda`, tiene el rol `superadministrador` y entra al centro de control de VOID. Si el correo ya pertenece a una cuenta de empresa, el seeder se detiene para evitar convertirla accidentalmente en una cuenta global.

## Seeders y producción

- En entornos local y de desarrollo, `php artisan db:seed` crea las dos empresas y sus cuentas con datos comerciales de exposición.
- Los seeders son repetibles y no ejecutan las antiguas fábricas masivas de 500 clientes, 500 productos o 300 facturas.
- En producción, las empresas y cuentas de demostración se omiten por defecto. No actives `VOID_DEMO_DATA=true` en producción real.
- Cambia las contraseñas de las cuentas de demostración antes de publicar el sistema y no utilices esas cuentas como cuentas reales de clientes.
- Nunca publiques el archivo `.env` ni compartas la contraseña configurada para el superadministrador.
