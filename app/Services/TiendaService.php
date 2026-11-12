<?php

namespace App\Services;

use App\Repositories\Interfaces\TiendaRepositoryInterface;

class TiendaService
{
    public function __construct(private TiendaRepositoryInterface $tienda_repository) {}

    public function listarTodo(int $tiendaId)
    {
        return $this->tienda_repository->listarTodo($tiendaId);
    }

    public function eliminar(int $id, int $tiendaId)
    {
        return $this->tienda_repository->eliminar($id, $tiendaId);
    }

    public function buscarPorId(int $id, int $tiendaId)
    {
        return $this->tienda_repository->buscarPorId($id, $tiendaId);
    }

    public function actualizar(int $id, array $datos, int $tiendaId)
    {
        return $this->tienda_repository->actualizar($id, $datos, $tiendaId);
    }
}
