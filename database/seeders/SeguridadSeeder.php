<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SeguridadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar la caché de permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Definición exacta de permisos requeridos
        $permisos = [
            // Dashboard
            'ver dashboard',

            // Tiendas
            'ver tiendas',
            'crear tiendas',
            'editar tiendas',
            'eliminar tiendas',

            // Categorías
            'ver categorias',
            'crear categorias',
            'editar categorias',
            'eliminar categorias',

            // Productos
            'ver productos',
            'crear productos',
            'editar productos',
            'eliminar productos',

            // Clientes
            'ver clientes',
            'crear clientes',
            'editar clientes',
            'eliminar clientes',

            // Facturas
            'ver facturas',
            'crear facturas',
            'editar facturas',
            'eliminar facturas',

            // Detalle de facturas
            'ver detallefacturas',
            'crear detallefacturas',
            'editar detallefacturas',
            'eliminar detallefacturas',

            // Seguridad
            'ver seguridad',
            'gestionar seguridad',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        // 2. Crear roles
        $rolAdmin = Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);
        $rolEmpleado = Role::firstOrCreate(['name' => 'empleado', 'guard_name' => 'web']);

        // 3. Asignación de permisos al Administrador: Todos los permisos
        $rolAdmin->syncPermissions(Permission::all());

        // Sincronizar los roles de cuentas históricas con el campo rol del sistema.
        // Esto no concede acceso a cuentas sin tienda: el middleware las bloquea.
        User::query()
            ->whereIn('rol', ['administrador', 'empleado'])
            ->each(function (User $user) {
                $user->syncRoles([$user->rol]);
            });

        // 4. Asignación de permisos al Empleado
        $permisosEmpleado = [
            'ver dashboard',
            'ver categorias',
            'ver productos',
            'ver clientes',
            'ver facturas',
            'crear facturas',
            'ver detallefacturas',
            'crear detallefacturas',
        ];

        $rolEmpleado->syncPermissions($permisosEmpleado);
    }
}
