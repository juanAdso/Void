<?php

namespace App\Services;

use App\Repositories\Interfaces\ClienteRepositoryInterface;

class ClienteService
{
    public function __construct(private ClienteRepositoryInterface $cliente_repository) {}

    public function listarTodo(int $tiendaId)
    {
        return $this->cliente_repository->listarTodo($tiendaId);
    }

    public function guardar(array $datos, int $tiendaId)
    {
        return $this->cliente_repository->guardar($datos, $tiendaId);
    }

    public function eliminar(int $id, int $tiendaId)
    {
        return $this->cliente_repository->eliminar($id, $tiendaId);
    }

    public function buscarPorId(int $id, int $tiendaId)
    {
        return $this->cliente_repository->buscarPorId($id, $tiendaId);
    }

    public function actualizar(int $id, array $datos, int $tiendaId)
    {
        return $this->cliente_repository->actualizar($id, $datos, $tiendaId);
    }
}
