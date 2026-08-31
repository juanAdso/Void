<?php 

namespace App\Services;

use App\Repositories\CategoriaRepository;

class CategoriaService
{
    private CategoriaRepository $categoria_repository;
    public function __construct(CategoriaRepository $categoria_repository)
    {
        $this->categoria_repository = $categoria_repository;
    }

    public function listarTodo()
    {
        return $this->categoria_repository->listarTodo();
    }

    public function guardar(array $datos)
    {
        $this->categoria_repository->guardar($datos);
    }

    public function eliminar(int $id)
    {
        $this->categoria_repository->eliminar($id); 
    }

    public function buscarPorId(int $id)
    {
        $categoria = $this->categoria_repository->buscarPorId($id);
        return $categoria;
    }

    public function actualizar(int $id, array $datos)
    {
        $this->categoria_repository->actualizar($id, $datos);
    }
}          