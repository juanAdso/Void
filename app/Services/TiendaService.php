<?php

namespace App\Services;

use App\Repositories\Interfaces\TiendaRepositoryInterface;

class TiendaService
{
    private TiendaRepositoryInterface $tienda_repository;

    public function __construct(TiendaRepositoryInterface $tienda_repository)
    {
        $this->tienda_repository = $tienda_repository;
    }

    public function listarTodo()
    {
        return $this->tienda_repository->listarTodo();
    }

    public function guardar(array $datos)
    {
        return $this->tienda_repository->guardar($datos);
    }

    public function eliminar(int $id)
    {
        return $this->tienda_repository->eliminar($id);
    }

    public function buscarPorId(int $id)
    {
        return $this->tienda_repository->buscarPorId($id);
    }

    public function actualizar(int $id, array $datos)
    {
        return $this->tienda_repository->actualizar($id, $datos);
    }
}
