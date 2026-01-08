<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\Exports\ReporteventaExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class  ReporteventaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
      
        if($request->input('view') == 'tabla'){
            return view('layouts/backoffice/nuevosistema/reporte/reporteventa/tabla',[
                'tienda' => $tienda,
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
        //dd($request->input());
        //$request->user()->authorizeRoles($request->path(),$idtienda);
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
                $where[] = ['s_venta.fechaconfirmacion','>=',$request->input('fechainicio').' 00:00:00'];
            }
            if($request->input('fechafin')!=''){
                $where[] = ['s_venta.fechaconfirmacion','<=',$request->input('fechafin').' 23:59:59'];
            }
            
            if($request->input('listarpor')==1) {
                /*$ventas = DB::table('s_venta')
                  ->join('s_tipocomprobante','s_tipocomprobante.id','s_venta.s_idcomprobante')
                  ->join('s_tipoentrega','s_tipoentrega.id','s_venta.s_idtipoentrega')
                  ->join('users as responsable','responsable.id','s_venta.s_idusuarioresponsable')
                  ->join('users as responsableregistro','responsableregistro.id','s_venta.s_idusuarioresponsableregistro')
                  ->join('users as cliente','cliente.id','s_venta.s_idusuariocliente')
                  ->where('s_venta.idtienda',$idtienda)
                  ->where($where)
                  ->select(
                      's_venta.*',
                      's_tipocomprobante.nombre as nombreComprobante',
                      's_tipoentrega.nombre as tipoentreganombre',
                      'responsable.nombre as responsablenombre',
                      'responsableregistro.nombre as responsableregistronombre',
                      DB::raw('CONCAT(cliente.nombre) as cliente'),
                  )
                  ->orderBy('s_venta.id','desc')
                  ->get();
                
                $total = 0;
                $ventas_tabla = [];
                foreach($ventas as $value){
                    $ventas_tabla[] = [
                        'codigo'           => str_pad($value->codigo, 8, "0", STR_PAD_LEFT),
                        'comprobante'      => $value->nombreComprobante,
                        'cliente'          => $value->cliente,
                        'vendedor'         => $value->responsableregistronombre,
                        'cajero'           => $value->responsablenombre,
                        'fechaconfirmacion'=> ($value->s_idestado==3 or $value->s_idestado==4) ? date_format(date_create($value->fechaconfirmacion),"d/m/Y h:i A") : '---',
                        'total'            => $value->totalredondeado==0 ? number_format($value->montoredondeado, 2, '.', '') : $value->totalredondeado,
                    ];
                    $total = $total+$value->totalredondeado;
                }*/
              
            }
            elseif($request->input('listarpor')==2) {
              if($request->input('idcliente')!=''){
                  $where[] = ['cliente.id','LIKE','%'.$request->input('idcliente').'%'];
              }
              
              $ventas = DB::table('s_venta')
                    ->join('users as cliente','cliente.id','s_venta.s_idusuariocliente')
                    ->where('s_venta.idtienda',$idtienda)
                    ->where('s_venta.s_idestado',3)
                    ->where($where)
                    ->select(
                        'cliente.id as idcliente',
                        'cliente.identificacion as cliente_identificacion',
                        DB::raw('CONCAT(cliente.nombrecompleto) as cliente'),
                    )
                    ->orderBy('cliente.nombrecompleto','asc')
                    ->distinct()
                    ->get();
                
                $total = 0;
                $ventas_tabla = [];
                foreach($ventas as $value){
                   $detalleventas = DB::table('s_venta')
                      ->join('s_tipocomprobante','s_tipocomprobante.id','s_venta.s_idcomprobante')
                      ->join('s_tipoentrega','s_tipoentrega.id','s_venta.s_idtipoentrega')
                      ->join('users as responsable','responsable.id','s_venta.s_idusuarioresponsable')
                      ->join('users as responsableregistro','responsableregistro.id','s_venta.s_idusuarioresponsableregistro')
                      ->join('users as cliente','cliente.id','s_venta.s_idusuariocliente')
                      ->where('s_venta.idtienda',$idtienda)
                      ->where('s_venta.s_idestado',3)
                      ->where('cliente.id', $value->idcliente)
                      ->where($where)
                      ->select(
                          's_venta.*',
                          's_tipocomprobante.nombre as nombreComprobante',
                          's_tipoentrega.nombre as tipoentreganombre',
                          'responsable.nombre as responsablenombre',
                           'responsableregistro.nombre as responsableregistronombre',
                          'cliente.nombre as clientenombre',
                           DB::raw('CONCAT(cliente.nombre) as cliente'),
                      )
                      ->orderBy('s_venta.fecharegistro','asc')
                      ->get();
                  
                    $detalleventas_tabla = [];
                  
                    $total = 0;
                    foreach($detalleventas as $valuedetalle){
                    
                        $s_ventadetalle  =   DB::table('s_ventadetalle')
                            ->join('s_producto as producto','producto.id','s_ventadetalle.s_idproducto')
                            ->join('unidadmedida','unidadmedida.id','producto.s_idunidadmedida')
                            ->where('s_ventadetalle.idtienda',$idtienda)
                            ->where('s_ventadetalle.s_idventa',$valuedetalle->id)
                            ->select(
                                's_ventadetalle.*',
                                'producto.nombre as nombreproducto',
                                'producto.codigo as codigo',
                                'unidadmedida.nombre as nombreunidadmedida'
                            )
                            ->orderBy('s_ventadetalle.id','asc')
                            ->get();
                                
                            
                        $productodetalleventas_tabla = [];
                        if ($request->input('tipo_reporte') == 'venta_detallada') {
                            $numero = 1;
                            foreach($s_ventadetalle as $valuedetalleproducto){
                                $productodetalleventas_tabla[] = [
                                    'numero' => $numero,
                                    'producto' => ($valuedetalleproducto->codigo!=''?$valuedetalleproducto->codigo.' - ':'').$valuedetalleproducto->nombreproducto.' '.$valuedetalleproducto->nombreunidadmedida,
                                    'precio' => $valuedetalleproducto->preciounitario,
                                    'cantidad' => $valuedetalleproducto->cantidad,
                                    'total' => $valuedetalleproducto->total,
                                ];
                                $numero ++;
                            }
                        }

                        $detalleventas_tabla[] = [
                            'codigo'           => str_pad($valuedetalle->codigo, 8, "0", STR_PAD_LEFT),
                            'comprobante'      => $valuedetalle->nombreComprobante,
                            'cliente'          => $valuedetalle->cliente,
                            'vendedor'         => $valuedetalle->responsableregistronombre,
                            'cajero'           => $valuedetalle->responsablenombre,
                            'fechaconfirmacion'=> ($valuedetalle->s_idestado==3 or $valuedetalle->s_idestado==4) ? date_format(date_create($valuedetalle->fechaconfirmacion),"d/m/Y h:i A") : '---',
                            'total'            => $valuedetalle->totalredondeado==0 ? number_format($valuedetalle->montoredondeado, 2, '.', '') : $valuedetalle->totalredondeado,
                            'detalleproducto'  => $productodetalleventas_tabla
                        ];
                    
                        $total += $valuedetalle->totalredondeado;
                    }

                    if(count($detalleventas)>0){
                        $ventas_tabla[] = [
                            'cliente_identificacion' => $value->cliente_identificacion,
                            'cliente'                => $value->cliente,
                            'detalle'                => $detalleventas_tabla
                        ];
                        $total += $total;
                    }
                }
              
            }
            elseif($request->input('listarpor')==3) {
              if($request->input('idvendedor')!=''){
                  $where[] = ['responsableregistro.id',$request->input('idvendedor')];
              }
              
              $ventas = DB::table('s_venta')
                    ->join('users as responsableregistro','responsableregistro.id','s_venta.s_idusuarioresponsableregistro')
                    ->where('s_venta.idtienda',$idtienda)
                    ->where('s_venta.s_idestado',3)
                    ->where($where)
                    ->select(
                        'responsableregistro.id as idvendedor',
                        'responsableregistro.identificacion as vendedor_identificacion',
                        DB::raw('CONCAT(responsableregistro.nombrecompleto) as vendedor'),
                    )
                    ->orderBy('responsableregistro.nombrecompleto','asc')
                    ->distinct()
                    ->get();
                
                $total = 0;
                $ventas_tabla = [];
                foreach($ventas as $value){
                   $detalleventas = DB::table('s_venta')
                      ->join('s_tipocomprobante','s_tipocomprobante.id','s_venta.s_idcomprobante')
                      ->join('s_tipoentrega','s_tipoentrega.id','s_venta.s_idtipoentrega')
                      ->join('users as responsable','responsable.id','s_venta.s_idusuarioresponsable')
                      ->join('users as responsableregistro','responsableregistro.id','s_venta.s_idusuarioresponsableregistro')
                      ->join('users as cliente','cliente.id','s_venta.s_idusuariocliente')
                      ->where('s_venta.idtienda',$idtienda)
                      ->where('s_venta.s_idestado',3)
                      ->where('responsableregistro.id', $value->idvendedor)
                      ->where($where)
                      ->select(
                          's_venta.*',
                          's_tipocomprobante.nombre as nombreComprobante',
                          's_tipoentrega.nombre as tipoentreganombre',
                          'responsable.nombre as responsablenombre',
                          'responsableregistro.nombre as responsableregistronombre',
                          'cliente.nombre as clientenombre',
                           DB::raw('CONCAT(cliente.nombrecompleto) as cliente'),
                      )
                      ->orderBy('s_venta.fecharegistro','asc')
                      ->get();
                  
                    $detalleventas_tabla = [];
                  
                    $total_detalle = 0;
                    foreach($detalleventas as $valuedetalle){
                        
                        $s_ventadetalle  =   DB::table('s_ventadetalle')
                        ->join('s_producto as producto','producto.id','s_ventadetalle.s_idproducto')
                        ->join('unidadmedida','unidadmedida.id','producto.s_idunidadmedida')
                        ->where('s_ventadetalle.idtienda',$idtienda)
                        ->where('s_ventadetalle.s_idventa',$valuedetalle->id)
                        ->select(
                            's_ventadetalle.*',
                            'producto.nombre as nombreproducto',
                            'producto.codigo as codigo',
                            'unidadmedida.nombre as nombreunidadmedida'
                            )
                            ->orderBy('s_ventadetalle.id','asc')
                            ->get();
                            
                            $productodetalleventas_tabla = [];
                            if ($request->input('tipo_reporte') == 'venta_detallada') {
                                $numero = 1;
                                foreach($s_ventadetalle as $valuedetalleproducto){
                                    $productodetalleventas_tabla[] = [
                                        'numero' => $numero,
                                        'producto' => ($valuedetalleproducto->codigo!=''?$valuedetalleproducto->codigo.' - ':'').$valuedetalleproducto->nombreproducto.' '.$valuedetalleproducto->nombreunidadmedida,
                                        'precio' => $valuedetalleproducto->preciounitario,
                                        'cantidad' => $valuedetalleproducto->cantidad,
                                        'total' => $valuedetalleproducto->total,
                                    ];
                                    $numero ++;
                                }
                            }
                        
                        $detalleventas_tabla[] = [
                            'codigo'           => str_pad($valuedetalle->codigo, 8, "0", STR_PAD_LEFT),
                            'comprobante'      => $valuedetalle->nombreComprobante,
                            'cliente'          => $valuedetalle->cliente,
                            'cajero'           => $valuedetalle->responsablenombre,
                            'fechaconfirmacion'=> ($valuedetalle->s_idestado==3 or $valuedetalle->s_idestado==4) ? date_format(date_create($valuedetalle->fechaconfirmacion),"d/m/Y h:i A") : '---',
                            'total'            => $valuedetalle->totalredondeado==0 ? number_format($valuedetalle->montoredondeado, 2, '.', '') : $valuedetalle->totalredondeado,
                            'detalleproducto'  => $productodetalleventas_tabla
                        ];
                    
                        $total_detalle += $valuedetalle->totalredondeado;
                    }

                    if(count($detalleventas)>0){
                        $ventas_tabla[] = [
                            'vendedor_identificacion' => $value->vendedor_identificacion,
                            'vendedor'                => $value->vendedor,
                            'detalle'                => $detalleventas_tabla,
                            'total'                  => $total_detalle,
                        ];
                        $total += $total_detalle;
                    }
                }
            }
            elseif($request->input('listarpor')==4) {
               if($request->input('icajero')!=''){
                  $where[] = ['cajero.id','LIKE','%'.$request->input('icajero').'%'];
               }
              
               $ventas = DB::table('s_venta')
                    ->join('users as cajero','cajero.id','s_venta.s_idusuarioresponsable') 
                    ->where('s_venta.idtienda',$idtienda)
                    ->where('s_venta.s_idestado',3)
                    ->where($where)
                    ->select(
                        'cajero.id as idcajero',
                        'cajero.identificacion as cajero_identificacion',
                        DB::raw('CONCAT(cajero.nombrecompleto) as cajero'),
                    )
                    ->orderBy('cajero.nombrecompleto','asc')
                    ->distinct()
                    ->get();
                
                $total = 0;
                $ventas_tabla = [];

                foreach($ventas as $value){
                   $detalleventas = DB::table('s_venta')
                      ->join('s_tipocomprobante','s_tipocomprobante.id','s_venta.s_idcomprobante')
                      ->join('s_tipoentrega','s_tipoentrega.id','s_venta.s_idtipoentrega')
                      ->join('users as responsable','responsable.id','s_venta.s_idusuarioresponsable')
                      ->join('users as responsableregistro','responsableregistro.id','s_venta.s_idusuarioresponsableregistro')
                      ->join('users as cliente','cliente.id','s_venta.s_idusuariocliente')
                      ->where('s_venta.idtienda',$idtienda)
                      ->where('s_venta.s_idestado',3)
                      ->where('responsable.id', $value->idcajero)
                      ->where($where)
                      ->select(
                          's_venta.*',
                          's_tipocomprobante.nombre as nombreComprobante',
                          's_tipoentrega.nombre as tipoentreganombre',
                          'responsable.nombre as responsablenombre',
                          'responsableregistro.nombre as responsableregistronombre',
                          'cliente.nombre as clientenombre',
                           DB::raw('CONCAT(cliente.nombre) as cliente'),
                      )
                      ->orderBy('s_venta.fecharegistro','asc')
                      ->get();
                  
                    $detalleventas_tabla = [];
                  
                    
                    $total_detalle = 0;
                    foreach($detalleventas as $valuedetalle){
                        
                        $s_ventadetalle  =   DB::table('s_ventadetalle')
                                ->join('s_producto as producto','producto.id','s_ventadetalle.s_idproducto')
                                ->join('unidadmedida','unidadmedida.id','producto.s_idunidadmedida')
                                ->where('s_ventadetalle.idtienda',$idtienda)
                                ->where('s_ventadetalle.s_idventa',$valuedetalle->id)
                                ->select(
                                    's_ventadetalle.*',
                                    'producto.nombre as nombreproducto',
                                    'producto.codigo as codigo',
                                    'unidadmedida.nombre as nombreunidadmedida'
                                    )
                                    ->orderBy('s_ventadetalle.id','asc')
                                    ->get();
                                    
                            $productodetalleventas_tabla = [];
                            if ($request->input('tipo_reporte') == 'venta_detallada') {      
                                $numero = 1;
                                foreach($s_ventadetalle as $valuedetalleproducto){
                                    $productodetalleventas_tabla[] = [
                                        'numero' => $numero,
                                        'producto' => ($valuedetalleproducto->codigo!=''?$valuedetalleproducto->codigo.' - ':'').$valuedetalleproducto->nombreproducto.' '.$valuedetalleproducto->nombreunidadmedida,
                                        'precio' => $valuedetalleproducto->preciounitario,
                                        'cantidad' => $valuedetalleproducto->cantidad,
                                        'total' => $valuedetalleproducto->total,
                                    ];
                                    $numero ++;
                                }
                            }
                        
                        $detalleventas_tabla[] = [
                            'codigo'           => str_pad($valuedetalle->codigo, 8, "0", STR_PAD_LEFT),
                            'comprobante'      => $valuedetalle->nombreComprobante,
                            'cliente'          => $valuedetalle->cliente,
                            'vendedor'         => $valuedetalle->responsableregistronombre,
                            'fechaconfirmacion'=> ($valuedetalle->s_idestado==3 or $valuedetalle->s_idestado==4) ? date_format(date_create($valuedetalle->fechaconfirmacion),"d/m/Y h:i A") : '---',
                            'total'            => $valuedetalle->totalredondeado==0 ? number_format($valuedetalle->montoredondeado, 2, '.', '') : $valuedetalle->totalredondeado,
                            'detalleproducto'  => $productodetalleventas_tabla
                        ];
                    
                        $total_detalle += $valuedetalle->totalredondeado;
                    }

                    if(count($detalleventas)>0){
                        $ventas_tabla[] = [
                            'cajero_identificacion' => $value->cajero_identificacion,
                            'cajero'                => $value->cajero,
                            'detalle'                => $detalleventas_tabla,
                            'total'                  => $total_detalle,
                        ];
                        $total += $total_detalle;
                    }
                }
            }

            if ($request->input('tipo_reporte') == 'venta_detallada') {
                $pdf = PDF::loadView('layouts/backoffice/nuevosistema/reporte/reporteventa/tablapdf',[
                    'tienda' => $tienda,
                    'ventas' => $ventas_tabla,
                    'total' => number_format($total, 2, '.', ''),
                    'listarpor' => $request->input('listarpor'),
                    'fechainicio' => $request->input('fechainicio'),
                    'fechafin' => $request->input('fechafin'),
                ]);
            } else {
                $pdf = PDF::loadView('layouts/backoffice/nuevosistema/reporte/reporteventa/tablapdf_resumen',[
                    'tienda' => $tienda,
                    'ventas' => $ventas_tabla,
                    'total' => number_format($total, 2, '.', ''),
                    'listarpor' => $request->input('listarpor'),
                    'fechainicio' => $request->input('fechainicio'),
                    'fechafin' => $request->input('fechafin'),
                ]);
            }
            
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
