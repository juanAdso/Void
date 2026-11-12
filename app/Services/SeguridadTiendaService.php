<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class SeguridadTiendaService
{
    private const PERMISOS_EMPLEADO = [
        'ver dashboard',
        'ver categorias',
        'ver productos',
        'ver clientes',
        'ver facturas',
        'crear facturas',
        'ver detallefacturas',
        'crear detallefacturas',
    ];

    public function crearRolesIniciales(int $tiendaId): void
    {
        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);
        $registrar->setPermissionsTeamId($tiendaId);

        try {
            $permisos = Permission::query()->where('guard_name', 'web')->get();

            $administrador = Role::query()->firstOrCreate([
                'name' => 'administrador',
                'guard_name' => 'web',
                'id_tienda' => $tiendaId,
            ]);
            $administrador->syncPermissions($permisos);

            $empleado = Role::query()->firstOrCreate([
                'name' => 'empleado',
                'guard_name' => 'web',
                'id_tienda' => $tiendaId,
            ]);
            $empleado->syncPermissions(self::PERMISOS_EMPLEADO);
        } finally {
            $registrar->setPermissionsTeamId(0);
        }
    }

    public function asignarRol(User $usuario, string $rol): void
    {
        if (! $usuario->id_tienda || ! in_array($rol, ['administrador', 'empleado'], true)) {
            throw new \InvalidArgumentException('La asignación requiere un usuario y un rol válido de una tienda.');
        }

        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);
        $registrar->setPermissionsTeamId((int) $usuario->id_tienda);

        try {
            $usuario->unsetRelation('roles');
            $usuario->syncRoles([$rol]);
        } finally {
            $registrar->setPermissionsTeamId(null);
        }
    }
}
