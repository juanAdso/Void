<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_usuario');
            $table->date('fecha');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('iva', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('estado', 30);
            $table->timestamps();

            $table->foreign('id_cliente')
                ->references('id')
                ->on('clientes');

            $table->foreign('id_usuario')
                ->references('id')
                ->on('usuarios');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
