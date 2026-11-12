<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Facturas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use RuntimeException;

class FacturasFactory extends Factory
{
    protected $model = Facturas::class;

    public function definition(): array
    {
        $usuario = User::query()
            ->where('estado', true)
            ->whereNotNull('id_tienda')
            ->inRandomOrder()
            ->first();

        if (! $usuario) {
            throw new RuntimeException('Debes crear usuarios asociados a una tienda antes de generar facturas.');
        }

        $cliente = Cliente::query()
            ->where('id_tienda', $usuario->id_tienda)
            ->inRandomOrder()
            ->first();

        if (! $cliente) {
            throw new RuntimeException('Debes crear clientes para la tienda del usuario antes de generar facturas.');
        }

        return [
            'id_cliente' => $cliente->getKey(),
            'id_usuario' => $usuario->getKey(),
            'fecha' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'subtotal' => 0,
            'iva' => 0,
            'total' => 0,
            'estado' => fake()->randomElement(['pendiente', 'pagada', 'anulada']),
        ];
    }
}
