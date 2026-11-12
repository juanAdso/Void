<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class CrearSuperadministrador extends Command
{
    protected $signature = 'void:crear-superadministrador';

    protected $description = 'Crea la única cuenta global de superadministrador de VOID';

    public function handle(): int
    {
        $correo = mb_strtolower(trim((string) config('void.superadmin_email')));

        if ($correo === '' || ! filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $this->components->error('Configura VOID_SUPERADMIN_EMAIL en el entorno antes de ejecutar este comando.');

            return self::FAILURE;
        }

        /** @var PermissionRegistrar $registrar */
        $registrar = app(PermissionRegistrar::class);
        $registrar->setPermissionsTeamId(null);

        $registrado = User::query()
            ->where('rol', 'superadministrador')
            ->orWhereHas('roles', fn ($query) => $query->where('name', 'superadministrador'))
            ->exists();

        if ($registrado) {
            $this->components->error('Ya existe una cuenta con privilegios de superadministrador. No se permite crear otra.');

            return self::FAILURE;
        }

        if (User::query()->where('correo', $correo)->exists()) {
            $this->components->error('El correo configurado ya pertenece a una cuenta. No se elevará automáticamente una cuenta existente.');

            return self::FAILURE;
        }

        if (! Permission::query()->where('guard_name', 'web')->exists()) {
            $this->components->error('No hay permisos registrados. Ejecuta primero el seeder de seguridad.');

            return self::FAILURE;
        }

        $nombre = trim((string) $this->ask('Nombre'));
        $apellido = trim((string) $this->ask('Apellido'));
        $documento = trim((string) $this->ask('Documento'));

        if ($nombre === '' || $apellido === '' || $documento === '') {
            $this->components->error('Nombre, apellido y documento son obligatorios.');

            return self::FAILURE;
        }

        $password = (string) $this->secret('Contraseña (mínimo 12 caracteres)');
        $confirmacion = (string) $this->secret('Confirma la contraseña');

        if (mb_strlen($password) < 12 || ! hash_equals($password, $confirmacion)) {
            $this->components->error('La contraseña debe tener al menos 12 caracteres y coincidir con su confirmación.');

            return self::FAILURE;
        }

        DB::transaction(function () use ($correo, $nombre, $apellido, $documento, $password) {
            $rol = Role::query()->firstOrCreate([
                'name' => 'superadministrador',
                'guard_name' => 'web',
                'id_tienda' => null,
            ]);

            $rol->syncPermissions(Permission::query()->where('guard_name', 'web')->get());

            $usuario = User::create([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'documento' => $documento,
                'correo' => $correo,
                'password' => $password,
                'rol' => 'superadministrador',
                'id_tienda' => null,
                'estado' => true,
            ]);

            $usuario->assignRole($rol);
        });

        $this->components->info('Superadministrador creado. Esta cuenta es la única autorizada para consultar todas las tiendas.');

        return self::SUCCESS;
    }
}
