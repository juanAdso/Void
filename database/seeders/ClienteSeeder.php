<?php

namespace Database\Seeders;

use App\Models\Cliente;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        if (Cliente::exists()) {
            return;
        }

        Cliente::factory()->count(500)->create();
    }
}
