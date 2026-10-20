<?php

namespace Database\Seeders;

use App\Models\Productos;
use Illuminate\Database\Seeder;

class ProductosSeeder extends Seeder
{
    public function run(): void
    {
        Productos::factory()->count(500)->create();
    }
}
