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
        Schema::create('transfer_zona', function (Blueprint $table) {
            $table->id('id_zona'); // Clave primaria con auto_increment
            $table->string('descripcion', 255); // Columna para la descripción

            $table->timestamps(); // Agrega columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_zona');
    }
};
