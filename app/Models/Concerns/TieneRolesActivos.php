<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Collection;

trait TieneRolesActivos
{
    public function estaActivo(): bool
    {
        return (bool) $this->estado;
    }

    public function getRolesActivos(): Collection
    {
        if (! $this->estaActivo()) {
            return new Collection;
        }

        return $this->roles;
    }

    public function tieneRolActivo(string|array $roles): bool
    {
        if (! $this->estaActivo()) {
            return false;
        }

        return $this->hasRole($roles);
    }

    /**
     * Impide que una cuenta desactivada conserve permisos efectivos por sus roles.
     */
    public function tienePermisoActivo(string $permiso): bool
    {
        if (! $this->estaActivo()) {
            return false;
        }

        return $this->hasPermissionTo($permiso);
    }

    public function tieneAlgunPermisoActivo(array|string ...$permisos): bool
    {
        if (! $this->estaActivo()) {
            return false;
        }

        return $this->hasAnyPermission(...$permisos);
    }
}
