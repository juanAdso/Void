<?php

namespace App\Repositories\Interfaces;

interface FacturasRepositoryInterface
{
    public function listarTodo();

    public function buscarPorId(int $id);

    public function guardar(array $data);

    public function actualizar(int $id, array $data);

    public function eliminar(int $id);
}