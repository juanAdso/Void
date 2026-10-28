<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('productos', 'id_tienda')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->unsignedBigInteger('id_tienda')->nullable();

                $table->foreign('id_tienda')
                    ->references('id')
                    ->on('tiendas')
                    ->nullOnDelete();
            });
        }

        $tiendaId = DB::table('tiendas')->orderBy('id')->value('id');

        if ($tiendaId !== null) {
            DB::table('productos')
                ->whereNull('id_tienda')
                ->update(['id_tienda' => $tiendaId]);
        }
    }

    public function down(): void
    {
        // Se conserva id_tienda para no perder la relación ni los datos existentes.
    }
};
