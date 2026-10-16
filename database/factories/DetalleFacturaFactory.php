<?php

namespace Database\Factories;

use App\Models\DetalleFactura;
use Illuminate\Database\Eloquent\Factories\Factory;


class DetalleFacturaFactory extends Factory
{
    protected $model = DetalleFactura::class;

    public function definition(): array
    {
        return [
            'cantidad' => $this->faker->numberBetween(1, 10),
            'precio_unitario' => $this->faker->randomFloat(2, 1, 100),
            'producto_id' => \App\Models\Productos::factory(),
            'factura_id' => \App\Models\Facturas::factory(),
        ];
    }
}
