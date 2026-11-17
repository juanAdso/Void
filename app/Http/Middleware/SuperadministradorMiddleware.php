<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperadministradorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        abort_unless(
            $usuario
                && $usuario->estaActivo()
                && $usuario->rol === 'superadministrador'
                && $usuario->hasRole('superadministrador')
                && $usuario->id_tienda === null,
            403,
            'No tienes autorización para acceder al centro de administración global de VOID.'
        );

        return $next($request);
    }
}
