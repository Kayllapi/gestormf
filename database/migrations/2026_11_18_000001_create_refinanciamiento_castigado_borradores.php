<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('refinanciamiento_castigado_borradores', function (Blueprint $table) {
            $table->unsignedBigInteger('idcredito')->primary();
            $table->unsignedBigInteger('idusuario');
            $table->text('datos');
            $table->timestamp('updated_at');
        });
    }

    public function down(): void
    {
        if (DB::table('refinanciamiento_castigado_borradores')->exists()) {
            throw new RuntimeException('Existen borradores de refinanciamiento; respalde sus datos antes de revertir.');
        }
        Schema::dropIfExists('refinanciamiento_castigado_borradores');
    }
};
