<?php

namespace App\Services;

use App\Repositories\Interfaces\TiendaRepositoryInterface;

class TiendaService
{
    public function __construct(
        protected TiendaRepositoryInterface $repository
    ) {}

    public function listar()
    {
        return $this->repository->all();
    }

    public function buscar($id)
    {
        return $this->repository->find($id);
    }

    public function crear(array $data)
    {
        return $this->repository->create($data);
    }

    public function actualizar($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function eliminar($id)
    {
        return $this->repository->delete($id);
    }
}