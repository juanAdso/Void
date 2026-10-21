<?php

namespace Database\Factories;

use App\Models\Tienda;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tienda>
 */
class TiendaFactory extends Factory
{
    protected $model = Tienda::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->company(),
            'descripcion' => fake()->optional()->sentence(),
            'logo' => null,
            'direccion' => fake()->optional()->address(),
            'telefono' => fake()->optional()->numerify('3#########'),
            'correo' => fake()->unique()->safeEmail(),
            'estado' => true,
        ];
    }
}
