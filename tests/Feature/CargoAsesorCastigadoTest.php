<?php

namespace Tests\Feature;

use App\Http\Controllers\Layouts\Backoffice\Sistema\CargoController;
use App\Http\Controllers\Layouts\Backoffice\Sistema\CargoAsesorController;
use App\Http\Controllers\Layouts\Backoffice\Sistema\CargoCastigadoController;
use App\Http\Controllers\Layouts\Backoffice\Sistema\CobranzacuotaController;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class CargoAsesorCastigadoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'cargo_test', 'database.connections.cargo_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('idasesor')->default(0);
            $table->string('nombrecompleto')->default('Cliente');
            $table->string('identificacion')->default('12345678');
        });
        Schema::create('s_listanegra', function (Blueprint $table) {
            $table->integer('idcliente');
        });
        Schema::create('tienda', function (Blueprint $table) {
            $table->integer('id');
            $table->string('nombre')->default('Agencia');
        });
        Schema::create('permiso', function (Blueprint $table) {
            $table->integer('id');
            $table->string('nombre');
        });
        Schema::create('users_permiso', function (Blueprint $table) {
            $table->integer('idusers');
            $table->integer('idpermiso');
            $table->integer('idtienda')->default(1);
            $table->integer('idsession')->default(2);
        });
        foreach (['tipo_credito', 'forma_pago_credito', 'modalidad_credito', 'tipo_operacion_credito', 'credito_prendatario'] as $tabla) {
            Schema::create($tabla, function (Blueprint $table) {
                $table->integer('id');
            });
            DB::table($tabla)->insert(['id' => 1]);
        }
        Schema::create('credito', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('idcliente');
            $table->integer('idasesor')->default(10);
            $table->integer('idusuariocobranza')->nullable();
            $table->integer('idtienda')->default(1);
            $table->string('estado')->default('DESEMBOLSADO');
            $table->integer('idestadocredito')->default(1);
            $table->string('castigo_estado')->default('NINGUNO');
            $table->string('fecha_desembolso')->default('2026-11-01');
            $table->integer('idforma_credito')->default(1);
            $table->integer('cuenta')->default(1);
            $table->decimal('monto_solicitado')->default(100);
            foreach (['idforma_pago_credito', 'idmodalidad_credito', 'idtipo_operacion_credito', 'idcredito_prendatario'] as $campo) {
                $table->integer($campo)->default(1);
            }
        });
        DB::table('tienda')->insert(['id' => 1]);
        DB::table('permiso')->insert([['id' => 3, 'nombre' => 'Ejecutivo'], ['id' => 12, 'nombre' => 'Cobranza']]);
        DB::table('users')->insert([['id' => 10], ['id' => 20], ['id' => 30], ['id' => 100], ['id' => 101]]);
        DB::table('users_permiso')->insert([
            ['idusers' => 10, 'idpermiso' => 3], ['idusers' => 20, 'idpermiso' => 12],
        ]);
        DB::table('credito')->insert([
            ['id' => 1, 'idcliente' => 100, 'castigo_estado' => 'CONFIRMADO', 'idusuariocobranza' => 20],
            ['id' => 2, 'idcliente' => 100, 'castigo_estado' => 'CONFIRMADO', 'idusuariocobranza' => 30],
            ['id' => 3, 'idcliente' => 100, 'castigo_estado' => 'NINGUNO', 'idusuariocobranza' => 20],
            ['id' => 4, 'idcliente' => 101, 'castigo_estado' => 'NINGUNO', 'idusuariocobranza' => null],
        ]);
    }

    public function test_cobranza_cuota_excluye_castigados_del_listado_normal_del_cliente(): void
    {
        $resultado = (new CobranzacuotaController)->show(Request::create('/', 'GET', [
            'idcliente' => 100,
        ]), 0, 'showlistacreditos');

        $this->assertStringContainsString("data-valor-columna='3'", $resultado['html']);
        $this->assertStringNotContainsString("data-valor-columna='1'", $resultado['html']);
        $this->assertStringNotContainsString("data-valor-columna='2'", $resultado['html']);
    }

    public function test_cobranza_cuota_conserva_el_listado_exclusivo_de_castigados(): void
    {
        $resultado = (new CobranzacuotaController)->show(Request::create('/', 'GET', [
            'idcliente' => 100, 'solo_castigados' => 1,
        ]), 0, 'showlistacreditos');

        $this->assertStringContainsString("data-valor-columna='1'", $resultado['html']);
        $this->assertStringContainsString("data-valor-columna='2'", $resultado['html']);
        $this->assertStringNotContainsString("data-valor-columna='3'", $resultado['html']);
    }

    public function test_pago_castigado_por_asesor_busca_y_lista_solo_asignados_a_cobranza(): void
    {
        $this->actingAs(User::find(20));
        $controller = new CobranzacuotaController;
        $clientes = $controller->show(Request::create('/', 'GET', [
            'buscar' => '123', 'por_asesor' => 1, 'solo_castigados' => 1,
        ]), 1, 'show_credito_tienda');
        $this->assertSame([100], array_column($clientes, 'id'));

        $resultado = $controller->show(Request::create('/', 'GET', [
            'idcliente' => 100, 'por_asesor' => 1, 'solo_castigados' => 1,
        ]), 0, 'showlistacreditos');
        $this->assertStringContainsString("data-valor-columna='1'", $resultado['html']);
        foreach ([2, 3] as $id) {
            $this->assertStringNotContainsString("data-valor-columna='{$id}'", $resultado['html']);
        }

        $this->actingAs(User::find(10));
        $clientes = $controller->show(Request::create('/', 'GET', [
            'buscar' => '123', 'por_asesor' => 1, 'solo_castigados' => 1,
        ]), 1, 'show_credito_tienda');
        $this->assertSame([], $clientes);
    }

    public function test_pago_castigado_encuentra_cliente_del_ejecutivo_con_credito_castigado(): void
    {
        $this->actingAs(User::find(20));
        DB::table('users')->whereId(101)->update(['nombrecompleto' => 'Berny', 'idasesor' => 20]);
        DB::table('credito')->whereId(4)->update(['castigo_estado' => 'CONFIRMADO']);
        $controller = new CobranzacuotaController;
        $clientes = $controller->show(Request::create('/', 'GET', [
            'buscar' => 'Berny', 'por_asesor' => 1, 'solo_castigados' => 1,
        ]), 1, 'show_credito_tienda');
        $this->assertSame([101], array_column($clientes, 'id'));
        $resultado = $controller->show(Request::create('/', 'GET', [
            'idcliente' => 101, 'por_asesor' => 1, 'solo_castigados' => 1,
        ]), 0, 'showlistacreditos');
        $this->assertStringContainsString("data-valor-columna='4'", $resultado['html']);

        DB::table('users')->whereId(101)->update(['idasesor' => 30]);
        $clientes = $controller->show(Request::create('/', 'GET', [
            'buscar' => 'Berny', 'por_asesor' => 1, 'solo_castigados' => 1,
        ]), 1, 'show_credito_tienda');
        $this->assertSame([], $clientes);
    }

    public function test_cargo_castigado_busca_y_lista_solo_castigados_asignados(): void
    {
        $this->actingAs(User::find(20));
        $controller = new CargoCastigadoController;
        $clientes = $controller->show(Request::create('/', 'GET', ['buscar' => '123']), 1, 'show_credito_asesor');
        $this->assertSame([100], array_column($clientes, 'id'));
        $resultado = $controller->show(Request::create('/', 'GET', [
            'idcliente' => 100, 'idasesor' => 20,
        ]), 0, 'showlistacreditos');
        $this->assertStringContainsString("data-valor-columna='1'", $resultado['html']);
        $this->assertStringNotContainsString("data-valor-columna='2'", $resultado['html']);
        $this->assertStringNotContainsString("data-valor-columna='3'", $resultado['html']);

        $this->expectException(HttpException::class);
        $controller->show(Request::create('/', 'GET', ['idcliente' => 101]), 0, 'showlistacreditos');
    }

    public function test_cargo_asesor_excluye_castigados_incluso_para_administrador(): void
    {
        DB::table('users')->whereId(100)->update(['idasesor' => 10]);
        DB::table('permiso')->insert(['id' => 1, 'nombre' => 'Administrador']);
        DB::table('users_permiso')->where('idusers', 10)->update(['idpermiso' => 1]);
        $this->actingAs(User::find(10));
        $controller = new CargoAsesorController;
        $resultado = $controller->show(Request::create('/', 'GET', ['idcliente' => 100]), 0, 'showlistacreditos');
        $this->assertStringContainsString("data-valor-columna='3'", $resultado['html']);
        $this->assertStringNotContainsString("data-valor-columna='1'", $resultado['html']);
        $this->assertStringNotContainsString("data-valor-columna='2'", $resultado['html']);

        DB::table('credito')->whereId(3)->update(['idestadocredito' => 2]);
        $clientes = $controller->show(Request::create('/', 'GET', ['buscar' => '123']), 1, 'show_credito_asesor');
        $this->assertNotContains(100, array_column($clientes, 'id'));
    }

    public function test_cargo_asesor_de_cobranza_encuentra_su_cartera_actual_y_excluye_castigados(): void
    {
        $this->actingAs(User::find(20));
        DB::table('users')->whereId(100)->update(['idasesor' => 20, 'nombrecompleto' => 'Lucas']);
        DB::table('users')->whereId(101)->update(['idasesor' => 20, 'nombrecompleto' => 'Berny']);
        $controller = new CargoAsesorController;
        foreach (['Lucas' => 100, 'Berny' => 101] as $nombre => $idcliente) {
            $clientes = $controller->show(Request::create('/', 'GET', ['buscar' => $nombre]), 1, 'show_credito_asesor');
            $this->assertSame([$idcliente], array_column($clientes, 'id'));
        }
        $resultado = $controller->show(Request::create('/', 'GET', [
            'idcliente' => 100, 'idasesor' => 20,
        ]), 0, 'showlistacreditos');
        $this->assertStringContainsString("data-valor-columna='3'", $resultado['html']);
        $this->assertStringNotContainsString("data-valor-columna='1'", $resultado['html']);
        $this->assertStringNotContainsString("data-valor-columna='2'", $resultado['html']);

        DB::table('users')->whereId(101)->update(['idasesor' => 30]);
        $clientes = $controller->show(Request::create('/', 'GET', ['buscar' => 'Berny']), 1, 'show_credito_asesor');
        $this->assertSame([], $clientes);
        $this->expectException(HttpException::class);
        $controller->show(Request::create('/', 'GET', ['idcliente' => 101]), 0, 'showlistacreditos');
    }

    public function test_cargo_asesor_permite_buscar_por_cobranza_aunque_otro_ejecutivo_sea_responsable(): void
    {
        $this->actingAs(User::find(20));
        DB::table('users')->whereId(20)->update(['nombrecompleto' => 'Percy']);
        DB::table('users')->whereId(100)->update(['idasesor' => 10, 'nombrecompleto' => 'Lucas']);
        DB::table('credito')->insert(['id' => 5, 'idcliente' => 100, 'idusuariocobranza' => 30]);
        $controller = new CargoAsesorController;

        $clientes = $controller->show(Request::create('/', 'GET', ['buscar' => 'Lucas']), 1, 'show_credito_asesor');
        $this->assertSame([100], array_column($clientes, 'id'));
        $resultado = $controller->show(Request::create('/', 'GET', [
            'idcliente' => 100, 'idasesor' => 20,
        ]), 0, 'showlistacreditos');
        $this->assertStringContainsString("data-valor-columna='3' data-cobranza='Percy (Cobranza)'", $resultado['html']);
        foreach ([1, 2, 5] as $id) {
            $this->assertStringNotContainsString("data-valor-columna='{$id}'", $resultado['html']);
        }

        DB::table('credito')->whereId(3)->update(['castigo_estado' => 'CONFIRMADO']);
        $clientes = $controller->show(Request::create('/', 'GET', ['buscar' => 'Lucas']), 1, 'show_credito_asesor');
        $this->assertSame([], $clientes);
    }

    public function test_ejecutivo_puede_filtrar_cliente_mixto_y_solo_recibe_sus_creditos_sin_castigo(): void
    {
        $this->actingAs(User::find(10));
        $controller = new CargoController;
        $clientes = $controller->show(Request::create('/', 'GET', ['buscar' => '123']), 1, 'show_credito_asesor');
        $this->assertContains(100, array_column($clientes, 'id'));
        $this->assertArrayNotHasKey('es_castigado', $clientes[0]);
        $response = $controller->show(Request::create('/', 'GET', ['idcliente' => 100]), 0, 'showlistacreditos');
        $this->assertStringContainsString("data-valor-columna='3'", $response['html']);
        $this->assertStringNotContainsString("data-valor-columna='1'", $response['html']);
        $this->assertStringNotContainsString("data-valor-columna='2'", $response['html']);

        DB::table('credito')->whereId(3)->update(['idestadocredito' => 2]);
        $clientes = $controller->show(Request::create('/', 'GET', ['buscar' => '123']), 1, 'show_credito_asesor');
        $this->assertNotContains(100, array_column($clientes, 'id'));
    }

    public function test_busquedas_de_cargos_solo_encuentran_clientes_con_creditos_activos(): void
    {
        DB::table('permiso')->insert(['id' => 1, 'nombre' => 'Administrador']);
        DB::table('users_permiso')->where('idusers', 10)->update(['idpermiso' => 1]);
        $this->actingAs(User::find(10));
        DB::table('credito')->whereId(4)->update(['idestadocredito' => 2]);
        DB::table('users')->insert(['id' => 102]);
        DB::table('credito')->insert(['id' => 5, 'idcliente' => 102, 'castigo_estado' => 'CONFIRMADO']);

        $controller = new CargoController;
        foreach (['show_credito', 'show_credito_asesor'] as $endpoint) {
            $clientes = $controller->show(Request::create('/', 'GET', ['buscar' => '123']), 1, $endpoint);
            $this->assertSame($endpoint === 'show_credito' ? [100, 102] : [100], array_column($clientes, 'id'));
        }

        DB::table('credito')->insert(['id' => 6, 'idcliente' => 100, 'idestadocredito' => 2]);
        $resultado = $controller->show(Request::create('/', 'GET', ['idcliente' => 100]), 0, 'showlistacreditos');
        $this->assertStringContainsString("data-valor-columna='3'", $resultado['html']);
        $this->assertStringContainsString("data-valor-columna='1'", $resultado['html']);
        $this->assertStringContainsString("data-valor-columna='2'", $resultado['html']);
        foreach ([6] as $id) {
            $this->assertStringNotContainsString("data-valor-columna='{$id}'", $resultado['html']);
        }
    }

    public function test_cobranza_solo_lista_castigados_asignados_a_el_sin_usar_el_asesor_original(): void
    {
        $this->actingAs(User::find(20));
        $controller = new CargoController;
        $clientes = $controller->show(Request::create('/', 'GET', ['buscar' => '123']), 1, 'show_credito_asesor');
        $this->assertSame([100], array_column($clientes, 'id'));
        $resultado = $controller->show(Request::create('/', 'GET', [
            'idcliente' => 100, 'idagencia' => 1, 'idasesor' => 30,
        ]), 0, 'showlistacreditos');
        $this->assertStringContainsString("data-valor-columna='1'", $resultado['html']);
        $this->assertStringNotContainsString("data-valor-columna='2'", $resultado['html']);
        $this->assertStringNotContainsString("data-valor-columna='3'", $resultado['html']);
    }

    public function test_cobranza_no_puede_abrir_por_url_un_credito_asignado_a_otro(): void
    {
        $this->actingAs(User::find(20));
        $this->expectException(HttpException::class);
        (new CargoController)->edit(Request::create('/', 'GET', ['view' => 'editar']), 1, 2);
    }

    public function test_ejecutivo_puede_filtrar_cliente_sin_castigo(): void
    {
        $this->actingAs(User::find(10));
        $resultado = (new CargoController)->show(Request::create('/', 'GET', [
            'idcliente' => 101, 'idasesor' => 10, 'idagencia' => 1,
        ]), 0, 'showlistacreditos');
        $this->assertStringContainsString("data-valor-columna='4'", $resultado['html']);
    }

    public function test_cliente_global_muestra_cobrador_y_no_el_asesor_historico_del_credito(): void
    {
        $this->actingAs(User::find(20));
        DB::table('users')->whereId(20)->update(['nombrecompleto' => 'Ejecutivo Cobranza']);
        $resultado = (new CargoController)->show(Request::create('/', 'GET', [
            'idcliente' => 100, 'idagencia' => 1,
        ]), 0, 'showlistacreditos');
        $this->assertSame('', $resultado['asesor']);
        $this->assertSame('Ejecutivo Cobranza (Cobranza)', $resultado['cobranza']);
        $this->assertStringContainsString('data-cobranza=\'Ejecutivo Cobranza (Cobranza)\'', $resultado['html']);
    }

    public function test_muestra_asesor_actual_del_cliente_y_cobranza_vacia_si_no_tiene(): void
    {
        $this->actingAs(User::find(10));
        DB::table('users')->whereId(101)->update(['idasesor' => 30]);
        DB::table('users')->whereId(30)->update(['nombrecompleto' => 'Asesor Actual']);
        DB::table('users_permiso')->insert(['idusers' => 30, 'idpermiso' => 3]);
        // El administrador puede consultar el crédito aun si cambió el asesor del cliente.
        DB::table('permiso')->insert(['id' => 1, 'nombre' => 'Administrador']);
        DB::table('users_permiso')->where('idusers', 10)->update(['idpermiso' => 1]);
        $resultado = (new CargoController)->show(Request::create('/', 'GET', [
            'idcliente' => 101, 'idagencia' => 1,
        ]), 0, 'showlistacreditos');
        $this->assertSame('Asesor Actual (Ejecutivo)', $resultado['asesor']);
        $this->assertSame('', $resultado['cobranza']);
    }

    public function test_cada_credito_incluye_su_cobrador_con_permiso_o_vacio_si_no_tiene(): void
    {
        DB::table('permiso')->insert(['id' => 1, 'nombre' => 'Administrador']);
        DB::table('users_permiso')->where('idusers', 10)->update(['idpermiso' => 1]);
        $this->actingAs(User::find(10));
        DB::table('users')->whereId(20)->update(['nombrecompleto' => 'Cobrador asignado']);
        DB::table('credito')->whereId(3)->update(['idusuariocobranza' => null]);

        $resultado = (new CargoController)->show(Request::create('/', 'GET', [
            'idcliente' => 100,
        ]), 0, 'showlistacreditos');

        $this->assertStringContainsString("data-valor-columna='1' data-cobranza='Cobrador asignado (Cobranza)'", $resultado['html']);
        $this->assertStringContainsString("data-valor-columna='3' data-cobranza=''", $resultado['html']);
    }

    public function test_ejecutivo_no_puede_registrar_cargo_en_castigado_enviando_el_id_directamente(): void
    {
        $this->actingAs(User::find(10));
        $this->expectException(HttpException::class);
        (new CargoController)->store(Request::create('/', 'POST', [
            'view' => 'registrar', 'idcredito' => 1,
        ]), 1);
    }

    public function test_cobranza_no_puede_filtrar_un_cliente_sin_creditos_castigados_asignados(): void
    {
        $this->actingAs(User::find(20));
        $this->expectException(HttpException::class);
        (new CargoController)->show(Request::create('/', 'GET', ['idcliente' => 101]), 0, 'showlistacreditos');
    }

    public function test_aviso_combina_lista_negra_y_castigo_del_credito(): void
    {
        $castigado = DB::table('credito')->whereId(1)->first();
        $normal = DB::table('credito')->whereId(4)->first();
        $this->assertSame('', sistema_aviso_credito($normal));
        $this->assertSame('CRÉDITO CASTIGADO', sistema_aviso_credito($castigado));
        DB::table('s_listanegra')->insert([['idcliente' => 100], ['idcliente' => 101]]);
        $this->assertSame('OBSERVADO', sistema_aviso_credito($normal));
        $this->assertSame('OBSERVADO / CRÉDITO CASTIGADO', sistema_aviso_credito($castigado));
        $this->assertSame('', sistema_aviso_credito(null));
    }
}
