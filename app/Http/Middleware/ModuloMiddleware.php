<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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

        // Todo usuario de los módulos administrativos debe pertenecer a una tienda.
        // Un id_tienda nulo nunca debe convertirse en permiso para consultar datos globales.
        if (! $user->id_tienda) {
            abort(403, 'Tu cuenta no tiene una tienda asignada. Contacta al administrador.');
        }

        if (! $user->hasAnyRole(['administrador', 'empleado'])) {
            abort(403, 'Tu cuenta no tiene un rol administrativo válido.');
        }

        foreach ($permisos as $permiso) {
            if ($user->can($permiso)) {
                return $next($request);
            }
        }

        if (empty($permisos)) {
            return $next($request);
        }

        abort(403, 'No tienes autorización para acceder a este módulo u operación.');
    }
}
