<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Facturas;
use Illuminate\Database\Seeder;
use RuntimeException;

class FacturasSeeder extends Seeder
{
    public function run(): void
    {
        if (!Cliente::exists()) {
            throw new RuntimeException('Ejecuta ClienteSeeder antes de FacturasSeeder.');
        }

        if (Facturas::exists()) {
            return;
        }

        Facturas::factory()->count(300)->create();
    }
}
