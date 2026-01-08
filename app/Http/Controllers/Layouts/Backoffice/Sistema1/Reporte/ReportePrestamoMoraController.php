<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class ReportePrestamoMoraController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        return view('layouts/backoffice/tienda/sistema/reporte/reporteprestamomora/index',[
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
      
            $diasatraso = -100000;
            if($request->input('check_estadomayoracero')=='on'){
              $diasatraso = 0;
            }
          
            $where = [];
            if($request->input('fechainicio')!=''){
                $where[] = ['s_prestamo_credito.fechadesembolsado','>=',$request->input('fechainicio').' 00:00:00'];
            }
            if($request->input('fechafin')!=''){
                $where[] = ['s_prestamo_credito.fechadesembolsado','<=',$request->input('fechafin').' 23:59:59'];
            }
          
            if($request->input('listarpor')==1){
              
                $prestamocreditos = DB::table('s_prestamo_credito')
                    ->join('users as cliente','cliente.id','s_prestamo_credito.idcliente')
                    ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','cliente.idprestamocartera')
                    ->join('users as asesor','asesor.id','s_prestamo_cartera.idasesordestino')
                    ->where('s_prestamo_credito.idtienda', $idtienda)
                    ->where('s_prestamo_credito.idestadocobranza','<>', 2)
                    ->where('s_prestamo_credito.idestadocredito', 4)
                    ->where('s_prestamo_credito.idestadoaprobacion', 1)
                    ->where('s_prestamo_credito.idestadodesembolso', 1)
                    ->where($where)
                    ->select(
                        'asesor.id as idasesor',
                        DB::raw('CONCAT(asesor.apellidos, ", ", asesor.nombre) as asesor')
                    )
                    ->distinct()
                    ->orderBy('asesor.nombre', 'asc')
                    ->get();
              
                $prestamomoras_tabla = [];
                $montototal = 0;
                $montoporcentaje = 0;
                $total_numeroclientes = 0;
                $array_numeroclientes  = array();
              
                foreach($prestamocreditos as $value){
                    $prestamocreditos_asesor = DB::table('s_prestamo_credito')
                        ->join('users as cliente','cliente.id','s_prestamo_credito.idcliente')
                        ->join('s_prestamo_frecuencia','s_prestamo_frecuencia.id','s_prestamo_credito.idprestamo_frecuencia')
                        ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','cliente.idprestamocartera')
                        ->where('s_prestamo_cartera.idasesordestino',$value->idasesor)
                        ->where('s_prestamo_credito.idestadocobranza','<>', 2)
                        ->where('s_prestamo_credito.idestadocredito', 4)
                        ->where('s_prestamo_credito.idestadoaprobacion', 1)
                        ->where('s_prestamo_credito.idestadodesembolso', 1)
                        ->where($where)
                        ->select(
                            's_prestamo_credito.monto as monto',
                            's_prestamo_credito.id as idprestamocredito',
                            's_prestamo_credito.numerocuota as numerocuota',
                            's_prestamo_credito.fechadesembolsado as fechadesembolsado',
                            'cliente.id as idcliente',
                            'cliente.identificacion as cliente_identificacion',
                            'cliente.numerotelefono as cliente_numerotelefono',
                            'cliente.direccion as cliente_direccion',
                            's_prestamo_frecuencia.nombre as frecuencianombre',
                            DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente'),
                        )
                        ->orderBy('cliente.apellidos', 'asc')
                        ->get();

                    $deudatotal = 0;
                    $moratotal_1 = 0;
                    $moratotal_2 = 0;
                    $moratotal_3 = 0;
                    $moratotal_4 = 0;
                    $moratotal_5 = 0;
                    $moratotal_total = 0;
                    $prestamomoras_tabladetalle = [];
                  
                    $numeroclientes = 0;
                    $desembolso = 0;
                  
                    foreach($prestamocreditos_asesor as $valuedetalle){
                      $cronograma = prestamo_cobranza_cronograma($idtienda,$valuedetalle->idprestamocredito,0,0,1,$valuedetalle->numerocuota);
                      if($cronograma['primeratraso']>$diasatraso){
                        $numeroclientes= $numeroclientes+1;
                        $mora_1 = 0;
                        $mora_2 = 0;
                        $mora_3 = 0;
                        $mora_4 = 0;
                        $mora_5 = 0;
                        $mora_cuota_vencidas = 0;
                        $total_atras = 0;
                        
                        if($cronograma['primeratraso']>=0 && $cronograma['primeratraso']<=4){
                            $mora_1 = number_format($cronograma['total_vencida_cuota']-$cronograma['total_vencida_acuenta'], 2, '.', '');
                        }
                        elseif($cronograma['primeratraso']>=5 && $cronograma['primeratraso']<=7){
                            $mora_2 = number_format($cronograma['total_vencida_cuota']-$cronograma['total_vencida_acuenta'], 2, '.', '');
                        }
                        elseif($cronograma['primeratraso']>=8 && $cronograma['primeratraso']<=11){
                            $mora_3 = number_format($cronograma['total_vencida_cuota']-$cronograma['total_vencida_acuenta'], 2, '.', '');
                        }
                        elseif($cronograma['primeratraso']>=12 && $cronograma['primeratraso']<=15){
                            $mora_4 = number_format($cronograma['total_vencida_cuota']-$cronograma['total_vencida_acuenta'], 2, '.', '');
                        }
                        elseif($cronograma['primeratraso']>=16){
                            $mora_5 = number_format($cronograma['total_vencida_cuota']-$cronograma['total_vencida_acuenta'], 2, '.', '');
                        }
                        
                        $mora_total = $cronograma['total_pendiente_mora'];
                        $prestamomoras_tabladetalle[] = [
                            'numeroclientes' => $numeroclientes,
                            'frecuencianombre' => $valuedetalle->frecuencianombre,
                            //'idcliente' => $valuedetalle->idcliente,
                            'cliente' => $valuedetalle->cliente,
                            'cliente_identificacion' => $valuedetalle->cliente_identificacion,
                            'cliente_direccion' => $valuedetalle->cliente_direccion,
                            'cliente_numerotelefono' => $valuedetalle->cliente_numerotelefono,
                            'fechadesembolso' => date_format(date_create($valuedetalle->fechadesembolsado), "d/m/Y"),
                            'deuda' => number_format($cronograma['total_pendiente_cuota']-$cronograma['total_pendiente_acuenta'], 2, '.', ''),
                            'mora_1' => number_format($mora_1, 2, '.', ''),
                            'mora_2' => number_format($mora_2, 2, '.', ''),
                            'mora_3' => number_format($mora_3, 2, '.', ''),
                            'mora_4' => number_format($mora_4, 2, '.', ''),
                            'mora_5' => number_format($mora_5, 2, '.', ''),
                            'mora_total' => number_format($mora_total, 2, '.', ''),
                        ];

                        $deudatotal = $deudatotal+number_format($cronograma['total_pendiente_cuota']-$cronograma['total_pendiente_acuenta'], 2, '.', '');
                        $moratotal_1 = $moratotal_1+number_format($mora_1, 2, '.', '');
                        $moratotal_2 = $moratotal_2+number_format($mora_2, 2, '.', '');
                        $moratotal_3 = $moratotal_3+number_format($mora_3, 2, '.', '');
                        $moratotal_4 = $moratotal_4+number_format($mora_4, 2, '.', '');
                        $moratotal_5 = $moratotal_5+number_format($mora_5, 2, '.', '');
                        $moratotal_total = $moratotal_total+number_format($mora_total, 2, '.', '');
                        $desembolso = $desembolso+number_format($valuedetalle->monto, 2, '.', '');
                        
                        //revisar si ya existe
                        if (!in_array($valuedetalle->cliente, $array_numeroclientes)) {
                            $array_numeroclientes[] = $valuedetalle->cliente;
                        }  
                      }
                    }
                    if($deudatotal>0){
                    $prestamomoras_tabla[] = [
                        'asesor' => $value->asesor,
                        'desembolso' => number_format($desembolso, 2, '.', ''),
                        'detalle' => $prestamomoras_tabladetalle,
                        'deudatotal' => number_format($deudatotal, 2, '.', ''),
                        'moratotal_1' => number_format($moratotal_1, 2, '.', ''),
                        'moratotal_2' => number_format($moratotal_2, 2, '.', ''),
                        'moratotal_3' => number_format($moratotal_3, 2, '.', ''),
                        'moratotal_4' => number_format($moratotal_4, 2, '.', ''),
                        'moratotal_5' => number_format($moratotal_5, 2, '.', ''),
                        'moratotal_total' => number_format($moratotal_total, 2, '.', ''),
                    ];
                    $montototal = $montototal+$moratotal_total;
                    $montoporcentaje = (($moratotal_2+$moratotal_3+$moratotal_4+$moratotal_5)/$deudatotal)*100; 
                    //$total_numeroclientes = $total_numeroclientes+$numeroclientes;
                    }
                }
                $total_numeroclientes = count($array_numeroclientes);
            }
            elseif($request->input('listarpor')==2){
                $whereasesor = [];
                if($request->idasesor!=''){
                    $whereasesor[] = ['asesor.id',$request->idasesor];
                }
              
                $prestamocreditos = DB::table('s_prestamo_credito')
                    ->join('users as cliente','cliente.id','s_prestamo_credito.idcliente')
                    ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','cliente.idprestamocartera')
                    ->join('users as asesor','asesor.id','s_prestamo_cartera.idasesordestino')
                    ->where('s_prestamo_credito.idtienda', $idtienda)
                    ->where('s_prestamo_credito.idestadocobranza','<>', 2)
                    ->where('s_prestamo_credito.idestadocredito', 4)
                    ->where('s_prestamo_credito.idestadoaprobacion', 1)
                    ->where('s_prestamo_credito.idestadodesembolso', 1)
                    ->where($whereasesor)
                    ->where($where)
                    ->select(
                        'asesor.id as idasesor',
                        DB::raw('CONCAT(asesor.apellidos, ", ", asesor.nombre) as asesor')
                    )
                    ->distinct()
                    ->orderBy('asesor.nombre', 'asc')
                    ->get();
              
                $prestamomoras_tabla = [];
                $montototal = 0;
                $montoporcentaje = 0;
                $total_numeroclientes = 0;
                $array_numeroclientes  = array();
              
                foreach($prestamocreditos as $value){
                    $prestamocreditos_asesor = DB::table('s_prestamo_credito')
                        ->join('users as cliente','cliente.id','s_prestamo_credito.idcliente')
                        ->join('s_prestamo_frecuencia','s_prestamo_frecuencia.id','s_prestamo_credito.idprestamo_frecuencia')
                        ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','cliente.idprestamocartera')
                        ->where('s_prestamo_cartera.idasesordestino',$value->idasesor)
                        ->where('s_prestamo_credito.idestadocobranza','<>', 2)
                        ->where('s_prestamo_credito.idestadocredito', 4)
                        ->where('s_prestamo_credito.idestadoaprobacion', 1)
                        ->where('s_prestamo_credito.idestadodesembolso', 1)
                        ->where($where)
                        ->select(
                            's_prestamo_credito.monto as monto',
                            's_prestamo_credito.id as idprestamocredito',
                            's_prestamo_credito.numerocuota as numerocuota',
                            's_prestamo_credito.fechadesembolsado as fechadesembolsado',
                            'cliente.identificacion as cliente_identificacion',
                            'cliente.numerotelefono as cliente_numerotelefono',
                            'cliente.direccion as cliente_direccion',
                            's_prestamo_frecuencia.nombre as frecuencianombre',
                            DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente'),
                        )
                        ->orderBy('cliente.apellidos', 'asc')
                        ->get();

                    $deudatotal = 0;
                    $moratotal_1 = 0;
                    $moratotal_2 = 0;
                    $moratotal_3 = 0;
                    $moratotal_4 = 0;
                    $moratotal_5 = 0;
                    $moratotal_total = 0;
                    $prestamomoras_tabladetalle = [];
                    $montoporcentaje = 0;
                    $numeroclientes = 0;
                    $desembolso = 0;
                  
                    foreach($prestamocreditos_asesor as $valuedetalle){
                      $cronograma = prestamo_cobranza_cronograma($idtienda,$valuedetalle->idprestamocredito,0,0,1,$valuedetalle->numerocuota);
                      if($cronograma['primeratraso']>$diasatraso){
                        $numeroclientes= $numeroclientes+1;
                        $mora_1 = 0;
                        $mora_2 = 0;
                        $mora_3 = 0;
                        $mora_4 = 0;
                        $mora_5 = 0;
                        $mora_cuota_vencidas = 0;
                        $total_atras = 0;
                        
                        if($cronograma['primeratraso']>=0 && $cronograma['primeratraso']<=4){
                            $mora_1 = number_format($cronograma['total_vencida_cuota']-$cronograma['total_vencida_acuenta'], 2, '.', '');
                        }
                        elseif($cronograma['primeratraso']>=5 && $cronograma['primeratraso']<=7){
                            $mora_2 = number_format($cronograma['total_vencida_cuota']-$cronograma['total_vencida_acuenta'], 2, '.', '');
                        }
                        elseif($cronograma['primeratraso']>=8 && $cronograma['primeratraso']<=11){
                            $mora_3 = number_format($cronograma['total_vencida_cuota']-$cronograma['total_vencida_acuenta'], 2, '.', '');
                        }
                        elseif($cronograma['primeratraso']>=12 && $cronograma['primeratraso']<=15){
                            $mora_4 = number_format($cronograma['total_vencida_cuota']-$cronograma['total_vencida_acuenta'], 2, '.', '');
                        }
                        elseif($cronograma['primeratraso']>=16){
                            $mora_5 = number_format($cronograma['total_vencida_cuota']-$cronograma['total_vencida_acuenta'], 2, '.', '');
                        }
                        
                        $mora_total = $cronograma['total_pendiente_mora'];
                        $prestamomoras_tabladetalle[] = [
                            'numeroclientes' => $numeroclientes,
                            'frecuencianombre' => $valuedetalle->frecuencianombre,
                            'cliente' => $valuedetalle->cliente,
                            'cliente_identificacion' => $valuedetalle->cliente_identificacion,
                            'cliente_direccion' => $valuedetalle->cliente_direccion,
                            'cliente_numerotelefono' => $valuedetalle->cliente_numerotelefono,
                            'fechadesembolso' => date_format(date_create($valuedetalle->fechadesembolsado), "d/m/Y"),
                            'deuda' => number_format($cronograma['total_pendiente_cuota']-$cronograma['total_pendiente_acuenta'], 2, '.', ''),
                            'mora_1' => number_format($mora_1, 2, '.', ''),
                            'mora_2' => number_format($mora_2, 2, '.', ''),
                            'mora_3' => number_format($mora_3, 2, '.', ''),
                            'mora_4' => number_format($mora_4, 2, '.', ''),
                            'mora_5' => number_format($mora_5, 2, '.', ''),
                            'mora_total' => number_format($mora_total, 2, '.', ''),
                        ];

                        $deudatotal = $deudatotal+number_format($cronograma['total_pendiente_cuota']-$cronograma['total_pendiente_acuenta'], 2, '.', '');
                        $moratotal_1 = $moratotal_1+number_format($mora_1, 2, '.', '');
                        $moratotal_2 = $moratotal_2+number_format($mora_2, 2, '.', '');
                        $moratotal_3 = $moratotal_3+number_format($mora_3, 2, '.', '');
                        $moratotal_4 = $moratotal_4+number_format($mora_4, 2, '.', '');
                        $moratotal_5 = $moratotal_5+number_format($mora_5, 2, '.', '');
                        $moratotal_total = $moratotal_total+number_format($mora_total, 2, '.', '');
                        $desembolso = $desembolso+number_format($valuedetalle->monto, 2, '.', '');
                        
                        //revisar si ya existe
                        if (!in_array($valuedetalle->cliente, $array_numeroclientes)) {
                            $array_numeroclientes[] = $valuedetalle->cliente;
                        }  
                      }
                    }
                    if($deudatotal>0){
                    $prestamomoras_tabla[] = [
                        'asesor' => $value->asesor,
                        'desembolso' => number_format($desembolso, 2, '.', ''),
                        'detalle' => $prestamomoras_tabladetalle,
                        'deudatotal' => number_format($deudatotal, 2, '.', ''),
                        'moratotal_1' => number_format($moratotal_1, 2, '.', ''),
                        'moratotal_2' => number_format($moratotal_2, 2, '.', ''),
                        'moratotal_3' => number_format($moratotal_3, 2, '.', ''),
                        'moratotal_4' => number_format($moratotal_4, 2, '.', ''),
                        'moratotal_5' => number_format($moratotal_5, 2, '.', ''),
                        'moratotal_total' => number_format($moratotal_total, 2, '.', ''),
                    ];
                    $montototal = $montototal+$moratotal_total;
                    $montoporcentaje = (($moratotal_2+$moratotal_3+$moratotal_4+$moratotal_5)/$deudatotal)*100; 
                    }
                }
                $total_numeroclientes = count($array_numeroclientes);
            }

            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/reporte/reporteprestamomora/tablapdf',[
                'tienda' => $tienda,
                'prestamomoras' => $prestamomoras_tabla,
                'total' => number_format($montototal, 2, '.', ''),
                'totalporcentaje' => number_format($montoporcentaje, 2, '.', ''),
                'numeroclientes' => $total_numeroclientes,
                'request' => $request
            ]);
            return $pdf->stream('REPORTE_DE_MORA.pdf');
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
