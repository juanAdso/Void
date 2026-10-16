<?php

namespace Database\Factories;

use App\Models\DetalleFactura;
use App\Models\Facturas;
use App\Models\Productos;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DetalleFactura>
 */
class DetalleFacturaFactory extends Factory
{
    protected $model = DetalleFactura::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cantidad = fake()->numberBetween(1, 5);
        $precioUnitario = fake()->randomFloat(2, 10, 100);
        $subtotal = $cantidad * $precioUnitario;

        return [
            'id_factura' => Facturas::factory(),
            'id_producto' => Productos::factory(),
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'subtotal' => $subtotal,
        ];
    }
}
