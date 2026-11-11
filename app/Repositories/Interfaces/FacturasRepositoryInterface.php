<?php

namespace App\Repositories\Interfaces;

interface FacturasRepositoryInterface
{
    public function listarTodo(?int $tiendaId = null);

    public function buscarPorId(int $id, ?int $tiendaId = null);

    public function guardar(array $data);

    public function actualizar(int $id, array $data);

    public function eliminar(int $id, ?int $tiendaId = null);
}
