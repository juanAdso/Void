<?php

namespace App\Repositories\Interfaces;

interface TiendaRepositoryInterface
{
    public function listarTodo(?int $tiendaId);
    public function eliminar(int $id, ?int $tiendaId);
    public function buscarPorId(int $id, ?int $tiendaId);
    public function actualizar(int $id, array $datos, ?int $tiendaId);
}
