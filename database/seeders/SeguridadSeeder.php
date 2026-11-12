<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Tienda;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class SeguridadSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permisos = [
            'ver dashboard',
            'ver tiendas', 'crear tiendas', 'editar tiendas', 'eliminar tiendas',
            'ver categorias', 'crear categorias', 'editar categorias', 'eliminar categorias',
            'ver productos', 'crear productos', 'editar productos', 'eliminar productos',
            'ver clientes', 'crear clientes', 'editar clientes', 'eliminar clientes',
            'ver facturas', 'crear facturas', 'editar facturas', 'eliminar facturas',
            'ver detallefacturas', 'crear detallefacturas', 'editar detallefacturas', 'eliminar detallefacturas',
            'ver seguridad', 'gestionar seguridad',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);

        // Un único rol global reservado al superadministrador.
        $registrar->setPermissionsTeamId(null);
        $superadministrador = Role::query()->firstOrCreate([
            'name' => 'superadministrador',
            'guard_name' => 'web',
            'id_tienda' => null,
        ]);
        $superadministrador->syncPermissions(Permission::query()->where('guard_name', 'web')->get());

        // Los roles normales existen por tienda, aunque compartan nombre y catálogo
        // de permisos. Sus asignaciones no se mezclan entre empresas.
        foreach (Tienda::query()->orderBy('id')->get() as $tienda) {
            $registrar->setPermissionsTeamId((int) $tienda->id);

            $rolAdmin = Role::query()->firstOrCreate([
                'name' => 'administrador',
                'guard_name' => 'web',
                'id_tienda' => $tienda->id,
            ]);
            $rolAdmin->syncPermissions(Permission::query()->where('guard_name', 'web')->get());

            $rolEmpleado = Role::query()->firstOrCreate([
                'name' => 'empleado',
                'guard_name' => 'web',
                'id_tienda' => $tienda->id,
            ]);
            $rolEmpleado->syncPermissions([
                'ver dashboard',
                'ver categorias',
                'ver productos',
                'ver clientes',
                'ver facturas',
                'crear facturas',
                'ver detallefacturas',
                'crear detallefacturas',
            ]);

            User::query()
                ->where('id_tienda', $tienda->id)
                ->whereIn('rol', ['administrador', 'empleado'])
                ->each(function (User $usuario) {
                    $usuario->unsetRelation('roles');
                    $usuario->syncRoles([$usuario->rol]);
                });
        }

        // Nunca asignar roles globales a cuentas normales ni cuentas sin tienda.
        $registrar->setPermissionsTeamId(null);
        $registrar->forgetCachedPermissions();
    }
}
