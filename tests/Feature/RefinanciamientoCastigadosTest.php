<?php

namespace Tests\Feature;

use App\Http\Controllers\Layouts\Backoffice\Sistema\RefinanciamientoCastigadosController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RefinanciamientoCastigadosTest extends TestCase
{
    public function test_busca_clientes_con_castigos_y_muestra_solo_cuentas_castigadas(): void
    {
        config(['database.default' => 'castigados_ref_test', 'database.connections.castigados_ref_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id'); $table->integer('idtienda'); $table->integer('idestado');
            $table->integer('idtipousuario'); $table->integer('idasesor');
            $table->string('nombrecompleto'); $table->string('identificacion'); $table->string('usuario')->default('');
        });
        Schema::create('credito', function (Blueprint $table) {
            foreach (['id', 'idtienda', 'idcliente', 'idasesor', 'idusuariocobranza', 'idcajero', 'idadministrador', 'idaval', 'idforma_pago_credito', 'idmodalidad_credito', 'idtipo_operacion_credito', 'idcredito_prendatario', 'idestadocredito', 'idcredito_refinanciado', 'idforma_credito', 'cuenta', 'cuotas'] as $campo) {
                $table->integer($campo)->default(0);
            }
            $table->string('estado'); $table->string('castigo_estado'); $table->string('fecha_desembolso');
            $table->decimal('monto_solicitado')->default(100);
        });
        DB::table('users')->insert([
            ['id' => 10, 'idtienda' => 1, 'idestado' => 1, 'idtipousuario' => 1, 'idasesor' => 0, 'nombrecompleto' => 'Cobranza Uno', 'identificacion' => ''],
            ['id' => 20, 'idtienda' => 1, 'idestado' => 1, 'idtipousuario' => 1, 'idasesor' => 0, 'nombrecompleto' => 'Cobranza Dos', 'identificacion' => ''],
            ['id' => 11, 'idtienda' => 1, 'idestado' => 1, 'idtipousuario' => 2, 'idasesor' => 0, 'nombrecompleto' => 'Ana Cliente', 'identificacion' => '123'],
            ['id' => 12, 'idtienda' => 1, 'idestado' => 1, 'idtipousuario' => 2, 'idasesor' => 0, 'nombrecompleto' => 'Ana Sin Castigo', 'identificacion' => '456'],
        ]);
        foreach (['forma_pago_credito', 'modalidad_credito', 'tipo_operacion_credito', 'credito_prendatario'] as $nombre) {
            Schema::create($nombre, function (Blueprint $table) { $table->integer('id'); $table->string('nombre')->default('Prueba'); });
            DB::table($nombre)->insert(['id' => 1]);
        }
        Schema::create('credito_formapago', fn (Blueprint $table) => $table->integer('idcredito'));
        foreach ([['id' => 1, 'cuenta' => 111, 'castigo_estado' => 'NINGUNO', 'idusuariocobranza' => 0],
            ['id' => 2, 'cuenta' => 222, 'castigo_estado' => 'CONFIRMADO', 'idusuariocobranza' => 10],
            ['id' => 3, 'cuenta' => 333, 'castigo_estado' => 'CONFIRMADO', 'idusuariocobranza' => 20]] as $credito) {
            DB::table('credito')->insert($credito + ['idtienda' => 1, 'idcliente' => 11, 'idestadocredito' => 1,
                'idforma_pago_credito' => 1, 'idmodalidad_credito' => 1, 'idtipo_operacion_credito' => 1,
                'idcredito_prendatario' => 1, 'estado' => 'DESEMBOLSADO', 'fecha_desembolso' => '2026-11-01']);
        }
        $controller = (new \ReflectionClass(RefinanciamientoCastigadosController::class))->newInstanceWithoutConstructor();
        $clientes = $controller->show(Request::create('/', 'GET', ['buscar' => 'Ana']), 1, 'clientes')->getData(true)['results'];
        $this->assertCount(1, $clientes);
        $this->assertSame('Cobranza Uno, Cobranza Dos', $clientes[0]['nombrecobranza']);
        $html = $controller->show(Request::create('/', 'GET', ['idagencia' => 1, 'idcliente' => 11]), 1, 'showtable')['html'];
        $this->assertStringContainsString('C222', $html);
        $this->assertStringContainsString('C333', $html);
        $this->assertStringNotContainsString('C111', $html);
    }
}
