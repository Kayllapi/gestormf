<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class CobranzacuotaController extends Controller
{
    public function __construct()
    {
        $this->tipo_credito = DB::table('tipo_credito')->get();
    }
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
          
            $creditos = DB::table('credito')
                ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
                ->join('users as cliente','cliente.id','credito.idcliente')
                ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
                ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
                ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                ->where('credito.estado','DESEMBOLSADO')
                ->select(
                    'credito.*',
                    'cliente.identificacion as identificacion',
                    'cliente.nombrecompleto as nombrecliente',
                )
                ->orderBy('credito.fecha_desembolso','asc')
                ->get();
          
            return view(sistema_view().'/cobranzacuota/tabla',[
              'tienda' => $tienda,
              'creditos' => $creditos,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->view == 'registrar') {
            return view(sistema_view().'/cobranzacuota/create',[
                'tienda' => $tienda
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        
      
        if($request->input('view') == 'registrar') {
            
            $rules = [
                'cobrar_total_pagar' => 'required',
                'cobrar_total_recibido' => 'required',
                'idformapago' => 'required',
            ];
            
            if($request->idformapago==2){
                $rules = array_merge($rules,[
                    'idbanco' => 'required',
                    'numerooperacion' => 'required',
                ]);
            }
            $messages = [
                'cobrar_total_pagar.required' => 'El "Total a pagar" es Obligatorio.',
                'cobrar_total_recibido.required' => 'El "Total Recibido" es Obligatorio.',
                'idformapago.required' => 'El "Cobrar por" es Obligatorio.',
                'idbanco.required' => 'El "Banco" es Obligatorio.',
                'numerooperacion.required' => 'El "Número de Operación" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
                
            $credito = DB::table('credito')
                ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                ->where('credito.id',$request->idcredito)
                ->select(
                    'credito.*',
                    'credito_prendatario.modalidad as modalidadproductocredito',
                )
                ->first();
              
                    
            /*if($request->opcion_pago=='PAGO_CUOTA' or $request->opcion_pago=='PAGO_TOTAL'){
                
                if($request->cobrar_total_recibido<$request->cobrar_total_pagar){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Total recibido debe ser mayor o igual a Total a Pagar.'
                    ]);
                }
                
                // descuento cuota
                $credito_descuentocuotas = DB::table('credito_descuentocuota')
                      ->where('credito_descuentocuota.idcredito',$request->idcredito)
                      ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                      ->first();
                $total_descuento_capital = 0; 
                $total_descuento_interes = 0; 
                $total_descuento_comision = 0; 
                $total_descuento_cargo = 0;  
                $total_descuento_penalidad = 0; 
                $total_descuento_tenencia = 0; 
                $total_descuento_compensatorio = 0; 
                $total_descuento_total = 0; 
                if($credito_descuentocuotas){
                    if($request->numerocuota>=$credito_descuentocuotas->numerocuota_fin){
                        $total_descuento_capital = $credito_descuentocuotas->capital;
                        $total_descuento_interes = $credito_descuentocuotas->interes;
                        $total_descuento_comision = $credito_descuentocuotas->comision;
                        $total_descuento_cargo = $credito_descuentocuotas->cargo;
                        $total_descuento_penalidad = $credito_descuentocuotas->penalidad;
                        $total_descuento_tenencia = $credito_descuentocuotas->tenencia;
                        $total_descuento_compensatorio = $credito_descuentocuotas->compensatorio;
                        $total_descuento_total = $credito_descuentocuotas->total;
                    }
                }
                
                $cronograma = select_cronograma(
                    $idtienda,
                    $request->idcredito,
                    $credito->idforma_credito,
                    $credito->modalidadproductocredito,
                    $request->numerocuota,
                    $total_descuento_capital,
                    $total_descuento_interes,
                    $total_descuento_comision,
                    $total_descuento_cargo,
                    $total_descuento_penalidad,
                    $total_descuento_tenencia,
                    $total_descuento_compensatorio
                );
                
                
          
                $credito_cobranzacuota = DB::table('credito_cobranzacuota')
                    ->orderBy('credito_cobranzacuota.codigo','desc')
                    ->limit(1)
                    ->first();
                $codigo = 1;
                if($credito_cobranzacuota!=''){
                    $codigo = $credito_cobranzacuota->codigo+1;
                }
                
          
                $bancoo = DB::table('banco')->where('banco.id',$request->idbanco!=null?$request->idbanco:0)->first();
                
                $banco = '';
                $cuenta = '';
                if($bancoo!=''){
                    $banco = $bancoo->nombre;
                    $cuenta = $bancoo->cuenta;
                }
                
                $pago_cuota = '';
                $pago_diasatraso = '';
                $i = 0 ;
                foreach($cronograma['cronograma'] as $value){
                  if($value['selected']=='selected'){
                      $coma = ', ';
                      if($i==0){
                        $coma = '';
                      }
                        $pago_cuota = $pago_cuota.$coma.$value['numerocuota'];
                        $pago_diasatraso = $pago_diasatraso.$coma.$value['atraso_dias'];
                        
                        $i++;
                  }
                }
                
                $idcredito_cobranzacuota = DB::table('credito_cobranzacuota')->insertGetId([
                    'fecharegistro' => Carbon::now(),
                    'codigo' => $codigo,
                    'total_pagar' => $request->cobrar_total_pagar,
                    'total_recibido' => $request->cobrar_total_recibido,
                    'vuelto' => $request->cobrar_vuelto,
                    'numerooperacion' => $request->numerooperacion!=''?$request->numerooperacion:'',
                    'banco' => $banco,
                    'cuenta' => $cuenta,
                    'proximo_vencimiento' => $cronograma['proximo_vencimiento'],
                    'pago_cuota' => $pago_cuota,
                    'pago_diasatraso' => $pago_diasatraso,
                    'opcion_pago' => $request->opcion_pago,
                    'estadocargo' => $request->estadocargo,
                    'cobrar_cargo' => $request->cobrar_cargo,
                    //'credito_cargo' => $total_cargo,
                    'total_amortizacion'         => $cronograma['select_amortizacion'],
                    'total_interes'              => $cronograma['select_interes'],
                    'total_comision'             => $cronograma['select_comision'],
                    'total_cargo'                => $cronograma['select_cargo'],
                    'total_cuota'                => $cronograma['select_cuota'],
                    'total_tenencia'             => $cronograma['select_tenencia'],
                    'total_penalidad'            => $cronograma['select_penalidad'],
                    'total_compensatorio'        => $cronograma['select_compensatorio'],
                    'total_totalcuota'           => $cronograma['select_totalcuota'],
                    //'total_pagoacuenta'          => $cronograma['total_pagoacuenta'],
                    'total_pagar_amortizacion'         => $cronograma['select_pagar_amortizacion'],
                    'total_pagar_interes'              => $cronograma['select_pagar_interes'],
                    'total_pagar_comision'             => $cronograma['select_pagar_comision'],
                    'total_pagar_cargo'                => $cronograma['select_pagar_cargo'],
                    'total_pagar_cuota'                => $cronograma['select_pagar_cuota'],
                    'total_pagar_tenencia'             => $cronograma['select_pagar_tenencia'],
                    'total_pagar_penalidad'            => $cronograma['select_pagar_penalidad'],
                    'total_pagar_compensatorio'        => $cronograma['select_pagar_compensatorio'],
                    'total_pagar_totalcuota'           => $cronograma['select_pagar_totalcuota'],
                    'total_descontar_amortizacion'     => $cronograma['select_descontar_amortizacion'],
                    'total_descontar_interes'          => $cronograma['select_descontar_interes'],
                    'total_descontar_comision'         => $cronograma['select_descontar_comision'],
                    'total_descontar_cargo'            => $cronograma['select_descontar_cargo'],
                    'total_descontar_cuota'            => $cronograma['select_descontar_cuota'],
                    'total_descontar_tenencia'         => $cronograma['select_descontar_tenencia'],
                    'total_descontar_penalidad'        => $cronograma['select_descontar_penalidad'],
                    'total_descontar_compensatorio'    => $cronograma['select_descontar_compensatorio'],
                    'total_descontar_totalcuota'       => $cronograma['select_descontar_totalcuota'],
                    //'idcredito_cargo' => $idcredito_cargo,
                    'idcajero' => Auth::user()->id,
                    'idcredito' => $request->idcredito,
                    'idformapago' => $request->idformapago,
                    'idbanco' => $request->idbanco!=null?$request->idbanco:0,
                    'idestadocredito_cobranzacuota' => 1, 
                    'idestadoextorno' => 0, // 0 = sin extornar, 2 = extornado
                    'idtienda' => $idtienda,
                    'idestado' => 1,
                ]);
                
                if($request->idcredito_descuentocuota>0){
                    DB::table('credito_descuentocuota')
                      ->whereId($request->idcredito_descuentocuota)
                      ->update([
                        'idcredito_cobranzacuota'        => $idcredito_cobranzacuota,
                        'idestadocredito_descuentocuota' => 2,
                    ]);
                }
                
                if($request->idcredito_cargo>0 && $request->estadocargo=='on'){
                    DB::table('credito_cargo')
                      ->whereId($request->idcredito_cargo)
                      ->update([
                        'idcredito_cobranzacuota'  => $idcredito_cobranzacuota,
                        'idestadocredito_cargo'    => 2,
                    ]);
                }
                
                //CAMBIAR ESTADO DE CUOTAS
                
                foreach($cronograma['cronograma'] as $value){
                  if($value['selected']=='selected'){
                      DB::table('credito_cronograma')
                          ->whereId($value['id'])
                          ->update([
                            'tenencia'             => $value['tenencia'],
                            'penalidad'            => $value['penalidad'],
                            'compensatorio'        => $value['compensatorio'],
                            'totalcuota'           => $value['totalcuota'],
                            
                            'atraso_dias'                => $value['atraso_dias'],
                            'acuenta'                    => 0,
                            'pagar_amortizacion'         => $value['pagar_amortizacion'],
                            'pagar_interes'              => $value['pagar_interes'],
                            'pagar_comision'             => $value['pagar_comision'],
                            'pagar_cargo'                => $value['pagar_cargo'],
                            'pagar_cuota'                => $value['pagar_cuota'],
                            'pagar_tenencia'             => $value['pagar_tenencia'],
                            'pagar_penalidad'            => $value['pagar_penalidad'],
                            'pagar_compensatorio'        => $value['pagar_compensatorio'],
                            'pagar_totalcuota'           => $value['pagar_totalcuota'],
                            'descontar_amortizacion'     => $value['descontar_amortizacion'],
                            'descontar_interes'          => $value['descontar_interes'],
                            'descontar_comision'         => $value['descontar_comision'],
                            'descontar_cargo'            => $value['descontar_cargo'],
                            'descontar_cuota'            => $value['descontar_cuota'],
                            'descontar_tenencia'         => $value['descontar_tenencia'],
                            'descontar_penalidad'        => $value['descontar_penalidad'],
                            'descontar_compensatorio'    => $value['descontar_compensatorio'],
                            'descontar_totalcuota'       => $value['descontar_totalcuota'],
                            'idcredito_cobranzacuota'    => $idcredito_cobranzacuota,
                            'idestadocredito_cronograma' => 2,
                            'idestadocronograma_pago'    => 2,
                      ]);
                  }
                }
                
            }
            elseif($request->opcion_pago=='PAGO_ACUENTA'){*/
                
                //----
                if($request->opcion_pago=='PAGO_CUOTA' or $request->opcion_pago=='PAGO_TOTAL'){
                    if($request->cobrar_total_pagar<=0){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El "Total recibido" debe ser mayor a 0.00.'
                        ]);
                    }
                    if($request->cobrar_total_recibido<=0){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El "Total a Pagar" debe ser mayor a 0.00.'
                        ]);
                    }
                    if($request->cobrar_total_recibido<$request->cobrar_total_pagar){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El "Total recibido" debe ser mayor ó igual a "Total a Pagar".'
                        ]);
                    }
                }
                elseif($request->opcion_pago=='PAGO_ACUENTA'){
                    if($request->cobrar_total_pagar<=0){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El "Dinero Recibido" debe ser mayor a 0.00.'
                        ]);
                    }
                    if($request->cobrar_total_recibido<=0){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El "Pago a Cuenta" debe ser mayor a 0.00.'
                        ]);
                    }


                    if($request->cobrar_total_recibido>$request->cobrar_total_pagar){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El "Pago a Cuenta" debe ser menor ó igual al "Dinero Recibido".'
                        ]);
                    }
                }else{
                    if($request->cobrar_cargo<=0){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'Las "Cuentas por Cobrar" debe ser mayor a 0.00.'
                        ]);
                    }
                  
                    $request->opcion_pago = '';
                }
                
                   
                $total_cuota = 0;
                $total_pagar = 0;
                $total_recibido = 0;
                if($request->opcion_pago=='PAGO_CUOTA' or $request->opcion_pago=='PAGO_TOTAL'){
                    $total_cuota = $request->cobrar_cuota_pagar;
                    $total_pagar = $request->cobrar_total_pagar;
                    $total_recibido = $request->cobrar_total_recibido;
                }elseif($request->opcion_pago=='PAGO_ACUENTA'){
                    $total_cuota = $request->cobrar_total_recibido;
                    $total_pagar = $request->cobrar_total_recibido;
                    $total_recibido = $request->cobrar_total_pagar;
                } 
              
                
                // descuento cuota
                $credito_descuentocuotas = DB::table('credito_descuentocuota')
                      ->where('credito_descuentocuota.idcredito',$request->idcredito)
                      ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                      ->first();
                $total_descuento_capital = 0; 
                $total_descuento_interes = 0; 
                $total_descuento_comision = 0; 
                $total_descuento_cargo = 0;  
                $total_descuento_penalidad = 0; 
                $total_descuento_tenencia = 0; 
                $total_descuento_compensatorio = 0; 
                $total_descuento_total = 0; 
                if($credito_descuentocuotas){
                    if($request->numerocuota>=$credito_descuentocuotas->numerocuota_fin){
                        $total_descuento_capital = $credito_descuentocuotas->capital;
                        $total_descuento_interes = $credito_descuentocuotas->interes;
                        $total_descuento_comision = $credito_descuentocuotas->comision;
                        $total_descuento_cargo = $credito_descuentocuotas->cargo;
                        $total_descuento_penalidad = $credito_descuentocuotas->penalidad;
                        $total_descuento_tenencia = $credito_descuentocuotas->tenencia;
                        $total_descuento_compensatorio = $credito_descuentocuotas->compensatorio;
                        $total_descuento_total = $credito_descuentocuotas->total;
                    }
                }
                
                $cronograma = select_cronograma(
                    $idtienda,
                    $request->idcredito,
                    $credito->idforma_credito,
                    $credito->modalidadproductocredito,
                    $request->numerocuota,
                    $total_descuento_capital,
                    $total_descuento_interes,
                    $total_descuento_comision,
                    $total_descuento_cargo,
                    $total_descuento_penalidad,
                    $total_descuento_tenencia,
                    $total_descuento_compensatorio,
                    $total_cuota,
                    1,
                    'detalle_cobranza'
                );
                
            
                //dd($cronograma['cuota_pendiente']);
          
                $credito_cobranzacuota = DB::table('credito_cobranzacuota')
                    ->orderBy('credito_cobranzacuota.codigo','desc')
                    ->limit(1)
                    ->first();
                $codigo = 1;
                if($credito_cobranzacuota!=''){
                    $codigo = $credito_cobranzacuota->codigo+1;
                }
                
                $bancoo = DB::table('banco')->where('banco.id',$request->idbanco!=null?$request->idbanco:0)->first();
                
                $banco = '';
                $cuenta = '';
                if($bancoo!=''){
                    $banco = $bancoo->nombre;
                    $cuenta = $bancoo->cuenta;
                }
                
              
                $pago_cuota = '';
                $pago_diasatraso = '';
                $i = 0 ;
                foreach($cronograma['cronograma'] as $value){
                  if($value['selected']=='selected'){
                      $coma = ', ';
                      if($i==0){
                        $coma = '';
                      }
                        $pago_cuota = $pago_cuota.$coma.$value['numerocuota'];
                        $pago_diasatraso = $pago_diasatraso.$coma.$value['atraso_dias'];
                        
                        $i++;
                  }
                }
          
                $idcredito_cobranzacuota = DB::table('credito_cobranzacuota')->insertGetId([
                    'fecharegistro' => Carbon::now(),
                    'codigo' => $codigo,
                    'total_pagar' => $total_pagar,
                    'total_recibido' => $total_recibido,
                    'vuelto' => $request->cobrar_vuelto,
                    'numerooperacion' => $request->numerooperacion!=''?$request->numerooperacion:'',
                    'banco' => $banco,
                    'cuenta' => $cuenta,
                  
                    'proximo_vencimiento' => $cronograma['proximo_vencimiento'],
                    'saldo_pendientepago' => $cronograma['saldo_capital'],
                    'total_pendientepago' => $cronograma['cuota_pendiente'],
                    'pago_cuota' => $pago_cuota,
                    'pago_diasatraso' => $pago_diasatraso,
                    'opcion_pago' => $request->opcion_pago,
                    'estadocargo' => $request->estadocargo!=null?$request->estadocargo:'',
                    'cobrar_cargo' => $request->cobrar_cargo!=null?$request->cobrar_cargo:'0.00',
                  
                    'total_amortizacion'         => $cronograma['select_amortizacion'],
                    'total_interes'              => $cronograma['select_interes'],
                    'total_comision'             => $cronograma['select_comision'],
                    'total_cargo'                => $cronograma['select_cargo'],
                    'total_cuota'                => $cronograma['select_cuota'],
                    'total_tenencia'             => $cronograma['select_tenencia'],
                    'total_penalidad'            => $cronograma['select_penalidad'],
                    'total_compensatorio'        => $cronograma['select_compensatorio'],
                    'total_totalcuota'           => $cronograma['select_totalcuota'],
                    'total_pagoacuenta'          => $cronograma['total_pagoacuenta'],
                    'total_adelanto'             => $cronograma['select_adelanto'],
                    //'numerocuota_pagoacuenta'          => $request->numerocuota,
                    'total_pagar_amortizacion'         => $cronograma['select_pagar_amortizacion'],
                    'total_pagar_interes'              => $cronograma['select_pagar_interes'],
                    'total_pagar_comision'             => $cronograma['select_pagar_comision'],
                    'total_pagar_cargo'                => $cronograma['select_pagar_cargo'],
                    'total_pagar_cuota'                => $cronograma['select_pagar_cuota'],
                    'total_pagar_tenencia'             => $cronograma['select_pagar_tenencia'],
                    'total_pagar_penalidad'            => $cronograma['select_pagar_penalidad'],
                    'total_pagar_compensatorio'        => $cronograma['select_pagar_compensatorio'],
                    'total_pagar_totalcuota'           => $cronograma['select_pagar_totalcuota'],
                    'total_descontar_amortizacion'     => $cronograma['select_descontar_amortizacion'],
                    'total_descontar_interes'          => $cronograma['select_descontar_interes'],
                    'total_descontar_comision'         => $cronograma['select_descontar_comision'],
                    'total_descontar_cargo'            => $cronograma['select_descontar_cargo'],
                    'total_descontar_cuota'            => $cronograma['select_descontar_cuota'],
                    'total_descontar_tenencia'         => $cronograma['select_descontar_tenencia'],
                    'total_descontar_penalidad'        => $cronograma['select_descontar_penalidad'],
                    'total_descontar_compensatorio'    => $cronograma['select_descontar_compensatorio'],
                    'total_descontar_totalcuota'       => $cronograma['select_descontar_totalcuota'],
                    'idcredito' => $request->idcredito,
                    'idcajero' =>  Auth::user()->id,
                    'idformapago' => $request->idformapago,
                    'idbanco' => $request->idbanco!=null?$request->idbanco:0,
                    'idestadocredito_cobranzacuota' => 1,
                    'idestadoextorno' => 0, // 0 = sin extornar, 2 = extornado
                    'idtienda' => $idtienda,
                    'idestado' => 1,
                ]);
              
              
                
                //CAMBIAR ESTADO DE CUOTAS
                
                foreach($cronograma['cronograma'] as $value){
                  $valid_adelanto = 0;
                  if($value['selected']=='selected'){
                      DB::table('credito_cronograma')
                          ->whereId($value['id'])
                          ->update([
                            'tenencia'             => $value['tenencia'],
                            'penalidad'            => $value['penalidad'],
                            'compensatorio'        => $value['compensatorio'],
                            'totalcuota'           => $value['totalcuota'],
                            
                            'atraso_dias'                => $value['atraso_dias'],
                            'acuenta'                    => $value['acuenta'],
                            'pagar_amortizacion'         => $value['pagar_amortizacion'],
                            'pagar_interes'              => $value['pagar_interes'],
                            'pagar_comision'             => $value['pagar_comision'],
                            'pagar_cargo'                => $value['pagar_cargo'],
                            'pagar_cuota'                => $value['pagar_cuota'],
                            'pagar_tenencia'             => $value['pagar_tenencia'],
                            'pagar_penalidad'            => $value['pagar_penalidad'],
                            'pagar_compensatorio'        => $value['pagar_compensatorio'],
                            'pagar_totalcuota'           => $value['pagar_totalcuota'],
                            'descontar_amortizacion'     => $value['descontar_amortizacion'],
                            'descontar_interes'          => $value['descontar_interes'],
                            'descontar_comision'         => $value['descontar_comision'],
                            'descontar_cargo'            => $value['descontar_cargo'],
                            'descontar_cuota'            => $value['descontar_cuota'],
                            'descontar_tenencia'         => $value['descontar_tenencia'],
                            'descontar_penalidad'        => $value['descontar_penalidad'],
                            'descontar_compensatorio'    => $value['descontar_compensatorio'],
                            'descontar_totalcuota'       => $value['descontar_totalcuota'],
                            //'idcredito_cobranzacuota'    => $idcredito_cobranzacuota,
                            'idestadocredito_cronograma' => 2,
                            'idestadocronograma_pago'    => 2, // =pagorealizados
                      ]);
                    
                      $valid_adelanto = 1;
                  }elseif($value['acuenta']>0){
                        
                      
                    // calcular el acuenta de la misma cobranza
                    /*$credito_cobranzacuota = DB::table('credito_cobranzacuota')
                        ->where('credito_cobranzacuota.idcredito',$request->idcredito)
                        ->where('credito_cobranzacuota.numerocuota_pagoacuenta',$value['numerocuota'])
                        ->where('credito_cobranzacuota.idestadoextorno',0)
                        ->first();
                    
                    $total_acuenta = 0;
                    if($credito_cobranzacuota){
                        $total_acuenta = $credito_cobranzacuota->total_pagoacuenta;
                    }*/
                    
                    /*DB::table('credito_cronograma')
                        ->where('credito_cronograma.idcredito', $request->idcredito)
                        ->update([
                          'acuenta' => 0,
                    ]);*/
                    
                    $credito_cronograma = DB::table('credito_cronograma')
                            ->whereId($value['id'])
                            ->first();
                    
                    if($credito_cronograma){
                      DB::table('credito_cronograma')
                          ->whereId($value['id'])
                          ->update([
                            'acuenta' => $value['acuenta'],
                            'idestadocronograma_pago'    => 2,
                      ]);
                    }
                    
                    $valid_adelanto = 1;
                  }
                  
                  
                  // registrado adelanto
                  if($valid_adelanto==1 && $value['adelanto']>0){
                      $credito_adelanto = DB::table('credito_adelanto')
                          ->orderBy('credito_adelanto.codigo','desc')
                          ->limit(1)
                          ->first();
                      $codigo = 1;
                      if($credito_adelanto!=''){
                          $codigo = $credito_adelanto->codigo+1;
                      }
                      DB::table('credito_adelanto')->insert([
                         'fecharegistro'        => Carbon::now(),
                         'codigo'               => $codigo,
                         'numerocuota'          => $value['numerocuota'],
                         'atraso'               => $value['atraso_dias'],

                         'capital'              => $value['acuenta_amortizacion'],
                         'interes'              => $value['acuenta_interes'],
                         'comision'             => $value['acuenta_comision'],
                         'cargo'                => $value['acuenta_cargo'],
                         'penalidad'            => $value['acuenta_penalidad'],
                         'tenencia'             => $value['acuenta_tenencia'],
                         'compensatorio'        => $value['acuenta_compensatorio'],

                         'total_capital'        => $value['acuenta_total_amortizacion'],
                         'total_interes'        => $value['acuenta_total_interes'],
                         'total_comision'       => $value['acuenta_total_comision'],
                         'total_cargo'          => $value['acuenta_total_cargo'],
                         'total_penalidad'      => $value['acuenta_total_penalidad'],
                         'total_tenencia'       => $value['acuenta_total_tenencia'],
                         'total_compensatorio'  => $value['acuenta_total_compensatorio'],

                         'total'                => $value['adelanto'],
                         'idcredito'            => $request->idcredito,
                         'idcredito_cronograma' => $value['id'],
                         'idcredito_cobranzacuota' => $idcredito_cobranzacuota,
                         'idestadocredito_adelanto'=> 1,
                         'idresponsable'        => Auth::user()->id,
                         'idtienda'             => $idtienda,
                         'idestado'             => 1,
                      ]);
                  }
                      
                  
                }
                
                //----
                if($request->opcion_pago=='PAGO_CUOTA' or $request->opcion_pago=='PAGO_TOTAL'){
                    if($request->idcredito_descuentocuota>0){
                        DB::table('credito_descuentocuota')
                          ->whereId($request->idcredito_descuentocuota)
                          ->update([
                            'idcredito_cobranzacuota'        => $idcredito_cobranzacuota,
                            'idestadocredito_descuentocuota' => 2,
                        ]);
                    }

                    if($request->idcredito_cargo>0 && $request->estadocargo=='on'){
                        DB::table('credito_cargo')
                          ->whereId($request->idcredito_cargo)
                          ->update([
                            'idcredito_cobranzacuota'  => $idcredito_cobranzacuota,
                            'idestadocredito_cargo'    => 2,
                        ]);
                    }
                }elseif($request->opcion_pago=='PAGO_ACUENTA'){
                  
                }else{
                        DB::table('credito_cargo')
                          ->whereId($request->idcredito_cargo)
                          ->update([
                            'idcredito_cobranzacuota'  => $idcredito_cobranzacuota,
                            'idestadocredito_cargo'    => 2,
                        ]);
                }
            
            
            // actualziar ultimo saldo
            $count_credito_cronograma = DB::table('credito_cronograma')
                ->where('credito_cronograma.idcredito',$request->idcredito)
                ->whereIn('credito_cronograma.idestadocredito_cronograma',[1,3])
                ->count();
                
            $idestadocredito = 1;
                
            if($count_credito_cronograma==0){ // credito cancelado
                
                DB::table('credito')
                  ->whereId($request->idcredito)
                  ->update([
                    'fecha_cancelado' => Carbon::now(),
                    'idestadocredito' => 2,
                ]);
                
                $idestadocredito = 2;
            }
            
            if($request->entregargarantia=='on'){
                
                DB::table('credito_garantia')
                  ->where('credito_garantia.idcredito',$request->idcredito)
                  ->update([
                    'fechaentrega' => Carbon::now(),
                    'idestadoentrega' => 2,
                ]);
                
            }
          
            // pagar saldo pendiente
            // descuento cuota
            $credito_descuentocuotas = DB::table('credito_descuentocuota')
                  ->where('credito_descuentocuota.idcredito',$request->idcredito)
                  ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                  ->first();
            $total_descuento_capital = 0; 
            $total_descuento_interes = 0; 
            $total_descuento_comision = 0; 
            $total_descuento_cargo = 0;  
            $total_descuento_penalidad = 0; 
            $total_descuento_tenencia = 0; 
            $total_descuento_compensatorio = 0; 
            $total_descuento_total = 0; 
            if($credito_descuentocuotas){
                if($request->numerocuota>=$credito_descuentocuotas->numerocuota_fin){
                    $total_descuento_capital = $credito_descuentocuotas->capital;
                    $total_descuento_interes = $credito_descuentocuotas->interes;
                    $total_descuento_comision = $credito_descuentocuotas->comision;
                    $total_descuento_cargo = $credito_descuentocuotas->cargo;
                    $total_descuento_penalidad = $credito_descuentocuotas->penalidad;
                    $total_descuento_tenencia = $credito_descuentocuotas->tenencia;
                    $total_descuento_compensatorio = $credito_descuentocuotas->compensatorio;
                    $total_descuento_total = $credito_descuentocuotas->total;
                }
            }
            $cronograma = select_cronograma(
                $idtienda,
                $request->idcredito,
                $credito->idforma_credito,
                $credito->modalidadproductocredito,
                $credito->cuotas,
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
              ->whereId($request->idcredito)
              ->update([
                    'saldo_pendientepago' => $cronograma['saldo_capital'],
                    'total_pendientepago' => $cronograma['cuota_pendiente'],
            ]);
          
            DB::table('credito_cobranzacuota')
              ->whereId($idcredito_cobranzacuota)
              ->update([
                    'saldo_pendientepago' => $cronograma['saldo_capital'],
                    'total_pendientepago' => $cronograma['cuota_pendiente'],
            ]);
          
            $count_creditopendiente = DB::table('credito_garantia')
                  ->where('credito_garantia.idcredito',$request->idcredito)
                  ->where('credito_garantia.idestadoentrega',1)
                  ->count();
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.',
                'idcobranzacuota'   => $idcredito_cobranzacuota,
                'idestadocredito'   => $idestadocredito,
                'credito'   => $credito,
                'idcliente'   => $credito->idcliente,
                'idcredito'   => $request->idcredito,
                'entregargarantia'   => $request->entregargarantia,
                'count_creditopendiente'   => $count_creditopendiente,
                'select_numerocuota_fin' => $cronograma['select_ultimacuotacancelada'],
            ]);
        }
      
        elseif($request->input('view') == 'congelarcredito') {
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
          
                $credito = DB::table('credito')
                    ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                    ->where('credito.id',$request->idcredito)
                    ->select(
                        'credito.*',
                        'credito_prendatario.modalidad as modalidadproductocredito',
                    )
                    ->first();
          
                $cronograma = select_cronograma(
                    $idtienda,
                    $request->idcredito,
                    $credito->idforma_credito,
                    $credito->modalidadproductocredito,
                    100,
                    0,
                    0,
                    0,
                    0,
                    0,
                    0,
                    0
                );
          
                //CAMBIAR ESTADO DE CUOTAS
                
                foreach($cronograma['cronograma'] as $value){
                  if($value['selected']=='selected'){
                      DB::table('credito_cronograma')
                          ->whereId($value['id'])
                          ->update([
                            'tenencia'             => $value['tenencia'],
                            'penalidad'            => $value['penalidad'],
                            'compensatorio'        => $value['compensatorio'],
                            'totalcuota'           => $value['totalcuota'],
                            
                            'atraso_dias'                => $value['atraso_dias'],
                            'pagar_amortizacion'         => $value['pagar_amortizacion'],
                            'pagar_interes'              => $value['pagar_interes'],
                            'pagar_comision'             => $value['pagar_comision'],
                            'pagar_cargo'                => $value['pagar_cargo'],
                            'pagar_cuota'                => $value['pagar_cuota'],
                            'pagar_tenencia'             => $value['pagar_tenencia'],
                            'pagar_penalidad'            => $value['pagar_penalidad'],
                            'pagar_compensatorio'        => $value['pagar_compensatorio'],
                            'pagar_totalcuota'           => $value['pagar_totalcuota'],
                            'descontar_amortizacion'     => $value['descontar_amortizacion'],
                            'descontar_interes'          => $value['descontar_interes'],
                            'descontar_comision'         => $value['descontar_comision'],
                            'descontar_cargo'            => $value['descontar_cargo'],
                            'descontar_cuota'            => $value['descontar_cuota'],
                            'descontar_tenencia'         => $value['descontar_tenencia'],
                            'descontar_penalidad'        => $value['descontar_penalidad'],
                            'descontar_compensatorio'    => $value['descontar_compensatorio'],
                            'descontar_totalcuota'       => $value['descontar_totalcuota'],
                            'idestadocredito_cronograma' => 3,
                      ]);
                  }
                }
          
          
                DB::table('credito')
                  ->whereId($request->idcredito)
                  ->update([
                    'fecha_congelarcredito' => Carbon::now(),
                    'idusuario_congelarcredito' => $idresponsable,
                    'idestado_congelarcredito' => 2,
                ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
          
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        if($id == 'show_cobranzacuota'){
          
          $credito = DB::table('credito')
              ->join('users as cliente','cliente.id','credito.idcliente')
              ->leftjoin('users as aval','aval.id','credito.idaval')
              ->join('forma_credito','forma_credito.id','credito.idforma_credito')
              ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
              ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
              ->join('tipo_destino_credito','tipo_destino_credito.id','credito.idtipo_destino_credito')
              ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
              ->where('credito.id',$request->idcredito)
              ->where('credito.idestadocredito',1)
              ->select(
                  'credito.*',
                  'cliente.codigo as codigo_cliente',
                  'cliente.identificacion as docuementocliente',
                  'cliente.nombrecompleto as nombreclientecredito',
                  'aval.identificacion as documentoaval',
                  'aval.nombrecompleto as nombreavalcredito',
                  'forma_credito.nombre as forma_credito_nombre',
                  'tipo_operacion_credito.nombre as tipo_operacion_credito_nombre',
                  'modalidad_credito.nombre as modalidad_credito_nombre',
                  'forma_pago_credito.nombre as forma_pago_credito_nombre',
                  'tipo_destino_credito.nombre as tipo_destino_credito_nombre',
                  'credito_prendatario.nombre as nombreproductocredito',
                  'credito_prendatario.modalidad as modalidad_calculo',
                  'credito_prendatario.conevaluacion as conevaluacion',
              )
              ->orderBy('credito.id','desc')
              ->first();
          
          $numero_cuotas = '<option></option>';
          $datosprestamos = '';
          $idcredito = '';
          
          if($credito!=''){
              $idcredito = $credito->id;
            
               $tasa_tip = $credito->modalidad_calculo == 'Interes Compuesto' ? '' : $credito->tasa_tip;
                 
              $datosprestamos = '<table class="table" style="width:100%;">
                      <tr>
                        <td style="background-color: #efefef !important;width: 90px;"><b>Préstamo S/.</b></td>
                        <td style="width:2px;"><b>:</b></td>
                        <td>'.$credito->monto_solicitado.'</td>
                        <td style="background-color: #efefef !important;width: 180px;"><b>Prest., Int., Serv. y Cargo S/.</b></td>
                        <td style="width:2px;"><b>:</b></td>
                        <td>'.$credito->total_pagar.'</td>
                        <td style="background-color: #efefef !important;width: 120px;"><b>Venc. Contrato</b></td>
                        <td style="width:2px;"><b>:</b></td>
                        <td ">'.date_format(date_create($credito->fecha_ultimopago),'d-m-Y').'</td>
                      </tr>
                      <tr>
                        <td style="background-color: #efefef !important;"><b>TEM (%)</b></td>
                        <td><b>:</b></td>
                        <td>'.$credito->tasa_tem.'</td>
                        <td style="background-color: #efefef !important;"><b>TIP (%)</b></td>
                        <td><b>:</b></td>
                        <td>'.$tasa_tip.'</td>
                        <td style="background-color: #efefef!important;"><b>F. PAGO</b></td>
                        <td><b>:</b></td>
                        <td>'.$credito->forma_pago_credito_nombre.' ('.$credito->cuotas.' Cuotas)</td>
                      </tr>
                      <tr>
                        <td style="background-color: #efefef !important;"><b>Producto</b></td>
                        <td><b>:</b></td>
                        <td>'.$credito->nombreproductocredito.'</td>
                        <td style="background-color: #efefef !important;"><b>Modalidad de C</b></td>
                        <td><b>:</b></td>
                        <td>'.$credito->modalidad_credito_nombre.'</td>
                        <td style="background-color: #efefef !important;"><b>F. Desembolso</b></td>
                        <td><b>:</b></td>
                        <td colspan="3">'.date_format(date_create($credito->fecha_desembolso),'d-m-Y').'</td>
                      </tr>
                    </table>';
              
          }
          
          
          $credito_descuentocuotas = DB::table('credito_descuentocuota')
                ->where('credito_descuentocuota.idcredito',$request->idcredito)
                ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                ->first();
          
          return array(
              'idcredito' => $idcredito,
              'datosprestamos' => $datosprestamos,
              'descuento_capital' => $credito_descuentocuotas?$credito_descuentocuotas->capital:'0.00',
              'descuento_interes' => $credito_descuentocuotas?$credito_descuentocuotas->interes:'0.00',
              'descuento_comision' => $credito_descuentocuotas?$credito_descuentocuotas->comision:'0.00',
              'descuento_cargo' => $credito_descuentocuotas?$credito_descuentocuotas->cargo:'0.00',
              'descuento_tenencia' => $credito_descuentocuotas?$credito_descuentocuotas->tenencia:'0.00',
              'descuento_penalidad' => $credito_descuentocuotas?$credito_descuentocuotas->penalidad:'0.00',
              'descuento_moratoria' => $credito_descuentocuotas?$credito_descuentocuotas->compensatorio:'0.00',
              'descuento_total' => $credito_descuentocuotas?$credito_descuentocuotas->total:'0.00',
              'descuento_numerocuota' => $credito_descuentocuotas?($credito_descuentocuotas->numerocuota_fin>0?'('.$credito_descuentocuotas->numerocuota.')':''):'',
          );
        }
        elseif($id == 'show_cobranzacuota_cronograma'){
        
          $disabled = '';
          if($request->tipo=='pagocuota'){
            
          }
          elseif($request->tipo=='pagoacuenta'){
              $disabled = 'disabled';
          }
          elseif($request->tipo=='pagototal'){
              $disabled = 'disabled';
          }
          
          $credito = DB::table('credito')
              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                    ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
                    ->join('users as asesor','asesor.id','credito.idasesor')
              ->where('credito.id',$request->idcredito)
              ->select(
                    'credito.*',
                    'credito_prendatario.modalidad as modalidadproductocredito',
                    'forma_pago_credito.nombre as forma_pago_credito_nombre',
                    'asesor.nombrecompleto as asesor',
              )
              ->first();
              
          // descuento cuota
          $credito_descuentocuotas = DB::table('credito_descuentocuota')
                ->where('credito_descuentocuota.idcredito',$request->idcredito)
                ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                ->first();
          $total_descuento_capital = 0; 
          $total_descuento_interes = 0; 
          $total_descuento_comision = 0; 
          $total_descuento_cargo = 0;  
          $total_descuento_penalidad = 0; 
          $total_descuento_tenencia = 0; 
          $total_descuento_compensatorio = 0; 
          $total_descuento_total = 0; 
          if($credito_descuentocuotas){
              if($request->numerocuota>=$credito_descuentocuotas->numerocuota_fin){
                  $total_descuento_capital = $credito_descuentocuotas->capital;
                  $total_descuento_interes = $credito_descuentocuotas->interes;
                  $total_descuento_comision = $credito_descuentocuotas->comision;
                  $total_descuento_cargo = $credito_descuentocuotas->cargo;
                  $total_descuento_penalidad = $credito_descuentocuotas->penalidad;
                  $total_descuento_tenencia = $credito_descuentocuotas->tenencia;
                  $total_descuento_compensatorio = $credito_descuentocuotas->compensatorio;
                  $total_descuento_total = $credito_descuentocuotas->total;
              }
          }
          
          //dd($credito->idforma_credito.'/'.$credito->modalidadproductocredito.'/'.$request->numerocuota);
          $cronograma = select_cronograma(
              $idtienda,
              $request->idcredito,
              $credito->idforma_credito,
              $credito->modalidadproductocredito,
              $request->numerocuota,
              $total_descuento_capital,
              $total_descuento_interes,
              $total_descuento_comision,
              $total_descuento_cargo,
              $total_descuento_penalidad,
              $total_descuento_tenencia,
              $total_descuento_compensatorio,
              ($request->acuenta!=null?$request->acuenta:0)-$request->cobrar_cargo,
              1,
              'detalle_cobranza'
          );
          
          $html = '<table class="table" id="table-detalle-cronograma">
              <thead style="position: sticky;top: 0;z-index: 1;">
              <tr>
                <th style="width:5px;"></th>
                <th style="width:10px;">N° Cuo.</th>
                <th>Fecha</th>
                <th>Amort.</th>
                <th>Interes</th>
                <th>C. Ss./ <br>Otros.</th>
                <th>Cargo</th>
                <th>Cuota</th>
                <th><span style="background-color: #ffb2b2 !important;font-weight: bold;">Vencido</span></th>
                <th>Custo.</th>
                <th style="width:10px;">Int. Comp.</th>
                <th style="width:10px;">Int. morat.</th>
                <th>Cuota Total</th>
                <!--th>Acuenta</th-->
              </tr>
              </thead>
              <tbody>';

          
          $primera_cuota_pendiente = 0;
          foreach($cronograma['cronograma'] as $value){
            
              if($value['idestadocredito_cronograma']==1 && $primera_cuota_pendiente==0){
                  $primera_cuota_pendiente = $value['numerocuota'];
              }
            
              /*$credito_cobranzacuota = DB::table('credito_cobranzacuota')
                ->where('credito_cobranzacuota.id',$value['idcredito_cobranzacuota'])
                ->where('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
                ->first();*/
              //  adelanto
              $credito_adelanto = DB::table('credito_adelanto')
                  ->join('credito_cobranzacuota','credito_cobranzacuota.id','credito_adelanto.idcredito_cobranzacuota')
                  ->where('credito_adelanto.idcredito_cronograma',$value['id'])
                  ->where('credito_adelanto.idestadocredito_adelanto',1)
                  ->get();
            
              $totaladelanto = 0;
              $ultimafechaadelanto = 0;
              foreach($credito_adelanto as $valueade){
                  $totaladelanto = $valueade->total_pagar;
                  $ultimafechaadelanto = date_format(date_create($valueade->fecharegistro),'d-m-Y h:i:s A');
              }
              
              $fechacobranza_fecharegistro = '';
              if($totaladelanto>=$value['totalcuota']){
                  $fechacobranza_fecharegistro = 'tabindex="0"  data-bs-container="body" data-bs-toggle="popover" data-trigger="focus" data-bs-placement="right" data-bs-content="Fecha Cancelada: '.$ultimafechaadelanto.'"';
              }
            
              
              // fin adelanto
              
              $html .= '<tr class="'.$value['selected'].' '.$value['seleccionar'].'"
                            id="posicion_cronograma'.$value['numerocuota'].'"
                            data-id="'.$value['id'].'" 
                            data-numerocuota="'.$value['numerocuota'].'">
                            <td style="'.$value['style'].'">
                                <label class="chk">
                                  <input type="checkbox" name="seleccionar_cuota" id="numerocuotaselect"onclick="pagocuota('.$value['numerocuota'].')" '.$value['checked'].' '.$value['disabled'].' '.$disabled.'>
                                  <span class="checkmark"></span>
                                </label>
                            </td>
                            <td style="'.$value['style'].'width:10px;text-align:center;" '.$fechacobranza_fecharegistro.' id="cont-popover-cuota">'.$value['numerocuota'].
                            //'///'.$totaladelanto.'=='.$value['totalcuota'].'//'.$value['id'].
                            '</td>
                            <td style="'.$value['style'].'text-align:center;">'.$value['fecha'].'</td>
                            <td style="'.$value['style'].'text-align:right;" pagar="'.$value['pagar_amortizacion'].'" descontar="'.$value['descontar_amortizacion'].'">'.$value['amortizacion'].'</td>
                            <td style="'.$value['style'].'text-align:right;" pagar="'.$value['pagar_interes'].'" descontar="'.$value['descontar_interes'].'">'.$value['interes'].'</td>
                            <td style="'.$value['style'].'text-align:right;" pagar="'.$value['pagar_comision'].'" descontar="'.$value['descontar_comision'].'">'.$value['comision'].'</td>
                            <td style="'.$value['style'].'text-align:right;" pagar="'.$value['pagar_cargo'].'" descontar="'.$value['descontar_cargo'].'">'.$value['cargo'].'</td>
                            <td style="'.$value['style'].'text-align:right;" pagar="'.$value['pagar_cuota'].'" descontar="'.$value['descontar_cuota'].'">'.$value['cuota'].'</td>
                            <td style="'.$value['style'].'text-align:right;background-color: #e1ffd8 !important;">
                            <span style="'.($value['atraso_dias']>0?'color: #ff4343 !important;':'').'font-weight: bold;">
                            '.$value['atraso_dias'].'</span></td>
                            <td style="'.$value['style'].'text-align:right;" pagar="'.$value['pagar_tenencia'].'" descontar="'.$value['descontar_tenencia'].'">'.$value['tenencia'].'</td>
                            <td style="'.$value['style'].'text-align:right;" pagar="'.$value['pagar_penalidad'].'" descontar="'.$value['descontar_penalidad'].'">'.$value['penalidad'].'</td>
                            <td style="'.$value['style'].'text-align:right;" pagar="'.$value['pagar_compensatorio'].'" descontar="'.$value['descontar_compensatorio'].'">
                            '.$value['compensatorio'].'
                            </td>
                            <td style="'.$value['style'].'text-align:right;background-color: #efefef !important;" pagar="'.$value['pagar_totalcuota'].'" descontar="'.$value['descontar_totalcuota'].'">'.$value['totalcuota'].'</td>
                            <!--td style="'.$value['style'].'text-align:right;">'.$value['acuenta'].'</td-->
                        </tr>';
            
          }
          $html .= '</tbody>
              <thead style="position: sticky;bottom: 0;">
              <tr>
                <th></th>
                <th></th>
                <th></th>
                <th style="text-align:right;">'.$cronograma['total_amortizacion'].'</th>
                <th style="text-align:right;">'.$cronograma['total_interes'].'</th>
                <th style="text-align:right;">'.$cronograma['total_comision'].'</th>
                <th style="text-align:right;">'.$cronograma['total_cargo'].'</th>
                <th style="text-align:right;">'.$cronograma['total_cuota'].'</th>
                <th></th>
                <th style="text-align:right;">'.$cronograma['total_tenencia'].'</th>
                <th style="text-align:right;">'.$cronograma['total_penalidad'].'</th>
                <th style="text-align:right;">'.$cronograma['total_compensatorio'].'</th>
                <th style="text-align:right;">'.$cronograma['total_totalcuota'].'</th>
                <!--th style="text-align:right;">'.$cronograma['total_acuenta'].'</th-->
              </tr>
              </thead>
              </table>';
          
          $total_cargo = DB::table('credito_cargo')
              ->where('credito_cargo.idestadocredito_cargo',1)
              ->where('credito_cargo.idcredito',$request->idcredito)
              ->sum('credito_cargo.importe');
          
      
          $numero_credito = DB::table('credito')
              ->where('credito.idestadocredito',1)
              ->where('credito.idcliente',$credito->idcliente)
              ->count();
              
          $descuento_total = number_format($cronograma['select_totalcuota'],2,'.','');
          $penalidad_pagar = $descuento_total-$total_descuento_total;
          
          //opciones
          
          $btn_congelarcredito = '<button type="button" class="btn btn-info" onclick="congelarcredito()" style="font-weight: bold;">
    <img src="'.url('public/backoffice/nuevosistema/congelador.png').'" style="width: 17px;"> CONGELAR CRÉDITO</button>';
          if($credito->idestado_congelarcredito==2){
              $btn_congelarcredito = '<div class="btn btn-info" style="float:right">CRÉDITO CONGELADO ('.date_format(date_create($credito->fecha_congelarcredito),'d-m-Y').')</div>';
          }
              
          $opciones_datosprestamos = '<button type="button" class="btn btn-warning" onclick="vistapreliminar()" style="background-color: #bcbcbc;
    border-color: #bcbcbc;
    font-weight: bold;">CRONOGRAMA/HOJA DE RESUMEN</button> '.$btn_congelarcredito;
          
          $total_adelantos = DB::table('credito_adelanto')
                ->where('credito_adelanto.numerocuota',$primera_cuota_pendiente)
                ->where('credito_adelanto.idcredito',$request->idcredito)
                ->whereIn('credito_adelanto.idestadocredito_adelanto',[1,2])
                ->where('credito_adelanto.idcredito',$request->idcredito)
                ->sum('credito_adelanto.total');
          
            // clasificacion
              $cronogramaclasi = select_cronograma(
                    $idtienda,
                    $credito->id,
                    $credito->idforma_credito,
                    $credito->modalidadproductocredito,
                    $credito->cuotas,
              );
              $clasificacion = '';
            
              if($cronogramaclasi['ultimo_atraso']<=8){
                  $clasificacion = 'NORMAL';
              }
              elseif($cronogramaclasi['ultimo_atraso']>8 && $cronogramaclasi['ultimo_atraso']<=30){
                  $clasificacion = 'CPP';
              }
              elseif($cronogramaclasi['ultimo_atraso']>30 && $cronogramaclasi['ultimo_atraso']<=60){
                  $clasificacion = 'DIFICIENTE';
              }
              elseif($cronogramaclasi['ultimo_atraso']>60 && $cronogramaclasi['ultimo_atraso']<=120){
                  $clasificacion = 'DUDOSO';
              }
              elseif($cronogramaclasi['ultimo_atraso']>120){
                  $clasificacion = 'PÉRDIDA';
              }
          
          return array(
              'credito' => $credito,
              'tabla_cronorgrama' => $html,
              'opciones_datosprestamos' => $opciones_datosprestamos,
              'btn_congelarcredito' => $btn_congelarcredito,
              'select_ultimacuotacancelada' => $cronograma['select_ultimacuotacancelada'],
              'proximo_vencimiento' => $cronograma['proximo_vencimiento'],
              
              'numerodecuenta' => 'C'.str_pad($credito->cuenta, 8, "0", STR_PAD_LEFT),
              'clasificacion' => $clasificacion,
              'numero_cuota_cancelada' => $cronograma['numero_cuota_cancelada'],
              'numero_cuota_pendiente' => $cronograma['numero_cuota_pendiente'],
              'numero_cuota_vencida' => $cronograma['numero_cuota_vencida'],
              'cuota_pagada' => $cronograma['cuota_pagada'],
              'cuota_pendiente' => $cronograma['cuota_pendiente'],
              'saldo_vencido' => $cronograma['cuota_vencida'],
              'saldo_capital' => $cronograma['saldo_capital'],
              'numero_credito' => $numero_credito,
              'estadocuotas' => $credito->forma_pago_credito_nombre,
              'asesor' => $credito->asesor,
              //'numero_total' => $cronograma['numero_cuota_cancelada']+$cronograma['numero_cuota_pendiente']+$cronograma['numero_cuota_vencida'],
              //'saldo_total' => number_format($cronograma['cuota_pagada']+$cronograma['cuota_pendiente']+$cronograma['cuota_vencida'],2,'.',''),
              
              'cantidad_cuota' => $cronograma['select_numerocuota'],
              'monto_apagar' => $cronograma['select_cuota'],
              'monto_totalapagar' => number_format($cronograma['select_pagar_totalcuota'],2,'.',''),
              'penalidad_pagar' => number_format($penalidad_pagar,2,'.',''),
              'tenencia_penalidad_mora' => number_format($cronograma['select_tenencia']+$cronograma['select_penalidad']+$cronograma['select_compensatorio'],2,'.',''),
              'pagoacuenta_acuenta' => $total_adelantos,
              'pagoacuenta_capital' => $cronograma['select_amortizacion'],
              'pagoacuenta_interes' => $cronograma['select_interes'],
              'pagoacuenta_interescuotamora' => $cronograma['select_compensatorio'],
              
              'descuento_capital' => $cronograma['select_amortizacion'],
              'descuento_interes' => $cronograma['select_interes'],
              'descuento_comision' => $cronograma['select_comision'],
              'descuento_cargo' => $cronograma['select_cargo'],
              'descuento_tenencia' => $cronograma['select_tenencia'],
              'descuento_penalidad' => $cronograma['select_penalidad'],
              'descuento_moratoria' => $cronograma['select_compensatorio'],
              'descuento_total' => $descuento_total,
              'descuento_totaldescontado' => number_format($total_descuento_total,2,'.',''),
              'descuento_porcobrar' => number_format($total_cargo,2,'.',''),
              'totalapagar' => number_format($cronograma['select_pagar_totalcuota']+$total_cargo,2,'.',''),
          );
        }

        elseif($id == 'show_credito'){
          $creditos = DB::table('credito')
                            ->join('users as cliente','cliente.id','credito.idcliente')
                            ->where('credito.estado','DESEMBOLSADO')
                            ->where('cliente.identificacion','LIKE','%'.$request->buscar.'%')
                            ->where('credito.idestadocredito',1)
                            ->orWhere('credito.estado','DESEMBOLSADO')
                            ->where('cliente.nombrecompleto','LIKE','%'.$request->buscar.'%')
                            ->where('credito.idestadocredito',1)
                            ->select(
                                'cliente.id as idcliente',
                                'cliente.identificacion as identificacion',
                                'cliente.nombrecompleto as nombrecliente',
                            )
                            ->distinct()
                            ->orderBy('credito.fecha_desembolso','asc')
                            ->get();
            $data = [];
            foreach($creditos as $value){
                $data[] = [
                    'id' => $value->idcliente,
                    'text' => $value->identificacion.' - '.$value->nombrecliente,
                ];
            }
          return $data;
        }
        else if($id == 'showlistacreditos'){
          $cliente = DB::table('users')->whereId($request->idcliente)->select('users.id','users.nombrecompleto','users.identificacion')->first();
          $creditos = DB::table('credito')
                            ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
                            ->join('users as cliente','cliente.id','credito.idcliente')
                            ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
                            ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
                            ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                            ->where('credito.estado','DESEMBOLSADO')
                            ->where('cliente.id',$request->idcliente)
                            ->where('credito.idestadocredito',1)
                            ->select(
                                'credito.*',
                                'cliente.identificacion as identificacion',
                                'cliente.nombrecompleto as nombrecliente',
                            )
                            ->orderBy('credito.fecha_desembolso','asc')
                            ->get();
          $html = '';
          foreach($creditos as $value){
              $cp = '';
              if($value->idforma_credito==1){
                  $cp = 'CP';
              }
              elseif($value->idforma_credito==2){
                  $cp = 'CNP';
              }
              elseif($value->idforma_credito==3){
                  $cp = 'CC';
              }
              $cuenta = str_pad($value->cuenta, 8, "0", STR_PAD_LEFT);
              $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data(this)'>
                            <td style='text-align: right;width: 90px;'>S/ {$value->monto_solicitado}</td>
                            <td style='width: 20px;'>{$cp}</td>
                            <td>C{$cuenta}</td>
                        </tr>";
          }
          return array(
            'cliente' => $cliente,
            'html' => $html
          );
          
        }
        else if($id == 'show_descuentodecuotas'){
            
          
          $total_cronograma = DB::table('credito_cronograma')
              ->where('credito_cronograma.idcredito',$request->idcredito)
              ->where('credito_cronograma.idestadocredito_cronograma',1)
              ->orderBy('credito_cronograma.numerocuota','asc')
              ->limit(1)
              ->first();
            
          $numerocuota = 0;
          if($total_cronograma!=''){
              $numerocuota = $total_cronograma->numerocuota;
          }
            
          /*$credito_cobranzacuota = DB::table('credito_cobranzacuota')
              ->join('credito_adelanto','credito_adelanto.idcredito_cobranzacuota','credito_cobranzacuota.id')
              ->where('credito_cobranzacuota.idcredito',$request->idcredito)
              ->where('credito_adelanto.idestadocredito_adelanto',1)
              ->select('credito_adelanto.*')
              ->orderBy('credito_cobranzacuota.codigo','desc')
              ->limit(1)
              ->first();
  
          $numerocuota = 0;
          if($credito_cobranzacuota!=''){
              $numerocuota = $credito_cobranzacuota->numerocuota;
          }*/
          
          $credito_adelantos = DB::table('credito_adelanto')
              ->where('credito_adelanto.numerocuota',$numerocuota)
              ->where('credito_adelanto.idcredito',$request->idcredito)
              ->whereIn('credito_adelanto.idestadocredito_adelanto',[1,2])
              ->orderBy('credito_adelanto.numerocuota','asc')
              ->get();
          
          $html = '<table class="table" id="table-detalle-descuentodecuotas">
              <thead>
              <tr>
                <th style="width:100px;">Fecha de Registro</th>
                <th style="width:5px;">N° Cuota</th>
                <th>Atraso</th>
                <th>Capital</th>
                <th>Interes</th>
                <th>Comisión</th>
                <th>Cargo</th>
                <th>Custodia</th>
                <th>Int. Comp.</th>
                <th>Int. Morat.</th>
                <th>Total</th>
              </tr>
              </thead>
              <tbody>';
          
          $total_capital = 0;
          $total_interes = 0;
          $total_comision = 0;
          $total_cargo = 0;
          $total_tenencia = 0;
          $total_penalidad = 0;
          $total_compensatorio = 0;
          $total_total = 0;
          $i = 1;
          
          foreach($credito_adelantos as $value){
               $fecharegistro = date_format(date_create($value->fecharegistro),'d-m-Y H:i:s A');
              $html .= "<tr data-valor-columna='{$value->id}'>
                            <td style='text-align:center'>{$fecharegistro}</td>
                            <td style='text-align:center'>{$value->numerocuota}</td>
                            <td style='text-align:center'>{$value->atraso}</td>
                            <td style='text-align:right'>{$value->capital}</td>
                            <td style='text-align:right'>{$value->interes}</td>
                            <td style='text-align:right'>{$value->comision}</td>
                            <td style='text-align:right'>{$value->cargo}</td>
                            <td style='text-align:right'>{$value->tenencia}</td>
                            <td style='text-align:right'>{$value->penalidad}</td>
                            <td style='text-align:right'>{$value->compensatorio}</td>
                            <td style='text-align:right'>{$value->total}</td>
                        </tr>";
          
              $total_capital = $total_capital+$value->capital;
              $total_interes = $total_interes+$value->interes;
              $total_comision = $total_comision+$value->comision;
              $total_cargo = $total_cargo+$value->cargo;
              $total_tenencia = $total_tenencia+$value->tenencia;
              $total_penalidad = $total_penalidad+$value->penalidad;
              $total_compensatorio = $total_compensatorio+$value->compensatorio;
              $total_total = $total_total+$value->total;
              $i = $i+1;
          }
          $html .= '</tbody>
              <thead>
              <tr>
                <th style="text-align:right" colspan="3">TOTAL</th>
                <th style="text-align:right">'.number_format($total_capital, 2, '.', '').'</th>
                <th style="text-align:right">'.number_format($total_interes, 2, '.', '').'</th>
                <th style="text-align:right">'.number_format($total_comision, 2, '.', '').'</th>
                <th style="text-align:right">'.number_format($total_cargo, 2, '.', '').'</th>
                <th style="text-align:right">'.number_format($total_tenencia, 2, '.', '').'</th>
                <th  style="text-align:right">'.number_format($total_penalidad, 2, '.', '').'</th>
                <th style="text-align:right">'.number_format($total_compensatorio, 2, '.', '').'</th>
                <th style="text-align:right">'.number_format($total_total, 2, '.', '').'</th>
              </tr>
              </thead>
              </table>';
          return array(
            'html' => $html
          );
          
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $credito = DB::table('credito')
                ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                ->where('credito.id',$id)
                ->select(
                    'credito.*',
                    'credito_prendatario.modalidad as modalidadproductocredito',
                    'credito_prendatario.nombre as nombreproductocredito',
                )
                ->first();
        
        if($request->input('view') == 'cobrar') {

            $bancos = DB::table('banco')->where('estado','ACTIVO')->get(); 
            
            $credito_cargo = DB::table('credito_cargo')
              ->where('credito_cargo.idestadocredito_cargo',1)
              ->where('credito_cargo.idcredito',$id)
              ->first();
              
            $idcredito_cargo = 0; 
            $total_cargo = 0;   
            if($credito_cargo){
                $idcredito_cargo = $credito_cargo->id;
                $total_cargo = $credito_cargo->importe;
            }

            // descuento cuota
            $credito_descuentocuotas = DB::table('credito_descuentocuota')
                  ->where('credito_descuentocuota.idcredito',$id)
                  ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                  ->first();
            $idcredito_descuentocuota = 0; 
            $total_descuento_capital = 0; 
            $total_descuento_interes = 0; 
            $total_descuento_comision = 0; 
            $total_descuento_cargo = 0;  
            $total_descuento_penalidad = 0; 
            $total_descuento_tenencia = 0; 
            $total_descuento_compensatorio = 0; 
            $total_descuento_total = 0; 
            if($credito_descuentocuotas){
                if($request->numerocuota>=$credito_descuentocuotas->numerocuota_fin){
                    $idcredito_descuentocuota = $credito_descuentocuotas->id;
                    $total_descuento_capital = $credito_descuentocuotas->capital;
                    $total_descuento_interes = $credito_descuentocuotas->interes;
                    $total_descuento_comision = $credito_descuentocuotas->comision;
                    $total_descuento_cargo = $credito_descuentocuotas->cargo;
                    $total_descuento_penalidad = $credito_descuentocuotas->penalidad;
                    $total_descuento_tenencia = $credito_descuentocuotas->tenencia;
                    $total_descuento_compensatorio = $credito_descuentocuotas->compensatorio;
                    $total_descuento_total = $credito_descuentocuotas->total;
                }
            }
                
            $cronograma = select_cronograma(
                $idtienda,
                $id,
                $credito->idforma_credito,
                $credito->modalidadproductocredito,
                $request->numerocuota,
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
          
            $primera_cuota_pendiente = 0;
            foreach($cronograma['cronograma'] as $value){

                if($value['idestadocredito_cronograma']==1 && $primera_cuota_pendiente==0){
                    $primera_cuota_pendiente = $value['numerocuota'];
                }

            }
          
            $total_adelantos = DB::table('credito_adelanto')
                ->where('credito_adelanto.numerocuota',$primera_cuota_pendiente)
                ->whereIn('credito_adelanto.idestadocredito_adelanto',[1,2])
                ->where('credito_adelanto.idcredito',$id)
                ->sum('credito_adelanto.total');
          
            $creditorefinanciado = DB::table('credito')
                ->where('idcredito_refinanciado',$credito->id)
                ->first();
          
            return view(sistema_view().'/cobranzacuota/cobrar',[
                'tienda' => $tienda,
                'credito' => $credito,
                'creditorefinanciado' => $creditorefinanciado,
                'bancos' => $bancos,
                'select_numerocuota_fin' => $cronograma['select_numerocuota_fin'],
                'monto_cargo' => number_format($total_cargo,2,'.',''),
                'total_acuenta' => $total_adelantos,
                'monto_cuotaapagar' => number_format($cronograma['select_pagar_totalcuota']-$total_adelantos,2,'.',''),
                'monto_totalapagar' => number_format($cronograma['select_pagar_totalcuota']+$total_cargo,2,'.',''),
                'total_cargo' => $total_cargo,
                'numerocuota' => $request->numerocuota,
                'idcredito_cargo' => $idcredito_cargo,
                'idcredito_descuentocuota' => $idcredito_descuentocuota,
                'opcion_pago' => $request->opcion_pago,
            ]);
        }
        elseif($request->input('view') == 'vistapreliminar'){
                

        $usuario = DB::table('users')
              ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
              ->leftJoin('ubigeo as ubigeonacimiento','ubigeonacimiento.id','users.idubigeo_nacimiento')
              ->leftJoin('role_user','role_user.user_id','users.id')
              ->leftJoin('roles','roles.id','role_user.role_id')
              ->where('users.id', $credito->idcliente)
              ->select(
                  'users.*',
                  'roles.id as idroles',
                  'roles.description as descriptionrole',
                  'ubigeo.nombre as ubigeonombre',
                  'ubigeonacimiento.nombre as ubigeonacimientonombre'
              )
              ->first();
        $nivel_aprobacion = DB::table('nivelaprobacion')
                              ->where('nivelaprobacion.idtipocredito',$credito->idforma_credito)
                              ->where('nivelaprobacion.riesgocredito1','<',$credito->monto_solicitado)
                              ->where('nivelaprobacion.riesgocredito2','>=',$credito->monto_solicitado)
                              ->first();
        
        $credito_aprobacion = DB::table('credito_aprobacion')
                              ->leftJoin('permiso','permiso.id','credito_aprobacion.idpermiso')
                              ->leftJoin('users','users.id','credito_aprobacion.idusers')
                              ->where('credito_aprobacion.idcredito',$credito->id)
                              ->select(
                                'credito_aprobacion.*',
                                'permiso.nombre as nombre_permiso',
                                'users.nombrecompleto as nombre_usuario',
                                'users.nombre as nombre',
                                'users.apellidopaterno as apellidopaterno',
                                'users.clave as clave_usuario'
                              )
                              ->orderBy('permiso.rango','asc')
                              ->get();
        
        $garantias = DB::table('credito_garantia')
                ->leftJoin('garantias','garantias.id','credito_garantia.idgarantias')
                ->where('idcredito', $credito->id)
                ->where('credito_garantia.tipo', 'CLIENTE')
                ->select(
                  'credito_garantia.id as id'
                )
                ->get();
        
        return view(sistema_view().'/cobranzacuota/vistapreliminar',[
          'tienda' => $tienda,
          'credito' => $credito,
          'usuario' => $usuario,
          'nivel_aprobacion' => $nivel_aprobacion,
          'credito_aprobacion' => $credito_aprobacion,
          'estado' => $request->input('tipo'),
              'garantias' => $garantias,
        ]);
      }
        elseif($request->input('view') == 'opcion') {
            return view(sistema_view().'/cobranzacuota/opcion',[
                'tienda' => $tienda,
                'credito' => $credito,
                'idcobranzacuota' => $request->idcobranzacuota,
                'idestadocredito' => $request->idestadocredito,
                'entregargarantia'   => $request->entregargarantia,
            ]);
        }
        elseif($request->input('view') == 'congelarcredito') {
         
            
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->where('users_permiso.idpermiso',1)
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
          
            return view(sistema_view().'/cobranzacuota/congelarcredito',[
              'tienda' => $tienda,
              'credito' => $credito,
                'usuarios' => $usuarios,
            ]);
        }
        elseif($request->input('view') == 'pdf_pago'){
          
            $credito_cobranzacuota = DB::table('credito_cobranzacuota')
              ->join('credito','credito.id','credito_cobranzacuota.idcredito')
              ->join('users as cliente','cliente.id','credito.idcliente')
              ->where('credito_cobranzacuota.id',$request->idcobranzacuota)
              ->select(
                  'credito_cobranzacuota.*',
                  'cliente.nombrecompleto as nombrecliente',
                  'credito.idcliente as idcliente',
                  'credito.cuenta as creditocuenta',
              )
              ->first();
              
            $usuario = DB::table('users')
              ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
              ->leftJoin('ubigeo as ubigeonacimiento','ubigeonacimiento.id','users.idubigeo_nacimiento')
              ->leftJoin('role_user','role_user.user_id','users.id')
              ->leftJoin('roles','roles.id','role_user.role_id')
              ->where('users.id', $credito->idcliente)
              ->select(
                  'users.*',
                  'roles.id as idroles',
                  'roles.description as descriptionrole',
                  'ubigeo.nombre as ubigeonombre',
                  'ubigeonacimiento.nombre as ubigeonacimientonombre'
              )
              ->first();
            
            $cajero = DB::table('users')->where('users.id',$credito_cobranzacuota->idcajero)->first();
           
            /*$total_cronogramaultimo = DB::table('credito_cronograma')
                ->where('credito_cronograma.idcredito',$credito_cobranzacuota->idcredito)
                ->where('credito_cronograma.idestadocredito_cronograma',1)
                ->orderBy('credito_cronograma.numerocuota','asc')
                ->limit(1)
                ->first();
               
            $numerocuota_ultimo = 0;
            if($total_cronogramaultimo!=''){
                $numerocuota_ultimo = $total_cronogramaultimo->numerocuota;
            }*/
          
            
            $count_creditopendiente = DB::table('credito_garantia')
                  ->where('credito_garantia.idcredito',$credito_cobranzacuota->idcredito)
                  ->where('credito_garantia.idestadoentrega',1)
                  ->count();
          
            $count_credito_cronograma = DB::table('credito_cronograma')
                ->where('credito_cronograma.idcredito',$credito_cobranzacuota->idcredito)
                ->whereIn('credito_cronograma.idestadocredito_cronograma',[1,3])
                ->count();
          
            $pdf = PDF::loadView(sistema_view().'/cobranzacuota/pdf_pago',[
                'tienda' => $tienda,
                'creditocuenta' => $credito_cobranzacuota->cuenta,
                'usuario' => $usuario,
                'cajero' => $cajero,
                'credito' => $credito,
                'banco' => $credito_cobranzacuota->banco,
                'bancocuenta' => $credito_cobranzacuota->cuenta,
                'numerooperacion' => $credito_cobranzacuota->numerooperacion,
                'idformapago' => $credito_cobranzacuota->idformapago,
                'pago_cuota' => $credito_cobranzacuota->pago_cuota,
                'pago_diasatraso' => $credito_cobranzacuota->pago_diasatraso,
                'total_pendientepago' => $credito_cobranzacuota->total_pendientepago,
                'credito_cobranzacuota' => $credito_cobranzacuota,
                'count_creditopendiente'   => $count_creditopendiente,
                'count_credito_cronograma' => $count_credito_cronograma,
            ]); 
            $pdf->setPaper('A4');
            return $pdf->stream('VOUCHER_PAGO.pdf');
        }
        elseif($request->input('view') == 'pdf_garantia' ){
            
            $credito_cobranzacuota = DB::table('credito_cobranzacuota')
              ->join('credito','credito.id','credito_cobranzacuota.idcredito')
              ->where('credito_cobranzacuota.id', $request->idcobranzacuota)
              ->select(
                  'credito_cobranzacuota.*',
                  'credito.cuenta as creditocuenta',
              )
              ->first();
              
            $usuario = DB::table('users')
              ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
              ->leftJoin('ubigeo as ubigeonacimiento','ubigeonacimiento.id','users.idubigeo_nacimiento')
              ->leftJoin('role_user','role_user.user_id','users.id')
              ->leftJoin('roles','roles.id','role_user.role_id')
              ->where('users.id', $credito->idcliente)
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
              ->where('idcredito', $credito->id)
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
            'num' => $request->num,
            ]); 
            $pdf->setPaper('A4');
            return $pdf->stream('VOUCHER_PAGO.pdf');
        }
        elseif($request->input('view') == 'ver_descuentos') {
            return view(sistema_view().'/cobranzacuota/ver_descuentos',[
              'tienda' => $tienda,
              'credito' => $credito,
            ]);
        }
        elseif($request->input('view') == 'ver_cuentasporcobrar') {
            return view(sistema_view().'/cobranzacuota/ver_cuentasporcobrar',[
              'tienda' => $tienda,
              'credito' => $credito,
            ]);
        }
        elseif($request->input('view') == 'ver_pagoacuenta') {
            return view(sistema_view().'/cobranzacuota/ver_pagoacuenta',[
              'tienda' => $tienda,
              'credito' => $credito,
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
