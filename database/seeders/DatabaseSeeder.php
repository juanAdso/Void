<?php

namespace Database\Seeders;

use App\Models\Tienda;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            TiendaSeeder::class,
            SeguridadSeeder::class,
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

        if (! $admin->id_tienda) {
            $admin->forceFill(['id_tienda' => $tiendaInicial->id])->save();
        }

        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);
        $registrar->setPermissionsTeamId((int) $tiendaInicial->id);
        $admin->unsetRelation('roles');
        $admin->syncRoles(['administrador']);
        $registrar->setPermissionsTeamId(null);

        $this->call([
            CategoriaSeeder::class,
            ClienteSeeder::class,
            ProductosSeeder::class,
            FacturasSeeder::class,
            DetalleFacturaSeeder::class,
        ]);
    }
}
