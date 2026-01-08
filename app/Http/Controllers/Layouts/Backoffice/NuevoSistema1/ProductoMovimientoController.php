<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class ProductoMovimientoController extends Controller
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
         json_productomovimiento($idtienda,$request->name_modulo);

        return view('layouts/backoffice/tienda/nuevosistema/productomovimiento/index',[
            'tienda' => $tienda,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $tipomovimiento = DB::table('s_tipomovimiento')->get();
        $configuracion = configuracion_comercio($idtienda);
        return view('layouts/backoffice/tienda/nuevosistema/productomovimiento/create',[
          'tipomovimiento' => $tipomovimiento,
          'configuracion' => $configuracion,
          'tienda' => $tienda
        ]);
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
        if($request->input('view') == 'registrar') {
            
            $rules = [
                'idtipomovimiento' => 'required',
                'motivo' => 'required',
                'productos' => 'required',
            ];
            
            $messages = [
              'idtipomovimiento.required' => 'El "Tipo de Movimiento" es Obligatorio.',
              'motivo.required' => 'El "Motivo" es Obligatorio.',
              'productos.required' => 'Los "Productos" son Obligatorio.',
            ];
          
            $this->validate($request,$rules,$messages);
          
            $productos = explode('/&/', $request->input('productos'));
            for($i = 1;$i <  count($productos);$i++){
                $item = explode('/,/', $productos[$i]);
                if($item[1]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad minímo es 1.'
                    ]);
                    break;
                }
                // stock
                if($request->input('idtipomovimiento')==2){
                    $configuracion = configuracion_comercio($idtienda);
                    if($configuracion!=''){
                        if($configuracion->venta_estadostock==1){
                            $stock_producto = stock_producto($idtienda,$item[0]);
                            if($stock_producto['total']<$item[1]){
                                return response()->json([
                                    'resultado' => 'ERROR',
                                    'mensaje'   => 'El Producto <b>"'.$item[2].'"</b> no cuenta con stock suficiente, ingrese otro producto!!.'
                                ]);
                                break;
                            }
                        }  
                    }        
                }
            } 
            for($i = 1;$i <  count($productos);$i++){
                $item = explode('/,/', $productos[$i]);
                $idproductomovimiento = DB::table('s_productomovimiento')->insertGetId([
                   'fecharegistro' => Carbon::now(),
                   'motivo' => $request->input('motivo'),
                   'cantidad' => $item[1],
                   's_idproducto' => $item[0],
                   's_idusuarioresponsable' => Auth::user()->id,
                   's_idtipomovimiento' => $request->input('idtipomovimiento'),
                   's_idestado' => 1,
                   'idtienda' => $idtienda,
                ]); 
              
                $producto = DB::table('s_producto')->whereId($item[0])->first();
              
                $produxto_preciounitario = $producto->precioalpublico;
              
                // SALDO
                if($request->input('idtipomovimiento')==1){
                    productosaldo_actualizar(
                        $idtienda,
                        'MOVIMIENTO INGRESO',
                        $producto->codigo,
                        $producto->nombre,
                        $producto->idunidadmedida,
                        $producto->por,
                        $item[1],
                        $produxto_preciounitario,
                        $item[1]*$produxto_preciounitario,
                        $item[0],
                        $idproductomovimiento
                    );
                }elseif($request->input('idtipomovimiento')==2){
                    productosaldo_actualizar(
                        $idtienda,
                        'MOVIMIENTO SALIDA',
                        $producto->codigo,
                        $producto->nombre,
                        $producto->idunidadmedida,
                        $producto->por,
                        $item[1],
                        $produxto_preciounitario,
                        $item[1]*$produxto_preciounitario,
                        $item[0],
                        $idproductomovimiento
                    );
                } 
            }
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if ($id == 'show-moduloactualizar') {
              json_productomovimiento($idtienda,$request->name_modulo);
        }
        elseif ($id == 'showseleccionarproducto') {
            return producto($idtienda,$request->input('idproducto'));
        }
        elseif ($id == 'showseleccionarproductocodigo') {
            if ($request->input('codigoproducto')=='') {
              return [ 
                'resultado' => 'ERROR',
                'mensaje'   => 'Ingrese un codigo de Producto!!.',
              ];
            }
            $datosProducto = DB::table('s_producto')
              ->join('tienda','tienda.id','s_producto.idtienda')
              ->where('s_producto.idtienda',$idtienda)
              ->where('s_producto.codigo',$request->input('codigoproducto'))
              ->where('s_producto.s_idestado',1)
              ->select(
                  's_producto.*',
                  'tienda.nombre as tiendanombre',
                  'tienda.link as tiendalink',
                  DB::raw('(SELECT imagen FROM s_productogaleria WHERE s_idproducto=s_producto.id ORDER BY orden ASC LIMIT 1) as imagen')
              )
              ->first();
            if ($datosProducto=='') {
              return [ 
                'resultado' => 'ERROR',
                'mensaje'   => 'No existe el producto, ingrese otro código.',
              ];
            }
            return [ 
              'producto' => $datosProducto,
              'stock' => productosaldo($idtienda,$datosProducto->id)['stock']
            ];
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $idproductomovimiento)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $s_productomovimiento = DB::table('s_productomovimiento')
          ->join('s_producto','s_producto.id','s_productomovimiento.s_idproducto')
          ->where('s_productomovimiento.id',$idproductomovimiento)
          ->select(
            's_productomovimiento.*',
            's_producto.codigo as productocodigo',
            's_producto.nombre as productonombre'
          )
          ->first();
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $tipomovimiento = DB::table('s_tipomovimiento')->get();

        if($request->input('view') == 'editar') {
            return view('layouts/backoffice/tienda/nuevosistema/productomovimiento/edit',[
              's_productomovimiento' => $s_productomovimiento,
              'tipomovimiento' => $tipomovimiento,
              'tienda' => $tienda
            ]);
        }
        elseif($request->input('view') == 'detalle') {
            return view('layouts/backoffice/tienda/nuevosistema/productomovimiento/detalle',[
              's_productomovimiento' => $s_productomovimiento,
              'tipomovimiento' => $tipomovimiento,
              'tienda' => $tienda
            ]);
        }
        elseif($request->input('view') == 'eliminar') {
            return view('layouts/backoffice/tienda/nuevosistema/productomovimiento/delete',[
              's_productomovimiento' => $s_productomovimiento,
              'tipomovimiento' => $tipomovimiento,
              'tienda' => $tienda
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idtienda, $idproductomovimiento)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);

        if($request->input('view') == 'editar') {
          $rules = [
            's_idtipomovimiento' => 'required',
            'motivo' => 'required',
            'cantidad' => 'required',
            's_idestado' => 'required'
          ];
          $messages = [
            's_idtipomovimiento.required' => 'El "Tipo de Movimiento" es Obligatorio.',
            'motivo.required' => 'El "Motivo" es Obligatorio.',
            'cantidad.required' => 'La "Cantidad" es Obligatorio.',
            's_idestado.required' => 'El "Estado" es Obligatorio.'
          ];
          $this->validate($request,$rules,$messages);
          
          if ($request->s_idestado == 1) {
            $fechaconfirmacion = null;
          } else {
            $fechaconfirmacion = Carbon::now();
          }

          DB::table('s_productomovimiento')->whereId($idproductomovimiento)->update([
            'fechaconfirmacion' => $fechaconfirmacion,
            'motivo' => $request->motivo,
            'cantidad' => $request->cantidad,
            's_idtipomovimiento' => $request->s_idtipomovimiento,
            's_idestado' => $request->s_idestado
          ]);
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha actualizado correctamente.'
          ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtienda, $idproductomovimiento)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
            DB::table('s_productomovimiento')
              ->where('id',$idproductomovimiento)
              ->where('idtienda',$idtienda)
              ->update([
                'fechaeliminado' => Carbon::now(),
                's_idestado' => 3
              ]);
           
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
