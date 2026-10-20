
<?php

use App\Models\Productos;
use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductosFactory extends Factory
{
    protected $model = Productos::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->words(3, true),
            'descripcion' => fake()->sentence(),
            'precio' => fake()->randomFloat(2, 5000, 200000),
            'stock' => fake()->numberBetween(0, 100),
            'imagen' => null,
            'estado' => true,
            'id_categoria' => Categoria::factory(),
        ];
    }
}
