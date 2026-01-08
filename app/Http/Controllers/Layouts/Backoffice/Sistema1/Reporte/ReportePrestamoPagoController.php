<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class ReportePrestamoPagoController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        return view('layouts/backoffice/tienda/sistema/reporte/reporteprestamopago/index',[
            'tienda' => $tienda,
        ]);
    }

    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')
            ->leftJoin('ubigeo', 'ubigeo.id', 'tienda.idubigeo')
            ->select(
                'tienda.*',
                'ubigeo.nombre as ubigeonombre',
            )
            ->where('tienda.id',$idtienda)
            ->first();
        if($id == 'showtablapdf') {
            $where = [];
        
            if($request->input('listarpor')==1){
                $prestamocreditos = DB::table('s_prestamo_credito')
                    ->join('users as cliente','cliente.id','s_prestamo_credito.idcliente')
                    ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','cliente.idprestamocartera')
                    ->join('users as asesor','asesor.id','s_prestamo_cartera.idasesordestino')
                    ->join('s_prestamo_frecuencia','s_prestamo_frecuencia.id','s_prestamo_credito.idprestamo_frecuencia')
                    ->where('s_prestamo_credito.idtienda', $idtienda)
                    ->where('s_prestamo_credito.idestadocobranza','<>', 2)
                    ->where('s_prestamo_credito.idestadocredito', 4)
                    ->where('s_prestamo_credito.idestadoaprobacion', 1)
                    ->where('s_prestamo_credito.idestadodesembolso', 1)
                    ->where($where)
                    ->select(
                        's_prestamo_credito.id as idprestamocredito',
                        's_prestamo_credito.monto as monto',
                        's_prestamo_credito.total_interes as total_interes',
                        's_prestamo_credito.total_cuotafinal as total_cuotafinal',
                        's_prestamo_credito.numerocuota as numerocuota',
                        'cliente.identificacion as cliente_identificacion',
                        'cliente.numerotelefono as cliente_numerotelefono',
                        'cliente.direccion as cliente_direccion',
                        's_prestamo_frecuencia.nombre as frecuencianombre',
                        DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente'),
                        DB::raw('CONCAT(asesor.nombre) as asesor')
                    )
                    ->orderBy('asesor.nombre', 'asc')
                    ->orderBy('cliente.apellidos', 'asc')
                    ->get();

                $prestamopagos_tabla = [];
                $totalfinal_desembolso = 0;
                $totalfinal_capital = 0;
                $totalfinal_interes = 0;
                $totalfinal_acuenta = 0;
                $totalfinal_deudacapital = 0;
                $totalfinal_mora = 0;
                $totalfinal_total = 0;
              
                foreach($prestamocreditos as $value){
                    $cronograma = prestamo_cobranza_cronograma($idtienda,$value->idprestamocredito,0,0,1,$value->numerocuota);
  
                    $total_cuota = 0;
                    $total_interes = 0;
                    $total_acuenta = 0;
                    $total_deudacapital = 0;
                    $total_moratotal = 0;
                    $total_deudatotal = 0;
                  
                    $i = 0;
                    foreach($cronograma['cuotas_pendientes'] as $valuecuotas){
                        if((($valuecuotas['tabla_fvencimiento']>=$request->fechainicio) or ($request->fechainicio=='')) and (($valuecuotas['tabla_fvencimiento']<=$request->fechafin) or ($request->fechafin==''))){
                            $total_cuota = $total_cuota+$valuecuotas['tabla_cuota'];
                            $total_interes = $total_interes+$valuecuotas['tabla_interes'];
                            $total_acuenta = $total_acuenta+$valuecuotas['tabla_acuenta'];
                            $total_deudacapital = $total_deudacapital+$valuecuotas['tabla_cuota'];
                            $total_moratotal = $total_moratotal+$valuecuotas['tabla_moraapagar'];
                            $total_deudatotal = $total_deudatotal+$valuecuotas['tabla_cuotaapagar'];
                            $i++;
                        }
                    }
                    if($i>0){
                        //$total_deudacapital = number_format($value->monto+$value->total_interes-$cronograma['select_acuentaanterior'], 2, '.', '');
                        //$total_total = number_format($total_deudacapital+$cronograma['select_moraapagar'], 2, '.', '');
                        $total_capital = number_format($total_cuota-$total_interes, 2, '.', '');
                        $total_deudacapital = number_format($total_deudacapital-$total_acuenta, 2, '.', '');
                        $prestamopagos_tabla[] = [
                            'frecuencianombre' => $value->frecuencianombre,
                            'cliente' => $value->cliente,
                            'cliente_identificacion' => $value->cliente_identificacion,
                            'cliente_direccion' => $value->cliente_direccion,
                            'cliente_numerotelefono' => $value->cliente_numerotelefono,
                            'asesor' => $value->asesor,
                            'primeratraso' => $cronograma['primeratraso'],
                            'total_desembolso' => $value->monto,
                            /*'total_interes' => $value->total_interes,
                            'total_acuenta' => $cronograma['select_acuentaanterior'],
                            'total_deudacapital' => $total_deudacapital,
                            'total_mora' => $cronograma['select_moraapagar'],
                            'total_total' => $total_deudatotal,*/
                            'total_capital' => number_format($total_capital, 2, '.', ''),
                            'total_interes' => number_format($total_interes, 2, '.', ''),
                            'total_acuenta' => number_format($total_acuenta, 2, '.', ''),
                            'total_deudacapital' => number_format($total_deudacapital, 2, '.', ''),
                            'total_mora' => number_format($total_moratotal, 2, '.', ''),
                            'total_total' => number_format($total_deudatotal, 2, '.', ''),
                            //'cuotas' => $cronograma_reporte
                        ];
                        /*$totalfinal_desembolso = $totalfinal_desembolso+$value->monto;
                        $totalfinal_interes = $totalfinal_interes+$value->total_interes;
                        $totalfinal_acuenta = $totalfinal_acuenta+$cronograma['select_acuentaanterior'];
                        $totalfinal_deudacapital = $totalfinal_deudacapital+$total_deudacapital;
                        $totalfinal_mora = $totalfinal_mora+$cronograma['select_moraapagar'];
                        $totalfinal_total = $totalfinal_total+$total_total;*/
                        $totalfinal_desembolso = $totalfinal_desembolso+$value->monto;
                        $totalfinal_capital = $totalfinal_capital+number_format($total_capital, 2, '.', '');
                        $totalfinal_interes = $totalfinal_interes+number_format($total_interes, 2, '.', '');
                        $totalfinal_acuenta = $totalfinal_acuenta+number_format($total_acuenta, 2, '.', '');
                        $totalfinal_deudacapital = $totalfinal_deudacapital+number_format($total_deudacapital, 2, '.', '');
                        $totalfinal_mora = $totalfinal_mora+number_format($total_moratotal, 2, '.', '');
                        $totalfinal_total = $totalfinal_total+number_format($total_deudatotal, 2, '.', '');
                    }
                  
                }

                $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/reporte/reporteprestamopago/tablapdf',[
                    'tienda' => $tienda,
                    'prestamopagos' => $prestamopagos_tabla,
                    'totalfinal_desembolso' => number_format($totalfinal_desembolso, 2, '.', ''),
                    'totalfinal_capital' => number_format($totalfinal_capital, 2, '.', ''),
                    'totalfinal_interes' => number_format($totalfinal_interes, 2, '.', ''),
                    'totalfinal_acuenta' => number_format($totalfinal_acuenta, 2, '.', ''),
                    'totalfinal_deudacapital' => number_format($totalfinal_deudacapital, 2, '.', ''),
                    'totalfinal_mora' => number_format($totalfinal_mora, 2, '.', ''),
                    'totalfinal_total' => number_format($totalfinal_total, 2, '.', ''),
                    'request' => $request
                ]);
                return $pdf->stream('REPORTE_DE_PAGO.pdf');
            }
            elseif($request->input('listarpor')==2){

                if($request->idcliente!=''){
                    $where[] = ['cliente.id',$request->idcliente];
                }

                $prestamocreditos = DB::table('s_prestamo_credito')
                    ->join('users as cliente','cliente.id','s_prestamo_credito.idcliente')
                    ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','cliente.idprestamocartera')
                    ->join('users as asesor','asesor.id','s_prestamo_cartera.idasesordestino')
                    ->join('s_prestamo_frecuencia','s_prestamo_frecuencia.id','s_prestamo_credito.idprestamo_frecuencia')
                    ->where('s_prestamo_credito.idtienda', $idtienda)
                    ->where('s_prestamo_credito.idestadocobranza','<>', 2)
                    ->where('s_prestamo_credito.idestadocredito', 4)
                    ->where('s_prestamo_credito.idestadoaprobacion', 1)
                    ->where('s_prestamo_credito.idestadodesembolso', 1)
                    ->where($where)
                    ->select(
                        's_prestamo_credito.id as idprestamocredito',
                        's_prestamo_credito.numerocuota as numerocuota',
                        'cliente.identificacion as cliente_identificacion',
                        'cliente.numerotelefono as cliente_numerotelefono',
                        'cliente.direccion as cliente_direccion',
                        's_prestamo_frecuencia.nombre as frecuencianombre',
                        DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente'),
                        DB::raw('CONCAT(asesor.nombre) as asesor')
                    )
                    ->orderBy('s_prestamo_credito.cronograma_total_vencida_atraso', 'asc')
                    ->orderBy('s_prestamo_credito.cronograma_total_restante_atraso', 'asc')
                    ->get();

                $prestamopagos_tabla = [];
                $totalfinal_cuota = 0;
                $totalfinal_mora = 0;
                $totalfinal_total = 0;
                $totalfinal_acuenta = 0;
                $totalfinal_apagar = 0;
                foreach($prestamocreditos as $value){
                    $cronograma = prestamo_cobranza_cronograma($idtienda,$value->idprestamocredito,0,0,1,$value->numerocuota);
                    $cronograma_reporte = [];
                    $total_cuota = 0;
                    $total_mora = 0;
                    $total_total = 0;
                    $total_acuenta = 0;
                    $total_apagar = 0;
                    $i = 0;
                    foreach($cronograma['cuotas_pendientes'] as $valuecuotas){
                        if((($valuecuotas['tabla_fvencimiento']>=$request->fechainicio) or ($request->fechainicio=='')) and (($valuecuotas['tabla_fvencimiento']<=$request->fechafin) or ($request->fechafin==''))){
                            $cronograma_reporte[] = [
                                'tabla_numero' => $valuecuotas['tabla_numero'],
                                'tabla_fechavencimiento' => $valuecuotas['tabla_fechavencimiento'],
                                'tabla_cuota' => $valuecuotas['tabla_cuota'],
                                'tabla_atraso' => $valuecuotas['tabla_atraso'],
                                'tabla_mora' => $valuecuotas['tabla_mora'],
                                'tabla_cuotatotal' => $valuecuotas['tabla_cuotatotal'],
                                'tabla_acuenta' => $valuecuotas['tabla_acuenta'],
                                'tabla_cuotaapagar' => $valuecuotas['tabla_cuotaapagar'],
                            ];
                            $total_cuota = $total_cuota+$valuecuotas['tabla_cuota'];
                            $total_mora = $total_mora+$valuecuotas['tabla_mora'];
                            $total_total = $total_total+$valuecuotas['tabla_cuotatotal'];
                            $total_acuenta = $total_acuenta+$valuecuotas['tabla_acuenta'];
                            $total_apagar = $total_apagar+$valuecuotas['tabla_cuotaapagar'];
                            $i++;
                        }
                    }
                    if($i>0){
                        $prestamopagos_tabla[] = [
                            'frecuencianombre' => $value->frecuencianombre,
                            'cliente' => $value->cliente,
                            'cliente_identificacion' => $value->cliente_identificacion,
                            'cliente_direccion' => $value->cliente_direccion,
                            'cliente_numerotelefono' => $value->cliente_numerotelefono,
                            'asesor' => $value->asesor,
                            'primeratraso' => $cronograma['primeratraso'],
                            'total_cuota' => number_format($total_cuota, 2, '.', ''),
                            'total_mora' => number_format($total_mora, 2, '.', ''),
                            'total_total' => number_format($total_total, 2, '.', ''),
                            'total_acuenta' => number_format($total_acuenta, 2, '.', ''),
                            'total_apagar' => number_format($total_apagar, 2, '.', ''),
                            'cuotas' => $cronograma_reporte
                        ];
                        $totalfinal_cuota = $totalfinal_cuota+number_format($total_cuota, 2, '.', '');
                        $totalfinal_mora = $totalfinal_mora+number_format($total_mora, 2, '.', '');
                        $totalfinal_total = $totalfinal_total+number_format($total_total, 2, '.', '');
                        $totalfinal_acuenta = $totalfinal_acuenta+number_format($total_acuenta, 2, '.', '');
                        $totalfinal_apagar = $totalfinal_apagar+number_format($total_apagar, 2, '.', '');
                    }   
                }

                $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/reporte/reporteprestamopago/tablapdf',[
                    'tienda' => $tienda,
                    'prestamopagos' => $prestamopagos_tabla,
                    'totalfinal_cuota' => number_format($totalfinal_cuota, 2, '.', ''),
                    'totalfinal_mora' => number_format($totalfinal_mora, 2, '.', ''),
                    'totalfinal_total' => number_format($totalfinal_total, 2, '.', ''),
                    'totalfinal_acuenta' => number_format($totalfinal_acuenta, 2, '.', ''),
                    'totalfinal_apagar' => number_format($totalfinal_apagar, 2, '.', ''),
                    'request' => $request
                ]);
                return $pdf->stream('REPORTE_DE_PAGO.pdf');
            }
            elseif($request->input('listarpor')==3){
                if($request->idasesor!=''){
                    $where[] = ['asesor.id',$request->idasesor];
                }

                $prestamocreditos = DB::table('s_prestamo_credito')
                    ->join('users as cliente','cliente.id','s_prestamo_credito.idcliente')
                    ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','cliente.idprestamocartera')
                    ->join('users as asesor','asesor.id','s_prestamo_cartera.idasesordestino')
                    ->join('s_prestamo_frecuencia','s_prestamo_frecuencia.id','s_prestamo_credito.idprestamo_frecuencia')
                    ->where('s_prestamo_credito.idtienda', $idtienda)
                    ->where('s_prestamo_credito.idestadocobranza','<>', 2)
                    ->where('s_prestamo_credito.idestadocredito', 4)
                    ->where('s_prestamo_credito.idestadoaprobacion', 1)
                    ->where('s_prestamo_credito.idestadodesembolso', 1)
                    ->where($where)
                    ->select(
                        's_prestamo_credito.id as idprestamocredito',
                        's_prestamo_credito.numerocuota as numerocuota',
                        'cliente.identificacion as cliente_identificacion',
                        'cliente.numerotelefono as cliente_numerotelefono',
                        'cliente.direccion as cliente_direccion',
                        's_prestamo_frecuencia.nombre as frecuencianombre',
                        DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente'),
                        DB::raw('CONCAT(asesor.nombre) as asesor')
                    )
                    ->orderBy('s_prestamo_credito.cronograma_total_vencida_atraso', 'asc')
                    ->orderBy('s_prestamo_credito.cronograma_total_restante_atraso', 'asc')
                    ->get();

                $prestamopagos_tabla = [];
                $totalfinal_cuota = 0;
                $totalfinal_mora = 0;
                $totalfinal_total = 0;
                $totalfinal_acuenta = 0;
                $totalfinal_apagar = 0;
                foreach($prestamocreditos as $value){
                    $cronograma = prestamo_cobranza_cronograma($idtienda,$value->idprestamocredito,0,0,1,$value->numerocuota);
                    $cronograma_reporte = [];
                    $total_cuota = 0;
                    $total_mora = 0;
                    $total_total = 0;
                    $total_acuenta = 0;
                    $total_apagar = 0;
                    $i = 0;
                    foreach($cronograma['cuotas_pendientes'] as $valuecuotas){
                        if((($valuecuotas['tabla_fvencimiento']>=$request->fechainicio) or ($request->fechainicio=='')) and (($valuecuotas['tabla_fvencimiento']<=$request->fechafin) or ($request->fechafin==''))){
                            $cronograma_reporte[] = [
                                'tabla_numero' => $valuecuotas['tabla_numero'],
                                'tabla_fechavencimiento' => $valuecuotas['tabla_fechavencimiento'],
                                'tabla_cuota' => $valuecuotas['tabla_cuota'],
                                'tabla_atraso' => $valuecuotas['tabla_atraso'],
                                'tabla_mora' => $valuecuotas['tabla_mora'],
                                'tabla_cuotatotal' => $valuecuotas['tabla_cuotatotal'],
                                'tabla_acuenta' => $valuecuotas['tabla_acuenta'],
                                'tabla_cuotaapagar' => $valuecuotas['tabla_cuotaapagar'],
                            ];
                            $total_cuota = $total_cuota+$valuecuotas['tabla_cuota'];
                            $total_mora = $total_mora+$valuecuotas['tabla_mora'];
                            $total_total = $total_total+$valuecuotas['tabla_cuotatotal'];
                            $total_acuenta = $total_acuenta+$valuecuotas['tabla_acuenta'];
                            $total_apagar = $total_apagar+$valuecuotas['tabla_cuotaapagar'];
                            $i++;
                        }
                    }
                    if($i>0){
                        $prestamopagos_tabla[] = [
                            'frecuencianombre' => $value->frecuencianombre,
                            'cliente' => $value->cliente,
                            'cliente_identificacion' => $value->cliente_identificacion,
                            'cliente_direccion' => $value->cliente_direccion,
                            'cliente_numerotelefono' => $value->cliente_numerotelefono,
                            'asesor' => $value->asesor,
                            'primeratraso' => $cronograma['primeratraso'],
                            'total_cuota' => number_format($total_cuota, 2, '.', ''),
                            'total_mora' => number_format($total_mora, 2, '.', ''),
                            'total_total' => number_format($total_total, 2, '.', ''),
                            'total_acuenta' => number_format($total_acuenta, 2, '.', ''),
                            'total_apagar' => number_format($total_apagar, 2, '.', ''),
                            'cuotas' => $cronograma_reporte
                        ];
                        $totalfinal_cuota = $totalfinal_cuota+number_format($total_cuota, 2, '.', '');
                        $totalfinal_mora = $totalfinal_mora+number_format($total_mora, 2, '.', '');
                        $totalfinal_total = $totalfinal_total+number_format($total_total, 2, '.', '');
                        $totalfinal_acuenta = $totalfinal_acuenta+number_format($total_acuenta, 2, '.', '');
                        $totalfinal_apagar = $totalfinal_apagar+number_format($total_apagar, 2, '.', '');
                    }   
                }

                $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/reporte/reporteprestamopago/tablapdf',[
                    'tienda' => $tienda,
                    'prestamopagos' => $prestamopagos_tabla,
                    'totalfinal_cuota' => number_format($totalfinal_cuota, 2, '.', ''),
                    'totalfinal_mora' => number_format($totalfinal_mora, 2, '.', ''),
                    'totalfinal_total' => number_format($totalfinal_total, 2, '.', ''),
                    'totalfinal_acuenta' => number_format($totalfinal_acuenta, 2, '.', ''),
                    'totalfinal_apagar' => number_format($totalfinal_apagar, 2, '.', ''),
                    'request' => $request
                ]);
                return $pdf->stream('REPORTE_DE_PAGO.pdf');
            }
        }
    }

    public function edit(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    public function update(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    public function destroy(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }
}
