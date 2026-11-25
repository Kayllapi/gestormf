<?php

namespace Tests\Feature;

use App\Services\ListaNegraCredito;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ListaNegraCancelacionRefinanciadoTest extends TestCase
{
    public function test_registra_nuevamente_al_cliente_al_cancelar_su_credito_refinanciado(): void
    {
        config(['database.default' => 'lista_cancelacion_test', 'database.connections.lista_cancelacion_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('nombrecompleto');
        });
        Schema::create('s_listanegra', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idcliente');
            $table->string('motivo');
            $table->integer('idresponsable');
            $table->dateTime('fecharegistro');
            $table->integer('idtienda');
            $table->integer('idestado');
            $table->string('db_cliente');
        });
        DB::table('users')->insert(['id' => 10, 'nombrecompleto' => 'Pablo']);
        $credito = (object) ['idcliente' => 10, 'idtienda' => 1, 'idmodalidad_credito' => 4, 'cuenta' => 188];

        ListaNegraCredito::registrarCancelacionRefinanciado($credito, 20);
        $this->assertDatabaseHas('s_listanegra', [
            'idcliente' => 10, 'idresponsable' => 20,
            'motivo' => 'CANCELACIÓN DE CRÉDITO REFINANCIADO de la cuenta C00000188',
        ]);
        ListaNegraCredito::registrarCancelacionRefinanciado($credito, 20);
        $this->assertSame(1, DB::table('s_listanegra')->count());

        DB::table('s_listanegra')->delete();
        $credito->idmodalidad_credito = 1;
        ListaNegraCredito::registrarCancelacionRefinanciado($credito, 20);
        $this->assertSame(0, DB::table('s_listanegra')->count());
    }
}
