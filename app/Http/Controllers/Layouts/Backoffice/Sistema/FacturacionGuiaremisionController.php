<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

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
        $tienda        = DB::table('tienda')->whereId($idtienda)->first();      

        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/facturacionguiaremision/tabla',[
                'tienda' => $tienda,
            ]);
        }
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
        $motivos        = DB::table('s_sunat_motivotraslado')->get();
        /*
      
        $guia = new \stdClass();
        //DATOS EMISOR
        $guia->setRucEmisor = '20161515648';
        $guia->setRazonSocialEmisor = 'JHON TOSCANO S.A.C.';

        //DATOS DE TRANSPORTE
        $guia->setTipoDocTransporte = '6';
        $guia->setNumDocTransporte = '20000000002';
        $guia->setRznSocialTransporte = 'TRANSPORTES S.A.C';
        $guia->setNroMtcTransporte = '0001';

        //DATOS DEL ENVIO
        $guia->setCodTraslado = '01'; // Cat.20 - Venta
        $guia->setModTraslado = '01'; // Cat.18 - Transp. Publico
        $guia->setFecTraslado = '2023-01-25';
        $guia->setPesoTotal = 12.5;
        $guia->setUndPesoTotal = 'KGM'; // SOLO KGM y TNE
        $guia->setLlegadaUbigeo = '150101';
        $guia->setLlegadaDireccion = 'AV PANAMERICANA SUR 323';
        $guia->setPartidaUbigeo = '150203';
        $guia->setPartidaDireccion = 'AV LA VICTORIA 240';

        // DATOS DE LA GUIA REMISION
        $guia->setSerie = 'T001';
        $guia->setCorrelativo = '1';
        $guia->setFechaEmision = '2023-01-25';

        // DATOS DEL DESTINATARIO
        $guia->setTipoDocDestinatario = '6';
        $guia->setNumDocDestinatario = '20000000002';
        $guia->setRznSocialDestinatario = 'EMPRESA DESTINO';

        // ITEMS DE LA GUIA
        $array_items = [
            [
                'cantidad' => 2,
                'codigoUndidad' => 'ZZ',
                'descripcion' => 'MI PRODUCTO DE PRUEBA',
                'codigoProducto' => '001'
            ],
            [
                'cantidad' => 3,
                'codigoUndidad' => 'UND',
                'descripcion' => 'MI PRODUCTO DE PRUEBA 2',
                'codigoProducto' => '002'
            ]
        ];
        $guia->items = $array_items;

        $cdr = emitir_gre($guia);
        
        $qr_image = generar_qr_bacon($cdr['qr']);
        */
      

      
        if($request->view == 'registrar') {
            return view(sistema_view().'/facturacionguiaremision/create',[
                'tienda' => $tienda,
                'motivos' => $motivos,
            ]);
        }
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
                // 'transportista'         => 'required',
                'peso_neto_gre'           => 'required',
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
                // 'transportista.required'          => 'El "Transportista" es Obligatorio.',
                'peso_neto_gre.required'            => 'El "Peso Total" es Obligatorio.',
            ];
            if( $request->input('idmodalidadtraslado') == 1 ){
              $rules['transportista']              = 'required';
              $messages['transportista.required']  = 'El campo "Empresa de Transporte" es obligatorio.';

            }
            else if( $request->input('idmodalidadtraslado') == 2 ){
              $rules['transportista']                  = 'required';
            //   $rules['clienteidentificacion']               = 'min:8';
              $rules['placavehiculoprincipal']              = 'min:6|required';

              $messages['transportista.required']      = 'El campo "Conductor" es obligatorio.';
            //   $messages['clienteidentificacion.min']        = 'Deberia seleccionar una PERSONA NATURAL (DNI)';

              $messages['placavehiculoprincipal.required']  = 'El campo "Placa Principal" es obligatorio.';
              $messages['placavehiculoprincipal.min']       = 'El campo "Placa Principal" debe tener 6 caracteres.';

              if( $request->placavehiculosecundario!='' ){
                $rules['placavehiculosecundario']             = 'min:6|required';
                $messages['placavehiculosecundario.required'] = 'El campo "Placa Secundario" es obligatorio.';
                $messages['placavehiculosecundario.min']      = 'El campo "Placa Secundario" debe tener 6 caracteres.';

              }

            }
          
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
      
            $facturacionboletafactura = DB::table('s_facturacionboletafactura')->whereId($request->input('factura_boleta'))->first();
            $list = explode('B',$facturacionboletafactura->venta_serie);
            $despacho_serie = 'T'.str_pad(intval($list[1]), 3, "0", STR_PAD_LEFT);


                //$agencia = DB::table('s_agencia')->whereId($request->input('agencia'))->first();
                $ubigeo  = DB::table('s_ubigeo')->whereId($request->input('puntopartida'))->first();

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
                //$despacho_serie = 'T'.str_pad($agencia->facturacion_serie, 3, "0", STR_PAD_LEFT);
                $correlativo    = DB::table('s_facturacionguiaremision')
                    ->where('s_facturacionguiaremision.emisor_ruc', $facturacionboletafactura->emisor_ruc)
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
              if( $request->input('idmodalidadtraslado') == 1 && $transportista->idtipopersona==1 ){
                return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje'   => 'EL TRANSPORTISTA DEBE SER UNA IMPRESA CON RUC 20'
                ]);
              }
              
                // Datos del motivo o envio
            $sunat_motivotraslado = DB::table('s_sunat_motivotraslado')->whereId($request->input('motivo'))->first();
            $sunat_modalidadtraslado = DB::table('s_sunat_modalidadtraslado')->whereId($request->input('idmodalidadtraslado'))->first();
            $puntopartida = DB::table('s_ubigeo')->whereId($request->input('puntopartida'))->first();
            $puntollegada = DB::table('s_ubigeo')->whereId($request->input('destinatario_ubigeo'))->first();
            
            
            
            // Almacenando la guia de remision
            $idfacturacionguiaremision = DB::table('s_facturacionguiaremision')->insertGetId([
                'emisor_ruc'                            => $facturacionboletafactura->emisor_ruc,
                'emisor_razonsocial'                    => $facturacionboletafactura->emisor_razonsocial,
                'emisor_nombrecomercial'                => $facturacionboletafactura->emisor_nombrecomercial,
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
                'despacho_destinatario_razonsocial'     => $despacho->nombrecompleto,
                'despacho_tercero_tipodocumento'        => '',
                'despacho_tercero_numerodocumento'      => '',
                'despacho_tercero_razonsocial'          => '',
                'despacho_observacion'                  => !is_null($request->input('observacion')) ? $request->input('observacion') : '',
              
                'transporte_tipodocumento'              => 6,
                'transporte_numerodocumento'            => $request->input('idmodalidadtraslado') == 1 ? $transportista->identificacion : '',
                'transporte_razonsocial'                => $request->input('idmodalidadtraslado') == 1 ? $transportista->nombre : '',
              
                'transporte_placa'                      => $request->input('idmodalidadtraslado') != 1 ? $request->placavehiculoprincipal: '000000',
                'transporte_chofertipodocumento'        => $transportista->tipodocumento,
                'transporte_choferdocumento'            => $transportista->identificacion,
             
                'transporte_chofernombres'              => $transportista->nombre,
                'transporte_choferapellidos'            => $transportista->apellidopaterno.' '.$transportista->apellidomaterno,
                'transporte_choferlicencia'             => $request->licencia_conductor,
              
                'envio_codigotraslado'                  => $sunat_motivotraslado->codigo,
                'envio_descripciontraslado'             => $sunat_motivotraslado->nombre,
                'envio_modtraslado'                     => $sunat_modalidadtraslado->codigo,
                'envio_fechatraslado'                   => $request->input('fechatraslado'),
                'envio_codigopuerto'                    => '',
                'envio_indtransbordo'                   => '',
                'envio_pesototal'                       => $request->input('peso_neto_gre'),
                'envio_unidadpesototal'                 => 'KGM', // KGM !! TNE
                'envio_numerocontenedor'                => '',
                'envio_direccionllegadacodigoubigeo'    => $puntollegada->codigo,
                'envio_direccionllegada'                => $request->input('destinatario_direccion'),
                'envio_direccionpartidacodigoubigeo'    => $puntopartida->codigo,
                'envio_direccionpartida'                => $request->input('direccionpartida'),
              
                'idfacturacionboletafactura'            => !is_null($request->input('idfacturacion')) ? $request->input('idfacturacion') : 0,
                'idventa'                               => !is_null($request->input('idventa')) ? $request->input('idventa') : 0,
                'idcompra'                              => !is_null($request->input('idcompra')) ? $request->input('idcompra') : 0,
                'idagencia'                             => $request->input('agencia'),
                'idtienda'                              => $idtienda,
                'idusuarioresponsable'                  => Auth::user()->id,
                'idusuariocliente'                      => $despacho->id,
                'idusuariochofer'                       => $transportista->id
            ]);
          
            foreach ($productos as $item) {
                $producto = DB::table('s_producto')
                    ->join('unidadmedida','unidadmedida.id','s_producto.s_idunidadmedida')
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
                    'idfacturacionguiaremision' => $idfacturacionguiaremision,
                    'idtienda'                  => $idtienda,
                ]);
            }
          
            // Enviando a la Sunat
            $result = facturador_guiaremision_api($idfacturacionguiaremision);
 
            
            return [
                  'resultado' => $result['tipo'],
                  'mensaje'   => $result['mensaje'],
                  'idfacturacionguiaremision' => $idfacturacionguiaremision
            ];
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  
    
    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);

        if($id=='show_table'){
            $tienda        = DB::table('tienda')->whereId($idtienda)->first();  
            $guiaremision  = DB::table('s_facturacionguiaremision')
                ->join('users as responsable', 'responsable.id', 's_facturacionguiaremision.idusuarioresponsable')
                ->leftJoin('users as transportista', 'transportista.id', 's_facturacionguiaremision.idusuariochofer')
                ->join('s_sunat_motivotraslado', 's_sunat_motivotraslado.codigo', 's_facturacionguiaremision.envio_modtraslado')
                ->where('s_facturacionguiaremision.idtienda', $tienda->id)
                ->select(
                    's_facturacionguiaremision.*',
                    'responsable.nombre as responsablenombre',
                    's_sunat_motivotraslado.nombre as motivotrasladonombre',
                    DB::raw('IF(transportista.idtipopersona=1,
                    CONCAT(transportista.nombrecompleto),
                    CONCAT(transportista.nombrecompleto)) as transportista'),
                )
                ->orderBy('s_facturacionguiaremision.id','desc')
                ->paginate($request->length,'*',null,($request->start/$request->length)+1);
        
            $tabla = [];
        
            foreach($guiaremision as $value){
                $serie_corre= $value->despacho_serie.' - '.str_pad($value->despacho_correlativo, 8, "0", STR_PAD_LEFT);
                $fecha_emi = date_format(date_create($value->despacho_fechaemision),"d/m/Y h:i:s A");
                $fecha_tras = date_format(date_create($value->envio_fechatraslado),"d/m/Y");
                
                $tabla[]=[
                    'id'  => $value->id,
                    'serie' => $value->despacho_serie,
                    'correlativo' => $value->despacho_correlativo,
                    'fecha_emision' => $fecha_emi,
                    'ruc_emisor' => $value->emisor_ruc,
                    'razon_social_emisor' => $value->emisor_razonsocial,
                    'ruc_destinatario' => $value->despacho_destinatario_numerodocumento,
                    'razon_social_destinatario' => $value->despacho_destinatario_razonsocial,
                    'descripcion_traslado' => $value->envio_descripciontraslado,
                    'transporte_numerodocumento' => $value->transporte_numerodocumento,
                    'transporte_razonsocial' => $value->transporte_razonsocial,
                    'responsable' => $value->responsablenombre,
                    'opcion' => [
                    [
                        'nombre' => 'Comprobantes',
                        'onclick' => '/'.$idtienda.'/facturacionguiaremision/'.$value->id.'/edit?view=ticket',
                        'icono' => 'receipt',
                    ]
                    ],
                ];
            }
            
            return response()->json([
                'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $guiaremision->total(),
                'data'            => $tabla,
            ]);
        }
        else if( $id == 'showseleccionarproducto' ) {
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
                        DB::raw('CONCAT(users.identificacion, " - ", users.nombrecompleto) as nombreCompleto')
                    )
                    ->first();
                $detalle = DB::table('s_ventadetalle')
                    ->join('s_producto as product', 'product.id', 's_ventadetalle.s_idproducto')
                    ->where('s_ventadetalle.s_idventa', $venta->id)
                    ->select(
                        's_ventadetalle.*',
                        'product.nombre as nombreProduct',
                        'product.codigo as codigoProduct',
                        'product.imagen as imagen',
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
          
        } 
        else if ($id == 'show-seleccionarcompra') {
          
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
                        DB::raw('CONCAT(users.identificacion, " - ", users.nombrecompleto) as nombreCompleto')
                    )
                    ->first();
                $detalle = DB::table('s_compradetalle')
                    ->join('s_producto as product', 'product.id', 's_compradetalle.s_idproducto')
                    ->where('s_idcompra', $compra->id)
                    ->select(
                        's_compradetalle.*',
                        'product.nombre as nombreProduct',
                        'product.codigo as codigoProduct',
                        'product.imagen as imagen',
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
                    ['s_facturacionboletafactura.id', $request->idfactura]
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
                    ->leftJoin('ubigeo', 'ubigeo.id', 'users.idubigeo')
                    ->where('users.id', $boletafactura->idusuariocliente)
                    ->select(
                        'users.*',
                        'ubigeo.nombre as ubigeonombre',
                        DB::raw('CONCAT(users.identificacion, " - ", users.nombrecompleto) as nombreCompleto')
                    )
                    ->first();
              
                $detalle = DB::table('s_facturacionboletafacturadetalle')
                    ->join('s_producto as product', 'product.id', 's_facturacionboletafacturadetalle.idproducto')
                    ->where('s_facturacionboletafacturadetalle.idfacturacionboletafactura', $boletafactura->id)
                    ->select(
                        's_facturacionboletafacturadetalle.*',
                        'product.nombre as nombreProduct',
                        'product.codigo as codigoProduct',
                        'product.imagen as imagen',
                    )
                    ->get();
                    
                $html_detalle = '';
                $num = 1;
                foreach($detalle as $item){
                  $guia_emitidas = DB::table('s_facturacionguiaremision')
                                    ->join('s_facturacionguiaremisiondetalle','s_facturacionguiaremisiondetalle.idfacturacionguiaremision','s_facturacionguiaremision.id')
                                    ->where('s_facturacionguiaremision.idfacturacionboletafactura',$boletafactura->id)
                                    ->where('s_facturacionguiaremisiondetalle.idproducto',$item->idproducto)
                                    ->sum('s_facturacionguiaremisiondetalle.cantidad');
                    
                  $cantidad_emision = $item->cantidad - $guia_emitidas;

                //   dump($cantidad_emision);

                  if( $cantidad_emision > 0 ){
                    
                    $html_detalle .= '<tr id="'.$num.'" idproducto="'.$item->idproducto.'" nombreproducto="'.$item->codigoproducto.' - '.$item->descripcion.'" >
                                      <td>'.$item->codigoproducto.'</td>
                                      <td>'.$item->descripcion.'</td>
                                      <td>'.$cantidad_emision.'</td>
                                      <td class="mx-td-input"><input class="form-control" id="productCant'.$num.'" type="number" value="'.$cantidad_emision.'" onkeyup="calcularmonto()" onchange="calcularmonto()"></td>
                                      <td><a id="del'.$num.'" href="javascript:;" onclick="eliminarproducto('.$num.')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>
                                    </tr>';
                    $num++;
                  }
                  
                }
              if( $num == 1){
                return [
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Todos los Productos fueron entregados'
                ];
              }else{
                
                return [
                    'resultado' => 'CORRECTO',
                    'boletafactura' => $boletafactura,
                    'agencia'       => $agencia,
                    'cliente'       => $cliente,
                    'detalle'       => $detalle,
                    'html_detalle'  => $html_detalle,
                    'tienda'        => $tienda
                ];
              }
              

                
              
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
          
        } 
        else if ($id == 'showlistaragencia') {
          
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
          
        } 
        else if ($id == 'showseleccionaragencia') {
          
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
                    's_producto.imagen as imagen',
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
        }
      
    }
    public function edit(Request $request, $idtienda, $id)
    {
//         $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $facturacionguiaremision = DB::table('s_facturacionguiaremision as guiaremision')
            ->join('users as responsable','responsable.id','guiaremision.idusuarioresponsable')
            ->join('s_agencia','s_agencia.id','guiaremision.idagencia')
            ->join('ubigeo as llegadaubigeo','llegadaubigeo.codigo','guiaremision.envio_direccionllegadacodigoubigeo')
            ->join('ubigeo as partidaubigeo','partidaubigeo.codigo','guiaremision.envio_direccionpartidacodigoubigeo')
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','guiaremision.idfacturacionrespuesta')
            ->leftJoin('users as transportista', 'transportista.id', 'guiaremision.idusuariochofer')
            ->where('guiaremision.id', $id)
            ->select(
                'guiaremision.*',
                's_facturacionrespuesta.codigo as respuestacodigo',
                's_facturacionrespuesta.estado as respuestaestado',
                's_facturacionrespuesta.mensaje as respuestamensaje',
                's_facturacionrespuesta.nombre as respuestanombre',
                's_facturacionrespuesta.qr as respuestaqr',
                'responsable.nombre as responsablenombre',
                's_agencia.logo as agencialogo',
                DB::raw('CONCAT(guiaremision.emisor_ruc, " - ", guiaremision.emisor_razonsocial) as agencia'),
                DB::raw('CONCAT(guiaremision.despacho_destinatario_numerodocumento, " - ", guiaremision.despacho_destinatario_razonsocial) as destinatario'),
                DB::raw('IF(transportista.idtipopersona=1,
                CONCAT(transportista.nombrecompleto),
                CONCAT(transportista.nombrecompleto)) as transportista'),
                'llegadaubigeo.nombre as llegadaubigeonombre',
                'partidaubigeo.nombre as partidaubigeonombre',
            )
            ->first();
      
        $configuracion = configuracion_facturacion($idtienda);
        
        if ($request->input('view') == 'editar') {
          
            return view(sistema_view().'/facturacionguiaremision/edit', [
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
                    DB::raw('CONCAT(users.identificacion, " - ", users.nombrecompleto) as transportista')
                )
                ->first();
          
            return view(sistema_view().'/facturacionguiaremision/detalle', [
                'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
                'facturacionguiaremision'         => $facturacionguiaremision,
                'ubigeo_partida'                  => $ubigeo_partida,
                'ubigeo_llegada'                  => $ubigeo_llegada,
                'transportista'                   => $transportista,
                'tienda'                          => $tienda
            ]);
          
        }
        elseif($request->input('view') == 'ticket') {
          /*  
          $ticket = new \stdClass();
          //DATOS EMISOR
          $ticket->tipo_fuente = configuracion($tienda->id,'sistema_tipoletra')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_tipoletra')['valor']:'Helvetica';
          $ticket->ancho_ticket = configuracion($tienda->id,'sistema_anchoticket')['resultado']=='CORRECTO'?(configuracion($tienda->id,'sistema_anchoticket')['valor']-1):'8'.'cm';
          $ticket->ruc_emision = $facturacionguiaremision->emisor_ruc;
          $ticket->razonsocial_emisor = strtoupper($facturacionguiaremision->emisor_razonsocial);
          $ticket->logotipo = url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacionguiaremision->agencialogo);
          $ticket->direccion_emisor = strtoupper($facturacionguiaremision->emisor_direccion);
          $ticket->ubigeo_emisor = strtoupper($facturacionguiaremision->emisor_distrito.' - '.$facturacionguiaremision->emisor_provincia.' - '.$facturacionguiaremision->emisor_departamento);
          
          $ticket->tipo_documento = getTipoDocumento( $facturacionguiaremision->despacho_tipodocumento );
          $ticket->serie_documento = $facturacionguiaremision->despacho_serie;
          $ticket->correlativo_documento = $facturacionguiaremision->despacho_correlativo;
          $ticket->fechaemision = $facturacionguiaremision->despacho_fechaemision;
          
          
          
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
                    DB::raw('CONCAT(users.identificacion, " - ", users.nombrecompleto) as transportista')
                )
                ->first();
            $facturacionrespuesta = DB::table('s_facturacionrespuesta')
                  ->where('s_facturacionrespuesta.s_idfacturacionguiaremision',$facturacionguiaremision->id)
                  ->first();
          
            return view(sistema_view().'/facturacionguiaremision/comprobante',[
                'ticket'                          => $ticket,
                'tienda'                          => $tienda,
                'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
                'facturacionguiaremision'         => $facturacionguiaremision,
                'ubigeo_partida'                  => $ubigeo_partida,
                'ubigeo_llegada'                  => $ubigeo_llegada,
                'transportista'                   => $transportista,
                'configuracion'                   => $configuracion,
                'respuesta'                       => $facturacionrespuesta
            ]);
          */
            return view(sistema_view().'/facturacionguiaremision/ticket',[
                'tienda' => $tienda,
                'facturacionguiaremision'=> $facturacionguiaremision
            ]);
        }
        elseif($request->input('view') == 'ticketpdf') {
            
            $ticket = new \stdClass();
            $ticket->tipo_fuente = configuracion($tienda->id,'sistema_tipoletra')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_tipoletra')['valor']:'Helvetica';
            $ticket->ancho_ticket = configuracion($tienda->id,'sistema_anchoticket')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_anchoticket')['valor']:'7';
            $ticket->ruc_emision = $facturacionguiaremision->emisor_ruc;
            $ticket->razonsocial_emisor = strtoupper($facturacionguiaremision->emisor_razonsocial);
            $ticket->logotipo = url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacionguiaremision->agencialogo);
            $ticket->direccion_emisor = strtoupper($facturacionguiaremision->emisor_direccion);
            $ticket->ubigeo_emisor = strtoupper($facturacionguiaremision->emisor_distrito.' - '.$facturacionguiaremision->emisor_provincia.' - '.$facturacionguiaremision->emisor_departamento);
            
            $ticket->tipo_documento = getTipoDocumento( $facturacionguiaremision->despacho_tipodocumento );
            $ticket->serie_documento = $facturacionguiaremision->despacho_serie;
            $ticket->correlativo_documento = $facturacionguiaremision->despacho_correlativo;
            $ticket->fechaemision = $facturacionguiaremision->despacho_fechaemision;
            
          
          
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
                    DB::raw('CONCAT(users.identificacion, " - ", users.nombrecompleto) as transportista')
                )
                ->first();
            $facturacionrespuesta = DB::table('s_facturacionrespuesta')
                  ->where('s_facturacionrespuesta.s_idfacturacionguiaremision',$facturacionguiaremision->id)
                  ->first();
            $agencia = DB::table('s_agencia')->whereId($facturacionguiaremision->idagencia)->first();
          
            $pdf = PDF::loadView(sistema_view().'/facturacionguiaremision/ticketpdf',[
                'ticket'                          => $ticket,
                'agencia'                         => $agencia,
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
                    DB::raw('CONCAT(users.identificacion, " - ", users.nombrecompleto) as transportista')
                )
                ->first();
            $facturacionrespuesta = DB::table('s_facturacionrespuesta')
                  ->where('s_facturacionrespuesta.s_idfacturacionguiaremision',$facturacionguiaremision->id)
                  ->first();
          
            $agencia = DB::table('s_agencia')->whereId($facturacionguiaremision->idagencia)->first();
            $pdf = PDF::loadView(sistema_view().'/facturacionguiaremision/a4pdf',[
                'tienda'                          => $tienda,
                'agencia'                         => $agencia,
                'facturacionguiaremision'         => $facturacionguiaremision,
                'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
                'ubigeo_partida'                  => $ubigeo_partida,
                'ubigeo_llegada'                  => $ubigeo_llegada,
                'transportista'                   => $transportista,
                'configuracion'                   => $configuracion,
                'respuesta'                       => $facturacionrespuesta
            ]);
            $a4pdf = 'PDF_A4_'.str_pad($facturacionguiaremision->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($a4pdf.'.pdf');
        }
        elseif($request->input('view') == 'a5pdf') {
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
                    DB::raw('CONCAT(users.identificacion, " - ", users.nombrecompleto) as transportista')
                )
                ->first();
            $facturacionrespuesta = DB::table('s_facturacionrespuesta')
                  ->where('s_facturacionrespuesta.s_idfacturacionguiaremision',$facturacionguiaremision->id)
                  ->first();
          
            $agencia = DB::table('s_agencia')->whereId($facturacionguiaremision->idagencia)->first();
            $pdf = PDF::loadView(sistema_view().'/facturacionguiaremision/a5pdf',[
                'tienda'                          => $tienda,
                'agencia'                         => $agencia,
                'facturacionguiaremision'         => $facturacionguiaremision,
                'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
                'ubigeo_partida'                  => $ubigeo_partida,
                'ubigeo_llegada'                  => $ubigeo_llegada,
                'transportista'                   => $transportista,
                'configuracion'                   => $configuracion,
                'respuesta'                       => $facturacionrespuesta
            ]);
            $a5pdf = 'PDF_A5_'.str_pad($facturacionguiaremision->id, 8, "0", STR_PAD_LEFT);
            $pdf->setPaper('a4','landscape');
            return $pdf->stream($a5pdf.'.pdf');
            
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
                ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','guiaremision.idfacturacionrespuesta')
                ->leftJoin('users as transportista', 'transportista.id', 'guiaremision.idusuariochofer')
                ->where('guiaremision.id', $request->input('idfacturacionguiaremision'))
                ->select(
                    'guiaremision.*',
                    'responsable.nombre as responsablenombre',
                    's_facturacionrespuesta.codigo as respuestacodigo',
                    's_facturacionrespuesta.estado as respuestaestado',
                    's_facturacionrespuesta.mensaje as respuestamensaje',
                    's_facturacionrespuesta.nombre as respuestanombre',
                    's_facturacionrespuesta.qr as respuestaqr',
                    's_agencia.logo as agencialogo',
                    DB::raw('CONCAT(guiaremision.emisor_ruc, " - ", guiaremision.emisor_razonsocial) as agencia'),
                    DB::raw('CONCAT(guiaremision.despacho_destinatario_numerodocumento, " - ", guiaremision.despacho_destinatario_razonsocial) as destinatario'),
                    DB::raw('IF(transportista.idtipopersona=1,
                    CONCAT(transportista.nombrecompleto),
                    CONCAT(transportista.nombrecompleto)) as transportista'),
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
                DB::raw('CONCAT(users.identificacion, " - ", users.nombrecompleto) as transportista')
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

            //   $pdf = PDF::loadView(sistema_view().'/facturacionguiaremision/ticketpdf',[
            //       'tienda'                   => $tienda,
            //       'facturacionguiaremision' => $facturacionguiaremision,
            //       'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
            //       'configuracion'            => $configuracion,
            //       'respuesta'                => $facturacionrespuesta,
            //   ]);
          
            $agencia = DB::table('s_agencia')->whereId($facturacionguiaremision->idagencia)->first();
            $a4pdf = PDF::loadView(sistema_view().'/facturacionguiaremision/a4pdf',[
                'tienda'                          => $tienda,
                'agencia'                         => $agencia,
                'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
                'facturacionguiaremision'         => $facturacionguiaremision,
                'ubigeo_partida'                  => $ubigeo_partida,
                'ubigeo_llegada'                  => $ubigeo_llegada,
                'transportista'                   => $transportista,
                'configuracion'                   => $configuracion,
                'respuesta'                       => $facturacionrespuesta
            ]);
          
            //   $output = $pdf->output();
              $a4_output = $a4pdf->output();

              $user = array (
                'correo' => 'ventas@kayllapi.com',
                'nombre' => strtoupper($facturacionguiaremision->emisor_nombrecomercial),
                'correo_destino' => $request->input('enviarcorreo_email'),
                'titulo' => 'GUIA REMISIÓN '.$facturacionguiaremision->despacho_serie.'-'.str_pad($facturacionguiaremision->despacho_correlativo, 6, "0", STR_PAD_LEFT),
                // 'pdf' => $output,
                'a4pdf' => $a4_output,
                'nombrepdf'=>'GUIA_REMISION_'.$facturacionguiaremision->despacho_serie.'_'.str_pad($facturacionguiaremision->despacho_correlativo, 6, "0", STR_PAD_LEFT).'.pdf',
                // 'xml' => 'public/backoffice/tienda/'.$tienda->id.'/sunat/produccion/guiaremision/'.$facturacionguiaremision->respuestanombre.'.xml',
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
                    // $message->attach($user['xml']);
                    //$message->attachData($user['pdf'], $user['nombrepdf'], [ 'mime' => 'application/pdf' ]);
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