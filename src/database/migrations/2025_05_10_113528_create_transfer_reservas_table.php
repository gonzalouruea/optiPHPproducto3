<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('transfer_reservas', function (Blueprint $table) {
      $table->id('id_reserva');
      $table->string('localizador', 100);
      $table->unsignedBigInteger('id_hotel')->nullable();
      $table->unsignedBigInteger('id_tipo_reserva');
      $table->string('email_cliente', 255);
      $table->dateTime('fecha_reserva');
      $table->dateTime('fecha_modificacion');
      $table->unsignedBigInteger('id_destino')->nullable();
      $table->date('fecha_entrada')->nullable();
      $table->time('hora_entrada')->nullable();
      $table->string('numero_vuelo_entrada', 50)->nullable();
      $table->string('origen_vuelo_entrada', 100)->nullable();
      $table->time('hora_vuelo_salida')->nullable();
      $table->date('fecha_vuelo_salida')->nullable();
      $table->integer('num_viajeros');
      $table->integer('precio');
      $table->unsignedBigInteger('id_vehiculo');
      $table->time('hora_recogida')->nullable();
      $table->boolean('creado_por_admin')->default(0);

      $table->timestamps();

      // Opcional: Agregar claves foráneas si es necesario
      // $table->foreign('id_hotel')->references('id')->on('hoteles');
      // $table->foreign('id_tipo_reserva')->references('id')->on('tipos_reserva');
      // $table->foreign('id_destino')->references('id')->on('destinos');
      // $table->foreign('id_vehiculo')->references('id')->on('vehiculos');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('transfer_reservas');
  }
};
