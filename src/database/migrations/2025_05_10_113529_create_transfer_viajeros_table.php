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
        Schema::create('transfer_viajeros', function (Blueprint $table) {
            $table->id('id_viajero'); // Clave primaria con auto_increment
            $table->string('nombre', 100); // Columna para el nombre
            $table->string('apellido1', 100); // Columna para el primer apellido
            $table->string('apellido2', 100); // Columna para el segundo apellido
            $table->string('direccion', 100); // Columna para la dirección
            $table->string('codigoPostal', 100); // Columna para el código postal
            $table->string('ciudad', 100); // Columna para la ciudad
            $table->string('pais', 100); // Columna para el país
            $table->string('email', 100); // Columna para el email
            $table->string('password', 100); // Columna para la contraseña
            $table->string('rol', 20)->default('usuario'); // Columna para el rol con valor por defecto 'usuario'

            $table->timestamps(); // Agrega columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_viajeros');
    }
};
