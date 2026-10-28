<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_categoria');
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->integer('stock');
            $table->string('imagen', 255)->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->foreign('id_categoria')
                ->references('id')
                ->on('categorias');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
