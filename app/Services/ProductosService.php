<?php

namespace App\Services;

use App\Repositories\Interfaces\ProductoRepositoryInterface;

class ProductosService
{
    private ProductoRepositoryInterface $productos_repository;

    public function __construct(ProductoRepositoryInterface $productos_repository)
    {
        $this->productos_repository = $productos_repository;
    }

    public function listarTodo(?int $tiendaId = null)
    {
        return $this->productos_repository->listarTodo($tiendaId);
    }

    public function guardar(array $datos)
    {
        return $this->productos_repository->guardar($datos);
    }

    public function eliminar(int $id, ?int $tiendaId = null)
    {
        return $this->productos_repository->eliminar($id, $tiendaId);
    }

    public function buscarPorId(int $id, ?int $tiendaId = null)
    {
        $producto = $this->productos_repository->buscarPorId($id, $tiendaId);

        return $producto;
    }

    public function actualizar(int $id, array $datos)
    {
        $this->productos_repository->actualizar($id, $datos);
    }
}
