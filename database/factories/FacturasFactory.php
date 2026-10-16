<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Facturas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facturas>
 */
class FacturasFactory extends Factory
{
    protected $model = Facturas::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 50, 500);
        $iva = round($subtotal * 0.19, 2);
        $total = $subtotal + $iva;

        return [
            'id_cliente' => Cliente::factory(),
            'fecha' => fake()->date(),
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => $total,
            'estado' => 'Pagada',
        ];
    }
}
