<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ListaNegraCredito
{
    public static function registrarCancelacionRefinanciado(object $credito, int $responsable): void
    {
        if ((int) $credito->idmodalidad_credito !== 4) {
            return;
        }

        self::registrar($credito, $responsable, 'CANCELACIÓN DE CRÉDITO REFINANCIADO de la cuenta C'.str_pad($credito->cuenta, 8, '0', STR_PAD_LEFT));
    }

    public static function registrar(object $credito, int $responsable, string $motivo): void
    {
        DB::transaction(function () use ($credito, $responsable, $motivo) {
            $cliente = DB::table('users')->where('id', $credito->idcliente)->lockForUpdate()->first();
            if (!$cliente) {
                throw new \RuntimeException('No se encontró el cliente para registrarlo en lista negra.');
            }
            if (DB::table('s_listanegra')->where('idcliente', $credito->idcliente)->exists()) {
                return;
            }
            DB::table('s_listanegra')->insert([
                'idcliente' => $credito->idcliente,
                'motivo' => $motivo,
                'idresponsable' => $responsable,
                'fecharegistro' => now(),
                'idtienda' => $credito->idtienda,
                'idestado' => '1',
                'db_cliente' => $cliente->nombrecompleto,
            ]);
        });
    }
}
