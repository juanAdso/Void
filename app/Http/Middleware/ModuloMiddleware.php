<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModuloMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$permisos
     */
    public function handle(Request $request, Closure $next, ...$permisos): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        if (method_exists($user, 'estaActivo') && ! $user->estaActivo()) {
            abort(403, 'Tu cuenta de usuario no está activa.');
        }

        // Si no se pasaron permisos específicos, solo se requería estar autenticado y activo
        if (empty($permisos)) {
            return $next($request);
        }

        // Si tiene el rol administrador, tiene acceso completo
        if (method_exists($user, 'hasRole') && $user->hasRole('administrador')) {
            return $next($request);
        }

        // Comprueba si el usuario tiene alguno de los permisos especificados
        foreach ($permisos as $permiso) {
            if ($user->can($permiso)) {
                return $next($request);
            }
        }

        abort(403, 'No tienes autorización para acceder a este módulo u operación.');
    }
}
