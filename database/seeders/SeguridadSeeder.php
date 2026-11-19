<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SeguridadSeeder extends Seeder
{
    public function run(): void
    {

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permisos = [

            'ver dashboard',

            'ver tiendas',
            'crear tiendas',
            'editar tiendas',
            'eliminar tiendas',

            'ver categorias',
            'crear categorias',
            'editar categorias',
            'eliminar categorias',

            'ver productos',
            'crear productos',
            'editar productos',
            'eliminar productos',

            'ver clientes',
            'crear clientes',
            'editar clientes',
            'eliminar clientes',

            'ver facturas',
            'crear facturas',
            'editar facturas',
            'eliminar facturas',

            'ver detallefacturas',
            'crear detallefacturas',
            'editar detallefacturas',
            'eliminar detallefacturas',

            'ver seguridad',
            'gestionar seguridad',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        $rolAdmin = Role::firstOrCreate(['name' => 'administrador', 'guard_name' => 'web']);
        $rolEmpleado = Role::firstOrCreate(['name' => 'empleado', 'guard_name' => 'web']);

        Role::firstOrCreate(['name' => 'superadministrador', 'guard_name' => 'web']);

        $rolAdmin->syncPermissions(Permission::all());

        User::query()
            ->whereIn('rol', ['administrador', 'empleado'])
            ->each(function (User $user) {
                $user->syncRoles([$user->rol]);
            });

        $permisosEmpleado = [
            'ver dashboard',
            'ver categorias',
            'ver productos',
            'ver clientes',
            'crear clientes',
            'editar clientes',
            'ver facturas',
            'crear facturas',
            'ver detallefacturas',
            'crear detallefacturas',
        ];

        $rolEmpleado->syncPermissions($permisosEmpleado);
    }
}
