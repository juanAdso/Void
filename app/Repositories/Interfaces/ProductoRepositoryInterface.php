<?php

namespace App\Repositories\Interfaces;

interface ProductoRepositoryInterface
{
    public function listarTodo(?int $tiendaId = null);

    public function guardar(array $datos);

    public function eliminar(int $id, ?int $tiendaId = null);

    public function buscarPorId(int $id, ?int $tiendaId = null);

    public function actualizar(int $id, array $datos);
}
