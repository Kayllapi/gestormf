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

class CobranzaCreditoController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/cobranzacredito/tabla',[
                'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->view == 'registrar') {
            return view(sistema_view().'/cobranzacredito/create',[
                'tienda' => $tienda
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      if( $request->view == 'registrar' ){
        
        if($request->tipobusqueda == 'VENTA'){
          $rules['idventa']              = 'required';
          $messages['idventa.required']  = 'La "Venta" es Obligatorio.';
        }
        else if($request->tipobusqueda == 'COTIZACION'){
          $rules['idcotizacion']              = 'required';
          $messages['idcotizacion.required']  = 'La "Cotizacion" es Obligatorio.';
        }      
        $this->validate($request,$rules,$messages);

        

        if($request->tipobusqueda == 'VENTA'){
          $total_efectivo = 0;
          $total_deposito = 0;
          $db_formapagocobranza = [];
          if($request->formapago_idformapago==1){
              $i = 1;
              foreach(json_decode($request->formapagos) as $value){
                
                  if($request->input('formapago_idtipopago'.$value->num)==''){
                      $rules = array_merge($rules,[
                          'formapago_idtipopago'.$value->num => 'required'
                      ]);
                      $messages = array_merge($messages,[
                          'formapago_idtipopago'.$value->num.'.required' => 'El "Tipo de Pago" es Obligatorio.',
                      ]);
                  }
                  $formapagomonto = 0;
                  if($request->input('formapago_idtipopago'.$value->num)==1){
                      if($request->input('formapago_efectivo_montoefectivo'.$value->num)==''){
                          $rules = array_merge($rules,[
                              'formapago_efectivo_montoefectivo'.$value->num => 'required'
                          ]);
                          $messages = array_merge($messages,[
                              'formapago_efectivo_montoefectivo'.$value->num.'.required' => 'El "Monto en Efectivo" es Obligatorio.',
                          ]);
                      }
                      $formapagomonto = $request->input('formapago_efectivo_montoefectivo'.$value->num);
                      $total_efectivo = $total_efectivo+$formapagomonto;
                  }
                  elseif($request->input('formapago_idtipopago'.$value->num)==2){
                      if($request->input('formapago_deposito_idcuentabancaria'.$value->num)==''){
                          $rules = array_merge($rules,[
                              'formapago_deposito_idcuentabancaria'.$value->num => 'required'
                          ]);
                          $messages = array_merge($messages,[
                              'formapago_deposito_idcuentabancaria'.$value->num.'.required' => 'La "Cuenta Bancaria" es Obligatorio.',
                          ]);
                      }
                      if($request->input('formapago_deposito_numerooperacion'.$value->num)==''){
                          $rules = array_merge($rules,[
                              'formapago_deposito_numerooperacion'.$value->num => 'required'
                          ]);
                          $messages = array_merge($messages,[
                              'formapago_deposito_numerooperacion'.$value->num.'.required' => 'El "Número de Operación" es Obligatorio.',
                          ]);
                      }
                      if($request->input('formapago_deposito_montodeposito'.$value->num)==''){
                          $rules = array_merge($rules,[
                              'formapago_deposito_montodeposito'.$value->num => 'required'
                          ]);
                          $messages = array_merge($messages,[
                              'formapago_deposito_montodeposito'.$value->num.'.required' => 'El "Monto en Depósito" es Obligatorio.',
                          ]);
                      }
                      $formapagomonto = $request->input('formapago_deposito_montodeposito'.$value->num);
                      $total_deposito = $total_deposito+$formapagomonto;
                  }   
                  $db_formapagocobranza[] = [
                      'orden'           => $i,
                      'idtipopago'      => $request->input('formapago_idtipopago'.$value->num),
                      'monto'           => $formapagomonto,
                      'cuentabancaria'  => $request->input('formapago_deposito_idcuentabancaria'.$value->num),
                      'numerooperacion' => $request->input('formapago_deposito_numerooperacion'.$value->num),
                  ];
                  $i++;
              }
              // dd($request->montorestante);
              // dd($request->formapago_totalpagado);
              if($request->montorestante < $request->formapago_totalpagado){
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'El "Total Pagado" debe ser igual o menor al "Total Restante".',
                  ]);
              }
          }
          elseif($request->formapago_idformapago==2){
              $rules = array_merge($rules,[
                  'formapago_credito_fechainicio' => 'required',
                  'formapago_credito_ultimafecha' => 'required',
              ]);
          }

          $venta = DB::table('s_venta')->whereId($request->idventa)->first();
          $data_pagocobranza = $venta->db_formapagocobranza ? json_decode($venta->db_formapagocobranza,true): [];

          $max_orden_pago = count($data_pagocobranza) > 0 ?  max(array_column($data_pagocobranza, 'orden')) : 0;

          foreach ($db_formapagocobranza as $key => $value) {
            $db_formapagocobranza[$key]['orden'] = $max_orden_pago + $key + 1;
          }

          $data_pagocobranza_json = array_merge($data_pagocobranza, $db_formapagocobranza);
          
          
          DB::table('s_venta')->whereId($request->idventa)->update([
            'db_formapagocobranza' => json_encode($data_pagocobranza_json)
          ]);

          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje' => 'Se ha registrado correctamente.',
          ]);
        }
        else if($request->tipobusqueda == 'COTIZACION'){
          $total_efectivo = 0;
          $total_deposito = 0;
          $db_formapagocobranza = [];
          if($request->formapago_idformapago==1){
              $i = 1;
              foreach(json_decode($request->formapagos) as $value){
                
                  if($request->input('formapago_idtipopago'.$value->num)==''){
                      $rules = array_merge($rules,[
                          'formapago_idtipopago'.$value->num => 'required'
                      ]);
                      $messages = array_merge($messages,[
                          'formapago_idtipopago'.$value->num.'.required' => 'El "Tipo de Pago" es Obligatorio.',
                      ]);
                  }
                  $formapagomonto = 0;
                  if($request->input('formapago_idtipopago'.$value->num)==1){
                      if($request->input('formapago_efectivo_montoefectivo'.$value->num)==''){
                          $rules = array_merge($rules,[
                              'formapago_efectivo_montoefectivo'.$value->num => 'required'
                          ]);
                          $messages = array_merge($messages,[
                              'formapago_efectivo_montoefectivo'.$value->num.'.required' => 'El "Monto en Efectivo" es Obligatorio.',
                          ]);
                      }
                      $formapagomonto = $request->input('formapago_efectivo_montoefectivo'.$value->num);
                      $total_efectivo = $total_efectivo+$formapagomonto;
                  }
                  elseif($request->input('formapago_idtipopago'.$value->num)==2){
                      if($request->input('formapago_deposito_idcuentabancaria'.$value->num)==''){
                          $rules = array_merge($rules,[
                              'formapago_deposito_idcuentabancaria'.$value->num => 'required'
                          ]);
                          $messages = array_merge($messages,[
                              'formapago_deposito_idcuentabancaria'.$value->num.'.required' => 'La "Cuenta Bancaria" es Obligatorio.',
                          ]);
                      }
                      if($request->input('formapago_deposito_numerooperacion'.$value->num)==''){
                          $rules = array_merge($rules,[
                              'formapago_deposito_numerooperacion'.$value->num => 'required'
                          ]);
                          $messages = array_merge($messages,[
                              'formapago_deposito_numerooperacion'.$value->num.'.required' => 'El "Número de Operación" es Obligatorio.',
                          ]);
                      }
                      if($request->input('formapago_deposito_montodeposito'.$value->num)==''){
                          $rules = array_merge($rules,[
                              'formapago_deposito_montodeposito'.$value->num => 'required'
                          ]);
                          $messages = array_merge($messages,[
                              'formapago_deposito_montodeposito'.$value->num.'.required' => 'El "Monto en Depósito" es Obligatorio.',
                          ]);
                      }
                      $formapagomonto = $request->input('formapago_deposito_montodeposito'.$value->num);
                      $total_deposito = $total_deposito+$formapagomonto;
                  }   
                  $db_formapagocobranza[] = [
                      'orden'           => $i,
                      'idtipopago'      => $request->input('formapago_idtipopago'.$value->num),
                      'monto'           => $formapagomonto,
                      'cuentabancaria'  => $request->input('formapago_deposito_idcuentabancaria'.$value->num),
                      'numerooperacion' => $request->input('formapago_deposito_numerooperacion'.$value->num),
                  ];
                  $i++;
              }
              // dd($request->montorestante);
              // dd($request->formapago_totalpagado);
              if($request->montorestante < $request->formapago_totalpagado){
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'El "Total Pagado" debe ser igual o menor al "Total Restante".',
                  ]);
              }
          }
          elseif($request->formapago_idformapago==2){
              $rules = array_merge($rules,[
                  'formapago_credito_fechainicio' => 'required',
                  'formapago_credito_ultimafecha' => 'required',
              ]);
          }
          $idpagocotizacion = DB::table('s_cotizacionpagos')->insertGetId([
              'fecharegistro'         => Carbon::now(),
              'monto'                 => $request->formapago_totalpagado,
              'comentario'            => 'PAGO A CUENTA',
              'idusersresponsable'    => Auth::user()->id,
              's_idcotizacion'        => $request->idcotizacion
          ]);
          /*
          $idcobranza = DB::table('s_formapagodetalle')
                          ->insertGetId([
                            'fecharegistro'     => Carbon::now(),
                            'numerocuenta'      => '',
                            'numerooperacion'   => '',
                            'banco'             => '',
                            'fecha'             => '',
                            'hora'              => '',
                            'monto'             => $request->formapago_totalpagado,
                            'voucher'           => '',
                            's_idcuentabancaria'    => 0,
                            's_idprestamo_cobranza' => 0,
                            'idventacobranza'       => 0,
//                             'idcotizacioncobranza'  => $request->tipobusqueda == 'COTIZACION',
                            'idcotizacioncobranza'  => $idpagocotizacion,
                            'idmoneda'          => 1,
                            'idtienda'          => $tienda->id,
                            'idestado'          => 1
                          ]);
          */
          
          
          return response()->json([
            'resultado'   => 'CORRECTO',
            'mensaje'     => 'Se ha registrado correctamente.',
          ]);
          
        }  

        dd("lista");
        // $idcobranza = DB::table('s_formapagodetalle')
        //               ->insertGetId([
        //                 'fecharegistro'     => Carbon::now(),
        //                 'numerocuenta'      => '',
        //                 'numerooperacion'   => '',
        //                 'banco'             => '',
        //                 'fecha'             => '',
        //                 'hora'              => '',
        //                 'monto'             => $request->montopago,
        //                 'voucher'           => '',
        //                 's_idcuentabancaria'    => 0,
        //                 's_idprestamo_cobranza' => 0,
        //                 'idventacobranza'       => $request->tipobusqueda == 'VENTA' ? $request->idventa : 0,
        //                 'idcotizacioncobranza'  => $request->tipobusqueda == 'COTIZACION' ? $request->idcotizacion : 0,
        //                 'idmoneda'          => 1,
        //                 'idtienda'          => $tienda->id,
        //                 'idestado'          => 1
        //               ]);
        
        // json_ventacredito($idtienda,Auth::user()->idsucursal);
        // json_cobranzacredito($idtienda,Auth::user()->idsucursal);
        
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje' => 'Se ha registrado correctamente.',
          'idcobranza' => $idcobranza
        ]);
      }
      else if( $request->view == 'anular' ){
         DB::table('s_formapagodetalle')
               ->where('s_formapagodetalle.idtienda',$idtienda)
               ->where('s_formapagodetalle.id',$request->idformapagodetalle)
               ->update([
                 'idestado' => 2
               ]);
        
        json_ventacredito($idtienda,Auth::user()->idsucursal);
        json_cobranzacredito($idtienda,Auth::user()->idsucursal);
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje' => 'Anulación Completada.'
        ]);
      }
    }

    public function show(Request $request, $idtienda, $id)
    {
      if($id == 'show_table'){
        $forma_pago_detalle = DB::table('s_formapagodetalle')
                            ->where('s_formapagodetalle.idtienda',$idtienda)
                            ->where('s_formapagodetalle.idventacobranza', '!=', 0)
                            ->orWhere('s_formapagodetalle.idcotizacioncobranza', '!=', 0)
                            ->where('s_formapagodetalle.idtienda',$idtienda)
                            ->select(
                                's_formapagodetalle.id',
                                's_formapagodetalle.idventacobranza',
                                's_formapagodetalle.idcotizacioncobranza',
                                's_formapagodetalle.id as idformapagodetalle',
                                's_formapagodetalle.monto as montopagocredito',
                                's_formapagodetalle.fecharegistro as formapago_fecharegistro',
                                's_formapagodetalle.formapago_credito_fechainicio as formapago_credito_fechainicio',
                                's_formapagodetalle.formapago_credito_ultimafecha as formapago_credito_ultimafecha',
                                's_formapagodetalle.idestado as formapago_estado',
                            )
                            ->orderBy('s_formapagodetalle.fecharegistro', 'DESC')
                            ->paginate($request->length,'*',null,($request->start/$request->length)+1);
                            
        $tabla = [];
        foreach($forma_pago_detalle as $value){
          
            $opcion = [];
            $style = '';
            $estadoventa = '';
            
            
           
            $tipo_operacion = '';
            if( $value->idventacobranza != 0 ){
              $cliente = DB::table('s_venta')
                          ->whereId($value->idventacobranza)
                          ->select(
                            's_venta.db_idusuariocliente as nombrecliente',
                            's_venta.codigo',
                          )
                          ->first();
              $tipo_operacion = 'VENTA';
            }
            else if( $value->idcotizacioncobranza != 0 ){
              $cliente = DB::table('s_cotizacion')
                          ->whereId($value->idcotizacioncobranza)
                          ->select(
                            's_cotizacion.db_iduserscliente as nombrecliente',
                            's_cotizacion.codigo',
                          )
                          ->first();
              $tipo_operacion = 'COTIZACION';
            }
            $opcion[] = [
              'nombre'  => 'Ticket de Cobranza',
              'onclick' => '/'.$idtienda.'/cobranzacredito/'.$value->idformapagodetalle.'/edit?view=ticketcobranza&tipo_op='.$tipo_operacion,
              'icono'   => 'invoice'
            ];
            $estado = 'ANULADO';
            if( $value->formapago_estado == 1 ){
              $estado = 'ACEPTADO';
              $opcion[] = [
                    'nombre'  => 'Anular',
                    'onclick' => '/'.$idtienda.'/cobranzacredito/'.$value->idformapagodetalle.'/edit?view=anular&tipo_op='.$tipo_operacion,
                    'icono'   => 'remove'
                ];
            }
            // dump($tipo_operacion);
            // dump($value->idventacobranza);
            // dump($value->idcotizacioncobranza);
            
            if($cliente){
              $tabla[] = [
                'id'                    => $value->id,
                // 'text'                  => $value->codigo.' - '.$value->cliente.' ('.$value->monedasimbolo.' '.$value->total.')',
                'codigo'                => $tipo_operacion.' - '.str_pad($cliente->codigo, 5, "0", STR_PAD_LEFT),
                'montopagado'           => $value->montopagocredito,
                'cliente'               => $cliente->nombrecliente,
                'fecharegistro'         => date_format(date_create($value->formapago_fecharegistro),"d/m/Y h:i A"),
                'estado'                => $estado,
                'opcion'                => $opcion
              ];
            }
            
            
        }
        return response()->json([
          'start'           => $request->start,
          'draw'            => $request->draw,
          'recordsTotal'    => $request->length,
          'recordsFiltered' => $forma_pago_detalle->total(),
          'data'            => $tabla,
      ]);   
      }
      else if($id == 'show_creditos'){
        // dump($request->buscar);

        $s_ventas = DB::table('s_venta')
                    // ->join('s_moneda','s_moneda.id','s_venta.s_idmoneda')
                    // ->join('s_formapago','s_formapago.id','s_venta.s_idformapago')
                    // ->join('s_formapagodetalle','s_formapagodetalle.s_idventa','s_venta.id')
                    // ->join('s_tipocomprobante','s_tipocomprobante.id','s_venta.s_idcomprobante')
                    // ->join('users as cliente','cliente.id','s_venta.s_idusuariocliente')
                    // ->join('users as responsableregistro','responsableregistro.id','s_venta.s_idusuarioresponsableregistro')
                    // ->join('users as responsable','responsable.id','s_venta.s_idusuarioresponsable')
                    ->where('s_venta.idtienda',$idtienda)
                    ->where('s_venta.s_idformapago',2)
                    ->whereIn('s_venta.s_idestadoventa',[2])
                    ->select(
                        's_venta.*',
                        // 's_tipocomprobante.nombre as nombreComprobante',
                        // 's_venta.nombre as nombreformapago',
                  
                        // 's_formapagodetalle.formapago_credito_fechainicio as formapago_credito_fechainicio',
                        // 's_formapagodetalle.formapago_credito_ultimafecha as formapago_credito_ultimafecha',
                  
                        // DB::raw('IF(cliente.idtipopersona=1,
                        // CONCAT(cliente.nombrecompleto),
                        // CONCAT(cliente.nombrecompleto)) as cliente'),
                        // 'responsable.nombre as responsablenombre',
                        // 'responsableregistro.nombre as responsableregistronombre',
                        // 's_moneda.simbolo as monedasimbolo'
                    )
                    ->orderBy('s_venta.codigo','desc')
                    ->get();
                    
                  
        $tabla = [];
        foreach($s_ventas as $value){
          $monto_pagado = 0;
          $data_pago = $value->db_formapagocobranza ? json_decode($value->db_formapagocobranza,true) : [];

          
          foreach ($data_pago as $item) {
            $monto = (float) $item['monto']; // Convierte el valor de "monto" a un número decimal
            $monto_pagado += $monto; // Suma el monto al total
          }
          // $monto_pagado = DB::table('s_formapagodetalle')
          //                   ->where('s_formapagodetalle.idventacobranza',$value->id)
          //                   ->where('s_formapagodetalle.idestado',1)
          //                   ->sum('s_formapagodetalle.monto');

          // if($monto_pagado < $value->totalredondeado){
            $tabla[] = [
              'id'                    => $value->id,
              'text'                  => str_pad($value->codigo, 8, "0", STR_PAD_LEFT).' - '.$value->db_iduserscliente.' ('.$value->db_idmoneda.' '.$value->totalventa.')',
              'total'                 => $value->totalredondeado,
              'montopagado'           => $monto_pagado,
              'fechafinalpagocredito' => date_format(date_create($value->fecharegistro),'Y-m-d'),
              // 'fechafinalpagocredito' => $value->formapago_credito_ultimafecha,
            ];
          // } 
        }


        return $tabla;
      }
      else if( $id == 'show_cotizaciones' ){
        
        $cotizacions = DB::table('s_cotizacion')
                ->where('s_cotizacion.idtienda',$idtienda)
                ->where('s_cotizacion.idsucursal',Auth::user()->idsucursal)
                ->where('s_cotizacion.s_idtipocotizacion',2)
                // ->where('s_cotizacion.codigo','LIKE','%'.$request['columns'][0]['search']['value'].'%')
                // ->where('s_cotizacion.db_idtipocotizacion','LIKE','%'.$request['columns'][1]['search']['value'].'%')
                // ->where('s_cotizacion.db_iduserscliente','LIKE','%'.$request['columns'][2]['search']['value'].'%')
                // ->where('s_cotizacion.numerotelefono','LIKE','%'.$request['columns'][3]['search']['value'].'%')
                ->orderBy('s_cotizacion.codigo','desc')
                ->get();

        $tabla = [];
        foreach($cotizacions as $value){
          /*
          $monto_pagado = DB::table('s_formapagodetalle')
                            ->where('s_formapagodetalle.idcotizacioncobranza',$value->id)
                            ->where('s_formapagodetalle.idestado',1)
                            ->sum('s_formapagodetalle.monto');

          if($monto_pagado < $value->total){
            $tabla[] = [
              'id'                    => $value->id,
              'text'                  => str_pad($value->codigo, 8, "0", STR_PAD_LEFT).' - '.$value->db_iduserscliente,
              'total'                 => $value->total,
              'montopagado'           => $monto_pagado,
              'fechafinalpagocredito' => $value->fecharegistro,
            ];
          }
          */
          $monto_pagado = DB::table('s_cotizacionpagos')
                            ->where('s_cotizacionpagos.s_idcotizacion',$value->id)
                            ->sum('s_cotizacionpagos.monto');

          if($monto_pagado < $value->total){
            $tabla[] = [
              'id'                    => $value->id,
              'text'                  => str_pad($value->codigo, 8, "0", STR_PAD_LEFT).' - '.$value->db_iduserscliente,
              'total'                 => $value->total,
              'montopagado'           => $monto_pagado,
              'fechafinalpagocredito' => date('Y-m-d'),
            ];
          }
          
        }
        return $tabla;
      }
        
           
    }

    public function edit(Request $request, $idtienda, $id)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      $tienda = DB::table('tienda')
                  ->join('s_ubigeo','s_ubigeo.id','tienda.idubigeo')
                  ->where('tienda.id',$idtienda)
                  ->select(
                    'tienda.*',
                    's_ubigeo.nombre as ubigeonombre'
                  )
                  ->first();
      
      $s_venta = DB::table('s_formapagodetalle')
                  ->where('s_formapagodetalle.id',$id)
                  ->where('s_formapagodetalle.idtienda',$idtienda)
                  ->select(
                      's_formapagodetalle.id',
                      's_formapagodetalle.idventacobranza',
                      's_formapagodetalle.idcotizacioncobranza',
                      's_formapagodetalle.id as idformapagodetalle',
                      's_formapagodetalle.monto as montopagocredito',
                      's_formapagodetalle.fecharegistro as formapago_fecharegistro',
                      's_formapagodetalle.formapago_credito_fechainicio as formapago_credito_fechainicio',
                      's_formapagodetalle.formapago_credito_ultimafecha as formapago_credito_ultimafecha',
                      's_formapagodetalle.idestado as formapago_estado',
                  )
                ->first(); 
      
      if( $request->view == 'ticketcobranza' ){
        
        return view(sistema_view().'/cobranzacredito/ticketcobranza',[
          'tienda' => $tienda,
          's_venta' => $s_venta,
          'tipo_op' => $request->input('tipo_op')
        ]);
      }
      elseif($request->input('view') == 'ticketpdf') {
        
        if($request->input('tipo_op') == 'VENTA'){
          $operacion = DB::table('s_venta')
                        ->leftJoin('s_agencia','s_agencia.id','s_venta.s_idagencia')
                        ->where('s_venta.id',$s_venta->idventacobranza)
                        ->select(
                          's_venta.db_idusuariocliente as nombrecliente',
                          's_venta.codigo',
                          's_agencia.nombrecomercial as agencianombrecomercial',
                          's_agencia.razonsocial as agenciarazonsocial',
                          's_agencia.direccion as agenciadireccion',
                          's_agencia.logo as agencialogo',

                        )
                        ->first();
        }
        else if($request->input('tipo_op') == 'COTIZACION'){
          $operacion = DB::table('s_cotizacion')
                        ->whereId($s_venta->idcotizacioncobranza)
                        ->select(
                          's_cotizacion.db_iduserscliente as nombrecliente',
                          's_cotizacion.codigo',
                        )
                        ->first();
        }
        $pdf = PDF::loadView(sistema_view().'/cobranzacredito/ticketpdf',[
            'tienda'    => $tienda,
            'operacion' => $operacion,
            'tipo_op'   => $request->input('tipo_op'),
            'venta'     => $s_venta
        ]);
        $ticket = $request->input('tipo_op').'_'.str_pad($operacion->codigo, 8, "0", STR_PAD_LEFT);
        return $pdf->stream($ticket.'.pdf');
      }
      else if( $request->input('view') == 'anular' ){
        // $cliente = DB::table('users')
        //       ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
        //       ->where('users.id',$s_venta->s_idusuariocliente)
        //       ->select(
        //           'users.*',
        //           'ubigeo.nombre as ubigeonombre'
        //       )
        //       ->first();
        if($request->input('tipo_op') == 'VENTA'){
          $operacion = DB::table('s_venta')
          ->whereId($s_venta->idventacobranza)
          ->select(
            's_venta.db_idusuariocliente as nombrecliente',
            's_venta.codigo',
          )
          ->first();
        }
        else if($request->input('tipo_op') == 'COTIZACION'){
          $operacion = DB::table('s_cotizacion')
          ->whereId($s_venta->idcotizacioncobranza)
          ->select(
            's_cotizacion.db_iduserscliente as nombrecliente',
            's_cotizacion.codigo',
          )
          ->first();
        }
        
        
        return view(sistema_view().'/cobranzacredito/anular',[
          'tienda'    => $tienda,
          'operacion' => $operacion,
          'tipo_op'   => $request->input('tipo_op'),
          'venta'     => $s_venta,
        ]);
      }
    }

    public function update(Request $request, $idtienda, $id)
    {
        
    }


    public function destroy(Request $request, $idtienda, $id)
    {
       
    }
}
