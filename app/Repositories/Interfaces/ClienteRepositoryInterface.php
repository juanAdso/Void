<?php

namespace App\Repositories\Interfaces;

interface ClienteRepositoryInterface
{
    public function listarTodo();
    public function guardar(array $datos);
    public function eliminar(int $id);
    public function buscarPorId(int $id);
    public function actualizar(int $id, array $datos);
}