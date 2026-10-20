<?php

namespace Database\Seeders;

use App\Models\Facturas;
use Illuminate\Database\Seeder;

class FacturaSeeder extends Seeder
{
    public function run(): void
    {
        Facturas::factory()->count(300)->create();
    }
}
