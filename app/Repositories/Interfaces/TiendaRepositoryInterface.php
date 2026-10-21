<?php

namespace App\Repositories\Interfaces;

interface TiendaRepositoryInterface
{
    public function listarTodo();
    public function guardar(array $datos);
    public function eliminar(int $id);
    public function buscarPorId(int $id);
    public function actualizar(int $id, array $datos);
}
