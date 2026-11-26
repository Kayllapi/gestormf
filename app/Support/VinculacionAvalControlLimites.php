<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class VinculacionAvalControlLimites
{
    public static function calcular($credito, $guardado, $detalle, array $avaladosCliente, array $avaladosAval): array
    {
        if ($detalle === 'false') {
            $avaladosCliente = json_decode($guardado->credito_saldodeduda_cliente_aval_det ?? '', true) ?: [];
            $avaladosAval = json_decode($guardado->credito_saldodeduda_aval_aval_det ?? '', true) ?: [];
        }

        $cuentas = [];
        foreach (array_merge($avaladosCliente, $avaladosAval) as $fila) {
            if (preg_match('/^C(\d+)$/', $fila['cuenta'] ?? '', $coincidencia)) {
                $cuentas[] = (int) $coincidencia[1];
            }
        }

        $titulares = DB::table('credito')->whereIn('cuenta', array_unique($cuentas))
            ->get(['cuenta', 'idcliente'])->groupBy('cuenta')->map(function ($creditos) {
                $ids = $creditos->pluck('idcliente')->unique();
                return $ids->count() === 1 ? $ids->first() : null;
            });

        $titular = static function ($fila) use ($titulares) {
            return preg_match('/^C(\d+)$/', $fila['cuenta'] ?? '', $coincidencia)
                ? $titulares->get((int) $coincidencia[1]) : null;
        };

        $clienteAvalaAval = false;
        foreach ($avaladosCliente as $fila) {
            if ($credito->idaval && (int) $titular($fila) === (int) $credito->idaval) {
                $clienteAvalaAval = true;
                break;
            }
        }

        $avalAvalaCliente = false;
        $totalAvalCliente = 0;
        foreach ($avaladosAval as $fila) {
            if ((int) $titular($fila) === (int) $credito->idcliente) {
                $avalAvalaCliente = true;
                $totalAvalCliente += (float) ($detalle === 'false' ? ($fila['saldo_vigente'] ?? 0) : ($fila['cuota_pendiente'] ?? 0));
            }
        }

        return ['cruzado' => $clienteAvalaAval && $avalAvalaCliente, 'total_aval_cliente' => $totalAvalCliente];
    }
}
