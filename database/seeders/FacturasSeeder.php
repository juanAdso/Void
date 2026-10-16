<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facturas;

class FacturaSeeder extends Seeder
{
    public function run(): void
    {
        Facturas::factory()->count(20)->create();
    }
}