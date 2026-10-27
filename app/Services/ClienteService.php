<?php

namespace App\Services;

use App\Repositories\Interfaces\ClienteRepositoryInterface;

class ClienteService
{
    public function __construct(private ClienteRepositoryInterface $cliente_repository) {}

    public function listarTodo()
    {
        return $this->cliente_repository->listarTodo();
    }

    public function guardar(array $datos)
    {
        return $this->cliente_repository->guardar($datos);
    }

    public function eliminar(int $id)
    {
        return $this->cliente_repository->eliminar($id);
    }

    public function buscarPorId(int $id)
    {
        return $this->cliente_repository->buscarPorId($id);
    }

    public function actualizar(int $id, array $datos)
    {
        return $this->cliente_repository->actualizar($id, $datos);
    }
}
