<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Productos;

class ProductosSeeder extends Seeder
{
    public function run(): void
    {
        Productos::factory()->count(30)->create();
    }
}