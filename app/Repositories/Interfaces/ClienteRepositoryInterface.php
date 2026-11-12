<?php

namespace App\Repositories\Interfaces;

interface ClienteRepositoryInterface
{
    public function listarTodo(?int $tiendaId);
    public function guardar(array $datos, int $tiendaId);
    public function eliminar(int $id, ?int $tiendaId);
    public function buscarPorId(int $id, ?int $tiendaId);
    public function actualizar(int $id, array $datos, ?int $tiendaId);
}
