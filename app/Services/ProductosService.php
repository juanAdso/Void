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

    public function listarTodo()
    {
        return $this->productos_repository->listarTodo();
    }

    public function guardar(array $datos)
    {
        $this->productos_repository->guardar($datos);
    }

    public function eliminar(int $id)
    {
        return $this->productos_repository->eliminar($id);
    }

    public function buscarPorId(int $id)
    {
        $producto = $this->productos_repository->buscarPorId($id);
        return $producto;
    }

    public function actualizar(int $id, array $datos)
    {
        $this->productos_repository->actualizar($id, $datos);
    }
}
