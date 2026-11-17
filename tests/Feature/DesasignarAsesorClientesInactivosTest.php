<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DesasignarAsesorClientesInactivosTest extends TestCase
{
    public function test_desasigna_solo_clientes_inactivos_de_la_agencia_indicada(): void
    {
        config(['database.default' => 'inactivos_test', 'database.connections.inactivos_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo')->default('C');
            $table->string('identificacion')->default('');
            $table->string('nombrecompleto')->default('Cliente');
            $table->integer('idtienda')->default(1);
            $table->integer('idestado')->default(1);
            $table->integer('idtipousuario')->default(2);
            $table->integer('idasesor')->default(5);
            $table->string('origen_global')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('fechamodificacion')->nullable();
        });
        Schema::create('credito', function (Blueprint $table) {
            $table->integer('idcliente');
            $table->integer('idaval')->default(0);
            $table->dateTime('fecha')->nullable();
            $table->string('estado');
        });
        foreach (range(1, 7) as $id) {
            DB::table('users')->insert(['id' => $id, 'created_at' => now()->subDays(120),
                'idtienda' => $id === 3 ? 2 : 1, 'idestado' => $id === 4 ? 0 : 1,
                'idtipousuario' => $id === 5 ? 1 : 2]);
        }
        foreach ([1, 3, 4, 5, 6, 7] as $id) {
            DB::table('credito')->insert(['idcliente' => $id, 'fecha' => now()->subDays($id === 6 ? 10 : 100),
                'estado' => $id === 7 ? 'ELIMINADO' : 'ACTIVO']);
        }
        $this->artisan('clientes:desasignar-asesor', ['--incluir-sin-credito' => true, '--tienda' => 1])->assertSuccessful();
        $this->assertEquals([1, 2, 7], DB::table('users')->where('idasesor', 0)->orderBy('id')->pluck('id')->all());
        $this->assertSame(3, DB::table('users')->where('origen_global', 'INACTIVIDAD')->count());
    }
}
