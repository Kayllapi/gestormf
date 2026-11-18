<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class NumeracionCuentaCredito
{
    /** Reserva y asigna la cuenta en la misma transaccion que protege la secuencia. */
    public static function asignar(int $credito, array $datos = []): int
    {
        return DB::transaction(function () use ($credito, $datos) {
            $secuencia = DB::table('secuencia_cuentas_credito')->where('id', 1)->lockForUpdate()->first();
            if (!$secuencia) {
                throw new \RuntimeException('No existe la secuencia de cuentas de credito.');
            }
            $cuenta = max((int) $secuencia->ultimo, (int) DB::table('credito')->max('cuenta')) + 1;
            DB::table('secuencia_cuentas_credito')->where('id', 1)->update(['ultimo' => $cuenta]);
            if (DB::table('credito')->where('id', $credito)->update(array_merge($datos, ['cuenta' => $cuenta])) !== 1) {
                throw new \RuntimeException('No se pudo asignar la cuenta al credito.');
            }
            return $cuenta;
        }, 3);
    }
}
