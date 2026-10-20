<?php

namespace Database\Seeders;

use App\Models\Facturas;
use Illuminate\Database\Seeder;

class FacturasSeeder extends Seeder
{
    public function run(): void
    {
        Facturas::factory()->count(300)->create();
    }
}
