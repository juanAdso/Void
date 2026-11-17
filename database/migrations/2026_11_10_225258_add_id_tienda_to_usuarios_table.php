<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tienda')->nullable()->after('rol');

            $table->foreign('id_tienda')
                ->references('id')
                ->on('tiendas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['id_tienda']);
            $table->dropColumn('id_tienda');
        });
    }
};
