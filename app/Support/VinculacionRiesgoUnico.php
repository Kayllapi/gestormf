<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class VinculacionRiesgoUnico
{
    public static function filas($idCliente): array
    {
        $cliente = DB::table('users')->where('id', $idCliente)->first(['id', 'idestadocivil']);
        $datos = DB::table('s_users_prestamo')->where('id_s_users', $idCliente)
            ->first(['dni_pareja', 'direccion_ac_economica', 'idubigeo_ac_economica']);

        if (! $cliente) {
            return [];
        }

        $gestion = DireccionesNegocio::clientesSimilares($datos->direccion_ac_economica ?? null, $datos->idubigeo_ac_economica ?? null);
        $pareja = null;
        $dniPareja = trim((string) ($datos->dni_pareja ?? ''));
        if (in_array((int) $cliente->idestadocivil, [2, 4, 6], true) && $dniPareja !== '') {
            $parejas = DB::table('users')->where('identificacion', $dniPareja)
                ->where('id', '<>', $idCliente)->limit(2)->pluck('id');
            if ($parejas->count() === 1) {
                $pareja = $parejas->first();
            }
        }

        $ids = array_unique(array_merge($gestion, $pareja === null ? [] : [$pareja]));
        $creditos = DB::table('credito')->join('users', 'users.id', '=', 'credito.idcliente')
            ->whereIn('credito.idcliente', $ids)->where('credito.idcliente', '<>', $idCliente)
            ->where('credito.idestadocredito', 1)->whereIn('credito.estado', ['APROBADO', 'DESEMBOLSADO'])
            ->select('credito.id', 'credito.idcliente', 'credito.cuenta', 'credito.monto_solicitado', 'users.identificacion', 'users.nombrecompleto')
            ->orderBy('users.nombrecompleto')->orderBy('credito.cuenta')->get();

        $filas = [];
        foreach ($creditos as $credito) {
            $id = $credito->idcliente;
            if (! isset($filas[$id])) {
                $filas[$id] = ['identificacion' => $credito->identificacion, 'nombre' => $credito->nombrecompleto, 'gestion' => [], 'familia' => [], 'total' => 0];
            }
            $cuenta = ['cuenta' => $credito->cuenta, 'monto' => (float) $credito->monto_solicitado];
            if (in_array($id, $gestion)) {
                $filas[$id]['gestion'][] = $cuenta;
            }
            if ($pareja !== null && (string) $id === (string) $pareja) {
                $filas[$id]['familia'][] = $cuenta;
            }
            $filas[$id]['total'] += $cuenta['monto'];
        }

        return array_values($filas);
    }
}
