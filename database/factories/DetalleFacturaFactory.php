<?php

namespace Database\Factories;

use App\Models\DetalleFactura;
use App\Models\Facturas;
use App\Models\Productos;
use Illuminate\Database\Eloquent\Factories\Factory;
use RuntimeException;

class DetalleFacturaFactory extends Factory
{
    protected $model = DetalleFactura::class;

    public function definition(): array
    {
        $factura = Facturas::query()->inRandomOrder()->first();

        $producto = Productos::query()
            ->where('stock', '>', 0)
            ->inRandomOrder()
            ->first();

        if (!$factura || !$producto) {
            throw new RuntimeException(
                'Debes tener facturas y productos con stock para generar detalles.'
            );
        }

        $cantidad = fake()->numberBetween(
            1,
            min(3, (int) $producto->stock)
        );

        $precio = (float) $producto->precio;

        return [
            'id_factura' => $factura->getKey(),
            'id_producto' => $producto->getKey(),
            'cantidad' => $cantidad,
            'precio_unitario' => $precio,
            'subtotal' => round($cantidad * $precio, 2),
        ];
    }
}

