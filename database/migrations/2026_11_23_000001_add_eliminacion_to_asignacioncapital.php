<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('asignacioncapital', 'fecha_eliminado')) {
            Schema::table('asignacioncapital', function (Blueprint $table) {
                $table->dateTime('fecha_eliminado')->nullable();
            });
        }
        if (!Schema::hasColumn('asignacioncapital', 'idresponsble_eliminado')) {
            Schema::table('asignacioncapital', function (Blueprint $table) {
                $table->unsignedBigInteger('idresponsble_eliminado')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Conservar los datos de auditoría y las columnas que pudieran existir previamente.
    }
};
