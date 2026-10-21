<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Facturas;
use Illuminate\Database\Eloquent\Factories\Factory;
use RuntimeException;

class FacturasFactory extends Factory
{
    protected $model = Facturas::class;

    public function definition(): array
    {
        $cliente = Cliente::query()->inRandomOrder()->first();

        if (!$cliente) {
            throw new RuntimeException(
                'Debes crear los clientes antes de generar facturas.'
            );
        }

        return [
            'id_cliente' => $cliente->getKey(),
            'fecha' => fake()->dateTimeBetween('-1 year', 'now'),
            'subtotal' => 0,
            'iva' => 0,
            'total' => 0,
            'estado' => fake()->randomElement(['pendiente', 'pagada']),
        ];
    }
}

