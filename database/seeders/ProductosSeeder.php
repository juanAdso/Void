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
        if (!Categoria::exists()) {
            throw new RuntimeException('Ejecuta CategoriaSeeder antes de ProductosSeeder.');
        }

        if (Productos::exists()) {
            return;
        }

        Productos::factory()->count(500)->create();
    }
}
