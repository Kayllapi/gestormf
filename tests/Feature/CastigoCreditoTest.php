<?php

namespace Tests\Feature;

use App\Services\CastigoCredito;
use App\Http\Controllers\Layouts\Backoffice\Sistema\CarteraCreditoController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class CastigoCreditoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'castigo_test', 'database.connections.castigo_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('users', function (Blueprint $t) {
            $t->integer('id')->primary(); $t->integer('idasesor'); $t->string('origen_global')->nullable();
            $t->integer('idestado')->default(1); $t->string('clave')->default('secreto');
        });
        Schema::create('permiso', function (Blueprint $t) { $t->integer('id'); $t->integer('rango'); });
        Schema::create('users_permiso', function (Blueprint $t) {
            $t->integer('idusers'); $t->integer('idtienda'); $t->integer('idpermiso'); $t->integer('idsession');
        });
        Schema::create('credito', function (Blueprint $t) {
            $t->integer('id')->primary(); $t->integer('idcliente'); $t->integer('idasesor'); $t->integer('idtienda');
            $t->string('estado'); $t->integer('idestadocredito');
            $t->decimal('saldo_pendientepago', 12, 2); $t->decimal('total_pendientepago', 12, 2);
        });
        $migration = require database_path('migrations/2026_10_27_000001_add_castigo_to_credito.php');
        $migration->up();
        DB::table('users')->insert([['id' => 1, 'idasesor' => 10], ['id' => 20, 'idasesor' => 0]]);
        DB::table('permiso')->insert([['id' => 2, 'rango' => 1], ['id' => 1, 'rango' => 3]]);
        DB::table('users')->insert(['id' => 40, 'idasesor' => 0]);
        DB::table('users_permiso')->insert(['idusers' => 40, 'idtienda' => 1, 'idpermiso' => 1, 'idsession' => 2]);
        DB::table('users_permiso')->insert(['idusers' => 20, 'idtienda' => 1, 'idpermiso' => 2, 'idsession' => 2]);
    }

    private function cuenta(int $id, array $extra = []): void
    {
        DB::table('credito')->insert(array_merge([
            'id' => $id, 'idcliente' => 1, 'idasesor' => 10, 'idtienda' => 1,
            'estado' => 'DESEMBOLSADO', 'idestadocredito' => 1,
            'saldo_pendientepago' => 100, 'total_pendientepago' => 120,
        ], $extra));
    }

    public function test_lote_confirmado_libera_cliente_y_conserva_saldos(): void
    {
        $this->cuenta(1); $this->cuenta(2, ['idasesor' => 11]);
        $s = new CastigoCredito;
        $s->procesar([1, 2], 40, 1, false);
        $this->assertEquals(10, DB::table('users')->where('id', 1)->value('idasesor'));
        $s->procesar([1, 2], 20, 1, true);
        $this->assertEquals(0, DB::table('users')->where('id', 1)->value('idasesor'));
        $this->assertEquals('CASTIGADO', DB::table('users')->where('id', 1)->value('origen_global'));
        $this->assertEquals(2, DB::table('credito')->where('castigo_estado', 'CONFIRMADO')->count());
        $this->assertEquals(240, DB::table('credito')->sum('total_pendientepago'));
        $this->assertEquals(20, DB::table('credito')->where('id', 1)->value('castigo_responsable'));
        $this->expectException(ValidationException::class);
        $s->procesar([1], 20, 1, true);
    }

    public function test_otro_credito_con_deuda_en_otra_agencia_conserva_asesor(): void
    {
        $this->cuenta(1); $this->cuenta(2, ['idtienda' => 2, 'saldo_pendientepago' => 0, 'total_pendientepago' => 10]);
        $s = new CastigoCredito;
        $s->procesar([1], 40, 1, false); $s->procesar([1], 20, 1, true);
        $this->assertEquals(10, DB::table('users')->where('id', 1)->value('idasesor'));
    }

    public function test_lote_invalido_no_solicita_ninguna_cuenta(): void
    {
        $this->cuenta(1); $this->cuenta(2, ['idtienda' => 2]);
        try { (new CastigoCredito)->procesar([1, 2], 40, 1, false); $this->fail(); }
        catch (HttpException $e) {
            $this->assertEquals(2, DB::table('credito')->where('castigo_estado', 'NINGUNO')->count());
        }
    }

    public function test_no_gerente_no_confirma(): void
    {
        $this->cuenta(1, ['castigo_estado' => 'PENDIENTE']);
        $this->expectException(HttpException::class);
        (new CastigoCredito)->procesar([1], 10, 1, true);
    }

    public function test_sin_deuda_no_se_solicita(): void
    {
        $this->cuenta(1, ['saldo_pendientepago' => 0, 'total_pendientepago' => 0]);
        $this->expectException(ValidationException::class);
        (new CastigoCredito)->procesar([1], 40, 1, false);
    }

    public function test_password_correcto_confirma_desde_controlador(): void
    {
        $this->cuenta(1, ['castigo_estado' => 'PENDIENTE']);
        Auth::shouldReceive('id')->andReturn(20);
        Auth::shouldReceive('check')->andReturn(true);
        $request = Request::create('/', 'POST', ['view' => 'confirmar_castigo', 'creditos' => [1], 'idresponsable' => 20, 'responsableclave' => 'secreto']);
        $response = (new CarteraCreditoController)->store($request, 1);
        $this->assertEquals('CORRECTO', $response->getData(true)['resultado']);
        $this->assertEquals('CONFIRMADO', DB::table('credito')->value('castigo_estado'));
    }

    public function test_creditos_cancelados_y_sin_deuda_no_retienen_cliente(): void
    {
        $this->cuenta(1, ['castigo_estado' => 'PENDIENTE']);
        $this->cuenta(2, ['estado' => 'CANCELADO']);
        $this->cuenta(3, ['saldo_pendientepago' => 0, 'total_pendientepago' => 0]);
        (new CastigoCredito)->procesar([1], 20, 1, true);
        $this->assertEquals(0, DB::table('users')->where('id', 1)->value('idasesor'));
    }
    public function test_administrador_confirma_con_su_password(): void
    {

        DB::table('users_permiso')->where('idusers', 20)->update(['idpermiso' => 1]);
        $this->test_password_correcto_confirma_desde_controlador();
    }

    public function test_administrador_de_otra_agencia_no_confirma(): void
    {

        DB::table('users_permiso')->where('idusers', 20)->update(['idpermiso' => 1, 'idtienda' => 2]);
        $this->cuenta(1, ['castigo_estado' => 'PENDIENTE']);
        $this->expectException(HttpException::class);
        (new CastigoCredito)->procesar([1], 20, 2, true);
    }
    public function test_responsable_elegido_autoriza_y_queda_registrado(): void
    {
        DB::table('users')->insert(['id' => 30, 'idasesor' => 0, 'clave' => 'clave-elegido']);
        DB::table('users_permiso')->insert(['idusers' => 30, 'idtienda' => 1, 'idpermiso' => 2, 'idsession' => 1]);
        $this->cuenta(1, ['castigo_estado' => 'PENDIENTE']);
        Auth::shouldReceive('id')->andReturn(20);
        Auth::shouldReceive('check')->andReturn(true);
        $request = Request::create('/', 'POST', ['view' => 'confirmar_castigo', 'creditos' => [1], 'idresponsable' => 30, 'responsableclave' => 'clave-elegido']);
        $response = (new CarteraCreditoController)->store($request, 1);
        $this->assertEquals('CORRECTO', $response->getData(true)['resultado']);
        $this->assertEquals(30, DB::table('credito')->value('castigo_responsable'));
    }

    public function test_responsable_elegido_sin_cargo_autorizado_es_rechazado(): void
    {
        DB::table('users')->insert(['id' => 30, 'idasesor' => 0]);
        $this->cuenta(1, ['castigo_estado' => 'PENDIENTE']);
        Auth::shouldReceive('id')->andReturn(20);
        Auth::shouldReceive('check')->andReturn(true);
        $request = Request::create('/', 'POST', ['view' => 'confirmar_castigo', 'creditos' => [1], 'idresponsable' => 30, 'responsableclave' => 'secreto']);
        $this->expectException(HttpException::class);
        (new CarteraCreditoController)->store($request, 1);
    }
    public function test_password_incorrecto_no_confirma(): void
    {
        $this->cuenta(1, ['castigo_estado' => 'PENDIENTE']);
        Auth::shouldReceive('id')->andReturn(20);
        Auth::shouldReceive('check')->andReturn(true);
        $request = Request::create('/', 'POST', ['view' => 'confirmar_castigo', 'creditos' => [1], 'idresponsable' => 20, 'responsableclave' => 'incorrecta']);
        try { (new CarteraCreditoController)->store($request, 1); $this->fail(); }
        catch (ValidationException $e) {
            $this->assertArrayHasKey('responsableclave', $e->errors());
            $this->assertEquals('PENDIENTE', DB::table('credito')->value('castigo_estado'));
        }
    }
    public function test_asesor_no_puede_solicitar(): void
    {
        $this->cuenta(1);
        $this->expectException(HttpException::class);
        (new CastigoCredito)->procesar([1], 10, 1, false);
    }

    public function test_solicitud_exige_clave_y_registra_administrador(): void
    {
        $this->cuenta(1);
        Auth::shouldReceive('id')->andReturn(40);
        Auth::shouldReceive('check')->andReturn(true);
        $request = Request::create('/', 'POST', ['view' => 'solicitar_castigo', 'creditos' => [1], 'idresponsable' => 40, 'responsableclave' => 'incorrecta']);
        try { (new CarteraCreditoController)->store($request, 1); $this->fail(); }
        catch (ValidationException $e) {
            $this->assertEquals('NINGUNO', DB::table('credito')->value('castigo_estado'));
        }
        $request->merge(['responsableclave' => 'secreto']);
        (new CarteraCreditoController)->store($request, 1);
        $this->assertEquals('PENDIENTE', DB::table('credito')->value('castigo_estado'));
        $this->assertEquals(40, DB::table('credito')->value('castigo_solicitante'));
    }
}
