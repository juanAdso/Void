<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Orden deliberado: primero permisos/roles, después empresas y cuentas,
        // y finalmente los datos comerciales asociados a cada empresa.
        $this->call([
            SeguridadSeeder::class,
            TiendaSeeder::class,
            SuperadministradorSeeder::class,
            CuentasDemostracionSeeder::class,
        ]);
    }
}
