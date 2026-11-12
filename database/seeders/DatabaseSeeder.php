<?php

namespace Database\Seeders;

use App\Models\Tienda;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            SeguridadSeeder::class,
            TiendaSeeder::class,
        ]);

        $tiendaInicial = Tienda::query()->orderBy('id')->firstOrFail();

        $admin = User::firstOrCreate(
            ['correo' => 'test@example.com'],
            [
                'nombre' => 'Usuario',
                'apellido' => 'Prueba',
                'documento' => '1000000000',
                'password' => bcrypt('password'),
                'rol' => 'administrador',
                'id_tienda' => $tiendaInicial->id,
                'estado' => true,
            ]
        );

        // Mantener el usuario de demostración asociado a una tienda para que no obtenga
        // acceso global accidental. Las credenciales son exclusivamente de desarrollo.
        if (! $admin->id_tienda) {
            $admin->forceFill(['id_tienda' => $tiendaInicial->id])->save();
        }

        if (! $admin->hasRole('administrador')) {
            $admin->assignRole('administrador');
        }

        $this->call([
            CategoriaSeeder::class,
            ClienteSeeder::class,
            ProductosSeeder::class,
            FacturasSeeder::class,
            DetalleFacturaSeeder::class,
        ]);
    }
}
