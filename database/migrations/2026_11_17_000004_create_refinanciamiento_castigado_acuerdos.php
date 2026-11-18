<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('secuencia_cuentas_credito', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->unsignedBigInteger('ultimo')->default(0);
        });
        DB::table('secuencia_cuentas_credito')->insert(['id' => 1, 'ultimo' => 0]);
        Schema::create('refinanciamiento_castigado_acuerdos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idcredito_original')->unique();
            $table->unsignedBigInteger('idcredito_nuevo')->unique();
            $table->unsignedBigInteger('idresponsable');
            $table->unsignedBigInteger('idsolicitante');
            $table->decimal('deuda_anterior', 15, 2);
            $table->decimal('monto_acordado', 15, 2);
            $table->decimal('ajuste', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (DB::table('refinanciamiento_castigado_acuerdos')->exists()) {
            throw new RuntimeException('Respalde los acuerdos de refinanciamiento antes de revertir.');
        }
        Schema::dropIfExists('refinanciamiento_castigado_acuerdos');
        Schema::dropIfExists('secuencia_cuentas_credito');
    }
};
