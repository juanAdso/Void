<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DetalleFactura;

class DetalleFacturaSeeder extends Seeder
{
    public function run(): void
    {
        DetalleFactura::factory()->count(50)->create();
    }
}
