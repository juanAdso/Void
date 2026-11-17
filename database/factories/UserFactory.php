<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nombre' => fake('es_CO')->firstName(),
            'apellido' => fake('es_CO')->lastName(),
            'documento' => fake()->unique()->numerify('##########'),
            'correo' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'rol' => fake()->randomElement(['administrador', 'empleado']),
            'estado' => true,
        ];
    }
}
