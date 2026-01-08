<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

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
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);

        $tienda = DB::table('tienda')->whereId($idtienda)->first();  
        $facturacionresumendiario = DB::table('s_facturacionresumendiariodetalle')
            ->join('s_facturacionresumendiario','s_facturacionresumendiario.id','s_facturacionresumendiariodetalle.idfacturacionresumendiario')
            ->join('users as responsable','responsable.id','s_facturacionresumendiario.idusuarioresponsable')
            ->leftJoin('users as cliente','cliente.id','s_facturacionresumendiariodetalle.idusuariocliente')
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionresumendiario.idfacturacionrespuesta')
            ->where('s_facturacionresumendiario.idtienda', $tienda->id)
            ->select(
                's_facturacionresumendiariodetalle.*',
                's_facturacionresumendiario.resumen_fechageneracion as resumen_fechageneracion',
                's_facturacionresumendiario.resumen_correlativo as resumen_correlativo',
                's_facturacionresumendiario.resumen_fecharesumen as resumen_fecharesumen',
                's_facturacionresumendiario.emisor_ruc as emisor_ruc',
                's_facturacionresumendiario.emisor_nombrecomercial as emisor_nombrecomercial',
                's_facturacionresumendiario.emisor_razonsocial as emisor_razonsocial',
                's_facturacionrespuesta.codigo as respuestacodigo',
                'responsable.nombre as responsablenombre',
                DB::raw('IF(cliente.idtipopersona=1,
                  CONCAT(cliente.apellidos,", ",cliente.nombre),
                  CONCAT(cliente.apellidos)) as cliente'),
                's_facturacionrespuesta.estado as respuestaestado',
            )
            ->orderBy('s_facturacionresumendiario.id','desc')
            ->paginate(10);
        return view('layouts/backoffice/tienda/sistema/facturacionresumendiario/index',[
            'tienda'                       => $tienda,
            'facturacionresumendiario'     => $facturacionresumendiario
        ]);
     

    }

    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $agencias = DB::table('s_agencia')->where('idtienda',$idtienda)->where('idestadofacturacion',1)->get();

        return view('layouts/backoffice/tienda/sistema/facturacionresumendiario/create',[
            'tienda'        => $tienda,
            'agencias'      => $agencias
        ]);
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if($request->input('view') == 'registrar') {
            
            $rules = [
               'idagencia'    => 'required',
               'fechaemision' => 'required',
               'comprobantes' => 'required',
            ]; 
            $messages = [
               'idagencia.required'     => 'La "Empresa" es Obligatorio.',
               'fechaemision.required'  => 'La "Fecha de Emisión es Obligatorio.',
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
          
                if ($value->facturador_estado==3 && $diff_fecha->days >= 5) {
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
                'resumen_fechageneracion' => $request->fechaemision,
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
                                  <option value="B'.$facturacion_serie1.'" tipodocumento="BOLETA">B'.$facturacion_serie1.'</option>
                                  <option value="BB'.$facturacion_serie2.'" tipodocumento="NOTACREDITO">BB'.$facturacion_serie2.'</option>';
            }
            return [ 'agenciaoption' => $agenciaoption ];
        }elseif($id == 'show-seleccionarboletafactura-fecha') {
            $facturacionboletafactura = DB::table('s_facturacionboletafactura')
                  ->join('users as usuariocliente','usuariocliente.id','s_facturacionboletafactura.idusuariocliente')
                  ->where('s_facturacionboletafactura.venta_fechaemision', 'LIKE', '%'.$request->fechaemision.'%')
                  ->where('s_facturacionboletafactura.idtienda',$idtienda)
                  ->where('s_facturacionboletafactura.venta_tipodocumento', '=', '03')
                  ->select(
                      's_facturacionboletafactura.*',
                      DB::raw('IF(usuariocliente.idtipopersona=1,
                      CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                      CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos)) as cliente')
                  )
                  ->get();
             $factura_notacredito =  [];
          
             foreach ($facturacionboletafactura as $facturaboleta) {
                $exist_facturacionresumendiario = DB::table('s_facturacionresumendiariodetalle')
                        ->where('s_facturacionresumendiariodetalle.idfacturacionboletafactura', $facturaboleta->id)
                        ->exists();
               
                $exist_comunicacionbaja = DB::table('s_facturacioncomunicacionbajadetalle')
                  ->where('s_facturacioncomunicacionbajadetalle.idfacturacionboletafactura', $facturaboleta->id)
                  ->exists();
                
               if (!$exist_facturacionresumendiario && !$exist_comunicacionbaja) {
                  $factura_notacredito[] = [
                     'venta_serie'       => $facturaboleta->venta_serie,
                     'venta_correlativo' => $facturaboleta->venta_correlativo,
                     'cliente'           => $facturaboleta->cliente,
                     'venta_subtotal'    => $facturaboleta->venta_subtotal,
                     'tipodocumento'     => 'BOLETA'
                   ];
               }
             }
            
             $notascreditos = DB::table('s_facturacionnotacredito')
                    ->join('users as usuariocliente','usuariocliente.id','s_facturacionnotacredito.idusuariocliente')
                    ->where('s_facturacionnotacredito.notacredito_fechaemision', 'LIKE', '%'.$request->fechaemision.'%')
                    ->where('s_facturacionnotacredito.notacredito_tipodocafectado', '=', '03')
                    ->where('s_facturacionnotacredito.idtienda',$idtienda)
                    ->select(
                        's_facturacionnotacredito.*',
                        DB::raw('IF(usuariocliente.idtipopersona=1,
                        CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos,", ",usuariocliente.nombre),
                        CONCAT(usuariocliente.identificacion," - ",usuariocliente.apellidos)) as cliente'),
                    )
                    ->get();
          
            
            foreach ($notascreditos as $notacredito) {
              $exist_facturacionresumendiario = DB::table('s_facturacionresumendiariodetalle')
                      ->where('s_facturacionresumendiariodetalle.idfacturacionnotacredito', $notacredito->id)
                      ->exists();
              
              $exist_comunicacionbaja = DB::table('s_facturacioncomunicacionbajadetalle')
                  ->where('s_facturacioncomunicacionbajadetalle.idfacturacionboletafactura', $notacredito->idfacturacionboletafactura)
                  ->exists();
              
              if (!$exist_facturacionresumendiario && !$exist_comunicacionbaja) {
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
                  'mensaje' => 'No se encontraron Boletas y Notas de Credito con esta Fecha.',
                ];
            }

            return [
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Boletas y Nota de Creditos Encontrada, seleccione para dar de baja.',
              'facturas'  => $factura_notacredito,
            ];
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
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionresumendiario.idfacturacionrespuesta')
            ->where('s_facturacionresumendiario.id',$id)
            ->select(
                's_facturacionresumendiario.*',
                'responsable.nombre as responsablenombre',
                's_agencia.logo as agencialogo',
                's_facturacionrespuesta.codigo as respuestacodigo',
                's_facturacionrespuesta.estado as respuestaestado',
                's_facturacionrespuesta.mensaje as respuestamensaje',
                's_facturacionrespuesta.nombre as respuestanombre',
            )
            ->first();
      
        if($request->input('view') == 'detalle') {
          
            $facturacionresumendetalle = DB::table('s_facturacionresumendiariodetalle')
                ->where('s_facturacionresumendiariodetalle.idfacturacionresumendiario',$facturacionresumen->id)
                ->orderBy('s_facturacionresumendiariodetalle.id','asc')
                ->get();   
            return view('layouts/backoffice/tienda/sistema/facturacionresumendiario/detalle',[
                'tienda'                   => $tienda,
                'facturacionresumen'       => $facturacionresumen,
                'facturacionresumendetalle'=> $facturacionresumendetalle
            ]);
        }
        elseif($request->input('view') == 'ticket') {
            return view('layouts/backoffice/tienda/sistema/facturacionresumendiario/ticket',[
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
            $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionresumendiario/ticketpdf',[
                'tienda'                    => $tienda,
                'facturacionresumen'        => $facturacionresumen,
                'facturacionresumendetalle' => $facturacionresumendetalle,
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
                  ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionresumendiario.idfacturacionrespuesta')
                  ->where('s_facturacionresumendiario.id',$request->input('idfacturacionresumen'))
                  ->select(
                      's_facturacionresumendiario.*',
                      'responsable.nombre as responsablenombre',
                      's_agencia.logo as agencialogo',
                      's_facturacionrespuesta.codigo as respuestacodigo',
                      's_facturacionrespuesta.estado as respuestaestado',
                      's_facturacionrespuesta.mensaje as respuestamensaje',
                      's_facturacionrespuesta.nombre as respuestanombre',
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
          
              $tienda = DB::table('tienda')->whereId($idtienda)->first();

              $pdf = PDF::loadView('layouts/backoffice/tienda/sistema/facturacionresumendiario/ticketpdf',[
                  'tienda'                    => $tienda,
                  'facturacionresumen'        => $facturacionresumen,
                  'facturacionresumendetalle' => $facturacionresumendetalle,
              ]);
          
              $output = $pdf->output();
   
              $user = array (
                 'correo' => 'ventas@kayllapi.com',
                 'nombre' => strtoupper($facturacionresumen->emisor_nombrecomercial),
                 'correo_destino' => $request->input('enviarcorreo_email'),
                 'titulo' => 'RESUMEN DIARIO '.str_pad($facturacionresumen->resumen_correlativo, 6, "0", STR_PAD_LEFT),
                 'pdf' => $output,
                 'nombrepdf'=>'RESUMEN_DIARIO_'.str_pad($facturacionresumen->resumen_correlativo, 6, "0", STR_PAD_LEFT).'.pdf',
                 'xml' => 'public/backoffice/tienda/'.$tienda->id.'/sunat/produccion/resumendiario/'.$facturacionresumen->respuestanombre.'.xml',
              );

              Mail::send('app/email_resumendiario',  [
                  'user'                      => $user,
                  'tienda'                    => $tienda,
                  'facturacionresumen'        => $facturacionresumen,
                  'facturacionresumendetalle' => $facturacionresumendetalle,
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
