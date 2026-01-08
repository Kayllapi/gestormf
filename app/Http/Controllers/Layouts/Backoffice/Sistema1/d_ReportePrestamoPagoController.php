<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class ReportePrestamoPagoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$idtienda)
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
      
        if($request->input('view') == 'tablapdf') {
            $where = [];
            if($request->input('fechainicio')!=''){
                $where[] = ['s_prestamo_cobranza.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
            }
            if($request->input('fechafin')!=''){
                $where[] = ['s_prestamo_cobranza.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
            }

            if($request->input('listarpor')==1){
                $prestamocobranzas = DB::table('s_prestamo_cobranza')
                    ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_cobranza.idprestamo_credito')
                    ->join('users as cliente', 'cliente.id', 's_prestamo_cobranza.idcliente')
                    ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
                    ->join('s_moneda','s_moneda.id','s_prestamo_credito.idmoneda')
                    ->where('s_prestamo_cobranza.idtienda', $idtienda)
                    ->where('s_prestamo_cobranza.idestadocobranza',2)
                    ->where('s_prestamo_cobranza.idestado',1)
                    ->where($where)
                    ->select(
                        's_prestamo_cobranza.*',
                        's_prestamo_credito.codigo as creditocodigo',
                        'cliente.identificacion as cliente_identificacion',
                        's_moneda.simbolo as monedasimbolo',
                        DB::raw('CONCAT(asesor.apellidos, ", ", asesor.nombre) as asesor'),
                        DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente')
                    )
                    ->orderBy('s_prestamo_cobranza.fecharegistro','asc')
                    ->get();
         
                $total = 0;
                $prestamocobranzas_tabla = [];
                foreach($prestamocobranzas as $value){
                    $prestamocobranzas_tabla[] = [
                        'asesor' => $value->asesor,
                        'cliente' => $value->cliente,
                        'fecharegistro' => date_format(date_create($value->fecharegistro), "d/m/Y h:i A"),
                        'codigo' => $value->codigo,
                        'creditocodigo' => $value->creditocodigo,
                        'cronograma_total' => $value->cronograma_total,
                    ];
                    $total = $total+$value->cronograma_total;
                }
            }
            elseif($request->input('listarpor')==2){
                if($request->input('idcliente')!=''){
                    $where[] = ['cliente.id',$request->input('idcliente')];
                }
                $prestamocobranzas = DB::table('s_prestamo_cobranza')
                    ->join('users as cliente', 'cliente.id', 's_prestamo_cobranza.idcliente')
                    ->where('s_prestamo_cobranza.idtienda', $idtienda)
                    ->where('s_prestamo_cobranza.idestadocobranza',2)
                    ->where('s_prestamo_cobranza.idestado',1)
                    ->where($where)
                    ->select(
                        'cliente.id as idcliente',
                        'cliente.identificacion as cliente_identificacion',
                        DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente'),
                    )
                    ->orderBy('cliente.apellidos','asc')
                    ->distinct()
                    ->get();
              
                $total = 0;
                $prestamocobranzas_tabla = [];
                foreach($prestamocobranzas as $value){
                  
                    $detalleprestamocobranzas = DB::table('s_prestamo_cobranza')
                        ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_cobranza.idprestamo_credito')
                        ->where('s_prestamo_cobranza.idtienda', $tienda->id)
                        ->where('s_prestamo_cobranza.idestadocobranza',2)
                        ->where('s_prestamo_cobranza.idestado',1)
                        ->where('s_prestamo_cobranza.idcliente',$value->idcliente)
                        ->select(
                            's_prestamo_cobranza.*',
                            's_prestamo_credito.codigo as creditocodigo',
                        )
                        ->orderBy('s_prestamo_cobranza.fecharegistro','asc')
                        ->get();
                    $total_cuota = 0;
                    $total_mora = 0;
                    $total_total = 0;
                  
                    $detalleprestamocobranzas_tabla = [];
                    foreach($detalleprestamocobranzas as $valuedetalle){
                        $detalleprestamocobranzas_tabla[] = [
                            'fecharegistro' => date_format(date_create($valuedetalle->fecharegistro), "d/m/Y h:i A"),
                            'codigo' => $valuedetalle->codigo,
                            'creditocodigo' => $valuedetalle->creditocodigo,
                            'cronograma_totalcuota' => $valuedetalle->cronograma_totalcuota,
                            'cronograma_morapagar' => $valuedetalle->cronograma_morapagar,
                            'cronograma_total' => $valuedetalle->cronograma_total,
                        ];
                        $total_cuota = $total_cuota+$valuedetalle->cronograma_totalcuota;
                        $total_mora = $total_mora+$valuedetalle->cronograma_morapagar;
                        $total_total = $total_total+$valuedetalle->cronograma_total;
                    }
                    
                    $prestamocobranzas_tabla[] = [
                        'cliente_identificacion' => $value->cliente_identificacion,
                        'cliente' => $value->cliente,
                        'total_cuota' => number_format($total_cuota, 2, '.', ''),
                        'total_mora' => number_format($total_mora, 2, '.', ''),
                        'total_total' => number_format($total_total, 2, '.', ''),
                        'detalle' => $detalleprestamocobranzas_tabla
                    ];
                    $total = $total+$total_total;
                }
            }
            elseif($request->input('listarpor')==3){
                if($request->input('idasesor')!=''){
                    $where[] = ['asesor.id',$request->input('idasesor')];
                }
                $prestamocobranzas = DB::table('s_prestamo_cobranza')
                    ->join('users as asesor', 'asesor.id', 's_prestamo_cobranza.idasesor')
                    ->where('s_prestamo_cobranza.idtienda', $idtienda)
                    ->where('s_prestamo_cobranza.idestadocobranza',2)
                    ->where('s_prestamo_cobranza.idestado',1)
                    ->where($where)
                    ->select(
                        'asesor.id as idasesor',
                        'asesor.identificacion as asesor_identificacion',
                        DB::raw('CONCAT(asesor.apellidos, ", ", asesor.nombre) as asesor'),
                    )
                    ->orderBy('asesor.apellidos','asc')
                    ->distinct()
                    ->get();
              
                $total = 0;
                $prestamocobranzas_tabla = [];
                foreach($prestamocobranzas as $value){
                    $detalleprestamocobranzas = DB::table('s_prestamo_cobranza')
                        ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_cobranza.idprestamo_credito')
                        ->join('users as cliente', 'cliente.id', 's_prestamo_cobranza.idcliente')
                        ->join('users as asesor', 'asesor.id', 's_prestamo_credito.idasesor')
                        ->where('s_prestamo_cobranza.idtienda', $tienda->id)
                        ->where('s_prestamo_cobranza.idestadocobranza',2)
                        ->where('s_prestamo_cobranza.idestado',1)
                        ->where('s_prestamo_cobranza.idasesor',$value->idasesor)
                        ->select(
                            's_prestamo_cobranza.*',
                            's_prestamo_credito.codigo as creditocodigo',
                            'asesor.nombre as asesor_nombre',
                            'cliente.identificacion as cliente_identificacion',
                            DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente')
                        )
                        ->orderBy('s_prestamo_cobranza.fecharegistro','asc')
                        ->get();
                  
                    $total_cuota = 0;
                    $total_mora = 0;
                    $total_total = 0;
                  
                    $detalleprestamocobranzas_tabla = [];
                    foreach($detalleprestamocobranzas as $valuedetalle){
                        $detalleprestamocobranzas_tabla[] = [
                            'cliente' => $valuedetalle->cliente,
                            'fecharegistro' => date_format(date_create($valuedetalle->fecharegistro), "d/m/Y h:i A"),
                            'codigo' => $valuedetalle->codigo,
                            'creditocodigo' => $valuedetalle->creditocodigo,
                            'cronograma_totalcuota' => $valuedetalle->cronograma_totalcuota,
                            'cronograma_morapagar' => $valuedetalle->cronograma_morapagar,
                            'cronograma_total' => $valuedetalle->cronograma_total,
                        ];
                        $total_cuota = $total_cuota+$valuedetalle->cronograma_totalcuota;
                        $total_mora = $total_mora+$valuedetalle->cronograma_morapagar;
                        $total_total = $total_total+$valuedetalle->cronograma_total;
                    }
                  
                    $prestamocobranzas_tabla[] = [
                        'asesor_identificacion' => $value->asesor_identificacion,
                        'asesor' => $value->asesor,
                        'total_cuota' => number_format($total_cuota, 2, '.', ''),
                        'total_mora' => number_format($total_mora, 2, '.', ''),
                        'total_total' => number_format($total_total, 2, '.', ''),
                        'detalle' => $detalleprestamocobranzas_tabla
                    ];
                    $total = $total+$total_total;
                }
            }
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/reporteprestamopago/tablapdf',[
                'tienda' => $tienda,
                'prestamocobranzas' => $prestamocobranzas_tabla,
                'total' => number_format($total, 2, '.', ''),
                'listarpor' => $request->input('listarpor'),
                'fechainicio' => $request->input('fechainicio'),
                'fechafin' => $request->input('fechafin'),
            ]);
            return $pdf->stream('REPORTE_DE_PAGO.pdf');
        }
        else{
            return view('layouts/backoffice/tienda/sistema/reporteprestamopago/index',[
              'tienda' => $tienda,
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }
}
