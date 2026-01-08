<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class ProductoMovimientoController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'index'){
            return view(sistema_view().'/productomovimiento/index',[
                'tienda' => $tienda,
            ]);
        }
        elseif($request->input('view') == 'tabla'){
            return view(sistema_view().'/productomovimiento/tabla',[
                'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->view == 'registrar') {
            return view(sistema_view().'/productomovimiento/create',[
                'tienda' => $tienda,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
          
            /* =================================================  VALIDAR */
            $rules = [
                'idtipoproductomovimiento'  => 'required',
                'motivo'                    => 'required',
                'productos'                 => 'required',
            ];
            
            $messages = [];
            foreach(json_decode($request->input('productos')) as $value){
                $rules = array_merge($rules,[
                    'producto_cantidad'.$value->num  => 'required|numeric|gte:0',
                ]);
                $messages = array_merge($messages,[
                    'producto_cantidad'.$value->num.'.required' => 'La "Cantidad" es Obligatorio.',
                    'producto_cantidad'.$value->num.'.numeric'  => 'La "Cantidad", debe ser númerico.',
                    'producto_cantidad'.$value->num.'.gte'      => 'La "Cantidad", debe ser mayor a 0.',
                ]);
              
                if(configuracion($idtienda,'sistema_estadostock')['valor']==1 && $request->input('idtipoproductomovimiento')==2){
                    $stockproducto = sistema_productosaldo([
                        'idtienda'    => $idtienda,
                        'idsucursal'  => Auth::user()->idsucursal,
                        'idproducto'  => $value->idproducto,
                    ])['stock'];
                    if($stockproducto<$value->producto_cantidad){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El Producto no cuenta con stock suficiente, ingrese otro producto!!.'
                        ]);
                        break;
                    }
                }
            } 
            
            $messages = array_merge($messages,[
                'idtipoproductomovimiento.required' => 'El "Tipo de Movimiento" es Obligatorio.',
                'motivo.required'                   => 'El "Motivo" es Obligatorio.',
                'productos.required'                => 'Los "Productos" son Obligatorio.',
            ]);
          
            $this->validate($request,$rules,$messages);
          
            foreach(json_decode($request->input('productos')) as $value){
          
                /* =================================================  UNIDAD DE MEDIDA */
                $idproducto = $value->idproducto;
                $db_idproducto = '';
                if($idproducto!=0){
                    $s_producto = DB::table('s_producto')->whereId($idproducto)->first();
                    $db_idproducto = $s_producto->nombre;
                }

                /* =================================================  RESPONSABLE */
                $idusuarioresponsable = Auth::user()->id;
                $db_idusuarioresponsable = '';
                if($idusuarioresponsable!=0){
                    $s_users = DB::table('users')->whereId($idusuarioresponsable)->first();
                    $db_idusuarioresponsable = $s_users->nombrecompleto;
                }
              
                /* =================================================  PRODUCTO MOVIMIENTO */
                $idtipoproductomovimiento = $request->idtipoproductomovimiento;
                $db_idtipoproductomovimiento = '';
                if($idtipoproductomovimiento!=0){
                    $s_tipoproductomovimiento = DB::table('s_tipoproductomovimiento')->whereId($idtipoproductomovimiento)->first();
                    $db_idtipoproductomovimiento = $s_tipoproductomovimiento->nombre;
                }
              
                /* =================================================  UNIDAD DE MEDIDA */
                $idunidadmedida = $value->producto_idunidadmedida;
                $db_idunidadmedida = '';
                if($idunidadmedida!=0){
                    $s_unidadmedida = DB::table('s_unidadmedida')->whereId($idunidadmedida)->first();
                    $db_idunidadmedida = $s_unidadmedida->nombre.' x '.$value->producto_por;
                }
                
                /* =================================================  INSERTAR */
                $idproductomovimiento = DB::table('s_productomovimiento')->insertGetId([
                   'fecharegistro'                => Carbon::now(),
                   'motivo'                       => $request->motivo,
                   'cantidad'                     => $value->producto_cantidad,
                   'por'                          => $value->producto_por,
                   'cantidadunidad'               => $value->producto_cantidad*$value->producto_por,
                   'db_idproducto'                => $db_idproducto,
                   'db_idusuarioresponsable'      => $db_idusuarioresponsable,
                   'db_idtipoproductomovimiento'  => $db_idtipoproductomovimiento,
                   'db_idunidadmedida'            => $db_idunidadmedida,
                   's_idproducto'                 => $idproducto,
                   's_idusuarioresponsable'       => $idusuarioresponsable,
                   's_idtipoproductomovimiento'   => $idtipoproductomovimiento,
                   's_idunidadmedida'             => $value->producto_idunidadmedida,
                   'idsucursal'                   => Auth::user()->idsucursal,
                   'idtienda'                     => $idtienda,
                   'idestado'                     => 1,
                ]); 
                
                /* =================================================  ACTUALIZAR INVENTARIO */
                sistema_inventario([
                    'idtienda'      => $idtienda,
                    'idsucursal'    => Auth::user()->idsucursal,
                    'idproducto'    => $idproducto,
                    'responsable'   => $db_idusuarioresponsable,
                    'tipo'          => $db_idtipoproductomovimiento,
                    'referencia'    => 'PRODUCTO MOVIMIENTO',
                    'concepto'      => $db_idproducto.' - '.$db_idunidadmedida,
                    'cantidad'      => $value->producto_cantidad,
                    'por'           => $value->producto_por,
                    'precio'        => 0,
                    'total'         => 0,
                ]);

            }
          
            json_producto($idtienda);
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        if($id == 'show_table'){
            $productomovimientos  = DB::table('s_productomovimiento')
                ->where('s_productomovimiento.idtienda',$idtienda)
                ->where('s_productomovimiento.idsucursal',Auth::user()->idsucursal)
                ->where('s_productomovimiento.db_idtipoproductomovimiento','LIKE','%'.$request['columns'][0]['search']['value'].'%')
                ->where('s_productomovimiento.motivo','LIKE','%'.$request['columns'][1]['search']['value'].'%')
                ->where('s_productomovimiento.db_idproducto','LIKE','%'.$request['columns'][2]['search']['value'].'%')
                ->where('s_productomovimiento.db_idunidadmedida','LIKE','%'.$request['columns'][3]['search']['value'].'%')
                ->where('s_productomovimiento.idestado',1)
                ->orderBy('s_productomovimiento.id','desc')
                ->paginate($request->length,'*',null,($request->start/$request->length)+1);

            $tabla = [];
            foreach($productomovimientos as $value){
                $tabla[] = [
                    'id' => $value->id,
                    'fecharegistro' => date_format(date_create($value->fecharegistro),"d/m/Y h:i A"),
                    'motivo' => $value->motivo,
                    'cantidad' => $value->cantidad,
                    'producto' => $value->db_idproducto,
                    'unidadmedida' => $value->db_idunidadmedida,
                    'tipoproductomovimiento' => $value->db_idtipoproductomovimiento,
                ];

            }

            return response()->json([
                'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $productomovimientos->total(),
                'data'            => $tabla,
            ]);  
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $s_productomovimiento = DB::table('s_productomovimiento')
            ->join('s_producto','s_producto.id','s_productomovimiento.s_idproducto')
            ->join('unidadmedida','unidadmedida.id','s_productomovimiento.idunidadmedida')
            ->join('s_tipomovimiento','s_tipomovimiento.id','s_productomovimiento.s_idtipomovimiento')
            ->where('s_productomovimiento.id',$id)
            ->select(
                's_productomovimiento.*',
                's_producto.codigo as producto_codigo',
                's_producto.nombre as producto_nombre',
                'unidadmedida.nombre as unidadmedida_nombre',
                's_tipomovimiento.nombre as tipomovimiento_nombre',
            )
            ->first();
      
        if($request->input('view') == 'editar') {
            return view(sistema_view().'/productomovimiento/edit',[
              'tienda' => $tienda,
              's_productomovimiento' => $s_productomovimiento,
            ]);
        }
        elseif($request->input('view') == 'eliminar') {
            return view(sistema_view().'/productomovimiento/delete',[
              'tienda' => $tienda,
              's_productomovimiento' => $s_productomovimiento,
            ]);
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        //  
    }


    public function destroy(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
              
            /*// ACTUALIZAR SALDO
            $s_productomovimiento = DB::table('s_productomovimiento')->whereId($id)->first();
            sistema_productosaldo_actualizar(
                $idtienda,
                $s_productomovimiento->s_idproducto,
                'ELIMINAR'
            );
            // FIN ACTUALIZAR SALDO
          
            DB::table('s_productomovimiento')
                ->where('idtienda',$idtienda)
                ->where('id',$id)
                ->delete();
  
            json_producto($idtienda);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);*/
        }
    }
}
