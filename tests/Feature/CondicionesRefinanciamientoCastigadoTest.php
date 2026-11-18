<?php

namespace Tests\Feature;

use App\Http\Controllers\Layouts\Backoffice\Sistema\RefinanciamientoCastigadosController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ControladorRefinanciamientoCastigadoPrueba extends RefinanciamientoCastigadosController
{
    public bool $sinCronograma = false;

    public function __construct() {}

    protected function crearPropuesta(Request $request, $idtienda, $id)
    {
        DB::table('credito')->insert([
            'id' => 2, 'idtienda' => $idtienda, 'idcliente' => 5, 'estado' => 'PROCESO',
            'idestadocredito' => 1, 'castigo_estado' => 'NINGUNO', 'idcredito_prendatario' => 3,
            'idcredito_refinanciado' => $id, 'es_refinanciamiento_castigado' => true,
            'monto_solicitado' => $request->monto_solicitado, 'fecha_desembolso' => now()->toDateString(),
        ]);
        if (!$this->sinCronograma) {
            DB::table('credito_cronograma')->insert(['idcredito' => 2, 'cuota_real' => 450]);
        }
        return response()->json(['resultado' => 'CORRECTO', 'idcredito' => 2]);
    }
}

class CondicionesRefinanciamientoCastigadoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'condiciones_ref_test', 'database.connections.condiciones_ref_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('users', function (Blueprint $t) {
            $t->integer('id')->primary(); $t->integer('idestado'); $t->integer('idestadousuario');
            $t->integer('idtipousuario'); $t->boolean('es_master')->default(false); $t->string('clave');
        });
        Schema::create('permiso', function (Blueprint $t) { $t->integer('id'); $t->integer('rango'); });
        Schema::create('users_permiso', function (Blueprint $t) {
            $t->integer('idusers'); $t->integer('idtienda'); $t->integer('idpermiso'); $t->integer('idsession');
        });
        Schema::create('credito_prendatario', function (Blueprint $t) { $t->integer('id'); $t->string('modalidad'); });
        Schema::create('diasdegracia', function (Blueprint $t) { $t->string('nombre'); $t->integer('dias'); });
        Schema::create('credito', function (Blueprint $t) {
            $t->integer('id')->primary(); $t->integer('idtienda'); $t->string('estado');
            $t->integer('idestadocredito'); $t->string('castigo_estado'); $t->integer('idcredito_prendatario'); $t->integer('idforma_credito')->default(2);
            $t->integer('idcredito_refinanciado')->default(0);
            $t->integer('idcliente')->default(5); $t->integer('cuenta')->default(0); $t->boolean('es_refinanciamiento_castigado')->default(false);
            $t->dateTime('fecha_aprobacion')->nullable(); $t->integer('idadministrador')->nullable();
            $t->integer('castigo_responsable')->nullable(); $t->dateTime('castigo_confirmado_at')->nullable();
            $t->integer('idusuariocobranza')->nullable(); $t->dateTime('fecha_cancelado')->nullable(); $t->dateTime('fecha_desembolso')->nullable();
            $t->decimal('monto_solicitado')->default(500); $t->decimal('saldo_pendientepago')->default(400); $t->decimal('total_pendientepago')->default(450);
        });
        Schema::create('credito_cronograma', function (Blueprint $t) { $t->integer('idcredito'); $t->decimal('cuota_real'); });
        Schema::create('secuencia_cuentas_credito', function (Blueprint $t) { $t->integer('id')->primary(); $t->integer('ultimo'); });
        Schema::create('refinanciamiento_castigado_acuerdos', function (Blueprint $t) {
            $t->increments('id'); $t->integer('idcredito_original')->unique(); $t->integer('idcredito_nuevo')->unique();
            $t->integer('idresponsable'); $t->integer('idsolicitante'); $t->decimal('deuda_anterior');
            $t->decimal('monto_acordado'); $t->decimal('ajuste'); $t->timestamps();
        });
        DB::table('secuencia_cuentas_credito')->insert(['id' => 1, 'ultimo' => 0]);
        DB::table('users')->insert(['id' => 10, 'idestado' => 1, 'idestadousuario' => 1, 'idtipousuario' => 1, 'clave' => 'secreto']);
        DB::table('permiso')->insert(['id' => 2, 'rango' => 1]);
        DB::table('users_permiso')->insert(['idusers' => 10, 'idtienda' => 1, 'idpermiso' => 2, 'idsession' => 2]);
        DB::table('credito_prendatario')->insert(['id' => 3, 'modalidad' => 'Interes Simple']);
        DB::table('diasdegracia')->insert(['nombre' => 'NOPRENDARIA', 'dias' => 30]);
        DB::table('credito')->insert(['id' => 1, 'idtienda' => 1, 'estado' => 'DESEMBOLSADO',
            'idestadocredito' => 1, 'castigo_estado' => 'CONFIRMADO', 'idcredito_prendatario' => 3]);
    }

    private function solicitud(array $cambios = []): Request
    {
        return Request::create('/', 'PUT', array_merge([
            'view' => 'refinanciamiento', 'idresponsable' => 10, 'responsableclave' => 'secreto',
            'monto_solicitado' => '400.00', 'idforma_pago_credito' => 4, 'cuotas' => 6,
            'tasa_tem' => '2.5', 'dia_gracia' => 0, 'cargo' => 0, 'tipotasa' => 2,
        ], $cambios));
    }

    public function test_autorizacion_valida_activa_el_refinanciamiento_en_la_misma_transaccion(): void
    {
        \Illuminate\Support\Facades\Auth::shouldReceive('id')->once()->andReturn(50);
        $cobranza = \Mockery::mock(\App\Http\Controllers\Layouts\Backoffice\Sistema\CobranzacuotaController::class);
        $cobranza->shouldReceive('totales_pago_total')->once()->with(1, 1)->andReturn(['totalapagar' => '450.00']);
        $this->app->instance(\App\Http\Controllers\Layouts\Backoffice\Sistema\CobranzacuotaController::class, $cobranza);
        $resultado = (new ControladorRefinanciamientoCastigadoPrueba)->update($this->solicitud(), 1, 1)->getData(true);
        $this->assertSame('CORRECTO', $resultado['resultado']);
        $this->assertSame(2, DB::table('credito')->where('id', 1)->value('idestadocredito'));
        $this->assertDatabaseHas('credito', ['id' => 2, 'estado' => 'DESEMBOLSADO', 'castigo_estado' => 'CONFIRMADO', 'idadministrador' => 10]);
        $this->assertDatabaseHas('refinanciamiento_castigado_acuerdos', ['idcredito_original' => 1, 'idcredito_nuevo' => 2, 'ajuste' => -50]);
    }

    public function test_si_falla_la_activacion_no_queda_propuesta_ni_cierre(): void
    {
        \Illuminate\Support\Facades\Auth::shouldReceive('id')->once()->andReturn(50);
        $cobranza = \Mockery::mock(\App\Http\Controllers\Layouts\Backoffice\Sistema\CobranzacuotaController::class);
        $cobranza->shouldReceive('totales_pago_total')->once()->andReturn(['totalapagar' => '450.00']);
        $this->app->instance(\App\Http\Controllers\Layouts\Backoffice\Sistema\CobranzacuotaController::class, $cobranza);
        $controller = new ControladorRefinanciamientoCastigadoPrueba;
        $controller->sinCronograma = true;
        try {
            $controller->update($this->solicitud(), 1, 1);
            $this->fail('Debió rechazar el cronograma ausente.');
        } catch (ValidationException $e) {
            $this->assertDatabaseMissing('credito', ['id' => 2]);
            $this->assertSame(1, DB::table('credito')->where('id', 1)->value('idestadocredito'));
            $this->assertSame(0, DB::table('refinanciamiento_castigado_acuerdos')->count());
        }
    }

    public function test_rechaza_condiciones_invalidas_o_autorizacion_incorrecta(): void
    {
        foreach ([['responsableclave' => 'incorrecta'], ['monto_solicitado' => '0'],
            ['cuotas' => 0], ['idforma_pago_credito' => 9], ['dia_gracia' => 31]] as $datos) {
            try {
                (new ControladorRefinanciamientoCastigadoPrueba)->update($this->solicitud($datos), 1, 1);
                $this->fail('Debió rechazar la solicitud.');
            } catch (ValidationException $e) {
                $this->assertNotEmpty($e->errors());
            }
        }
    }

    public function test_rechaza_cuenta_cerrada_o_ya_refinanciada(): void
    {
        DB::table('credito')->insert(['id' => 2, 'idtienda' => 1, 'estado' => 'PROCESO',
            'idestadocredito' => 1, 'castigo_estado' => 'NINGUNO', 'idcredito_prendatario' => 3, 'idcredito_refinanciado' => 1]);
        try {
            (new ControladorRefinanciamientoCastigadoPrueba)->update($this->solicitud(), 1, 1);
            $this->fail('Debió rechazar el refinanciamiento duplicado.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('credito', $e->errors());
        }
        DB::table('credito')->where('id', 2)->delete();
        DB::table('credito')->where('id', 1)->update(['idestadocredito' => 2]);
        $this->expectException(ValidationException::class);
        (new ControladorRefinanciamientoCastigadoPrueba)->update($this->solicitud(), 1, 1);
    }
}
