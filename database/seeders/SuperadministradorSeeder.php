<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class SuperadministradorSeeder extends Seeder
{
    public function run(): void
    {
        $correo = trim((string) env('VOID_SUPERADMIN_CORREO', ''));
        $password = (string) env('VOID_SUPERADMIN_PASSWORD', '');
        $documento = trim((string) env('VOID_SUPERADMIN_DOCUMENTO', ''));
        $nombre = trim((string) env('VOID_SUPERADMIN_NOMBRE', ''));
        $apellido = trim((string) env('VOID_SUPERADMIN_APELLIDO', ''));

        if ($correo === '' || $password === '' || $documento === '' || $nombre === '' || $apellido === '') {
            $mensaje = 'Superadministrador VOID no creado: completa VOID_SUPERADMIN_NOMBRE, VOID_SUPERADMIN_APELLIDO, VOID_SUPERADMIN_DOCUMENTO, VOID_SUPERADMIN_CORREO y VOID_SUPERADMIN_PASSWORD en .env y vuelve a ejecutar php artisan db:seed --class=SuperadministradorSeeder.';
            if ($this->command) {
                $this->command->warn($mensaje);
            }

            return;
        }

        if (strlen($password) < 12) {
            throw new RuntimeException('La contraseña del superadministrador debe tener al menos 12 caracteres.');
        }

        $existente = User::query()->where('correo', $correo)->first();

        if ($existente && ($existente->id_tienda !== null || $existente->rol !== 'superadministrador')) {
            throw new RuntimeException('El correo configurado para el superadministrador ya pertenece a una cuenta de empresa. Usa un correo exclusivo para la cuenta global.');
        }

        $usuario = User::firstOrCreate(
            ['correo' => $correo],
            [
                'nombre' => $nombre,
                'apellido' => $apellido,
                'documento' => $documento,
                'password' => Hash::make($password),
                'rol' => 'superadministrador',
                'id_tienda' => null,
                'estado' => true,
            ]
        );

        $usuario->syncRoles(['superadministrador']);
    }
}
