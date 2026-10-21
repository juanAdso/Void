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
        $productosDisponibles = Productos::query()->where('stock', '>', 0)->get();

        if (!Facturas::exists()) {
            throw new RuntimeException('Ejecuta FacturasSeeder antes de DetalleFacturaSeeder.');
        }

        if ($productosDisponibles->isEmpty()) {
            throw new RuntimeException('Debes tener productos con stock para generar detalles.');
        }

        foreach (Facturas::all() as $factura) {
            if (!$factura->detalle_facturas()->exists()) {
                $cantidadDetalles = fake()->numberBetween(1, min(4, $productosDisponibles->count()));
                $seleccionados = $productosDisponibles->random($cantidadDetalles);

                foreach ($seleccionados as $producto) {
                    $cantidad = fake()->numberBetween(1, min(3, (int) $producto->stock));
                    $precio = (float) $producto->precio;

                    $factura->detalle_facturas()->create([
                        'id_producto' => $producto->getKey(),
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio,
                        'subtotal' => round($cantidad * $precio, 2),
                    ]);
                }
            }

            $subtotal = (float) $factura->detalle_facturas()->sum('subtotal');
            $iva = round($subtotal * 0.19, 2);

            $factura->update([
                'subtotal' => $subtotal,
                'iva' => $iva,
                'total' => round($subtotal + $iva, 2),
            ]);
        }
    }
}
