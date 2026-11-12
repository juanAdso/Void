<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Tienda;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'id_tienda' => fn () => Tienda::query()->inRandomOrder()->value('id'),
            'nombre' => fake('es_CO')->firstName(),
            'apellido' => fake('es_CO')->lastName(),
            'documento' => fake()->unique()->numerify('##########'),
            'telefono' => fake()->optional()->numerify('3#########'),
            'correo' => fake()->optional()->safeEmail(),
            'direccion' => fake()->optional()->address(),
        ];
    }
}
