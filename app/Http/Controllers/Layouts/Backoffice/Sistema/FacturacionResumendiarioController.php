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
        
        return view(sistema_view().'/facturacionresumendiario/tabla',[
            'tienda' => $tienda,
        ]);
    }

    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $agencias = DB::table('s_agencia')->where('idtienda',$idtienda)->where('idestadofacturacion',1)->get();

        return view(sistema_view().'/facturacionresumendiario/create',[
            'tienda'   => $tienda,
            'agencias' => $agencias
        ]);
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if($request->input('view') == 'registrar') {
            
            $rules = [
               'idagencia'    => 'required',
               'idboletafactura' => 'required',
            ]; 
            $messages = [
               'idagencia.required'     => 'La "Empresa" es Obligatorio.',
               'idboletafactura.required'  => 'El "Nro del Comprobante" es Obligatorio.',
            ];
            
            $this->validate($request,$rules,$messages);
            
            $factura  = DB::table('s_facturacionboletafactura')->whereId($request->idboletafactura)->first();
            $fechaemision = $factura->venta_fechaemision;

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
         
            $factura      = DB::table('s_facturacionboletafactura')->whereId($request->idboletafactura)->first();

            DB::table('s_facturacionresumendiariodetalle')->insert([
               'tipodocumento'              => $factura->venta_tipodocumento,
               'serienumero'                => $factura->venta_serie.'-'.$factura->venta_correlativo,
               'estado'                     => 3, 
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
          
            $result = facturador_resumendiario($idfacturacionresumendiario);
            
            // json_facturacionresumendiario($idtienda, Auth::user()->idsucursal, Auth::user()->id);
            return [
                  'resultado' => $result['resultado'],
                  'mensaje'   => $result['mensaje']
            ];
        }
        else if( $request->input('view') == 'registrar_notacredito' ){
            $rules = [
               'motivo'    => 'required',
            ]; 
            $messages = [
               'motivo.required'     => 'La "Motivo" es Obligatorio.',
            ];
            
            $this->validate($request,$rules,$messages);
            
            $notacredito  = DB::table('s_facturacionnotacredito')->whereId($request->idboletafacturanotacredito)->first();
            $fechaemision = $notacredito->notacredito_fechaemision;

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
         
            $factura      = DB::table('s_facturacionnotacredito')->whereId($request->idboletafacturanotacredito)->first();

            DB::table('s_facturacionresumendiariodetalle')->insert([
               'tipodocumento'              => $notacredito->notacredito_tipodocumento,
               'serienumero'                => $notacredito->notacredito_serie.'-'.$notacredito->notacredito_correlativo,
               'estado'                     => 3, 
               'clientetipo'                => $notacredito->cliente_tipodocumento,
               'clientenumero'              => $notacredito->cliente_numerodocumento,
               'total'                      => $notacredito->notacredito_montoimpuestoventa,
               'operacionesgravadas'        => $notacredito->notacredito_montooperaciongravada,
               'operacionesinafectas'       => 0,
               'operacionesexoneradas'      => 0,
               'otroscargos'                => 0,
               'montoigv'                   => $notacredito->notacredito_montoigv,
               'idfacturacionboletafactura' => 0,
               'idfacturacionnotacredito'   => $notacredito->id,
               'idagencia'                  => $notacredito->idagencia,
               'idtienda'                   => $notacredito->idtienda,
               'idusuariocliente'           => $notacredito->idusuariocliente,                 
               'idfacturacionresumendiario' => $idfacturacionresumendiario,
           ]);
          
            $result = facturador_resumendiario($idfacturacionresumendiario);
            
            // json_facturacionresumendiario($idtienda, Auth::user()->idsucursal, Auth::user()->id);
            return [
                  'resultado' => $result['resultado'],
                  'mensaje'   => $result['mensaje']
            ];
        }
        else if( $request->input('view') == 'registrar_notadebito' ){
            $rules = [
               'motivo'    => 'required',
            ]; 
            $messages = [
               'motivo.required'     => 'La "Motivo" es Obligatorio.',
            ];
            
            $this->validate($request,$rules,$messages);
            
            $notadebito  = DB::table('s_facturacionnotadebito')->whereId($request->idboletafacturanotadebito)->first();
            $fechaemision = $notadebito->notadebito_fechaemision;

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
         
            // $factura      = DB::table('s_facturacionnotadebito')->whereId($request->idboletafacturanotadebito)->first();

            DB::table('s_facturacionresumendiariodetalle')->insert([
               'tipodocumento'              => $notadebito->notadebito_tipodocumento,
               'serienumero'                => $notadebito->notadebito_serie.'-'.$notadebito->notadebito_correlativo,
               'estado'                     => 3, 
               'clientetipo'                => $notadebito->cliente_tipodocumento,
               'clientenumero'              => $notadebito->cliente_numerodocumento,
               'total'                      => $notadebito->notadebito_montoimpuestoventa,
               'operacionesgravadas'        => $notadebito->notadebito_montooperaciongravada,
               'operacionesinafectas'       => 0,
               'operacionesexoneradas'      => 0,
               'otroscargos'                => 0,
               'montoigv'                   => $notadebito->notadebito_montoigv,
               'idfacturacionboletafactura' => 0,
               'idfacturacionnotacredito'   => 0,
               'idfacturacionnotadebito'    => $notadebito->id,
               'idagencia'                  => $notadebito->idagencia,
               'idtienda'                   => $notadebito->idtienda,
               'idusuariocliente'           => $notadebito->idusuariocliente,                 
               'idfacturacionresumendiario' => $idfacturacionresumendiario,
           ]);
          
            $result = facturador_resumendiario($idfacturacionresumendiario);
            
            // json_facturacionresumendiario($idtienda, Auth::user()->idsucursal, Auth::user()->id);
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
        if($id=='show_table'){
            $tienda = DB::table('tienda')->whereId($idtienda)->first();  
            $idsucursal = Auth::user()->idsucursal; 
  
            $facturacionresumendiario = DB::table('s_facturacionresumendiariodetalle')
                      ->join('s_facturacionresumendiario','s_facturacionresumendiario.id','s_facturacionresumendiariodetalle.idfacturacionresumendiario')
                      ->join('users as responsable','responsable.id','s_facturacionresumendiario.idusuarioresponsable')
                      ->leftJoin('users as cliente','cliente.id','s_facturacionresumendiariodetalle.idusuariocliente')
                      ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionresumendiario.idfacturacionrespuesta')
                      ->where('s_facturacionresumendiario.idtienda', $tienda->id)
                      ->where('s_facturacionresumendiario.idsucursal', $idsucursal)
                      ->select(
                          's_facturacionresumendiariodetalle.*',
                          's_facturacionresumendiario.id as ids_facturacionresumendiario',
                          's_facturacionresumendiario.resumen_fechageneracion as resumen_fechageneracion',
                          's_facturacionresumendiario.resumen_correlativo as resumen_correlativo',
                          's_facturacionresumendiario.resumen_fecharesumen as resumen_fecharesumen',
                          's_facturacionresumendiario.emisor_ruc as emisor_ruc',
                          's_facturacionresumendiario.emisor_nombrecomercial as emisor_nombrecomercial',
                          's_facturacionresumendiario.emisor_razonsocial as emisor_razonsocial',
                          's_facturacionrespuesta.codigo as respuestacodigo',
                          'responsable.nombre as responsablenombre',
                          DB::raw('IF(cliente.idtipopersona=1,
                            CONCAT(cliente.nombrecompleto),
                            CONCAT(cliente.nombrecompleto)) as cliente'),
                          's_facturacionrespuesta.estado as respuestaestado',
                      )
                      ->orderBy('s_facturacionresumendiario.id','desc')
                      ->paginate($request->length,'*',null,($request->start/$request->length)+1);
            
            $tabla = [];
        
            foreach ($facturacionresumendiario as $value) {
                $correlativo = date_format(date_create($value->resumen_fecharesumen), 'd-m-Y h:i:s A');
                $fecha_resumen = $value->resumen_fechageneracion;
                $comprobante_afectado = '';
                switch($value->tipodocumento){
                    case  '03':
                        $comprobante_afectado = 'BOLETA';
                            break;
                    case '07':
                        $comprobante_afectado = 'NOTA DE CRÉDITO';
                        break;
                    case '08':
                        $comprobante_afectado =  'NOTA DE DÉBITO';
                            break;
                    }
                $estado_envio = '';
            
                if($value->respuestaestado=='ACEPTADA') {
                $estado_envio = 'Aceptada';          
                } elseif ($value->respuestaestado=='OBSERVACIONES') {
                $estado_envio = 'Observaciones';           
                } elseif ($value->respuestaestado=='RECHAZADA') {
                if($value->respuestacodigo=='2223') {
                    $estado_envio = 'Aceptada';        
                } else {
                    $estado_envio = 'Rechazada';        
                }
                } elseif ($value->respuestaestado=='EXCEPCION') {
                $estado_envio = 'Excepción';          
                } else {
                if($value->respuestacodigo=='0402') {
                    $estado_envio = 'Aceptada';    
                } else {
                    $estado_envio = 'No enviado';
                }
                }
            
                $tabla[] = [
                'id' => $value->id,
                'correlativo' => $correlativo,
                'fecha_generacion' => $fecha_resumen,
                'comprobante_afectado' => $comprobante_afectado.'<br>'.$value->serienumero,
                'total' => $value->total,
                'estado' => $value->estado==1?'Adicionado':($value->estado==2?'Modificado':($value->estado==3?'Anulado':'---')),
                'cliente' => $value->clientenumero.' - '.$value->cliente,
                'emisor' => $value->emisor_ruc.' - '.$value->emisor_razonsocial,
                'estado_envio' => $estado_envio,
                'opcion' => [
                        [
                        'nombre' => 'Comprobantes',
                        'onclick' => '/'.$idtienda.'/facturacionresumendiario/'.$value->ids_facturacionresumendiario.'/edit?view=ticket',
                        'icono' => 'receipt',
                    ]
                ],
                ];
            }
            return response()->json([
                'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $facturacionresumendiario->total(),
                'data'            => $tabla,
            ]);
        }
        else if($id == 'show-seleccionarboletafactura') {
            
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
                        CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombrecompleto),
                        CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombrecompleto)) as cliente')
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
                      CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombrecompleto),
                      CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombrecompleto)) as cliente')
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
                        CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombrecompleto),
                        CONCAT(usuariocliente.identificacion," - ",usuariocliente.nombrecompleto)) as cliente'),
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
            return view(sistema_view().'/facturacionresumendiario/detalle',[
                'tienda'                   => $tienda,
                'facturacionresumen'       => $facturacionresumen,
                'facturacionresumendetalle'=> $facturacionresumendetalle
            ]);
        }
        elseif($request->input('view') == 'ticket') {
          $ticket = new \stdClass();
          //DATOS EMISOR
          $ticket->tipo_fuente = configuracion($tienda->id,'sistema_tipoletra')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_tipoletra')['valor']:'Helvetica';
          $ticket->ancho_ticket = configuracion($tienda->id,'sistema_anchoticket')['resultado']=='CORRECTO'?(configuracion($tienda->id,'sistema_anchoticket')['valor']-1):'8'.'cm';
          $ticket->ruc_emision = $facturacionresumen->emisor_ruc;
          $ticket->razonsocial_emisor = strtoupper($facturacionresumen->emisor_razonsocial);
          $ticket->logotipo = url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$facturacionresumen->agencialogo);
          $ticket->direccion_emisor = strtoupper($facturacionresumen->emisor_direccion);
          $ticket->ubigeo_emisor = strtoupper($facturacionresumen->emisor_distrito.' - '.$facturacionresumen->emisor_provincia.' - '.$facturacionresumen->emisor_departamento);
    
          $ticket->correlativo_documento = str_pad($facturacionresumen->resumen_correlativo, 8, "0", STR_PAD_LEFT);
          
          $ticket->fechaemision = date_format(date_create($facturacionresumen->resumen_fecharesumen),"d/m/Y h:i:s A");
          $ticket->fechageneracion = date_format(date_create($facturacionresumen->resumen_fechageneracion),"d/m/Y h:i:s A");
          
         $facturacionresumendetalle = DB::table('s_facturacionresumendiariodetalle')
                ->leftJoin('users as cliente','cliente.id','s_facturacionresumendiariodetalle.idusuariocliente')
                ->where('s_facturacionresumendiariodetalle.idfacturacionresumendiario',$facturacionresumen->id)
                ->select(
                    's_facturacionresumendiariodetalle.*',
                    DB::raw('IF(cliente.idtipopersona=1,
                      CONCAT(cliente.nombrecompleto),
                      CONCAT(cliente.nombrecompleto)) as cliente')
                )
                ->orderBy('s_facturacionresumendiariodetalle.id','asc')
                ->get(); 
          
          
          
          
          return view(sistema_view().'/facturacionresumendiario/comprobante',[
            'ticket'                    => $ticket,
            'facturacionresumendetalle' => $facturacionresumendetalle
          ]);
//             return view(sistema_view().'/facturacionresumendiario/ticket',[
//                 'tienda' => $tienda,
//                 'facturacionresumen'       => $facturacionresumen
//             ]);
        }
//         elseif($request->input('view') == 'ticketpdf') {
//             $facturacionresumendetalle = DB::table('s_facturacionresumendiariodetalle')
//                 ->leftJoin('users as cliente','cliente.id','s_facturacionresumendiariodetalle.idusuariocliente')
//                 ->where('s_facturacionresumendiariodetalle.idfacturacionresumendiario',$facturacionresumen->id)
//                 ->select(
//                     's_facturacionresumendiariodetalle.*',
//                     DB::raw('IF(cliente.idtipopersona=1,
//                       CONCAT(cliente.apellidos,", ",cliente.nombre),
//                       CONCAT(cliente.apellidos)) as cliente')
//                 )
//                 ->orderBy('s_facturacionresumendiariodetalle.id','asc')
//                 ->get();   
//             $pdf = PDF::loadView(sistema_view().'/facturacionresumendiario/ticketpdf',[
//                 'tienda'                    => $tienda,
//                 'facturacionresumen'        => $facturacionresumen,
//                 'facturacionresumendetalle' => $facturacionresumendetalle,
//             ]);
//             $ticket = 'Ticket_'.str_pad($facturacionresumen->id, 8, "0", STR_PAD_LEFT);
//             return $pdf->stream($ticket.'.pdf');
//         }
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
                        CONCAT(cliente.nombrecompleto),
                        CONCAT(cliente.nombrecompleto)) as cliente')
                  )
                  ->orderBy('s_facturacionresumendiariodetalle.id','asc')
                  ->get();   
          
              $tienda = DB::table('tienda')->whereId($idtienda)->first();

              $pdf = PDF::loadView(sistema_view().'/facturacionresumendiario/ticketpdf',[
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
