<?php

namespace Database\Seeders;

use App\Models\Facturas;
use App\Models\Productos;
use Illuminate\Database\Seeder;
use RuntimeException;

class DetalleFacturaSeeder extends Seeder
{
    public function run(): void
    {
        if (!Facturas::exists()) {
            throw new RuntimeException('Ejecuta FacturasSeeder antes de DetalleFacturaSeeder.');
        }

        foreach (Facturas::query()->orderBy('id_detalle_factura')->get() as $factura) {
            if (!$factura->detalle_factura()->exists()) {
                $cantidadDetalles = fake()->numberBetween(1, 4);

                for ($i = 0; $i < $cantidadDetalles; $i++) {
                    $producto = Productos::query()
                        ->where('stock', '>', 0)
                        ->inRandomOrder()
                        ->first();

                    if (!$producto) {
                        break;
                    }

                    $cantidad = fake()->numberBetween(1, min(3, (int) $producto->stock));
                    $precio = (float) $producto->precio;

                    $factura->detalle_factura()->create([
                        'id_producto' => $producto->getKey(),
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio,
                        'subtotal' => round($cantidad * $precio, 2),
                    ]);

                    if ($factura->estado !== 'anulada') {
                        $producto->decrement('stock', $cantidad);
                    }
                }
            }

            $subtotal = (float) $factura->detalle_factura()->sum('subtotal');
            $iva = round($subtotal * 0.19, 2);

            $factura->update([
                'subtotal' => round($subtotal, 2),
                'iva' => $iva,
                'total' => round($subtotal + $iva, 2),
            ]);
        }
    }
}
