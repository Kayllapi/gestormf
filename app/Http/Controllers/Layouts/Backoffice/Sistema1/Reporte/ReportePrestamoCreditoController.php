<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class ReportePrestamoCreditoController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        return view('layouts/backoffice/tienda/sistema/reporte/reporteprestamocredito/index',[
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
            if($request->input('fechainicio')!=''){
                $where[] = ['s_prestamo_credito.fechadesembolsado','>=',$request->input('fechainicio').' 00:00:00'];
            }
            if($request->input('fechafin')!=''){
                $where[] = ['s_prestamo_credito.fechadesembolsado','<=',$request->input('fechafin').' 23:59:59'];
            }
          
            if($request->input('estadocredito')==2){
                $where[] = ['s_prestamo_credito.idestadocobranza',1];
            }
            elseif($request->input('estadocredito')==3){
                $where[] = ['s_prestamo_credito.idestadocobranza',2];
            }

            if($request->input('listarpor')==1){
             
                $prestamoscreditos = DB::table('s_prestamo_credito')
                    ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
                    ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','cliente.idprestamocartera')
                    ->join('users as asesor','asesor.id','s_prestamo_cartera.idasesordestino')
                    ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
                    ->where('s_prestamo_credito.idestado', 1)
                    ->where('s_prestamo_credito.idtienda', $idtienda)
                    //->where('s_prestamo_credito.idestadocobranza','<>', 2)
                    ->where('s_prestamo_credito.idestadocredito', 4)
                    ->where('s_prestamo_credito.idestadoaprobacion', 1)
                    ->where('s_prestamo_credito.idestadodesembolso', 1)
                    ->where($where)
                    ->select(
                        's_prestamo_credito.*',
                        'cliente.identificacion as cliente_identificacion',
                        'cliente.numerotelefono as cliente_numerotelefono',
                        'cliente.direccion as cliente_direccion',
                        DB::raw('CONCAT(asesor.apellidos, ", ", asesor.nombre) as asesor'),
                        DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente'),
                        's_moneda.simbolo as monedasimbolo',
                    )
                    ->orderBy('s_prestamo_credito.fechadesembolsado','desc')
                    ->get();
         
                $totalfinal_desembolso = 0;
                $totalfinal_interes = 0;
                $totalfinal_interesdescontado = 0;
                $totalfinal_pagar = 0;
                $totalfinal_cancelado = 0;
                $totalfinal_pendiente = 0;
              
                $total_desembolso = 0;
                $total_interes = 0;
                $total_interesdescontado = 0;
                $total_pagar = 0;
                $total_cancelado = 0;
                $total_pendiente = 0;
              
                $prestamoscredito_tabla = [];
                $detalleprestamoscredito_tabla = [];
                foreach($prestamoscreditos as $value){
                    $cronograma = prestamo_cobranza_cronograma($idtienda,$value->id,0,0,1,$value->numerocuota);
                    $detalleprestamoscredito_tabla[] = [
                        'asesor' => $value->asesor,
                        'clienteidentificacion' => $value->cliente_identificacion,
                        'cliente' => $value->cliente,
                      
                        'desembolso' => $value->monto,
                        'interes' => $value->total_interes,
                        'interesdescontado' => $cronograma['interesdescontado'],
                        'pagar' => $value->total_cuota,
                        'cancelado' => $cronograma['total_cancelada_cuotaapagar'],
                        'pendiente' => $cronograma['total_pendiente_cuotaapagar'],
                        'creditocodigo' =>  str_pad($value->codigo, 8, "0", STR_PAD_LEFT),
                        'fechadesembolso' => date_format(date_create($value->fechadesembolsado), "d/m/Y"),
                        'estadocobranza' => $value->idestadocobranza==2?'<span style="color:red;">©</span>':'',
                    ];
                    $total_desembolso = $total_desembolso+$value->monto;
                    $total_interes = $total_interes+$value->total_interes;
                    $total_interesdescontado = $total_interesdescontado+$cronograma['interesdescontado'];
                    $total_pagar = $total_pagar+$value->total_cuota;
                    $total_cancelado = $total_cancelado+$cronograma['total_cancelada_cuotaapagar'];
                    $total_pendiente = $total_pendiente+$cronograma['total_pendiente_cuotaapagar'];
                }
                $prestamoscredito_tabla[] = [
                    'total_desembolso' => number_format($total_desembolso, 2, '.', ''),
                    'total_interes' => number_format($total_interes, 2, '.', ''),
                    'total_interesdescontado' => number_format($total_interesdescontado, 2, '.', ''),
                    'total_pagar' => number_format($total_pagar, 2, '.', ''),
                    'total_cancelado' => number_format($total_cancelado, 2, '.', ''),
                    'total_pendiente' => number_format($total_pendiente, 2, '.', ''),
                    'detalle' => $detalleprestamoscredito_tabla
                ];
                $totalfinal_desembolso = $totalfinal_desembolso+number_format($total_desembolso, 2, '.', '');
                $totalfinal_interes = $totalfinal_interes+number_format($total_interes, 2, '.', '');
                $totalfinal_interesdescontado = $totalfinal_interesdescontado+number_format($total_interesdescontado, 2, '.', '');
                $totalfinal_pagar = $totalfinal_pagar+number_format($total_pagar, 2, '.', '');
                $totalfinal_cancelado = $totalfinal_cancelado+number_format($total_cancelado, 2, '.', '');
                $totalfinal_pendiente = $totalfinal_pendiente+number_format($total_pendiente, 2, '.', '');
            }
            elseif($request->input('listarpor')==2){
                if($request->input('idcliente')!=''){
                    $where[] = ['cliente.id',$request->input('idcliente')];
                }
                $prestamoscreditos = DB::table('s_prestamo_credito')
                    ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
                    ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','cliente.idprestamocartera')
                    ->join('users as asesor','asesor.id','s_prestamo_cartera.idasesordestino')
                    ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
                    ->where('s_prestamo_credito.idestado', 1)
                    ->where('s_prestamo_credito.idtienda', $idtienda)
                    //->where('s_prestamo_credito.idestadocobranza','<>', 2)
                    ->where('s_prestamo_credito.idestadocredito', 4)
                    ->where('s_prestamo_credito.idestadoaprobacion', 1)
                    ->where('s_prestamo_credito.idestadodesembolso', 1)
                    ->where($where)
                    ->select(
                        's_prestamo_credito.*',
                        'cliente.identificacion as cliente_identificacion',
                        'cliente.numerotelefono as cliente_numerotelefono',
                        'cliente.direccion as cliente_direccion',
                        DB::raw('CONCAT(asesor.apellidos, ", ", asesor.nombre) as asesor'),
                        DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente'),
                        's_moneda.simbolo as monedasimbolo',
                    )
                    ->orderBy('s_prestamo_credito.fechadesembolsado','desc')
                    ->get();
         
                $totalfinal_desembolso = 0;
                $totalfinal_interes = 0;
                $totalfinal_interesdescontado = 0;
                $totalfinal_pagar = 0;
                $totalfinal_cancelado = 0;
                $totalfinal_pendiente = 0;
              
                $total_desembolso = 0;
                $total_interes = 0;
                $total_interesdescontado = 0;
                $total_pagar = 0;
                $total_cancelado = 0;
                $total_pendiente = 0;
              
                $prestamoscredito_tabla = [];
                $detalleprestamoscredito_tabla = [];
                foreach($prestamoscreditos as $value){
                    $cronograma = prestamo_cobranza_cronograma($idtienda,$value->id,0,0,1,$value->numerocuota);
                    $detalleprestamoscredito_tabla[] = [
                        'asesor' => $value->asesor,
                        'clienteidentificacion' => $value->cliente_identificacion,
                        'cliente' => $value->cliente,
                      
                        'desembolso' => $value->monto,
                        'interes' => $value->total_interes,
                        'interesdescontado' => $cronograma['interesdescontado'],
                        'pagar' => $value->total_cuota,
                        'cancelado' => $cronograma['total_cancelada_cuotaapagar'],
                        'pendiente' => $cronograma['total_pendiente_cuotaapagar'],
                        'creditocodigo' =>  str_pad($value->codigo, 8, "0", STR_PAD_LEFT),
                        'fechadesembolso' => date_format(date_create($value->fechadesembolsado), "d/m/Y"),
                        'estadocobranza' => $value->idestadocobranza==2?'<span style="color:red;">©</span>':'',
                    ];
                    $total_desembolso = $total_desembolso+$value->monto;
                    $total_interes = $total_interes+$value->total_interes;
                    $total_interesdescontado = $total_interesdescontado+$cronograma['interesdescontado'];
                    $total_pagar = $total_pagar+$value->total_cuota;
                    $total_cancelado = $total_cancelado+$cronograma['total_cancelada_cuotaapagar'];
                    $total_pendiente = $total_pendiente+$cronograma['total_pendiente_cuotaapagar'];
                }
                $total = $total_desembolso;
              
                $prestamoscredito_tabla[] = [
                    'total_desembolso' => number_format($total_desembolso, 2, '.', ''),
                    'total_interes' => number_format($total_interes, 2, '.', ''),
                    'total_interesdescontado' => number_format($total_interesdescontado, 2, '.', ''),
                    'total_pagar' => number_format($total_pagar, 2, '.', ''),
                    'total_cancelado' => number_format($total_cancelado, 2, '.', ''),
                    'total_pendiente' => number_format($total_pendiente, 2, '.', ''),
                    'detalle' => $detalleprestamoscredito_tabla
                ];
                $totalfinal_desembolso = $totalfinal_desembolso+number_format($total_desembolso, 2, '.', '');
                $totalfinal_interes = $totalfinal_interes+number_format($total_interes, 2, '.', '');
                $totalfinal_interesdescontado = $totalfinal_interesdescontado+number_format($total_interesdescontado, 2, '.', '');
                $totalfinal_pagar = $totalfinal_pagar+number_format($total_pagar, 2, '.', '');
                $totalfinal_cancelado = $totalfinal_cancelado+number_format($total_cancelado, 2, '.', '');
                $totalfinal_pendiente = $totalfinal_pendiente+number_format($total_pendiente, 2, '.', '');
            }
            elseif($request->input('listarpor')==3){
                if($request->input('idasesor')!=''){
                    $where[] = ['asesor.id',$request->input('idasesor')];
                }
                $prestamocobranzas = DB::table('s_prestamo_credito')
                    ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
                    ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','cliente.idprestamocartera')
                    ->join('users as asesor','asesor.id','s_prestamo_cartera.idasesordestino')
                    ->where('s_prestamo_credito.idestado', 1)
                    ->where('s_prestamo_credito.idtienda', $idtienda)
                    //->where('s_prestamo_credito.idestadocobranza','<>', 2)
                    ->where('s_prestamo_credito.idestadocredito', 4)
                    ->where('s_prestamo_credito.idestadoaprobacion', 1)
                    ->where('s_prestamo_credito.idestadodesembolso', 1)
                    ->where($where)
                    ->select(
                        'asesor.id as idasesor',
                        'asesor.identificacion as asesor_identificacion',
                        DB::raw('CONCAT(asesor.apellidos, ", ", asesor.nombre) as asesor'),
                    )
                    ->orderBy('asesor.apellidos','asc')
                    ->distinct()
                    ->get();
              
                $totalfinal_desembolso = 0;
                $totalfinal_interes = 0;
                $totalfinal_interesdescontado = 0;
                $totalfinal_pagar = 0;
                $totalfinal_cancelado = 0;
                $totalfinal_pendiente = 0;
                $prestamoscredito_tabla = [];
                foreach($prestamocobranzas as $value){
                    $detalleprestamoscreditos = DB::table('s_prestamo_credito')
                        ->join('users as cliente', 'cliente.id', 's_prestamo_credito.idcliente')
                        ->join('s_prestamo_cartera','s_prestamo_cartera.id','=','cliente.idprestamocartera')
                        ->join('users as asesor','asesor.id','s_prestamo_cartera.idasesordestino')
                        ->join('s_moneda', 's_moneda.id', 's_prestamo_credito.idmoneda')
                        ->where('s_prestamo_credito.idestado', 1)
                        ->where('s_prestamo_credito.idtienda', $idtienda)
                        //->where('s_prestamo_credito.idestadocobranza','<>', 2)
                        ->where('s_prestamo_credito.idestadocredito', 4)
                        ->where('s_prestamo_credito.idestadoaprobacion', 1)
                        ->where('s_prestamo_credito.idestadodesembolso', 1)
                        ->where('s_prestamo_cartera.idasesordestino',$value->idasesor)
                        ->where($where)
                        ->select(
                            's_prestamo_credito.*',
                            'cliente.identificacion as cliente_identificacion',
                            'cliente.numerotelefono as cliente_numerotelefono',
                            'cliente.direccion as cliente_direccion',
                            DB::raw('CONCAT(asesor.apellidos, ", ", asesor.nombre) as asesor'),
                            DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente'),
                            's_moneda.simbolo as monedasimbolo',
                        )
                        ->orderBy('s_prestamo_credito.fechadesembolsado','asc')
                        ->get();

                    $total_desembolso = 0;
                    $total_interes = 0;
                    $total_interesdescontado = 0;
                    $total_pagar = 0;
                    $total_cancelado = 0;
                    $total_pendiente = 0;
                    $detalleprestamoscredito_tabla = [];
                    foreach($detalleprestamoscreditos as $valuedetalle){
                        $cronograma = prestamo_cobranza_cronograma($idtienda,$valuedetalle->id,0,0,1,$valuedetalle->numerocuota);
                        $detalleprestamoscredito_tabla[] = [
                            'asesor' => $valuedetalle->asesor,
                            'clienteidentificacion' => $valuedetalle->cliente_identificacion,
                            'cliente' => $valuedetalle->cliente,
                            'desembolso' => $valuedetalle->monto,
                            'interes' => $valuedetalle->total_interes,
                            'interesdescontado' => $cronograma['interesdescontado'],
                            'pagar' => $valuedetalle->total_cuota,
                            'cancelado' => $cronograma['total_cancelada_cuotaapagar'],
                            'pendiente' => $cronograma['total_pendiente_cuotaapagar'],
                            'creditocodigo' =>  str_pad($valuedetalle->codigo, 8, "0", STR_PAD_LEFT),
                            'fechadesembolso' => date_format(date_create($valuedetalle->fechadesembolsado), "d/m/Y"),
                            'estadocobranza' => $valuedetalle->idestadocobranza==2?'<span style="color:red;">©</span>':'',
                        ];
                        $total_desembolso = $total_desembolso+$valuedetalle->monto;
                        $total_interes = $total_interes+$valuedetalle->total_interes;
                        $total_interesdescontado = $total_interesdescontado+$cronograma['interesdescontado'];
                        $total_pagar = $total_pagar+$valuedetalle->total_cuota;
                        $total_cancelado = $total_cancelado+$cronograma['total_cancelada_cuotaapagar'];
                        $total_pendiente = $total_pendiente+$cronograma['total_pendiente_cuotaapagar'];
                    }
                        
                    
                    if(count($detalleprestamoscreditos)>0){
                        $prestamoscredito_tabla[] = [
                            'asesor_identificacion' => $value->asesor_identificacion,
                            'asesor' => $value->asesor,
                            'total_desembolso' => number_format($total_desembolso, 2, '.', ''),
                            'total_interes' => number_format($total_interes, 2, '.', ''),
                            'total_interesdescontado' => number_format($total_interesdescontado, 2, '.', ''),
                            'total_pagar' => number_format($total_pagar, 2, '.', ''),
                            'total_cancelado' => number_format($total_cancelado, 2, '.', ''),
                            'total_pendiente' => number_format($total_pendiente, 2, '.', ''),
                            'detalle' => $detalleprestamoscredito_tabla
                        ];
                        $totalfinal_desembolso = $totalfinal_desembolso+number_format($total_desembolso, 2, '.', '');
                        $totalfinal_interes = $totalfinal_interes+number_format($total_interes, 2, '.', '');
                        $totalfinal_interesdescontado = $totalfinal_interesdescontado+number_format($total_interesdescontado, 2, '.', '');
                        $totalfinal_pagar = $totalfinal_pagar+number_format($total_pagar, 2, '.', '');
                        $totalfinal_cancelado = $totalfinal_cancelado+number_format($total_cancelado, 2, '.', '');
                        $totalfinal_pendiente = $totalfinal_pendiente+number_format($total_pendiente, 2, '.', '');
                    }
                }  
            } 
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/reporte/reporteprestamocredito/tablapdf',[
                'tienda' => $tienda,
                'prestamocreditos' => $prestamoscredito_tabla,
                'totalfinal_desembolso' => number_format($totalfinal_desembolso, 2, '.', ''),
                'totalfinal_interes' => number_format($totalfinal_interes, 2, '.', ''),
                'totalfinal_interesdescontado' => number_format($totalfinal_interesdescontado, 2, '.', ''),
                'totalfinal_pagar' => number_format($totalfinal_pagar, 2, '.', ''),
                'totalfinal_cancelado' => number_format($totalfinal_cancelado, 2, '.', ''),
                'totalfinal_pendiente' => number_format($totalfinal_pendiente, 2, '.', ''),
                'totalfinal_pagar' => number_format($totalfinal_pagar, 2, '.', ''),
                'listarpor' => $request->input('listarpor'),
                'fechainicio' => $request->input('fechainicio'),
                'fechafin' => $request->input('fechafin'),
                'estadocredito' => $request->input('estadocredito'),
            ]);
            return $pdf->stream('REPORTE_DE_PRESTAMOS.pdf');
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
