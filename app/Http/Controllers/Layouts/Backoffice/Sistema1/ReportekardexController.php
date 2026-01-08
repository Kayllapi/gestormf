<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use App\Exports\ReportekardexExport;
use Maatwebsite\Excel\Facades\Excel;

class  ReportekardexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        /*$s_venta = DB::table('s_ventadetalle')
            ->join('s_venta','s_venta.id','s_ventadetalle.s_idventa')
            ->select(
                DB::raw('CONCAT("VENTA") as concepto'),
                's_ventadetalle.id as idreferencia',
                's_ventadetalle.s_idproducto as idproducto',
                's_ventadetalle.cantidad as cantidad',
                's_ventadetalle.por as por',
                's_ventadetalle.idunidadmedida as idunidadmedida',
                's_ventadetalle.idtienda as idtienda',
                's_venta.fechaconfirmacion as fechaconfirmacion',
            );
      
        $s_compra = DB::table('s_compradetalle')
            ->join('s_compra','s_compra.id','s_compradetalle.s_idcompra')
            ->select(
                DB::raw('CONCAT("COMPRA") as concepto'),
                's_compradetalle.id as idreferencia',
                's_compradetalle.s_idproducto as idproducto',
                's_compradetalle.cantidad as cantidad',
                's_compradetalle.por as por',
                's_compradetalle.idunidadmedida as idunidadmedida',
                's_compradetalle.idtienda as idtienda',
                's_compra.fechaconfirmacion as fechaconfirmacion',
            );
      
        $s_movimiento_ingreso = DB::table('s_productomovimiento')
            ->where('s_productomovimiento.s_idtipomovimiento',1)
            ->select(
                DB::raw('CONCAT("MOVIMIENTO INGRESO") as concepto'),
                's_productomovimiento.id as idreferencia',
                's_productomovimiento.s_idproducto as idproducto',
                's_productomovimiento.cantidad as cantidad',
                's_productomovimiento.por as por',
                's_productomovimiento.idunidadmedida as idunidadmedida',
                's_productomovimiento.idtienda as idtienda',
                's_productomovimiento.fechaconfirmacion as fechaconfirmacion',
            );
      
        $s_movimiento_salida = DB::table('s_productomovimiento')
            ->where('s_productomovimiento.s_idtipomovimiento',2)
            ->select(
                DB::raw('CONCAT("MOVIMIENTO SALIDA") as concepto'),
                's_productomovimiento.id as idreferencia',
                's_productomovimiento.s_idproducto as idproducto',
                's_productomovimiento.cantidad as cantidad',
                's_productomovimiento.por as por',
                's_productomovimiento.idunidadmedida as idunidadmedida',
                's_productomovimiento.idtienda as idtienda',
                's_productomovimiento.fechaconfirmacion as fechaconfirmacion',
            );
      
        
        $s_ventadevolucion = DB::table('s_ventadevoluciondetalle')
            ->join('s_ventadevolucion','s_ventadevolucion.id','s_ventadevoluciondetalle.idventadevolucion')
            ->select(
                DB::raw('CONCAT("DEVOLUCION VENTA") as concepto'),
                's_ventadevoluciondetalle.id as idreferencia',
                's_ventadevoluciondetalle.idproducto as idproducto',
                's_ventadevoluciondetalle.cantidad as cantidad',
                's_ventadevoluciondetalle.por as por',
                's_ventadevoluciondetalle.idunidadmedida as idunidadmedida',
                's_ventadevoluciondetalle.idtienda as idtienda',
                's_ventadevolucion.fechaconfirmacion as fechaconfirmacion',
            );

        $data = $s_compra
              ->union($s_venta)
              ->union($s_movimiento_ingreso)
              ->union($s_movimiento_salida)
              ->union($s_ventadevolucion)
              ->orderBy('fechaconfirmacion','asc')
              ->offset(70000)
              ->limit(10000)
              ->get();
      
        foreach($data as $value){
            productosaldo_actualizar(
                $value->idtienda,
                $value->idproducto,
                $value->concepto,
                $value->cantidad,
                $value->por,
                $value->idunidadmedida,
                $value->idreferencia
            );
        }*/

            /*productosaldo_actualizar(
                154,
                8327,
                'MOVIMIENTO INGRESO',
                2,
                0
            );*/
      
        /*dd(count($s_compraventa));*/
      
        /*$s_producto  = DB::table('s_producto')
            ->orderBy('s_producto.id','asc')
            ->get();
        foreach($s_producto as $valuepro){
          
                    $s_productosaldosventa = DB::table('s_productosaldos')
                        ->where('s_productosaldos.idtienda',$idtienda)
                        ->where('s_productosaldos.idproducto',$valuepro->id)
                        ->where('s_productosaldos.cantidadrestante','>',0)
                        ->where('s_productosaldos.concepto','VENTA')
                        ->orderBy('s_productosaldos.id','asc')
                        ->get(); 
                    foreach($s_productosaldosventa as $valueventa){
                    
                        $s_productosaldoscompra = DB::table('s_productosaldos')
                            ->where('s_productosaldos.idtienda',$idtienda)
                            ->where('s_productosaldos.idproducto',$valuepro->id)
                            ->where('s_productosaldos.cantidadrestante','>',0)
                            ->where('s_productosaldos.concepto','COMPRA')
                            ->orderBy('s_productosaldos.id','asc')
                            ->get();
                      
                        $cantidadventa = $valueventa->cantidadrestante; 
                        foreach($s_productosaldoscompra as $valuecompra){
                            if($cantidadventa<=$valuecompra->cantidadrestante && $cantidadventa>0){
                              
                                // detalle
                                DB::table('s_productosaldosdetalle')->insert([
                                    'cantidad' => $cantidadventa,
                                    'idproductosaldoingreso' => $valuecompra->id,
                                    'idproductosaldosalida' => $valueventa->id
                                ]);
                                // venta
                                DB::table('s_productosaldos')->whereId($valueventa->id)->update([
                                    'cantidadrestante' => 0
                                ]);
                                // compra
                                DB::table('s_productosaldos')->whereId($valuecompra->id)->update([
                                    'cantidadrestante' => $valuecompra->cantidadrestante-$cantidadventa
                                ]);
                              
                                //$cantidadventa = $valuecompra->cantidadrestante-$cantidadventa;
                                $cantidadventa = 0;
                              
                            }elseif($cantidadventa>$valuecompra->cantidadrestante && $valuecompra->cantidadrestante>0){
                              
                                // detalle
                                DB::table('s_productosaldosdetalle')->insert([
                                    'cantidad' => $valuecompra->cantidadrestante,
                                    'idproductosaldoingreso' => $valuecompra->id,
                                    'idproductosaldosalida' => $valueventa->id
                                ]);
                                // venta
                                DB::table('s_productosaldos')->whereId($valueventa->id)->update([
                                    'cantidadrestante' => $cantidadventa-$valuecompra->cantidadrestante
                                ]);
                                // compra
                                DB::table('s_productosaldos')->whereId($valuecompra->id)->update([
                                    'cantidadrestante' => 0
                                ]);
                                $cantidadventa = $cantidadventa-$valuecompra->cantidadrestante;
                            }else{
                                break;
                            }
                        }
                    }
        }*/
        if($request->input('tipo')=='excel'){
         $productosaldos = DB::table('s_productosaldos')
            ->where('s_productosaldos.idtienda',$idtienda)
            ->where('s_productosaldos.idproducto',$request->input('idproducto'))
            ->orderBy('s_productosaldos.id','desc')
            ->get();
          
        
            $titulo = 'Reporte de Stock';
         

            return Excel::download(new 
                                    ReportekardexExport($productosaldos, $titulo),
                                    $titulo.'.xls'
                                  );
            /* FIN - Capturando los valores de filtrar para mostrar en el excel */
            
        }else{
      
        $productosaldos = DB::table('s_productosaldos')
            ->join('s_producto','s_producto.id','s_productosaldos.idproducto')
            ->join('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
            ->where('s_productosaldos.idtienda',$idtienda)
            ->where('s_productosaldos.idproducto',$request->input('idproducto'))
            ->select(
                's_productosaldos.*',
                's_producto.codigo as producto_codigo',
                's_producto.nombre as producto_nombre',
                's_producto.por as producto_por',
                'unidadmedida.nombre as unidadmedidanombre',
            )
            ->orderBy('s_productosaldos.id','desc')
            ->paginate(10);
        
        return view('layouts/backoffice/tienda/sistema/reportekardex/index',[
            'tienda' => $tienda,
            'productosaldos' => $productosaldos,
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
