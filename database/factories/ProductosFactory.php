<?php

namespace Database\Factories;

use App\Models\Productos;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;


class ProductosFactory extends Factory
{
    protected $model = Productos::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->word(),
            'descripcion' => $this->faker->sentence(),
            'precio' => $this->faker->randomFloat(2, 1, 100),
            'stock' => $this->faker->numberBetween(0, 100),
            'categoria_id' => Categoria::factory(),
        ];
    }
}
