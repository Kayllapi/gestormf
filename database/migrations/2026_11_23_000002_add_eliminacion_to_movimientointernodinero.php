<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('movimientointernodinero', 'fecha_eliminado')) {
            Schema::table('movimientointernodinero', fn (Blueprint $table) => $table->dateTime('fecha_eliminado')->nullable());
        }
        if (!Schema::hasColumn('movimientointernodinero', 'idresponsble_eliminado')) {
            Schema::table('movimientointernodinero', fn (Blueprint $table) => $table->unsignedBigInteger('idresponsble_eliminado')->nullable());
        }
        if (!Schema::hasColumn('movimientointernodinero', 'extorno_agrupado')) {
            Schema::table('movimientointernodinero', fn (Blueprint $table) => $table->boolean('extorno_agrupado')->default(false));
        }
    }

    public function down(): void
    {
        // Conservar la auditoría y las columnas que pudieran existir previamente.
    }
};
