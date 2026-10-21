<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Productos;
use Illuminate\Database\Seeder;
use RuntimeException;

class ProductosSeeder extends Seeder
{
    public function run(): void
    {
        if (Productos::exists()) {
            return;
        }

        if (!Categoria::exists()) {
            throw new RuntimeException(
                'Debes crear las categorías antes de generar productos.'
            );
        }

        Productos::factory()->count(500)->create();
    }
}

