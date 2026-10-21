<?php

namespace Database\Seeders;

use App\Models\Tienda;
use Illuminate\Database\Seeder;

class TiendaSeeder extends Seeder
{
    public function run(): void
    {
        if (Tienda::exists()) {
            return;
        }

        Tienda::factory()->count(10)->create();
    }
}
