
<?php


use App\Models\Facturas;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacturasFactory extends Factory
{
    protected $model = Facturas::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 10000, 500000);
        $iva = round($subtotal * 0.19, 2);

        return [
            'id_cliente' => Cliente::factory(),
            'fecha' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => round($subtotal + $iva, 2),
            'estado' => fake()->randomElement([
                'pendiente',
                'pagada',
                'anulada',
            ]),
        ];
    }
}
