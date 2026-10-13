<?php

namespace App\Services;

use App\Repositories\Interfaces\FacturasRepositoryInterface;

class FacturasService
{
    public function __construct(
        protected FacturasRepositoryInterface $repository
    ) {}

    public function listarTodo()
    {
        return $this->repository->listarTodo();
    }

    public function buscarPorId(int $id)
    {
        return $this->repository->buscarPorId($id);
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

    public function eliminar(int $id)
    {
        return $this->repository->eliminar($id);
    }
}