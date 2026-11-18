<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('credito', function (Blueprint $table) {
            $table->boolean('es_refinanciamiento_castigado')->default(false);
            $table->unsignedBigInteger('refinanciamiento_castigado_responsable')->nullable();
        });
    }

    public function down(): void
    {
        if (DB::table('credito')->where('es_refinanciamiento_castigado', true)->exists()) {
            throw new RuntimeException('Existen propuestas de refinanciamiento castigado; respalde sus datos antes de revertir.');
        }
        Schema::table('credito', fn (Blueprint $table) => $table->dropColumn([
            'es_refinanciamiento_castigado', 'refinanciamiento_castigado_responsable',
        ]));
    }
};
