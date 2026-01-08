<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;
use Mail;
use DateTime;

class FacturacionResumendiarioController extends Controller
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
        $facturacionresumendiario = DB::table('s_facturacionresumendiariodetalle')
            ->join('s_facturacionresumendiario','s_facturacionresumendiario.id','s_facturacionresumendiariodetalle.idfacturacionresumendiario')
            ->join('users as responsable','responsable.id','s_facturacionresumendiario.idusuarioresponsable')
            ->leftJoin('users as cliente','cliente.id','s_facturacionresumendiariodetalle.idusuariocliente')
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.s_idfacturacionresumendiario','s_facturacionresumendiario.id')
            ->where('s_facturacionresumendiario.idtienda', $tienda->id)
            ->select(
                's_facturacionresumendiariodetalle.*',
                's_facturacionresumendiario.resumen_fechageneracion as resumen_fechageneracion',
                's_facturacionresumendiario.resumen_correlativo as resumen_correlativo',
                's_facturacionresumendiario.resumen_fecharesumen as resumen_fecharesumen',
                's_facturacionresumendiario.emisor_ruc as emisor_ruc',
                's_facturacionresumendiario.emisor_nombrecomercial as emisor_nombrecomercial',
                'responsable.nombre as responsablenombre',
                DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
                's_facturacionrespuesta.estado as respuestaestado',
            )
            ->orderBy('s_facturacionresumendiario.id','desc')
            ->get();
      $tabla = [];
      foreach($facturacionresumendiario as $value){
        $comprobante = $value->tipodocumento=='03'?'BOLETA':($value->tipodocumento=='07'?'NOTA DE CRÉDITO':'---');
        $correlativo= str_pad($value->resumen_correlativo, 8, "0", STR_PAD_LEFT);
        $fecha_gene = date_format(date_create($value->resumen_fechageneracion), 'd-m-Y h:i:s A');
        $fecha_resu = date_format(date_create($value->resumen_fecharesumen), 'd-m-Y h:i:s A');
        $estado =  $value->estado==1?'Adicionado':($value->estado==2?'Modificado':($value->estado==3?'Anulado':'---'));
        $sunat ='';
           switch($value->respuestaestado){
             case  'ACEPTADA':
                $sunat =  '<span><i class="fa fa-check"></i> Aceptada</span>';
                   break;
             case 'OBSERVACIONES':
                  $sunat = '<span><i class="fas fa-sync-alt"></i> Observaciones</span>';
                  break;
             case 'RECHAZADA':
                 $sunat =  '<span><i class="fas fa-sync-alt"></i> Rechazada</span>';
                   break;
             case 'EXCEPCION':
                  $sunat =  '<span><i class="fa fa-sync-alt"></i> Excepción</span>';
                   break;
             default:
                  $sunat = '<span><i class="fa fa-sync-alt"></i> No enviado</span>';
           }
        
        $tabla[]=[
            'id'  => $value->id,
            'titulo' => 'CORRELATIVO<br> FECHA GENERACION <br> FECHA RESUMEN <br> RUC <br> EMISOR  <br> COMPROBANTE <br> SERIE-CORRELATIVO <br> ',
            'nombre' => ': '.$correlativo.'<br>: '.$fecha_gene.'<br>: '.$fecha_resu .'<br>: '.$value->emisor_ruc.'<br>: '. $value->emisor_nombrecomercial.'<br>: '.$comprobante.'<br>: '.$value->serienumero.'<br>',
            'titulo2' => 'DNI/RUC <br> CLIENTE <br> SUB TOTAL <br> IGV <br> TOTAL <br> ESTADO <br>RESPONSABLE<br>SUNAT<br>',
            'nombre2' => ': '.$value->clientenumero .'<br>: '.$value->cliente.'<br>: '.$value->operacionesgravadas.'<br>: '. $value->montoigv.'<br>: '.$value->total.'<br>: '.$estado.'<br>: '.$value->responsablenombre.'<br>: '.$sunat,
           
            'option'    => '<div class="option3">
                                        <a href="javascript:;" onclick="table_modal('.$value->idfacturacionresumendiario.',\'Detalle\',\'detalle\')" class="btn-tabla"><div class="btn-tabla-detail"></div>Detalle</a>
                                        <a style="width:97px" href="javascript:;" onclick="table_modal('.$value->idfacturacionresumendiario.',\'Ticket de Comprobante \',\'ticket\')" class="btn-tabla"><div class="btn-tabla-detail"></div>Comprobante</a>
                              </div>',
        ];
      }
      json_create($idtienda,$request->name_modulo,$tabla);
        return view('layouts/backoffice/tienda/nuevosistema/facturacionresumendiario/index',[
            'tienda'                       => $tienda,
            'facturacionresumendiario'     => $facturacionresumendiario
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
      
        $configuracion  = configuracion_facturacion($idtienda);
      
        $agencias = DB::table('s_agencia')->where('idtienda',$idtienda)->where('idestadofacturacion',1)->get();

        return view('layouts/backoffice/tienda/nuevosistema/facturacionresumendiario/create',[
            'tienda'        => $tienda,
            'configuracion' => $configuracion,
            'agencias'      => $agencias
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
               'idagencia'    => 'required',
               'comprobantes' => 'required',
            ]; 
            $messages = [
               'idagencia.required'     => 'La "Empresa" es Obligatorio.',
               'comprobantes.required'  => 'Los "Comprobantes" son Obligatorio.',
            ];
            
            $this->validate($request,$rules,$messages);
            
            $comprobantes = json_decode($request->input('comprobantes'));
           
            if(empty($comprobantes)) {
                return response()->json([
                   'resultado' => 'ERROR',
                   'mensaje'   => 'No hay boletas para su envio'
                ]);
            }
          
            foreach ($comprobantes as $value) {

                $serie = '';
                $correlativo = '';
                if ($value->tipo == 'BOLETA') {
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
              
                if($value->facturador_estado==''){
                    return response()->json([
                       'resultado' => 'ERROR',
                       'mensaje'   => 'El estado del comprobante "'.$serie.'-'.$correlativo.'" es obligatorio'
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
            $correlativo = DB::table('s_facturacionresumendiario')
                          ->where('s_facturacionresumendiario.idtienda', $idtienda)
                          ->where('s_facturacionresumendiario.emisor_ruc', $agencia->ruc)
                          ->orderBy('s_facturacionresumendiario.resumen_correlativo', 'desc')
                          ->first();
                  
            $resumen_correlativo = ($correlativo == '') ? 1 : $correlativo->resumen_correlativo + 1;
       
            $idfacturacionresumendiario = DB::table('s_facturacionresumendiario')->insertGetId([
                'emisor_ruc'              => $agencia->ruc,
                'emisor_razonsocial'      => $agencia->razonsocial,
                'emisor_nombrecomercial'  => $agencia->nombrecomercial,
                'emisor_ubigeo'           => $tienda->tiendaubigeocodigo,
                'emisor_departamento'     => $tienda->tiendaubigeodepartamento,
                'emisor_provincia'        => $tienda->tiendaubigeoprovincia,
                'emisor_distrito'         => $tienda->tiendaubigeodistrito,
                'emisor_urbanizacion'     => '',
                'emisor_direccion'        => $tienda->tiendadireccion,
                'resumen_correlativo'     => $resumen_correlativo,
                'resumen_fechageneracion' => Carbon::now(),
                'resumen_fecharesumen'    => Carbon::now(),
                'idagencia'               => $agencia->id,
                'idtienda'                => $idtienda,
                'idusuarioresponsable'    => Auth::user()->id
            ]);
         
            foreach ($comprobantes as $value) {
               if ($value->tipo == 'BOLETA') {
                  $factura      = DB::table('s_facturacionboletafactura')->whereId($value->idventa_boletafactura)->first();
                 
                  DB::table('s_facturacionresumendiariodetalle')->insert([
                     'tipodocumento'              => $factura->venta_tipodocumento,
                     'serienumero'                => $factura->venta_serie.'-'.$factura->venta_correlativo,
                     'estado'                     => $value->facturador_estado, 
                     'clientetipo'                => $factura->cliente_tipodocumento,
                     'clientenumero'              => $factura->cliente_numerodocumento,
                     'total'                      => $factura->venta_montoimpuestoventa,
                     'operacionesgravadas'        => $factura->venta_montooperaciongravada,
                     'operacionesinafectas'       => 0,
                     'operacionesexoneradas'      => 0,
                     'otroscargos'                => 0,
                     'montoigv'                   => $factura->venta_montoigv,
                     'idfacturacionboletafactura' => $factura->id,
                     'idfacturacionnotacredito'   => 0,
                     'idagencia'                  => $factura->idagencia,
                     'idtienda'                   => $factura->idtienda,
                     'idusuariocliente'           => $factura->idusuariocliente,                 
                     'idfacturacionresumendiario' => $idfacturacionresumendiario,
                 ]);
               }else if ($value->tipo == 'NOTACREDITO') {
                  $notacredito  = DB::table('s_facturacionnotacredito')->whereId($value->idventa_boletafactura)->first();
                 
                  DB::table('s_facturacionresumendiariodetalle')->insert([
                     'tipodocumento'              => $notacredito->notacredito_tipodocumento,
                     'serienumero'                => $notacredito->notacredito_serie.'-'.$notacredito->notacredito_correlativo,
                     'estado'                     => $value->facturador_estado, 
                     'clientetipo'                => $notacredito->cliente_tipodocumento,
                     'clientenumero'              => $notacredito->cliente_numerodocumento,
                     'total'                      => $notacredito->notacredito_montoimpuestoventa,
                     'operacionesgravadas'        => $notacredito->notacredito_montooperaciongravada,
                     'operacionesinafectas'       => 0,
                     'operacionesexoneradas'      => 0,
                     'otroscargos'                => 0,
                     'montoigv'                   => $notacredito->notacredito_montoigv,
                     'idfacturacionboletafactura' => $notacredito->idfacturacionboletafactura,
                     'idfacturacionnotacredito'   => $notacredito->id,
                     'idagencia'                  => $notacredito->idagencia,
                     'idtienda'                   => $notacredito->idtienda,   
                     'idusuariocliente'           => $notacredito->idusuariocliente,               
                     'idfacturacionresumendiario' => $idfacturacionresumendiario,
                 ]);
               }
            }
          
            $result = facturador_resumendiario($idfacturacionresumendiario);

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
        if($id == 'show-seleccionarboletafactura') {
          
            $data = '';
            $resultado = '';
            $mensaje = '';
            if ($request->input('tipodocumento') == 'BOLETA') {

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
                    $facturacionresumendiario = DB::table('s_facturacionresumendiariodetalle')
                        ->where('s_facturacionresumendiariodetalle.idfacturacionboletafactura', $facturacionboletafactura->id)
                        ->limit(1)
                        ->first();
                    if($facturacionresumendiario!=''){
                        $estado = '';
                        if($facturacionresumendiario->estado==1){
                            $estado = 'Adicionado';
                        }elseif($facturacionresumendiario->estado==2){
                            $estado = 'Modificado';
                        }elseif($facturacionresumendiario->estado==3){
                            $estado = 'Anulado';
                        }
                        $resultado = 'ERROR';
                        $mensaje = 'El Boleta "'.$facturacionboletafactura->venta_serie.'-'.str_pad($facturacionboletafactura->venta_correlativo, 8, "0", STR_PAD_LEFT).'", fue "'.$estado.'" con Resumen Diario anteriormente.';
                    }else{
                        $data =  [
                          'tipo'                        => 'BOLETA',
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
              
            }elseif($request->input('tipodocumento') == 'NOTACREDITO'){
  
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
                    $facturacionresumendiario = DB::table('s_facturacionresumendiariodetalle')
                        ->where('s_facturacionresumendiariodetalle.idfacturacionnotacredito', $notacredito->id)
                        ->limit(1)
                        ->first();
                    if($facturacionresumendiario!=''){
                        $estado = '';
                        if($facturacionresumendiario->estado==1){
                            $estado = 'Adicionado';
                        }elseif($facturacionresumendiario->estado==2){
                            $estado = 'Modificado';
                        }elseif($facturacionresumendiario->estado==3){
                            $estado = 'Anulado';
                        }
                        $resultado = 'ERROR';
                        $mensaje = 'La Nota de Crédito "'.$notacredito->venta_serie.'-'.str_pad($notacredito->venta_correlativo, 8, "0", STR_PAD_LEFT).'", fue "'.$estado.'" con Resumen Diario anteriormente.';
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
              
            }else{
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
                                  <option value="B'.$facturacion_serie1.'" tipodocumento="BOLETA">B'.$facturacion_serie1.'</option>
                                  <option value="BB'.$facturacion_serie2.'" tipodocumento="NOTACREDITO">BB'.$facturacion_serie2.'</option>';
            }
            return [ 'agenciaoption' => $agenciaoption ];
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
       $facturacionresumen = DB::table('s_facturacionresumendiario')
            ->join('users as responsable','responsable.id','s_facturacionresumendiario.idusuarioresponsable')
            ->join('s_agencia','s_agencia.id','s_facturacionresumendiario.idagencia')
            ->where('s_facturacionresumendiario.id',$id)
            ->select(
                's_facturacionresumendiario.*',
                'responsable.nombre as responsablenombre',
                's_agencia.logo as agencialogo',
            )
            ->first();
        if($request->input('view') == 'detalle') {
          
            $facturacionresumendetalle = DB::table('s_facturacionresumendiariodetalle')
                ->where('s_facturacionresumendiariodetalle.idfacturacionresumendiario',$facturacionresumen->id)
                ->orderBy('s_facturacionresumendiariodetalle.id','asc')
                ->get();   
            return view('layouts/backoffice/tienda/nuevosistema/facturacionresumendiario/detalle',[
                'tienda'                   => $tienda,
                'facturacionresumen'       => $facturacionresumen,
                'facturacionresumendetalle'=> $facturacionresumendetalle
            ]);
        }
        elseif($request->input('view') == 'ticket') {
            return view('layouts/backoffice/tienda/nuevosistema/facturacionresumendiario/ticket',[
                'tienda' => $tienda,
                'facturacionresumen'       => $facturacionresumen
            ]);
        }
        elseif($request->input('view') == 'ticketpdf') {
            $facturacionresumendetalle = DB::table('s_facturacionresumendiariodetalle')
                ->leftJoin('users as cliente','cliente.id','s_facturacionresumendiariodetalle.idusuariocliente')
                ->where('s_facturacionresumendiariodetalle.idfacturacionresumendiario',$facturacionresumen->id)
                ->select(
                    's_facturacionresumendiariodetalle.*',
                    DB::raw('IF(cliente.idtipopersona=1,
                      CONCAT(cliente.apellidos,", ",cliente.nombre),
                      CONCAT(cliente.apellidos)) as cliente')
                )
                ->orderBy('s_facturacionresumendiariodetalle.id','asc')
                ->get();   
          
            $facturacionrespuesta = DB::table('s_facturacionrespuesta')
                  ->where('s_facturacionrespuesta.s_idfacturacionresumendiario',$facturacionresumen->id)
                  ->first();
            $configuracion = tienda_configuracion($idtienda);
            $pdf = PDF::loadView('layouts/backoffice/tienda/nuevosistema/facturacionresumendiario/ticketpdf',[
                'tienda'                    => $tienda,
                'facturacionresumen'        => $facturacionresumen,
                'facturacionresumendetalle' => $facturacionresumendetalle,
                'configuracion'             => $configuracion,
                'respuesta'                 => $facturacionrespuesta
            ]);
            $ticket = 'Ticket_'.str_pad($facturacionresumen->id, 8, "0", STR_PAD_LEFT);
            return $pdf->stream($ticket.'.pdf');
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

            $result = facturador_resumendiario($id);

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
          
              $facturacionresumen = DB::table('s_facturacionresumendiario')
                  ->join('users as responsable','responsable.id','s_facturacionresumendiario.idusuarioresponsable')
                  ->join('s_agencia','s_agencia.id','s_facturacionresumendiario.idagencia')
                  ->where('s_facturacionresumendiario.id',$request->input('idfacturacionresumen'))
                  ->select(
                      's_facturacionresumendiario.*',
                      'responsable.nombre as responsablenombre',
                      's_agencia.logo as agencialogo',
                  )
                  ->first();
          
             
          
              $facturacionresumendetalle = DB::table('s_facturacionresumendiariodetalle')
                  ->leftJoin('users as cliente','cliente.id','s_facturacionresumendiariodetalle.idusuariocliente')
                  ->where('s_facturacionresumendiariodetalle.idfacturacionresumendiario',$facturacionresumen->id)
                  ->select(
                      's_facturacionresumendiariodetalle.*',
                      DB::raw('IF(cliente.idtipopersona=1,
                        CONCAT(cliente.apellidos,", ",cliente.nombre),
                        CONCAT(cliente.apellidos)) as cliente')
                  )
                  ->orderBy('s_facturacionresumendiariodetalle.id','asc')
                  ->get();   

              $facturacionrespuesta = DB::table('s_facturacionrespuesta')
                    ->where('s_facturacionrespuesta.s_idfacturacionresumendiario',$facturacionresumen->id)
                    ->first();
          
              if($facturacionrespuesta==''){
                  return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No se puede enviar un comprobante con error, revise por favor.'
                  ]);
              }
          
              $tienda = DB::table('tienda')->whereId($idtienda)->first();
              $configuracion = tienda_configuracion($idtienda);

              $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionresumendiario/ticketpdf',[
                  'tienda'                    => $tienda,
                  'facturacionresumen'        => $facturacionresumen,
                  'facturacionresumendetalle' => $facturacionresumendetalle,
                  'configuracion'             => $configuracion,
                  'respuesta'                 => $facturacionrespuesta,
              ]);
          
              $output = $pdf->output();
   
              $user = array (
                 'correo' => 'ventas@kayllapi.com',
                 'nombre' => strtoupper($facturacionresumen->emisor_nombrecomercial),
                 'correo_destino' => $request->input('enviarcorreo_email'),
                 'titulo' => 'RESUMEN DIARIO '.str_pad($facturacionresumen->resumen_correlativo, 6, "0", STR_PAD_LEFT),
                 'pdf' => $output,
                 'nombrepdf'=>'RESUMEN_DIARIO_'.str_pad($facturacionresumen->resumen_correlativo, 6, "0", STR_PAD_LEFT).'.pdf',
                 'xml' => 'public/backoffice/tienda/'.$tienda->id.'/sunat/produccion/resumendiario/'.$facturacionrespuesta->nombre.'.xml',
              );

              Mail::send('app/email_resumendiario',  [
                  'user'                      => $user,
                  'tienda'                    => $tienda,
                  'facturacionresumen'        => $facturacionresumen,
                  'facturacionresumendetalle' => $facturacionresumendetalle,
                  'configuracion'             => $configuracion,
                  'respuesta'                 => $facturacionrespuesta,
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
