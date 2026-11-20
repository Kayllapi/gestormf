<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TarifarioRefinanciamiento
{
    public static function paraMontoExcedido(object $credito, $frecuencia, $monto, $cuotas): ?object
    {
        $tarifarios = DB::table('tarifario')
            ->where('idtienda', $credito->idtienda)
            ->where('idcredito_prendatario', $credito->idcredito_prendatario)
            ->where('idforma_pago_credito', $frecuencia);

        $maximo = (clone $tarifarios)->max('monto');
        if ($maximo === null || (float) $monto <= (float) $maximo) {
            return null;
        }

        return $tarifarios->where('monto', $maximo)
            ->where('cuotas', '>=', $cuotas)
            ->orderBy('cuotas')
            ->orderBy('id')
            ->first();
    }
}
