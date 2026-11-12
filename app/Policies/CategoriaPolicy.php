<?php

namespace App\Policies;

use App\Models\Categoria;
use App\Models\User;

class CategoriaPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->id_tienda && $user->can('ver categorias');
    }

    public function view(User $user, Categoria $categoria): bool
    {
        return (bool) $user->id_tienda
            && (int) $categoria->id_tienda === (int) $user->id_tienda
            && $user->can('ver categorias');
    }

    public function create(User $user): bool
    {
        return (bool) $user->id_tienda && $user->can('crear categorias');
    }

    public function update(User $user, Categoria $categoria): bool
    {
        return (bool) $user->id_tienda
            && (int) $categoria->id_tienda === (int) $user->id_tienda
            && $user->can('editar categorias');
    }

    public function delete(User $user, Categoria $categoria): bool
    {
        return (bool) $user->id_tienda
            && (int) $categoria->id_tienda === (int) $user->id_tienda
            && $user->can('eliminar categorias');
    }

    public function restore(User $user, Categoria $categoria): bool
    {
        return $this->update($user, $categoria);
    }

    public function forceDelete(User $user, Categoria $categoria): bool
    {
        return $this->delete($user, $categoria);
    }
}
