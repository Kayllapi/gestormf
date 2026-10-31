<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('credito', function (Blueprint $table) {
            $table->unsignedBigInteger('idusuariocobranza')->nullable()->index();
        });
    }

    public function down(): void
    {
        // Impedir perder el registro de a quién se asignó la cobranza al revertir.
        if (\Illuminate\Support\Facades\DB::table('credito')->whereNotNull('idusuariocobranza')->exists()) {
            throw new RuntimeException('Existen cuentas con ejecutivo de cobranza asignado: respalde y resuelva estos registros antes de revertir.');
        }
        Schema::table('credito', function (Blueprint $table) {
            $table->dropIndex(['idusuariocobranza']);
            $table->dropColumn('idusuariocobranza');
        });
    }
};
