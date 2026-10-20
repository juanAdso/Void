<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'documento' => fake()->unique()->numerify('##########'),
            'telefono' => fake()->numerify('3#########'),
            'correo' => fake()->unique()->safeEmail(),
            'direccion' => fake()->address(),
        ];
    }
}
