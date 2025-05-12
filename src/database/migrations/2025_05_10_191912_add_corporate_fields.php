<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    /* --------- 1. % comisión en HOTELS --------- */
    Schema::table('transfer_hotel', function (Blueprint $t) {
      if (!Schema::hasColumn('transfer_hotel', 'commission')) {
        $t->decimal('commission', 5, 2)->default(0)->after('id_zona');
      }
    });

    /* --------- 2. id_hotel y rol en VIAJEROS --------- */
    Schema::table('transfer_viajeros', function (Blueprint $t) {
      if (!Schema::hasColumn('transfer_viajeros', 'id_hotel')) {
        $t->unsignedBigInteger('id_hotel')->nullable()->after('password');
        $t->foreign('id_hotel')->references('id_hotel')
          ->on('transfer_hotel')
          ->nullOnDelete();
      }

      if (!Schema::hasColumn('transfer_viajeros', 'rol')) {
        $t->enum('rol', ['admin', 'corporativo', 'usuario'])
          ->default('usuario')
          ->after('id_hotel');
      }
    });

    /* --------- 3. comisión calculada en RESERVAS --------- */
    Schema::table('transfer_reservas', function (Blueprint $t) {
      if (!Schema::hasColumn('transfer_reservas', 'precio')) {
        $t->decimal('precio', 8, 2)->nullable();
        $t->decimal('comision_hotel', 8, 2)->nullable();
      }
    });

  }

  public function down(): void
  {
    Schema::table('transfer_reservas', fn(Blueprint $t) => $t->dropColumn('comision_hotel'));
    Schema::table('transfer_viajeros', function (Blueprint $t) {
      $t->dropForeign(['id_hotel']);
      $t->dropColumn(['id_hotel', 'rol']);
    });
    Schema::table('transfer_hotel', fn(Blueprint $t) => $t->dropColumn('commission'));
  }
};
