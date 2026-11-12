<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class SeguridadServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Solo se aplica el bloqueo global de cuentas inactivas.
        // Los administradores no reciben un bypass automático de Policies o permisos Spatie.
        Gate::before(function ($user) {
            if (method_exists($user, 'estaActivo') && ! $user->estaActivo()) {
                return false;
            }

            return null;
        });
    }
}
