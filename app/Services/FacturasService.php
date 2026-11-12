<?php

namespace App\Services;

use App\Repositories\Interfaces\FacturasRepositoryInterface;

class FacturasService
{
    public function __construct(protected FacturasRepositoryInterface $repository) {}

    public function listarTodo(int $tiendaId)
    {
        return $this->repository->listarTodo($tiendaId);
    }

    public function buscarPorId(int $id, int $tiendaId)
    {
        return $this->repository->buscarPorId($id, $tiendaId);
    }

    public function guardar(array $data, int $tiendaId)
    {
        return $this->repository->guardar($data, $tiendaId);
    }

    public function crear(array $data, int $tiendaId)
    {
        return $this->repository->guardar($data, $tiendaId);
    }

    public function actualizar(int $id, array $data, int $tiendaId)
    {
        return $this->repository->actualizar($id, $data, $tiendaId);
    }

    public function eliminar(int $id, int $tiendaId)
    {
        return $this->repository->eliminar($id, $tiendaId);
    }
}
