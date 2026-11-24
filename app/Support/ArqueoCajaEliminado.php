<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArqueoCajaEliminado
{
    public static function eliminar(string $tipo, int $id, int $usuario, int $responsable): object
    {
        $tabla = $tipo === 'cv' ? 'cvarqueocaja' : 'arqueocaja';

        return DB::transaction(function () use ($tipo, $id, $usuario, $responsable, $tabla) {
            $arqueo = DB::table($tabla)->where('id', $id)->lockForUpdate()->first();
            abort_if(!$arqueo, 404, 'El arqueo ya no existe.');
            $denominaciones = DB::table($tabla.'_denominacion')->where('idarqueocaja', $id)->get();

            DB::table('arqueocaja_eliminaciones')->insert([
                'tipo' => $tipo,
                'idarqueocaja' => $arqueo->id,
                'idagencia' => $arqueo->idagencia,
                'idresponsable' => $arqueo->idresponsable,
                'idusuario_eliminacion' => $usuario,
                'idresponsable_eliminacion' => $responsable,
                'corte' => $arqueo->corte,
                'total' => $arqueo->total,
                'fecha_eliminacion' => now(),
                'datos_arqueo' => json_encode($arqueo, JSON_THROW_ON_ERROR),
                'denominaciones' => json_encode($denominaciones, JSON_THROW_ON_ERROR),
            ]);

            DB::table($tabla.'_denominacion')->where('idarqueocaja', $id)->delete();
            DB::table($tabla)->where('id', $id)->delete();

            return $arqueo;
        });
    }

    public static function consulta(Request $request, string $tipo)
    {
        return DB::table('arqueocaja_eliminaciones as ae')
            ->leftJoin('users as responsable', 'responsable.id', '=', 'ae.idresponsable')
            ->leftJoin('users as responsableeliminado', 'responsableeliminado.id', '=', 'ae.idresponsable_eliminacion')
            ->leftJoin('tienda', 'tienda.id', '=', 'ae.idagencia')
            ->where('ae.tipo', $tipo)
            ->when($request->idagencia !== null && $request->idagencia !== '', fn ($q) => $q->where('ae.idagencia', $request->idagencia))
            ->when($request->fecha_inicio, fn ($q) => $q->where('ae.fecha_eliminacion', '>=', $request->fecha_inicio.' 00:00:00'))
            ->when($request->fecha_fin, fn ($q) => $q->where('ae.fecha_eliminacion', '<=', $request->fecha_fin.' 23:59:59'));
    }

    public static function paraReporte(Request $request, string $tipo)
    {
        $consulta = self::consulta($request, $tipo);

        if ($tipo === 'cv') {
            return $consulta->selectRaw("'ELIM. ARQUEO CAJA' as operacion, CONCAT('A', LPAD(ae.idarqueocaja, GREATEST(8, LENGTH(ae.idarqueocaja)), '0')) as codigo, '--' as cuenta, ae.fecha_eliminacion as fechaextorno, '--' as pago_cuota, '' as detalleoperacion, CONCAT('Corte: ', ae.corte) as descripcion, ae.total as total_pagar, '' as banco, '' as numerooperacion, responsable.codigo as codigoresponsable, responsableeliminado.codigo as codigoresponsableeliminado, tienda.nombreagencia as tiendanombre");
        }

        return $consulta->selectRaw("'ELIM. ARQUEO CAJA' as operacion, '--' as cuenta, ae.fecha_eliminacion as fechaextorno, '--' as pago_cuota, ae.total as total_pagar, '' as banco, '' as cuenta_bancaria, '' as numerooperacion, CONCAT('A', LPAD(ae.idarqueocaja, GREATEST(8, LENGTH(ae.idarqueocaja)), '0')) as codigo, CONCAT('Corte: ', ae.corte) as nombrecliente, responsable.codigo as codigoresponsable, tienda.nombreagencia as tiendanombre, responsableeliminado.codigo as codigoresponsableeliminado, '' as fc");
    }
}
