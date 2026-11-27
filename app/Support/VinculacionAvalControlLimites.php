<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class VinculacionAvalControlLimites
{
    public static function calcular($credito, $guardado, $detalle, array $propiosCliente, array $avaladosCliente, array $propiosAval, array $avaladosAval): array
    {
        if ($detalle === 'false') {
            $propiosCliente = json_decode($guardado->credito_saldodeduda_cliente_propio_det ?? '', true) ?: [];
            $avaladosCliente = json_decode($guardado->credito_saldodeduda_cliente_aval_det ?? '', true) ?: [];
            $propiosAval = json_decode($guardado->credito_saldodeduda_aval_propio_det ?? '', true) ?: [];
            $avaladosAval = json_decode($guardado->credito_saldodeduda_aval_aval_det ?? '', true) ?: [];
        }

        $cuentas = [];
        foreach (array_merge($avaladosCliente, $avaladosAval) as $fila) {
            if (preg_match('/^C(\d+)$/', $fila['cuenta'] ?? '', $coincidencia)) {
                $cuentas[] = $coincidencia[1];
                $cuentas[] = (string) (int) $coincidencia[1];
            }
        }

        $titulares = DB::table('credito')->whereIn('cuenta', array_unique($cuentas))
            ->get(['cuenta', 'idcliente'])->groupBy(function ($credito) {
                return (int) $credito->cuenta;
            })->map(function ($creditos) {
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
                $totalAvalCliente += (float) ($fila['saldo_vigente'] ?? 0);
            }
        }

        $cuentasIncluidas = [];
        foreach (array_merge($propiosCliente, $avaladosCliente) as $fila) {
            $cuentasIncluidas[$fila['cuenta'] ?? ''] = true;
        }
        $totalAvalSinDuplicados = 0;
        foreach (array_merge($propiosAval, $avaladosAval) as $fila) {
            $cuenta = $fila['cuenta'] ?? '';
            if ($cuenta !== '' && !isset($cuentasIncluidas[$cuenta])) {
                $totalAvalSinDuplicados += (float) ($fila['saldo_vigente'] ?? 0);
                $cuentasIncluidas[$cuenta] = true;
            }
        }

        return ['cruzado' => $clienteAvalaAval && $avalAvalaCliente, 'total_aval_cliente' => $totalAvalCliente, 'total_aval_sin_duplicados' => $totalAvalSinDuplicados];
    }
}
