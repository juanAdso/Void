<?php

namespace App\Services;

use App\Repositories\Interfaces\CategoriaRepositoryInterface;

class CategoriaService
{
    public function __construct(private CategoriaRepositoryInterface $categoria_repository) {}

    public function listarTodo(int $tiendaId)
    {
        return $this->categoria_repository->listarTodo($tiendaId);
    }

    public function guardar(array $datos, int $tiendaId)
    {
        return $this->categoria_repository->guardar($datos, $tiendaId);
    }

    public function eliminar(int $id, int $tiendaId)
    {
        return $this->categoria_repository->eliminar($id, $tiendaId);
    }

    public function buscarPorId(int $id, int $tiendaId)
    {
        return $this->categoria_repository->buscarPorId($id, $tiendaId);
    }

    public function actualizar(int $id, array $datos, int $tiendaId)
    {
        return $this->categoria_repository->actualizar($id, $datos, $tiendaId);
    }
}
