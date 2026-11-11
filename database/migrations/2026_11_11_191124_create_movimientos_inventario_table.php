<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_producto');
            $table->unsignedBigInteger('id_tienda');
            $table->unsignedBigInteger('id_usuario')->nullable();
            $table->string('tipo_movimiento', 50);
            $table->integer('cantidad');
            $table->dateTime('fecha');
            $table->string('motivo', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_producto')
                ->references('id')
                ->on('productos');

            $table->foreign('id_tienda')
                ->references('id')
                ->on('tiendas');

            $table->foreign('id_usuario')
                ->references('id')
                ->on('usuarios')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
