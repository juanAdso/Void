<?php 

namespace App\Services;

use App\Repositories\ClienteRepository;

class ClienteService
{
    private ClienteRepository $cliente_repository;
    public function __construct(ClienteRepository $cliente_repository)
    {
        $this->cliente_repository = $cliente_repository;
    }

    public function listarTodo()
    {
        return $this->cliente_repository->listarTodo();
    }

    public function guardar(array $datos)
    {
        $this->cliente_repository->guardar($datos);
    }

    public function eliminar(int $id)
    {
        $this->cliente_repository->eliminar($id); 
    }

    public function buscarPorId(int $id)
    {
        $cliente = $this->cliente_repository->buscarPorId($id);
        return $cliente ;
    }

    public function actualizar(int $id, array $datos)
    {
        $this->cliente_repository->actualizar($id, $datos);
    }
}      