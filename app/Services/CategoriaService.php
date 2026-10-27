<?php

namespace App\Services;

use App\Repositories\Interfaces\CategoriaRepositoryInterface;

class CategoriaService
{
    public function __construct(private CategoriaRepositoryInterface $categoria_repository) {}

    public function listarTodo()
    {
        return $this->categoria_repository->listarTodo();
    }

    public function guardar(array $datos)
    {
        return $this->categoria_repository->guardar($datos);
    }

    public function eliminar(int $id)
    {
        return $this->categoria_repository->eliminar($id);
    }

    public function buscarPorId(int $id)
    {
        return $this->categoria_repository->buscarPorId($id);
    }

    public function actualizar(int $id, array $datos)
    {
        return $this->categoria_repository->actualizar($id, $datos);
    }
}
