<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ingresoextraordinario', function (Blueprint $table) {
            // Enlaza el ingreso extraordinario generado automaticamente al cobrar un credito
            // castigado (CobranzacuotaController::store) con su cobranza, para poder
            // revertirlo cuando esa cobranza se extorna (PagoprestamoCastigadoController::update).
            $table->unsignedBigInteger('idcredito_cobranzacuota')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('ingresoextraordinario', function (Blueprint $table) {
            $table->dropIndex(['idcredito_cobranzacuota']);
            $table->dropColumn('idcredito_cobranzacuota');
        });
    }
};
