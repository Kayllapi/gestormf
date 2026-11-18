<?php

namespace Tests\Feature;

use App\Services\CierreRefinanciamientoCastigado;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CierreRefinanciamientoCastigadoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'cierre_ref_test', 'database.connections.cierre_ref_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('credito', function (Blueprint $t) {
            $t->integer('id')->primary(); $t->integer('idtienda'); $t->integer('idcliente');
            $t->integer('idasesor')->default(50);
            $t->integer('idcredito_refinanciado')->default(0); $t->integer('cuenta')->default(0);
            $t->boolean('es_refinanciamiento_castigado')->default(false); $t->dateTime('fecha_desembolso')->nullable();
            $t->integer('refinanciamiento_castigado_responsable')->nullable();
            $t->integer('cuotas')->default(2); $t->integer('idforma_pago_credito')->default(4); $t->integer('dia_gracia')->default(0);
            $t->date('fecha_primerpago')->nullable(); $t->date('fecha_ultimopago')->nullable();
            $t->string('estado'); $t->integer('idestadocredito'); $t->string('castigo_estado');
            $t->integer('castigo_responsable')->nullable(); $t->dateTime('castigo_confirmado_at')->nullable();
            $t->integer('idusuariocobranza')->nullable(); $t->dateTime('fecha_cancelado')->nullable();
            $t->decimal('monto_solicitado'); $t->decimal('saldo_pendientepago')->default(0); $t->decimal('total_pendientepago')->default(0);
        });
        Schema::create('secuencia_cuentas_credito', function (Blueprint $t) { $t->integer('id')->primary(); $t->integer('ultimo'); });
        Schema::create('credito_cronograma', function (Blueprint $t) {
            $t->increments('id'); $t->integer('idcredito'); $t->integer('numerocuota');
            $t->integer('idestadocredito_cronograma')->default(1); $t->date('fechapago')->nullable(); $t->decimal('cuota_real');
        });
        Schema::create('feriados', fn (Blueprint $t) => $t->date('fecha_feriado'));
        Schema::create('refinanciamiento_castigado_acuerdos', function (Blueprint $t) {
            $t->increments('id'); $t->integer('idcredito_original')->unique(); $t->integer('idcredito_nuevo')->unique();
            $t->integer('idresponsable'); $t->integer('idsolicitante'); $t->decimal('deuda_anterior');
            $t->decimal('monto_acordado'); $t->decimal('ajuste'); $t->timestamps();
        });
        DB::table('secuencia_cuentas_credito')->insert(['id' => 1, 'ultimo' => 0]);
        DB::table('credito_cronograma')->insert([['idcredito' => 2, 'numerocuota' => 1, 'cuota_real' => 220], ['idcredito' => 2, 'numerocuota' => 2, 'cuota_real' => 230]]);
        DB::table('credito')->insert([
            ['id' => 1, 'idtienda' => 1, 'idcliente' => 10, 'idcredito_refinanciado' => 0, 'cuenta' => 20,
                'estado' => 'DESEMBOLSADO', 'idestadocredito' => 1, 'castigo_estado' => 'CONFIRMADO', 'es_refinanciamiento_castigado' => false,
                'idusuariocobranza' => 30, 'monto_solicitado' => 500, 'saldo_pendientepago' => 400, 'total_pendientepago' => 450],
            ['id' => 2, 'idtienda' => 1, 'idcliente' => 10, 'idcredito_refinanciado' => 1, 'cuenta' => 0,
                'estado' => 'APROBADO', 'idestadocredito' => 1, 'castigo_estado' => 'NINGUNO', 'es_refinanciamiento_castigado' => true,
                'idusuariocobranza' => null, 'monto_solicitado' => 420, 'saldo_pendientepago' => 420, 'total_pendientepago' => 420],
        ]);
    }

    public function test_cierra_la_cuenta_y_registra_el_ajuste_sin_pago(): void
    {
        $cuenta = DB::transaction(fn () => CierreRefinanciamientoCastigado::finalizar(1, 2, 1, 40, 50, '450.00'));
        $this->assertSame(21, $cuenta);
        $this->assertDatabaseHas('credito', ['id' => 1, 'idestadocredito' => 2, 'saldo_pendientepago' => 0]);
        $this->assertDatabaseHas('credito', ['id' => 2, 'cuenta' => 21, 'castigo_estado' => 'CONFIRMADO', 'idusuariocobranza' => 30, 'saldo_pendientepago' => 420, 'total_pendientepago' => 450]);
        $this->assertDatabaseHas('refinanciamiento_castigado_acuerdos', ['idcredito_original' => 1, 'idcredito_nuevo' => 2, 'ajuste' => -30]);
    }

    public function test_rechaza_un_acuerdo_sin_cronograma(): void
    {
        DB::table('credito_cronograma')->delete();
        $this->expectException(ValidationException::class);
        DB::transaction(fn () => CierreRefinanciamientoCastigado::finalizar(1, 2, 1, 40, 50, '450.00'));
    }

    public function test_no_activa_una_propuesta_que_aun_esta_en_proceso(): void
    {
        DB::table('credito')->where('id', 2)->update(['estado' => 'PROCESO']);
        $this->expectException(ValidationException::class);
        DB::transaction(fn () => CierreRefinanciamientoCastigado::finalizar(1, 2, 1, 40, 50, '450.00'));
    }

    public function test_rechaza_cuenta_cerrada_y_no_modifica_la_propuesta(): void
    {
        DB::table('credito')->where('id', 1)->update(['idestadocredito' => 2]);
        try {
            DB::transaction(fn () => CierreRefinanciamientoCastigado::finalizar(1, 2, 1, 40, 50, '450.00'));
            $this->fail('Debía rechazar el acuerdo.');
        } catch (ValidationException $e) {
            $this->assertSame('APROBADO', DB::table('credito')->where('id', 2)->value('estado'));
            $this->assertSame(0, DB::table('refinanciamiento_castigado_acuerdos')->count());
        }
    }

    public function test_un_error_posterior_revierte_el_cierre_la_cuenta_y_el_acuerdo(): void
    {
        try {
            DB::transaction(function () {
                CierreRefinanciamientoCastigado::finalizar(1, 2, 1, 40, 50, '450.00');
                throw new \RuntimeException('Fallo simulado posterior al cierre');
            });
            $this->fail('La transacción debía fallar.');
        } catch (\RuntimeException $e) {
            $this->assertSame('Fallo simulado posterior al cierre', $e->getMessage());
        }

        $this->assertDatabaseHas('credito', ['id' => 1, 'idestadocredito' => 1, 'saldo_pendientepago' => 400]);
        $this->assertDatabaseHas('credito', ['id' => 2, 'estado' => 'APROBADO', 'cuenta' => 0, 'castigo_estado' => 'NINGUNO']);
        $this->assertSame(0, DB::table('refinanciamiento_castigado_acuerdos')->count());
        $this->assertSame(0, DB::table('secuencia_cuentas_credito')->where('id', 1)->value('ultimo'));
    }

    public function test_el_cierre_no_puede_ejecutarse_fuera_de_una_transaccion(): void
    {
        $this->expectException(\LogicException::class);
        CierreRefinanciamientoCastigado::finalizar(1, 2, 1, 40, 50, '450.00');
    }

    public function test_calcula_el_ajuste_con_el_total_vigente_de_cobranza(): void
    {
        $cobranza = \Mockery::mock(\App\Http\Controllers\Layouts\Backoffice\Sistema\CobranzacuotaController::class);
        $cobranza->shouldReceive('totales_pago_total')->once()->with(1, 1)->andReturn(['totalapagar' => '431.25']);
        $this->app->instance(\App\Http\Controllers\Layouts\Backoffice\Sistema\CobranzacuotaController::class, $cobranza);

        DB::transaction(fn () => CierreRefinanciamientoCastigado::finalizarConDeudaActual(1, 2, 1, 40, 50));
        $this->assertDatabaseHas('refinanciamiento_castigado_acuerdos', [
            'idcredito_original' => 1, 'deuda_anterior' => 431.25, 'monto_acordado' => 420, 'ajuste' => -11.25,
        ]);
    }

    public function test_desembolso_de_sustitucion_no_necesita_caja_ni_genera_pago(): void
    {
        DB::table('credito')->where('id', 2)->update(['refinanciamiento_castigado_responsable' => 40]);
        $cobranza = \Mockery::mock(\App\Http\Controllers\Layouts\Backoffice\Sistema\CobranzacuotaController::class);
        $cobranza->shouldReceive('totales_pago_total')->once()->with(1, 1)->andReturn(['totalapagar' => '431.25']);
        $this->app->instance(\App\Http\Controllers\Layouts\Backoffice\Sistema\CobranzacuotaController::class, $cobranza);

        $respuesta = (new \App\Http\Controllers\Layouts\Backoffice\Sistema\DesembolsoController)->update(
            \Illuminate\Http\Request::create('/', 'PUT', ['view' => 'realizar_desembolo']), 1, 2
        )->getData(true);
        $this->assertSame('CORRECTO', $respuesta['resultado']);
        $this->assertStringContainsString('sin movimiento de caja', $respuesta['mensaje']);
        $this->assertDatabaseHas('credito', ['id' => 2, 'cuenta' => 21, 'castigo_estado' => 'CONFIRMADO']);
        $this->assertDatabaseHas('refinanciamiento_castigado_acuerdos', ['idcredito_original' => 1, 'idcredito_nuevo' => 2]);
    }

    public function test_reprograma_las_cuotas_si_el_desembolso_es_en_otra_fecha(): void
    {
        \Carbon\Carbon::setTestNow('2026-11-17 10:00:00');
        try {
            DB::table('credito')->where('id', 2)->update(['fecha_desembolso' => '2026-11-01']);
            DB::transaction(fn () => CierreRefinanciamientoCastigado::finalizar(1, 2, 1, 40, 50, '450.00'));
            $this->assertSame(['2026-12-17', '2027-01-18'], DB::table('credito_cronograma')->orderBy('numerocuota')->pluck('fechapago')->all());
            $this->assertDatabaseHas('credito', ['id' => 2, 'fecha_primerpago' => '2026-12-17', 'fecha_ultimopago' => '2027-01-18']);
        } finally {
            \Carbon\Carbon::setTestNow();
        }
    }
}
