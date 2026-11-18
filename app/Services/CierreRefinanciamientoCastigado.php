<?php

namespace App\Services;

use App\Http\Controllers\Layouts\Backoffice\Sistema\CobranzacuotaController;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CierreRefinanciamientoCastigado
{
    /** Calcula la deuda con el mismo importe de pago total mostrado en cobranza. */
    public static function finalizarConDeudaActual(int $originalId, int $nuevoId, int $agencia, int $responsable, int $solicitante): int
    {
        if (DB::transactionLevel() === 0) {
            throw new \LogicException('El cierre debe ejecutarse dentro de la transacción del refinanciamiento.');
        }
        $original = DB::table('credito')->where('id', $originalId)->lockForUpdate()->first();
        if (!$original || (int) $original->idtienda !== $agencia || $original->estado !== 'DESEMBOLSADO'
            || (int) $original->idestadocredito !== 1 || $original->castigo_estado !== 'CONFIRMADO') {
            throw ValidationException::withMessages(['credito' => 'El crédito ya no está disponible para refinanciar.']);
        }

        $totales = app(CobranzacuotaController::class)->totales_pago_total($agencia, $originalId);
        if (!isset($totales['totalapagar']) || !is_numeric($totales['totalapagar'])) {
            throw ValidationException::withMessages(['credito' => 'No se pudo calcular la deuda pendiente del crédito.']);
        }
        $deuda = (string) $totales['totalapagar'];

        return self::finalizar($originalId, $nuevoId, $agencia, $responsable, $solicitante, $deuda);
    }

    /**
     * Finaliza una propuesta creada y un saldo calculado bajo el bloqueo del crédito
     * original. El llamador debe mantener abierta la transacción de toda la operación.
     */
    public static function finalizar(int $originalId, int $nuevoId, int $agencia, int $responsable, int $solicitante, string $deuda): int
    {
        if (DB::transactionLevel() === 0) {
            throw new \LogicException('El cierre debe ejecutarse dentro de la transacción del refinanciamiento.');
        }

        $original = DB::table('credito')->where('id', $originalId)->lockForUpdate()->first();
        $nuevo = DB::table('credito')->where('id', $nuevoId)->lockForUpdate()->first();
        if (!$original || !$nuevo || (int) $original->idtienda !== $agencia
            || (int) $nuevo->idtienda !== $agencia || (int) $nuevo->idcliente !== (int) $original->idcliente
            || (int) $nuevo->idcredito_refinanciado !== $originalId
            || $original->estado !== 'DESEMBOLSADO' || (int) $original->idestadocredito !== 1
            || $original->castigo_estado !== 'CONFIRMADO' || $nuevo->estado !== 'APROBADO'
            || !(bool) $nuevo->es_refinanciamiento_castigado
            || self::centavos((string) $nuevo->monto_solicitado) <= 0 || self::centavos($deuda) <= 0) {
            throw ValidationException::withMessages(['credito' => 'El crédito o el acuerdo ya no están disponibles para refinanciar.']);
        }
        if (DB::table('refinanciamiento_castigado_acuerdos')->where('idcredito_original', $originalId)->exists()) {
            throw ValidationException::withMessages(['credito' => 'El crédito ya tiene un acuerdo de refinanciamiento.']);
        }

        self::actualizarFechasCronograma($nuevo);

        $cronograma = DB::table('credito_cronograma')->where('idcredito', $nuevoId);
        $totalCronograma = (string) (clone $cronograma)->sum('cuota_real');
        if (!(clone $cronograma)->exists() || self::centavos($totalCronograma) <= 0) {
            throw ValidationException::withMessages(['credito' => 'El nuevo crédito no tiene un cronograma válido.']);
        }

        $cuenta = NumeracionCuentaCredito::asignar($nuevoId, [
            'estado' => 'DESEMBOLSADO', 'idestadocredito' => 1,
            'fecha_desembolso' => now(),
            'castigo_estado' => 'CONFIRMADO', 'castigo_responsable' => $responsable,
            'castigo_confirmado_at' => now(), 'idusuariocobranza' => $original->idusuariocobranza,
            'saldo_pendientepago' => $nuevo->monto_solicitado,
            'total_pendientepago' => $totalCronograma,
        ]);
        DB::table('credito')->where('id', $originalId)->update([
            'idestadocredito' => 2, 'fecha_cancelado' => now(),
            'saldo_pendientepago' => 0, 'total_pendientepago' => 0,
        ]);
        DB::table('refinanciamiento_castigado_acuerdos')->insert([
            'idcredito_original' => $originalId, 'idcredito_nuevo' => $nuevoId,
            'idresponsable' => $responsable, 'idsolicitante' => $solicitante,
            'deuda_anterior' => $deuda, 'monto_acordado' => $nuevo->monto_solicitado,
            'ajuste' => self::formatearCentavos(self::centavos((string) $nuevo->monto_solicitado) - self::centavos($deuda)),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return $cuenta;
    }

    private static function actualizarFechasCronograma(object $credito): void
    {
        $fechaDesembolso = now()->toDateString();
        if ($credito->fecha_desembolso && substr((string) $credito->fecha_desembolso, 0, 10) === $fechaDesembolso) {
            return;
        }

        $cuotas = DB::table('credito_cronograma')->where('idcredito', $credito->id)
            ->orderBy('numerocuota')->lockForUpdate()->get();
        if ($cuotas->count() !== (int) $credito->cuotas || $cuotas->contains(fn ($cuota) => (int) $cuota->idestadocredito_cronograma !== 1)
            || !in_array((int) $credito->idforma_pago_credito, [1, 2, 3, 4], true)) {
            throw ValidationException::withMessages(['credito' => 'El cronograma no está disponible para actualizar sus fechas.']);
        }

        $fecha = now()->startOfDay()->addDays((int) $credito->dia_gracia)->toDateString();
        $feriados = DB::table('feriados')->get();
        $primera = null;
        $ultima = null;
        foreach ($cuotas as $cuota) {
            $resultado = cronograma_fecha((int) $credito->idforma_pago_credito, $fecha, $feriados);
            $fecha = $resultado['fecha_inicio'];
            $ultima = $resultado['fecha_normal'];
            $primera ??= $ultima;
            DB::table('credito_cronograma')->where('id', $cuota->id)->update(['fechapago' => $ultima]);
        }
        DB::table('credito')->where('id', $credito->id)->update([
            'fecha_primerpago' => $primera, 'fecha_ultimopago' => $ultima,
        ]);
    }

    private static function centavos(string $valor): int
    {
        if (!preg_match('/^(\d{1,13})(?:\.(\d{1,2}))?$/D', $valor, $partes)) {
            throw ValidationException::withMessages(['monto' => 'El monto debe ser válido y tener como máximo dos decimales.']);
        }
        return ((int) $partes[1] * 100) + (int) str_pad($partes[2] ?? '', 2, '0');
    }

    private static function formatearCentavos(int $centavos): string
    {
        $absoluto = abs($centavos);
        return ($centavos < 0 ? '-' : '').intdiv($absoluto, 100).'.'.str_pad((string) ($absoluto % 100), 2, '0', STR_PAD_LEFT);
    }
}
