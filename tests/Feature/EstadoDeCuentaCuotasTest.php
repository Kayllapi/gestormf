<?php

namespace Tests\Feature;

use App\Http\Controllers\Layouts\Backoffice\Sistema\CobranzacuotaController;
use App\Http\Controllers\Layouts\Backoffice\Sistema\EstadocuentaController;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;
use DB;

/**
 * El estado de cuenta (PDF) tiene que mostrar los MISMOS saldos y el mismo
 * cronograma que la pantalla de cobranza: es el mismo credito y el mismo
 * select_cronograma(), asi que cualquier diferencia es un error de una de las dos.
 *
 * Regresion 1: el PDF armaba la columna "F. de Cancel." leyendo
 * credito_adelanto.total_pagar, columna que NO existe en esa tabla (la de
 * credito_adelanto es "total"). Como la consulta hace JOIN con
 * credito_cobranzacuota, que si tiene "total_pagar", PHP recibia en silencio el
 * importe de la COBRANZA y no el acumulado del adelanto de la cuota: la
 * comparacion contra totalcuota casi nunca se cumplia y ninguna cuota aparecia
 * como "Canc." ni con su fecha, aunque estuviera pagada.
 *
 * Regresion 2: el PDF pasaba a la vista la suma cruda de select_cronograma() para
 * "Pendientes" y "Cumplido y Vencidos", sin restar el pago a cuenta de la
 * primera cuota pendiente que la pantalla de cobranza si resta.
 *
 * DatabaseTransactions solo por uniformidad con el resto de pruebas del modulo:
 * estos tests son de lectura.
 */
class EstadoDeCuentaCuotasTest extends TestCase
{
    use DatabaseTransactions;

    /** doble del PDF: guarda los datos con los que se arma la vista */
    private $pdf;

    private function usarPdfFake(): void
    {
        $this->pdf = new class {
            public array $datos = [];
            public function loadView($vista, $datos)
            {
                $this->datos = $datos;

                return $this;
            }
            public function setPaper($a = null, $b = null)
            {
                return $this;
            }
            public function stream($nombre = null)
            {
                return response('');
            }
        };

        $pdf = $this->pdf;
        app()->bind('dompdf.wrapper', fn () => $pdf);
    }

    private function credito(int $idcredito): object
    {
        $c = DB::table('credito')->where('id', $idcredito)->first();
        $this->assertNotNull($c, "no existe el credito {$idcredito}");

        return $c;
    }

    private function usuarioConAcceso(int $idtienda): User
    {
        $idusers = DB::table('users_permiso')
            ->where('idtienda', $idtienda)
            ->where('idestado', 1)
            ->value('idusers');

        $u = $idusers ? User::where('id', $idusers)->where('idestadousuario', 1)->first() : null;

        $this->assertNotNull($u, "no hay usuario activo con permiso en la agencia {$idtienda}");

        return $u;
    }

    /** saldos tal como los muestra la pantalla de cobranza */
    private function saldosCobranza(int $idcredito): array
    {
        return (new CobranzacuotaController())->show(
            Request::create('/x', 'GET', [
                'idcredito'   => $idcredito,
                'numerocuota' => 0,
                'tipo'        => 'pagocuota',
            ]),
            (int) $this->credito($idcredito)->idtienda,
            'show_cobranzacuota_cronograma'
        );
    }

    /** datos con los que el estado de cuenta arma su cronograma, sus saldos y su HTML */
    private function estadoDeCuenta(int $idcredito): array
    {
        (new EstadocuentaController())->edit(
            Request::create('/x', 'GET', ['view' => 'pdf_credito']),
            (int) $this->credito($idcredito)->idtienda,
            $idcredito
        );

        $datos = $this->pdf->datos;
        $this->assertNotEmpty($datos, 'el estado de cuenta no llego a la vista del PDF');

        $datos['html'] = view(sistema_view().'/estadocuenta/pdf_credito', $datos)->render();

        return $datos;
    }

    /** celdas de la fila del cronograma del PDF de una cuota */
    private function filaCronograma(string $html, int $numerocuota): array
    {
        // preg_match devuelve int, y assertTrue exige un booleano estricto
        $ok = (bool) preg_match(
            '#<tr>\s*<td style="width:10px;text-align:center;">'.$numerocuota.'</td>(.*?)</tr>#s',
            $html,
            $m
        );

        $this->assertTrue($ok, "no se encontro la fila de la cuota {$numerocuota} en el cronograma del PDF");

        $celdas = array_map(
            fn ($c) => trim(preg_replace('/\s+/', ' ', strip_tags($c))),
            preg_split('#</td>#', $m[1])
        );

        return [
            'total'          => $celdas[6] ?? '',   // Total
            'fecha_cancel'   => $celdas[7] ?? '',   // F. de Cancel.
            'atraso'         => $celdas[8] ?? '',   // Atraso
            'estado'         => $celdas[9] ?? '',   // Estado
        ];
    }

    /**
     * Un credito con cuotas pagadas y con pagos a cuenta: es el caso donde el PDF
     * se equivocaba. Si la base de desarrollo no tiene ninguno, se salta.
     */
    private function creditoConCuotasPagadasYPagos(): ?object
    {
        $ids = DB::table('credito')
            ->where('credito.idestadocredito', 1)
            ->whereIn('credito.estado', ['DESEMBOLSADO', 'REFINANCIADO', 'CONGELADO'])
            ->orderBy('credito.id')
            ->pluck('credito.id');

        foreach ($ids as $id) {
            $pagadas = DB::table('credito_cronograma')
                ->where('idcredito', $id)->where('idestadocredito_cronograma', 2)->count();

            $adelantos = (float) DB::table('credito_adelanto')
                ->where('idcredito', $id)->whereIn('idestadocredito_adelanto', [1, 2])->sum('total');

            if ($pagadas > 0 && $adelantos > 0) {
                return DB::table('credito')->where('id', $id)->first();
            }
        }

        return null;
    }

    /**
     * El caso exacto de la regresion: una cuota pagada y cubierta por su pago a
     * cuenta, donde el importe de la COBRANZA (credito_cobranzacuota.total_pagar,
     * la columna que se colaba por el JOIN) es MENOR que el total de la cuota.
     * Leyendo esa columna en vez de credito_adelanto.total, la cuota saldia
     * "Pend." y sin fecha de cancelacion.
     *
     * Devuelve ['credito' => id, 'numerocuota' => n] o null.
     */
    private function cuotaDondeElImporteSeConfunde(): ?array
    {
        $ids = DB::table('credito')
            ->where('credito.idestadocredito', 1)
            ->whereIn('credito.estado', ['DESEMBOLSADO', 'REFINANCIADO', 'CONGELADO'])
            ->orderBy('credito.id')
            ->pluck('credito.id');

        foreach ($ids as $id) {
            $cuotas = DB::table('credito_cronograma')
                ->where('idcredito', $id)
                ->where('idestadocredito_cronograma', 2)
                ->orderBy('numerocuota')
                ->get();

            foreach ($cuotas as $cuota) {
                $adelanto = (float) DB::table('credito_adelanto')
                    ->where('credito_adelanto.idcredito_cronograma', $cuota->id)
                    ->where('credito_adelanto.idestadocredito_adelanto', 1)
                    ->sum('credito_adelanto.total');

                if ($adelanto < (float) $cuota->totalcuota) {
                    continue; // el adelanto no cubre la cuota: no es este caso
                }

                // y que ningun total_pagar de la cobranza llegue al total de la cuota
                $alcanzaLaDeLaCobranza = DB::table('credito_adelanto as a')
                    ->join('credito_cobranzacuota as cc', 'cc.id', 'a.idcredito_cobranzacuota')
                    ->where('a.idcredito_cronograma', $cuota->id)
                    ->where('a.idestadocredito_adelanto', 1)
                    ->where('cc.total_pagar', '>=', $cuota->totalcuota)
                    ->exists();

                if (! $alcanzaLaDeLaCobranza) {
                    return ['credito' => (int) $id, 'numerocuota' => (int) $cuota->numerocuota];
                }
            }
        }

        return null;
    }

    public function test_una_cuota_pagada_sale_cancelada_aunque_el_importe_de_la_cobranza_no_alcance(): void
    {
        $this->usarPdfFake();

        $caso = $this->cuotaDondeElImporteSeConfunde();
        if (! $caso) {
            $this->markTestSkipped('no hay ninguna cuota pagada cuyo pago a cuenta cubra y el total_pagar de la cobranza no');
        }

        $credito = $this->credito($caso['credito']);
        $this->actingAs($this->usuarioConAcceso((int) $credito->idtienda));

        $estado = $this->estadoDeCuenta($caso['credito']);
        $fila   = $this->filaCronograma($estado['html'], $caso['numerocuota']);

        $this->assertSame(
            'Canc.',
            $fila['estado'],
            "la cuota {$caso['numerocuota']} del credito {$caso['credito']} esta pagada con su pago a cuenta, "
                ."pero el PDF no la marca como cancelada (esta leyendo el importe de la cobranza en vez del adelanto)"
        );

        $this->assertNotSame(
            '',
            $fila['fecha_cancel'],
            "la cuota {$caso['numerocuota']} del credito {$caso['credito']} esta pagada pero el PDF no muestra su fecha de cancelacion"
        );
    }

    public function test_las_cuotas_pagadas_salen_como_canceladas_con_su_fecha(): void
    {
        $this->usarPdfFake();

        $credito = $this->creditoConCuotasPagadasYPagos();
        if (! $credito) {
            $this->markTestSkipped('no hay creditos con cuotas pagadas y pagos a cuenta');
        }

        $this->actingAs($this->usuarioConAcceso((int) $credito->idtienda));

        $estado = $this->estadoDeCuenta((int) $credito->id);

        $pagadas = DB::table('credito_cronograma')
            ->where('idcredito', $credito->id)
            ->where('idestadocredito_cronograma', 2)
            ->orderBy('numerocuota')
            ->pluck('numerocuota');

        $this->assertNotEmpty($pagadas);

        $conAdelanto = 0;

        foreach ($pagadas as $numerocuota) {
            $fila = $this->filaCronograma($estado['html'], (int) $numerocuota);

            $this->assertSame(
                'Canc.',
                $fila['estado'],
                "la cuota {$numerocuota} del credito {$credito->id} esta pagada pero el PDF no la marca como cancelada"
            );

            $adelanto = (float) DB::table('credito_adelanto')
                ->where('credito_adelanto.idcredito', $credito->id)
                ->where('credito_adelanto.numerocuota', $numerocuota)
                ->where('credito_adelanto.idestadocredito_adelanto', 1)
                ->sum('total');

            $totalcuota = (float) DB::table('credito_cronograma')
                ->where('idcredito', $credito->id)
                ->where('numerocuota', $numerocuota)
                ->value('totalcuota');

            if ($adelanto > 0 && $adelanto >= $totalcuota) {
                $conAdelanto++;

                $this->assertNotSame(
                    '',
                    $fila['fecha_cancel'],
                    "la cuota {$numerocuota} del credito {$credito->id} esta pagada pero el PDF no muestra su fecha de cancelacion"
                );
            }
        }

        // el caso solo sirve si de verdad hay cuotas cubiertas por un pago a cuenta
        $this->assertGreaterThan(
            0,
            $conAdelanto,
            'ninguna cuota pagada esta cubierta por un pago a cuenta: el caso de la regresion no se probo'
        );
    }

    public function test_los_saldos_del_estado_de_cuenta_igualan_a_la_pantalla_de_cobranza(): void
    {
        $this->usarPdfFake();

        $credito = $this->creditoConCuotasPagadasYPagos();
        if (! $credito) {
            $this->markTestSkipped('no hay creditos con cuotas pagadas y pagos a cuenta');
        }

        $this->actingAs($this->usuarioConAcceso((int) $credito->idtienda));

        $cobranza = $this->saldosCobranza((int) $credito->id);
        $estado   = $this->estadoDeCuenta((int) $credito->id);

        foreach ([
            'numero_cuota_cancelada',
            'numero_cuota_pendiente',
            'numero_cuota_vencida',
            'cuota_pagada',
            'cuota_pendiente',
            'saldo_vencido',
            'saldo_capital',
        ] as $campo) {
            $this->assertSame(
                (string) $cobranza[$campo],
                (string) $estado[$campo],
                "el campo {$campo} del estado de cuenta no coincide con la pantalla de cobranza"
            );
        }
    }

    /**
     * "Pendientes" debe ser el cronograma menos el pago a cuenta de la primera
     * cuota pendiente. Es el caso donde el PDF mostraba un monto mayor al de la
     * pantalla de cobranza.
     */
    public function test_pendientes_resta_el_pago_a_cuenta_de_la_primera_cuota_pendiente(): void
    {
        $this->usarPdfFake();

        // un credito con un pago a cuenta activo sobre su primera cuota pendiente
        $encontrado = null;

        $ids = DB::table('credito')
            ->where('credito.idestadocredito', 1)
            ->whereIn('credito.estado', ['DESEMBOLSADO', 'REFINANCIADO', 'CONGELADO'])
            ->orderBy('credito.id')
            ->pluck('credito.id');

        foreach ($ids as $id) {
            $primera = DB::table('credito_cronograma')
                ->where('idcredito', $id)->where('idestadocredito_cronograma', 1)
                ->orderBy('numerocuota')->value('numerocuota');

            if (! $primera) {
                continue;
            }

            $adelanto = (float) DB::table('credito_adelanto')
                ->where('credito_adelanto.idcredito', $id)
                ->where('credito_adelanto.numerocuota', $primera)
                ->whereIn('credito_adelanto.idestadocredito_adelanto', [1, 2])
                ->sum('total');

            if ($adelanto > 0) {
                $encontrado = ['id' => (int) $id, 'adelanto' => $adelanto];
                break;
            }
        }

        if (! $encontrado) {
            $this->markTestSkipped('no hay creditos con pago a cuenta sobre la primera cuota pendiente');
        }

        $this->actingAs($this->usuarioConAcceso((int) $this->credito($encontrado['id'])->idtienda));

        $cobranza = $this->saldosCobranza($encontrado['id']);
        $estado   = $this->estadoDeCuenta($encontrado['id']);

        $this->assertSame(
            (string) $cobranza['cuota_pendiente'],
            (string) $estado['cuota_pendiente'],
            'el credito tiene pago a cuenta pero el estado de cuenta no lo descuenta de Pendientes'
        );

        $this->assertSame(
            (string) $cobranza['saldo_vencido'],
            (string) $estado['saldo_vencido'],
            'el credito tiene pago a cuenta pero el estado de cuenta no lo descuenta de Cumplido y Vencidos'
        );
    }
}
