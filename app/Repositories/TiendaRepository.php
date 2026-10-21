<?php

namespace App\Repositories;

use App\Models\Tienda;
use App\Repositories\Interfaces\TiendaRepositoryInterface;

class TiendaRepository implements TiendaRepositoryInterface
{
    public function all()
    {
        return Tienda::all();
    }

    public function find($id)
    {
        return Tienda::findOrFail($id);
    }

    public function create(array $data)
    {
        return Tienda::create($data);
    }

    public function update($id, array $data)
    {
        $registro = Tienda::findOrFail($id);

        $registro->update($data);

        return $registro;
    }

    public function delete($id)
    {
        $registro = Tienda::findOrFail($id);

        return $registro->delete();
    }
}