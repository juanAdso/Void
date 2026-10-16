<?php

namespace Database\Factories;

use App\Models\Facturas;
use Illuminate\Database\Eloquent\Factories\Factory;


class FacturasFactory extends Factory
{
    protected $model = Facturas::class;

    public function definition(): array
    {
        return [
            'fecha' => $this->faker->date(),
            'subtotal' => $this->faker->randomFloat(2, 1, 1000),
            'iva' => $this->faker->randomFloat(2, 1, 100),
            'estado' => $this->faker->randomElement(['pendiente', 'pagada', 'cancelada']),
            'total' => $this->faker->randomFloat(2, 1, 1000),
            'cliente_id' => \App\Models\Cliente::factory(),
        ];
    }
}