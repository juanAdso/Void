<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface BusquedaRepositoryInterface
{
    /**
     * Recupera los registros que el usuario puede consultar dentro de su tienda.
     *
     * @return array<string, \Illuminate\Support\Collection>
     */
    public function buscar(User $usuario, string $termino): array;
}
