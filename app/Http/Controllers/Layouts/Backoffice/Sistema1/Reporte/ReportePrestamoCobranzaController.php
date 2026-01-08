<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class ReportePrestamoCobranzaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        return view('layouts/backoffice/tienda/sistema/reporte/reporteprestamocobranza/index',[
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
                $where[] = ['s_prestamo_cobranza.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
            }
            if($request->input('fechafin')!=''){
                $where[] = ['s_prestamo_cobranza.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
            }

            if($request->input('listarpor')==1){
                $prestamocobranzas = DB::table('s_prestamo_cobranza')
                    ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_cobranza.idprestamo_credito')
                    ->join('users as cliente', 'cliente.id', 's_prestamo_cobranza.idcliente')
                    ->join('users as cajero', 'cajero.id', 's_prestamo_cobranza.idcajero')
                    ->join('s_moneda','s_moneda.id','s_prestamo_cobranza.idmoneda')
                    ->where('s_prestamo_cobranza.idtienda', $idtienda)
                    ->where('s_prestamo_cobranza.idestadocobranza',2)
                    ->where('s_prestamo_cobranza.idestado',1)
                    ->where($where)
                    ->select(
                        's_prestamo_cobranza.*',
                        's_prestamo_credito.codigo as creditocodigo',
                        'cliente.identificacion as cliente_identificacion',
                        's_moneda.simbolo as monedasimbolo',
                        'cajero.identificacion as cajero_identificacion',
                        'cajero.apellidos as cajero_apellidos',
                        'cajero.nombre as cajero_nombre',
                        DB::raw('CONCAT(cajero.nombre) as cajero'),
                        DB::raw('CONCAT(cliente.apellidos, ", ", cliente.nombre) as cliente')
                    )
                    ->orderBy('s_prestamo_cobranza.fecharegistro','asc')
                    ->get();
         
                $totalfinal_cuota = 0;
                $totalfinal_acuenta = 0;
                $totalfinal_mora = 0;
                $totalfinal_total = 0;
              
                $prestamocobranzas_tabla = [];
                foreach($prestamocobranzas as $value){
                    $prestamocobranzas_tabla[] = [
                        'cajero_identificacion' => $value->cajero_identificacion,
                        'cajero_apellidos' => $value->cajero_apellidos,
                        'cajero_nombre' => $value->cajero_nombre,
                        'cajero' => $value->cajero,
                        'cliente_identificacion' => $value->cliente_identificacion,
                        'cliente' => $value->cliente,
                        'fecharegistro' => date_format(date_create($value->fecharegistro), "d/m/Y h:i A"),
                        'codigo' => str_pad($value->codigo, 8, "0", STR_PAD_LEFT),
                        'creditocodigo' => str_pad($value->creditocodigo, 8, "0", STR_PAD_LEFT),
                        'cronograma_total' => $value->cronograma_pagado,
                    ];
                    $totalfinal_total = $totalfinal_total+$value->cronograma_pagado;
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
              
                $totalfinal_cuota = 0;
                $totalfinal_acuenta = 0;
                $totalfinal_mora = 0;
                $totalfinal_total = 0;
                $prestamocobranzas_tabla = [];
                foreach($prestamocobranzas as $value){
                  
                    $detalleprestamocobranzas = DB::table('s_prestamo_cobranza')
                        ->join('users as cajero', 'cajero.id', 's_prestamo_cobranza.idcajero')
                        ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_cobranza.idprestamo_credito')
                        ->where('s_prestamo_cobranza.idtienda', $tienda->id)
                        ->where('s_prestamo_cobranza.idestadocobranza',2)
                        ->where('s_prestamo_cobranza.idestado',1)
                        ->where('s_prestamo_cobranza.idcliente',$value->idcliente)
                        ->where($where)
                        ->select(
                            's_prestamo_cobranza.*',
                            's_prestamo_credito.codigo as creditocodigo',
                            DB::raw('CONCAT(cajero.apellidos, ", ", cajero.nombre) as cajero'),
                        )
                        ->orderBy('s_prestamo_cobranza.fecharegistro','asc')
                        ->get();
                    $total_cuota = 0;
                    $total_acuenta = 0;
                    $total_mora = 0;
                    $total_total = 0;
                  
                    $detalleprestamocobranzas_tabla = [];
                    foreach($detalleprestamocobranzas as $valuedetalle){
                        $detalleprestamocobranzas_tabla[] = [
                            'cajero' => $valuedetalle->cajero,
                            'fecharegistro' => date_format(date_create($valuedetalle->fecharegistro), "d/m/Y h:i A"),
                            'codigo' => str_pad($valuedetalle->codigo, 8, "0", STR_PAD_LEFT),
                            'creditocodigo' => str_pad($valuedetalle->creditocodigo, 8, "0", STR_PAD_LEFT),
                            'cronograma_totalcuota' => number_format($valuedetalle->cronograma_totalcuota, 2, '.', ''),
                            'cronograma_totalacuenta' => number_format($valuedetalle->cronograma_acuentaanterior, 2, '.', ''),
                            'cronograma_morapagar' => $valuedetalle->cronograma_morapagar,
                            'cronograma_total' => number_format($valuedetalle->cronograma_pagado, 2, '.', ''),
                        ];
                        $total_cuota = $total_cuota+number_format($valuedetalle->cronograma_totalcuota, 2, '.', '');
                        $total_acuenta = $total_acuenta+number_format($valuedetalle->cronograma_acuentaanterior, 2, '.', '');
                        $total_mora = $total_mora+$valuedetalle->cronograma_morapagar;
                        $total_total = $total_total+number_format($valuedetalle->cronograma_pagado, 2, '.', '');
                    }
                    
                    if(count($detalleprestamocobranzas)>0){
                        $prestamocobranzas_tabla[] = [
                            'cliente_identificacion' => $value->cliente_identificacion,
                            'cliente' => $value->cliente,
                            'total_cuota' => number_format($total_cuota, 2, '.', ''),
                            'total_acuenta' => number_format($total_acuenta, 2, '.', ''),
                            'total_mora' => number_format($total_mora, 2, '.', ''),
                            'total_total' => number_format($total_total, 2, '.', ''),
                            'detalle' => $detalleprestamocobranzas_tabla
                        ];
                        $totalfinal_cuota = $totalfinal_cuota+number_format($total_cuota, 2, '.', '');
                        $totalfinal_acuenta = $totalfinal_acuenta+number_format($total_acuenta, 2, '.', '');
                        $totalfinal_mora = $totalfinal_mora+number_format($total_mora, 2, '.', '');
                        $totalfinal_total = $totalfinal_total+number_format($total_total, 2, '.', '');
                    }
                }
            }
            elseif($request->input('listarpor')==3){
                if($request->input('idcajero')!=''){
                    $where[] = ['s_prestamo_cobranza.idcajero',$request->input('idcajero')];
                }
                $prestamocobranzas = DB::table('s_prestamo_cobranza')
                    ->join('users as cajero', 'cajero.id', 's_prestamo_cobranza.idcajero')
                    ->where('s_prestamo_cobranza.idtienda', $idtienda)
                    ->where('s_prestamo_cobranza.idestadocobranza',2)
                    ->where('s_prestamo_cobranza.idestado',1)
                    ->where($where)
                    ->select(
                        'cajero.id as idcajero',
                        'cajero.identificacion as cajero_identificacion',
                        DB::raw('CONCAT(cajero.apellidos, ", ", cajero.nombre) as cajero'),
                    )
                    ->orderBy('cajero.apellidos','asc')
                    ->distinct()
                    ->get();
              
                $totalfinal_cuota = 0;
                $totalfinal_acuenta = 0;
                $totalfinal_mora = 0;
                $totalfinal_total = 0;
                $prestamocobranzas_tabla = [];
                foreach($prestamocobranzas as $value){
                    $detalleprestamocobranzas = DB::table('s_prestamo_cobranza')
                        ->join('s_prestamo_credito', 's_prestamo_credito.id', 's_prestamo_cobranza.idprestamo_credito')
                        ->join('users as cliente', 'cliente.id', 's_prestamo_cobranza.idcliente')
                        ->where('s_prestamo_cobranza.idtienda', $tienda->id)
                        ->where('s_prestamo_cobranza.idestadocobranza',2)
                        ->where('s_prestamo_cobranza.idestado',1)
                        ->where('s_prestamo_cobranza.idcajero',$value->idcajero)
                        ->where($where)
                        ->select(
                            's_prestamo_cobranza.*',
                            's_prestamo_credito.codigo as creditocodigo',
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
                            'cliente_identificacion' => $valuedetalle->cliente_identificacion,
                            'cliente' => $valuedetalle->cliente,
                            'fecharegistro' => date_format(date_create($valuedetalle->fecharegistro), "d/m/Y h:i A"),
                            'codigo' => str_pad($valuedetalle->codigo, 8, "0", STR_PAD_LEFT),
                            'creditocodigo' => str_pad($valuedetalle->creditocodigo, 8, "0", STR_PAD_LEFT),
                            'cronograma_totalcuota' => number_format($valuedetalle->cronograma_totalcuota, 2, '.', ''),
                            'cronograma_morapagar' => $valuedetalle->cronograma_morapagar,
                            'cronograma_total' => number_format($valuedetalle->cronograma_pagado, 2, '.', ''),
                        ];
                        $total_cuota = $total_cuota+number_format($valuedetalle->cronograma_totalcuota, 2, '.', '');
                        $total_mora = $total_mora+$valuedetalle->cronograma_morapagar;
                        $total_total = $total_total+number_format($valuedetalle->cronograma_pagado, 2, '.', '');
                    }
                    
                    if(count($detalleprestamocobranzas)>0){
                        $prestamocobranzas_tabla[] = [
                            'cajero_identificacion' => $value->cajero_identificacion,
                            'cajero' => $value->cajero,
                            'total_cuota' => number_format($total_cuota, 2, '.', ''),
                            'total_mora' => number_format($total_mora, 2, '.', ''),
                            'total_total' => number_format($total_total, 2, '.', ''),
                            'detalle' => $detalleprestamocobranzas_tabla
                        ];
                        $totalfinal_cuota = $totalfinal_cuota+number_format($total_cuota, 2, '.', '');
                        $totalfinal_mora = $totalfinal_mora+number_format($total_mora, 2, '.', '');
                        $totalfinal_total = $totalfinal_total+number_format($total_total, 2, '.', '');
                    }
                }
            }
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/reporte/reporteprestamocobranza/tablapdf',[
                'tienda' => $tienda,
                'prestamocobranzas' => $prestamocobranzas_tabla,
                'totalfinal_cuota' => number_format($totalfinal_cuota, 2, '.', ''),
                'totalfinal_acuenta' => number_format($totalfinal_acuenta, 2, '.', ''),
                'totalfinal_mora' => number_format($totalfinal_mora, 2, '.', ''),
                'totalfinal_total' => number_format($totalfinal_total, 2, '.', ''),
                'listarpor' => $request->input('listarpor'),
                'fechainicio' => $request->input('fechainicio'),
                'fechafin' => $request->input('fechafin'),
            ]);
            return $pdf->stream('REPORTE_DE_COBRANZA.pdf');
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
