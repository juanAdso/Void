<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cliente');

            $table->date('fecha');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('iva', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('estado', 30);

            $table->timestamps();

            $table->foreign('id_cliente')
                ->references('id')
                ->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
