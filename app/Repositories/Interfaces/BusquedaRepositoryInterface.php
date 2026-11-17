<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface BusquedaRepositoryInterface
{
    public function buscar(User $usuario, string $termino): array;
}
