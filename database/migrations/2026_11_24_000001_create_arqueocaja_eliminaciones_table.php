<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arqueocaja_eliminaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tipo', 10);
            $table->unsignedBigInteger('idarqueocaja');
            $table->unsignedBigInteger('idagencia');
            $table->unsignedBigInteger('idresponsable')->nullable();
            $table->unsignedBigInteger('idusuario_eliminacion');
            $table->unsignedBigInteger('idresponsable_eliminacion');
            $table->date('corte');
            $table->decimal('total', 18, 2);
            $table->dateTime('fecha_eliminacion');
            $table->longText('datos_arqueo');
            $table->longText('denominaciones');
            $table->index(['tipo', 'idagencia', 'fecha_eliminacion'], 'arqueo_eliminacion_filtro');
        });
    }

    public function down(): void
    {
        // Conservar el historial de auditoría.
    }
};
