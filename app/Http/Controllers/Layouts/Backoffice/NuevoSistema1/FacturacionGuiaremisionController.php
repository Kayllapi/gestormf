<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use Auth;
use Hash;
use PDF;
use DB;
use Mail;

class FacturacionGuiaremisionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);

        $tienda       = DB::table('tienda')->whereId($idtienda)->first();      
        $configuracion = configuracion_facturacion($idtienda);
       
      json_facturacionguiaremision($idtienda,$request->name_modulo);
        return view('layouts/backoffice/tienda/nuevosistema/facturacionguiaremision/index', [
            'tienda'                  => $tienda,
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
      
        $tienda         = DB::table('tienda')->whereId($idtienda)->first();
        $tipopersonas   = DB::table('tipopersona')->get();
        $motivos        = DB::table('s_sunat_motivotraslado')->get();
        $configuracion  = configuracion_comercio($idtienda);
        $configuracion_facturacion  = configuracion_facturacion($idtienda);
        $agencias       = DB::table('s_agencia')->where('idtienda',$idtienda)->where('idestadofacturacion',1)->get();

        return view('layouts/backoffice/tienda/nuevosistema/facturacionguiaremision/create',[
            'tienda'                   => $tienda,
            'configuracion'            => $configuracion,
            'tipopersonas'             => $tipopersonas,
            'motivos'                  => $motivos,
            'agencias'                 => $agencias,
            'configuracion_facturacion' => $configuracion_facturacion,
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
                'agencia'               => 'required',
                'puntopartida'          => 'required',
                'direccionpartida'      => 'required',
                'destinatario'          => 'required',
                'destinatario_ubigeo'   => 'required',
                'destinatario_direccion'=> 'required',
                'motivo'                => 'required',
                'fechatraslado'         => 'required',
                'transportista'         => 'required',
                'observacion'           => 'required',
                'productos'             => 'required'
            ];
            $messages = [
                'agencia.required'                => 'El "Remitente" es Obligatorio.',
                'puntopartida.required'           => 'El "Punto de Partida" es Obligatorio.',
                'direccionpartida.required'       => 'La "Direccion de Partida" es Obligatorio.',
                'destinatario_nombre.required'    => 'El "Destinatario" es Obligatorio.',
                'destinatario_ubigeo.required'    => 'El "Punto de LLegada" es Obligatorio.',
                'destinatario_direccion.required' => 'La "Direccion de LLegada" es Obligatorio.',
                'motivo.required'                 => 'El "Motivo" es Obligatorio.',
                'fechatraslado.required'          => 'La "Fecha de Traslado" es Obligatorio.',
                'transportista.required'          => 'El "Transportista" es Obligatorio.',
                'observacion.required'            => 'La "Observación" es Obligatorio.',
                'productos.required'              => 'Los "Productos" son Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
            
            // Capturando los PRODUCTOS
            $productos = json_decode($request->productos);
          
            if (empty($productos)) {
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Debe ingresar mínimo un producto!!.'
                ]);
            }
          
            foreach ($productos as $item) {
               if($item->cantidad <= 0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cantidad miníma es 1.'
                    ]);
                }
            }
          
            $tiendas = DB::table('tienda')
              ->join('ubigeo','ubigeo.id','tienda.idubigeo')
              ->where('tienda.id',$idtienda)
              ->select(
                  'tienda.*',
                  'tienda.direccion as tiendadireccion',
                  'ubigeo.codigo as tiendaubigeocodigo',
                  'ubigeo.distrito as tiendaubigeodistrito',
                  'ubigeo.provincia as tiendaubigeoprovincia',
                  'ubigeo.departamento as tiendaubigeodepartamento'
              )
              ->first();

                $agencia = DB::table('s_agencia')->whereId($request->input('agencia'))->first();
                $ubigeo  = DB::table('ubigeo')->whereId($request->input('puntopartida'))->first();

                // Datos del destinatario o despacho
                $tienda         = DB::table('tienda')->whereId($idtienda)->first();
                $despacho       = DB::table('users')
                    ->join('tipopersona','tipopersona.id','users.idtipopersona')
                    ->where('users.id',$request->input('destinatario'))
                    ->select(
                        'users.*',
                        'tipopersona.codigo as tipodocumento'
                    )
                    ->first();
                $despacho_serie = 'T'.str_pad($agencia->facturacion_serie, 3, "0", STR_PAD_LEFT);
                $correlativo    = DB::table('s_facturacionguiaremision')
                    ->where('s_facturacionguiaremision.emisor_ruc', $agencia ->ruc)
                    ->where('s_facturacionguiaremision.despacho_serie', $despacho_serie)
                    ->orderBy('s_facturacionguiaremision.despacho_correlativo','desc')
                    ->first();
                $despacho_correlativo = ($correlativo == '') ? 1 : $correlativo->despacho_correlativo + 1;

                // Datos del transportista
                $transportista = DB::table('users')
                    ->join('tipopersona','tipopersona.id','users.idtipopersona')
                    ->where('users.id',$request->input('transportista'))
                    ->select(
                        'users.*',
                        'tipopersona.codigo as tipodocumento'
                    )
                    ->first();
          

                // Datos del motivo o envio
                $sunat_motivotraslado = DB::table('s_sunat_motivotraslado')->whereId($request->input('motivo'))->first();
                $sunat_modalidadtraslado = DB::table('s_sunat_modalidadtraslado')->whereId(1)->first();
                $puntopartida = DB::table('ubigeo')->whereId($request->input('puntopartida'))->first();
                $puntollegada = DB::table('ubigeo')->whereId($request->input('destinatario_ubigeo'))->first();
            
            
            
            // Almacenando la guia de remision
            $idfacturacionguiaremision = DB::table('s_facturacionguiaremision')->insertGetId([
                'emisor_ruc'                            => $agencia->ruc,
                'emisor_razonsocial'                    => $agencia->razonsocial,
                'emisor_nombrecomercial'                => $agencia->nombrecomercial,
                'emisor_ubigeo'                         => $tiendas->tiendaubigeocodigo,
                'emisor_departamento'                   => $tiendas->tiendaubigeodepartamento,
                'emisor_provincia'                      => $tiendas->tiendaubigeoprovincia,
                'emisor_distrito'                       => $tiendas->tiendaubigeodistrito,
                'emisor_urbanizacion'                   => '',
                'emisor_direccion'                      => $tiendas->tiendadireccion,
              
                'despacho_tipodocumento'                => '09',
                'despacho_serie'                        => $despacho_serie,
                'despacho_correlativo'                  => $despacho_correlativo,
                'despacho_fechaemision'                 => Carbon::now(),
                'despacho_destinatario_tipodocumento'   => $despacho->tipodocumento,
                'despacho_destinatario_numerodocumento' => $despacho->identificacion,
                'despacho_destinatario_razonsocial'     => ($despacho->idtipopersona == 1) ? $despacho->apellidos.', '.$despacho->nombre : $despacho->apellidos,
                'despacho_tercero_tipodocumento'        => '',
                'despacho_tercero_numerodocumento'      => '',
                'despacho_tercero_razonsocial'          => '',
                'despacho_observacion'                  => !is_null($request->input('observacion')) ? $request->input('observacion') : '',
              
                'transporte_tipodocumento'              => 6,
                'transporte_numerodocumento'            => $agencia->ruc,
                'transporte_razonsocial'                => $agencia->nombrecomercial,
                'transporte_placa'                      => '',
                'transporte_chofertipodocumento'        => $transportista->tipodocumento,
                'transporte_choferdocumento'            => $transportista->identificacion,
              
                'envio_codigotraslado'                  => $sunat_motivotraslado->codigo,
                'envio_descripciontraslado'             => $sunat_motivotraslado->nombre,
                'envio_modtraslado'                     => $sunat_modalidadtraslado->codigo,
                'envio_fechatraslado'                   => $request->input('fechatraslado'),
                'envio_codigopuerto'                    => '',
                'envio_indtransbordo'                   => '',
                'envio_pesototal'                       => 0,
                'envio_unidadpesototal'                 => 'KGM',
                'envio_numerocontenedor'                => '',
                'envio_direccionllegadacodigoubigeo'    => $puntollegada->codigo,
                'envio_direccionllegada'                => $request->input('destinatario_direccion'),
                'envio_direccionpartidacodigoubigeo'    => $puntopartida->codigo,
                'envio_direccionpartida'                => $request->input('direccionpartida'),
              
                'idfacturacionboletafactura'            => !is_null($request->input('idfacturacion')) ? $request->input('idfacturacion') : 0,
                'idventa'                               => !is_null($request->input('idventa')) ? $request->input('idventa') : 0,
                'idcompra'                              => !is_null($request->input('idcompra')) ? $request->input('idcompra') : 0,
                'idagencia'                             => $agencia->id,
                'idtienda'                              => $idtienda,
                'idusuarioresponsable'                  => Auth::user()->id,
                'idusuariocliente'                      => $despacho->id,
                'idusuariochofer'                       => $transportista->id
            ]);
          
            foreach ($productos as $item) {
                $producto = DB::table('s_producto')
                    ->join('unidadmedida','unidadmedida.id','s_producto.idunidadmedida')
                    ->where('s_producto.id',$item->idproducto)
                    ->select(
                        's_producto.*',
                        'unidadmedida.codigo as unidadmedidacodigo'
                    )
                    ->first();
                DB::table('s_facturacionguiaremisiondetalle')->insert([
                    'cantidad'                  => $item->cantidad,
                    'unidad'                    => $producto->unidadmedidacodigo,
                    'descripcion'               => $producto->nombre,
                    'codigo'                    => $producto->codigo,
                    'codprodsunat'              => $producto->codigo,
                    'idproducto'                => $producto->id,
                    'idfacturacionguiaremision' => $idfacturacionguiaremision
                ]);
            }
          
            // Enviando a la Sunat
            $result = facturador_guiaremision($idfacturacionguiaremision);
          
            return [
                  'resultado' => $result['resultado'],
                  'mensaje'   => $result['mensaje']
            ];
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
      
        if( $id == 'showseleccionarproducto' ) {
            $producto = producto($idtienda,$request->input('idproducto'));
            if($producto['producto']==''){
                return [ 
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No existe el producto, ingrese otro código.',
                ];
            }
            return [ 
              'producto' => $producto['producto']
            ];
        }
        elseif($id == 'show-seleccionarventa') {
          
            $venta = DB::table('s_venta')
                ->where([
                    ['s_venta.idtienda', $idtienda],
                    ['s_venta.codigo', $request->input('codigo_venta')]
                ])
                ->first();
          
            if ( !is_null($venta) ) {
              
                $agencia = DB::table('s_agencia as agencia')
                    ->whereId($venta->s_idagencia)
                    ->select(
                        'agencia.*',
                        DB::raw('CONCAT(agencia.ruc, " - ", agencia.razonsocial) as agenciaCompleto')
                    )
                    ->first();
                $tienda = DB::table('tienda')
                    ->join('ubigeo', 'ubigeo.id', 'tienda.idubigeo')
                    ->where('tienda.id', $venta->idtienda)
                    ->select(
                        'tienda.*',
                        'ubigeo.nombre as ubigeonombre'
                    )
                    ->first();
                $cliente = DB::table('users')
                    ->join('ubigeo', 'ubigeo.id', 'users.idubigeo')
                    ->where('users.id', $venta->s_idusuariocliente)
                    ->select(
                        'users.*',
                        'ubigeo.nombre as ubigeonombre',
                        DB::raw('CONCAT(users.identificacion, " - ", users.apellidos, ", ", users.nombre) as nombreCompleto')
                    )
                    ->first();
                $detalle = DB::table('s_ventadetalle')
                    ->join('s_producto as product', 'product.id', 's_ventadetalle.s_idproducto')
                    ->where('s_ventadetalle.s_idventa', $venta->id)
                    ->select(
                        's_ventadetalle.*',
                        'product.nombre as nombreProduct',
                        'product.codigo as codigoProduct',
                        DB::raw('(SELECT imagen FROM s_productogaleria WHERE s_idproducto=product.id ORDER BY orden ASC LIMIT 1) as imagen')
                    )
                    ->get();
              
                return [
                    'venta'   => $venta,
                    'agencia' => $agencia,
                    'cliente' => $cliente,
                    'detalle' => $detalle,
                    'tienda'  => $tienda
                ];
              
            } else {
                return [ 'venta' => $venta ];
            }
          
        } else if ($id == 'show-seleccionarcompra') {
          
            $compra = DB::table('s_compra')
                ->where([
                    ['s_compra.idtienda', $idtienda],
                    ['s_compra.seriecorrelativo', $request->input('codigo_compra')]
                ])
                ->first();
            if ( !is_null($compra) ) {
                $agencia = DB::table('s_agencia as agencia')
                    ->where('idtienda', $compra->idtienda)
                    ->select(
                        'agencia.*',
                        DB::raw('CONCAT(agencia.ruc, " - ", agencia.razonsocial) as agenciaCompleto')
                    )
                    ->first();
                $tienda = DB::table('tienda')
                    ->join('ubigeo', 'ubigeo.id', 'tienda.idubigeo')
                    ->where('tienda.id', $compra->idtienda)
                    ->select(
                        'tienda.*',
                        'ubigeo.nombre as ubigeonombre'
                    )
                    ->first();
                $cliente = DB::table('users')
                    ->join('ubigeo', 'ubigeo.id', 'users.idubigeo')
                    ->where('users.id', $compra->s_idusuarioproveedor)
                    ->select(
                        'users.*',
                        'ubigeo.nombre as ubigeonombre',
                        DB::raw('CONCAT(users.identificacion, " - ", users.apellidos, ", ", users.nombre) as nombreCompleto')
                    )
                    ->first();
                $detalle = DB::table('s_compradetalle')
                    ->join('s_producto as product', 'product.id', 's_compradetalle.s_idproducto')
                    ->where('s_idcompra', $compra->id)
                    ->select(
                        's_compradetalle.*',
                        'product.nombre as nombreProduct',
                        'product.codigo as codigoProduct',
                        DB::raw('(SELECT imagen FROM s_productogaleria WHERE s_idproducto=product.id ORDER BY orden ASC LIMIT 1) as imagen')
                    )
                    ->get();
                return [ 
                    'compra'  => $compra,
                    'agencia' => $agencia,
                    'cliente' => $cliente,
                    'detalle' => $detalle,
                    'tienda'  => $tienda
                ];
              
            } else {
                return [ 'compra' => $compra ];
            }
          
        }
        elseif($id == 'show-seleccionarboletafactura') {
          
            $boletafactura = DB::table('s_facturacionboletafactura')
                ->where([
                    ['s_facturacionboletafactura.idtienda', $idtienda],
                    ['s_facturacionboletafactura.venta_serie', $request->input('facturador_serie')],
                    ['s_facturacionboletafactura.venta_correlativo', $request->input('facturador_correlativo')]
                ])
                ->first();
          
            if ( !is_null($boletafactura) ) {
              
                if ($boletafactura->venta_tipodocumento == '03') {
                    $comprobante = 'BOLETA';
                } else if ($boletafactura->venta_tipodocumento == '01') {
                    $comprobante = 'FACTURA';
                } else {
                    $comprobante = 'TICKET';
                }
              
                $agencia = DB::table('s_agencia as agencia')
                    ->whereId($boletafactura->idagencia)
                    ->select(
                        'agencia.*',
                        DB::raw('CONCAT(agencia.ruc, " - ", agencia.razonsocial) as agenciaCompleto')
                    )
                    ->first();
                $tienda = DB::table('tienda')
                    ->join('ubigeo', 'ubigeo.id', 'tienda.idubigeo')
                    ->where('tienda.id', $boletafactura->idtienda)
                    ->select(
                        'tienda.*',
                        'ubigeo.nombre as ubigeonombre'
                    )
                    ->first();
                $cliente = DB::table('users')
                    ->join('ubigeo', 'ubigeo.id', 'users.idubigeo')
                    ->where('users.id', $boletafactura->idusuariocliente)
                    ->select(
                        'users.*',
                        'ubigeo.nombre as ubigeonombre',
                        DB::raw('CONCAT(users.identificacion, " - ", users.apellidos, ", ", users.nombre) as nombreCompleto')
                    )
                    ->first();
                $detalle = DB::table('s_facturacionboletafacturadetalle')
                    ->join('s_producto as product', 'product.id', 's_facturacionboletafacturadetalle.idproducto')
                    ->where('s_facturacionboletafacturadetalle.idfacturacionboletafactura', $boletafactura->id)
                    ->select(
                        's_facturacionboletafacturadetalle.*',
                        'product.nombre as nombreProduct',
                        'product.codigo as codigoProduct',
                        DB::raw('(SELECT imagen FROM s_productogaleria WHERE s_idproducto=product.id ORDER BY orden ASC LIMIT 1) as imagen')
                    )
                    ->get();
                return [
                    'boletafactura' => $boletafactura,
                    'agencia'       => $agencia,
                    'cliente'       => $cliente,
                    'detalle'       => $detalle,
                    'tienda'        => $tienda
                ];
              
            } else {
                return [ 'boletafactura' => $boletafactura ];
            }
          
        } 
        elseif($id == 'showseleccionarusuario') {
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
          
        } else if ($id == 'showlistaragencia') {
          
            $agencias = DB::table('s_agencia as agencia')
                ->where('idtienda',$idtienda)
                ->where('agencia.nombrecomercial','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('idtienda',$idtienda)
                ->where('agencia.razonsocial','LIKE','%'.$request->input('buscar').'%')
                ->orWhere('idtienda',$idtienda)
                ->where('agencia.ruc','LIKE','%'.$request->input('buscar').'%')
                ->select(
                  'agencia.id as id',
                   DB::raw('CONCAT(agencia.ruc," - ",agencia.razonsocial) as text')
                )
                ->get();
            return $agencias;
          
        } else if ($id == 'showseleccionaragencia') {
          
            $agencia = DB::table('s_agencia as agencia')
                ->where('agencia.idtienda',$idtienda)
                ->where('agencia.id',$request->input('idagencia'))
                ->first();
            return [ 'agencia' => $agencia ];
          
        }
        elseif($id == 'show-selecionarserie'){
            $agencia = DB::table('s_agencia')
                ->leftJoin('ubigeo','ubigeo.id','s_agencia.idubigeo')
                ->where('s_agencia.id',$request->input('idagencia'))
                ->select(
                    's_agencia.*',
                    'ubigeo.nombre as ubigeonombre'
                )
                ->first();
            $agenciaoption = '';
            if($agencia!=''){
                $facturacion_serie = str_pad($agencia->facturacion_serie, 3, "0", STR_PAD_LEFT);
                $agenciaoption = '<option></option>
                                  <option value="B'.$facturacion_serie.'">B'.$facturacion_serie.'</option>
                                  <option value="F'.$facturacion_serie.'">F'.$facturacion_serie.'</option>';
            }
            return [ 
              'agenciaoption' => $agenciaoption,
              'agencia' => $agencia
            ];
        }
        elseif($id=='showseleccionarproductocodigo'){
            if($request->input('codigoproducto')==''){
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
        }elseif($id == 'show-moduloactualizar'){
                json_facturacionguiaremision($idtienda,$request->name_modulo);

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
      
        $facturacionguiaremision = DB::table('s_facturacionguiaremision as guiaremision')
            ->join('users as responsable','responsable.id','guiaremision.idusuarioresponsable')
            ->join('s_agencia','s_agencia.id','guiaremision.idagencia')
            ->join('ubigeo as llegadaubigeo','llegadaubigeo.codigo','guiaremision.envio_direccionllegadacodigoubigeo')
            ->join('ubigeo as partidaubigeo','partidaubigeo.codigo','guiaremision.envio_direccionpartidacodigoubigeo')
            ->leftJoin('users as transportista', 'transportista.id', 'guiaremision.idusuariochofer')
            ->where('guiaremision.id', $id)
            ->select(
                'guiaremision.*',
                'responsable.nombre as responsablenombre',
                's_agencia.logo as agencialogo',
                DB::raw('CONCAT(guiaremision.emisor_ruc, " - ", guiaremision.emisor_razonsocial) as agencia'),
                DB::raw('CONCAT(guiaremision.despacho_destinatario_numerodocumento, " - ", guiaremision.despacho_destinatario_razonsocial) as destinatario'),
                DB::raw('IF(transportista.idtipopersona=1,
                CONCAT(transportista.apellidos,", ",transportista.nombre),
                CONCAT(transportista.apellidos)) as transportista'),
                'llegadaubigeo.nombre as llegadaubigeonombre',
                'partidaubigeo.nombre as partidaubigeonombre',
            )
            ->first();
      
        $configuracion = configuracion_facturacion($idtienda);

        if ($request->input('view') == 'editar') {
          
            return view('layouts/backoffice/tienda/sistema/facturacionguiaremision/edit', [
                'facturacionguiaremision' => $facturacionguiaremision,
                'tienda'                  => $tienda
            ]);
          
        }elseif ($request->input('view') == 'detalle') {
          
            $facturacionguiaremisiondetalles = DB::table('s_facturacionguiaremisiondetalle')
                ->where('s_facturacionguiaremisiondetalle.idfacturacionguiaremision', $facturacionguiaremision->id)
                ->orderBy('s_facturacionguiaremisiondetalle.id', 'asc')
                ->get();
            $ubigeo_partida = DB::table('ubigeo')->where('ubigeo.codigo', $facturacionguiaremision->envio_direccionpartidacodigoubigeo)->first();
            $ubigeo_llegada = DB::table('ubigeo')->where('ubigeo.codigo', $facturacionguiaremision->envio_direccionllegadacodigoubigeo)->first();
            $transportista  = DB::table('users')
                ->where('identificacion', $facturacionguiaremision->transporte_choferdocumento)
                ->select(
                    'users.*',
                    DB::raw('CONCAT(users.identificacion, " - ", users.apellidos, ", ", users.nombre) as transportista')
                )
                ->first();
          
            return view('layouts/backoffice/tienda/sistema/facturacionguiaremision/detalle', [
                'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
                'facturacionguiaremision'         => $facturacionguiaremision,
                'ubigeo_partida'                  => $ubigeo_partida,
                'ubigeo_llegada'                  => $ubigeo_llegada,
                'transportista'                   => $transportista,
                'tienda'                          => $tienda
            ]);
          
        }
        elseif($request->input('view') == 'ticket') {
            return view('layouts/backoffice/tienda/nuevosistema/facturacionguiaremision/ticket',[
                'tienda' => $tienda,
                'facturacionguiaremision'=> $facturacionguiaremision
            ]);
        }elseif($request->input('view') == 'ticketpdf') {
          
            $facturacionguiaremisiondetalles = DB::table('s_facturacionguiaremisiondetalle')
                ->where('s_facturacionguiaremisiondetalle.idfacturacionguiaremision', $facturacionguiaremision->id)
                ->orderBy('s_facturacionguiaremisiondetalle.id', 'asc')
                ->get();
            $ubigeo_partida = DB::table('ubigeo')->where('ubigeo.codigo', $facturacionguiaremision->envio_direccionpartidacodigoubigeo)->first();
            $ubigeo_llegada = DB::table('ubigeo')->where('ubigeo.codigo', $facturacionguiaremision->envio_direccionllegadacodigoubigeo)->first();
            $transportista  = DB::table('users')
                ->where('identificacion', $facturacionguiaremision->transporte_choferdocumento)
                ->select(
                    'users.*',
                    DB::raw('CONCAT(users.identificacion, " - ", users.apellidos, ", ", users.nombre) as transportista')
                )
                ->first();
            $facturacionrespuesta = DB::table('s_facturacionrespuesta')
                  ->where('s_facturacionrespuesta.s_idfacturacionguiaremision',$facturacionguiaremision->id)
                  ->first();
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionguiaremision/ticketpdf',[
                'tienda'                          => $tienda,
                'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
                'facturacionguiaremision'         => $facturacionguiaremision,
                'ubigeo_partida'                  => $ubigeo_partida,
                'ubigeo_llegada'                  => $ubigeo_llegada,
                'transportista'                   => $transportista,
                'configuracion'                   => $configuracion,
                'respuesta'                       => $facturacionrespuesta
            ]);
            $ticket = 'Ticket_'.str_pad($facturacionguiaremision->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');
        }
        elseif($request->input('view') == 'a4pdf') {
            $facturacionguiaremisiondetalles = DB::table('s_facturacionguiaremisiondetalle')
                ->where('s_facturacionguiaremisiondetalle.idfacturacionguiaremision', $facturacionguiaremision->id)
                ->orderBy('s_facturacionguiaremisiondetalle.id', 'asc')
                ->get();
            $ubigeo_partida = DB::table('ubigeo')->where('ubigeo.codigo', $facturacionguiaremision->envio_direccionpartidacodigoubigeo)->first();
            $ubigeo_llegada = DB::table('ubigeo')->where('ubigeo.codigo', $facturacionguiaremision->envio_direccionllegadacodigoubigeo)->first();
            $transportista  = DB::table('users')
                ->where('identificacion', $facturacionguiaremision->transporte_choferdocumento)
                ->select(
                    'users.*',
                    DB::raw('CONCAT(users.identificacion, " - ", users.apellidos, ", ", users.nombre) as transportista')
                )
                ->first();
            $facturacionrespuesta = DB::table('s_facturacionrespuesta')
                  ->where('s_facturacionrespuesta.s_idfacturacionguiaremision',$facturacionguiaremision->id)
                  ->first();
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionguiaremision/a4pdf',[
                'tienda'                          => $tienda,
                'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
                'facturacionguiaremision'         => $facturacionguiaremision,
                'ubigeo_partida'                  => $ubigeo_partida,
                'ubigeo_llegada'                  => $ubigeo_llegada,
                'transportista'                   => $transportista,
                'configuracion'                   => $configuracion,
                'respuesta'                       => $facturacionrespuesta
            ]);
            $a4pdf = 'PDF_A4_'.str_pad($facturacionguiaremision->id, 8, "0", STR_PAD_LEFT);
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
        if($request->input('view') == 'enviarcorreo'){
              $rules = [
                  'enviarcorreo_email' => 'required|email',
              ];
              $messages = [
                  'enviarcorreo_email.required' => 'El "Correo Electrónico" es Obligatorio.',
                  'enviarcorreo_email.email' => 'El "Correo Electrónico" es Invalido, ingrese otro por favor.',
              ];

              $this->validate($request,$rules,$messages);
          
              $facturacionguiaremision = DB::table('s_facturacionguiaremision as guiaremision')
                  ->join('users as responsable','responsable.id','guiaremision.idusuarioresponsable')
                  ->join('s_agencia','s_agencia.id','guiaremision.idagencia')
                  ->join('ubigeo as llegadaubigeo','llegadaubigeo.codigo','guiaremision.envio_direccionllegadacodigoubigeo')
                  ->join('ubigeo as partidaubigeo','partidaubigeo.codigo','guiaremision.envio_direccionpartidacodigoubigeo')
                  ->leftJoin('users as transportista', 'transportista.id', 'guiaremision.idusuariochofer')
                  ->where('guiaremision.id', $request->input('idfacturacionguiaremision'))
                  ->select(
                      'guiaremision.*',
                      'responsable.nombre as responsablenombre',
                      's_agencia.logo as agencialogo',
                      DB::raw('CONCAT(guiaremision.emisor_ruc, " - ", guiaremision.emisor_razonsocial) as agencia'),
                      DB::raw('CONCAT(guiaremision.despacho_destinatario_numerodocumento, " - ", guiaremision.despacho_destinatario_razonsocial) as destinatario'),
                      DB::raw('IF(transportista.idtipopersona=1,
                      CONCAT(transportista.apellidos,", ",transportista.nombre),
                      CONCAT(transportista.apellidos)) as transportista'),
                      'llegadaubigeo.nombre as llegadaubigeonombre',
                      'partidaubigeo.nombre as partidaubigeonombre',
                  )
                  ->first();
          
              $facturacionguiaremisiondetalles = DB::table('s_facturacionguiaremisiondetalle')
                ->where('s_facturacionguiaremisiondetalle.idfacturacionguiaremision', $facturacionguiaremision->id)
                ->orderBy('s_facturacionguiaremisiondetalle.id', 'asc')
                ->get();
          
              $ubigeo_partida = DB::table('ubigeo')->where('ubigeo.codigo', $facturacionguiaremision->envio_direccionpartidacodigoubigeo)->first();
              $ubigeo_llegada = DB::table('ubigeo')->where('ubigeo.codigo', $facturacionguiaremision->envio_direccionllegadacodigoubigeo)->first();
              $transportista  = DB::table('users')
                ->where('identificacion', $facturacionguiaremision->transporte_choferdocumento)
                ->select(
                    'users.*',
                    DB::raw('CONCAT(users.identificacion, " - ", users.apellidos, ", ", users.nombre) as transportista')
                )
                ->first();
          
              $facturacionrespuesta = DB::table('s_facturacionrespuesta')
                  ->where('s_facturacionrespuesta.s_idfacturacionguiaremision',$facturacionguiaremision->id)
                  ->first();
          
              if($facturacionrespuesta==''){
                  return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No se puede enviar un comprobante con error, revise por favor.'
                  ]);
              }
          
              $tienda = DB::table('tienda')->whereId($idtienda)->first();
              $configuracion = configuracion_facturacion($idtienda);

              $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionguiaremision/ticketpdf',[
                  'tienda'                   => $tienda,
                  'facturacionguiaremision' => $facturacionguiaremision,
                  'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
                  'configuracion'            => $configuracion,
                  'respuesta'                => $facturacionrespuesta,
              ]);
          
              $a4pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionguiaremision/a4pdf',[
                  'tienda'                          => $tienda,
                  'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
                  'facturacionguiaremision'         => $facturacionguiaremision,
                  'ubigeo_partida'                  => $ubigeo_partida,
                  'ubigeo_llegada'                  => $ubigeo_llegada,
                  'transportista'                   => $transportista,
                  'configuracion'                   => $configuracion,
                  'respuesta'                       => $facturacionrespuesta
              ]);
          
              $output = $pdf->output();
              $a4_output = $a4pdf->output();

              $user = array (
                 'correo' => 'ventas@kayllapi.com',
                 'nombre' => strtoupper($facturacionguiaremision->emisor_nombrecomercial),
                 'correo_destino' => $request->input('enviarcorreo_email'),
                 'titulo' => 'GUIA REMISIÓN '.$facturacionguiaremision->despacho_serie.'-'.str_pad($facturacionguiaremision->despacho_correlativo, 6, "0", STR_PAD_LEFT),
                 'pdf' => $output,
                 'a4pdf' => $a4_output,
                 'nombrepdf'=>'GUIA_REMISION_'.$facturacionguiaremision->despacho_serie.'_'.str_pad($facturacionguiaremision->despacho_correlativo, 6, "0", STR_PAD_LEFT).'.pdf',
                 'xml' => 'public/backoffice/tienda/'.$tienda->id.'/sunat/produccion/guiaremision/'.$facturacionrespuesta->nombre.'.xml',
              );

              Mail::send('app/email_guiaremision',  [
                  'user' => $user,
                  'tienda' => $tienda,
                  'facturacionguiaremision' => $facturacionguiaremision,
                  'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
                  'configuracion' => $configuracion,
                  'respuesta' => $facturacionrespuesta,
                ], function ($message) use ($user) {
                  $message->from($user['correo'],$user['nombre']);
                  $message->to($user['correo_destino'])->subject($user['titulo']);
                  $message->attach($user['xml']);
                  $message->attachData($user['pdf'], $user['nombrepdf'], [ 'mime' => 'application/pdf' ]);
                  $message->attachData($user['a4pdf'], $user['nombrepdf'], [ 'mime' => 'application/pdf' ]);
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
