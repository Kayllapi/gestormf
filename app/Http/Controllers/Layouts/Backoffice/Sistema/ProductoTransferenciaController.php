<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class ProductoTransferenciaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
         //   json_productotransferencia($idtienda,Auth::user()->idsucursal);
      
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'index'){
            return view(sistema_view().'/productotransferencia/index',[
                'tienda' => $tienda,
            ]);
        }
        elseif($request->input('view') == 'tabla'){
            $tipomovimientos = DB::table('s_tipomovimiento')->get();
            return view(sistema_view().'/productotransferencia/tabla',[
                'tienda' => $tienda,
                'tipomovimientos' => $tipomovimientos,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

     

        if($request->view == 'registrar') {
            return view(sistema_view().'/productotransferencia/create',[
                'tienda' => $tienda,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
         if($request->input('view') == 'registrar') {
            
            $rules    = [
                's_idtipotransferencia'     => 'required',
                'motivo'                  => 'required',
                's_idtiendaorigen'          => 'required',
                's_idtiendadestino'         => 'required',
                //'productos'               => 'required',
            ];
           
            $messages = [];
            foreach(json_decode($request->input('productos')) as $value){
                $rules = array_merge($rules,[
                    'producto_cantidad'.$value->num       => 'required|numeric|integer|gte:1',
                    'producto_idunidadmedida'.$value->num => 'required',
                ]);
                $messages = array_merge($messages,[
                    'producto_cantidad'.$value->num.'.required'       => 'La "Cantidad" es Obligatorio.',
                    'producto_cantidad'.$value->num.'.numeric'        => 'La "Cantidad" debe ser númerico.',
                    'producto_cantidad'.$value->num.'.integer'        => 'La "Cantidad" debe ser entero.',
                    'producto_cantidad'.$value->num.'.gte'            => 'La "Cantidad" debe ser mayor ó igual 1.',
                    'producto_idunidadmedida'.$value->num.'.required' => 'La "Unidad de Medida" es Obligatorio.',
                ]);
              
                if(configuracion($idtienda,'sistema_estadostock')['valor']==1 && $request->input('s_idtipotransferencia')==2){
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
                's_idtipotransferencia.required'  => 'El "Estado" es Obligatorio.',
                'motivo.required'               => 'El "Motivo" es Obligatorio.',
                's_idtiendaorigen.required'       => 'El campo "De" es Obligatorio.',
                's_idtiendadestino.required'      => 'El campo "Para" es Obligatorio.',
                'productos.required'            => 'Los "Productos" son Obligatorio.',
            ]);
            $this->validate($request,$rules,$messages);  
           
           if(count(json_decode($request->input('productos')))==0){
              return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje'   => 'Ingrese un producto!!.'
              ]);
           }
            
           $productotransferencia = DB::table('s_productotransferencia')
                ->orderBy('s_productotransferencia.codigo','desc')
                ->limit(1)
                ->first();
           
            $codigo = 1;
            if($productotransferencia!=''){
                $codigo = $productotransferencia->codigo+1;
            }
           
            $fechaenvio             = null;
            $idusersolicitud          = 0;
            $idusersenvio         = 0;
            // $s_idestadotransferencia  = 1; 
            if($request->input('s_idtipotransferencia')==1){
                $idusersolicitud = Auth::user()->id;
                $s_idestadotransferencia  = 2;
                $db_idestadotransferencia = 'SOLICITADO';
                $idestadomivimiento  = 1;
                $db_idestadomovimiento = 'PENDIENTE';
            }elseif($request->input('s_idtipotransferencia')==2){
                $fechaenvio = Carbon::now();
                $idusersenvio = Auth::user()->id;
                $s_idestadotransferencia  = 3;
                $db_idestadotransferencia = 'ENVIADO';
                $idestadomivimiento  = 2;
                $db_idestadomovimiento = 'EN PROCESO';
            }

            $usersolicitado = DB::table('users')->whereId($idusersolicitud)->first();
            $db_idusersolicitado = $usersolicitado ? $usersolicitado->nombrecompleto: '';

            $usersenviado = DB::table('users')->whereId($idusersenvio)->first();
            $db_idusersenviado = $usersenviado ? $usersenviado->nombrecompleto : '';

            $usersregistro = DB::table('users')->whereId(Auth::user()->id)->first();
            $db_idusersregistro = $usersregistro->nombrecompleto;
            
            
            

            if($request->input('s_idtiendadestino') == 0){
                $tienda_destino = DB::table('tienda')->whereId($idtienda)->first();
            }else{
                $tienda_destino = DB::table('s_sucursal')->whereId($request->input('s_idtiendadestino'))->first();
            }
            $db_idtiendadestino = $tienda_destino->nombre;

            if($request->input('s_idtiendaorigen') == 0){
                $tienda_origen = DB::table('tienda')->whereId($idtienda)->first();
            }else{
                $tienda_origen = DB::table('s_sucursal')->whereId($request->input('s_idtiendaorigen'))->first();
            }
            $db_idtiendaorigen = $tienda_origen->nombre;
            


            // db_idtiendaorigen
            // db_idtiendadestino

            // db_idusersregistro
            // db_idusersolicitado
            // db_idusersenviado

            // db_idusersrecepcion
            // db_idusersrechazo


            // db_idtipotransferencia
            // db_idestadotransferencia
            // db_idestadomovimiento

            
            
            $idtransferencia = DB::table('s_productotransferencia')->insertGetId([
                'fecharegistro'         => Carbon::now(),
                'fechasolicitado'       => Carbon::now(),
                'fechaenviado'          => $fechaenvio,
                'codigo'                => $codigo,
                'motivo'                => $request->input('motivo')!=''?$request->input('motivo'):'',


                'db_idtiendaorigen'         => $db_idtiendaorigen,
                'db_idtiendadestino'        => $db_idtiendadestino,
                'db_idusersolicitado'       => $db_idusersolicitado,
                'db_idusersenviado'         => $db_idusersenviado,
                'db_idusersregistro'        => $db_idusersregistro,
                'db_idusersrecepcion'       => '',
                'db_idusersrechazo'         => '',

                'db_idtipotransferencia'    => $request->input('s_idtipotransferencia')==1 ? 'SOLICITADO' : 'ENVIADO',
                'db_idestadotransferencia'  => $db_idestadotransferencia,
                'db_idestadomovimiento'     => $db_idestadomovimiento,

                's_idtiendaorigen'        => $request->input('s_idtiendaorigen'),
                's_idtiendadestino'       => $request->input('s_idtiendadestino'),
                's_idusersolicitado'      => $idusersolicitud,
                's_idusersenviado'        => $idusersenvio,
                's_idusersregistro'       => Auth::user()->id,
                's_idtipotransferencia'   => $request->input('s_idtipotransferencia'), // solicitando,
                'idsucursal'            => Auth::user()->idsucursal,
                'idtienda'              => $idtienda,
                's_idestadotransferencia' => $s_idestadotransferencia,
                's_idestadomovimiento'    => $idestadomivimiento, // 1 = PENDIENTE | 2 = EN PROCESO | 3 = CONFIRMADO | 4 = RECHAZADO
                'idestado'              => 1
            ]);

            
     
            foreach(json_decode($request->input('productos')) as $value){
                $cantidadenviado = 0;
                if($request->input('s_idtipotransferencia')==2){
                    $cantidadenviado = $value->producto_cantidad;
                }
                $producto = DB::table('s_producto')->whereId($value->idproducto)->first();
                $unidad_medida = DB::table('s_unidadmedida')->whereId($value->producto_idunidadmedida)->first();
                DB::table('s_productotransferenciadetalle')->insert([
                  'cantidadsolicitado'      => $value->producto_cantidad,
                  'cantidadenviado'         => $cantidadenviado,
                  'cantidadrecepcionado'    => 0,
                  'motivo'                  => '',
                  'por'                     => $value->producto_por,
                  'db_idunidadmedida'       => $unidad_medida->nombre,
                  'db_idproducto'           => $producto->nombre,
                  's_idunidadmedida'          => $value->producto_idunidadmedida,
                  's_idproducto'              => $value->idproducto,
                  's_idproductotransferencia' => $idtransferencia,
                  'idsucursal'              => Auth::user()->idsucursal,
                  'idtienda'                => $idtienda,
                  'idestado'                => 2,
                ]);

              
            }
           
            // json_productotransferencia($idtienda,Auth::user()->idsucursal);
            // json_producto($idtienda,Auth::user()->idsucursal);
           
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        if($id == 'show_table'){
            $productotransferencias = DB::table('s_productotransferencia')
                ->leftJoin('s_sucursal as tienda_origen','tienda_origen.id' ,'s_productotransferencia.s_idtiendaorigen')
                ->leftJoin('s_sucursal as tienda_destino','tienda_destino.id' ,'s_productotransferencia.s_idtiendadestino')
                ->leftJoin('users as user_origen','user_origen.id' ,'s_productotransferencia.s_idusersolicitado')
                ->leftJoin('users as user_destino','user_destino.id' ,'s_productotransferencia.s_idusersenviado')

                ->leftJoin('users as user_recepcion','user_recepcion.id' ,'s_productotransferencia.s_idusersrecepcion')
                ->leftJoin('users as user_rechazo','user_rechazo.id' ,'s_productotransferencia.s_idusersrechazo')
                
                ->where('s_productotransferencia.idtienda',$idtienda)

                ->where('s_productotransferencia.codigo','LIKE','%'.$request['columns'][0]['search']['value'].'%')
                ->where('s_productotransferencia.db_idtipotransferencia','LIKE','%'.$request['columns'][1]['search']['value'].'%')
                ->where('s_productotransferencia.db_idestadotransferencia','LIKE','%'.$request['columns'][2]['search']['value'].'%')
                ->where('s_productotransferencia.db_idtiendaorigen','LIKE','%'.$request['columns'][3]['search']['value'].'%')
                ->where('s_productotransferencia.db_idtiendadestino','LIKE','%'.$request['columns'][4]['search']['value'].'%')
                ->where('s_productotransferencia.fecharegistro','LIKE','%'.$request['columns'][5]['search']['value'].'%')

                //->where('s_productotransferencia.idestado',1)
                ->select(
                's_productotransferencia.*',
                'user_origen.nombre as user_origen_nombre',
                'user_destino.nombre as user_destino_nombre',
                'user_recepcion.nombre as user_recepcion_nombre',
                'user_rechazo.nombre as user_rechazo_nombre',
                'tienda_destino.id as id_tienda_destino',
                'tienda_origen.nombre as tienda_origen_nombre',
                'tienda_destino.nombre as tienda_destino_nombre',
                )
                ->orderBy('s_productotransferencia.id','desc')
                ->paginate($request->length,'*',null,($request->start/$request->length)+1);

            $tabla = [];
            foreach($productotransferencias as $value){
                $opciones = [];
              
                $opcionConfirmar = [
                    'nombre'  => 'Confirmar',
                    'onclick' => '/'.$idtienda.'/productotransferencia/'.$value->id.'/edit?view=confirmar',
                    'icono'   => 'check'
                ];
        
                $opcionEditar = [
                    'nombre'  => 'Editar',
                    'onclick' => '/'.$idtienda.'/productotransferencia/'.$value->id.'/edit?view=editar',
                    'icono'   => 'edit'
                ];
        
                $opcionEliminar = [
                    'nombre'  => 'Eliminar',
                    'onclick' => '/'.$idtienda.'/productotransferencia/'.$value->id.'/edit?view=eliminar',
                    'icono'   => 'trash'
                ];
        
                $opcionRecepcionar = [
                    'nombre'  => 'Recepcionar',
                    'onclick' => '/'.$idtienda.'/productotransferencia/'.$value->id.'/edit?view=editar',
                    'icono'   => 'trash'
                ];
        
                $opcionRechazar = [
                    'nombre'  => 'Rechazar',
                    'onclick' => '/'.$idtienda.'/productotransferencia/'.$value->id.'/edit?view=rechazar',
                    'icono'   => 'times'
                ];
        
                $opcionEnviar = [
                    'nombre'  => 'Enviar',
                    'onclick' => '/'.$idtienda.'/productotransferencia/'.$value->id.'/edit?view=editar',
                    'icono'   => 'paper-plane'
                ];
              
                $opcionDetalle = [
                    'nombre'  => 'Detalle',
                    'onclick' => '/'.$idtienda.'/productotransferencia/'.$value->id.'/edit?view=detalle',
                    'icono'   => 'info-circle'
                ];
                
                if($value->s_idtipotransferencia==1){
                    $tipotransferencia = "Solicitud";
                }
                else if($value->s_idtipotransferencia==2){
                    $tipotransferencia =  "Envio";
                }  
                
                if($value->s_idestadomovimiento==1){
                    $estadomov = "(Pendiente)";
                }
                else if($value->s_idestadomovimiento==2){
                    $estadomov =  "(En Proceso)";
                }  
                else if($value->s_idestadomovimiento==3){
                    $estadomov =  "(Confirmado)";
                }
                else if($value->s_idestadomovimiento==4){
                    $estadomov =  "(Rechazado)";
                }
        
                $estadotransferencia = '';
                if($value->s_idestadotransferencia==1){
                    $estadotransferencia = "En Proceso ".$estadomov;
                }
                else if($value->s_idestadotransferencia==2){
                    $estadotransferencia =  "Solicitado ".$estadomov;
                }   
                else if($value->s_idestadotransferencia==3){
                    $estadotransferencia =  "Enviado ".$estadomov;
                }
                else if($value->s_idestadotransferencia==4){
                    $estadotransferencia =  "Recepcionado ".$estadomov;
                }
                
        
           
                
                $id_tienda_destino = $value->s_idtiendadestino == 0 ? $idtienda : $value->s_idtiendadestino ;
                $id_tienda_origen  = $value->s_idtiendaorigen == 0 ? $idtienda : $value->s_idtiendaorigen ;
        
                $id_actual_sucursal = Auth::user()->idsucursal == 0 ? $idtienda : Auth::user()->idsucursal ;
        
                $personal_origen = '';
                $personal_destino = '';
                
                if( $id_tienda_destino == $id_actual_sucursal ){
                    // TIENDA DESTINO
                    if($value->s_idestadotransferencia==1){
                        // $estadotransferencia = "En Proceso ".$estadomov;
                    }
                    else if($value->s_idestadotransferencia==2){
                        // $estadotransferencia =  "Solicitado ".$estadomov;
                        if($value->s_idestadomovimiento == 1 ){
                            
                            $opciones[] = $opcionEliminar ;
                            $personal_origen = '('.$value->user_origen_nombre.')';
                            
                        }
                        else if($value->s_idestadomovimiento == 4){
                            // $opciones[] = $opcionRecepcionar;
                            $opciones[] = $opcionEditar;
                            $opciones[] = $opcionEliminar ;
                            $personal_origen = '('.$value->user_origen_nombre.')';
                            $personal_destino = '('.$value->user_rechazo_nombre.')';
                            
                        }
                    }   
                    else if($value->s_idestadotransferencia==3){
                        // $estadotransferencia =  "Enviado ".$estadomov;
                        if($value->s_idestadomovimiento == 1){
                            // $opciones[] = $opcionEditar;
                        }else if($value->s_idestadomovimiento == 2){
                            $opciones[] = $opcionRecepcionar;
                            $opciones[] = $opcionRechazar;
        
                            if($value->s_idusersolicitado == 0){
                                $personal_origen = '('.$value->user_destino_nombre.')';
                            }else{
                                $personal_origen = '('.$value->user_origen_nombre.')';
                                $personal_destino = '('.$value->user_destino_nombre.')';
                            }
        
                            
                        }
                        else if($value->s_idestadomovimiento == 4){
                            $personal_origen = '('.$value->user_rechazo_nombre.')';
                            $personal_destino = '('.$value->user_destino_nombre.')';
                        }
                    }
                    else if($value->s_idestadotransferencia==4){
                        // $estadotransferencia =  "Recepcionado ".$estadomov;
                        $personal_origen = '('.$value->user_recepcion_nombre.')';
                        $personal_destino = '('.$value->user_destino_nombre.')';
                    }
                    
                }
                else if( $id_tienda_origen == $id_actual_sucursal ){
                    // TIENDA DE ORIGEN
                    if( $value->s_idestadotransferencia==2 ){
        
                        if($value->s_idestadomovimiento == 1){
                            $opciones[] = $opcionEnviar;
                            $opciones[] = $opcionRechazar;
                            $personal_origen = '('.$value->user_origen_nombre.')';
                        }
                        else if($value->s_idestadomovimiento == 4){
                            $personal_origen = '('.$value->user_origen_nombre.')';
                            $personal_destino = '('.$value->user_rechazo_nombre.')';
                        }
                    }
                    else if($value->s_idestadotransferencia==3){
                        if($value->s_idestadomovimiento == 1 ){
                            $opciones[] = $opcionEditar;
                            $personal_origen = '('.$value->user_destino_nombre.')';
                        }
                        else if($value->s_idestadomovimiento == 2 ){
                            if($value->s_idusersolicitado == 0){
                                $personal_origen = '('.$value->user_destino_nombre.')';
                            }else{
                                $personal_origen = '('.$value->user_origen_nombre.')';
                                $personal_destino = '('.$value->user_destino_nombre.')';
                            }
                            
                        }
                        else if( $value->s_idestadomovimiento == 4 ){
                            $opciones[] = $opcionEnviar;
        
                            if($value->s_idusersolicitado == 0){
                                $personal_origen = '('.$value->user_destino_nombre.')';
                                $personal_destino = '('.$value->user_rechazo_nombre.')';
                            }
                        }
                    }
                    else if($value->s_idestadotransferencia==4){
                        // $estadotransferencia =  "Recepcionado ".$estadomov;
                        $personal_origen = '('.$value->user_recepcion_nombre.')';
                        $personal_destino = '('.$value->user_destino_nombre.')';
                    }
                    
                }
                $opciones[] = $opcionDetalle;
                
                
                $tiendaorigen = $value->tienda_origen_nombre;
                if($value->s_idtiendaorigen==0){
                    $tienda = DB::table('tienda')->where('tienda.id',$idtienda)->first();
                    $tiendaorigen = $tienda->nombre;
                }
              
                $tiendadestino = $value->tienda_destino_nombre;
                if($value->s_idtiendadestino==0){
                    $tienda = DB::table('tienda')->where('tienda.id',$idtienda)->first();
                    $tiendadestino = $tienda->nombre;
                }
              
                // $text_tienda_origen = ( $value->idusersolicitado != 0 ? $tiendaorigen.' ('.$value->user_origen_nombre.')' : $tiendaorigen );
                // $text_tienda_destino = ( $value->idusersenviado!=0 ? $tiendadestino.' ('.$value->user_destino_nombre.')' : $tiendadestino );
                $text_tienda_origen = $tiendaorigen.' '.$personal_origen;
                $text_tienda_destino = $tiendadestino.' '.$personal_destino;
            
                
                $tabla[] = [
                    'id'                  => $value->id,
                    'codigo'              => str_pad($value->codigo, 8, "0", STR_PAD_LEFT),
                    'fechasolicitado'     => date_format(date_create($value->fecharegistro),"d/m/Y h:i A"),
                    'fechaenviado'        => date_format(date_create($value->fechaenviado),"d/m/Y h:i A"),
                    'fechaenviado'        => date_format(date_create($value->fechaenviado),"d/m/Y h:i A"),
                    'fecharecepcionado'   => date_format(date_create($value->fecharecepcionado),"d/m/Y h:i A"),
                    // 'tipotransferencia'   => $tipotransferencia." || O:".$id_tienda_origen.' D: '.$id_tienda_destino.' ACTUAL: '.$idsucursal,
                    'tipotransferencia'   => $value->db_idtipotransferencia,
                    // 'tipotransferencia'   => $tipotransferencia,
                    'tiendaorigen'        => $text_tienda_origen,
                    'tiendadestino'       => $text_tienda_destino,
                    // 'estadotransferencia' => $estadotransferencia,
                    'estadotransferencia' => $value->db_idestadotransferencia.'('.$value->db_idestadomovimiento.')',
                    'opcion'              => $opciones
                ];
        
            }
            

            return response()->json([
                'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $productotransferencias->total(),
                'data'            => $tabla,
            ]);  
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $idsucursal = Auth::user()->idsucursal;
      
        $s_productotransferencia = DB::table('s_productotransferencia')
            ->where('s_productotransferencia.id',$id)
            ->select(
                's_productotransferencia.*'
            )
            ->first();
      
        $s_detalletransferencia = DB::table('s_productotransferenciadetalle')
            ->join('s_producto','s_producto.id','s_productotransferenciadetalle.s_idproducto')
            ->join('s_unidadmedida','s_unidadmedida.id','s_productotransferenciadetalle.s_idunidadmedida')
            // ->leftJoin('s_productostock', function($leftJoin) use ($idtienda,$idsucursal){
            //     $leftJoin->on('s_productostock.s_idproducto','s_producto.id')
            //         ->where('s_productostock.idtienda',$idtienda)
            //         ->where('s_productostock.idsucursal',$idsucursal);
            // })
            ->where('s_productotransferenciadetalle.s_idproductotransferencia', $s_productotransferencia->id)
            ->select(
              's_productotransferenciadetalle.*',
              's_producto.codigo as productocodigo',
              's_producto.nombre as productonombre',
              's_producto.db_stock as productostock',
            //   's_producto.precioalpublico as productoprecioalpublico',
              's_unidadmedida.nombre as productounidadmedidanombre',
              's_unidadmedida.codigo as productounidadmedidacodigo',
              's_unidadmedida.id as productounidadmedidaid',
               
            //    's_productostock.cantidad as stock',
            )
            ->orderBy('s_productotransferenciadetalle.id','asc')
            ->get();
    
        if($request->input('view') == 'editar') {
            return view(sistema_view().'/productotransferencia/edit',[
              'tienda' => $tienda,
              's_productotransferencia' => $s_productotransferencia,
              's_detalletransferencia' => $s_detalletransferencia,
            ]);
        }
        elseif($request->input('view') == 'eliminar') {
            return view(sistema_view().'/productotransferencia/delete',[
              'tienda' => $tienda,
              's_productotransferencia' => $s_productotransferencia,
              's_detalletransferencia' => $s_detalletransferencia,
            ]);
        }
        elseif ($request->input('view') == 'confirmar') { 
            
            return view(sistema_view().'/productotransferencia/confirmar', [
              'tienda' => $tienda,
              's_productotransferencia' => $s_productotransferencia,
              's_detalletransferencia' => $s_detalletransferencia
            ]);
        }
        elseif ($request->input('view') == 'rechazar') {
            
            return view(sistema_view().'/productotransferencia/rechazar', [
              'tienda' => $tienda,
              's_productotransferencia' => $s_productotransferencia,
              's_detalletransferencia' => $s_detalletransferencia
            ]);
        }
        elseif ($request->input('view') == 'detalle') { 
            
            return view(sistema_view().'/productotransferencia/detalle', [
              'tienda' => $tienda,
              's_productotransferencia' => $s_productotransferencia,
              's_detalletransferencia' => $s_detalletransferencia
            ]);
        }
    }

    public function update(Request $request, $idtienda, $idtransferencia)
    {
        $transferencia = DB::table('s_productotransferencia')->whereId($idtransferencia);
      
        if($request->input('view') == 'editar' || $request->input('view') == 'correccionsolicitud') {
            if($request->input('view') == 'correccionsolicitud'){
                DB::table('s_productotransferencia')->whereId($idtransferencia)->update([

                    'db_idestadotransferencia'  => 'SOLICITADO',
                    'db_idestadomovimiento'     => 'PENDIENTE',
                    's_idestadotransferencia'   => 2,
                    's_idestadomovimiento'      => 1
                    
                ]);
            }
            $productos = json_decode($request->productos);
            DB::table('s_productotransferenciadetalle')->where('s_idproductotransferencia', $idtransferencia)->delete();
            foreach ($productos as $producto) {
                $prod = DB::table('s_producto')->whereId($producto->idproducto)->first();
                $unidad_medida = DB::table('s_unidadmedida')->whereId($producto->producto_idunidadmedida)->first();

                DB::table('s_productotransferenciadetalle')->insert([
                  'cantidadsolicitado'          => $producto->producto_cantidad,
                  'cantidadenviado'             => 0,
                  'cantidadrecepcionado'        => 0,
                  'motivo'                      => '',
                  'por'                         => $producto->producto_por,
                  'db_idunidadmedida'           => $unidad_medida->nombre,
                  'db_idproducto'               => $prod->nombre,
                  's_idunidadmedida'            => $producto->producto_idunidadmedida,
                  's_idproductotransferencia'   => $idtransferencia,
                  's_idproducto'                => $producto->idproducto,
                  'idsucursal'                  => Auth::user()->idsucursal,
                  'idtienda'                    => $idtienda,
                  'idestado'                    => 2,
                ]);
            }       

            
            
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'confirmar') { 
          
            $productotransferencias = DB::table('s_productotransferenciadetalle')->where('s_idproductotransferencia',$idtransferencia)->get();
            
            if($request->input('s_idestadotransferencia')==2){
                foreach($productotransferencias as $value){
                    $stock = stock_producto(usersmaster()->idtienda,$value->idproducto)['total'];
                    if($value->cantidad>$stock){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay suficiente stock, ingrese otra cantidad.'
                        ]);
                        break;
                    }
                }
            }
            
            DB::table('s_productotransferencia')->whereId($idtransferencia)->update([
                'fechasolicitud' => Carbon::now(),
                'idestado' => 2,
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha confirmado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'rechazar') { 
            $usersrechazo = DB::table('users')->whereId(Auth::user()->id)->first();
            $db_idusersrechazo = $usersrechazo->nombrecompleto;

            $productotransferencia = DB::table('s_productotransferencia')->whereId($idtransferencia)->first();
            DB::table('s_productotransferencia')->whereId($idtransferencia)->update([
                'fecharechazo'              => Carbon::now(),
                'db_idusersrechazo'         => $db_idusersrechazo,
                's_idusersrechazo'          => Auth::user()->id,
                'db_idestadomovimiento'     => 'RECHAZADO',
                's_idestadomovimiento'      => 4
            ]);
           
            
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha rechazado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'responder') {
            $rules = [
                'productos' => 'required',
            ];

            $messages = [
                'productos.required' => 'Los "Productos" son Obligatorio.',
            ];
      
            $this->validate($request,$rules,$messages);
            
            $productos = json_decode($request->productos);
       
            foreach ($productos as $producto) {
                if($producto->producto_enviar <= 0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad minímo es 1.'
                    ]);
                    break;
                }elseif(!isset($producto->producto_idunidadmedida)){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Unidad de Medida es obligaorio.'
                    ]);
                    break;
                }
            }
            $usersenviado = DB::table('users')->whereId(Auth::user()->id)->first();
            $db_idusersenviado = $usersenviado->nombrecompleto;

            DB::table('s_productotransferencia')->whereId($idtransferencia)->update([
                'fechaenviado'              => Carbon::now(),
                's_idusersenviado'          => Auth::user()->id,
                'db_idusersenviado'         => $db_idusersenviado,
                'db_idestadotransferencia'  => 'ENVIADO',
                'db_idestadomovimiento'     => 'EN PROCESO',
                's_idestadotransferencia'   => 3, 
                's_idestadomovimiento'      => 2
            ]);

            DB::table('s_productotransferenciadetalle')->where('s_idproductotransferencia', $idtransferencia)->delete();
            foreach ($productos as $producto) {
                $prod = DB::table('s_producto')->whereId($producto->idproducto)->first();
                $unidad_medida = DB::table('s_unidadmedida')->whereId($producto->producto_idunidadmedida)->first();

                DB::table('s_productotransferenciadetalle')->insert([
                  'cantidadsolicitado'          => $producto->producto_cantidad,
                  'cantidadenviado'             => $producto->producto_enviar,
                  'cantidadrecepcionado'        => 0,
                  'motivo'                      => '',
                  'por'                         => $producto->producto_por,
                  'db_idunidadmedida'           => $unidad_medida->nombre,
                  'db_idproducto'               => $prod->nombre,
                  's_idunidadmedida'            => $producto->producto_idunidadmedida,
                  's_idproductotransferencia'   => $idtransferencia,
                  's_idproducto'                => $producto->idproducto,
                  'idsucursal'                  => Auth::user()->idsucursal,
                  'idtienda'                    => $idtienda,
                  'idestado'                    => 2,
                ]);
            }       

            
            
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'recepcionar') {

            $usersrecepcion = DB::table('users')->whereId(Auth::user()->id)->first();
            $db_idusersrecepcion = $usersrecepcion->nombrecompleto;

            DB::table('s_productotransferencia')->whereId($idtransferencia)->update([
                's_idestadomovimiento'      => 3, // 1 = PENDIENTE | 2 = EN PROCESO | 3 = CONFIRMADO | 4 = RECHAZADO
                'db_idestadomovimiento'     => 'CONFIRMADO',
                'db_idestadotransferencia'  => 'RECEPCIONADO',
                's_idusersrecepcion'        => Auth::user()->id, 
                'db_idusersrecepcion'       => $db_idusersrecepcion,
                's_idestadotransferencia'   => 4
            ]);
          
            $productos = json_decode($request->productos);
            DB::table('s_productotransferenciadetalle')->where('s_idproductotransferencia', $idtransferencia)->delete();
            foreach ($productos as $producto) {
                $prod = DB::table('s_producto')->whereId($producto->idproducto)->first();
                $unidad_medida = DB::table('s_unidadmedida')->whereId($producto->producto_idunidadmedida)->first();

                DB::table('s_productotransferenciadetalle')->insert([
                  'cantidadsolicitado'          => $producto->producto_cantidad,
                  'cantidadenviado'             => $producto->producto_enviar,
                  'cantidadrecepcionado'        => $producto->producto_recepcionar,
                  'motivo'                      => '',
                  'por'                         => $producto->producto_por,
                  'db_idunidadmedida'           => $unidad_medida->nombre,
                  'db_idproducto'               => $prod->nombre,
                  's_idunidadmedida'            => $producto->producto_idunidadmedida,
                  's_idproductotransferencia'   => $idtransferencia,
                  's_idproducto'                => $producto->idproducto,
                  'idsucursal'                  => Auth::user()->idsucursal,
                  'idtienda'                    => $idtienda,
                  'idestado'                    => 2,
                ]);
            }  
            // Fin Actualizar Stock
            

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha enviado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'enviar') {
            $rules = [
                'productos' => 'required',
            ];

            $messages = [
                'productos.required' => 'Los "Productos" son Obligatorio.',
            ];
      
            $this->validate($request,$rules,$messages);
            
            $productos = json_decode($request->productos);
       
            foreach ($productos as $producto) {
                //$stock = stock_producto(usersmaster()->idtienda, $producto->idproducto)['total'];
                if($producto->producto_enviar <= 0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad minímo es 1.'
                    ]);
                    break;
                }elseif(!isset($producto->producto_idunidadmedida)){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Unidad de Medida es obligaorio.'
                    ]);
                    break;
                }elseif($producto->producto_enviar > $producto->producto_cantidad){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'No hay suficiente stock, ingrese otra cantidad.'
                    ]);
                    break;
                } 
            }
          
            DB::table('s_productotransferencia')->whereId($idtransferencia)->update([
                'fechaenviado'          => Carbon::now(),
                'db_idestadomovimiento' => 'EN PROCESO',
                's_idestadomovimiento'  => 2, // 1 = PENDIENTE | 2 = EN PROCESO | 3 = CONFIRMADO
            ]);

            DB::table('s_productotransferenciadetalle')->where('s_idproductotransferencia', $idtransferencia)->delete();
            foreach ($productos as $producto) {
                $producto = DB::table('s_producto')->whereId($value->idproducto)->first();
                $unidad_medida = DB::table('s_unidadmedida')->whereId($value->producto_idunidadmedida)->first();

                DB::table('s_productotransferenciadetalle')->insert([
                  'cantidadsolicitado'      => $producto->producto_cantidad,
                  'cantidadenviado'         => $producto->producto_enviar,
                  'cantidadrecepcionado'    => 0,
                  'motivo'                  => '',
                  'por'                     => $producto->producto_por,
                  'db_idunidadmedida'       => $unidad_medida->nombre,
                  'db_idproducto'           => $producto->nombre,
                  's_idunidadmedida'          => $producto->producto_idunidadmedida,
                  's_idproductotransferencia' => $idtransferencia,
                  's_idproducto'              => $producto->idproducto,
                  'idsucursal'              => Auth::user()->idsucursal,
                  'idtienda'                => $idtienda,
                  'idestado'                => 2,
                ]);
            }       

            // Fin Actualizar Stock
            
            
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'delete'){
            DB::table('s_productotransferenciadetalle')->where('s_idproductotransferencia', $idtransferencia)->delete();
            DB::table('s_productotransferencia')->whereId($idtransferencia)->delete();
            
            
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminno correctamente.'
            ]);
        }
    }


    public function destroy(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
              
            /*// ACTUALIZAR SALDO
            $s_productotransferencia = DB::table('s_productotransferencia')->whereId($id)->first();
            sistema_productosaldo_actualizar(
                $idtienda,
                $s_productotransferencia->s_idproducto,
                'ELIMINAR'
            );
            // FIN ACTUALIZAR SALDO
          
            DB::table('s_productotransferencia')
                ->where('idtienda',$idtienda)
                ->where('id',$id)
                ->delete();
  
            json_productotransferencia($idtienda); 
            json_producto($idtienda);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);*/
        }
    }
}
