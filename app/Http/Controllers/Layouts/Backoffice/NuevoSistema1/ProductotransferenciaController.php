<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class ProductotransferenciaController extends Controller
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
        json_productotransferencia($idtienda,$request->name_modulo);

        return view('layouts/backoffice/tienda/nuevosistema/productotransferencia/index',[
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
        $tiendas = DB::table('tienda')->where('id',$idtienda)->get();
        $configuracion  = configuracion_comercio($idtienda);

        return view('layouts/backoffice/tienda/nuevosistema/productotransferencia/create',[
            'tienda'        => $tienda,
            'tiendas'       => $tiendas,
            'configuracion' => $configuracion,
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
            $rules    = [
                'idtiendaorigen'                 => 'required',
                'idtiendadestino'                => 'required',
                'idestadotransferencia'          => 'required',
                'productos'                      => 'required',
            ];
            $messages = [
                'idtiendaorigen.required'         => 'El campo "De" es Obligatorio.',
                'idtiendadestino.required'        => 'El campo "Para" es Obligatorio.',
                'idestadotransferencia.required'  => 'El "Estado" es Obligatorio.',
                'productos.required'              => 'Los "Productos" son Obligatorio.',
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
                }elseif($item[2]<0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Unidad de Medida es obligatorio.'
                    ]);
                    break;
                }        
            } 
            
           $productotransferencia = DB::table('s_productotransferencia')
                ->orderBy('s_productotransferencia.codigo','desc')
                ->limit(1)
                ->first();
           
            $codigo = 1;
            if($productotransferencia!=''){
                $codigo = $productotransferencia->codigo+1;
            }
           
            $fechaenvio = null;
            $idusersorigen = 0;
            $idusersdestino = 0;
           
           // 1-Estado = Solicitud
           // 2-Estado = Envio
            if($request->input('idestadotransferencia')==1){
                $idusersdestino = Auth::user()->id;
            }elseif($request->input('idestadotransferencia')==2){
                $fechaenvio = Carbon::now();
                $idusersorigen = Auth::user()->id;
            }

            $idtransferencia = DB::table('s_productotransferencia')->insertGetId([
              'fecharegistro'         => Carbon::now(),
              'fechasolicitud'        => Carbon::now(),
              'fechaenvio'            => $fechaenvio,
              'codigo'                => $codigo,
              'motivo'                => $request->input('motivo')!=''?$request->input('motivo'):'',
              'idtiendaorigen'        => $request->input('idtiendaorigen'),
              'idtiendadestino'       => $request->input('idtiendadestino'),
              'idusersorigen'         => $idusersorigen,
              'idusersdestino'        => $idusersdestino,
              'idestadotransferencia' => $request->input('idestadotransferencia'),
              'idestado'              => 2,
            ]);
     
            for($i = 1; $i < count($productos); $i++){
                $item   = explode('/,/',$productos[$i]);
              
              DB::table('s_productotransferenciadetalle')->insert([
                  'cantidad'                => $item[1],
                  'cantidadenviado'         => $item[1],
                  'cantidadrecepcion'       => 0,
                  'motivo'                  => $item[3],
                  'idunidadmedida'          => $item[2],
                  'idproducto'              => $item[0],
                  'idproductotransferencia' => $idtransferencia,
                ]);
              
            }
           
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $idtienda,$id)
    {
       $request->user()->authorizeRoles($request->path(),$idtienda);
        if($id=='showseleccionarproducto'){
          $producto = DB::table('s_producto')
                ->join('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                ->where('s_producto.idtienda',$idtienda)
                ->where('s_producto.id',$request->input('idproducto'))
                ->where('s_producto.s_idestado',1)
                ->select(
                    's_producto.*',
                    'unidadmedida.id as idunidadmedida',
                    'unidadmedida.nombre as unidadmedidanombre'
                )
                ->first();
          if($producto==''){
              return [ 
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No existe el producto, ingrese otro código.',
              ];
          }
            return [ 
                'producto'  => $producto,
                'stock'     => productosaldo($idtienda,$producto->id)['stock']
            ];
        }elseif($id=='showseleccionarproductocodigo'){
            if($request->input('codigoproducto')==''){
                return [ 
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Ingrese un codigo de Producto!!.',
                ];
            }
            $datosProducto = DB::table('s_producto')
                ->join('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                ->where('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo',$request->input('codigoproducto'))
                ->where('s_producto.s_idestado',1)
                ->select(
                    's_producto.*',
                    'unidadmedida.id as idunidadmedida',
                    'unidadmedida.nombre as unidadmedidanombre'
                )
                ->first();
            if($datosProducto==''){
                return [ 
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No existe el producto, ingrese otro código.',
                ];
            }
            return [ 
              'producto' => $datosProducto,
              'stock'    => productosaldo($idtienda,$datosProducto->id)['stock']
            ];
        }elseif($id=='show-modoactualizar'){
           json_productotransferencia($idtienda,$request->name_modulo);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      //
      $configuracion  = configuracion_comercio($idtienda);
      //
        $productotransferencia = DB::table('s_productotransferencia')
          ->join('tienda as tienda_origen','tienda_origen.id' ,'s_productotransferencia.idtiendaorigen')
          ->join('ubigeo as ubigeo_origen','ubigeo_origen.id' ,'tienda_origen.idubigeo')
          ->join('tienda as tienda_destino','tienda_destino.id' ,'s_productotransferencia.idtiendadestino')
          ->join('ubigeo as ubigeo_destino','ubigeo_destino.id' ,'tienda_destino.idubigeo')
            ->leftJoin('users as user_origen','user_origen.id' ,'s_productotransferencia.idusersorigen')
            ->leftJoin('users as user_destino','user_destino.id' ,'s_productotransferencia.idusersdestino')
          ->where('s_productotransferencia.id', $id)
          ->select(
            's_productotransferencia.*',
            'tienda_origen.idubigeo as tienda_origen_idubigeo',
            'tienda_origen.nombre as tienda_origen_nombre',
            'tienda_origen.direccion as tienda_origen_direccion',
            'ubigeo_origen.nombre as ubigeo_origen_nombre',
            'tienda_destino.idubigeo as tienda_destino_idubigeo',
            'tienda_destino.nombre as tienda_destino_nombre',
            'tienda_destino.direccion as tienda_destino_direccion',
            'ubigeo_destino.nombre as ubigeo_destino_nombre',
              'tienda_destino.id as idusersdestino',
           'tienda_origen.id as idusersorigen',
             'user_origen.nombre as user_origen_nombre',
              'user_destino.nombre as user_destino_nombre',
          )
          ->first();
      //$configuracion = configuracion_comercio($idtienda);
      
       if($request->input('view') == 'detalle') {
          
            $tiendas = DB::table('tienda')->where('id',$idtienda)->first();
          
            $detalletransferencia = DB::table('s_productotransferenciadetalle')
              ->join('s_producto','s_producto.id','s_productotransferenciadetalle.idproducto')
              ->join('unidadmedida','unidadmedida.id','s_productotransferenciadetalle.idunidadmedida')
              ->where('s_productotransferenciadetalle.idproductotransferencia', $productotransferencia->id)
              ->select(
                's_productotransferenciadetalle.*',
                's_producto.codigo as producodigoimpresion',
                's_producto.nombre as productonombre',
                'unidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('s_productotransferenciadetalle.id','asc')
              ->get();
         
          return view('layouts/backoffice/tienda/nuevosistema/productotransferencia/detalle',[
              'tienda' => $tienda,
              'tiendas' => $tiendas,
              'productotransferencia' => $productotransferencia,
              'detalletransferencia' => $detalletransferencia
          ]);
          
        }
        else if($request->input('view') == 'editar') {
          
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $tiendas = DB::table('tienda')->where('id',$idtienda)->first();
          
        $configuracion  = configuracion_comercio($idtienda);
          
          $detalletransferencia = DB::table('s_productotransferenciadetalle')
              ->join('s_producto','s_producto.id','s_productotransferenciadetalle.idproducto')
              ->join('unidadmedida','unidadmedida.id','s_productotransferenciadetalle.idunidadmedida')
              ->where('s_productotransferenciadetalle.idproductotransferencia', $productotransferencia->id)
              ->select(
                's_productotransferenciadetalle.*',
                's_producto.codigo as producodigoimpresion',
                's_producto.nombre as productonombre',
                'unidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('s_productotransferenciadetalle.id','asc')
              ->get();
          return view('layouts/backoffice/tienda/nuevosistema/productotransferencia/edit',[
              'tienda'                 => $tienda,
              'tiendas'                => $tiendas,
              'productotransferencia'  => $productotransferencia,
              'detalletransferencia'   => $detalletransferencia,
              'configuracion'          =>$configuracion
          ]);
          
        }else if($request->input('view') == 'rechazar') {
          
            $tiendas = DB::table('tienda')->where('id',$idtienda)->get();
            $detalletransferencia = DB::table('s_productotransferenciadetalle')
              ->join('s_producto','s_producto.id','s_productotransferenciadetalle.idproducto')
              ->join('unidadmedida','unidadmedida.id','s_productotransferenciadetalle.idunidadmedida')
              ->where('s_productotransferenciadetalle.idproductotransferencia', $productotransferencia->id)
              ->select(
                's_productotransferenciadetalle.*',
                's_producto.codigo as producodigoimpresion',
                's_producto.nombre as productonombre',
                'unidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('s_productotransferenciadetalle.id','asc')
              ->get();
          
          return view('layouts/backoffice/tienda/nuevosistema/productotransferencia/rechazar',[
              'tienda'                => $tienda,
              'tiendas'               => $tiendas,
              'productotransferencia' => $productotransferencia,
              'detalletransferencia'  => $detalletransferencia
          ]);
          
        }else if($request->input('view') == 'confirmar') {
          
            $tiendas = DB::table('tienda')->where('id',$idtienda)->get();
            $detalletransferencia = DB::table('s_productotransferenciadetalle')
              ->join('s_producto','s_producto.id','s_productotransferenciadetalle.idproducto')
              ->join('unidadmedida','unidadmedida.id','s_productotransferenciadetalle.idunidadmedida')
              ->where('s_productotransferenciadetalle.idproductotransferencia', $productotransferencia->id)
              ->select(
                's_productotransferenciadetalle.*',
                's_producto.codigo as producodigoimpresion',
                's_producto.nombre as productonombre',
                'unidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('s_productotransferenciadetalle.id','asc')
              ->get();
          
          return view('layouts/backoffice/tienda/nuevosistema/productotransferencia/confirmar',[
             'tienda' => $tienda,
              'tiendas' => $tiendas,
              'productotransferencia' => $productotransferencia,
              'detalletransferencia' => $detalletransferencia
          ]);
          
        }else if($request->input('view') == 'eliminar') {
          
            $tiendas = DB::table('tienda')->where('id',$idtienda)->first();
            $detalletransferencia = DB::table('s_productotransferenciadetalle')
              ->join('s_producto','s_producto.id','s_productotransferenciadetalle.idproducto')
              ->join('unidadmedida','unidadmedida.id','s_productotransferenciadetalle.idunidadmedida')
              ->where('s_productotransferenciadetalle.idproductotransferencia', $productotransferencia->id)
              ->select(
                's_productotransferenciadetalle.*',
                's_producto.codigo as producodigoimpresion',
                's_producto.nombre as productonombre',
                'unidadmedida.nombre as unidadmedidanombre'
              )
              ->orderBy('s_productotransferenciadetalle.id','asc')
              ->get();
          
          return view('layouts/backoffice/tienda/nuevosistema/productotransferencia/delete',[
             'tienda' => $tienda,
              'tiendas' => $tiendas,
              'productotransferencia' => $productotransferencia,
              'detalletransferencia' => $detalletransferencia
          ]);
          
        }  elseif($request->input('view') == 'ticket') {
            return view('layouts/backoffice/tienda/nuevosistema/productotransferencia/ticket',[
                'tienda' => $tienda,
                'productotransferencia' => $productotransferencia

            ]);
        }
        elseif($request->input('view') == 'ticketpdf') {
            $agencia = DB::table('s_agencia')
                ->leftJoin('ubigeo','ubigeo.id','s_agencia.idubigeo')
                ->select(
                  's_agencia.*',
                  'ubigeo.nombre as ubigeonombre'
                )
                ->first();
          
           
            $transferenciadetalle = DB::table('s_productotransferenciadetalle')
                ->join('s_producto','s_producto.id','s_productotransferenciadetalle.idproducto')
                ->where('s_productotransferenciadetalle.idproductotransferencia',$productotransferencia->id)
                ->select(
                  's_productotransferenciadetalle.*',
                  's_producto.codigo as productocodigo',
                  's_producto.nombre as productonombre'
                )
                ->orderBy('s_productotransferenciadetalle.id','asc')
                ->get();
          

            $pdf = PDF::loadView('layouts/backoffice/tienda/nuevosistema/productotransferencia/ticketpdf',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'transferenciadetalle' => $transferenciadetalle,
               'productotransferencia' => $productotransferencia,
                              'configuracion' => $configuracion,

              
            ]);
            $ticket = 'Ticket_'.str_pad($productotransferencia->codigo, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');

        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      
      if($request->input('view') == 'edit') {
            $rules    = [
                'idtiendaorigen'                 => 'required',
                'idtiendadestino'                => 'required',
                'idestadotransferencia'          => 'required',
                'productos'                      => 'required',
            ];
            $messages = [
                'idtiendaorigen.required'         => 'El campo "De" es Obligatorio.',
                'idtiendadestino.required'        => 'El campo "Para" es Obligatorio.',
                'idestadotransferencia.required'  => 'El "Estado" es Obligatorio.',
                'productos.required'              => 'Los "Productos" son Obligatorio.',
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
                }elseif($item[2]<0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Unidad de Medida es obligatorio.'
                    ]);
                    break;
                }        
            } 
           
            $idusersorigen = 0;
            $idusersdestino = 0;
           
           // 1-Estado = Solicitud
           // 2-Estado = Envio
            if($request->input('idestadotransferencia')==1){
                $idusersdestino = Auth::user()->id;
            }elseif($request->input('idestadotransferencia')==2){
                $idusersorigen = Auth::user()->id;
            }

            DB::table('s_productotransferencia')->whereId($id)->update([
              'motivo'                => $request->input('motivo')!=''?$request->input('motivo'):'',
              'idtiendaorigen'        => $request->input('idtiendaorigen'),
              'idtiendadestino'       => $request->input('idtiendadestino'),
              'idusersorigen'         => $idusersorigen,
              'idusersdestino'        => $idusersdestino,
              'idestadotransferencia' => $request->input('idestadotransferencia'),
            ]);
     
            DB::table('s_productotransferenciadetalle')->where('idproductotransferencia', $id)->delete();
            for($i = 1; $i < count($productos); $i++){
                $item   = explode('/,/',$productos[$i]);
              
              DB::table('s_productotransferenciadetalle')->insert([
                  'cantidad'                => $item[1],
                  'cantidadenviado'         => $item[1],
                  'cantidadrecepcion'       => 0,
                  'motivo'                  => $item[3],
                  'idunidadmedida'          => $item[2],
                  'idproducto'              => $item[0],
                  'idproductotransferencia' => $id,
                ]);
              
            }
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }elseif($request->input('view') == 'recepcionar') {
            $rules = [
                'idtiendaorigen'        => 'required',
                'idtiendadestino'       => 'required',
                'idestadotransferencia' => 'required',
                'productos'             => 'required',
            ];

            $messages = [
                'idtiendaorigen.required'   => 'El campo "De" es Obligatorio.',
                'idtiendadestino.required'   => 'El campo "Para" es Obligatorio.',
                'idtiendadestino.required'   => 'El "Estado" es Obligatorio.',
                'productos.required'      => 'Los "Productos" son Obligatorio.',
            ];
      
            $this->validate($request,$rules,$messages);
      
            $productos = explode('&', $request->input('productos'));
            for($i = 1;$i <  count($productos);$i++){
                $item = explode(',', $productos[$i]);
                if($item[5]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad minímo es 1.'
                    ]);
                    break;
                }elseif($item[2]<0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Unidad de Medida es obligaorio.'
                    ]);
                    break;
                }
            } 
          
            DB::table('s_productotransferencia')->whereId($id)->update([
              'fecharecepcion' => Carbon::now(),
              'idusersdestino' => Auth::user()->id,
              'idestadotransferencia' => 3,
              'idestado' => 2
            ]);
          
            DB::table('s_productotransferenciadetalle')->where('idproductotransferencia', $id)->delete();
            for($i = 1; $i < count($productos); $i++){
                $item = explode(',',$productos[$i]);
                DB::table('s_productotransferenciadetalle')->insert([
                  'motivo' => $item[3],
                  'cantidad' => $item[1],
                  'cantidadenviado' => $item[4],
                  'cantidadrecepcion' => $item[5],
                  'idunidadmedida' => $item[2],
                  'idproducto' => $item[0],
                  'idproductotransferencia' => $id,
                ]);
            }
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha enviado correctamente.'
            ]);
        }elseif($request->input('view') == 'rechazar') { 
            
            $productotransferencia = DB::table('s_productotransferencia')->whereId($id)->first();
          
            if($productotransferencia->idestadotransferencia==1){
                DB::table('s_productotransferencia')->whereId($id)->update([
                    'idestadotransferencia' => 1,
                    'idestado' => 1,
                ]);
            }elseif($productotransferencia->idestadotransferencia==2){
                DB::table('s_productotransferencia')->whereId($id)->update([
                    'idestadotransferencia' => 1,
                    'idestado' => 1,
                ]);
            }  

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha rechazado correctamente.'
            ]);
        }elseif($request->input('view') == 'confirmar') { 
          
            DB::table('s_productotransferencia')->whereId($id)->update([
                'fechasolicitud' => Carbon::now(),
                'idestado' => 2,
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha confirmado correctamente.'
            ]);
        }elseif($request->input('view') == 'enviar') {
            $rules = [
                'productos' => 'required',
            ];

            $messages = [
                'productos.required' => 'Los "Productos" son Obligatorio.',
            ];
      
            $this->validate($request,$rules,$messages);
      
            $productos = explode('&', $request->input('productos'));
            for($i = 1;$i <  count($productos);$i++){
                $item = explode(',', $productos[$i]);
                if($item[4]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad minímo es 1.'
                    ]);
                    break;
                }elseif($item[2]<0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Unidad de Medida es obligaorio.'
                    ]);
                    break;
                }
            } 
          
            DB::table('s_productotransferencia')->whereId($id)->update([
                'fechaenvio' => $fechaenvio = Carbon::now(),
                'idusersorigen' => Auth::user()->id,
                'idestadotransferencia' => $request->input('idestadotransferencia')
            ]);

            DB::table('s_productotransferenciadetalle')->where('idproductotransferencia', $id)->delete();
            for($i = 1; $i < count($productos); $i++){
                $item = explode(',',$productos[$i]);
                DB::table('s_productotransferenciadetalle')->insert([
                  'motivo' => $item[3],
                  'cantidad' => $item[1],
                  'cantidadenviado' => $item[4],
                  'cantidadrecepcion' => 0,
                  'idunidadmedida' => $item[2],
                  'idproducto' => $item[0],
                  'idproductotransferencia' => $id,
                ]);
            }       

             

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
      if($request->input('view') == 'eliminar') {
          
            DB::table('s_productotransferencia')->whereId($id)->update([
                'fechaeliminado' => Carbon::now(),
                'idestado' => 3,
            ]);

            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }
       
    }
}
