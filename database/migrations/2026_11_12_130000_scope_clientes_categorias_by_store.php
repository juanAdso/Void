<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tienda')->nullable()->after('id');
            $table->dropUnique(['nombre']);
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tienda')->nullable()->after('id');
            $table->dropUnique(['documento']);
        });

        DB::transaction(function () {
            $categorias = DB::table('categorias')->orderBy('id')->get();

            foreach ($categorias as $categoria) {
                $tiendaIds = DB::table('productos')
                    ->where('id_categoria', $categoria->id)
                    ->distinct()
                    ->orderBy('id_tienda')
                    ->pluck('id_tienda')
                    ->all();

                if (count($tiendaIds) === 0) {
                    $tiendaIds = DB::table('tiendas')->orderBy('id')->pluck('id')->all();
                }

                foreach ($tiendaIds as $index => $tiendaId) {
                    if ($index === 0) {
                        DB::table('categorias')
                            ->where('id', $categoria->id)
                            ->update(['id_tienda' => $tiendaId, 'updated_at' => now()]);

                        continue;
                    }

                    $nuevaCategoriaId = DB::table('categorias')->insertGetId([
                        'id_tienda' => $tiendaId,
                        'nombre' => $categoria->nombre,
                        'descripcion' => $categoria->descripcion,
                        'estado' => $categoria->estado,
                        'created_at' => $categoria->created_at,
                        'updated_at' => now(),
                    ]);

                    DB::table('productos')
                        ->where('id_categoria', $categoria->id)
                        ->where('id_tienda', $tiendaId)
                        ->update(['id_categoria' => $nuevaCategoriaId]);
                }
            }

            $clientes = DB::table('clientes')->orderBy('id')->get();

            foreach ($clientes as $cliente) {
                $tiendaIds = DB::table('facturas')
                    ->join('usuarios', 'usuarios.id', '=', 'facturas.id_usuario')
                    ->where('facturas.id_cliente', $cliente->id)
                    ->whereNotNull('usuarios.id_tienda')
                    ->distinct()
                    ->orderBy('usuarios.id_tienda')
                    ->pluck('usuarios.id_tienda')
                    ->all();

                $tieneFacturasSinTienda = DB::table('facturas')
                    ->join('usuarios', 'usuarios.id', '=', 'facturas.id_usuario')
                    ->where('facturas.id_cliente', $cliente->id)
                    ->whereNull('usuarios.id_tienda')
                    ->exists();

                foreach ($tiendaIds as $index => $tiendaId) {
                    // Si existen facturas sin tienda, la fila original se conserva sin asignar
                    // y se crea una copia para cada comercio identificable.
                    if ($index === 0 && ! $tieneFacturasSinTienda) {
                        DB::table('clientes')
                            ->where('id', $cliente->id)
                            ->update(['id_tienda' => $tiendaId, 'updated_at' => now()]);

                        continue;
                    }

                    $nuevoClienteId = DB::table('clientes')->insertGetId([
                        'id_tienda' => $tiendaId,
                        'nombre' => $cliente->nombre,
                        'apellido' => $cliente->apellido,
                        'documento' => $cliente->documento,
                        'telefono' => $cliente->telefono,
                        'correo' => $cliente->correo,
                        'direccion' => $cliente->direccion,
                        'created_at' => $cliente->created_at,
                        'updated_at' => now(),
                    ]);

                    $usuariosTienda = DB::table('usuarios')
                        ->select('id')
                        ->where('id_tienda', $tiendaId);

                    DB::table('facturas')
                        ->where('id_cliente', $cliente->id)
                        ->whereIn('id_usuario', $usuariosTienda)
                        ->update(['id_cliente' => $nuevoClienteId]);
                }
            }
        });

        Schema::table('categorias', function (Blueprint $table) {
            $table->foreign('id_tienda')->references('id')->on('tiendas')->restrictOnDelete();
            $table->unique(['id_tienda', 'nombre']);
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->foreign('id_tienda')->references('id')->on('tiendas')->restrictOnDelete();
            $table->unique(['id_tienda', 'documento']);
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            $categorias = DB::table('categorias')
                ->select('nombre', DB::raw('MIN(id) as id'))
                ->groupBy('nombre')
                ->get();

            foreach ($categorias as $categoria) {
                $duplicados = DB::table('categorias')
                    ->where('nombre', $categoria->nombre)
                    ->where('id', '<>', $categoria->id)
                    ->pluck('id');

                if ($duplicados->isNotEmpty()) {
                    DB::table('productos')
                        ->whereIn('id_categoria', $duplicados)
                        ->update(['id_categoria' => $categoria->id]);

                    DB::table('categorias')->whereIn('id', $duplicados)->delete();
                }
            }

            $clientes = DB::table('clientes')
                ->select('documento', DB::raw('MIN(id) as id'))
                ->groupBy('documento')
                ->get();

            foreach ($clientes as $cliente) {
                $duplicados = DB::table('clientes')
                    ->where('documento', $cliente->documento)
                    ->where('id', '<>', $cliente->id)
                    ->pluck('id');

                if ($duplicados->isNotEmpty()) {
                    DB::table('facturas')
                        ->whereIn('id_cliente', $duplicados)
                        ->update(['id_cliente' => $cliente->id]);

                    DB::table('clientes')->whereIn('id', $duplicados)->delete();
                }
            }
        });

        Schema::table('categorias', function (Blueprint $table) {
            $table->dropForeign(['id_tienda']);
            $table->dropUnique(['id_tienda', 'nombre']);
            $table->dropColumn('id_tienda');
            $table->unique('nombre');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['id_tienda']);
            $table->dropUnique(['id_tienda', 'documento']);
            $table->dropColumn('id_tienda');
            $table->unique('documento');
        });
    }
};
