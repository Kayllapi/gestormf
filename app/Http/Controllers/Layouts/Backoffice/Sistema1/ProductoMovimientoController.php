<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

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
      
        $where = [];
        if($request->input('codigo')!=''){
            $where[] = ['s_productomovimiento.codigo',$request->input('codigo')];
        }
        $where[] = ['s_tipomovimiento.nombre','LIKE','%'.$request->input('tipo').'%'];
        $where[] = ['s_productomovimiento.motivo','LIKE','%'.$request->input('motivo').'%'];
        $where[] = ['responsable.nombre','LIKE','%'.$request->input('responsable').'%'];
        $where[] = ['s_producto.nombre','LIKE','%'.$request->input('producto').'%'];
      
       
        $s_productomovimiento  = DB::table('s_productomovimiento')
            ->leftJoin('users as responsable','responsable.id','s_productomovimiento.s_idusuarioresponsable')
            ->leftJoin('s_tipomovimiento','s_tipomovimiento.id','s_productomovimiento.s_idtipomovimiento')
            ->leftJoin('s_producto','s_producto.id','s_productomovimiento.s_idproducto')
            ->leftJoin('unidadmedida','unidadmedida.id','s_productomovimiento.idunidadmedida')
            ->where('s_productomovimiento.idtienda',$idtienda)
            ->where('s_productomovimiento.idestado',1)
            ->where($where)
            ->select(
                's_productomovimiento.*',
                'responsable.nombre as responsablenombre',
                's_tipomovimiento.nombre as nombretipomovimiento',
                's_producto.codigo as productocodigo',
                's_producto.nombre as productonombre',
                 DB::raw('CONCAT(unidadmedida.nombre," x ",s_producto.por) as unidadmedida')
            )
            ->orderBy('s_productomovimiento.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/productomovimiento/index',[
            'tienda'      => $tienda,
            's_productomovimiento'  => $s_productomovimiento
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
        $tipomovimientos = DB::table('s_tipomovimiento')->get();

        return view('layouts/backoffice/tienda/sistema/productomovimiento/create',[
            'tienda' => $tienda,
            'tipomovimientos' => $tipomovimientos,
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
                if($item[1]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad debe ser mayor a 0.'
                    ]);
                    break;
                }
                if($item[1]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad minímo es 1.'
                    ]);
                    break;
                }
                // stock
                if(configuracion($idtienda,'sistema_estadostock')['resultado']=='CORRECTO'){
                    if(configuracion($idtienda,'sistema_estadostock')['valor']==1){
                        if($request->input('idtipomovimiento')==2){
                        $productosaldo = productosaldo($idtienda,$item[0]);
                        if($productosaldo['stock']<$item[1]){
                            return response()->json([
                                'resultado' => 'ERROR',
                                'mensaje'   => 'El Producto <b>"'.$item[3].'"</b> no cuenta con stock suficiente, ingrese otro producto!!.'
                            ]);
                            break;
                        }
                        }
                    }  
                }  
            } 
            for($i = 1;$i <  count($productos);$i++){
                $item = explode('/,/', $productos[$i]);
              
                $producto = DB::table('s_producto')->whereId($item[0])->first();
                
                $idproductomovimiento = DB::table('s_productomovimiento')->insertGetId([
                   'fecharegistro' => Carbon::now(),
                   'fechaconfirmacion' => Carbon::now(),
                   'motivo' => $request->input('motivo'),
                   'cantidad' => $item[1],
                   'por' => $producto->por,
                   'idunidadmedida' => $producto->idunidadmedida,
                   's_idproducto' => $item[0],
                   's_idusuarioresponsable' => Auth::user()->id,
                   's_idtipomovimiento' => $request->input('idtipomovimiento'),
                   'idestadoproductomovimiento' => 2,
                   'idestado' => 1,
                   'idtienda' => $idtienda,
                ]); 
              
                // SALDO
                if($request->input('idtipomovimiento')==1){
                    productosaldo_actualizar(
                        $idtienda,
                        $producto->id,
                        'MOVIMIENTO INGRESO',
                        $item[1],
                        $producto->idunidadmedida,
                        $producto->por,
                        $idproductomovimiento
                    );
                }elseif($request->input('idtipomovimiento')==2){
                    productosaldo_actualizar(
                        $idtienda,
                        $producto->id,
                        'MOVIMIENTO SALIDA',
                        $item[1],
                        $producto->idunidadmedida,
                        $producto->por,
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
        if($id=='showseleccionarproducto'){
            return producto($idtienda,$request->input('idproducto'));
        }elseif($id=='showstockproducto'){
            return producto($idtienda,$request->input('idproducto'));
        }elseif($id=='showseleccionarunidadproducto'){
            return unidad_productos($idtienda,$request->input('idproducto'));
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
      
        /*$s_productomovimiento  = DB::table('s_productomovimiento')
            ->leftJoin('users as responsable','responsable.id','s_productomovimiento.s_idusuarioresponsable')
            ->leftJoin('s_tipomovimiento','s_tipomovimiento.id','s_productomovimiento.s_idtipomovimiento')
            ->where('s_productomovimiento.idtienda',$idtienda)
            ->where('s_productomovimiento.id',$idproductomovimiento)
            ->select(
                's_productomovimiento.*',
                'responsable.nombre as responsablenombre',
                's_tipomovimiento.nombre as nombretipomovimiento'
            )
            ->first();
      
        if($request->input('view') == 'editar') {
          
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $tipomovimientos = DB::table('s_tipomovimiento')->get();
          
            $s_productomovimientodetalles  = DB::table('s_productomovimientodetalle')
                ->leftJoin('s_producto','s_producto.id','s_productomovimientodetalle.s_idproducto')
                ->where('s_productomovimientodetalle.s_idproductomovimiento',$s_productomovimiento->id)
                ->select(
                    's_productomovimientodetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->get();

            return view('layouts/backoffice/tienda/sistema/productomovimiento/edit',[
                'tienda' => $tienda,
                'tipomovimientos' => $tipomovimientos,
                'productomovimiento' => $s_productomovimiento,
                'productomovimientodetalles' => $s_productomovimientodetalles,
            ]);
 
        }
        elseif($request->input('view') == 'confirmar') {
          
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $tipomovimientos = DB::table('s_tipomovimiento')->get();
          
            $s_productomovimientodetalles  = DB::table('s_productomovimientodetalle')
                ->leftJoin('s_producto','s_producto.id','s_productomovimientodetalle.s_idproducto')
                ->where('s_productomovimientodetalle.s_idproductomovimiento',$s_productomovimiento->id)
                ->select(
                    's_productomovimientodetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->get();

            return view('layouts/backoffice/tienda/sistema/productomovimiento/confirmar',[
                'tienda' => $tienda,
                'tipomovimientos' => $tipomovimientos,
                'productomovimiento' => $s_productomovimiento,
                'productomovimientodetalles' => $s_productomovimientodetalles,
            ]);
 
        }
        elseif($request->input('view') == 'detalle') {
          
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $tipomovimientos = DB::table('s_tipomovimiento')->get();
          
            $s_productomovimientodetalles  = DB::table('s_productomovimientodetalle')
                ->leftJoin('s_producto','s_producto.id','s_productomovimientodetalle.s_idproducto')
                ->where('s_productomovimientodetalle.s_idproductomovimiento',$s_productomovimiento->id)
                ->select(
                    's_productomovimientodetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->get();

            return view('layouts/backoffice/tienda/sistema/productomovimiento/detalle',[
                'tienda' => $tienda,
                'tipomovimientos' => $tipomovimientos,
                'productomovimiento' => $s_productomovimiento,
                'productomovimientodetalles' => $s_productomovimientodetalles,
            ]);
 
        }
        elseif($request->input('view') == 'eliminar') {
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $tipomovimientos = DB::table('s_tipomovimiento')->get();
          
            $s_productomovimientodetalles  = DB::table('s_productomovimientodetalle')
                ->leftJoin('s_producto','s_producto.id','s_productomovimientodetalle.s_idproducto')
                ->where('s_productomovimientodetalle.s_idproductomovimiento',$s_productomovimiento->id)
                ->select(
                    's_productomovimientodetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->get();

            return view('layouts/backoffice/tienda/sistema/productomovimiento/delete',[
                'tienda' => $tienda,
                'tipomovimientos' => $tipomovimientos,
                'productomovimiento' => $s_productomovimiento,
                'productomovimientodetalles' => $s_productomovimientodetalles,
            ]);
        }*/
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
      
        /*if($request->input('view') == 'editar') {
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
          
            $productos = explode('&', $request->input('productos'));
            for($i = 1;$i <  count($productos);$i++){
                $item = explode(',', $productos[$i]);
                if($item[1]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad minímo es 1.'
                    ]);
                    break;
                }elseif($item[2]<0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Precio minímo es 0.00.'
                    ]);
                    break;
                }
                // stock
        
                    if(configuracion($tienda->id,'sistema_estadostock')['valor']==1){
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
          
            DB::table('s_productomovimiento')->whereId($idproductomovimiento)->update([
               'motivo' => $request->input('motivo'),
               's_idusuarioresponsable' => Auth::user()->id,
               's_idtipomovimiento' => $request->input('idtipomovimiento'),
            ]);
            
            DB::table('s_productomovimientodetalle')->where('s_idproductomovimiento',$idproductomovimiento)->delete();
          
            $productos = explode('&', $request->input('productos'));
            for($i = 1; $i < count($productos); $i++){
                $item = explode(',',$productos[$i]);
                DB::table('s_productomovimientodetalle')->insert([
                  'cantidad' => $item[1],
                  's_idproducto' => $item[0],
                  's_idproductomovimiento' => $idproductomovimiento,
                ]);
            }       
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha actualizando correctamente.'
            ]);
        }
        elseif($request->input('view') == 'confirmar') {
  
            // stock
         
                if(configuracion($tienda->id,'sistema_estadostock')['valor']==1){
                      
                    $s_productomovimientodetalles  = DB::table('s_productomovimientodetalle')
                        ->leftJoin('s_producto','s_producto.id','s_productomovimientodetalle.s_idproducto')
                        ->where('s_productomovimientodetalle.s_idproductomovimiento',$idproductomovimiento)
                        ->select(
                            's_productomovimientodetalle.*',
                            's_producto.codigo as productocodigo',
                            's_producto.nombre as productonombre'
                        )
                        ->get();
                    foreach($s_productomovimientodetalles as $value){
                        $stock_producto = stock_producto($idtienda,$value->s_idproducto);
                        if($stock_producto['total']<$value->cantidad){
                            return response()->json([
                                'resultado' => 'ERROR',
                                'mensaje'   => 'El Producto <b>"'.$value->productocodigo.' - '.$value->productonombre.'"</b> no cuenta con stock suficiente, ingrese otro producto!!.'
                            ]);
                            break;
                        }
                    }
                }  
        
                
          
            DB::table('s_productomovimiento')->whereId($idproductomovimiento)->update([
               'fechaconfirmacion' => Carbon::now(),
               'idestadoproductomovimiento' => 2,
            ]);

            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha confirmado correctamente.'
            ]);
        }*/
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
      
        /*if($request->input('view') == 'eliminar') {
          
            DB::table('s_productomovimientodetalle')->where('s_idproductomovimiento',$idproductomovimiento)->delete();
            DB::table('s_productomovimiento')
                ->whereId($idproductomovimiento)
                ->delete();
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }*/
    }
}
