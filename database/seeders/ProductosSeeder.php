<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Productos;
use App\Models\Tienda;
use Illuminate\Database\Seeder;
use RuntimeException;

class ProductosSeeder extends Seeder
{
    public function run(): void
    {
        if (!Categoria::exists()) {
            throw new RuntimeException('Ejecuta CategoriaSeeder antes de ProductosSeeder.');
        }

        if (!Tienda::exists()) {
            throw new RuntimeException('Ejecuta TiendaSeeder antes de ProductosSeeder.');
        }

        // Si ya hay productos, asigna tienda a los registros antiguos que aún no tengan una.
        Productos::query()
            ->whereNull('id_tienda')
            ->get()
            ->each(function (Productos $producto): void {
                $producto->id_tienda = Tienda::query()->inRandomOrder()->value('id');
                $producto->save();
            });

        if (Productos::exists()) {
            return;
        }

        Productos::factory()->count(500)->create();
    }
}
