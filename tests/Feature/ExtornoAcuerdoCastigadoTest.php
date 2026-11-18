<?php

namespace Tests\Feature;

use App\Http\Controllers\Layouts\Backoffice\Sistema\PagoprestamoCastigadoController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExtornoAcuerdoCastigadoTest extends TestCase
{
    public function test_no_extorna_pagos_de_la_cuenta_original_con_acuerdo(): void
    {
        config(['database.default' => 'extorno_acuerdo_test', 'database.connections.extorno_acuerdo_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('credito', fn (Blueprint $table) => $table->integer('id')->primary());
        Schema::create('credito_cobranzacuota', function (Blueprint $table) {
            $table->integer('id')->primary(); $table->integer('idcredito');
        });
        Schema::create('refinanciamiento_castigado_acuerdos', fn (Blueprint $table) => $table->integer('idcredito_original'));
        DB::table('credito')->insert(['id' => 1]);
        DB::table('credito_cobranzacuota')->insert(['id' => 2, 'idcredito' => 1]);
        DB::table('refinanciamiento_castigado_acuerdos')->insert(['idcredito_original' => 1]);
        $controller = (new \ReflectionClass(PagoprestamoCastigadoController::class))->newInstanceWithoutConstructor();
        $respuesta = $controller->update(Request::create('/', 'PUT', ['view' => 'extornar']), 1, 2)->getData(true);
        $this->assertSame('ERROR', $respuesta['resultado']);
        $this->assertDatabaseHas('credito_cobranzacuota', ['id' => 2, 'idcredito' => 1]);
    }
}
