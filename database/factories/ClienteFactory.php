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
            'nombre' => fake('es_CO')->firstName(),
            'apellido' => fake('es_CO')->lastName(),
            'documento' => fake()->unique()->numerify('##########'),
            'telefono' => fake()->numerify('3#########'),
            'correo' => fake()->unique()->safeEmail(),
            'direccion' => fake('es_CO')->address(),
        ];
    }
}
