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
        $factura = Facturas::query()->with('usuario')->inRandomOrder()->first();

        if (! $factura || ! $factura->usuario?->id_tienda) {
            throw new RuntimeException('Debes tener facturas asociadas a una tienda antes de generar detalles.');
        }

        $producto = Productos::query()
            ->where('id_tienda', $factura->usuario->id_tienda)
            ->where('stock', '>', 0)
            ->inRandomOrder()
            ->first();

        if (! $producto) {
            throw new RuntimeException('Debes tener productos con stock en la tienda de la factura.');
        }

        $cantidad = fake()->numberBetween(1, min(3, (int) $producto->stock));
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
