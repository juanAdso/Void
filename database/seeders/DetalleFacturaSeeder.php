<?php

namespace Database\Seeders;

use App\Models\DetalleFactura;
use Illuminate\Database\Seeder;

class DetalleFacturaSeeder extends Seeder
{
    public function run(): void
    {
        DetalleFactura::factory()->count(1000)->create();
    }
}

