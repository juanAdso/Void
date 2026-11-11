<?php

namespace App\Services;

use App\Repositories\Interfaces\FacturasRepositoryInterface;

class FacturasService
{
    public function __construct(
        protected FacturasRepositoryInterface $repository
    ) {}

    public function listarTodo(?int $tiendaId = null)
    {
        return $this->repository->listarTodo($tiendaId);
    }

    public function buscarPorId(int $id, ?int $tiendaId = null)
    {
        return $this->repository->buscarPorId($id, $tiendaId);
    }

    public function guardar(array $data)
    {
        return $this->repository->guardar($data);
    }

    public function crear(array $data)
    {
        return $this->repository->guardar($data);
    }

    public function actualizar(int $id, array $data)
    {
        return $this->repository->actualizar($id, $data);
    }

    public function eliminar(int $id, ?int $tiendaId = null)
    {
        return $this->repository->eliminar($id, $tiendaId);
    }
}
