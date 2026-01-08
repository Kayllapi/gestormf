<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\Exports\ReportecompraExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class  ReportecompraController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        $where = [];
        if($request->input('idproveedor')!=''){
            $where[] = ['users.id',$request->input('idproveedor')];
        }
        if($request->input('idcomprobante')!=''){
            $where[] = ['s_tipocomprobante.id',$request->input('idcomprobante')];
        }
        if($request->input('seriecorrelativo')!=''){
            $where[] = ['s_compra.seriecorrelativo','LIKE','%'.$request->input('seriecorrelativo').'%'];
        }
        if($request->input('idestado')!=''){
            $where[] = ['s_compra.s_idestado',$request->input('idestado')];
        }
        if($request->input('fechainicio')!=''){
            $where[] = ['s_compra.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
        }
        if($request->input('fechafin')!=''){
            $where[] = ['s_compra.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
        }
      
       if($request->input('tipo')=='excel'){
             $s_compra = DB::table('s_compra')
                  ->join('users','users.id','s_compra.s_idusuarioproveedor')
                  ->join('users as responsable','responsable.id','s_compra.s_idusuarioresponsable')
                  ->join('s_tipocomprobante','s_tipocomprobante.id','s_compra.s_idcomprobante')
                  ->where('s_compra.idtienda',$idtienda)
                  ->where($where)
                  ->select(
                      's_compra.*',
                      'users.nombre as nombreProveedor',
                      'users.apellidos as apellidoProveedor',
                      's_tipocomprobante.nombre as nombreComprobante',
                      'responsable.nombre as responsablenombre'
                  )
                  ->orderBy('s_compra.id','desc')
                  ->get();
          
            $inicio = $request->input('fechainicio');
            $fin    = $request->input('fechafin');
            $titulo = 'Reporte de Compras';
            $fecha  = '';

            if($inicio != '' && $fin != ''){
              $fecha = '('.$inicio.' hasta '.$fin.')';
            }
            elseif($inicio != ''){                
              $fecha = '('.$inicio.')';
            }
            elseif($fin != ''){
              $fecha = '('.$fin.')';
            }
            else{
              $fecha = '';
            }
            return Excel::download(new ReportecompraExport($s_compra, $inicio, $fin, $titulo), $titulo.' '.$fecha.'.xls');
        }else{
          $s_compra = DB::table('s_compra')
              ->join('users','users.id','s_compra.s_idusuarioproveedor')
              ->join('users as responsable','responsable.id','s_compra.s_idusuarioresponsable')
              ->join('s_tipocomprobante','s_tipocomprobante.id','s_compra.s_idcomprobante')
              ->where('s_compra.idtienda',$idtienda)
              ->where($where)
              ->select(
                  's_compra.*',
                  'users.nombre as nombreProveedor',
                  'users.apellidos as apellidoProveedor',
                  's_tipocomprobante.nombre as nombreComprobante',
                  'responsable.nombre as responsablenombre'
              )
              ->orderBy('s_compra.id','desc')
              ->paginate(10);

          $comprobante = DB::table('s_tipocomprobante')->get();
          $tipopersonas = DB::table('tipopersona')->get();
      
          return view('layouts/backoffice/tienda/sistema/reporte/reportecompra/index',[
              'tienda' => $tienda,
              's_compra' => $s_compra,
              'comprobante' => $comprobante,
              'tipopersonas' => $tipopersonas
          ]);
       }
    }

    public function create(Request $request,$idtienda)
    {
        //
    }

    public function store(Request $request, $idtienda)
    {
        //
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
  
        if ($id == 'showtablapdf') {
        
            $where = [];
          
            if($request->input('fechainicio')!=''){
                $where[] = ['s_compra.fechaconfirmacion','>=',$request->input('fechainicio').' 00:00:00'];
            }
            if($request->input('fechafin')!=''){
                $where[] = ['s_compra.fechaconfirmacion','<=',$request->input('fechafin').' 23:59:59'];
            }
            
          
            if ($request->listarpor == 1) {
              /*$compras = DB::table('s_compra')
                  ->join('users','users.id','s_compra.s_idusuarioproveedor')
                  ->join('users as responsable','responsable.id','s_compra.s_idusuarioresponsable')
                  ->join('s_tipocomprobante','s_tipocomprobante.id','s_compra.s_idcomprobante')
                  ->where('s_compra.idtienda',$idtienda)
                  ->where($where)
                  ->select(
                      's_compra.*',
                      'users.nombre as nombreProveedor',
                      'users.apellidos as apellidoProveedor',
                      's_tipocomprobante.nombre as nombreComprobante',
                      'responsable.nombre as responsablenombre'
                  )
                  ->orderBy('s_compra.id','desc')
                  ->get();
          
                $total = 0;
                $compras_tabla = [];
                foreach($compras as $value){
                    $fila_total = 0;
                    if ($value->totalredondeado==0) {
                      $montototal = DB::table('s_compradetalle')->where('s_idcompra',$value->id)->sum('preciototal');
                      $fila_total = number_format($montototal, 2, '.', '');
                    }else {
                      $fila_total = $value->totalredondeado;
                    }
                  
                    $compras_tabla[] = [
                        'codigo'            => str_pad($value->codigo, 8, "0", STR_PAD_LEFT),
                        'comprobante'       => $value->nombreComprobante,
                        'serie_correlativo' => $value->seriecorrelativo,
                        'proveedor'         => $value->apellidoProveedor,
                        'fecharegistro'     => date_format(date_create($value->fecharegistro), 'd/m/Y h:i A'),
                        'responsable'       => $value->responsablenombre,
                        'total_detalle'     => $fila_total,
                    ];
                    $total = $total+$value->totalredondeado;
                }*/
            }
            elseif ($request->listarpor == 2) {
                if( $request->input('idproveedor')!='' ){
                    $where[] = ['s_compra.s_idusuarioproveedor',$request->input('idproveedor')];
                }
              
                $compras = DB::table('s_compra')
                    ->join('users as proveedor','proveedor.id','s_compra.s_idusuarioproveedor')
                    ->where('s_compra.idtienda',$idtienda)
                    ->where('s_compra.s_idestado',2)
                    ->where($where)
                    ->select(
                         'proveedor.id as idproveedor',
                         'proveedor.identificacion as proveedor_identificacion',
                         DB::raw('CONCAT(proveedor.apellidos, ", ", proveedor.nombre) as proveedor')
                    )
                    ->orderBy('proveedor.apellidos','asc')
                    ->distinct()
                    ->get();
                
                $total = 0;
                $compras_tabla = [];
                foreach($compras as $value){
                    $detallecompras = DB::table('s_compra')
                      ->join('users','users.id','s_compra.s_idusuarioproveedor')
                      ->join('users as responsable','responsable.id','s_compra.s_idusuarioresponsable')
                      ->join('s_tipocomprobante','s_tipocomprobante.id','s_compra.s_idcomprobante')
                      ->where('s_compra.idtienda',$idtienda)
                      ->where('s_compra.s_idestado',2)
                      ->where('users.id', $value->idproveedor)
                      ->where($where)
                      ->select(
                          's_compra.*',
                          'users.nombre as nombreProveedor',
                          'users.apellidos as apellidoProveedor',
                          's_tipocomprobante.nombre as nombreComprobante',
                          'responsable.nombre as responsablenombre'
                      )
                      ->orderBy('s_compra.id','desc')
                      ->get();
           
                     $detallecompras_tabla = [];
                  
                     $total_detalle = 0;
                     foreach($detallecompras as $valuedetalle){
                      
                        $s_compradetalle  =   DB::table('s_compradetalle')
                            ->join('s_producto as producto','producto.id','s_compradetalle.s_idproducto')
                            ->join('unidadmedida','unidadmedida.id','producto.idunidadmedida')
                            ->where('s_compradetalle.idtienda',$idtienda)
                            ->where('s_compradetalle.s_idcompra',$valuedetalle->id)
                            ->select(
                                's_compradetalle.*',
                                'producto.nombre as nombreproducto',
                                'producto.codigo as codigo',
                                'unidadmedida.nombre as nombreunidadmedida'
                            )
                            ->orderBy('s_compradetalle.id','asc')
                            ->get();
                      
                        $productodetallecompras_tabla = [];
                        $numero = 1;
                        foreach($s_compradetalle as $valuedetalleproducto){
                            $productodetallecompras_tabla[] = [
                                'numero' => $numero,
                                'producto' => ($valuedetalleproducto->codigo!=''?$valuedetalleproducto->codigo.' - ':'').$valuedetalleproducto->nombreproducto.' '.$valuedetalleproducto->nombreunidadmedida,
                                'precio' => $valuedetalleproducto->preciounitario,
                                'cantidad' => $valuedetalleproducto->cantidad,
                                'total' => $valuedetalleproducto->preciototal,
                            ];
                            $numero ++;
                        }
                       
                        $detallecompras_tabla[] = [
                            'codigo'            => str_pad($valuedetalle->codigo, 8, "0", STR_PAD_LEFT),
                            'comprobante'       => $valuedetalle->nombreComprobante,
                            'serie_correlativo' => $valuedetalle->seriecorrelativo,
                            'proveedor'         => $valuedetalle->apellidoProveedor,
                            'fechaconfirmacion' => date_format(date_create($valuedetalle->fechaconfirmacion), 'd/m/Y h:i A'),
                            'responsable'       => $valuedetalle->responsablenombre,
                            'total'             => $valuedetalle->total,
                            'detalleproducto'   => $productodetallecompras_tabla
                        ];

                        $total_detalle += $valuedetalle->totalredondeado;
                     }
                    

                      if(count($detallecompras)>0){
                          $compras_tabla[] = [
                              'proveedor_identificacion' => $value->proveedor_identificacion,
                              'proveedor'                => $value->proveedor,
                              'detalle'                  => $detallecompras_tabla,
                              'total'                    => $total_detalle,
                          ];
                          $total += $total_detalle;
                      }
                  
                }
            }
            elseif ($request->listarpor == 3) {
              
                if( $request->input('idresponsable')!='' ){
                    $where[] = ['responsable.id',$request->input('idresponsable')];
                }
              
                $compras = DB::table('s_compra')
                    ->join('users as responsable','responsable.id','s_compra.s_idusuarioresponsable')
                    ->where('s_compra.idtienda',$idtienda)
                    ->where('s_compra.s_idestado',2)
                    ->where($where)
                    ->select(
                         'responsable.id as idresponsable',
                         'responsable.identificacion as responsable_identificacion',
                         DB::raw('CONCAT(responsable.apellidos, ", ", responsable.nombre) as responsable')
                    )
                    ->orderBy('responsable.apellidos','asc')
                    ->distinct()
                    ->get();
              
                $total = 0;
                $compras_tabla = [];
                foreach($compras as $value){
                  
                    $detallecompras = DB::table('s_compra')
                      ->join('users','users.id','s_compra.s_idusuarioproveedor')
                      ->join('users as responsable','responsable.id','s_compra.s_idusuarioresponsable')
                      ->join('s_tipocomprobante','s_tipocomprobante.id','s_compra.s_idcomprobante')
                      ->where('s_compra.idtienda',$idtienda)
                      ->where('s_compra.s_idestado',2)
                      ->where('responsable.id', $value->idresponsable)
                      ->where($where)
                      ->select(
                          's_compra.*',
                          'users.nombre as nombreProveedor',
                          'users.apellidos as apellidoProveedor',
                          's_tipocomprobante.nombre as nombreComprobante',
                          'responsable.nombre as responsablenombre'
                      )
                      ->orderBy('s_compra.id','desc')
                      ->get();
                  
                     $detallecompras_tabla = [];
                  
                     $total_detalle = 0;
                     foreach($detallecompras as $valuedetalle){
                      
                        $s_compradetalle  =   DB::table('s_compradetalle')
                            ->join('s_producto as producto','producto.id','s_compradetalle.s_idproducto')
                            ->join('unidadmedida','unidadmedida.id','producto.idunidadmedida')
                            ->where('s_compradetalle.idtienda',$idtienda)
                            ->where('s_compradetalle.s_idcompra',$valuedetalle->id)
                            ->select(
                                's_compradetalle.*',
                                'producto.nombre as nombreproducto',
                                'producto.codigo as codigo',
                                'unidadmedida.nombre as nombreunidadmedida'
                            )
                            ->orderBy('s_compradetalle.id','asc')
                            ->get();
                      
                        $productodetallecompras_tabla = [];
                        $numero = 1;
                        foreach($s_compradetalle as $valuedetalleproducto){
                            $productodetallecompras_tabla[] = [
                                'numero' => $numero,
                                'producto' => ($valuedetalleproducto->codigo!=''?$valuedetalleproducto->codigo.' - ':'').$valuedetalleproducto->nombreproducto.' '.$valuedetalleproducto->nombreunidadmedida,
                                'precio' => $valuedetalleproducto->preciounitario,
                                'cantidad' => $valuedetalleproducto->cantidad,
                                'total' => $valuedetalleproducto->preciototal,
                            ];
                            $numero ++;
                        }
                       
                        $detallecompras_tabla[] = [
                            'codigo'            => str_pad($valuedetalle->codigo, 8, "0", STR_PAD_LEFT),
                            'comprobante'       => $valuedetalle->nombreComprobante,
                            'serie_correlativo' => $valuedetalle->seriecorrelativo,
                            'proveedor'         => $valuedetalle->apellidoProveedor,
                            'fechaconfirmacion' => date_format(date_create($valuedetalle->fechaconfirmacion), 'd/m/Y h:i A'),
                            'total'             => $valuedetalle->total,
                            'detalleproducto'   => $productodetallecompras_tabla
                        ];

                        $total_detalle += $valuedetalle->totalredondeado;
                     }
                    

                      if(count($detallecompras)>0){
                          $compras_tabla[] = [
                              'responsable_identificacion' => $value->responsable_identificacion,
                              'responsable'                => $value->responsable,
                              'detalle'                  => $detallecompras_tabla,
                              'total'                    => $total_detalle,
                          ];
                          $total += $total_detalle;
                      }
                  
                }
            }
          
             $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/reporte/reportecompra/tablapdf',[
                'tienda'      => $tienda,
                'compras'     => $compras_tabla,
                'total'       => number_format($total, 2, '.', ''),
                'listarpor'   => $request->input('listarpor'),
                'fechainicio' => $request->input('fechainicio'),
                'fechafin'    => $request->input('fechafin'),
            ]);
          
            return $pdf->stream('REPORTE_DE_VENTA.pdf');
        }
    }

    public function edit(Request $request, $idtienda, $idmarca)
    {
        //
    }

    public function update(Request $request, $idtienda, $idmarca)
    {
        //
    }

    public function destroy(Request $request, $idtienda, $idmarca)
    {
        //
    }
}
