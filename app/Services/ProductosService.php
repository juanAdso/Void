<?php

namespace App\Services;

use App\Repositories\Interfaces\ProductoRepositoryInterface;

class ProductosService
{
    public function __construct(private ProductoRepositoryInterface $productos_repository) {}

    public function listarTodo(int $tiendaId)
    {
        return $this->productos_repository->listarTodo($tiendaId);
    }

    public function guardar(array $datos, int $tiendaId)
    {
        return $this->productos_repository->guardar($datos, $tiendaId);
    }

    public function eliminar(int $id, int $tiendaId)
    {
        return $this->productos_repository->eliminar($id, $tiendaId);
    }

    public function buscarPorId(int $id, int $tiendaId)
    {
        return $this->productos_repository->buscarPorId($id, $tiendaId);
    }

    public function actualizar(int $id, array $datos, int $tiendaId)
    {
        return $this->productos_repository->actualizar($id, $datos, $tiendaId);
    }
}
