<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->unsignedBigInteger('id_tienda')->nullable()->after('id');
            $table->dropUnique('roles_name_guard_name_unique');
            $table->unique(['id_tienda', 'name', 'guard_name'], 'roles_tienda_nombre_guard_unique');
            $table->index('id_tienda', 'roles_id_tienda_index');
        });

        Schema::table($tableNames['model_has_roles'], function (Blueprint $table) {
            $table->unsignedBigInteger('id_tienda')->nullable()->after('model_type');
            $table->index('id_tienda', 'model_has_roles_id_tienda_index');
        });

        Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) {
            $table->unsignedBigInteger('id_tienda')->nullable()->after('model_type');
            $table->index('id_tienda', 'model_has_permissions_id_tienda_index');
        });

        DB::transaction(function () use ($tableNames) {
            $roles = DB::table($tableNames['roles'])->get();
            $tiendas = DB::table('tiendas')->orderBy('id')->pluck('id');

            foreach ($roles as $rolGlobal) {
                // El rol global es reservado exclusivamente para el propietario de VOID.
                if ($rolGlobal->name === 'superadministrador') {
                    continue;
                }

                $permisosRol = DB::table($tableNames['role_has_permissions'])
                    ->where('role_id', $rolGlobal->id)
                    ->pluck('permission_id');

                $asignaciones = DB::table($tableNames['model_has_roles'])
                    ->where('role_id', $rolGlobal->id)
                    ->get();

                foreach ($tiendas as $tiendaId) {
                    $nuevoRolId = DB::table($tableNames['roles'])
                        ->where('id_tienda', $tiendaId)
                        ->where('name', $rolGlobal->name)
                        ->where('guard_name', $rolGlobal->guard_name)
                        ->value('id');

                    if (! $nuevoRolId) {
                        $nuevoRolId = DB::table($tableNames['roles'])->insertGetId([
                            'id_tienda' => $tiendaId,
                            'name' => $rolGlobal->name,
                            'guard_name' => $rolGlobal->guard_name,
                            'created_at' => $rolGlobal->created_at,
                            'updated_at' => now(),
                        ]);
                    }

                    foreach ($permisosRol as $permissionId) {
                        DB::table($tableNames['role_has_permissions'])->insertOrIgnore([
                            'role_id' => $nuevoRolId,
                            'permission_id' => $permissionId,
                        ]);
                    }

                    foreach ($asignaciones as $asignacion) {
                        if (! str_ends_with($asignacion->model_type, '\\User')) {
                            continue;
                        }

                        $usuario = DB::table('usuarios')->where('id', $asignacion->model_id)->first();

                        if (! $usuario || (int) $usuario->id_tienda !== (int) $tiendaId) {
                            continue;
                        }

                        DB::table($tableNames['model_has_roles'])
                            ->where('role_id', $rolGlobal->id)
                            ->where('model_id', $asignacion->model_id)
                            ->where('model_type', $asignacion->model_type)
                            ->update([
                                'role_id' => $nuevoRolId,
                                'id_tienda' => $tiendaId,
                            ]);
                    }
                }

                // Los usuarios sin tienda pierden la asignación global heredada y
                // deben ser conciliados por un proceso administrativo explícito.
                DB::table($tableNames['model_has_roles'])
                    ->where('role_id', $rolGlobal->id)
                    ->delete();

                DB::table($tableNames['role_has_permissions'])
                    ->where('role_id', $rolGlobal->id)
                    ->delete();

                DB::table($tableNames['roles'])
                    ->where('id', $rolGlobal->id)
                    ->delete();
            }

            // Las asignaciones directas de permisos también quedan ligadas al tenant.
            $asignacionesDirectas = DB::table($tableNames['model_has_permissions'])->get();

            foreach ($asignacionesDirectas as $asignacion) {
                if (! str_ends_with($asignacion->model_type, '\\User')) {
                    DB::table($tableNames['model_has_permissions'])
                        ->where('permission_id', $asignacion->permission_id)
                        ->where('model_id', $asignacion->model_id)
                        ->where('model_type', $asignacion->model_type)
                        ->delete();

                    continue;
                }

                $usuario = DB::table('usuarios')->where('id', $asignacion->model_id)->first();

                if (! $usuario || ! $usuario->id_tienda) {
                    DB::table($tableNames['model_has_permissions'])
                        ->where('permission_id', $asignacion->permission_id)
                        ->where('model_id', $asignacion->model_id)
                        ->where('model_type', $asignacion->model_type)
                        ->delete();

                    continue;
                }

                DB::table($tableNames['model_has_permissions'])
                    ->where('permission_id', $asignacion->permission_id)
                    ->where('model_id', $asignacion->model_id)
                    ->where('model_type', $asignacion->model_type)
                    ->update(['id_tienda' => $usuario->id_tienda]);
            }
        });

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->foreign('id_tienda', 'roles_id_tienda_foreign')
                ->references('id')->on('tiendas')->restrictOnDelete();
        });

        Schema::table($tableNames['model_has_roles'], function (Blueprint $table) {
            $table->foreign('id_tienda', 'model_has_roles_id_tienda_foreign')
                ->references('id')->on('tiendas')->restrictOnDelete();
        });

        Schema::table($tableNames['model_has_permissions'], function (Blueprint $table) {
            $table->foreign('id_tienda', 'model_has_permissions_id_tienda_foreign')
                ->references('id')->on('tiendas')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        throw new RuntimeException(
            'Esta migración distribuye roles y asignaciones entre tiendas; no se revierte automáticamente para evitar mezclar permisos y perder el aislamiento multiempresa. Restaura un respaldo previo si necesitas revertirla.'
        );
    }
};
