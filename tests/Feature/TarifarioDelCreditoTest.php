<?php

namespace Tests\Feature;

use App\Http\Controllers\Layouts\Backoffice\Sistema\CreditoController;
use App\Http\Controllers\Layouts\Backoffice\Sistema\RefinanciamientoController;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TarifarioDelCreditoTest extends TestCase
{
    use DatabaseTransactions;

    private function prepararCredito(): object
    {
        $this->actingAs(User::findOrFail(6277));
        $credito = DB::table('credito')->where('idtienda', 194)->first();
        $this->assertNotNull($credito);
        $this->assertTrue(DB::table('users_permiso')->where('idusers', 6277)->where('idtienda', 198)->exists());
        DB::table('users_permiso')->where('idusers', 6277)->update(['idsession' => 1]);
        DB::table('users_permiso')->where('idusers', 6277)->where('idtienda', 198)->update(['idsession' => 2]);
        unset($GLOBALS['__idtienda_actual_cache'][6277]);
        $this->assertSame(198, idtienda_actual());

        DB::table('tarifario')->where('idcredito_prendatario', $credito->idcredito_prendatario)->delete();
        foreach ([194 => [5.5, 2.5], 198 => [99, 88]] as $agencia => [$tem, $comision]) {
            DB::table('tarifario')->insert([
                'idtienda' => $agencia,
                'idforma_credito' => $credito->idforma_credito,
                'idcredito_prendatario' => $credito->idcredito_prendatario,
                'idforma_pago_credito' => 4,
                'monto' => 10000, 'cuotas' => 12, 'tem' => $tem, 'cargos_otros' => $comision,
            ]);
        }

        return $credito;
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['__idtienda_actual_cache'][6277]);
        parent::tearDown();
    }

    public function test_tasa_y_comision_corresponden_al_credito_aunque_la_ruta_y_sesion_sean_de_otra_agencia(): void
    {
        $credito = $this->prepararCredito();
        $datos = (new CreditoController)->show(Request::create('/', 'GET', [
            'idcredito' => $credito->id, 'frecuencia' => 4,
            'monto' => 1000, 'numerocuota' => 6, 'tasa' => 6,
        ]), 198, 'showtasa');
        $this->assertEquals(5.5, $datos['tasa_tem_minima']);
        $this->assertEquals(2.5, $datos['cargootros']);
    }

    public function test_un_tarifario_ausente_no_se_suple_con_el_de_la_agencia_activa(): void
    {
        $credito = $this->prepararCredito();
        DB::table('tarifario')->where('idtienda', $credito->idtienda)->delete();
        $datos = (new CreditoController)->show(Request::create('/', 'GET', [
            'idcredito' => $credito->id, 'frecuencia' => 4,
            'monto' => 1000, 'numerocuota' => 6, 'tasa' => 6,
        ]), 198, 'showtasa');
        $this->assertEquals(0, $datos['tasa_tem_minima']);
        $this->assertEquals(0, $datos['cargootros']);
    }

    public function test_cronograma_valida_los_limites_de_la_agencia_del_credito(): void
    {
        $credito = $this->prepararCredito();
        DB::table('tarifario')->where('idtienda', 198)->update(['monto' => 20000]);
        $respuesta = (new CreditoController)->show(Request::create('/', 'GET', [
            'idcredito' => $credito->id, 'frecuencia' => 4,
            'monto' => 10001, 'numerocuota' => 6, 'modalidad_credito' => 'NORMAL',
        ]), 198, 'cronograma');
        $datos = $respuesta->getData(true);
        $this->assertSame('ERROR', $datos['resultado']);
        $this->assertStringContainsString('10000', $datos['mensaje']);
    }

    public function test_formularios_de_credito_y_refinanciamiento_muestran_su_tarifario(): void
    {
        $credito = $this->prepararCredito();
        foreach ([CreditoController::class => 'editar', RefinanciamientoController::class => 'refinanciar'] as $clase => $vista) {
            $formulario = (new $clase)->edit(Request::create('/', 'GET', ['view' => $vista]), 198, $credito->id);
            $tarifario = $formulario->getData()['tarifario_producto'];
            $this->assertCount(1, $tarifario);
            $this->assertSame(194, (int) $tarifario->first()->idtienda);
            $this->assertEquals(5.5, $tarifario->first()->tem);
        }
    }

    public function test_guardado_del_cronograma_valida_el_limite_del_credito(): void
    {
        $credito = $this->prepararCredito();
        DB::table('tarifario')->where('idtienda', 198)->update(['monto' => 20000]);
        $respuesta = (new CreditoController)->update(Request::create('/', 'PUT', [
            'view' => 'credito_garantia',
            'monto_solicitado' => 10001, 'idforma_pago_credito' => 4,
            'cuotas' => 6, 'tasa_tem' => 6, 'dia_gracia' => 0,
            'fecha_desembolso' => '2026-11-11',
        ]), 198, $credito->id);
        $datos = $respuesta->getData(true);
        $this->assertSame('ERROR', $datos['resultado']);
        $this->assertStringContainsString('10000', $datos['mensaje']);
        $this->assertEquals($credito->monto_solicitado, DB::table('credito')->where('id', $credito->id)->value('monto_solicitado'));
    }
}
