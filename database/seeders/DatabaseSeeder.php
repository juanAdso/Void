<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Roles y Permisos de Seguridad
        $this->call([
            SeguridadSeeder::class,
        ]);

        // 2. Usuario Administrador de Prueba
        $admin = User::firstOrCreate(
            ['correo' => 'test@example.com'],
            [
                'nombre' => 'Usuario',
                'apellido' => 'Prueba',
                'documento' => '1000000000',
                'password' => bcrypt('password'),
                'rol' => 'administrador',
                'estado' => true,
            ]
        );

        if (! $admin->hasRole('administrador')) {
            $admin->assignRole('administrador');
        }

        // 3. Seeders comerciales existentes
        $this->call([
            CategoriaSeeder::class,
            TiendaSeeder::class,
            ClienteSeeder::class,
            ProductosSeeder::class,
            FacturasSeeder::class,
            DetalleFacturaSeeder::class,
        ]);
    }
}
