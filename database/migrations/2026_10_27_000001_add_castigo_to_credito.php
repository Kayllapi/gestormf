<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('credito', function (Blueprint $table) {
            $table->string('castigo_estado', 15)->default('NINGUNO')->index();
            $table->unsignedBigInteger('castigo_solicitante')->nullable();
            $table->timestamp('castigo_solicitado_at')->nullable();
            $table->unsignedBigInteger('castigo_responsable')->nullable();
            $table->timestamp('castigo_confirmado_at')->nullable();
        });
    }

    public function down(): void
    {
        // Impedir perder el registro de solicitudes o castigos al revertir.
        if (\Illuminate\Support\Facades\DB::table('credito')->where('castigo_estado', '!=', 'NINGUNO')->exists()) {
            throw new RuntimeException('Existen solicitudes o castigos: respalde y resuelva estos registros antes de revertir.');
        }
        Schema::table('credito', function (Blueprint $table) {
            $table->dropIndex(['castigo_estado']);
            $table->dropColumn(['castigo_estado', 'castigo_solicitante', 'castigo_solicitado_at', 'castigo_responsable', 'castigo_confirmado_at']);
        });
    }
};
