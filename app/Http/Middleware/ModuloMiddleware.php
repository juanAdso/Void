<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class ModuloMiddleware
{
    public function handle(Request $request, Closure $next, ...$permisos): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        if (! $user->estaActivo()) {
            abort(403, 'Tu cuenta de usuario no está activa.');
        }

        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);

        // El superadministrador utiliza el contexto global null.
        $registrar->setPermissionsTeamId(null);
        $user->unsetRelation('roles');
        $user->unsetRelation('permissions');

        $esSuperadministrador = $user->esSuperadministrador();

        if ($esSuperadministrador) {
            $registrar->setPermissionsTeamId(null);
        } else {
            $tiendaId = (int) ($user->id_tienda ?? 0);

            if ($tiendaId <= 0) {
                abort(403, 'Tu cuenta no tiene una tienda asignada. No se permite acceso global.');
            }

            $registrar->setPermissionsTeamId($tiendaId);
            $user->unsetRelation('roles');
            $user->unsetRelation('permissions');

            $tienda = $user->tienda;

            if (! $tienda || ! $tienda->estado) {
                abort(403, 'La tienda asociada a tu cuenta está inactiva o no existe.');
            }

            if (! $user->hasAnyRole(['administrador', 'empleado'])) {
                abort(403, 'Tu cuenta no tiene un rol administrativo válido.');
            }
        }

        if (empty($permisos)) {
            return $next($request);
        }

        foreach ($permisos as $permiso) {
            if ($user->can($permiso)) {
                return $next($request);
            }
        }

        abort(403, 'No tienes autorización para acceder a este módulo u operación.');
    }
}
