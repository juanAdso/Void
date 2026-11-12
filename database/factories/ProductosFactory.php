<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Productos;
use App\Models\Tienda;
use Illuminate\Database\Eloquent\Factories\Factory;
use RuntimeException;

/**
 * @extends Factory<Productos>
 */
class ProductosFactory extends Factory
{
    protected $model = Productos::class;

    public function definition(): array
    {
        return [
            'id_tienda' => function () {
                $tiendaId = Tienda::query()->inRandomOrder()->value('id');

                if (! $tiendaId) {
                    throw new RuntimeException('Debes crear tiendas antes de generar productos.');
                }

                return $tiendaId;
            },
            'id_categoria' => function (array $attributes) {
                $categoriaId = Categoria::query()
                    ->where('id_tienda', $attributes['id_tienda'] ?? null)
                    ->inRandomOrder()
                    ->value('id');

                if (! $categoriaId) {
                    throw new RuntimeException('Debes crear categorías para la tienda del producto antes de generar productos.');
                }

                return $categoriaId;
            },
            'nombre' => fake()->unique()->words(3, true),
            'descripcion' => fake()->sentence(),
            'precio' => fake()->randomFloat(2, 10000, 500000),
            'stock' => fake()->numberBetween(0, 100),
            'imagen' => null,
            'estado' => true,
        ];
    }
}
