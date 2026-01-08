<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use Auth;
use Hash;
use DB;
use PDF; 
use Mail;
use NumeroALetras;

class FacturacionBoletafacturaController extends Controller
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
      
       json_facturacionboletafactura($idtienda,$request->name_modulo);
       
        return view('layouts/backoffice/tienda/nuevosistema/facturacionboletafactura/index', [
            'tienda'                   => $tienda,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda         = DB::table('tienda')->whereId($idtienda)->first();
        $configuracion  = configuracion_facturacion($idtienda);
        $configuracion_comer = configuracion_comercio($idtienda);
        $monedas        = DB::table('s_moneda')->get();
        $comprobantes   = DB::table('s_tipocomprobante')->where('id',2)->orWhere('id',3)->get();
        $tipopersonas   = DB::table('tipopersona')->get();
        $agencias = DB::table('s_agencia')
                ->where('idtienda',$idtienda)
                ->where('idestadofacturacion',1)
                ->get();
        return view('layouts/backoffice/tienda/nuevosistema/facturacionboletafactura/create',[
            'tienda'        => $tienda,
            'configuracion' => $configuracion,
            'monedas'       => $monedas,
            'comprobantes'  => $comprobantes,
            'tipopersonas'  => $tipopersonas,
            'agencias'      => $agencias,
            'configuracion_comer' => $configuracion_comer
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
                'idcliente'                  => 'required',
                'direccion'                  => 'required',
                'idubigeo'                   => 'required',
                'idagencia'                  => 'required',
                'idtipocomprobante'          => 'required',
                'idmoneda'                   => 'required',
                'productos'                  => 'required',
            ];
            $messages = [
                'idcliente.required'         => 'El "Cliente" es Obligatorio.',
                'direccion.required'         => 'La "Dirección" es Obligatorio.',
                'idubigeo.required'          => 'El "Ubigeo" es Obligatorio.',
                'idagencia.required'         => 'La "Agencia" es Obligatorio.',
                'idmoneda.required'          => 'La "Moneda" es Obligatorio.',
                'idtipocomprobante.required' => 'El "Tipo de comprobante" es Obligatorio.',
                'productos.required'         => 'Los "Productos" son Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);    
            /**
            *Validando si la persona tiene ruc o dni para emitir una boleta o factura
            * Factura = 3
            * Boleta = 2
            */
            if($request->input('idtipocomprobante')==3){
                $cliente = DB::table('users')->whereId($request->input('idcliente'))->first();
                if($cliente->idtipopersona<>2){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'En una Factura el cliente debe ser un Persona Jurídica con RUC.'
                    ]);
                }
            }elseif($request->input('idtipocomprobante')==2){
                $cliente = DB::table('users')->whereId($request->input('idcliente'))->first();
                if($cliente->idtipopersona<>1){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Cliente debe tener un DNI!!.'
                    ]);
                }
            }
          
            // Recorriendo los productos, capturados
          
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
                        'mensaje'   => 'La Precio minímo es 0.00.'
                    ]);
                    break;
                }elseif($item[5]==1){
                    if($item[4]==''){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'Es Obligatorio el Detalle.'
                        ]);
                        break;
                    }
                }                    
            } 
          
            // Actualizando los datos de direccion y ubigeo del cliente
            DB::table('users')->whereId($request->input('idcliente'))->update([
               'direccion'  => $request->input('direccion'),
               'idubigeo'   => $request->input('idubigeo')
            ]);
          
            
            $configuracion  = configuracion_facturacion($idtienda);
          
            $agencia = DB::table('s_agencia')
                ->where('s_agencia.id',$request->input('idagencia'))
                ->first();

            $cliente = DB::table('users')
                ->where('users.id',$request->input('idcliente'))
                ->first();

            $clienteubigeo = DB::table('ubigeo')
                ->where('ubigeo.id',$request->input('idubigeo'))
                ->first();
            
            $moneda = DB::table('s_moneda')->whereId($request->input('idmoneda'))->first();
          
            $tienda = DB::table('tienda')
                ->join('ubigeo','ubigeo.id','tienda.idubigeo')
                ->where('tienda.id',$idtienda)
                ->select(
                    'tienda.id as tiendaserie',
                    'ubigeo.codigo as tiendaubigeocodigo',
                    'ubigeo.distrito as tiendaubigeodistrito',
                    'ubigeo.provincia as tiendaubigeoprovincia',
                    'ubigeo.departamento as tiendaubigeodepartamento',
                    'tienda.direccion as tiendadireccion'
                )
                ->first();
          
            if($cliente->idtipopersona==1) {
                $cliente_tipodocumento  = 1;
                $cliente_razonsocial    = $cliente->apellidos.', '.$cliente->nombre;
            }elseif($cliente->idtipopersona==2) {
                $cliente_tipodocumento  = 6;
                $cliente_razonsocial    = $cliente->apellidos;
            }

            if($request->input('idtipocomprobante')==2) {
                $venta_tipodocumento  = '03';
                $venta_serie          = 'B'.str_pad($agencia->facturacion_serie, 3, "0", STR_PAD_LEFT);
            }else if($request->input('idtipocomprobante')==3) {
                $venta_tipodocumento  = '01';
                $venta_serie          = 'F'.str_pad($agencia->facturacion_serie, 3, "0", STR_PAD_LEFT);
            }
          
            $correlativo = DB::table('s_facturacionboletafactura')
                ->where('venta_tipodocumento',$venta_tipodocumento)
                ->where('emisor_ruc',$agencia->ruc)
                ->where('venta_serie',$venta_serie)
                ->orderBy('venta_correlativo','desc')
                ->limit(1)
                ->first();

            if(!is_null($correlativo) ){
                $venta_correlativo = $correlativo->venta_correlativo+1;
            }else{
                $venta_correlativo = 1;
            }
          
            $igv = ($configuracion->facturacion_igv/100)+1;
            $total_precioventa    = 0;
            $total_valorunitario  = 0;
            $total_valorventa     = 0;
            $total_impuesto       = 0;
            for($i = 1; $i < count($productos); $i++){
                $item           = explode('/,/',$productos[$i]);
                $cantidad       = $item[1];
                $preciounitario = number_format($item[2],2, '.', '');
                $precioventa    = number_format($preciounitario*$cantidad,2, '.', '');
                $valorunitario  = number_format(($preciounitario/$igv),2, '.', '');
                $valorventa     = number_format($valorunitario*$cantidad,2, '.', '');
                $impuesto            = number_format($precioventa-$valorventa,2, '.', '');

                $total_precioventa    = $total_precioventa+$precioventa;
                $total_valorunitario  = $total_valorunitario+$valorunitario;
                $total_valorventa     = $total_valorventa+$valorventa;
                $total_impuesto       = $total_impuesto+$impuesto;
            }
          
            // Almacenando en la base de datos la facturacion
            $idfacturacionboletafactura = DB::table('s_facturacionboletafactura')->insertGetId([
                'emisor_ruc'                  => $agencia->ruc,
                'emisor_razonsocial'          => $agencia->razonsocial,
                'emisor_nombrecomercial'      => $agencia->nombrecomercial,
                'emisor_ubigeo'               => $tienda->tiendaubigeocodigo,
                'emisor_departamento'         => $tienda->tiendaubigeodepartamento,
                'emisor_provincia'            => $tienda->tiendaubigeoprovincia,
                'emisor_distrito'             => $tienda->tiendaubigeodistrito,
                'emisor_urbanizacion'         => '',
                'emisor_direccion'            => $tienda->tiendadireccion,
                'cliente_tipodocumento'       => $cliente_tipodocumento,
                'cliente_numerodocumento'     => $cliente->identificacion,
                'cliente_razonsocial'         => $cliente_razonsocial,
                'cliente_ubigeo'              => $clienteubigeo->codigo,
                'cliente_departamento'        => $clienteubigeo->departamento,
                'cliente_provincia'           => $clienteubigeo->provincia,
                'cliente_distrito'            => $clienteubigeo->distrito,
                'cliente_urbanizacion'        => '',
                'cliente_direccion'           => $cliente->direccion,
                'venta_ublversion'            => '2.1',
                'venta_tipooperacion'         => '0101',
                'venta_tipodocumento'         => $venta_tipodocumento,
                'venta_serie'                 => $venta_serie,
                'venta_correlativo'           => $venta_correlativo,
                'venta_fechaemision'          => Carbon::now(),
                'venta_tipomoneda'            => $moneda->codigo,
                'venta_montooperaciongravada' => number_format($total_valorventa,2, '.', ''),
                'venta_montoigv'              => number_format($total_impuesto,2, '.', ''),
                'venta_totalimpuestos'        => number_format($total_impuesto,2, '.', ''),
                'venta_valorventa'            => number_format($total_valorventa,2, '.', ''),
                'venta_subtotal'              => number_format($total_precioventa,2, '.', ''),
                'venta_montoimpuestoventa'    => number_format($total_precioventa,2, '.', ''),
                'venta_igv'                   => $configuracion->facturacion_igv,
                'leyenda_codigo'              => '1000',
                'leyenda_value'               => NumeroALetras::convertir(number_format($total_precioventa,2, '.', '')).' CON  00/100 '.$moneda->nombre,
                'idventa'                     => 0,
                'idagencia'                   => $request->input('idagencia'),
                'idtienda'                    => $idtienda,
                'idusuarioresponsable'        => Auth::user()->id,
                'idusuariocliente'            => $request->input('idcliente')
            ]);
     
            for($i = 1; $i < count($productos); $i++){
                $item                 = explode('/,/',$productos[$i]);
                $producto             = DB::table('s_producto')->whereId($item[0])->first();
                $productounidadmedida = DB::table('unidadmedida')->whereId($producto->idunidadmedida)->first();

                $cantidad             = $item[1];
                $preciounitario       = number_format($item[2],2, '.', '');
                $precioventa          = number_format($preciounitario*$cantidad,2, '.', '');
                $valorunitario        = number_format(($preciounitario/$igv),2, '.', '');
                $valorventa           = number_format($valorunitario*$cantidad,2, '.', '');
                $impuesto             = number_format($precioventa-$valorventa,2, '.', '');

                DB::table('s_facturacionboletafacturadetalle')->insert([
                    'codigoproducto'             => str_pad($producto->codigo, 6, "0", STR_PAD_LEFT),
                    'unidad'                     => $productounidadmedida->codigo,
                    'cantidad'                   => $item[1],
                    'descripcion'                => $producto->nombre,
                    'montobaseigv'               => $valorventa,
                    'porcentajeigv'              => $configuracion->facturacion_igv,
                    'igv'                        => $impuesto,
                    'tipoafectacionigv'          => '10',
                    'totalimpuestos'             => $impuesto,
                    'montovalorventa'            => $valorventa,
                    'montovalorunitario'         => $valorunitario,
                    'montopreciounitario'        => $preciounitario,
                    'idproducto'                 => $producto->id,
                    'idfacturacionboletafactura' => $idfacturacionboletafactura
               ]);
            }
            // Fin de Facturacion
          
            // Enviando a la Sunat
            $result = facturador_facturaboleta($idfacturacionboletafactura);
          
            return [
                  'resultado' => $result['resultado'],
                  'mensaje'   => $result['mensaje']
            ];
        }elseif($request->input('view') == 'registrarcliente') {
            if($request->input('cliente_idtipopersona')==1){
                $rules = [
                    'cliente_dni'       => 'required|numeric|digits:8',
                    'cliente_nombre'    => 'required',
                    'cliente_apellidos' => 'required',
                    'cliente_idubigeo'  => 'required',
                    'cliente_direccion' => 'required'
                ];
                $identificacion = $request->input('cliente_dni');
                $nombre = $request->input('cliente_nombre');
                $apellidos = $request->input('cliente_apellidos');
            }else{
                $rules = [
                    'cliente_ruc'             => 'required|numeric|digits:11',
                    'cliente_nombrecomercial' => 'required',
                    'cliente_razonsocial'     => 'required',
                    'cliente_idubigeo'        => 'required',
                    'cliente_direccion'       => 'required'
                ];
                $identificacion = $request->input('cliente_ruc');
                $nombre = $request->input('cliente_nombrecomercial');
                $apellidos = $request->input('cliente_razonsocial');
            }
            $messages = [
                    'cliente_dni.required'              => 'El "DNI" es Obligatorio.',
                    'cliente_dni.numeric'               => 'El "DNI" debe ser Númerico.',
                    'cliente_dni.digits'                => 'El "DNI" debe ser de 8 Digitos.',
                    'cliente_nombre.required'           => 'El "Nombre" es Obligatorio.',
                    'cliente_apellidos.required'        => 'El "Apellidos" es Obligatorio.',
                    'cliente_ruc.required'              => 'El "RUC" es Obligatorio.',
                    'cliente_ruc.numeric'               => 'El "RUC" debe ser Númerico.',
                    'cliente_ruc.digits'                => 'El "RUC" debe ser de 11 Digitos.',
                    'cliente_nombrecomercial.required'  => 'El "Nombre Comercial" es Obligatorio.',
                    'cliente_razonsocial.required'      => 'El "Razón Social" es Obligatorio.',
                    'cliente_numerotelefono.required'   => 'El "Número de Teléfono" es Obligatorio.',
                    'cliente_email.required'            => 'El "Correo Electrónico" es Obligatorio.',
                    'cliente_email.email'               => 'El "Correo Electrónico" es Incorrecto.',
                    'cliente_idubigeo.required'         => 'El "Ubicación (Ubigeo)" es Obligatorio.',
                    'cliente_direccion.required'        => 'La "Dirección" es Obligatorio.',
                    'cliente_idestado.required'         => 'El "Estado" es Obligatorio.',
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
                'nombre'            => $nombre,
                'apellidos'         => $apellidos!=null?$apellidos:'',
                'identificacion'    => $identificacion!=null?$identificacion:'',
                'email'             => $request->input('cliente_email')!=null ? $request->input('cliente_email') : '',
                'email_verified_at' => Carbon::now(),
                'usuario'           => Carbon::now()->format("Ymdhisu"),
                'clave'             => '123',
                'password'          => Hash::make('123'),
                'numerotelefono'    => $request->input('cliente_numerotelefono')!=null?$request->input('cliente_numerotelefono'):'',
                'direccion'         => $request->input('cliente_direccion'),
                'imagen'            => '',
                'iduserspadre'      => 0,
                'idubigeo'          => $request->input('cliente_idubigeo'),
                'idtipopersona'     => $request->input('cliente_idtipopersona'),
                'idtipousuario'     => 2,
                'idtienda'          => $idtienda,
                'idestado'          => 2
            ]);
            $ubigeocliente = DB::table('ubigeo')->whereId($request->input('cliente_idubigeo'))->first();
          
            return response()->json([
              'resultado'     => 'CORRECTO',
              'mensaje'       => 'Se ha registrado correctamente.',
              'cliente'       => $user,
              'ubigeocliente' => $ubigeocliente
            ]);
        }
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
        if( $id == 'showlistarusuario' ){
          // Listando usuarios o clientes que la tienda tiene registrado
            $usuarios = DB::table('users')
                ->where('idtienda',$idtienda)
                ->where('users.nombre','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('idtienda',$idtienda)
                ->where('users.apellidos','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('idtienda',$idtienda)
                ->where('users.identificacion','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'users.id as id',
                   DB::raw('CONCAT(users.identificacion," - ",users.apellidos,", ",users.nombre) as text')
                )
                ->get();
            return $usuarios;
          
        }
        elseif( $id == 'showseleccionarusuario' ){       
            // Seleccionando un usuario y mostrando en el select
            $usuario = DB::table('users')
                ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
                ->where('users.idtienda',$idtienda)
                ->where('users.id',$request->input('idusuario'))
                ->select(
                    'users.*',
                    'ubigeo.nombre as ubigeonombre'
                )
                ->first();
            return [ 
              'usuario' => $usuario
            ];

        }
        elseif( $id == 'showlistarubigeo' ){ 
            // Listando ubigeo
            $ubigeos = DB::table('ubigeo')
                ->where('ubigeo.departamento','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('ubigeo.provincia','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('ubigeo.distrito','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('ubigeo.nombre','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'ubigeo.id as id',
                   DB::raw('CONCAT(ubigeo.nombre) as text')
                )
                ->get();
            return $ubigeos;
          
        }
        elseif( $id == 'showlistarproducto' ){
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
        elseif($id=='showseleccionarproducto'){
            $producto = producto($idtienda,$request->input('idproducto'));
            if($producto['producto']==''){
                return [ 
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No existe el producto, ingrese otro código.',
                ];
            }
            return [ 
              'producto' => $producto['producto'],
              'stock'    => $producto['stock']
            ];
        }
        elseif($id=='showstockproducto'){
            return producto($idtienda,$request->input('idproducto'));
        }
        elseif($id=='showseleccionarunidadproducto'){
            return unidad_productos($idtienda,$request->input('idproducto'));
        }
        elseif($id=='showseleccionarproductocodigo'){
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
                    'mensaje'   => 'No existe el producto, ingrese otro código.',
                ];
            }
            return [ 
              'producto' => $datosProducto,
              'stock' => productosaldo($idtienda,$datosProducto->id)['stock']
            ];
        }
        elseif($id == 'showbuscaridentificacion'){
            return consultaDniRuc($request->input('buscar_identificacion'), $request->input('tipo_persona'));
        }elseif($id == 'show-moduloactualizar'){
              json_facturacionboletafactura($idtienda,$request->name_modulo);
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
        $facturacionboletafactura = DB::table('s_facturacionboletafactura as facturaboleta')
            ->join('users as responsable','responsable.id','facturaboleta.idusuarioresponsable')
            ->join('s_agencia','s_agencia.id','facturaboleta.idagencia')
            ->where('facturaboleta.id',$id)
            ->select(
                'facturaboleta.*',
                'responsable.nombre as responsablenombre',
                's_agencia.logo as agencialogo',
                 DB::raw('CONCAT(facturaboleta.cliente_numerodocumento," - ",facturaboleta.cliente_razonsocial) as cliente'),
                 DB::raw('CONCAT(facturaboleta.cliente_departamento, " , ", facturaboleta.cliente_provincia, " , ", facturaboleta.cliente_distrito) as ubigeo'),
                 DB::raw('CONCAT(facturaboleta.emisor_ruc, " - ", facturaboleta.emisor_nombrecomercial) as agencia')
              )
            ->first();
      
        if($request->input('view') == 'detalle') {
            $boletafacturadetalle     = DB::table('s_facturacionboletafacturadetalle')
                ->join('s_producto','s_producto.id','s_facturacionboletafacturadetalle.idproducto')
                ->where('s_facturacionboletafacturadetalle.idfacturacionboletafactura',$facturacionboletafactura->id)
                ->select(
                    's_facturacionboletafacturadetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionboletafacturadetalle.id','asc')
                ->get();
            return view('layouts/backoffice/tienda/nuevosistema/facturacionboletafactura/detalle',[
                'facturacionboletafactura'=> $facturacionboletafactura,
                'boletafacturadetalle'    => $boletafacturadetalle,
                'tienda'                  => $tienda
            ]);

        }
        elseif($request->input('view') == 'ticket') {
            return view('layouts/backoffice/tienda/nuevosistema/facturacionboletafactura/ticket',[
                'tienda' => $tienda,
                'facturacionboletafactura'=> $facturacionboletafactura
            ]);
        }
        elseif($request->input('view') == 'ticketpdf') {
            $boletafacturadetalle     = DB::table('s_facturacionboletafacturadetalle')
                ->join('s_producto','s_producto.id','s_facturacionboletafacturadetalle.idproducto')
                ->where('s_facturacionboletafacturadetalle.idfacturacionboletafactura',$facturacionboletafactura->id)
                ->select(
                    's_facturacionboletafacturadetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionboletafacturadetalle.id','asc')
                ->get();
            $facturacionrespuesta = facturador_respuesta('BOLETAFACTURA',$facturacionboletafactura->id);
            $configuracion = configuracion_facturacion($idtienda);
          
            //comida
            $comida_venta = null;
            if($tienda->idcategoria==30){
                $comida_venta = DB::table('s_comida_ordenpedidoventa')
                  ->join('s_comida_ordenpedido','s_comida_ordenpedido.id','s_comida_ordenpedidoventa.s_idcomida_ordenpedido')
                  ->join('users as mesero','mesero.id','s_comida_ordenpedido.idresponsable')
                  ->join('s_comida_mesa','s_comida_mesa.id','s_comida_ordenpedido.idmesa')
                  ->where('s_comida_ordenpedidoventa.s_idventa',$facturacionboletafactura->idventa)
                  ->select(
                    'mesero.nombre as mesero_nombre',
                    's_comida_mesa.numero_mesa as mesa_numero_mesa'
                  )
                  ->first();
            }

            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionboletafactura/ticketpdf',[
                'tienda'                   => $tienda,
                'facturacionboletafactura' => $facturacionboletafactura,
                'boletafacturadetalle'     => $boletafacturadetalle,
                'configuracion'            => $configuracion,
                'respuesta'                => $facturacionrespuesta,
                'comida_venta'             => $comida_venta
            ]);
            $ticket = 'Ticket_'.str_pad($facturacionboletafactura->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');
        }
        elseif($request->input('view') == 'a4') {
            return view('layouts/backoffice/tienda/sistema/facturacionboletafactura/a4',[
                'tienda' => $tienda,
                'facturacionboletafactura'=> $facturacionboletafactura
            ]);
        }
        elseif($request->input('view') == 'a4pdf') {
            $boletafacturadetalle     = DB::table('s_facturacionboletafacturadetalle')
                ->join('s_producto','s_producto.id','s_facturacionboletafacturadetalle.idproducto')
                ->where('s_facturacionboletafacturadetalle.idfacturacionboletafactura',$facturacionboletafactura->id)
                ->select(
                    's_facturacionboletafacturadetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionboletafacturadetalle.id','asc')
                ->get();
            $facturacionrespuesta = facturador_respuesta('BOLETAFACTURA',$facturacionboletafactura->id);
          
            $configuracion = configuracion_facturacion($idtienda);
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionboletafactura/a4pdf',[
                'tienda'                   => $tienda,
                'facturacionboletafactura' => $facturacionboletafactura,
                'boletafacturadetalle'     => $boletafacturadetalle,
                'configuracion'            => $configuracion,
                'respuesta'                => $facturacionrespuesta
            ]);
            $a4pdf = 'PDF_A4_'.str_pad($facturacionboletafactura->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($a4pdf.'.pdf');
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
        
        if($request->input('view') == 'reenviarcomprobante'){

            $result = facturador_facturaboleta($id);

            return response()->json([
                'resultado' => $result['resultado'],
                'mensaje'   => $result['mensaje']
            ]);
        }
        elseif($request->input('view') == 'enviarcorreo'){
              $rules = [
                  'enviarcorreo_email' => 'required|email',
              ];
              $messages = [
                  'enviarcorreo_email.required' => 'El "Correo Electrónico" es Obligatorio.',
                  'enviarcorreo_email.email' => 'El "Correo Electrónico" es Invalido, ingrese otro por favor.',
              ];

              $this->validate($request,$rules,$messages);
          
          
              $facturacionboletafactura = DB::table('s_facturacionboletafactura as facturaboleta')
                  ->join('users as responsable','responsable.id','facturaboleta.idusuarioresponsable')
                  ->join('s_agencia','s_agencia.id','facturaboleta.idagencia')
                  ->where('facturaboleta.id',$request->input('idfacturacionboletafactura'))
                  ->select(
                      'facturaboleta.*',
                      'responsable.nombre as responsablenombre',
                      's_agencia.logo as agencialogo',
                       DB::raw('CONCAT(facturaboleta.cliente_numerodocumento," - ",facturaboleta.cliente_razonsocial) as cliente'),
                       DB::raw('CONCAT(facturaboleta.cliente_departamento, " , ", facturaboleta.cliente_provincia, " , ", facturaboleta.cliente_distrito) as ubigeo'),
                       DB::raw('CONCAT(facturaboleta.emisor_ruc, " - ", facturaboleta.emisor_nombrecomercial) as agencia')
                    )
                  ->first();
              $boletafacturadetalle = DB::table('s_facturacionboletafacturadetalle')
                  ->join('s_producto','s_producto.id','s_facturacionboletafacturadetalle.idproducto')
                  ->where('s_facturacionboletafacturadetalle.idfacturacionboletafactura',$facturacionboletafactura->id)
                  ->select(
                      's_facturacionboletafacturadetalle.*',
                      's_producto.codigo as productocodigo',
                      's_producto.nombre as productonombre'
                  )
                  ->orderBy('s_facturacionboletafacturadetalle.id','asc')
                  ->get();
          
              $facturacionrespuesta = facturador_respuesta('BOLETAFACTURA',$facturacionboletafactura->id);
          
              $tienda = DB::table('tienda')->whereId($idtienda)->first();
              $configuracion = configuracion_facturacion($idtienda);

              $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionboletafactura/ticketpdf',[
                  'tienda'                   => $tienda,
                  'facturacionboletafactura' => $facturacionboletafactura,
                  'boletafacturadetalle'     => $boletafacturadetalle,
                  'configuracion'            => $configuracion,
                  'respuesta'                => $facturacionrespuesta
              ]);
              $pdfa4 = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionboletafactura/a4pdf',[
                  'tienda'                   => $tienda,
                  'facturacionboletafactura' => $facturacionboletafactura,
                  'boletafacturadetalle'     => $boletafacturadetalle,
                  'configuracion'            => $configuracion,
                  'respuesta'                => $facturacionrespuesta
              ]);
          
              $output = $pdf->output();
              $output_pdfa4 = $pdfa4->output();
          
              $comprobante = '';
              if($facturacionboletafactura->venta_tipodocumento=='03'){
                  $comprobante = 'BOLETA';
              }elseif($facturacionboletafactura->venta_tipodocumento=='01'){
                  $comprobante = 'FACTURA';
              }

              $nombre = $facturacionrespuesta['facturacionrespuesta']->nombre;
              $user = array (
                 'correo' => 'ventas@kayllapi.com',
                 'nombre' => strtoupper($facturacionboletafactura->emisor_nombrecomercial),
                 'correo_destino' => $request->input('enviarcorreo_email'),
                 'titulo' => $comprobante.' '.$facturacionboletafactura->venta_serie.'-'.str_pad($facturacionboletafactura->venta_correlativo, 6, "0", STR_PAD_LEFT),
                 'pdf' => $output,
                 'pdfa4' => $output_pdfa4,
                 'nombrepdf'=>$comprobante.'_'.$facturacionboletafactura->venta_serie.'_'.str_pad($facturacionboletafactura->venta_correlativo, 6, "0", STR_PAD_LEFT).'.pdf',
                 'xml' => 'public/backoffice/tienda/'.$tienda->id.'/sunat/produccion/boletafactura/'.$nombre.'.xml',
              );
          
              Mail::send('app/email_comprobante',  [
                  'user' => $user,
                  'tienda' => $tienda,
                  'facturacionboletafactura' => $facturacionboletafactura,
                  'boletafacturadetalle' => $boletafacturadetalle,
                  'configuracion' => $configuracion,
                  'respuesta' => $facturacionrespuesta,
                ], function ($message) use ($user) {
                  $message->from($user['correo'],$user['nombre']);
                  $message->to($user['correo_destino'])->subject($user['titulo']);
                  $message->attach($user['xml']);
                  $message->attachData($user['pdf'], $user['nombrepdf'], [ 'mime' => 'application/pdf' ]);
                  $message->attachData($user['pdfa4'], $user['nombrepdf'], [ 'mime' => 'application/pdf' ]);
              });


              return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha enviado correctamente.'
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
    }
}
