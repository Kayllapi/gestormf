<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class ReportePrestamoCarteraclienteController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        return view('layouts/backoffice/tienda/sistema/reporte/reporteprestamocarteracliente/index',[
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

            if($request->idasesor!=''){
                $where[] = ['asesordestino.id',$request->idasesor];
            }
              
            $prestamocateras = DB::table('s_prestamo_cartera')
                ->join('users as asesordestino', 'asesordestino.id', 's_prestamo_cartera.idasesordestino')
                ->where('s_prestamo_cartera.idtienda', $idtienda)
                ->where('s_prestamo_cartera.idestado', 1)
                ->where('asesordestino.idestado', 1)
                ->where($where)
                ->select(
                    'asesordestino.id as idasesor',
                    DB::raw('IF(asesordestino.idtipopersona=1,
                    CONCAT(asesordestino.apellidos,", ",asesordestino.nombre),
                    CONCAT(asesordestino.apellidos)) as asesor'),
                )
                ->distinct()
                ->orderBy('asesordestino.apellidos','asc')
                ->get();
                
            $prestamocartera_tabla = [];
            foreach($prestamocateras as $value){
                $prestamocateras_detalle = DB::table('s_prestamo_cartera')
                    ->join('users as cliente', 'cliente.idprestamocartera', 's_prestamo_cartera.id')
                    ->join('ubigeo', 'ubigeo.id', 'cliente.idubigeo')
                    ->where('s_prestamo_cartera.idasesordestino', $value->idasesor)
                    ->where('cliente.idestado', 1)
                    ->select(
                        'cliente.id as idcliente',
                        'cliente.identificacion as cliente_identificacion',
                        'cliente.nombre as cliente_nombre',
                        'cliente.apellidos as cliente_apellidos',
                        'cliente.numerotelefono as cliente_numerotelefono',
                        'cliente.direccion as cliente_direccion',
                        'ubigeo.nombre as ubigeonombre',
                    )
                    ->orderBy('cliente.apellidos','asc')
                    ->get();
                $detalleprestamocartera_tabla = [];
                foreach($prestamocateras_detalle as $valuedetalle){
                  
                    $cantidad_garante = DB::table('s_prestamo_credito')
                       ->where('s_prestamo_credito.idgarante', $valuedetalle->idcliente)
                       ->where('s_prestamo_credito.idestado', 1)
                       ->where('s_prestamo_credito.idtienda', $tienda->id)
                       ->whereIn('s_prestamo_credito.idestadocredito', [4])
                       ->whereIn('s_prestamo_credito.idestadodesembolso', [1])
                       ->where('s_prestamo_credito.idestadocobranza', 1)
                       ->count();
                  
                    $cantidad_cliente = DB::table('s_prestamo_credito')
                       ->where('s_prestamo_credito.idcliente', $valuedetalle->idcliente)
                       ->where('s_prestamo_credito.idestado', 1)
                       ->where('s_prestamo_credito.idtienda', $tienda->id)
                       ->whereIn('s_prestamo_credito.idestadocredito', [4])
                       ->whereIn('s_prestamo_credito.idestadodesembolso', [1])
                       ->where('s_prestamo_credito.idestadocobranza', 1)
                       ->count();
                  
                    $cantidad_creditos = DB::table('s_prestamo_credito')
                       ->where('s_prestamo_credito.idcliente', $valuedetalle->idcliente)
                       ->where('s_prestamo_credito.idestado', 1)
                       ->where('s_prestamo_credito.idtienda', $tienda->id)
                       ->whereIn('s_prestamo_credito.idestadocredito', [4])
                       ->whereIn('s_prestamo_credito.idestadodesembolso', [1])
                       ->where('s_prestamo_credito.idestadocobranza', 1)
                       ->count();
                              
                    
                    $count = 0;
                    if($request->idtipo==3){
                        if($cantidad_garante>0 && $cantidad_cliente>0){
                            $count = 1;
                            /*$detalleprestamocartera_tabla[] = [
                                'idcliente' => $valuedetalle->idcliente,
                                'cliente_identificacion' => $valuedetalle->cliente_identificacion,
                                'cliente_nombre' => $valuedetalle->cliente_nombre,
                                'cliente_apellidos' => $valuedetalle->cliente_apellidos,
                                'cliente_numerotelefono' => $valuedetalle->cliente_numerotelefono,
                                'cliente_direccion' => $valuedetalle->cliente_direccion,
                                'ubigeonombre' => $valuedetalle->ubigeonombre,
                                'tipo' => 'CLIENTE/AVAL',
                                'estado' => $cantidad_creditos>0?'ACTIVO':'INACTIVO',
                            ];*/
                        }
                    }elseif($request->idtipo==2){
                        if($cantidad_garante>0){
                            $count = 1;
                            /*$detalleprestamocartera_tabla[] = [
                                'idcliente' => $valuedetalle->idcliente,
                                'cliente_identificacion' => $valuedetalle->cliente_identificacion,
                                'cliente_nombre' => $valuedetalle->cliente_nombre,
                                'cliente_apellidos' => $valuedetalle->cliente_apellidos,
                                'cliente_numerotelefono' => $valuedetalle->cliente_numerotelefono,
                                'cliente_direccion' => $valuedetalle->cliente_direccion,
                                'ubigeonombre' => $valuedetalle->ubigeonombre,
                                'tipo' => 'AVAL',
                                'estado' => $cantidad_creditos>0?'ACTIVO':'INACTIVO',
                            ];*/
                        }
                    }elseif($request->idtipo==1){
                        if($cantidad_cliente>0){
                            $count = 1;
                            /*$detalleprestamocartera_tabla[] = [
                                'idcliente' => $valuedetalle->idcliente,
                                'cliente_identificacion' => $valuedetalle->cliente_identificacion,
                                'cliente_nombre' => $valuedetalle->cliente_nombre,
                                'cliente_apellidos' => $valuedetalle->cliente_apellidos,
                                'cliente_numerotelefono' => $valuedetalle->cliente_numerotelefono,
                                'cliente_direccion' => $valuedetalle->cliente_direccion,
                                'ubigeonombre' => $valuedetalle->ubigeonombre,
                                'tipo' => 'CLIENTE',
                                'estado' => $cantidad_creditos>0?'ACTIVO':'INACTIVO',
                            ];*/
                        }
                    }else{
                        $count = 1;
                    }
                  
                    $countest = 0;
                    if($request->idestado==1){
                        if($cantidad_creditos>0){
                            $countest = 1;
                        }
                    }
                    elseif($request->idestado==2){
                        if($cantidad_creditos==0){
                            $countest = 1;
                        }
                    }else{
                        $countest = 1;
                    }
                  
                    if($count==1 && $countest==1){
                        $tipo = '';
                        if($cantidad_garante>0 && $cantidad_cliente>0){
                            $tipo = 'CLIENTE/AVAL';
                        }
                        elseif($cantidad_garante>0){
                            $tipo = 'AVAL';
                        }
                        elseif($cantidad_cliente>0){
                            $tipo = 'CLIENTE';
                        }
                        $detalleprestamocartera_tabla[] = [
                            'idcliente' => $valuedetalle->idcliente,
                            'cliente_identificacion' => $valuedetalle->cliente_identificacion,
                            'cliente_nombre' => $valuedetalle->cliente_nombre,
                            'cliente_apellidos' => $valuedetalle->cliente_apellidos,
                            'cliente_numerotelefono' => $valuedetalle->cliente_numerotelefono,
                            'cliente_direccion' => $valuedetalle->cliente_direccion,
                            'ubigeonombre' => $valuedetalle->ubigeonombre,
                            'tipo' => $tipo,
                            'estado' => $cantidad_creditos>0?'ACTIVO':'INACTIVO',
                        ];
                    }
                    
                }
                $prestamocartera_tabla[] = [
                    'asesor' => $value->asesor,
                    'detalle' => $detalleprestamocartera_tabla,
                ];
            }
       
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/reporte/reporteprestamocarteracliente/tablapdf',[
                'tienda' => $tienda,
                'prestamocateras' => $prestamocartera_tabla,
                'request' => $request
            ]);
            return $pdf->stream('REPORTE_DE_CARTERA_DE_CLIENTE.pdf');
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
