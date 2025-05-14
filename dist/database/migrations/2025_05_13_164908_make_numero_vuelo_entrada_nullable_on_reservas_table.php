<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up()
  {
    Schema::table('transfer_reservas', function (Blueprint $table) {
      $table->string('numero_vuelo_entrada')->nullable()->change();
    });
  }

  public function down()
  {
    Schema::table('transfer_reservas', function (Blueprint $table) {
      $table->string('numero_vuelo_entrada')->nullable(false)->change();
    });
  }
};
