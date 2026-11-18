<?php

namespace Tests\Feature;

use App\Services\AutorizacionRefinanciamientoCastigado;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AutorizacionRefinanciamientoCastigadoTest extends TestCase
{
    public function test_autoriza_unicamente_al_responsable_activo_de_la_agencia_con_clave_correcta(): void
    {
        config(['database.default' => 'autorizacion_ref_test', 'database.connections.autorizacion_ref_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id')->primary(); $table->integer('idestado'); $table->integer('idestadousuario');
            $table->integer('idtipousuario'); $table->boolean('es_master')->default(false); $table->string('clave');
        });
        Schema::create('permiso', function (Blueprint $table) { $table->integer('id'); $table->integer('rango'); });
        Schema::create('users_permiso', function (Blueprint $table) {
            $table->integer('idusers'); $table->integer('idtienda'); $table->integer('idpermiso'); $table->integer('idsession');
        });
        DB::table('users')->insert(['id' => 10, 'idestado' => 1, 'idestadousuario' => 1, 'idtipousuario' => 1, 'clave' => 'secreto']);
        DB::table('permiso')->insert(['id' => 2, 'rango' => 1]);
        DB::table('users_permiso')->insert(['idusers' => 10, 'idtienda' => 1, 'idpermiso' => 2, 'idsession' => 2]);
        AutorizacionRefinanciamientoCastigado::verificar(10, 1, 'secreto');
        $this->assertTrue(true);

        foreach ([[10, 1, 'incorrecta'], [10, 2, 'secreto'], [999, 1, 'secreto']] as [$usuario, $agencia, $clave]) {
            try {
                AutorizacionRefinanciamientoCastigado::verificar($usuario, $agencia, $clave);
                $this->fail('La autorización debía ser rechazada.');
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('responsableclave', $e->errors());
            }
        }
        DB::table('users')->where('id', 10)->update(['idestadousuario' => 2]);
        $this->expectException(ValidationException::class);
        AutorizacionRefinanciamientoCastigado::verificar(10, 1, 'secreto');
    }
}
