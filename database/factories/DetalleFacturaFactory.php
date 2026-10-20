<?php

namespace Database\Factories;

use App\Models\DetalleFactura;
use App\Models\Facturas;
use App\Models\Productos;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetalleFacturaFactory extends Factory
{
    protected $model = DetalleFactura::class;

    public function definition(): array
    {
        $cantidad = fake()->numberBetween(1, 5);
        $precio = fake()->randomFloat(2, 5000, 200000);

        return [
            'id_factura' => Facturas::factory(),
            'id_producto' => Productos::factory(),
            'cantidad' => $cantidad,
            'precio_unitario' => $precio,
            'subtotal' => round($cantidad * $precio, 2),
        ];
    }
}
