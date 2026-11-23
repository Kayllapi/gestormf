<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ExtornoMovimientoInterno
{
    public static function eliminar(int $id, int $usuario, bool $conDepositos): void
    {
        DB::transaction(function () use ($id, $usuario, $conDepositos) {
            $movimiento = DB::table('movimientointernodinero')->where('id', $id)
                ->where('idestadoeliminado', 1)->lockForUpdate()->first();
            abort_unless($movimiento, 404);

            $datos = ['fecha_eliminado' => now(), 'idestadoeliminado' => 2, 'idresponsble_eliminado' => $usuario];
            if ($conDepositos) {
                DB::table('movimientointernodinero')->where('idmovimientointernodinero', $id)
                    ->where('idestadoeliminado', 1)->update($datos + ['extorno_agrupado' => true]);
            }
            DB::table('movimientointernodinero')->where('id', $id)->update($datos);
        });
    }
}
