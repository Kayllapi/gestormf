<?php

namespace Tests\Feature;

use App\Http\Controllers\Layouts\Backoffice\Sistema\RefinanciamientoController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RefinanciamientoFiltrosTest extends TestCase
{
    public function test_busqueda_se_limita_a_clientes_activos_de_la_agencia_y_devuelve_su_ejecutivo(): void
    {
        config(['database.default' => 'refinanciamiento_test', 'database.connections.refinanciamiento_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id'); $table->integer('idtienda'); $table->integer('idestado');
            $table->integer('idtipousuario'); $table->integer('idasesor');
            $table->string('nombrecompleto'); $table->string('identificacion');
            $table->string('usuario')->default('');
        });
        Schema::create('s_listanegra', fn (Blueprint $table) => $table->integer('idcliente'));
        Schema::create('credito', function (Blueprint $table) {
            foreach (['id', 'idtienda', 'idcliente', 'idasesor', 'idcajero', 'idadministrador', 'idaval', 'idforma_pago_credito', 'idmodalidad_credito', 'idtipo_operacion_credito', 'idcredito_prendatario', 'idestadocredito', 'idcredito_refinanciado', 'idforma_credito', 'cuenta', 'cuotas'] as $campo) {
                $table->integer($campo)->default(0);
            }
            $table->string('estado'); $table->string('castigo_estado'); $table->string('fecha_desembolso');
            $table->decimal('monto_solicitado')->default(100);
        });
        DB::table('users')->insert([
            ['id' => 10, 'idtienda' => 1, 'idestado' => 1, 'idtipousuario' => 1, 'idasesor' => 0, 'nombrecompleto' => 'Ejecutivo Uno', 'identificacion' => ''],
            ['id' => 11, 'idtienda' => 1, 'idestado' => 1, 'idtipousuario' => 2, 'idasesor' => 10, 'nombrecompleto' => 'Ana Cliente', 'identificacion' => '123'],
            ['id' => 12, 'idtienda' => 2, 'idestado' => 1, 'idtipousuario' => 2, 'idasesor' => 10, 'nombrecompleto' => 'Ana Otra', 'identificacion' => '456'],
            ['id' => 13, 'idtienda' => 1, 'idestado' => 0, 'idtipousuario' => 2, 'idasesor' => 10, 'nombrecompleto' => 'Ana Inactiva', 'identificacion' => '789'],
        ]);
        $controller = (new \ReflectionClass(RefinanciamientoController::class))->newInstanceWithoutConstructor();
        $resultado = $controller->show(Request::create('/', 'GET', ['buscar' => 'Ana']), 1, 'clientes')->getData(true)['results'];
        $this->assertCount(1, $resultado);
        $this->assertSame(11, $resultado[0]['id']);
        $this->assertSame(10, $resultado[0]['idasesor']);
        $this->assertSame('Ejecutivo Uno', $resultado[0]['nombreasesor']);
        $this->assertFalse($resultado[0]['observado']);
        DB::table('s_listanegra')->insert(['idcliente' => 11]);
        $observado = $controller->show(Request::create('/', 'GET', ['buscar' => 'Ana']), 1, 'clientes')->getData(true)['results'];
        $this->assertTrue($observado[0]['observado']);
        $this->assertFalse($observado[0]['tiene_creditos_activos']);
        DB::table('users')->insert(['id' => 14, 'idtienda' => 1, 'idestado' => 1, 'idtipousuario' => 2,
            'idasesor' => 20, 'nombrecompleto' => 'Ana Otro Ejecutivo', 'identificacion' => '321']);
        \Illuminate\Support\Facades\Auth::shouldReceive('id')->andReturn(10);
        $asesor = (new \ReflectionClass(\App\Http\Controllers\Layouts\Backoffice\Sistema\RefinanciamientoAsesorController::class))->newInstanceWithoutConstructor();
        $resultadoAsesor = $asesor->show(Request::create('/', 'GET', ['buscar' => 'Ana', 'idasesor' => 20]), 1, 'clientes')->getData(true)['results'];
        $this->assertCount(1, $resultadoAsesor);
        $this->assertSame(11, $resultadoAsesor[0]['id']);

        foreach (['forma_pago_credito', 'modalidad_credito', 'tipo_operacion_credito', 'credito_prendatario'] as $nombre) {
            Schema::create($nombre, function (Blueprint $table) {
                $table->integer('id'); $table->string('nombre')->default('Prueba');
            });
            DB::table($nombre)->insert(['id' => 1]);
        }
        Schema::create('credito_formapago', fn (Blueprint $table) => $table->integer('idcredito'));
        foreach ([['id' => 1, 'cuenta' => 111, 'castigo_estado' => 'NINGUNO'], ['id' => 2, 'cuenta' => 222, 'castigo_estado' => 'CONFIRMADO']] as $credito) {
            DB::table('credito')->insert($credito + ['idtienda' => 1, 'idcliente' => 11, 'idasesor' => 99, 'idestadocredito' => 1,
                'idforma_pago_credito' => 1, 'idmodalidad_credito' => 1, 'idtipo_operacion_credito' => 1, 'idcredito_prendatario' => 1,
                'estado' => 'DESEMBOLSADO', 'fecha_desembolso' => '2026-11-01']);
        }
        $observadoConCredito = $controller->show(Request::create('/', 'GET', ['buscar' => 'Ana Cliente']), 1, 'clientes')->getData(true)['results'][0];
        $this->assertTrue($observadoConCredito['observado']);
        $this->assertTrue($observadoConCredito['tiene_creditos_activos']);
        DB::table('credito')->where('id', 1)->update(['castigo_estado' => 'CONFIRMADO']);
        $soloCastigados = $controller->show(Request::create('/', 'GET', ['buscar' => 'Ana Cliente']), 1, 'clientes')->getData(true)['results'][0];
        $this->assertFalse($soloCastigados['tiene_creditos_activos']);
        DB::table('credito')->where('id', 1)->update(['castigo_estado' => 'NINGUNO']);
        $html = $controller->show(Request::create('/', 'GET', ['idagencia' => 1, 'idcliente' => 11, 'idasesor' => 10]), 1, 'showtable')['html'];
        $this->assertStringContainsString('C111', $html);
        $this->assertStringNotContainsString('C222', $html);
    }
}
