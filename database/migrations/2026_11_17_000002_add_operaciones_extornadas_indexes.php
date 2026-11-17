<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('credito_cobranzacuota', fn (Blueprint $table) => $table->index(
            ['idestadocredito_cobranzacuota', 'idestadoextorno', 'fechaextorno', 'idtienda'], 'extorno_cuota_busqueda_idx'
        ));
        Schema::table('credito', fn (Blueprint $table) => $table->index(
            ['estado', 'fecha_eliminado', 'idtienda'], 'extorno_credito_busqueda_idx'
        ));
        Schema::table('gastoadministrativooperativo', fn (Blueprint $table) => $table->index(
            ['fecha_eliminado', 'idtienda'], 'extorno_gasto_busqueda_idx'
        ));
        Schema::table('ingresoextraordinario', fn (Blueprint $table) => $table->index(
            ['idestadoeliminado', 'fecha_eliminado', 'idtienda'], 'extorno_ingreso_busqueda_idx'
        ));
        Schema::table('credito_formapago', fn (Blueprint $table) => $table->index('idcredito', 'extorno_formapago_credito_idx'));
    }

    public function down(): void
    {
        Schema::table('credito_cobranzacuota', fn (Blueprint $table) => $table->dropIndex('extorno_cuota_busqueda_idx'));
        Schema::table('credito', fn (Blueprint $table) => $table->dropIndex('extorno_credito_busqueda_idx'));
        Schema::table('gastoadministrativooperativo', fn (Blueprint $table) => $table->dropIndex('extorno_gasto_busqueda_idx'));
        Schema::table('ingresoextraordinario', fn (Blueprint $table) => $table->dropIndex('extorno_ingreso_busqueda_idx'));
        Schema::table('credito_formapago', fn (Blueprint $table) => $table->dropIndex('extorno_formapago_credito_idx'));
    }
};
