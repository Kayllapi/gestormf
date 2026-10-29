<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('origen_global', 15)->nullable();
        });
        // Recuperar los castigos existentes; los otros registros no tienen origen auditable.
        DB::table('users')->where('idasesor', 0)->whereExists(function ($query) {
            $query->selectRaw('1')->from('credito')->whereColumn('credito.idcliente', 'users.id')
                ->where('credito.castigo_estado', 'CONFIRMADO');
        })->update(['origen_global' => 'CASTIGADO']);
    }

    public function down(): void
    {
        if (DB::table('users')->whereNotNull('origen_global')->exists()) {
            throw new RuntimeException('Respalde el origen de usuarios globales antes de revertir.');
        }
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('origen_global'));
    }
};
