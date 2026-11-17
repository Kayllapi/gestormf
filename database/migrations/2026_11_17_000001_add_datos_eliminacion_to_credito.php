<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('credito', function (Blueprint $table) {
            $table->string('estado_previo_eliminacion')->nullable();
            $table->unsignedBigInteger('idasesor_eliminacion')->nullable();
            $table->unsignedBigInteger('idusuario_eliminacion')->nullable();
        });
    }

    public function down(): void
    {
        if (DB::table('credito')->whereNotNull('estado_previo_eliminacion')->exists()) {
            throw new RuntimeException('Respalde los datos de eliminacion de creditos antes de revertir.');
        }
        Schema::table('credito', fn (Blueprint $table) => $table->dropColumn([
            'estado_previo_eliminacion', 'idasesor_eliminacion', 'idusuario_eliminacion',
        ]));
    }
};
