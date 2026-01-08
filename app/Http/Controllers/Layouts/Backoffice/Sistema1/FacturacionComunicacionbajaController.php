<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use DateTime;
use PDF;
use Mail;

class FacturacionComunicacionbajaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();   
        $where = [];
        $where[] = ['s_facturacioncomunicacionbaja.descripcionmotivobaja', 'LIKE', '%'.$request->motivo.'%'];
        $where[] = ['s_facturacioncomunicacionbaja.comunicacionbaja_fechageneracion', 'LIKE', '%'.$request->fecharegistro.'%'];
      

        
        $facturacioncomunicacionbaja = DB::table('s_facturacioncomunicacionbajadetalle')->join('s_facturacioncomunicacionbaja','s_facturacioncomunicacionbaja.id','s_facturacioncomunicacionbajadetalle.idfacturacioncomunicacionbaja')
            ->join('users as responsable','responsable.id','s_facturacioncomunicacionbaja.idusuarioresponsable')
            ->leftJoin('users as cliente','cliente.id','s_facturacioncomunicacionbajadetalle.idusuariocliente')
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacioncomunicacionbaja.idfacturacionrespuesta')
            ->where('s_facturacioncomunicacionbaja.idtienda', $tienda->id)
            ->select(
                's_facturacioncomunicacionbajadetalle.*',
                's_facturacioncomunicacionbaja.comunicacionbaja_fechageneracion as comunicacionbaja_fechageneracion',
                's_facturacioncomunicacionbaja.comunicacionbaja_correlativo as comunicacionbaja_correlativo',
                's_facturacioncomunicacionbaja.comunicacionbaja_fechacomunicacion as comunicacionbaja_fechacomunicacion',
                's_facturacioncomunicacionbaja.emisor_ruc as emisor_ruc',
                's_facturacioncomunicacionbaja.emisor_nombrecomercial as emisor_nombrecomercial',
                's_facturacioncomunicacionbaja.emisor_razonsocial as emisor_razonsocial',
                'responsable.nombre as responsablenombre',
                'cliente.identificacion as clienteidentificacion',
                DB::raw('IF(cliente.idtipopersona=1,
                CONCAT(cliente.apellidos,", ",cliente.nombre),
                CONCAT(cliente.apellidos)) as cliente'),
                's_facturacionrespuesta.codigo as respuestacodigo',
                's_facturacionrespuesta.estado as respuestaestado',
            )
            ->orderBy('s_facturacioncomunicacionbaja.id','desc')
            ->paginate(10);

        return view('layouts/backoffice/tienda/sistema/facturacioncomunicacionbaja/index', [
            'tienda'                      => $tienda,
            'facturacioncomunicacionbaja' => $facturacioncomunicacionbaja
        ]);
    }

    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $agencias = DB::table('s_agencia')->where('idtienda',$idtienda)->where('idestadofacturacion',1)->get();
        return view('layouts/backoffice/tienda/sistema/facturacioncomunicacionbaja/create',[
            'tienda'        => $tienda,
            'agencias'      => $agencias
        ]);
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            $rules    = [
                'idagencia'         => 'required',
                'fechaemision' => 'required',
                'comprobantes'      => 'required',
            ];
            $messages = [
                'idagencia.required'          => 'La "Empresa" es Obligatorio.',
                'fechaemision.required'  => 'La "Fecha de Emisión es Obligatorio.',
                'comprobantes.required'       => 'Los "Comprobantes" son Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $comprobantes = json_decode($request->input('comprobantes'));
           
            if(empty($comprobantes)) {
                return response()->json([
                   'resultado' => 'ERROR',
                   'mensaje'   => 'No hay boletas para su envio.'
                ]);
            }
            
            // validar vencimiento
            foreach ($comprobantes as $value) {
              
                $serie = '';
                $correlativo = '';
                if ($value->tipo == 'FACTURA') {
                    $factura  = DB::table('s_facturacionboletafactura')->whereId($value->idventa_boletafactura)->first();
                    $serie = $factura->venta_serie;
                    $correlativo = $factura->venta_correlativo;
                    $fechaemision = $factura->venta_fechaemision;
                }else if ($value->tipo == 'NOTACREDITO') {
                    $ntoacredito  = DB::table('s_facturacionnotacredito')->whereId($value->idventa_boletafactura)->first();
                    $serie = $ntoacredito->notacredito_serie;
                    $correlativo = $ntoacredito->notacredito_correlativo;
                    $fechaemision = $ntoacredito->notacredito_fechaemision;
                }else{
                    return response()->json([
                       'resultado' => 'ERROR',
                       'mensaje'   => 'Hay un comprobante no valido!!.'
                    ]);
                }
              
                if($value->facturador_motivo==''){
                    return response()->json([
                       'resultado' => 'ERROR',
                       'mensaje'   => 'El motivo del comprobante "'.$serie.'-'.$correlativo.'" es obligatorio'
                    ]);
                }
              
                if(!isset($fechaemision)){
                    return response()->json([
                       'resultado' => 'ERROR',
                       'mensaje'   => 'Fecha de emisión de comprobante no existe.'
                    ]);
                }
              
                $fecha_emision = explode(' ', $fechaemision);
                $fecha_emision = new DateTime($fecha_emision[0]);     
                $fecha_actual  = date("d-m-Y");
                $fecha_actual  = new DateTime($fecha_actual);
                $diff_fecha    = $fecha_emision->diff($fecha_actual);   
          
                if ($diff_fecha->days >= 5) {
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'Solo se admiten comprobantes con un plazo maximo de 5 dias de emisión!!.'
                    ]);
                }
            }
          
           $agencia= DB::table('s_agencia')->whereId($request->input('idagencia'))->first();
          
           $tienda = DB::table('tienda')
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
            
            // RESUMEN DIARIO      
            $correlativo = DB::table('s_facturacioncomunicacionbaja')
                          ->where('s_facturacioncomunicacionbaja.idtienda', $idtienda)
                          ->where('s_facturacioncomunicacionbaja.emisor_ruc', $agencia->ruc)
                          ->orderBy('s_facturacioncomunicacionbaja.comunicacionbaja_correlativo', 'desc')
                          ->first();
                  
            $comunicacionbaja_correlativo = ($correlativo == '') ? 1 : $correlativo->comunicacionbaja_correlativo + 1;
          
            $idfacturacioncomunicacionbaja = DB::table('s_facturacioncomunicacionbaja')->insertGetId([
                'emisor_ruc'                        => $agencia->ruc,
                'emisor_razonsocial'                => $agencia->razonsocial,
                'emisor_nombrecomercial'            => $agencia->nombrecomercial,
                'emisor_ubigeo'                     => $tienda->tiendaubigeocodigo,
                'emisor_departamento'               => $tienda->tiendaubigeodepartamento,
                'emisor_provincia'                  => $tienda->tiendaubigeoprovincia,
                'emisor_distrito'                   => $tienda->tiendaubigeodistrito,
                'emisor_urbanizacion'               => '',
                'emisor_direccion'                  => $tienda->tiendadireccion,
                'comunicacionbaja_correlativo'      => $comunicacionbaja_correlativo,
                'comunicacionbaja_fechageneracion'  => $request->fechaemision,
                'comunicacionbaja_fechacomunicacion'=> Carbon::now(),
                'idagencia'                         => $agencia->id,
                'idtienda'                          => $idtienda,
                'idusuarioresponsable'              => Auth::user()->id
            ]);
         
            foreach ($comprobantes as $value) {
               if ($value->tipo == 'FACTURA') {
                  $factura  = DB::table('s_facturacionboletafactura')->whereId($value->idventa_boletafactura)->first();
                 
                  DB::table('s_facturacioncomunicacionbajadetalle')->insert([
                      'tipodocumento'                 => $factura->venta_tipodocumento,
                      'serie'                         => $factura->venta_serie,
                      'correlativo'                   => $factura->venta_correlativo,
                      'descripcionmotivobaja'         => $value->facturador_motivo,
                      'idfacturacionboletafactura'    => $factura->id,
                      'idfacturacionnotacredito'      => 0,
                      'idagencia'                     => $factura->idagencia,
                      'idtienda'                      => $factura->idtienda,
                      'idusuariocliente'              => $factura->idusuariocliente,
                      'idfacturacioncomunicacionbaja' => $idfacturacioncomunicacionbaja,
                 ]);
                 
               }elseif ($value->tipo == 'NOTACREDITO') {
                  $notacredito  = DB::table('s_facturacionnotacredito')->whereId($value->idventa_boletafactura)->first();
                 
                  DB::table('s_facturacioncomunicacionbajadetalle')->insert([
                      'tipodocumento'                 => $notacredito->notacredito_tipodocumento,
                      'serie'                         => $notacredito->notacredito_serie,
                      'correlativo'                   => $notacredito->notacredito_correlativo,
                      'descripcionmotivobaja'         => $value->facturador_motivo,
                      'idfacturacionboletafactura'    => 0,
                      'idfacturacionnotacredito'      => $notacredito->id,
                      'idagencia'                     => $notacredito->idagencia,
                      'idtienda'                      => $notacredito->idtienda,
                      'idusuariocliente'              => $notacredito->idusuariocliente,
                      'idfacturacioncomunicacionbaja' => $idfacturacioncomunicacionbaja,
                 ]);
               }
            } 
          
            $result = facturador_comunicacionbaja($idfacturacioncomunicacionbaja);

            return [
                  'resultado' => $result['resultado'],
                  'mensaje'   => $result['mensaje']
            ];
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
     
         if($id == 'show-seleccionarboletafactura'){  
           
            $data = '';
            $resultado = '';
            $mensaje = '';
            if ($request->input('tipodocumento') == 'FACTURA') {

                $facturacionboletafactura = DB::table('s_facturacionboletafactura')
                    ->join('users as usuariocliente','usuariocliente.id','s_facturacionboletafactura.idusuariocliente')
                    ->where('s_facturacionboletafactura.venta_serie',$request->input('facturador_serie'))
                    ->where('s_facturacionboletafactura.venta_correlativo',$request->input('facturador_correlativo'))
                    ->where('s_facturacionboletafactura.idagencia',$request->input('idagencia'))
                    ->where('s_facturacionboletafactura.idtienda',$idtienda)
                    ->select(
                        's_facturacionboletafactura.*',
                        DB::raw('IF(usuariocliente.idtipopersona=1,
                        CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                        CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos)) as cliente')
                    )
                    ->first();   

                if (is_null($facturacionboletafactura)) {
                    $resultado = 'ERROR';
                    $mensaje = 'El Boleta "'.$request->input('facturador_serie').'-'.str_pad($request->input('facturador_correlativo'), 8, "0", STR_PAD_LEFT).'", no existe.';
                }else{
                    $facturacioncomunicacionbaja = DB::table('s_facturacioncomunicacionbajadetalle')
                        ->where('s_facturacioncomunicacionbajadetalle.idfacturacionboletafactura', $facturacionboletafactura->id)
                        ->limit(1)
                        ->first();
                    if($facturacioncomunicacionbaja!=''){
                        $resultado = 'ERROR';
                        $mensaje = 'El Boleta "'.$facturacionboletafactura->venta_serie.'-'.str_pad($facturacionboletafactura->venta_correlativo, 8, "0", STR_PAD_LEFT).'", fue enviado con Comunicación de Baja anteriormente.';
                    }else{
                        $data =  [
                          'tipo'                        => 'FACTURA',
                          'id'                          => $facturacionboletafactura->id,
                          'serie'                       => $facturacionboletafactura->venta_serie,
                          'correlativo'                 => str_pad($facturacionboletafactura->venta_correlativo, 8, "0", STR_PAD_LEFT),
                          'cliente'                     => $facturacionboletafactura->cliente,
                          'emision'                     => date_format(date_create($facturacionboletafactura->venta_fechaemision), 'd-m-Y h:i:s A'),
                          'moneda'                      => $facturacionboletafactura->venta_tipomoneda=='PEN'?'SOLES':'DOLARES',
                          'venta_montooperaciongravada' => $facturacionboletafactura->venta_montooperaciongravada,
                          'venta_montoigv'              => $facturacionboletafactura->venta_montoigv,
                          'venta_montoimpuestoventa'    => $facturacionboletafactura->venta_montoimpuestoventa,
                        ];
                    }
                }
              
            }
            elseif($request->input('tipodocumento') == 'NOTACREDITO'){
  
                $notacredito = DB::table('s_facturacionnotacredito')
                    ->where('s_facturacionnotacredito.notacredito_serie',$request->input('facturador_serie'))
                    ->where('s_facturacionnotacredito.notacredito_correlativo',$request->input('facturador_correlativo'))
                    ->where('s_facturacionnotacredito.idagencia',$request->input('idagencia'))
                    ->where('s_facturacionnotacredito.idtienda',$idtienda)
                    ->first();

                if (is_null($notacredito)) {
                    $resultado = 'ERROR';
                    $mensaje = 'La Nota de Crédito "'.$request->input('facturador_serie').'-'.str_pad($request->input('facturador_correlativo'), 8, "0", STR_PAD_LEFT).'", no existe.';
                }else{
                    $facturacioncomunicacionbaja = DB::table('s_facturacioncomunicacionbajadetalle')
                        ->where('s_facturacioncomunicacionbajadetalle.idfacturacionnotacredito', $notacredito->id)
                        ->limit(1)
                        ->first();
                    if($facturacioncomunicacionbaja!=''){
                        $resultado = 'ERROR';
                        $mensaje = 'La Nota de Crédito "'.$notacredito->venta_serie.'-'.str_pad($notacredito->venta_correlativo, 8, "0", STR_PAD_LEFT).'", fue enviado con Comunicación de Baja anteriormente.';
                    }else{
                        $data = [
                          'tipo'                        => 'NOTACREDITO',
                          'id'                          => $notacredito->id,
                          'serie'                       => $notacredito->notacredito_serie,
                          'correlativo'                 => str_pad($notacredito->notacredito_correlativo, 8, "0", STR_PAD_LEFT),
                          'cliente'                     => $notacredito->cliente_numerodocumento.' - '.$notacredito->cliente_razonsocial,
                          'emision'                     => date_format(date_create($notacredito->notacredito_fechaemision), 'd-m-Y h:i:s A'),
                          'moneda'                      => $notacredito->notacredito_tipomoneda=='PEN'?'SOLES':'DOLARES',
                          'venta_montooperaciongravada' => $notacredito->notacredito_montooperaciongravada,
                          'venta_montoigv'              => $notacredito->notacredito_montoigv,
                          'venta_montoimpuestoventa'    => $notacredito->notacredito_montoimpuestoventa,
                        ];
                    }  
                }
              
            }
            else{
                $resultado = 'ERROR';
                $mensaje = 'Ingrese una Serie y Correlativo valido!.';
            }
          
            return [
                'resultado' => $resultado,
                'mensaje' => $mensaje,
                'data' => $data
            ]; 
                      
        }
         elseif($id == 'show-selecionarserie'){
            $agencia = DB::table('s_agencia')->whereId($request->input('idagencia'))->first();
            $agenciaoption = '';
            if($agencia!=''){
                $facturacion_serie1 = str_pad($agencia->facturacion_serie, 3, "0", STR_PAD_LEFT);
                $facturacion_serie2 = str_pad($agencia->facturacion_serie, 2, "0", STR_PAD_LEFT);
                $agenciaoption = '<option></option>
                                  <option value="F'.$facturacion_serie1.'" tipodocumento="FACTURA">F'.$facturacion_serie1.'</option>
                                  <option value="FF'.$facturacion_serie2.'" tipodocumento="NOTACREDITO">FF'.$facturacion_serie2.'</option>';
            }
            return [ 'agenciaoption' => $agenciaoption ];
        }
         elseif($id == 'show-seleccionarboletafactura-fecha') {
            
              /* Facturas */
              $facturacionboletafacturas = DB::table('s_facturacionboletafactura')
                  ->join('users as usuariocliente','usuariocliente.id','s_facturacionboletafactura.idusuariocliente')
                  ->where('s_facturacionboletafactura.venta_fechaemision', 'LIKE', '%'.$request->fechaemision.'%')
                  ->where('s_facturacionboletafactura.idtienda',$idtienda)
                  ->where('s_facturacionboletafactura.venta_tipodocumento', '<>', '03')
                  ->select(
                      's_facturacionboletafactura.*',
                      DB::raw('IF(usuariocliente.idtipopersona=1,
                      CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                      CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos)) as cliente'),
                  )
                  ->get();   
              
              $factura_notacredito =  [];
           
              foreach ($facturacionboletafacturas as $factura) {
                $f_exist_comunicacionbaja = DB::table('s_facturacioncomunicacionbajadetalle')
                        ->where('s_facturacioncomunicacionbajadetalle.idfacturacionboletafactura', $factura->id)
                        ->exists();
                
                $f_exist_notacredito = DB::table('s_facturacionnotacredito')
                        ->where('s_facturacionnotacredito.idfacturacionboletafactura', $factura->id)
                        ->exists();
                
                if (!$f_exist_comunicacionbaja && !$f_exist_notacredito) {
                   $factura_notacredito[] = [
                     'venta_serie'       => $factura->venta_serie,
                     'venta_correlativo' => $factura->venta_correlativo,
                     'cliente'           => $factura->cliente,
                     'venta_subtotal'    => $factura->venta_subtotal,
                     'tipodocumento'     => 'FACTURA'
                   ];
                }
              }
            
              /* Nota de Credito */
    
              $notacreditos = DB::table('s_facturacionnotacredito')
                    ->join('users as usuariocliente','usuariocliente.id','s_facturacionnotacredito.idusuariocliente')
                    ->where('s_facturacionnotacredito.notacredito_fechaemision', 'LIKE', '%'.$request->fechaemision.'%')
                    ->where('s_facturacionnotacredito.idtienda',$idtienda)
                    ->where('s_facturacionnotacredito.notacredito_tipodocafectado', '<>', '03')
                    ->select(
                        's_facturacionnotacredito.*',
                        DB::raw('IF(usuariocliente.idtipopersona=1,
                        CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                        CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos)) as cliente'),
                    )
                    ->get();
           
              foreach ($notacreditos as $notacredito) {
                $n_exist_comunicacionbaja = DB::table('s_facturacioncomunicacionbajadetalle')
                        ->where('s_facturacioncomunicacionbajadetalle.idfacturacionnotacredito', $notacredito->id)
                        ->exists();
                
                if (!$n_exist_comunicacionbaja) {
                  $factura_notacredito[] = [
                     'venta_serie'       => $notacredito->notacredito_serie,
                     'venta_correlativo' => $notacredito->notacredito_correlativo,
                     'cliente'           => $notacredito->cliente,
                     'venta_subtotal'    => $notacredito->notacredito_montoimpuestoventa,
                     'tipodocumento'     => 'NOTACREDITO'
                   ];
                }
              }
           
              if (count($factura_notacredito) <= 0) {
                return [
                  'resultado' => 'ERROR',
                  'mensaje' => 'No se encontraron Facturas y Notas de Credito con esta Fecha.',
                ];
              }
           
              return [
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Facturas y Nota de Creditos Encontrada, seleccione para dar de baja.',
                'facturas'  => $factura_notacredito,
              ];
         }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

       
        $facturacioncomunicacionbaja = DB::table('s_facturacioncomunicacionbaja')
            ->join('users as responsable','responsable.id','s_facturacioncomunicacionbaja.idusuarioresponsable')
            ->join('s_agencia','s_agencia.id','s_facturacioncomunicacionbaja.idagencia')
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacioncomunicacionbaja.idfacturacionrespuesta')
            ->where('s_facturacioncomunicacionbaja.id',$id)
            ->select(
                's_facturacioncomunicacionbaja.*',
                'responsable.nombre as responsablenombre',
                's_agencia.logo as agencialogo',
                's_facturacionrespuesta.codigo as respuestacodigo',
                's_facturacionrespuesta.estado as respuestaestado',
                's_facturacionrespuesta.mensaje as respuestamensaje',
                's_facturacionrespuesta.nombre as respuestanombre',
            )
            ->first();
      
        if($request->input('view') == 'detalle'){
            $facturacioncomunicacionbajadetalle = DB::table('s_facturacioncomunicacionbajadetalle')
                ->leftJoin('s_facturacionboletafactura','s_facturacionboletafactura.id','s_facturacioncomunicacionbajadetalle.idfacturacionboletafactura')
                ->leftJoin('users as cliente','cliente.id','s_facturacioncomunicacionbajadetalle.idusuariocliente')
                ->where('s_facturacioncomunicacionbajadetalle.idfacturacioncomunicacionbaja',$facturacioncomunicacionbaja->id)
                ->select(
                    's_facturacioncomunicacionbajadetalle.*',
                    'cliente.identificacion as clienteidentificacion',
                    's_facturacionboletafactura.venta_fechaemision as venta_fechaemision',
                    's_facturacionboletafactura.venta_tipodocumento as venta_tipodocumento',
                    's_facturacionboletafactura.cliente_razonsocial as cliente_razonsocial',
                    's_facturacionboletafactura.venta_tipomoneda as venta_tipomoneda',
                    's_facturacionboletafactura.venta_subtotal as venta_subtotal',
                    's_facturacionboletafactura.venta_igv as venta_igv',
                    's_facturacionboletafactura.venta_montoimpuestoventa as venta_montoimpuestoventa',
                    DB::raw('IF(cliente.idtipopersona=1,
                      CONCAT(cliente.apellidos,", ",cliente.nombre),
                      CONCAT(cliente.apellidos)) as cliente')
                )
                ->orderBy('s_facturacioncomunicacionbajadetalle.id','asc')
                ->get();   
            return view('layouts/backoffice/tienda/sistema/facturacioncomunicacionbaja/detalle',[
                'tienda'                      => $tienda,
                'facturacioncomunicacionbaja' => $facturacioncomunicacionbaja,
                'facturacioncomunicacionbajadetalle' => $facturacioncomunicacionbajadetalle
            ]);

        }
        elseif($request->input('view') == 'ticket') {
            return view('layouts/backoffice/tienda/sistema/facturacioncomunicacionbaja/ticket',[
                'tienda' => $tienda,
                'facturacioncomunicacionbaja'       => $facturacioncomunicacionbaja
            ]);
        }
        elseif($request->input('view') == 'ticketpdf') {
            $facturacioncomunicacionbajadetalle = DB::table('s_facturacioncomunicacionbajadetalle')
                ->leftJoin('users as cliente','cliente.id','s_facturacioncomunicacionbajadetalle.idusuariocliente')
                ->where('s_facturacioncomunicacionbajadetalle.idfacturacioncomunicacionbaja',$facturacioncomunicacionbaja->id)
                ->select(
                    's_facturacioncomunicacionbajadetalle.*',
                    'cliente.identificacion as clienteidentificacion',
                    DB::raw('IF(cliente.idtipopersona=1,
                      CONCAT(cliente.apellidos,", ",cliente.nombre),
                      CONCAT(cliente.apellidos)) as cliente')
                )
                ->orderBy('s_facturacioncomunicacionbajadetalle.id','asc')
                ->get();   
          
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacioncomunicacionbaja/ticketpdf',[
                'tienda'                    => $tienda,
                'facturacioncomunicacionbaja'        => $facturacioncomunicacionbaja,
                'facturacioncomunicacionbajadetalle' => $facturacioncomunicacionbajadetalle,
            ]);
            $ticket = 'Ticket_'.str_pad($facturacioncomunicacionbaja->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        
        if($request->input('view') == 'reenviarcomprobante'){

            $result = facturador_comunicacionbaja($id);

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
          
              $facturacioncomunicacionbaja = DB::table('s_facturacioncomunicacionbaja')
                  ->join('users as responsable','responsable.id','s_facturacioncomunicacionbaja.idusuarioresponsable')
                  ->join('s_agencia','s_agencia.id','s_facturacioncomunicacionbaja.idagencia')
                  ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacioncomunicacionbaja.idfacturacionrespuesta')
                  ->where('s_facturacioncomunicacionbaja.id',$request->input('idfacturacioncomunicacionbaja'))
                  ->select(
                      's_facturacioncomunicacionbaja.*',
                      'responsable.nombre as responsablenombre',
                      's_agencia.logo as agencialogo',
                      's_facturacionrespuesta.codigo as respuestacodigo',
                      's_facturacionrespuesta.estado as respuestaestado',
                      's_facturacionrespuesta.mensaje as respuestamensaje',
                      's_facturacionrespuesta.nombre as respuestanombre',
                  )
                  ->first();
             
          
              $facturacioncomunicacionbajadetalle = DB::table('s_facturacioncomunicacionbajadetalle')
                  ->leftJoin('users as cliente','cliente.id','s_facturacioncomunicacionbajadetalle.idusuariocliente')
                  ->where('s_facturacioncomunicacionbajadetalle.idfacturacioncomunicacionbaja',$facturacioncomunicacionbaja->id)
                  ->select(
                      's_facturacioncomunicacionbajadetalle.*',
                      'cliente.identificacion as clienteidentificacion',
                      DB::raw('IF(cliente.idtipopersona=1,
                        CONCAT(cliente.apellidos,", ",cliente.nombre),
                        CONCAT(cliente.apellidos)) as cliente')
                  )
                  ->orderBy('s_facturacioncomunicacionbajadetalle.id','asc')
                  ->get();   
          
              $tienda = DB::table('tienda')->whereId($idtienda)->first();

              $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacioncomunicacionbaja/ticketpdf',[
                  'tienda'                    => $tienda,
                  'facturacioncomunicacionbaja'        => $facturacioncomunicacionbaja,
                  'facturacioncomunicacionbajadetalle' => $facturacioncomunicacionbajadetalle,
              ]);
          
              $output = $pdf->output();
   
              $user = array (
                 'correo' => 'ventas@kayllapi.com',
                 'nombre' => strtoupper($facturacioncomunicacionbaja->emisor_nombrecomercial),
                 'correo_destino' => $request->input('enviarcorreo_email'),
                 'titulo' => 'COMUNICACIÓN DE BAJA '.str_pad($facturacioncomunicacionbaja->comunicacionbaja_correlativo, 6, "0", STR_PAD_LEFT),
                 'pdf' => $output,
                 'nombrepdf'=>'COMUNICACION_BAJA_'.str_pad($facturacioncomunicacionbaja->comunicacionbaja_correlativo, 6, "0", STR_PAD_LEFT).'.pdf',
                 'xml' => 'public/backoffice/tienda/'.$tienda->id.'/sunat/produccion/comunicacionbaja/'.$facturacioncomunicacionbaja->respuestanombre.'.xml',
              );

              Mail::send('app/email_comunicacionbaja',  [
                  'user'                      => $user,
                  'tienda'                    => $tienda,
                  'facturacioncomunicacionbaja'        => $facturacioncomunicacionbaja,
                  'facturacioncomunicacionbajadetalle' => $facturacioncomunicacionbajadetalle,
                ], function ($message) use ($user) {
                  $message->from($user['correo'],$user['nombre']);
                  $message->to($user['correo_destino'])->subject($user['titulo']);
                  $message->attach($user['xml']);
                  $message->attachData($user['pdf'], $user['nombrepdf'], [ 'mime' => 'application/pdf' ]);
              });


              return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha enviado correctamente.'
              ]);
        }
    }

    public function destroy(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }
}
