<?php

namespace App\Http\Controllers\Layouts\Buscador\Tienda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF; 
use Mail;

class ComprobanteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$linktienda)
    { 
        $data = [
          'identificacion' => isset($_GET['numerodocumento']) ? $_GET['numerodocumento'] : null,
          'tipocomprobante' => isset($_GET['tipodocumento']) ? strval($_GET['tipodocumento']) : null,
          'serie' => isset($_GET['serie']) ? $_GET['serie'] : null,
          'correlativo' => isset($_GET['correlativo']) ? $_GET['correlativo'] : null,
          'fechaemision' => isset($_GET['fechaemision']) ? $_GET['fechaemision'] : null,
        ];

        $tienda = tienda_link($linktienda);
        if($tienda==''){
            return redirect('/');
        }
        return view('layouts/buscador/tienda/comprobante/index',[
            'tienda' => $tienda,
            'data' => $data
        ]);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request,$linktienda,$data0,$data1=0,$data2=0)
    {
        //  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$linktienda)
    {
            $rules = [
               'documento_ruc'           => 'required',
               'tipo_comprobante'        => 'required',
               'facturador_serie'        => 'required',
               'facturador_correlativo'  => 'required',
               'facturador_fechaemision' => 'required',
            ];
            $messages = [
               'documento_ruc.required'           => 'El "Numero de RUC" es Obligatorio.',
               'tipo_comprobante.required'        => 'El "Tipo de Comprobante" es Obligatorio.',
               'facturador_serie.required'        => 'El "Numero de Serie" es Obligatorio.',
               'facturador_correlativo.required'  => 'El "Numero de Correlativo" es Obligatorio.',
               'facturador_fechaemision.required' => 'El "Fecha de Emision" es Obligatorio.',
            ];

            $this->validate($request,$rules,$messages);

            $tienda = DB::table('tienda')
                  ->where('tienda.link',$linktienda)
                  ->first();

            if($request->input('tipo_comprobante')=='01' or $request->input('tipo_comprobante')=='03'){ // factura y boleta
                $comprobante = DB::table('s_facturacionboletafactura')
                    ->where([
                      ['s_facturacionboletafactura.idtienda', $tienda->id],
                      ['s_facturacionboletafactura.cliente_numerodocumento', $request->input('documento_ruc')],
                      ['s_facturacionboletafactura.venta_tipodocumento', $request->input('tipo_comprobante')],
                      ['s_facturacionboletafactura.venta_serie', $request->input('facturador_serie')],
                      ['s_facturacionboletafactura.venta_correlativo', $request->input('facturador_correlativo')],
                      ['s_facturacionboletafactura.venta_fechaemision', 'LIKE', '%'.$request->input('facturador_fechaemision').'%']
                    ])
                    ->first();
            }elseif($request->input('tipo_comprobante')=='07'){ // nota de credito
                $comprobante = DB::table('s_facturacionnotacredito')
                    ->where([
                      ['s_facturacionnotacredito.idtienda', $tienda->id],
                      ['s_facturacionnotacredito.cliente_numerodocumento', $request->input('documento_ruc')],
                      ['s_facturacionnotacredito.notacredito_tipodocumento', $request->input('tipo_comprobante')],
                      ['s_facturacionnotacredito.notacredito_serie', $request->input('facturador_serie')],
                      ['s_facturacionnotacredito.notacredito_correlativo', $request->input('facturador_correlativo')],
                      ['s_facturacionnotacredito.notacredito_fechaemision', 'LIKE', '%'.$request->input('facturador_fechaemision').'%']
                    ])
                    ->first();
            }elseif($request->input('tipo_comprobante')=='08'){ // nota de debito
                $comprobante = DB::table('s_facturacionnotadebito')
                    ->where([
                      ['s_facturacionnotadebito.idtienda', $tienda->id],
                      ['s_facturacionnotadebito.cliente_numerodocumento', $request->input('documento_ruc')],
                      ['s_facturacionnotadebito.notadebito_tipodocumento', $request->input('tipo_comprobante')],
                      ['s_facturacionnotadebito.notadebito_serie', $request->input('facturador_serie')],
                      ['s_facturacionnotadebito.notadebito_correlativo', $request->input('facturador_correlativo')],
                      ['s_facturacionnotadebito.notadebito_fechaemision', 'LIKE', '%'.$request->input('facturador_fechaemision').'%']
                    ])
                    ->first();
            }elseif($request->input('tipo_comprobante')=='09'){ // guiare mision
                $comprobante = DB::table('s_facturacionguiaremision')
                    ->where([
                      ['s_facturacionguiaremision.idtienda', $tienda->id],
                      ['s_facturacionguiaremision.despacho_destinatario_numerodocumento', $request->input('documento_ruc')],
                      ['s_facturacionguiaremision.despacho_tipodocumento', $request->input('tipo_comprobante')],
                      ['s_facturacionguiaremision.despacho_serie', $request->input('facturador_serie')],
                      ['s_facturacionguiaremision.despacho_correlativo', $request->input('facturador_correlativo')],
                      ['s_facturacionguiaremision.despacho_fechaemision', 'LIKE', '%'.$request->input('facturador_fechaemision').'%']
                    ])
                    ->first();
            }

            $idcomprobante = 0;
            $idtienda = 0;
            if(!is_null($comprobante)){
                $idcomprobante = $comprobante->id;
                $idtienda = $comprobante->idtienda;

            }
            return response()->json([
                  'resultado' => 'CORRECTO',
                  'mensaje'   => 'La consulta ha finalizado!!.',
                  'tipo_comprobante'   => $request->input('tipo_comprobante'),
                  'idcomprobante'   => $idcomprobante,
                  'idtienda'   => $idtienda
             ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$linktienda, $id)
    {
        if ($id == 'showmostrarcomprobante'){
          $tienda = DB::table('tienda')->where('tienda.link',$linktienda)->first();
          if($request->input('tipo_comprobante')=='01' or $request->input('tipo_comprobante')=='03') {
            $facturacionboletafactura = DB::table('s_facturacionboletafactura')
                ->join('users as responsable','responsable.id','s_facturacionboletafactura.idusuarioresponsable')
                ->join('s_agencia','s_agencia.id','s_facturacionboletafactura.idagencia')
                ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionboletafactura.idfacturacionrespuesta')
                ->where('s_facturacionboletafactura.id', $request->input('idcomprobante'))
                ->select(
                    's_facturacionboletafactura.*',
                    'responsable.nombre as responsablenombre',
                    's_agencia.logo as agencialogo',
                    's_facturacionrespuesta.codigo as respuestacodigo',
                    's_facturacionrespuesta.estado as respuestaestado',
                    's_facturacionrespuesta.mensaje as respuestamensaje',
                    's_facturacionrespuesta.nombre as respuestanombre',
                     DB::raw('CONCAT(s_facturacionboletafactura.cliente_numerodocumento," - ",s_facturacionboletafactura.cliente_razonsocial) as cliente'),
                     DB::raw('CONCAT(s_facturacionboletafactura.cliente_departamento, " , ", s_facturacionboletafactura.cliente_provincia, " , ", s_facturacionboletafactura.cliente_distrito) as ubigeo'),
                     DB::raw('CONCAT(s_facturacionboletafactura.emisor_ruc, " - ", s_facturacionboletafactura.emisor_nombrecomercial) as agencia')
                  )
                ->first();
            
            return view('layouts/buscador/tienda/comprobante/facturacionboletafactura', [
              'tienda' => $tienda,
              'facturacionboletafactura'=> $facturacionboletafactura
            ]);
          }
          elseif($request->input('tipo_comprobante')=='07') { // nota credito
            $facturacionnotacredito = DB::table('s_facturacionnotacredito')
                ->join('users as responsable','responsable.id','s_facturacionnotacredito.idusuarioresponsable')
                ->join('s_agencia','s_agencia.id','s_facturacionnotacredito.idagencia')
                ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionnotacredito.idfacturacionrespuesta')
                ->where('s_facturacionnotacredito.id', $request->input('idcomprobante'))  
                ->select(
                    's_facturacionnotacredito.*',
                    'responsable.nombre as responsablenombre',
                    's_agencia.logo as agencialogo',
                    's_facturacionrespuesta.codigo as respuestacodigo',
                    's_facturacionrespuesta.estado as respuestaestado',
                    's_facturacionrespuesta.mensaje as respuestamensaje',
                    's_facturacionrespuesta.nombre as respuestanombre',
                )
                ->first();
            return view('layouts/buscador/tienda/comprobante/facturacionnotacredito', [
                'tienda' => $tienda,
                'facturacionnotacredito' => $facturacionnotacredito
            ]);
          }
          elseif($request->input('tipo_comprobante')=='08') { // nota debito
            $facturacionnotadebito = DB::table('s_facturacionnotadebito')
                ->join('users as responsable','responsable.id','s_facturacionnotadebito.idusuarioresponsable')
                ->join('s_agencia','s_agencia.id','s_facturacionnotadebito.idagencia')
                ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionnotadebito.idfacturacionrespuesta')
                ->where('s_facturacionnotadebito.id', $request->input('idcomprobante'))
                ->select(
                    's_facturacionnotadebito.*',
                    'responsable.nombre as responsablenombre',
                    's_agencia.logo as agencialogo',
                    's_facturacionrespuesta.codigo as respuestacodigo',
                    's_facturacionrespuesta.estado as respuestaestado',
                    's_facturacionrespuesta.mensaje as respuestamensaje',
                    's_facturacionrespuesta.nombre as respuestanombre',
                )
                ->first();
            return view('layouts/buscador/tienda/comprobante/facturacionnotadebito',[
                'tienda' => $tienda,
                'facturacionnotadebito' => $facturacionnotadebito
            ]);
          }
          elseif($request->input('tipo_comprobante')=='09') { //guia remision
            $facturacionguiaremision = DB::table('s_facturacionguiaremision')
                ->join('users as responsable','responsable.id','s_facturacionguiaremision.idusuarioresponsable')
                ->join('s_agencia','s_agencia.id','s_facturacionguiaremision.idagencia')
                ->join('ubigeo as llegadaubigeo','llegadaubigeo.codigo','s_facturacionguiaremision.envio_direccionllegadacodigoubigeo')
                ->join('ubigeo as partidaubigeo','partidaubigeo.codigo','s_facturacionguiaremision.envio_direccionpartidacodigoubigeo')
                ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionnotacredito.idfacturacionrespuesta')
                ->leftJoin('users as transportista', 'transportista.id', 's_facturacionguiaremision.idusuariochofer')
                ->where('s_facturacionguiaremision.id', $request->input('idcomprobante'))
                ->select(
                    's_facturacionguiaremision.*',
                    'responsable.nombre as responsablenombre',
                    's_agencia.logo as agencialogo',
                    's_facturacionrespuesta.codigo as respuestacodigo',
                    's_facturacionrespuesta.estado as respuestaestado',
                    's_facturacionrespuesta.mensaje as respuestamensaje',
                    's_facturacionrespuesta.nombre as respuestanombre',
                    DB::raw('CONCAT(s_facturacionguiaremision.emisor_ruc, " - ", s_facturacionguiaremision.emisor_razonsocial) as agencia'),
                    DB::raw('CONCAT(s_facturacionguiaremision.despacho_destinatario_numerodocumento, " - ", s_facturacionguiaremision.despacho_destinatario_razonsocial) as destinatario'),
                    DB::raw('IF(transportista.idtipopersona=1,
                    CONCAT(transportista.apellidos,", ",transportista.nombre),
                    CONCAT(transportista.apellidos)) as transportista'),
                    'llegadaubigeo.nombre as llegadaubigeonombre',
                    'partidaubigeo.nombre as partidaubigeonombre',
                )
                ->first();
            return view('layouts/buscador/tienda/comprobante/facturacionguiaremision',[
                'tienda' => $tienda,
                'facturacionguiaremision'=> $facturacionguiaremision
            ]);
          }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $linktienda, $id)
    {
        $tienda = DB::table('tienda')->where('tienda.link',$linktienda)->first();
      
        if($request->input('view') == 'ticketpdf_facturaboleta') {
            $tienda = DB::table('tienda')
                ->where('tienda.link',$linktienda)
                ->first();
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
          
            
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionboletafactura/ticketpdf',[
                'tienda'                   => $tienda,
                'facturacionboletafactura' => $facturacionboletafactura,
                'boletafacturadetalle'     => $boletafacturadetalle,
            ]);
            $ticket = 'Ticket_'.str_pad($facturacionboletafactura->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');
        }
        elseif($request->input('view') == 'ticketpdf_notacredito') {
            $tienda = DB::table('tienda')
                ->where('tienda.link',$linktienda)
                ->first();

            $facturacionnotacredito = DB::table('s_facturacionnotacredito')
                ->join('users as responsable','responsable.id','s_facturacionnotacredito.idusuarioresponsable')
                ->join('s_agencia','s_agencia.id','s_facturacionnotacredito.idagencia')
                ->where('s_facturacionnotacredito.id',$id)  
                ->select(
                    's_facturacionnotacredito.*',
                    'responsable.nombre as responsablenombre',
                    's_agencia.logo as agencialogo',
                )
                ->first();
          
            $facturacionnotacreditodetalles = DB::table('s_facturacionnotacreditodetalle')
                ->join('s_producto','s_producto.id','s_facturacionnotacreditodetalle.idproducto')
                ->where('s_facturacionnotacreditodetalle.idfacturacionnotacredito',$facturacionnotacredito->id)
                ->select(
                    's_facturacionnotacreditodetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionnotacreditodetalle.id','asc')
                ->get();
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionnotacredito/ticketpdf',[
                'tienda' => $tienda,
                'facturacionnotacredito' => $facturacionnotacredito,
                'notacreditodetalle' => $facturacionnotacreditodetalles,
            ]);
            $ticket = 'Ticket_'.str_pad($facturacionnotacredito->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');
        }
        elseif($request->input('view') == 'ticketpdf_notadebito') {
            $tienda = DB::table('tienda')
                ->where('tienda.link',$linktienda)
                ->first();
          
            $facturacionnotadebito = DB::table('s_facturacionnotadebito')
                ->join('users as responsable','responsable.id','s_facturacionnotadebito.idusuarioresponsable')
                ->join('s_agencia','s_agencia.id','s_facturacionnotadebito.idagencia')
                ->where('s_facturacionnotadebito.id',$id)  
                ->select(
                    's_facturacionnotadebito.*',
                    'responsable.nombre as responsablenombre',
                    's_agencia.logo as agencialogo',
                )
                ->first();
            $facturacionnotadebitodetalles = DB::table('s_facturacionnotadebitodetalle')
                ->join('s_producto','s_producto.id','s_facturacionnotadebitodetalle.idproducto')
                ->where('s_facturacionnotadebitodetalle.idfacturacionnotadebito',$facturacionnotadebito->id)
                ->select(
                    's_facturacionnotadebitodetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionnotadebitodetalle.id','asc')
                ->get();
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionnotadebito/ticketpdf',[
                'tienda' => $tienda,
                'facturacionnotadebito' => $facturacionnotadebito,
                'notadebitodetalle' => $facturacionnotadebitodetalles,
            ]);
            $ticket = 'Ticket_'.str_pad($facturacionnotadebito->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');
        }
        elseif($request->input('view') == 'ticketpdf_guiaremision') {
            $tienda = DB::table('tienda')
                ->where('tienda.link',$linktienda)
                ->first();
          
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
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionguiaremision/ticketpdf',[
                'tienda'                          => $tienda,
                'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
                'facturacionguiaremision'         => $facturacionguiaremision,
                'ubigeo_partida'                  => $ubigeo_partida,
                'ubigeo_llegada'                  => $ubigeo_llegada,
                'transportista'                   => $transportista,
            ]);
            $ticket = 'Ticket_'.str_pad($facturacionguiaremision->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');
        } 
      
      
        
        elseif ($request->view == 'fbl_ticketpdf') {
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
          
            $pdf = PDF::loadView('layouts/buscador/tienda/comprobante/fbl_ticketpdf',[
                'tienda'                   => $tienda,
                'facturacionboletafactura' => $facturacionboletafactura,
                'boletafacturadetalle'     => $boletafacturadetalle,
            ]);
            $ticket = 'Ticket_'.str_pad($facturacionboletafactura->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');
        }
        elseif ($request->view = 'fbl_a4pdf') {
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
            
            $pdf = PDF::loadView('layouts/buscador/tienda/comprobante/fbl_a4pdf',[
                'tienda'                   => $tienda,
                'facturacionboletafactura' => $facturacionboletafactura,
                'boletafacturadetalle'     => $boletafacturadetalle,
            ]);
            $a4pdf = 'PDF_A4_'.str_pad($facturacionboletafactura->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($a4pdf.'.pdf');
        }
        elseif ($id == 'fnc_ticketpdf') {
          $facturacionnotacredito = DB::table('s_facturacionnotacredito')
            ->join('users as responsable','responsable.id','s_facturacionnotacredito.idusuarioresponsable')
            ->join('s_agencia','s_agencia.id','s_facturacionnotacredito.idagencia')
            ->where('s_facturacionnotacredito.id',$id)  
            ->select(
                's_facturacionnotacredito.*',
                'responsable.nombre as responsablenombre',
                's_agencia.logo as agencialogo',
            )
            ->first();
          $facturacionnotacreditodetalles = DB::table('s_facturacionnotacreditodetalle')
                ->join('s_producto','s_producto.id','s_facturacionnotacreditodetalle.idproducto')
                ->where('s_facturacionnotacreditodetalle.idfacturacionnotacredito',$facturacionnotacredito->id)
                ->select(
                    's_facturacionnotacreditodetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionnotacreditodetalle.id','asc')
                ->get();
          
            $pdf = PDF::loadView('layouts/buscador/tienda/comprobante/fnc_ticketpdf',[
                'tienda' => $tienda,
                'facturacionnotacredito' => $facturacionnotacredito,
                'notacreditodetalle' => $facturacionnotacreditodetalles,
            ]);
            $ticket = 'Ticket_'.str_pad($facturacionnotacredito->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');
        }
        elseif ($id == 'fnc_a4pdf') {
          $facturacionnotacredito = DB::table('s_facturacionnotacredito')
            ->join('users as responsable','responsable.id','s_facturacionnotacredito.idusuarioresponsable')
            ->join('s_agencia','s_agencia.id','s_facturacionnotacredito.idagencia')
            ->where('s_facturacionnotacredito.id',$id)  
            ->select(
                's_facturacionnotacredito.*',
                'responsable.nombre as responsablenombre',
                's_agencia.logo as agencialogo',
            )
            ->first();
          $facturacionnotacreditodetalles = DB::table('s_facturacionnotacreditodetalle')
                ->join('s_producto','s_producto.id','s_facturacionnotacreditodetalle.idproducto')
                ->where('s_facturacionnotacreditodetalle.idfacturacionnotacredito',$facturacionnotacredito->id)
                ->select(
                    's_facturacionnotacreditodetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionnotacreditodetalle.id','asc')
                ->get();
          
            $pdf = PDF::loadView('layouts/buscador/tienda/comprobante/fnc_a4pdf',[
                'tienda' => $tienda,
                'facturacionnotacredito' => $facturacionnotacredito,
                'notacreditodetalle' => $facturacionnotacreditodetalles,
            ]);
            $a4pdf = 'PDF_A4_'.str_pad($facturacionnotacredito->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($a4pdf.'.pdf');
        }
        elseif ($id == 'fnd_ticketpdf') {
          $facturacionnotadebito = DB::table('s_facturacionnotadebito')
            ->join('users as responsable','responsable.id','s_facturacionnotadebito.idusuarioresponsable')
            ->join('s_agencia','s_agencia.id','s_facturacionnotadebito.idagencia')
            ->where('s_facturacionnotadebito.id',$id)  
            ->select(
                's_facturacionnotadebito.*',
                'responsable.nombre as responsablenombre',
                's_agencia.logo as agencialogo',
            )
            ->first();
          $facturacionnotadebitodetalles = DB::table('s_facturacionnotadebitodetalle')
                ->join('s_producto','s_producto.id','s_facturacionnotadebitodetalle.idproducto')
                ->where('s_facturacionnotadebitodetalle.idfacturacionnotadebito',$facturacionnotadebito->id)
                ->select(
                    's_facturacionnotadebitodetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionnotadebitodetalle.id','asc')
                ->get();

            $pdf = PDF::loadView('layouts/buscador/tienda/comprobante/fnd_ticketpdf',[
                'tienda' => $tienda,
                'facturacionnotadebito' => $facturacionnotadebito,
                'notadebitodetalle' => $facturacionnotadebitodetalles,
            ]);
            $ticket = 'Ticket_'.str_pad($facturacionnotadebito->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');
        }
        elseif ($id == 'fnd_a4pdf') {
          $facturacionnotadebito = DB::table('s_facturacionnotadebito')
            ->join('users as responsable','responsable.id','s_facturacionnotadebito.idusuarioresponsable')
            ->join('s_agencia','s_agencia.id','s_facturacionnotadebito.idagencia')
            ->where('s_facturacionnotadebito.id',$id)  
            ->select(
                's_facturacionnotadebito.*',
                'responsable.nombre as responsablenombre',
                's_agencia.logo as agencialogo',
            )
            ->first();
          $facturacionnotadebitodetalles = DB::table('s_facturacionnotadebitodetalle')
                ->join('s_producto','s_producto.id','s_facturacionnotadebitodetalle.idproducto')
                ->where('s_facturacionnotadebitodetalle.idfacturacionnotadebito',$facturacionnotadebito->id)
                ->select(
                    's_facturacionnotadebitodetalle.*',
                    's_producto.codigo as productocodigo',
                    's_producto.nombre as productonombre'
                )
                ->orderBy('s_facturacionnotadebitodetalle.id','asc')
                ->get();
          
            $pdf = PDF::loadView('layouts/buscador/tienda/comprobante/fnd_a4pdf',[
                'tienda' => $tienda,
                'facturacionnotadebito' => $facturacionnotadebito,
                'notadebitodetalle' => $facturacionnotadebitodetalles,
            ]);
            $a4pdf = 'PDF_A4_'.str_pad($facturacionnotadebito->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($a4pdf.'.pdf');
        }
        elseif ($id == 'fgr_ticketpdf') {
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
          
            $pdf = PDF::loadView('layouts/buscador/tienda/comprobante/fgr_ticketpdf',[
                'tienda'                          => $tienda,
                'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
                'facturacionguiaremision'         => $facturacionguiaremision,
                'ubigeo_partida'                  => $ubigeo_partida,
                'ubigeo_llegada'                  => $ubigeo_llegada,
                'transportista'                   => $transportista,
            ]);
            $ticket = 'Ticket_'.str_pad($facturacionguiaremision->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');
        }
        elseif ($id == 'fgr_a4pdf') {
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
          
            $pdf = PDF::loadView('layouts/buscador/tienda/comprobante/fgr_a4pdf',[
                'tienda'                          => $tienda,
                'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
                'facturacionguiaremision'         => $facturacionguiaremision,
                'ubigeo_partida'                  => $ubigeo_partida,
                'ubigeo_llegada'                  => $ubigeo_llegada,
                'transportista'                   => $transportista,
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
    public function update(Request $request, $linktienda, $idtienda)
    {
        if($request->input('view') == 'enviarcorreo-boletafactura'){
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
                  ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','facturaboleta.idfacturacionrespuesta')
                  ->where('facturaboleta.id',$request->input('idfacturacionboletafactura'))
                  ->select(
                      'facturaboleta.*',
                      'responsable.nombre as responsablenombre',
                      's_agencia.logo as agencialogo',
                      's_facturacionrespuesta.codigo as respuestacodigo',
                      's_facturacionrespuesta.estado as respuestaestado',
                      's_facturacionrespuesta.mensaje as respuestamensaje',
                      's_facturacionrespuesta.nombre as respuestanombre',
                      's_facturacionrespuesta.qr as respuestaqr',
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
              $tienda = DB::table('tienda')->whereId($idtienda)->first();
          
              //comida
              $comida_venta = '';
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
                  'comida_venta'     => $comida_venta,
              ]);
              $pdfa4 = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionboletafactura/a4pdf',[
                  'tienda'                   => $tienda,
                  'facturacionboletafactura' => $facturacionboletafactura,
                  'boletafacturadetalle'     => $boletafacturadetalle,
              ]);
          
              $output = $pdf->output();
              $output_pdfa4 = $pdfa4->output();
          
              $comprobante = '';
              if($facturacionboletafactura->venta_tipodocumento=='03'){
                  $comprobante = 'BOLETA';
              }elseif($facturacionboletafactura->venta_tipodocumento=='01'){
                  $comprobante = 'FACTURA';
              }

              $user = array (
                 'correo' => 'ventas@kayllapi.com',
                 'nombre' => strtoupper($facturacionboletafactura->emisor_nombrecomercial),
                 'correo_destino' => $request->input('enviarcorreo_email'),
                 'titulo' => $comprobante.' '.$facturacionboletafactura->venta_serie.'-'.str_pad($facturacionboletafactura->venta_correlativo, 6, "0", STR_PAD_LEFT),
                 'pdf' => $output,
                 'pdfa4' => $output_pdfa4,
                 'nombrepdf'=>$comprobante.'_'.$facturacionboletafactura->venta_serie.'_'.str_pad($facturacionboletafactura->venta_correlativo, 6, "0", STR_PAD_LEFT).'.pdf',
                 'xml' => 'public/backoffice/tienda/'.$tienda->id.'/sunat/produccion/boletafactura/'.$facturacionboletafactura->respuestanombre.'.xml',
              );
          
              Mail::send('app/email_comprobante',  [
                  'user' => $user,
                  'tienda' => $tienda,
                  'facturacionboletafactura' => $facturacionboletafactura,
                  'boletafacturadetalle' => $boletafacturadetalle,
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
        elseif($request->input('view') == 'enviarcorreo-notacredito'){
              $rules = [
                  'enviarcorreo_email' => 'required|email',
              ];
              $messages = [
                  'enviarcorreo_email.required' => 'El "Correo Electrónico" es Obligatorio.',
                  'enviarcorreo_email.email' => 'El "Correo Electrónico" es Invalido, ingrese otro por favor.',
              ];

              $this->validate($request,$rules,$messages);
          
              $facturacionnotacredito = DB::table('s_facturacionnotacredito')
                  ->join('users as responsable','responsable.id','s_facturacionnotacredito.idusuarioresponsable')
                  ->join('s_agencia','s_agencia.id','s_facturacionnotacredito.idagencia')
                  ->where('s_facturacionnotacredito.id',$request->input('idfacturacionnotacredito'))
                  ->select(
                      's_facturacionnotacredito.*',
                      'responsable.nombre as responsablenombre',
                      's_agencia.logo as agencialogo',
                       DB::raw('CONCAT(s_facturacionnotacredito.cliente_numerodocumento," - ",s_facturacionnotacredito.cliente_razonsocial) as cliente'),
                       DB::raw('CONCAT(s_facturacionnotacredito.cliente_departamento, " , ", s_facturacionnotacredito.cliente_provincia, " , ", s_facturacionnotacredito.cliente_distrito) as ubigeo'),
                       DB::raw('CONCAT(s_facturacionnotacredito.emisor_ruc, " - ", s_facturacionnotacredito.emisor_nombrecomercial) as agencia')
                    )
                  ->first();
              $notacreditodetalle = DB::table('s_facturacionnotacreditodetalle')
                  ->join('s_producto','s_producto.id','s_facturacionnotacreditodetalle.idproducto')
                  ->where('s_facturacionnotacreditodetalle.idfacturacionnotacredito',$facturacionnotacredito->id)
                  ->select(
                      's_facturacionnotacreditodetalle.*',
                      's_producto.codigo as productocodigo',
                      's_producto.nombre as productonombre'
                  )
                  ->orderBy('s_facturacionnotacreditodetalle.id','asc')
                  ->get();
          
              $tienda = DB::table('tienda')->whereId($idtienda)->first();

              $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionnotacredito/ticketpdf',[
                  'tienda'                    => $tienda,
                  'facturacionnotacredito'    => $facturacionnotacredito,
                  'notacreditodetalle'        => $notacreditodetalle,
              ]);
          
              $a4pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionnotacredito/a4pdf',[
                  'tienda' => $tienda,
                  'facturacionnotacredito' => $facturacionnotacredito,
                  'notacreditodetalle' => $notacreditodetalle,
              ]);
          
              $output = $pdf->output();
              $a4_output = $a4pdf->output();

              $user = array (
                 'correo' => 'ventas@kayllapi.com',
                 'nombre' => strtoupper($facturacionnotacredito->emisor_nombrecomercial),
                 'correo_destino' => $request->input('enviarcorreo_email'),
                 'titulo' => 'NOTA DE CRÉDITO '.$facturacionnotacredito->notacredito_serie.'-'.str_pad($facturacionnotacredito->notacredito_correlativo, 6, "0", STR_PAD_LEFT),
                 'pdf' => $output,
                 'a4pdf' => $a4_output,
                 'nombrepdf'=>'NOTA_DE_CREDITO_'.$facturacionnotacredito->notacredito_serie.'_'.str_pad($facturacionnotacredito->notacredito_correlativo, 6, "0", STR_PAD_LEFT).'.pdf',
                 'xml' => 'public/backoffice/tienda/'.$tienda->id.'/sunat/produccion/notacredito/'.$facturacionrespuesta->nombre.'.xml',
              );

              Mail::send('app/email_notacredito',  [
                  'user' => $user,
                  'tienda'                   => $tienda,
                  'facturacionnotacredito' => $facturacionnotacredito,
                  'notacreditodetalle'     => $notacreditodetalle,
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
        elseif($request->input('view') == 'enviarcorreo-notadebito'){
              $rules = [
                  'enviarcorreo_email' => 'required|email',
              ];
              $messages = [
                  'enviarcorreo_email.required' => 'El "Correo Electrónico" es Obligatorio.',
                  'enviarcorreo_email.email' => 'El "Correo Electrónico" es Invalido, ingrese otro por favor.',
              ];

              $this->validate($request,$rules,$messages);
          
          
              $facturacionnotadebito = DB::table('s_facturacionnotadebito')
                  ->join('users as responsable','responsable.id','s_facturacionnotadebito.idusuarioresponsable')
                  ->join('s_agencia','s_agencia.id','s_facturacionnotadebito.idagencia')
                  ->where('s_facturacionnotadebito.id',$request->input('idfacturacionnotadebito'))
                  ->select(
                      's_facturacionnotadebito.*',
                      'responsable.nombre as responsablenombre',
                      's_agencia.logo as agencialogo',
                       DB::raw('CONCAT(s_facturacionnotadebito.cliente_numerodocumento," - ",s_facturacionnotadebito.cliente_razonsocial) as cliente'),
                       DB::raw('CONCAT(s_facturacionnotadebito.cliente_departamento, " , ", s_facturacionnotadebito.cliente_provincia, " , ", s_facturacionnotadebito.cliente_distrito) as ubigeo'),
                       DB::raw('CONCAT(s_facturacionnotadebito.emisor_ruc, " - ", s_facturacionnotadebito.emisor_nombrecomercial) as agencia')
                    )
                  ->first();
              $notadebitodetalle = DB::table('s_facturacionnotadebitodetalle')
                  ->join('s_producto','s_producto.id','s_facturacionnotadebitodetalle.idproducto')
                  ->where('s_facturacionnotadebitodetalle.idfacturacionnotadebito',$facturacionnotadebito->id)
                  ->select(
                      's_facturacionnotadebitodetalle.*',
                      's_producto.codigo as productocodigo',
                      's_producto.nombre as productonombre'
                  )
                  ->orderBy('s_facturacionnotadebitodetalle.id','asc')
                  ->get();
          
              $tienda = DB::table('tienda')->whereId($idtienda)->first();

              $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionnotadebito/ticketpdf',[
                  'tienda'                    => $tienda,
                  'facturacionnotadebito'    => $facturacionnotadebito,
                  'notadebitodetalle'        => $notadebitodetalle,
              ]);
          
              $a4pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionnotadebito/a4pdf',[
                  'tienda' => $tienda,
                  'facturacionnotadebito' => $facturacionnotadebito,
                  'notadebitodetalle' => $notadebitodetalle,
              ]);
          
              $output = $pdf->output();
              $a4_output = $a4pdf->output();

              $user = array (
                 'correo' => 'ventas@kayllapi.com',
                 'nombre' => strtoupper($facturacionnotadebito->emisor_nombrecomercial),
                 'correo_destino' => $request->input('enviarcorreo_email'),
                 'titulo' => 'NOTA DE DEBITO '.$facturacionnotadebito->notadebito_serie.'-'.str_pad($facturacionnotadebito->notadebito_correlativo, 6, "0", STR_PAD_LEFT),
                 'pdf' => $output,
                 'a4pdf' => $a4_output,
                 'nombrepdf'=>'NOTA_DE_DEBITO_'.$facturacionnotadebito->notadebito_serie.'_'.str_pad($facturacionnotadebito->notadebito_correlativo, 6, "0", STR_PAD_LEFT).'.pdf',
                 'xml' => 'public/backoffice/tienda/'.$tienda->id.'/sunat/produccion/notadebito/'.$facturacionrespuesta->nombre.'.xml',
              );

              Mail::send('app/email_notadebito',  [
                  'user' => $user,
                  'tienda'                   => $tienda,
                  'facturacionnotadebito' => $facturacionnotadebito,
                  'notadebitodetalle'     => $notadebitodetalle,
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
        elseif($request->input('view') == 'enviarcorreo-guiaremision'){
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
          
              $tienda = DB::table('tienda')->whereId($idtienda)->first();

              $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionguiaremision/ticketpdf',[
                  'tienda'                   => $tienda,
                  'facturacionguiaremision' => $facturacionguiaremision,
                  'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
              ]);
          
              $a4pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionguiaremision/a4pdf',[
                  'tienda'                          => $tienda,
                  'facturacionguiaremisiondetalles' => $facturacionguiaremisiondetalles,
                  'facturacionguiaremision'         => $facturacionguiaremision,
                  'ubigeo_partida'                  => $ubigeo_partida,
                  'ubigeo_llegada'                  => $ubigeo_llegada,
                  'transportista'                   => $transportista,
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
    public function destroy($id)
    {
        //
    }
}
