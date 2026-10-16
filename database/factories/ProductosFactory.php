<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Productos;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Productos>
 */
class ProductosFactory extends Factory
{
    protected $model = Productos::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->word(),
            'descripcion' => fake()->sentence(),
            'precio' => fake()->randomFloat(2, 10, 1000),
            'stock' => fake()->numberBetween(1, 100),
            'imagen' => fake()->imageUrl(),
            'estado' => true,
            'id_categoria' => Categoria::factory(),
        ];
    }
}
