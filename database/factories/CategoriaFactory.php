<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Tienda;
use Illuminate\Database\Eloquent\Factories\Factory;
use RuntimeException;

/**
 * @extends Factory<Categoria>
 */
class CategoriaFactory extends Factory
{
    protected $model = Categoria::class;

    public function definition(): array
    {
        return [
            'id_tienda' => fn () => Tienda::query()->inRandomOrder()->value('id'),
            'nombre' => fake()->unique()->word(),
            'descripcion' => fake()->optional()->sentence(),
            'estado' => true,
        ];
    }
}
