<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transfer_precios', function (Blueprint $table) {
            $table->id('id_precio');
            $table->unsignedBigInteger('id_zona');
            $table->unsignedBigInteger('id_vehiculo');
            $table->unsignedBigInteger('id_tipo_reserva');
            $table->decimal('precio', 8, 2);

            // Relaciones (si las FK ya existen)
            $table->foreign('id_zona')->references('id_zona')->on('transfer_zona');
            $table->foreign('id_vehiculo')->references('id_vehiculo')->on('transfer_vehiculo');
            $table->foreign('id_tipo_reserva')->references('id_tipo_reserva')->on('transfer_tipo_reserva');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_precios');
    }
};
