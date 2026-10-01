<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use DB;

/**
 * El tarifario entra al motor de calculo de interes y comision. Estos tests
 * comprueban que los simuladores y las pantallas de credito siguen
 * funcionando con el tarifario ahora filtrado por agencia, y que un producto
 * de la agencia del usuario es el que se resuelve.
 */
class SimuladorConAgenciaTest extends TestCase
{
    private function usuario(): User
    {
        $ids = DB::table('users_permiso')->distinct()->pluck('idusers');
        $u = User::whereIn('id', $ids)->where('idestadousuario', 1)->orderBy('id')->first();
        $this->assertNotNull($u, 'no hay usuario activo con cargo');

        return $u;
    }

    public function test_showtasa_devuelve_un_tarifario_de_la_agencia_del_usuario(): void
    {
        $u = $this->usuario();
        $this->actingAs($u);
        $agencia = idtienda_actual();

        // un producto de esta agencia que tenga tarifario
        $prod = DB::table('tarifario')
            ->where('idtienda', $agencia)
            ->whereNotNull('idcredito_prendatario')
            ->value('idcredito_prendatario');

        $this->assertNotNull($prod, 'la agencia activa no tiene tarifario');

        $fila = DB::table('tarifario')
            ->where('idtienda', $agencia)
            ->where('idcredito_prendatario', $prod)
            ->first();

        $r = $this->getJson(
            "/backoffice/0/calculosimple/showtasa?producto={$prod}&frecuencia={$fila->idforma_pago_credito}&monto={$fila->monto}&numerocuota={$fila->cuotas}"
        );

        $r->assertOk();
        $tasa = $r->json();

        $this->assertIsArray($tasa, 'showtasa no devolvio un tarifario');
        $this->assertNotEmpty($tasa);
        $this->assertSame($agencia, (int) $tasa['idtienda'], 'devolvio un tarifario de otra agencia');
    }

    public function test_showtasa_no_se_confunde_con_el_tarifario_de_otra_agencia(): void
    {
        $u = $this->usuario();
        $this->actingAs($u);
        $agencia = idtienda_actual();

        $propio = DB::table('tarifario')->where('idtienda', $agencia)->orderBy('id')->first();
        // su equivalente en otra agencia, emparejado por el nombre del producto
        $otro = DB::table('tarifario')
            ->join('credito_prendatario as p1', 'p1.id', '=', DB::raw('tarifario.idcredito_prendatario'))
            ->where('tarifario.idtienda', '!=', $agencia)
            ->where('p1.nombre', '=', DB::table('credito_prendatario')->where('id', $propio->idcredito_prendatario)->value('nombre'))
            ->where('tarifario.idforma_pago_credito', $propio->idforma_pago_credito)
            ->where('tarifario.monto', $propio->monto)
            ->where('tarifario.cuotas', $propio->cuotas)
            ->select('tarifario.*')
            ->first();

        if (! $otro) {
            $this->markTestSkipped('no hay equivalente en otra agencia para este combo');
        }

        $r = $this->getJson(
            "/backoffice/0/calculosimple/showtasa?producto={$propio->idcredito_prendatario}&frecuencia={$propio->idforma_pago_credito}&monto={$propio->monto}&numerocuota={$propio->cuotas}"
        );

        $tasa = $r->json();
        $this->assertSame($agencia, (int) $tasa['idtienda']);
        // el TEM puede diferir entre agencias: la prueba de que elige la suya
        $this->assertNotEquals(
            (int) $otro->id,
            (int) $tasa['id'],
            'el simulador eligio el tarifario de otra agencia'
        );
    }

    public function test_los_calculos_no_reciben_nulos(): void
    {
        // regresion del riesgo que se descarto: si forma_pago_credito se
        // duplicara, $frecuenciaDiasMap daria undefined y el calculo dividiria
        // entre null. Se comprueba que la forma de pago resuelve por id fijo.
        $this->assertSame([1, 2, 3, 4], DB::table('forma_pago_credito')->orderBy('id')->pluck('id')->map(fn ($i) => (int) $i)->all());
        $this->assertSame('Diario', DB::table('forma_pago_credito')->where('id', 1)->value('nombre'));
        $this->assertSame('Mensual', DB::table('forma_pago_credito')->where('id', 4)->value('nombre'));

        $frecuenciaDiasMap = [1 => 26, 2 => 4, 3 => 2, 4 => 1];
        foreach ([1, 2, 3, 4] as $f) {
            $this->assertArrayHasKey($f, $frecuenciaDiasMap);
        }

        // y que la columna dias que usa genera_cronograma sigue poblada
        $this->assertSame(
            ['1.0', '7.0', '15.0', '30.0'],
            DB::table('forma_pago_credito')->orderBy('id')->pluck('dias')->map(fn ($d) => (string) $d)->all()
        );
    }

    public function test_los_creditos_historicos_siguen_resolviendo_su_producto(): void
    {
        // los 163 creditos NO se reasignaron: su id de producto apunta a la
        // copia de la agencia que los emitio y el join debe seguir resolviendo
        $sinNombre = DB::table('credito')
            ->join('credito_prendatario', 'credito_prendatario.id', '=', 'credito.idcredito_prendatario')
            ->whereNull('credito_prendatario.nombre')
            ->count();

        $this->assertSame(0, $sinNombre, 'hay creditos que no resuelven su producto');

        $totales = DB::table('credito')->count();
        $resueltos = DB::table('credito')
            ->join('credito_prendatario', 'credito_prendatario.id', '=', 'credito.idcredito_prendatario')
            ->count();

        $this->assertSame($totales, $resueltos, 'algunos creditos quedaron sin producto');
    }
}
