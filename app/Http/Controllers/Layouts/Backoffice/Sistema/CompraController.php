<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class CompraController extends Controller
{
    public function index(Request $request,$idtienda)
    {
       json_compra($idtienda, Auth::user()->idsucursal, Auth::user()->id);
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/compra/tabla',[
                'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        return view(sistema_view().'/compra/create',[
            'tienda' => $tienda,
        ]);
    }
  
    public function store(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            $rules = [
              'idproveedor' => 'required',
              'idcomprobante' => 'required',
              'seriecorrelativo' => 'required',
              'fechaemision' => 'required',
              'idmoneda' => 'required',
              'idestado' => 'required',
              'productos' => 'required',
            ];
            $messages = [
              'idproveedor.required' => 'El "Cliente" es Obligatorio.',
              'idcomprobante.required' => 'El "Comprobante" es Obligatorio.',
              'seriecorrelativo.required' => 'La "Serie - Correlativo" es Obligatorio.',
              'fechaemision.required' => 'La "Fecha emisión" es Obligatorio.',
              'idmoneda.required' => 'La "Moneda" es Obligatorio.',
              'idestado.required' => 'El "Estado" es Obligatorio.',
              'productos.required' => 'Los "Productos" son Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            $productos = explode('&', $request->input('productos'));
            for($i = 1;$i <  count($productos);$i++){
                $item = explode(',', $productos[$i]);
                $producto = DB::table('s_producto')->whereId($item[0])->first();
                if($item[1]<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad minímo es 1 del producto "'.$producto->nombre.'".'
                    ]);
                    break;
                }elseif($item[2]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Es Obligatorio el Precio del producto "'.$producto->nombre.'".'
                    ]);
                    break;
                }elseif($item[2]<0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Precio minímo es 0.00 del producto "'.$producto->nombre.'".'
                    ]);
                    break;
                }
                /*if($item[3]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Es Obligatorio la Fecha de vencimiento del producto "'.$producto->nombre.'".'
                    ]);
                    break;
                }*/
                /*$list = explode('-',$item[3]);
                if(strlen($list[0])>4){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Año de la Fecha de Vencimiento es incorreto del producto "'.$producto->nombre.'".'
                    ]);
                    break;
                }*/
                if($item[5] < 0){
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'El Precio Nuevo debe ser mayor que 0 (cero) ',
                  ]);
                  break;
                }
            } 
            

            $idaperturacierre = 0;
            if($request->input('idestado')==2){
                // aperturacaja
                
                $apertura = sistema_apertura([
                    'idtienda'          => $idtienda,
                    'idsucursal'        => Auth::user()->idsucursal,
                    'idusersrecepcion'  => Auth::user()->id,
                ]);
                
                if($apertura['resultado']!='ABIERTO'){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Caja debe estar Aperturada.'
                    ]);
                }
                $idaperturacierre = $apertura['idapertura'];
              
                
                
                $efectivo_soles = sistema_efectivo([
                    'idtienda'    => $idtienda,
                    'idsucursal'  => Auth::user()->idsucursal,
                    'idapertura'  => $idaperturacierre,
                    'idmoneda'    => $request->input('idmoneda'),
                ]);
                if($request->input('total')>$efectivo_soles['total']){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'No hay suficiente saldo en caja!.'
                    ]);
                }
            }

            $s_compra = DB::table('s_compra')
                ->where('s_compra.idtienda',$idtienda)
                ->orderBy('s_compra.codigo','desc')
                ->limit(1)
                ->first();
            // json_compra($idtienda, Auth::user()->idsucursal, Auth::user()->id);
          
            $codigo = 1;
            if($s_compra!=''){
                $codigo = $s_compra->codigo+1;
            }
          
            
            $moneda = DB::table('s_moneda')->whereId($request->input('idmoneda'))->first();
            $responsable = DB::table('users')->whereId(Auth::user()->id)->first();
            $proveeedor = DB::table('users')
                            ->leftJoin('s_ubigeo','s_ubigeo.id','users.idubigeo')
                            ->where('users.id',$request->input('idproveedor'))
                            ->select('users.nombrecompleto','users.identificacion','users.direccion','s_ubigeo.nombre as nombreubigeo')
                            ->first();
            $comprobante = DB::table('s_tipocomprobante')->whereId($request->input('idcomprobante'))->first();
            
            $idcompra = DB::table('s_compra')->insertGetId([
               'codigo' => $codigo,
               'fecharegistro' => Carbon::now(),
               'fechaconfirmacion' => Carbon::now(),
               'seriecorrelativo' => $request->input('seriecorrelativo'),
               'fechaemision' => $request->input('fechaemision'),
               'total' => $request->input('total'),
               'totalredondeado' => $request->input('totalredondeado'),
               'db_idmoneda' =>  $moneda->nombre,
               'db_idusuarioresponsable' => $responsable->nombrecompleto,
               'db_idusuarioproveedor' => $proveeedor->nombrecompleto,
               'db_ubigeoproveedor'         => $proveeedor->nombreubigeo ? $proveeedor->nombreubigeo : '',
               'db_identificacionproveedor' => $proveeedor->identificacion,
               'db_direccionproveedor'      => $proveeedor->direccion,
               'db_idestado' => $request->input('idestado') == 1 ? 'PENDIENTE' : 'COMPRADO',
               'db_idcomprobante' => $comprobante->nombre,
               's_idmoneda' => $request->input('idmoneda'),
               's_idaperturacierre' => $idaperturacierre,
               's_idusuarioresponsable' => Auth::user()->id,
               's_idusuarioproveedor' => $request->input('idproveedor'),
               's_idcomprobante' =>  $request->input('idcomprobante'),
               's_idestado' => $request->input('idestado'),
               'idtienda' => $idtienda,
            ]);
            
            $productos = explode('&', $request->input('productos'));
            for($i = 1; $i < count($productos); $i++){
                $item = explode(',',$productos[$i]);
                $producto = DB::table('s_producto')->whereId($item[0])->first();

                $unidad_medida = DB::table('s_unidadmedida')->whereId($item[6])->first();
                $idcompradetalle = DB::table('s_compradetalle')->insertGetId([
                  'concepto' => $producto->nombre,
                  'cantidad' => $item[1],
                  'preciounitario' => $item[2],
                  'preciototal' => $item[4],
                  'por' => $producto->por,
                  'idunidadmedida' => $item[6],
                  'fechavencimiento' => $item[3]!=''?$item[3]:Carbon::now(),
                  'db_idunidadmedida' => $unidad_medida->nombre,
                  's_idproducto' => $item[0],
                  's_idcompra' => $idcompra,
                  'idtienda' => $idtienda,
                  'idestado' => 1,
                ]);

                if($request->input('idestado')==2){
                    // SALDO
                  
                    // productosaldo_actualizar(
                    //     $idtienda,
                    //     $producto->id,
                    //     'COMPRA',
                    //     $item[1],
                    //     $producto->por,
                    //     $producto->idunidadmedida,
                    //     $idcompradetalle
                    // );
                  
                    // inicio actualizando precio del producto
                    // DB::table('s_producto')->whereId($item[0])->update([
                    //   'precioalpublico' => $item[5],
                    //   'fechavencimiento' => $item[3]!=''?$item[3]:Carbon::now(),
                    // ]);
                    // fin actualizando precio del producto
                }
                
                    
            }          
          
            // json_compra($idtienda, Auth::user()->idsucursal, Auth::user()->id);
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif ($request->input('view') == 'registrarcliente') {
            if($request->input('cliente_idtipopersona')==1){
                $rules = [
                    'cliente_dni' => 'required|numeric|digits:8',
                    'cliente_nombre' => 'required',
                    'cliente_apellidos' => 'required',
                    'cliente_idubigeo' => 'required',
                    'cliente_direccion' => 'required'
                ];
                $identificacion = $request->input('cliente_dni');
                $nombre = $request->input('cliente_nombre');
                $apellidos = $request->input('cliente_apellidos');
            }else{
                $rules = [
                    'cliente_ruc' => 'required|numeric|digits:11',
                    'cliente_nombrecomercial' => 'required',
                    'cliente_razonsocial' => 'required',
                    'cliente_idubigeo' => 'required',
                    'cliente_direccion' => 'required'
                ];
                $identificacion = $request->input('cliente_ruc');
                $nombre = $request->input('cliente_nombrecomercial');
                $apellidos = $request->input('cliente_razonsocial');
            }
            $messages = [
                    'cliente_dni.required'   => 'El "DNI" es Obligatorio.',
                    'cliente_dni.numeric'   => 'El "DNI" debe ser Númerico.',
                    'cliente_dni.digits'   => 'El "DNI" debe ser de 8 Digitos.',
                    'cliente_nombre.required'   => 'El "Nombre" es Obligatorio.',
                    'cliente_apellidos.required'   => 'El "Apellidos" es Obligatorio.',
                    'cliente_ruc.required'   => 'El "RUC" es Obligatorio.',
                    'cliente_ruc.numeric'   => 'El "RUC" debe ser Númerico.',
                    'cliente_ruc.digits'   => 'El "RUC" debe ser de 11 Digitos.',
                    'cliente_nombrecomercial.required'   => 'El "Nombre Comercial" es Obligatorio.',
                    'cliente_razonsocial.required'   => 'El "Razón Social" es Obligatorio.',
                    'cliente_numerotelefono.required' => 'El "Número de Teléfono" es Obligatorio.',
                    'cliente_email.required'    => 'El "Correo Electrónico" es Obligatorio.',
                    'cliente_email.email'    => 'El "Correo Electrónico" es Incorrecto.',
                    'cliente_idubigeo.required'    => 'El "Ubicación (Ubigeo)" es Obligatorio.',
                    'cliente_direccion.required'    => 'La "Dirección" es Obligatorio.',
                    'cliente_idestado.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $usuario = DB::table('users')
                ->where('identificacion',$identificacion)
                ->where('idtienda',$idtienda)
                ->first();
            if($usuario!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "DNI/RUC" ya existe, Ingrese Otro por favor.'
                ]);
            }
          
            $user = User::create([
                'nombre'         => $nombre,
                'apellidos'      => $apellidos!=null?$apellidos:'',
                'identificacion' => $identificacion!=null?$identificacion:'',
                'email'          => $request->input('cliente_email')!=null ? $request->input('cliente_email') : '',
                'email_verified_at' => Carbon::now(),
                'usuario'        => Carbon::now()->format("Ymdhisu"),
                'clave'          => '123',
                'password'       => Hash::make('123'),
                'numerotelefono' => $request->input('cliente_numerotelefono')!=null?$request->input('cliente_numerotelefono'):'',
                'direccion'      => $request->input('cliente_direccion'),
                'imagen'         => '',
                'iduserspadre'   => 0,
                'idubigeo'       => $request->input('cliente_idubigeo'),
                'idtipopersona'  => $request->input('cliente_idtipopersona'),
                'idtipousuario'  => 2,
                'idtienda'       => $idtienda,
                'idestado'       => 2
            ]);
          
            $ubigeocliente = DB::table('ubigeo')->whereId($request->input('cliente_idubigeo'))->first();
            
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.',
              'cliente' => $user,
              'ubigeocliente' => $ubigeocliente
            ]);
        }
        elseif ($request->view == 'registrarproducto') {
            $rules = [
              'nombre-registrarproducto' => 'required', 
              'precioalpublico-registrarproducto' => 'required', 
              'idcategoria-registrarproducto' => 'required',
            ];
            $messages = [
              'nombre-registrarproducto.required' => 'El "Nombre" es Obligatorio.',
              'precioalpublico-registrarproducto.required' => 'El "Precio al Público" es Obligatorio.',
              'idcategoria-registrarproducto.required' => 'La "Categoría" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $s_producto = DB::table('s_producto')
                ->where('codigo',$request->input('codigo-registrarproducto'))
                ->where('idtienda',$idtienda)
                ->where('s_idestado',1)
                ->first();
            if($s_producto!='' and $request->input('codigo-registrarproducto')!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "Código de Producto" ya existe, Ingrese Otro por favor.'
                ]);
            }  
          
            $s_idcategoria1 = 0; 
            $s_idcategoria2 = 0; 
            $s_idcategoria3 = 0;
          
            $categoria1 = DB::table('s_categoria')->whereId($request->input('idcategoria-registrarproducto'))->first();
            if($categoria1!=''){
                $s_idcategoria1 = $categoria1->id;
                $categoria2 = DB::table('s_categoria')->whereId($categoria1->s_idcategoria)->first();
                if($categoria2!=''){
                    $s_idcategoria2 = $categoria1->id;
                    $s_idcategoria1 = $categoria2->id;
                    $categoria3 = DB::table('s_categoria')->whereId($categoria2->s_idcategoria)->first();
                    if($categoria3!=''){
                        $s_idcategoria3 = $categoria1->id;
                        $s_idcategoria2 = $categoria2->id;
                        $s_idcategoria1 = $categoria3->id;
                    }
                }
            }
                  
            $idproducto = DB::table('s_producto')->insertGetId([
                'fecharegistro'  => Carbon::now(),
                'orden'  => 0,
                'codigo'         => $request->input('codigo-registrarproducto')!=null ? $request->input('codigo-registrarproducto') : '',
                'nombre'         => $request->input('nombre-registrarproducto'),
                'descripcion'    => '',
                'preciopormayor' => '0.00',
                'precioalpublico' => $request->input('precioalpublico-registrarproducto'),
                'por' => 1,
                'stockminimo' => 0,
                'alertavencimiento'=> 0,
                's_idproducto'  => 0,
                's_idcategoria1'  => $s_idcategoria1,
                's_idcategoria2'  => $s_idcategoria2,
                's_idcategoria3'  => $s_idcategoria3,
                's_idmarca'      => $request->input('idmarca-registrarproducto')!=null ? $request->input('idmarca-registrarproducto') : 0,
                's_idestadodetalle' => 2,
                's_idestado'     => 1,
                's_idestadotiendavirtual' => 2,
                's_idestadosistema'     => 1,
                'idunidadmedida'  => 1,
                'idproductopresentacion'  => 0,
                'idtienda'     => $idtienda
            ]);
          
            $producto_nuevo = DB::table('s_producto')->whereId($idproducto)->first();
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.',
              'producto' => $producto_nuevo
            ]);
        }
       
    }

    public function show(Request $request, $idtienda, $id)
    {
        if($id == 'show_table'){
            $idsucursal = Auth::user()->idsucursal;
            $idusuario = Auth::user()->id;
            $idapertura = sistema_apertura([
                'idtienda'          => $idtienda,
                'idsucursal'        => $idsucursal,
                'idusersrecepcion'  => $idusuario,
            ])['idapertura'];
        
            $compras = DB::table('s_compra')
                      ->join('s_tipocomprobante','s_tipocomprobante.id','s_compra.s_idcomprobante')
                      ->join('s_moneda','s_moneda.id','s_compra.s_idmoneda')
                      ->where('s_compra.idtienda',$idtienda)
                      ->where('s_compra.idsucursal',$idsucursal)
                      ->where('s_compra.s_idusuarioresponsable',$idusuario)

                      ->where('s_compra.codigo','LIKE','%'.$request['columns'][0]['search']['value'].'%')
                      ->where('s_compra.db_idcomprobante','LIKE','%'.$request['columns'][1]['search']['value'].'%')
                      ->where('s_compra.seriecorrelativo','LIKE','%'.$request['columns'][2]['search']['value'].'%')
                      ->where('s_compra.db_idmoneda','LIKE','%'.$request['columns'][3]['search']['value'].'%')
                      ->where('s_compra.totalredondeado','LIKE','%'.$request['columns'][4]['search']['value'].'%')
                      ->where('s_compra.db_idusuarioproveedor','LIKE','%'.$request['columns'][5]['search']['value'].'%')
                      ->where('s_compra.fecharegistro','LIKE','%'.$request['columns'][6]['search']['value'].'%')
                      ->where('s_compra.db_idusuarioresponsable','LIKE','%'.$request['columns'][7]['search']['value'].'%')
                      ->where('s_compra.db_idestado','LIKE','%'.$request['columns'][8]['search']['value'].'%')
                      
                     // ->where('s_compra.s_idaperturacierre',$idapertura)
                      ->select(
                          's_compra.*',
                          's_tipocomprobante.nombre as nombreComprobante',
                          's_moneda.codigo as monedacodigo',
                      )
                      ->orderBy('s_compra.id','desc')
                      ->paginate($request->length,'*',null,($request->start/$request->length)+1);
        
            $tabla = [];
            foreach($compras as $value){
                
    
                if($value->totalredondeado == 0){
                    $mon_total = DB::table('s_compradetalle')->where('s_idcompra',$value->id)->sum('preciototal');
                    $total = number_format($mon_total, 2, '.', '');
                }else{
                    $total = $value->totalredondeado;
                }
    
                $fecharegistro = $value->fecharegistro != '' ? date_format(date_create($value->fecharegistro),"d/m/Y h:i:s A") : '---';
                $tabla[] = [
                    'id' => $value->id,
                    'codigo' => str_pad($value->codigo, 8, "0", STR_PAD_LEFT),
                    'comprobante' => $value->nombreComprobante,
                    'correlativo' => $value->seriecorrelativo,
                    'moneda' => $value->db_idmoneda,
                    'total' => $total,
                    'proveedor' => $value->db_idusuarioproveedor,
                    'fecha_registro' => $fecharegistro,
                    'responsable'   => $value->db_idusuarioresponsable,
                    'estado'        => $value->db_idestado,
                    'opcion'  => [
                        [
                        'nombre' => 'Editar',
                        'onclick' => '/'.$idtienda.'/compra/'.$value->id.'/edit?view=editar',
                        'icono' => 'edit',
                    ],
                    [
                        'nombre' => 'Detalle',
                        'onclick' => '/'.$idtienda.'/compra/'.$value->id.'/edit?view=detalle',
                        'icono' => 'circle-info',
                    ],
                    [
                        'nombre' => 'Eliminar',
                        'onclick' => '/'.$idtienda.'/compra/'.$value->id.'/edit?view=eliminar',
                        'icono' => 'trash',
                    ],
                    [
                        'nombre' => 'Comprobante',
                        'onclick' => '/'.$idtienda.'/compra/'.$value->id.'/edit?view=ticket',
                        'icono' => 'invoice',
                    ]
                    ],
                ];
            }
            return response()->json([
                'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $compras->total(),
                'data'            => $tabla,
            ]);
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $s_compra = DB::table('s_compra')
            ->join('users','users.id','s_compra.s_idusuarioproveedor')
            ->where('s_compra.id',$id)
            ->select(
                's_compra.*'
            )
            ->first();
      
        if($request->input('view') == 'editar') {
            $comprobante = DB::table('s_tipocomprobante')->get();
            $s_compradetalles = DB::table('s_compradetalle')
              ->join('s_producto','s_producto.id','s_compradetalle.s_idproducto')
              ->join('s_unidadmedida','s_unidadmedida.id','s_compradetalle.idunidadmedida')
              ->where('s_compradetalle.s_idcompra',$s_compra->id)
              ->select(
                's_compradetalle.*',
                's_producto.codigo as productocodigo',
                's_producto.nombre as productonombre',
                's_producto.db_presentacion as presentaciones',
                's_unidadmedida.nombre as unidadmedida_nombre'
              )
              ->orderBy('s_compradetalle.id','asc')
              ->get();
          
            $tipopersonas = DB::table('tipopersona')->get();
             $s_monedas = DB::table('s_moneda')->get();
            return view(sistema_view().'/compra/edit',[
                'tienda' => $tienda,
                'comprobante' => $comprobante,
                's_compra' => $s_compra,
                's_compradetalles' => $s_compradetalles,
                'tipopersonas' => $tipopersonas,
                's_monedas' => $s_monedas,
            ]);
        }
        elseif($request->input('view') == 'detalle') {
            $ubigeos = DB::table('ubigeo')->get();
            $agencia = DB::table('s_agencia')->get();
            $comprobante = DB::table('s_tipocomprobante')->get();
            $usuarios = DB::table('users')->where('idtienda',$idtienda)->get();
            $s_compradetalles = DB::table('s_compradetalle')
              ->join('s_producto','s_producto.id','s_compradetalle.s_idproducto')
              ->join('s_unidadmedida','s_unidadmedida.id','s_compradetalle.idunidadmedida')
              ->where('s_compradetalle.s_idcompra',$s_compra->id)
              ->select(
                's_compradetalle.*',
                's_producto.codigo as productocodigo',
                's_producto.nombre as productonombre',
                's_producto.db_presentacion as presentaciones',
                's_unidadmedida.nombre as unidadmedida_nombre'
              )
              ->orderBy('s_compradetalle.id','asc')
              ->get();
            $s_monedas = DB::table('s_moneda')->get();
            
            
            return view(sistema_view().'/compra/detalle',[
                'tienda' => $tienda,
                'ubigeos' => $ubigeos,
                'agencia' => $agencia,
                'comprobante' => $comprobante,
                'usuarios' => $usuarios,
                's_compra' => $s_compra,
                's_compradetalles' => $s_compradetalles,
                's_monedas' => $s_monedas,
            ]);
        }
        elseif($request->input('view') == 'ticket'){
          
          $s_compradetalles = DB::table('s_compradetalle')
                              ->join('s_producto','s_producto.id','s_compradetalle.s_idproducto')
                              ->join('s_unidadmedida','s_unidadmedida.id','s_compradetalle.idunidadmedida')
                              ->where('s_compradetalle.s_idcompra',$s_compra->id)
                              ->select(
                                's_compradetalle.*',
                                's_producto.codigo as productocodigo',
                                's_producto.nombre as productonombre',
                                's_producto.db_presentacion as presentaciones',
                                's_unidadmedida.nombre as unidadmedida_nombre'
                              )
                              ->orderBy('s_compradetalle.id','asc')
                              ->get();
          $ticket = new \stdClass();
          //DATOS EMISOR
          $ticket->tipo_fuente = configuracion($tienda->id,'sistema_tipoletra')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_tipoletra')['valor']:'Helvetica';
          $ticket->ancho_ticket = configuracion($tienda->id,'sistema_anchoticket')['resultado']=='CORRECTO'?(configuracion($tienda->id,'sistema_anchoticket')['valor']-1):'8'.'cm';
          $ticket->ruc_emision = $s_compra->db_identificacionproveedor;
          $ticket->logotipo = url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$tienda->sucursal_imagen_logo);
          $ticket->razonsocial_emisor = strtoupper($s_compra->db_idusuarioproveedor);
          $ticket->direccion_emisor = strtoupper($s_compra->db_direccionproveedor);
          $ticket->tipo_documento = getTipoDocumentoCompra( $s_compra->s_idcomprobante );
          $ticket->serie_documento = $s_compra->seriecorrelativo;
          $ticket->correlativo_documento = '';
          $ticket->fechaemision = date_format(date_create($s_compra->fechaemision),"d/m/Y");
          $ticket->moneda = getTipoMoneda( $s_compra->s_idmoneda );
          
          $items = [];
          foreach( $s_compradetalles as $value ){
              $items[] = [
                          'codigoProducto' => 'S/N',
                          'descripcion'    => trim(strtoupper($value->concepto)),
                          'cantidad'       => $value->cantidad ,
                          'precio'         => $value->preciounitario,
                          'total'          => number_format($value->cantidad*$value->preciounitario, 2, '.', '')
                        ];
            
          }
          $ticket->items = $items;
          $ticket->total_venta = $s_compra->total;
          
          
          return view(sistema_view().'/compra/ticket',[
            'ticket' => $ticket
          ]);
        }
        elseif($request->input('view') == 'eliminar') {
            return view(sistema_view().'/compra/delete',[
              'tienda' => $tienda,
              's_compra' => $s_compra,
            ]);
        }
    }

    public function update(Request $request, $idtienda, $s_idcompra)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'editar') {
            $rules = [
              'idproveedor' => 'required',
              'idcomprobante' => 'required',
              'seriecorrelativo' => 'required',
              'fechaemision' => 'required',
              'idmoneda' => 'required',
              'idestado' => 'required',
              'productos' => 'required',
            ];
            $messages = [
              'idproveedor.required' => 'El "Cliente" es Obligatorio.',
              'idcomprobante.required' => 'El "Comprobante" es Obligatorio.',
              'seriecorrelativo.required' => 'La "Serie - Correlativo" es Obligatorio.',
              'fechaemision.required' => 'La "Fecha emisión" es Obligatorio.',
              'idmoneda.required' => 'La "Moneda" es Obligatorio.',
              'idestado.required' => 'El "Estado" es Obligatorio.',
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
                }elseif($item[2]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Es Obligatorio el Precio.'
                    ]);
                    break;
                }elseif($item[2]<0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Precio minímo es 0.00.'
                    ]);
                    break;
                }elseif($item[3]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Es Obligatorio la Fecha de vencimiento.'
                    ]);
                    break;
                }
                $list = explode('-',$item[3]);
                if(strlen($list[0])>4){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Año de la Fecha de Vencimiento es incorreto.'
                    ]);
                    break;
                }
            } 
         
            $idaperturacierre = 0;
            if($request->input('idestado')==2){
              
                // aperturacaja
                $caja = caja($idtienda,Auth::user()->id);
                if($caja['resultado']!='ABIERTO'){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Caja debe estar Aperturada.'
                    ]);
                }
                $idaperturacierre = $caja['apertura']->id;
                // fin aperturacaja

                $efectivo = efectivo($idtienda,$caja['apertura']->id,$request->input('idmoneda'));
                if($request->input('total')>$efectivo['total']){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'No hay suficiente saldo en caja!.'
                    ]);
                }
            }
            
            DB::table('s_compra')->whereId($s_idcompra)->update([
               'fechaconfirmacion' => Carbon::now(),
               'seriecorrelativo' => $request->input('seriecorrelativo'),
               'fechaemision' => $request->input('fechaemision'),
               'db_idestado' => $request->input('idestado') == 1 ? 'PENDIENTE' : 'COMPRADO',
               's_idmoneda' => $request->input('idmoneda'),
               's_idaperturacierre' => $idaperturacierre,
               's_idusuarioresponsable' => Auth::user()->id,
               's_idusuarioproveedor' => $request->input('idproveedor'),
               's_idcomprobante' =>  $request->input('idcomprobante'),
               's_idestado' => $request->input('idestado'),
            ]);
            
            DB::table('s_compradetalle')->where('s_idcompra',$s_idcompra)->delete();
            $productos = explode('&', $request->input('productos'));
            for($i = 1; $i < count($productos); $i++){
                $item = explode(',',$productos[$i]);
                $producto = DB::table('s_producto')->whereId($item[0])->first();
                $idcompradetalle = DB::table('s_compradetalle')->insertGetId([
                  'concepto' => $producto->nombre,
                  'cantidad' => $item[1],
                  'preciounitario' => $item[2],
                  'preciototal' => $item[4],
                  'por' => $producto->por,
                  'idunidadmedida' => $producto->idunidadmedida,
                  'fechavencimiento' => $item[3],
                  's_idproducto' => $item[0],
                  's_idcompra' => $s_idcompra,
                  'idtienda' => $idtienda,
                  'idestado' => 1,
                ]);
                if($request->input('idestado')==2){
                    // SALDO
                    productosaldo_actualizar(
                        $idtienda,
                        $producto->id,
                        'COMPRA',
                        $item[1],
                        $producto->por,
                        $producto->idunidadmedida,
                        $idcompradetalle
                    );
                }
            }  
          
             json_compra($idtienda, Auth::user()->idsucursal, Auth::user()->id);

            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'anular') {

            // aperturacaja
            $caja = caja($idtienda,Auth::user()->id);
            if($caja['resultado']!='ABIERTO'){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La Caja debe estar Aperturada.'
                ]);
            }
            $idaperturacierre = $caja['apertura']->id;
            // fin aperturacaja
          
            // validar 
            $s_compra = DB::table('s_compra')->whereid($s_idcompra)->first();
            if($idaperturacierre!=$s_compra->s_idaperturacierre){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El movimiento no se puede anular, ya que no pertenece a esta caja aperturada.'
                ]);
            }

            DB::table('s_compra')->whereId($s_idcompra)->update([     
                'fechaanulacion'=> Carbon::now(),
                'db_idestado' => 'ANULADO',
                's_idestado'=> 3
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha anulado correctamente.'
            ]);
        }
    }


   public function destroy(Request $request, $idtienda, $s_idcompra)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
            DB::table('s_compradetalle')->where('s_idcompra',$s_idcompra)->delete();
            DB::table('s_compra')
                ->whereId($s_idcompra)
                ->delete();
            json_compra($idtienda, Auth::user()->idsucursal, Auth::user()->id);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
