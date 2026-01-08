<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use App\User;
use Hash;

class CompraController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        //sistema_json_compras($idtienda,$request->name_modulo);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
         json_compra($idtienda,$request->name_modulo);

        return view('layouts/backoffice/tienda/nuevosistema/compra/index',[
            'tienda' => $tienda,
        ]);
      
        /*$where = [];
        if($request->input('codigo')!=''){
            $where[] = ['s_compra.codigo','LIKE',$request->input('codigo')];
        }
        $where[] = ['s_tipocomprobante.nombre','LIKE','%'.$request->input('comprobante').'%'];
        $where[] = ['s_compra.seriecorrelativo','LIKE','%'.$request->input('seriecorrelativo').'%'];
        $where[] = ['users.nombre','LIKE','%'.$request->input('proveedor').'%'];
        $where[] = ['s_compra.fecharegistro','LIKE','%'.$request->input('fecharegistro').'%'];
      
        if(Auth::user()->idtienda!=0 && Auth::user()->idtipousuario!=1){
            $where[] = ['responsable.id',Auth::user()->id];
        }
        if($request->input('moneda')!=''){
            $where[] = ['s_moneda.id',$request->input('moneda')];
        }
        
        $s_compra = DB::table('s_compra')
            ->join('users','users.id','s_compra.s_idusuarioproveedor')
            ->join('s_tipocomprobante','s_tipocomprobante.id','s_compra.s_idcomprobante')
            ->join('users as responsable','responsable.id','s_compra.s_idusuarioresponsable')
            ->join('s_moneda','s_moneda.id','s_compra.s_idmoneda')
            ->where('s_compra.idtienda',$idtienda)
            ->where($where)
            ->select(
                's_compra.*',
                DB::raw('CONCAT(users.apellidos, ', ', users.nombre) as proveedor')
                's_tipocomprobante.nombre as comprobante',
                'responsable.nombre as responsable',
                's_moneda.codigo as moneda',
            )
            ->orderBy('s_compra.id','desc')
            ->paginate(10);

        // aperturacaja
        $caja = caja($idtienda,Auth::user()->id);
        $idaperturacierre = 0;
        if($caja['resultado']=='ABIERTO'){
            $idaperturacierre = $caja['apertura']->id;
        }

        return view('layouts/backoffice/tienda/nuevosistema/compra/index',[
            'tienda' => $tienda,
            's_compra' => $s_compra,
            'idapertura' => $idaperturacierre,
        ]);*/
    }

    public function create(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $comprobante = DB::table('s_tipocomprobante')->get();
        $tipopersonas = DB::table('tipopersona')->get();
        $s_monedas = DB::table('s_moneda')->get();
        $configuracion = configuracion_facturacion($idtienda);
      
        // Registrar Producto
        $marcas       = DB::table('s_marca')->where('idtienda',$idtienda)->get();
        $categorias = DB::table('s_categoria')
          ->where('s_categoria.idtienda',$idtienda)
          ->where('s_categoria.s_idcategoria',0)
          ->orderBy('s_categoria.nombre','asc')
          ->get();
      
        return view('layouts/backoffice/tienda/nuevosistema/compra/create',[
          'tienda' => $tienda,
          'comprobante' => $comprobante,
          'tipopersonas' => $tipopersonas,
          's_monedas' => $s_monedas,
          'configuracion' => $configuracion,

          'marcas' => $marcas,
          'categorias' => $categorias,
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
                }elseif($item[3]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Es Obligatorio la Fecha de vencimiento del producto "'.$producto->nombre.'".'
                    ]);
                    break;
                }
                $list = explode('-',$item[3]);
                if(strlen($list[0])>4){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Año de la Fecha de Vencimiento es incorreto del producto "'.$producto->nombre.'".'
                    ]);
                    break;
                }
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

            $s_compra = DB::table('s_compra')
                ->where('s_compra.idtienda',$idtienda)
                ->orderBy('s_compra.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($s_compra!=''){
                $codigo = $s_compra->codigo+1;
            }

            $idcompra = DB::table('s_compra')->insertGetId([
               'codigo' => $codigo,
               'fecharegistro' => Carbon::now(),
               'fechaconfirmacion' => Carbon::now(),
               'seriecorrelativo' => $request->input('seriecorrelativo'),
               'fechaemision' => $request->input('fechaemision'),
               'total' => $request->input('total'),
               'totalredondeado' => $request->input('totalredondeado'),
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
                $idcompradetalle = DB::table('s_compradetalle')->insertGetId([
                  'concepto' => $producto->nombre,
                  'cantidad' => $item[1],
                  'preciounitario' => $item[2],
                  'preciototal' => $item[4],
                  'fechavencimiento' => $item[3],
                  's_idproducto' => $item[0],
                  's_idcompra' => $idcompra,
                ]);
                if($request->input('idestado')==2){
                    // SALDO
                    productosaldo_actualizar(
                        $idtienda,
                        'COMPRA',
                        $producto->codigo,
                        $producto->nombre,
                        $producto->idunidadmedida,
                        $producto->por,
                        $item[1],
                        $item[2],
                        $item[4],
                        $producto->id,
                        $idcompradetalle
                    );
                }
                
                // inicio actualizando precio del producto
                DB::table('s_producto')->whereId($item[0])->update([
                  'precioalpublico' => $item[5]
                ]);
                // fin actualizando precio del producto
            }          
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
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if ($id=='showlistarproducto') {
            $productos = DB::table('s_producto')
                ->join('tienda','tienda.id','s_producto.idtienda')
                ->leftJoin('s_categoria','s_categoria.id','s_producto.s_idcategoria1')
                ->leftJoin('s_categoria as subcategoria','subcategoria.id','s_producto.s_idcategoria2')
                ->leftJoin('s_marca','s_marca.id','s_producto.s_idmarca')
                ->leftJoin('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                ->where('s_producto.idtienda',$idtienda)
                ->where('s_producto.nombre','LIKE','%'.$request->input('buscar').'%')
                ->where('s_producto.s_idestado',1)
                ->select(
                  's_producto.id as id',
                  's_producto.codigo as codigo',
                  's_producto.nombre as nombre',
                  's_producto.precioalpublico as precioalpublico',
                  's_producto.s_idestadodetalle as idestadodetalle',
                  's_producto.s_idestado as idestado',
                  's_producto.s_idestadotiendavirtual as idestadotv',
                   DB::raw('CONCAT(unidadmedida.nombre," x ",s_producto.por) as unidadmedida'),
                   DB::raw('CONCAT(s_producto.nombre," / ",s_producto.precioalpublico) as text'),
                   'tienda.id as idtienda',
                   'tienda.nombre as tiendanombre',
                   'tienda.link as tiendalink',
                   's_marca.nombre as marcanombre',
                   's_categoria.nombre as categorianombre',
                   DB::raw('(SELECT imagen FROM s_productogaleria WHERE s_idproducto=s_producto.id ORDER BY orden ASC LIMIT 1) as imagen')
                )
                ->limit(20)
                ->get();
            return $productos;
        }
        elseif ($id=='showseleccionarproducto') {
            $producto = producto($idtienda,$request->input('idproducto'));
            if($producto['producto']==''){
                return [ 
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No existe el producto, ingrese otro producto.',
                ];
            }
            return [ 
              'producto' => $producto['producto'],
              'stock' => $producto['stock']
            ];
        }
        elseif ($id=='showseleccionarproductocodigo') {
            if($request->input('codigoproducto')==''){
                return [ 
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Ingrese un codigo de Producto!!.',
                ];
            }
            $datosProducto = DB::table('s_producto')
                ->where('s_producto.idtienda',$idtienda)
                ->where('s_producto.codigo',$request->input('codigoproducto'))
                ->where('s_producto.s_idestado',1)
                ->first();
            if($datosProducto==''){
                return [ 
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El producto no existe, puede registrarlo ahora.',
                ];
            }
            return [ 
              'producto' => $datosProducto,
              'stock' => productosaldo($idtienda,$datosProducto->id)['stock']
            ];
        }
      
        elseif ($id == 'show-moduloactualizar') {
          json_compra($idtienda,$request->name_modulo);
                     
        }
            
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
       $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $compra = DB::table('s_compra')
          ->join('users','users.id','s_compra.s_idusuarioproveedor')
          ->where('s_compra.id',$id)
          ->select(
              's_compra.*',
              'users.idubigeo as idubigeo',
              'users.identificacion as proveedoridentificacion',
              'users.nombre as proveedornombre',
              'users.apellidos as proveedorapellidos'
          )
          ->first();
        $s_compradetalles = DB::table('s_compradetalle')
          ->join('s_producto','s_producto.id','s_compradetalle.s_idproducto')
          ->where('s_compradetalle.s_idcompra',$compra->id)
          ->select(
            's_compradetalle.*',
            's_producto.codigo as productocodigo',
            's_producto.nombre as productonombre'
          )
          ->orderBy('s_compradetalle.id','asc')
          ->get();
        $comprobante = DB::table('s_tipocomprobante')->get();
        $tipopersonas = DB::table('tipopersona')->get();
        $s_monedas = DB::table('s_moneda')->get();

        $ubigeos = DB::table('ubigeo')->get();
        $agencia = DB::table('s_agencia')->get();
        $usuarios = DB::table('users')->where('idtienda',$idtienda)->get();

        if ($request->input('view') == 'editar') {
         
            $comprobante = DB::table('s_tipocomprobante')->get();
            $s_compradetalles = DB::table('s_compradetalle')
              ->join('s_producto','s_producto.id','s_compradetalle.s_idproducto')
              ->where('s_compradetalle.s_idcompra',$compra->id)
              ->select(
                's_compradetalle.*',
                's_producto.codigo as productocodigo',
                's_producto.nombre as productonombre'
              )
              ->orderBy('s_compradetalle.id','asc')
              ->get();
            $tipopersonas = DB::table('tipopersona')->get();
          
            
            $s_monedas = DB::table('s_moneda')->get();
            return view('layouts/backoffice/tienda/nuevosistema/compra/edit',[
                'tienda' => $tienda,
                'comprobante' => $comprobante,
                'compra' => $compra,
                's_compradetalles' => $s_compradetalles,
                'tipopersonas' => $tipopersonas,
                's_monedas' => $s_monedas,
            ]);
 
        }
        elseif ($request->input('view') == 'detalle') {
            $ubigeos = DB::table('ubigeo')->first();
            $agencia = DB::table('s_agencia')->first();
            $comprobante = DB::table('s_tipocomprobante')->first();
            $usuarios = DB::table('users')->where('idtienda',$idtienda)->first();
            $s_compradetalles = DB::table('s_compradetalle')
              ->join('s_producto','s_producto.id','s_compradetalle.s_idproducto')
              ->where('s_compradetalle.s_idcompra',$compra->id)
              ->select(
                's_compradetalle.*',
                's_producto.codigo as productocodigo',
                's_producto.nombre as productonombre'
              )
              ->get();
            $s_monedas = DB::table('s_moneda')->first();
            return view('layouts/backoffice/tienda/nuevosistema/compra/detalle',[
                'tienda' => $tienda,
                'ubigeos' => $ubigeos,
                'agencia' => $agencia,
                'comprobante' => $comprobante,
                'usuarios' => $usuarios,
                'compra' => $compra,
                's_compradetalles' => $s_compradetalles,
                's_monedas' => $s_monedas,
            ]);
        }
        elseif ($request->input('view') == 'eliminar') {
          $ubigeos = DB::table('ubigeo')->first();
            $agencia = DB::table('s_agencia')->first();
            $comprobante = DB::table('s_tipocomprobante')->first();
            $usuarios = DB::table('users')->where('idtienda',$idtienda)->first();
            $s_compradetalles = DB::table('s_compradetalle')
              ->join('s_producto','s_producto.id','s_compradetalle.s_idproducto')
              ->where('s_compradetalle.s_idcompra',$compra->id)
              ->select(
                's_compradetalle.*',
                's_producto.codigo as productocodigo',
                's_producto.nombre as productonombre'
              )
              ->get();
            $s_monedas = DB::table('s_moneda')->first();
            return view('layouts/backoffice/tienda/nuevosistema/compra/delete',[
                'tienda' => $tienda,
                'ubigeos' => $ubigeos,
                'agencia' => $agencia,
                'comprobante' => $comprobante,
                'usuarios' => $usuarios,
                'compra' => $compra,
                's_compradetalles' => $s_compradetalles,
                's_monedas' => $s_monedas,
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
                  'fechavencimiento' => $item[3],
                  's_idproducto' => $item[0],
                  's_idcompra' => $s_idcompra,
                ]);
                if($request->input('idestado')==2){
                    // SALDO
                    productosaldo_actualizar(
                        $idtienda,
                        'COMPRA',
                        $producto->codigo,
                        $producto->nombre,
                        $producto->idunidadmedida,
                        $producto->por,
                        $item[1],
                        $item[2],
                        $item[4],
                        $producto->id,
                        $idcompradetalle
                    );
                }
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
    public function destroy(Request $request, $idtienda, $s_idcompra)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if($request->input('view') == 'eliminar') {
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
                's_idestado'=> 3
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
