<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class CarritocompraController extends Controller
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
        $s_carritocompra = DB::table('s_carritocompra')
            ->join('users','users.id','s_carritocompra.s_idusuariocliente')
            ->where('users.nombre','LIKE','%'.$request->input('nombre').'%')
            ->select(
                's_carritocompra.*',
                'users.nombre as clientenombre'
            )
            ->orderBy('s_carritocompra.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/carritocompra/index',[
            'tienda' => $tienda,
            's_carritocompra' => $s_carritocompra
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
        $tipopersonas = DB::table('tipopersona')->get();
        return view('layouts/backoffice/tienda/sistema/carritocompra/create',[
            'tienda' => $tienda,
            'tipopersonas' => $tipopersonas,
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
                'idcliente' => 'required',
                'productos' => 'required',
                'idestadoenvio' => 'required',
                'delivery_fecha' => 'required',
                'delivery_hora' => 'required',
                'delivery_pernonanombre' => 'required',
                'delivery_numerocelular' => 'required',
                'delivery_direccion' => 'required',
                'mapa_ubicacion_lat' => 'required',
                'mapa_ubicacion_lng' => 'required',
            ];
            
            $messages = [
              'idcliente.required' => 'El "Cliente" es Obligatorio.',
              'productos.required' => 'Los "Productos" son Obligatorio.',
              'idestadoenvio.required' => 'El "Estado de envio" son Obligatorio.',
              'delivery_fecha.required' => 'La "Fecha" son Obligatorio.',
              'delivery_hora.required' => 'La "Hora" son Obligatorio.',
              'delivery_pernonanombre.required' => 'El "Nombre de persona a entregar" son Obligatorio.',
              'delivery_numerocelular.required' => 'El "Número de celular de entrega" son Obligatorio.',
              'delivery_direccion.required' => 'El "Dirección de entrega" son Obligatorio.',
              'mapa_ubicacion_lat.required' => 'La "Ubicación de entrega" son Obligatorio.',
              'mapa_ubicacion_lng.required' => '',
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
                }elseif($item[3]<0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Descuento minímo es 0.00.'
                    ]);
                    break;
                }
            } 

            // actualizar información de cliente
            DB::table('users')->whereId($request->input('idcliente'))->update([
               'direccion' => $request->input('direccion'),
               'idubigeo' =>  $request->input('idubigeo'),
            ]);
            // fin actualizar información de cliente
          
            // obtener ultimo código
            $s_carritocompra = DB::table('s_carritocompra')
                ->where('s_carritocompra.idtienda',$idtienda)
                ->orderBy('s_carritocompra.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($s_carritocompra!=''){
                $codigo = $s_carritocompra->codigo+1;
            }
            // fin obtener ultimo código
            
            $idcarritocompra = DB::table('s_carritocompra')->insertGetId([
               'fecharegistro' => Carbon::now(),
               'fechaconfirmacion' => Carbon::now(),
               'codigo' => $codigo,
               'descuento' => '0.00',
               'envio_fecha' => $request->input('delivery_fecha'),
               'envio_hora' => $request->input('delivery_hora'),
               'envio_nombre' => $request->input('delivery_pernonanombre'),
               'envio_telefono' => $request->input('delivery_numerocelular'),
               'envio_direccion' => $request->input('delivery_direccion'),
               'mapa_ubicacion_lat' => $request->input('mapa_ubicacion_lat'),
               'mapa_ubicacion_lng' => $request->input('mapa_ubicacion_lng'),
               's_idusuarioresponsable' => Auth::user()->id,
               's_idusuariocliente' => $request->input('idcliente'),
               's_idestadoenvio' => $request->input('idestadoenvio'),
               's_idestado' => 1,
               'idtienda' => $idtienda,
            ]);
            
            $productos = explode('&', $request->input('productos'));
            for($i = 1; $i < count($productos); $i++){
                $item = explode(',',$productos[$i]);
                DB::table('s_carritocompradetalle')->insert([
                  'cantidad' => $item[1],
                  'preciounitario' => $item[2],
                  'descuento' => $item[3],
                  's_idproducto' => $item[0],
                  's_idcarritocompra' => $idcarritocompra,
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
    public function show(Request $request, $idtienda)
    {
       $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $idcarritocompra)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $carritocompra = DB::table('s_carritocompra')
            ->join('users','users.id','s_carritocompra.s_idusuariocliente')
            ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
            ->where('s_carritocompra.id',$idcarritocompra)
            ->select(
                's_carritocompra.*',
                'users.idubigeo as idubigeo',
                'users.identificacion as clienteidentificacion',
                'users.nombre as clientenombre',
                'users.apellidos as clienteapellidos',
                'users.direccion as clientedireccion',
                'ubigeo.nombre as ubigeonombre'
            )
            ->first();     
        if($request->input('view') == 'editar') {
            $s_carritocompradetalles = DB::table('s_carritocompradetalle')
              ->join('s_producto','s_producto.id','s_carritocompradetalle.s_idproducto')
              ->where('s_carritocompradetalle.s_idcarritocompra',$carritocompra->id)
              ->select(
                's_carritocompradetalle.*',
                's_producto.codigo as productocodigo',
                's_producto.nombre as productonombre'
              )
              ->orderBy('s_carritocompradetalle.id','asc')
              ->get();
            
            $tipopersonas = DB::table('tipopersona')->get();
            return view('layouts/backoffice/tienda/sistema/carritocompra/edit',[
                'tienda' => $tienda,
                'carritocompra' => $carritocompra,
                'carritocompradetalles' => $s_carritocompradetalles,
                'tipopersonas' => $tipopersonas,
            ]);
        }elseif($request->input('view') == 'eliminar') {
          
          return view('layouts/backoffice/tienda/sistema/carritocompra/delete',[
            's_carritocompra' => $s_carritocompra,
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
    public function update(Request $request, $idtienda, $s_idcarritocompra)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'editar') {
            $rules = [
                'idcliente' => 'required',
                'productos' => 'required',
                'idestadoenvio' => 'required',
                'delivery_fecha' => 'required',
                'delivery_hora' => 'required',
                'delivery_pernonanombre' => 'required',
                'delivery_numerocelular' => 'required',
                'delivery_direccion' => 'required',
                'mapa_ubicacion_lat' => 'required',
                'mapa_ubicacion_lng' => 'required',
            ];
            
            $messages = [
              'idcliente.required' => 'El "Cliente" es Obligatorio.',
              'productos.required' => 'Los "Productos" son Obligatorio.',
              'idestadoenvio.required' => 'El "Estado de envio" son Obligatorio.',
              'delivery_fecha.required' => 'La "Fecha" son Obligatorio.',
              'delivery_hora.required' => 'La "Hora" son Obligatorio.',
              'delivery_pernonanombre.required' => 'El "Nombre de persona a entregar" son Obligatorio.',
              'delivery_numerocelular.required' => 'El "Número de celular de entrega" son Obligatorio.',
              'delivery_direccion.required' => 'El "Dirección de entrega" son Obligatorio.',
              'mapa_ubicacion_lat.required' => 'La "Ubicación de entrega" son Obligatorio.',
              'mapa_ubicacion_lng.required' => '',
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
                }elseif($item[3]<0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Descuento minímo es 0.00.'
                    ]);
                    break;
                }
            } 

            // actualizar información de cliente
            DB::table('users')->whereId($request->input('idcliente'))->update([
               'direccion' => $request->input('direccion'),
               'idubigeo' =>  $request->input('idubigeo'),
            ]);
            // fin actualizar información de cliente
            
            DB::table('s_carritocompra')->whereId($s_idcarritocompra)->update([
               'descuento' => '0.00',
               'envio_fecha' => $request->input('delivery_fecha'),
               'envio_hora' => $request->input('delivery_hora'),
               'envio_nombre' => $request->input('delivery_pernonanombre'),
               'envio_telefono' => $request->input('delivery_numerocelular'),
               'envio_direccion' => $request->input('delivery_direccion'),
               'mapa_ubicacion_lat' => $request->input('mapa_ubicacion_lat'),
               'mapa_ubicacion_lng' => $request->input('mapa_ubicacion_lng'),
               's_idusuarioresponsable' => Auth::user()->id,
               's_idusuariocliente' => $request->input('idcliente'),
               's_idestadoenvio' => $request->input('idestadoenvio'),
            ]);
            
            DB::table('s_carritocompradetalle')->where('s_idcarritocompra',$s_idcarritocompra)->delete();
            $productos = explode('&', $request->input('productos'));
            for($i = 1; $i < count($productos); $i++){
                $item = explode(',',$productos[$i]);
                DB::table('s_carritocompradetalle')->insert([
                  'cantidad' => $item[1],
                  'preciounitario' => $item[2],
                  'descuento' => $item[3],
                  's_idproducto' => $item[0],
                  's_idcarritocompra' => $s_idcarritocompra,
                ]);
            }       
          
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
    public function destroy(Request $request, $idtienda, $s_idcarritocompra)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
            //
        }
    }
}
