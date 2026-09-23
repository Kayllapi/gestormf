<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class PagoprestamocajaController extends Controller
{
    public function __construct()
    {
        //
    }
    public function index(Request $request,$idtienda)
    {
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            
            $agencias = DB::table('tienda')->get();
          
            return view(sistema_view().'/pagoprestamocaja/tabla',[
              'tienda' => $tienda,
              'agencias' => $agencias,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
    }
  
    public function store(Request $request, $idtienda)
    {
      
        if($request->input('view') == 'registrar') {
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

    }

    public function edit(Request $request, $idtienda, $id)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        
        $credito_cobranzacuota = DB::table('credito_cobranzacuota')
              ->join('credito','credito.id','credito_cobranzacuota.idcredito')
              ->join('users as cliente','cliente.id','credito.idcliente')
              ->where('credito_cobranzacuota.id',$id)
              ->select(
                  'credito_cobranzacuota.*',
                  'cliente.nombrecompleto as nombrecliente',
                  'credito.idcliente as idcliente',
                  'credito.cuenta as creditocuenta',
              )
              ->first();
                
        if($request->input('view') == 'ticket') {
            return view(sistema_view().'/pagoprestamocaja/ticket',[
              'tienda' => $tienda,
              'credito_cobranzacuota' => $credito_cobranzacuota,
            ]);
        }
        else if( $request->input('view') == 'pdf_pago' ){
              
            $usuario = DB::table('users')
              ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
              ->leftJoin('ubigeo as ubigeonacimiento','ubigeonacimiento.id','users.idubigeo_nacimiento')
              ->leftJoin('role_user','role_user.user_id','users.id')
              ->leftJoin('roles','roles.id','role_user.role_id')
              ->where('users.id', $credito_cobranzacuota->idcliente)
              ->select(
                  'users.*',
                  'roles.id as idroles',
                  'roles.description as descriptionrole',
                  'ubigeo.nombre as ubigeonombre',
                  'ubigeonacimiento.nombre as ubigeonacimientonombre'
              )
              ->first();
            
            $count_creditopendiente = DB::table('credito_garantia')
                  ->where('credito_garantia.idcredito',$credito_cobranzacuota->idcredito)
                  ->where('credito_garantia.idestadoentrega',1)
                  ->count();
          
            $cajero = DB::table('users')->where('users.id',$credito_cobranzacuota->idcajero)->first();
        
            $pdf = PDF::loadView(sistema_view().'/cobranzacuota/pdf_pago',[
                'tienda' => $tienda,
                'creditocuenta' => $credito_cobranzacuota->creditocuenta,
                'usuario' => $usuario,
                'cajero' => $cajero,
                'banco' => $credito_cobranzacuota->banco,
                'bancocuenta' => $credito_cobranzacuota->cuenta,
                'numerooperacion' => $credito_cobranzacuota->numerooperacion,
                'idformapago' => $credito_cobranzacuota->idformapago,
                'pago_cuota' => $credito_cobranzacuota->pago_cuota,
                'pago_diasatraso' => $credito_cobranzacuota->pago_diasatraso,
                'saldo_pendientepago' => $credito_cobranzacuota->saldo_pendientepago,
                'credito_cobranzacuota' => $credito_cobranzacuota,
                'count_creditopendiente' => $count_creditopendiente,
            ]); 
            $pdf->setPaper('A4');
            return $pdf->stream('VOUCHER_PAGO.pdf');
        }   
        else if($request->input('view') == 'ticket_garantia') {
            $count_creditopendiente = DB::table('credito_garantia')
                  ->where('credito_garantia.idcredito',$credito_cobranzacuota->idcredito)
                  ->where('credito_garantia.idestadoentrega',1)
                  ->count();
            return view(sistema_view().'/pagoprestamocaja/ticket_garantia',[
                'tienda' => $tienda,
                'credito_cobranzacuota' => $credito_cobranzacuota,
                'count_creditopendiente' => $count_creditopendiente,
            ]);
        }
        else if( $request->input('view') == 'pdf_garantia' ){
              
            $usuario = DB::table('users')
              ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
              ->leftJoin('ubigeo as ubigeonacimiento','ubigeonacimiento.id','users.idubigeo_nacimiento')
              ->leftJoin('role_user','role_user.user_id','users.id')
              ->leftJoin('roles','roles.id','role_user.role_id')
              ->where('users.id', $credito_cobranzacuota->idcliente)
              ->select(
                  'users.*',
                  'roles.id as idroles',
                  'roles.description as descriptionrole',
                  'ubigeo.nombre as ubigeonombre',
                  'ubigeonacimiento.nombre as ubigeonacimientonombre'
              )
              ->first();
            $cajero = DB::table('users')->where('users.id',$credito_cobranzacuota->idcajero)->first();
              
          
            $garantias = DB::table('credito_garantia')
              ->leftJoin('garantias','garantias.id','credito_garantia.idgarantias')
              ->where('idcredito', $credito_cobranzacuota->idcredito)
              ->where('credito_garantia.tipo', 'CLIENTE')
              ->select(
                'garantias.*'
              )
              ->get();
          
            $pdf = PDF::loadView(sistema_view().'/cobranzacuota/pdf_garantia',[
                'tienda' => $tienda,
                'creditocuenta' => $credito_cobranzacuota->creditocuenta,
                'usuario' => $usuario,
                'cajero' => $cajero,
                'banco' => $credito_cobranzacuota->banco,
                'bancocuenta' => $credito_cobranzacuota->cuenta,
                'operacion' => $credito_cobranzacuota->numerooperacion,
                'idformapago' => $credito_cobranzacuota->idformapago,
                'credito_cobranzacuota' => $credito_cobranzacuota,
                'garantias' => $garantias,
            ]); 
            $pdf->setPaper('A4');
            return $pdf->stream('VOUCHER_PAGO.pdf');
        }
        else if($request->input('view') == 'exportar') {
            return view(sistema_view().'/pagoprestamocaja/exportar',[
                'tienda' => $tienda,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'idagencia' => $request->idagencia,
                'idcliente' => $request->idcliente,
            ]);
        }
        else if( $request->input('view') == 'exportar_pdf' ){
              
            
          $where = [];
          if($request->idagencia!=''){
              $where[] = ['credito.idtienda',$request->idagencia];
          }
          if($request->idcliente!=''){
              $where[] = ['credito.idcliente',$request->idcliente];
          }
          $where[] = ['credito_cobranzacuota.fecharegistro','>=',$request->fecha_inicio.' 00:00:00'];
          $where[] = ['credito_cobranzacuota.fecharegistro','<=',$request->fecha_fin.' 23:59:59'];

          $creditos = DB::table('credito_cobranzacuota')
              ->join('credito','credito.id','credito_cobranzacuota.idcredito')
              ->join('users as cliente','cliente.id','credito.idcliente')
              ->join('ubigeo','ubigeo.id','cliente.idubigeo')
              ->where('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
              ->where('credito_cobranzacuota.idestadoextorno',0)
              ->where($where)
              ->select(
                  'credito_cobranzacuota.*',
                  'credito.cuenta as cuentacredito',
                  'cliente.id as idcliente',
                  'cliente.nombrecompleto as nombrecliente',
                  'cliente.direccion as clientedireccion',
                  'ubigeo.nombre as ubigeonombre',
              )
              ->orderBy('credito_cobranzacuota.fecharegistro','asc')
              ->get();
        
            $pdf = PDF::loadView(sistema_view().'/pagoprestamocaja/exportar_pdf',[
                'tienda' => $tienda,
                'creditos' => $creditos,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
            ]); 
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('HISTORIAL_PAGOS_PRESTAMOS.pdf');
        }  
        else if($request->input('view') == 'extornar') {
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->where('users_permiso.idpermiso',1)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/pagoprestamocaja/extornar',[
              'tienda' => $tienda,
              'credito_cobranzacuota' => $credito_cobranzacuota,
              'usuarios' => $usuarios,
            ]);
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        
      if( $request->input('view') == 'extornar' ){
            $rules = [     
                'idresponsable' => 'required',          
                'responsableclave' => 'required',                 
            ];
          
            $messages = [
                'idresponsable.required' => 'El "Responsable" es Obligatorio.',
                'responsableclave.required' => 'La "Contraseña" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
            $usuario = DB::table('users')
                ->where('users.id',$request->idresponsable)
                ->where('users.clave',$request->responsableclave)
                ->first();
            $idresponsable = 0;
            if($usuario==''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El usuario y/o la contraseña es incorrecta!!.'
                ]);
            }
            $idresponsable = $usuario->id;
        
          $credito_cobranzacuota = DB::table('credito_cobranzacuota')
              ->join('credito','credito.id','credito_cobranzacuota.idcredito')
              ->join('users as cliente','cliente.id','credito.idcliente')
              ->where('credito_cobranzacuota.id',$id)
              ->select(
                  'credito_cobranzacuota.*',
                  'cliente.nombrecompleto as nombrecliente',
                  'credito.idcliente as idcliente',
                  'credito.idestado_congelarcredito as idestado_congelarcredito',
                  'credito_cobranzacuota.opcion_pago as opcion_pago',
              )
              ->first();
        
          //------ no extornar credito ampliado
          /*$credito_cobranzacuota_val = DB::table('credito_cobranzacuota')
              ->where('credito_cobranzacuota.id_credito_ampliado',$credito_cobranzacuota->idcredito)
              ->first();*/
        
          // Solo se puede extornar un pago el mismo dia en que se registro.
          if(Carbon::parse($credito_cobranzacuota->fecharegistro)->toDateString() != Carbon::now()->toDateString()){
              return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje'   => 'Solo se puede extornar un pago el mismo día en que se registró.',
              ]);
          }

          if($credito_cobranzacuota->id_credito_ampliado!=0){
              return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje'   => 'No puede extornar esta cobranza, lo tiene que realizar desde eliminar desembolso!!.',
              ]);
          }
          //------
        
          //dd($credito_cobranzacuota_val);
        
          $credito_cobranzacuota_valid = DB::table('credito_cobranzacuota')
              ->where('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
              ->where('credito_cobranzacuota.idestadoextorno',0)
              ->where('credito_cobranzacuota.idcredito',$credito_cobranzacuota->idcredito)
              ->orderBy('credito_cobranzacuota.fecharegistro','desc')
              ->limit(1)
              ->first();
        
          if($credito_cobranzacuota_valid->id!=$id){
              return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje'   => 'Primero tiene que extornar el ultimo pago realizado!!.',
              ]);
          }
        
          //dd('...');
        
              
            if($credito_cobranzacuota->opcion_pago!='PAGO_ACUENTA'){

                // restaurar pago
                DB::table('credito_descuentocuota')
                  ->where('credito_descuentocuota.idcredito_cobranzacuota',$id)
                  ->update([
                    'idcredito_cobranzacuota'        => 0,
                    'idestadocredito_descuentocuota' => 1,
                ]);
                
                DB::table('credito_cargo')
                  ->where('credito_cargo.idcredito_cobranzacuota',$id)
                  ->update([
                    'idcredito_cobranzacuota'  => 0,
                    'idestadocredito_cargo'    => 1,
                ]);
                
                if($credito_cobranzacuota->idestado_congelarcredito==2){ // credito congeladp
                    DB::table('credito_cronograma')
                        ->where('credito_cronograma.idcredito_cobranzacuota',$id)
                        ->update([
                          'acuenta' => 0,
                          'idestadocredito_cronograma' => 1,
                          'idestadocronograma_pago' => 0,
                    ]);
                }else{
                    DB::table('credito_cronograma')
                        ->where('credito_cronograma.idcredito_cobranzacuota',$id)
                        ->update([
                          'tenencia'             => 0,
                          'penalidad'            => 0,
                          'compensatorio'        => 0,
                          'totalcuota'           => 0,
                          'acuenta'              => 0,

                          'atraso_dias'                => 0,
                          'pagar_amortizacion'         => 0,
                          'pagar_interes'              => 0,
                          'pagar_comision'             => 0,
                          'pagar_cargo'                => 0,
                          'pagar_cuota'                => 0,
                          'pagar_tenencia'             => 0,
                          'pagar_penalidad'            => 0,
                          'pagar_compensatorio'        => 0,
                          'pagar_totalcuota'           => 0,
                          'descontar_amortizacion'     => 0,
                          'descontar_interes'          => 0,
                          'descontar_comision'         => 0,
                          'descontar_cargo'            => 0,
                          'descontar_cuota'            => 0,
                          'descontar_tenencia'         => 0,
                          'descontar_penalidad'        => 0,
                          'descontar_compensatorio'    => 0,
                          'descontar_totalcuota'       => 0,
                          'idestadocredito_cronograma' => 1,
                          'idestadocronograma_pago'    => 0,
                    ]);
                }
                    
                
            }
            else{

                $credito_cronograma = DB::table('credito_cronograma')
                    ->where('credito_cronograma.idcredito',$credito_cobranzacuota->idcredito)
                    ->where('credito_cronograma.idestadocronograma_pago',2)
                    ->orderBy('credito_cronograma.numerocuota','desc')
                    ->get();

                foreach($credito_cronograma as $value){
                    $total_adelanto = DB::table('credito_adelanto')
                        ->where('credito_adelanto.idestadocredito_adelanto',1)
                        ->where('credito_adelanto.numerocuota',$value->numerocuota)
                        ->where('credito_adelanto.idcredito_cobranzacuota',$credito_cobranzacuota->id)
                        ->sum('credito_adelanto.total');
                    if($total_adelanto>0){
                        $acuenta = 0;
                        $idestadocredito_cronograma = 0;
                        $idestadocronograma_pago = 0;
                        if($value->acuenta>0 && $value->acuenta<=$total_adelanto){
                            $acuenta = $total_adelanto-$value->acuenta;
                            $idestadocredito_cronograma = 1;
                            $idestadocronograma_pago = 0;
                        }else{
                            $acuenta = $value->acuenta-$total_adelanto;
                            $idestadocredito_cronograma = 1;
                            $idestadocronograma_pago = 2;
                        }
                        if($credito_cobranzacuota->idestado_congelarcredito==2){ // credito congelado
                            DB::table('credito_cronograma')
                                ->whereId($value->id)
                                ->update([
                                  'acuenta' => $acuenta,
                                  'idestadocredito_cronograma' => $idestadocredito_cronograma,
                                  'idestadocronograma_pago' => $idestadocronograma_pago,
                            ]);
                        }else{
                            DB::table('credito_cronograma')
                                ->whereId($value->id)
                                ->update([
                                  'acuenta' => $acuenta,
                                  'idestadocredito_cronograma' => $idestadocredito_cronograma,
                                  'idestadocronograma_pago' => $idestadocronograma_pago,

                                  'tenencia'             => 0,
                                  'penalidad'            => 0,
                                  'compensatorio'        => 0,
                                  'totalcuota'           => 0,

                                  'atraso_dias'                => 0,
                                  'pagar_amortizacion'         => 0,
                                  'pagar_interes'              => 0,
                                  'pagar_comision'             => 0,
                                  'pagar_cargo'                => 0,
                                  'pagar_cuota'                => 0,
                                  'pagar_tenencia'             => 0,
                                  'pagar_penalidad'            => 0,
                                  'pagar_compensatorio'        => 0,
                                  'pagar_totalcuota'           => 0,
                                  'descontar_amortizacion'     => 0,
                                  'descontar_interes'          => 0,
                                  'descontar_comision'         => 0,
                                  'descontar_cargo'            => 0,
                                  'descontar_cuota'            => 0,
                                  'descontar_tenencia'         => 0,
                                  'descontar_penalidad'        => 0,
                                  'descontar_compensatorio'    => 0,
                                  'descontar_totalcuota'       => 0,
                                  'idcredito_cobranzacuota'    => 0,
                            ]);
                        }
                    }else{
                        break;
                    }
                }

            }

            // PAGO_CUOTA / PAGO_TOTAL con adelantos: la cascada de select_cronograma() puede dejar
            // (a) un sobrante como adelanto parcial en la cuota siguiente (esa cuota NO queda con
            // idcredito_cobranzacuota = este pago, por eso el reset de arriba no la toca y se quedaba
            // con 'acuenta' y estado parcial) y (b) cuotas que este pago cerro pero que ya tenian
            // adelantos de pagos a cuenta anteriores (deben volver a "pago parcial", no a "sin pago").
            if($credito_cobranzacuota->opcion_pago!='PAGO_ACUENTA'){
                $adelantos_por_cuota = DB::table('credito_adelanto')
                    ->where('credito_adelanto.idcredito_cobranzacuota',$credito_cobranzacuota->id)
                    ->whereIn('credito_adelanto.idestadocredito_adelanto',[1,2])
                    ->select('credito_adelanto.numerocuota', DB::raw('SUM(credito_adelanto.total) as total'))
                    ->groupBy('credito_adelanto.numerocuota')
                    ->get();
                foreach($adelantos_por_cuota as $adelanto_cuota){
                    $cuota_cronograma = DB::table('credito_cronograma')
                        ->where('credito_cronograma.idcredito',$credito_cobranzacuota->idcredito)
                        ->where('credito_cronograma.numerocuota',$adelanto_cuota->numerocuota)
                        ->first();
                    if(!$cuota_cronograma){
                        continue;
                    }
                    // adelantos vigentes de OTROS pagos (los de este pago se marcan como extornados mas abajo)
                    $adelantos_restantes = DB::table('credito_adelanto')
                        ->where('credito_adelanto.idcredito',$credito_cobranzacuota->idcredito)
                        ->where('credito_adelanto.numerocuota',$adelanto_cuota->numerocuota)
                        ->where('credito_adelanto.idcredito_cobranzacuota','<>',$credito_cobranzacuota->id)
                        ->whereIn('credito_adelanto.idestadocredito_adelanto',[1,2])
                        ->sum('credito_adelanto.total');

                    if($cuota_cronograma->idcredito_cobranzacuota == $credito_cobranzacuota->id){
                        // cuota cerrada por este pago (ya reseteada arriba): recupera su estado parcial previo
                        if($adelantos_restantes>0){
                            DB::table('credito_cronograma')
                                ->whereId($cuota_cronograma->id)
                                ->update(['idestadocronograma_pago' => 2]);
                        }
                    }else{
                        // cuota que solo recibio un adelanto parcial de este pago: se le quita
                        $acuenta = max(0, (float) $cuota_cronograma->acuenta - (float) $adelanto_cuota->total);
                        DB::table('credito_cronograma')
                            ->whereId($cuota_cronograma->id)
                            ->update([
                              'acuenta' => $acuenta,
                              'idestadocronograma_pago' => ($adelantos_restantes>0 || $acuenta>0) ? 2 : 0,
                        ]);
                    }
                }
            }

            // Pago Anticipado (reduccion_cuota / reduccion_plazo): ademas de lo de arriba (que ya
            // revierte las cuotas vencidas/de hoy que se cerraron de una), hay que reconstruir el
            // cronograma que esas dos modalidades eliminaron/recalcularon estructuralmente.
            if($credito_cobranzacuota->opcion_pago=='PAGO_ANTICIPADO'){
                revertir_pagoanticipado($credito_cobranzacuota->idcredito, $id);
            }

            // eliminar las fotos de saldo (credito_adelanto_saldo) del/los adelanto(s) que se estan extornando,
            // sino "show_descuentodecuotas" sigue mostrando el saldo del pago ya revertido (toma la ultima por id)
            DB::table('credito_adelanto_saldo')
                ->whereIn('idcredito_adelanto', function($query) use ($credito_cobranzacuota) {
                    $query->select('id')
                        ->from('credito_adelanto')
                        ->where('idcredito_cobranzacuota', $credito_cobranzacuota->id);
                })
                ->delete();

            DB::table('credito_adelanto')
                ->where('credito_adelanto.idcredito_cobranzacuota',$credito_cobranzacuota->id)
                ->update([
                  'credito_adelanto.idestadocredito_adelanto' => 3,
            ]);

            DB::table('credito_cobranzacuota')
              ->whereId($id)
              ->update([
                  'fechaextorno' => Carbon::now(),
                  'idestadoextorno'  => 2,
                  'idresponsableextorno'  => $idresponsable,
            ]);
            // restaurar estado de credito
            // (si el pago extornado habia dejado el credito marcado como cancelado,
            // fecha_cancelado debe limpiarse: ya no esta cancelado)
            DB::table('credito')
              ->whereId($credito_cobranzacuota->idcredito)
              ->update([
                  'idestadocredito'  => 1,
                  'fecha_cancelado'  => null,
            ]);
            // restaurar garantias
            DB::table('credito_garantia')
              ->where('credito_garantia.idcredito',$credito_cobranzacuota->idcredito)
              ->update([
                'idestadoentrega' => 1,
            ]);

            // recalcular y restaurar el saldo pendiente del credito
            // (store() lo actualiza en cada cobranza, pero el extorno nunca lo recalculaba,
            // dejando saldo_pendientepago/total_pendientepago con el valor de la cobranza revertida)
            $credito_actual = DB::table('credito')
                ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                ->where('credito.id',$credito_cobranzacuota->idcredito)
                ->select('credito.*','credito_prendatario.modalidad as modalidadproductocredito')
                ->first();

            $primera_cuota_pendiente_restaurada = DB::table('credito_cronograma')
                ->where('idcredito',$credito_cobranzacuota->idcredito)
                ->where('idestadocredito_cronograma',1)
                ->orderBy('numerocuota','asc')
                ->value('numerocuota');

            $credito_descuentocuotas = DB::table('credito_descuentocuota')
                ->where('credito_descuentocuota.idcredito',$credito_cobranzacuota->idcredito)
                ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                ->first();
            $total_descuento_capital = 0;
            $total_descuento_interes = 0;
            $total_descuento_comision = 0;
            $total_descuento_cargo = 0;
            $total_descuento_penalidad = 0;
            $total_descuento_tenencia = 0;
            $total_descuento_compensatorio = 0;
            if($credito_descuentocuotas && $primera_cuota_pendiente_restaurada>=$credito_descuentocuotas->numerocuota_fin){
                $total_descuento_capital = $credito_descuentocuotas->capital;
                $total_descuento_interes = $credito_descuentocuotas->interes;
                $total_descuento_comision = $credito_descuentocuotas->comision;
                $total_descuento_cargo = $credito_descuentocuotas->cargo;
                $total_descuento_penalidad = $credito_descuentocuotas->penalidad;
                $total_descuento_tenencia = $credito_descuentocuotas->tenencia;
                $total_descuento_compensatorio = $credito_descuentocuotas->compensatorio;
            }

            $cronograma_restaurado = select_cronograma(
                $idtienda,
                $credito_cobranzacuota->idcredito,
                $credito_actual->idforma_credito,
                $credito_actual->modalidadproductocredito,
                $credito_actual->cuotas,
                $total_descuento_capital,
                $total_descuento_interes,
                $total_descuento_comision,
                $total_descuento_cargo,
                $total_descuento_penalidad,
                $total_descuento_tenencia,
                $total_descuento_compensatorio,
                0,
                1,
                'detalle_cobranza'
            );

            DB::table('credito')
                ->whereId($credito_cobranzacuota->idcredito)
                ->update([
                    'saldo_pendientepago' => $cronograma_restaurado['saldo_capital'],
                    'total_pendientepago' => $cronograma_restaurado['cuota_pendiente'],
                ]);

          return response()->json([
              'resultado'           => 'CORRECTO',
              'mensaje'             => 'Se ha elimino correctamente.',
          ]);
        
      }
    
    }

    public function destroy(Request $request, $idtienda, $id)
    {
      
    }
}
