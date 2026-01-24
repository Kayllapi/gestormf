<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class CvreporteconsolidadoopeinstiController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            $agencias = DB::table('tienda')->get();
            return view(sistema_view().'/cvreporteconsolidadoopeinsti/tabla',[
                'tienda' => $tienda,
                'agencias' => $agencias,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
    }
  
    public function store(Request $request, $idtienda)
    {
        
    }

    public function show(Request $request, $idtienda, $id)
    {
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view') == 'pdf_reporte'){
            $co_actual = consolidadooperaciones($tienda,$request->idagencia,$request->corte);
            //$co_actual = cvconsolidadooperaciones($tienda,$request->idagencia,$request->corte);
            $date = Carbon::createFromFormat('Y-m-d', $request->corte);
            $date->subDay(); // Subtracts 1 day
            //$co_anterior = consolidadooperaciones($tienda,$request->idagencia,$date->format('Y-m-d'));
            $co_anterior = DB::table('arqueocaja')
                ->where('idagencia',$request->idagencia)
                ->where('corte',$date->format('Y-m-d'))
                ->first();
            $data_actual = DB::table('arqueocaja')
                ->where('idagencia',$request->idagencia)
                ->where('corte',$request->corte)
                ->first();

            // code abraham
            $where = [];
            $idagencia = $request->idagencia;
            $fechacorte = $request->corte;

            if($idagencia!=''){
                $where[] = ['idtienda',$idagencia];
            }
            if($fechacorte!=''){
                $where[] = ['fecharegistro','>=',$fechacorte.' 00:00:00'];
                $where[] = ['fecharegistro','<=',$fechacorte.' 23:59:59'];
            }

            // ASIGNACION 
            $cvasignacioncapital = DB::table('cvasignacioncapital')
                ->where('idestadoeliminado',1)
                ->where('idresponsable_recfinal', '<>', 0)
                ->where('idtipooperacion', 4) // asignación
                ->where($where)
                ->sum('monto');
            $cvasignacioncapital_caja = DB::table('cvasignacioncapital')
                ->where('idestadoeliminado',1)
                ->where('idresponsable_recfinal', '<>', 0)
                ->where('idtipodestino',1) // caja
                ->where('idtipooperacion', 4) // asignación
                ->where($where)
                ->sum('monto');
            $cvasignacioncapital_cf = DB::table('cvasignacioncapital')
                ->where('idestadoeliminado',1)
                ->where('idresponsable_recfinal', '<>', 0)
                ->where('idtipodestino',2) // caja fuerte
                ->where('idtipooperacion', 4) // asignación
                ->where($where)
                ->sum('monto');
            $cvasignacioncapital_banco = DB::table('cvasignacioncapital')
                ->where('idestadoeliminado',1)
                ->where('idresponsable_recfinal', '<>', 0)
                ->where('idtipodestino',3) // banco
                ->where('idtipooperacion', 4) // asignación
                ->where($where)
                ->sum('monto');

            $cvasignacioncapital_bancos = DB::table('banco as b')
                ->leftJoin('cvasignacioncapital as c', function ($join) use ($idagencia, $fechacorte) {
                    $join->on('c.idbanco', '=', 'b.id')
                        ->where('c.idestadoeliminado', 1)
                        ->where('c.idresponsable_recfinal', '<>', 0)
                        ->where('c.idtipodestino', 3) // banco
                        ->whereIn('c.idtipooperacion', [1,2,4]) // deposito, retiro, asignación
                        ->where('c.idtienda',$idagencia)
                        ->where('c.fecharegistro','>=',$fechacorte.' 00:00:00')
                        ->where('c.fecharegistro','<=',$fechacorte.' 23:59:59');
                })
                ->groupBy('b.id', 'b.nombre', 'b.cuenta')
                ->select(
                    'b.nombre as banco',
                    DB::raw("CONCAT('(***', RIGHT(b.cuenta, 5), ')') as cuenta"),
                    DB::raw('COALESCE(SUM(c.monto), 0) as monto')
                )
                ->get();

            // CAJA 
            $caja_cvasignacioncapital_dep = DB::table('cvasignacioncapital')
                ->where('idestadoeliminado',1)
                ->where('idresponsable_recfinal', '<>', 0)
                ->where('idtipodestino', 1) // caja
                ->where('idtipooperacion', 1) // Deposito
                ->where($where)
                ->sum('monto');
            $caja_cvasignacioncapital_ret = DB::table('cvasignacioncapital')
                ->where('idestadoeliminado',1)
                ->where('idresponsable_recfinal', '<>', 0)
                ->where('idtipodestino', 1) // caja
                ->where('idtipooperacion', 2) // Retiro
                ->where($where)
                ->sum('monto');

            $cvasignacioncapital_caja = $cvasignacioncapital_caja + $caja_cvasignacioncapital_dep;
            $cvasignacioncapital_caja = $cvasignacioncapital_caja - $caja_cvasignacioncapital_ret;
            $cvasignacioncapital = $cvasignacioncapital + $caja_cvasignacioncapital_dep;
            $cvasignacioncapital = $cvasignacioncapital - $caja_cvasignacioncapital_ret;

            // CAJA FUERTE
            $cajafuerte_cvasignacioncapital_dep = DB::table('cvasignacioncapital')
                ->where('idestadoeliminado',1)
                ->where('idresponsable_recfinal', '<>', 0)
                ->where('idtipodestino', 2) // caja fuerte
                ->where('idtipooperacion', 1) // Deposito
                ->where($where)
                ->sum('monto');
            $cajafuerte_cvasignacioncapital_ret = DB::table('cvasignacioncapital')
                ->where('idestadoeliminado',1)
                ->where('idresponsable_recfinal', '<>', 0)
                ->where('idtipodestino', 2) // caja fuerte
                ->where('idtipooperacion', 2) // Retiro
                ->where($where)
                ->sum('monto');

            $cvasignacioncapital_cf = $cvasignacioncapital_cf + $cajafuerte_cvasignacioncapital_dep;
            $cvasignacioncapital_cf = $cvasignacioncapital_cf - $cajafuerte_cvasignacioncapital_ret;
            $cvasignacioncapital = $cvasignacioncapital + $cajafuerte_cvasignacioncapital_dep;
            $cvasignacioncapital = $cvasignacioncapital - $cajafuerte_cvasignacioncapital_ret;

            // BANCO 
            $banco_cvasignacioncapital_dep = DB::table('cvasignacioncapital')
                ->where('idestadoeliminado',1)
                ->where('idresponsable_recfinal', '<>', 0)
                ->where('idtipodestino', 3) // banco
                ->where('idtipooperacion', 1) // Deposito
                ->where($where)
                ->sum('monto');
            $banco_cvasignacioncapital_ret = DB::table('cvasignacioncapital')
                ->where('idestadoeliminado',1)
                ->where('idresponsable_recfinal', '<>', 0)
                ->where('idtipodestino', 3) // banco
                ->where('idtipooperacion', 2) // Retiro
                ->where($where)
                ->sum('monto');

            $cvasignacioncapital_banco = $cvasignacioncapital_banco + $banco_cvasignacioncapital_dep;
            $cvasignacioncapital_banco = $cvasignacioncapital_banco - $banco_cvasignacioncapital_ret;
            $cvasignacioncapital = $cvasignacioncapital + $banco_cvasignacioncapital_dep;
            $cvasignacioncapital = $cvasignacioncapital + $banco_cvasignacioncapital_ret;

            $banco_cvasignacioncapital_deps = DB::table('banco as b')
                ->leftJoin('cvasignacioncapital as c', function ($join) use ($idagencia, $fechacorte) {
                    $join->on('c.idbanco', '=', 'b.id')
                        ->where('c.idestadoeliminado', 1)
                        ->where('c.idresponsable_recfinal', '<>', 0)
                        ->where('c.idtipodestino', 3) // banco
                        ->where('c.idtipooperacion', 1) // deposito
                        ->where('c.idtienda',$idagencia)
                        ->where('c.fecharegistro','>=',$fechacorte.' 00:00:00')
                        ->where('c.fecharegistro','<=',$fechacorte.' 23:59:59');
                })
                ->groupBy('b.id', 'b.nombre', 'b.cuenta')
                ->select(
                    'b.nombre as banco',
                    DB::raw("CONCAT('(***', RIGHT(b.cuenta, 5), ')') as cuenta"),
                    DB::raw('COALESCE(SUM(c.monto), 0) as monto')
                )
                ->get();
            $banco_cvasignacioncapital_rets = DB::table('banco as b')
                ->leftJoin('cvasignacioncapital as c', function ($join) use ($idagencia, $fechacorte) {
                    $join->on('c.idbanco', '=', 'b.id')
                        ->where('c.idestadoeliminado', 1)
                        ->where('c.idresponsable_recfinal', '<>', 0)
                        ->where('c.idtipodestino', 3) // banco
                        ->where('c.idtipooperacion', 2) // retiro
                        ->where('c.idtienda',$idagencia)
                        ->where('c.fecharegistro','>=',$fechacorte.' 00:00:00')
                        ->where('c.fecharegistro','<=',$fechacorte.' 23:59:59');
                })
                ->groupBy('b.id', 'b.nombre', 'b.cuenta')
                ->select(
                    'b.nombre as banco',
                    DB::raw("CONCAT('(***', RIGHT(b.cuenta, 5), ')') as cuenta"),
                    DB::raw('COALESCE(SUM(c.monto), 0) as monto')
                )
                ->get();

            // INGRESO EXTRAORDINARIO 
            $caja_cvingresoextraordinario = DB::table('cvingresoextraordinario')
                ->where('idestadoeliminado',1)
                ->where('idformapago', 1) // caja
                ->where($where)
                ->sum('monto');
            $banco_cvingresoextraordinario = DB::table('cvingresoextraordinario')
                ->where('idestadoeliminado',1)
                ->where('idformapago', 2) // banco
                ->where($where)
                ->sum('monto');

            $cvasignacioncapital_caja = $cvasignacioncapital_caja + $caja_cvingresoextraordinario;
            $cvasignacioncapital_banco = $cvasignacioncapital_banco + $banco_cvingresoextraordinario;
            $cvasignacioncapital = $cvasignacioncapital + $caja_cvingresoextraordinario;
            $cvasignacioncapital = $cvasignacioncapital + $banco_cvingresoextraordinario;

            // GASTOS ADMINISTRATIVOS
            $caja_cvgastoadministrativooperativo = DB::table('cvgastoadministrativooperativo')
                ->where('idestadoeliminado',1)
                ->where('idformapago', 1) // caja
                ->where($where)
                ->sum('monto');
            $banco_cvgastoadministrativooperativo = DB::table('cvgastoadministrativooperativo')
                ->where('idestadoeliminado',1)
                ->where('idformapago', 2) // banco
                ->where($where)
                ->sum('monto');

            $cvasignacioncapital_caja = $cvasignacioncapital_caja - $caja_cvgastoadministrativooperativo;
            $cvasignacioncapital_banco = $cvasignacioncapital_banco - $banco_cvgastoadministrativooperativo;
            $cvasignacioncapital = $cvasignacioncapital - $caja_cvgastoadministrativooperativo;
            $cvasignacioncapital = $cvasignacioncapital - $banco_cvgastoadministrativooperativo;

            // COMPRA
            $caja_cvcompra = DB::table('cvcompra')
                ->where('idestadoeliminado',1)
                ->where('compra_idformapago', 1) // caja
                ->where($where)
                ->sum('valorcompra');
            $banco_cvcompra = DB::table('cvcompra')
                ->where('idestadoeliminado',1)
                ->where('compra_idformapago', 2) // banco
                ->where($where)
                ->sum('valorcompra');

            $cvasignacioncapital_caja = $cvasignacioncapital_caja - $caja_cvcompra;
            $cvasignacioncapital_banco = $cvasignacioncapital_banco - $banco_cvcompra;
            $cvasignacioncapital = $cvasignacioncapital - $caja_cvcompra;
            $cvasignacioncapital = $cvasignacioncapital - $banco_cvcompra;

            /* $banco_cvcompras = DB::table('banco as b')
                ->leftJoin('cvcompra as c', function ($join) use ($idagencia, $fechacorte) {
                    $join->on('c.idbanco', '=', 'b.id')
                        ->where('c.idestadoeliminado', 1)
                        ->where('c.idtipodestino', 3) // banco
                        ->where('c.idtipooperacion', 2) // retiro
                        ->where('c.idtienda',$idagencia)
                        ->where('c.fecharegistro','>=',$fechacorte.' 00:00:00')
                        ->where('c.fecharegistro','<=',$fechacorte.' 23:59:59');
                })
                ->groupBy('b.id', 'b.nombre', 'b.cuenta')
                ->select(
                    'b.nombre as banco',
                    DB::raw("CONCAT('(***', RIGHT(b.cuenta, 5), ')') as cuenta"),
                    DB::raw('COALESCE(SUM(c.monto), 0) as monto')
                )
                ->get(); */

            // VENTA
            $caja_cvventa = DB::table('cvventa')
                ->where('idestadoeliminado',1)
                ->where('venta_idformapago', 1) // caja
                ->where($where)
                ->sum('venta_montoventa');
            $banco_cvventa = DB::table('cvventa')
                ->where('idestadoeliminado',1)
                ->where('venta_idformapago', 2) // banco
                ->where($where)
                ->sum('venta_montoventa');

            $cvasignacioncapital_caja = $cvasignacioncapital_caja + $caja_cvventa;
            $cvasignacioncapital_banco = $cvasignacioncapital_banco + $banco_cvventa;
            $cvasignacioncapital = $cvasignacioncapital + $caja_cvventa;
            $cvasignacioncapital = $cvasignacioncapital + $banco_cvventa;

            // Totales
            $cvasignacioncapital = number_format($cvasignacioncapital, 2, '.', '');
            $cvasignacioncapital_caja = number_format($cvasignacioncapital_caja, 2, '.', '');
            $cvasignacioncapital_cf = number_format($cvasignacioncapital_cf, 2, '.', '');
            $cvasignacioncapital_banco = number_format($cvasignacioncapital_banco, 2, '.', '');
            // fin code

            $pdf = PDF::loadView(sistema_view().'/cvreporteconsolidadoopeinsti/pdf_reporte', compact(
                'co_actual',
                'co_anterior',
                'data_actual',

                'cvasignacioncapital',
                'cvasignacioncapital_caja',
                'cvasignacioncapital_cf',
                'cvasignacioncapital_banco',
                'cvasignacioncapital_bancos',

                'caja_cvasignacioncapital_dep',
                'caja_cvasignacioncapital_ret',
                'banco_cvasignacioncapital_dep',
                'banco_cvasignacioncapital_ret',
                'banco_cvasignacioncapital_deps',
                'banco_cvasignacioncapital_rets',

                'caja_cvingresoextraordinario',
                'banco_cvingresoextraordinario',
                'caja_cvgastoadministrativooperativo',
                'banco_cvgastoadministrativooperativo',
                'caja_cvcompra',
                'banco_cvcompra',
                'caja_cvventa',
                'banco_cvventa',
            ));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('REPORTE_CONSOLIDADO_OPE_INST.pdf');
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        
    }


    public function destroy(Request $request, $idtienda, $id)
    {

    }
}
