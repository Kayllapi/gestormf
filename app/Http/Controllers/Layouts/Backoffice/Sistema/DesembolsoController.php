<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class DesembolsoController extends Controller
{
    public function __construct()
    {
        //
    }
    public function index(Request $request,$idtienda)
    {
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/desembolso/tabla',[
              'tienda' => $tienda,
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

        if($id == 'showtable'){
          /*$where = [];
          $where[] = ['credito.fecha_aprobacion','>=',$request->inicio.' 00:00:00'];
          $where[] = ['credito.fecha_aprobacion','<=',$request->fin.' 23:59:59'];*/
          
          $creditos = DB::table('credito')
                            ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
                            ->join('users as cliente','cliente.id','credito.idcliente')
                            ->leftjoin('users as asesor','asesor.id','credito.idasesor')
                            ->leftjoin('users as administrador','administrador.id','credito.idadministrador')
                            ->leftjoin('users as aval','aval.id','credito.idaval')
                            ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
                            ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
                            // ->join('tarifario','tarifario.id','credito.idtarifario')
                            ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                            ->where('credito.estado','APROBADO')
                            //->where($where)
                            ->select(
                                'credito.*',
                                'cliente.nombrecompleto as nombrecliente',
                                'aval.nombrecompleto as nombreaval',
                                'credito_prendatario.nombre as nombreproductocredito' ,
                                'modalidad_credito.nombre as nombremodalidadcredito' ,
                                'forma_pago_credito.nombre as frecuencianombre' ,
                                'asesor.usuario as asesorcodigo',
                                'administrador.nombrecompleto as nombreadministrador',
                            )
                            ->orderBy('credito.fecha_aprobacion','asc')
                            ->get();
          
          $html = '';
          foreach($creditos as $key => $value){
              
              $html .= "<tr id='show_data_select' idcredito='{$value->id}'>
                            <td>".($key+1)."</td>
                            <td>{$value->nombrecliente}</td>
                            <td>{$value->nombreaval}</td>
                            <td>{$value->monto_solicitado}</td>
                            <td>{$value->cuotas}</td>
                            <td>{$value->frecuencianombre}</td>
                            <td>{$value->fecha_aprobacion}</td>
                            <td>{$value->asesorcodigo}</td>
                            <td>{$value->nombremodalidadcredito}</td>
                        </tr>";
          }
          if(count($creditos)==0){
              $html.= '<tr><td colspan="16" style="text-align: center;font-weight: bold;">No hay ningún desembolso aprobado!!</td></tr>';
          }
          return array(
            'html' => $html
          );
          
        }

    }

    public function edit(Request $request, $idtienda, $id)
    {
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
 
      $credito = DB::table('credito')
                    ->join('users as cliente','cliente.id','credito.idcliente')
                    ->leftjoin('users as aval','aval.id','credito.idaval')
                    ->join('forma_credito','forma_credito.id','credito.idforma_credito')
                    ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
                    ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
                    ->join('tipo_destino_credito','tipo_destino_credito.id','credito.idtipo_destino_credito')
                    ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
                    ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                    ->where('credito.id',$id)

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
      
        $asesor = DB::table('users')->where('users.id',$credito->idasesor)->first();
 
        $users_prestamo = DB::table('s_users_prestamo')->where('s_users_prestamo.id_s_users',$credito->idcliente)->first();
        $users_prestamo_aval = DB::table('s_users_prestamo')->where('s_users_prestamo.id_s_users',$credito->idaval)->first();
      if( $request->input('view') == 'desembolsar' ){
                
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
        
        return view(sistema_view().'/desembolso/desembolsar',[
          'tienda' => $tienda,
          'credito' => $credito,
          'usuario' => $usuario,
          'nivel_aprobacion' => $nivel_aprobacion,
          'credito_aprobacion' => $credito_aprobacion,
          'estado' => $request->input('tipo'),
          'garantias' => $garantias,
        ]);
      }
      elseif( $request->input('view') == 'desembolsarticket' ){

        $bancos = DB::table('banco')->where('estado','ACTIVO')->get();
        
        $credito_propuesta = DB::table('credito_propuesta')->where('idcredito',$credito->id)->first();
        
        return view(sistema_view().'/desembolso/desembolsarticket',[
          'tienda' => $tienda,
          'credito' => $credito,
          'usuario' => $usuario,
          'bancos' => $bancos,
          'credito_propuesta' => $credito_propuesta,
        ]);
      }
      
      else if( $request->input('view') == 'pdf_cronograma' ){

        $credito_cronograma = DB::table('credito_cronograma')
                              ->where('credito_cronograma.idcredito',$credito->id)
                              ->get();
        
        $pdf = PDF::loadView(sistema_view().'/desembolso/pdf_cronograma',[
            'users_prestamo'    => $users_prestamo,
            'tienda' => $tienda,
            'credito' => $credito,
            'usuario' => $usuario,
            'asesor' => $asesor,
            'credito_cronograma' => $credito_cronograma,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('CRONOGRAMA.pdf');
      }
      else if( $request->input('view') == 'pdf_contrato' ){

        $aval = DB::table('users')->where('users.id',$credito->idaval)->first();
        $ubigeo_tienda = DB::table('ubigeo')->where('ubigeo.id',$tienda->idubigeo)->first();
        $garantias = DB::table('credito_garantia')
          ->leftJoin('garantias','garantias.id','credito_garantia.idgarantias')
          ->where('idcredito', $credito->id)
          ->where('credito_garantia.tipo', 'CLIENTE')
          ->select(
            'garantias.*'
          )
          ->get();
        //dd($credito->custodiagarantia_id);
        $nomcredito = '';
        if($credito->idforma_credito==1){
            if($credito->constituciongarantia_id==1){
                $nomcredito = 'prendario_con_posesion';
            }elseif($credito->constituciongarantia_id==2){
                $nomcredito = 'prendario_sin_posesion';
            }
        }
        elseif($credito->idforma_credito==2){
            $nomcredito = 'noprendario';
        }
        $pdf = PDF::loadView(sistema_view().'/desembolso/pdf_contrato_'.$nomcredito,[
            'users_prestamo'    => $users_prestamo,
            'tienda' => $tienda,
            'credito' => $credito,
            'usuario' => $usuario,
            'asesor' => $asesor,
            'aval' => $aval,
            'garantias' => $garantias,
            'users_prestamo_aval' => $users_prestamo_aval,
            'ubigeo_tienda' => $ubigeo_tienda,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('CONTRATO.pdf');
      }
      else if( $request->input('view') == 'pdf_resumen' ){

        $aval = DB::table('users')->where('users.id',$credito->idaval)->first();
        $tipo_garantia1 = DB::table('tipo_garantia')->offset(0)->limit(3)->get();
        $tipo_garantia2 = DB::table('tipo_garantia')->offset(3)->limit(3)->get();
        $tipo_garantia3 = DB::table('tipo_garantia')->offset(6)->limit(3)->get();
        $garantias = DB::table('credito_garantia')
          ->where('credito_garantia.tipo', 'CLIENTE')
          ->where('credito_garantia.idcredito', $credito->id)
          ->select(
            'credito_garantia.*',
          )
          ->get();
        $garantiasaval = DB::table('credito_garantia')
          ->where('tipo', 'AVAL')
          ->where('idcredito', $credito->id)
          ->select(
            'credito_garantia.*',
          )
          ->get();
        $pdf = PDF::loadView(sistema_view().'/desembolso/pdf_resumen',[
            'users_prestamo'    => $users_prestamo,
            'tienda' => $tienda,
            'credito' => $credito,
            'usuario' => $usuario,
            'asesor' => $asesor,
            'garantias' => $garantias,
            'garantiasaval' => $garantiasaval,
            'tipo_garantia1' => $tipo_garantia1,
            'tipo_garantia2' => $tipo_garantia2,
            'tipo_garantia3' => $tipo_garantia3,
            'aval' => $aval,
            'users_prestamo_aval' => $users_prestamo_aval,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('RESUMEN.pdf');
      }
      else if( $request->input('view') == 'pdf_declaracion' ){

        $ubigeo_tienda = DB::table('ubigeo')->where('ubigeo.id',$tienda->idubigeo)->first();
        $garantias = DB::table('credito_garantia')
          ->leftJoin('garantias','garantias.id','credito_garantia.idgarantias')
          ->where('idcredito', $credito->id)
          ->where('credito_garantia.tipo', 'CLIENTE')
          ->select(
            'garantias.*'
          )
          ->get();
        $pdf = PDF::loadView(sistema_view().'/desembolso/pdf_declaracion',[
            'users_prestamo'    => $users_prestamo,
            'tienda' => $tienda,
            'credito' => $credito,
            'usuario' => $usuario,
            'asesor' => $asesor,
            'garantias' => $garantias,
            'ubigeo_tienda' => $ubigeo_tienda,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('DECLARACION.pdf');
      }
      else if( $request->input('view') == 'pdf_pagare' ){

        $aval = DB::table('users')->where('users.id',$credito->idaval)->first();
        $tipo_garantia1 = DB::table('tipo_garantia')->offset(0)->limit(3)->get();
        $tipo_garantia2 = DB::table('tipo_garantia')->offset(3)->limit(3)->get();
        $tipo_garantia3 = DB::table('tipo_garantia')->offset(6)->limit(3)->get();
        $garantias = DB::table('credito_garantia')
          ->where('credito_garantia.tipo', 'CLIENTE')
          ->where('credito_garantia.idcredito', $credito->id)
          ->select(
            'credito_garantia.*',
          )
          ->get();
        $garantiasaval = DB::table('credito_garantia')
          ->where('tipo', 'AVAL')
          ->where('idcredito', $credito->id)
          ->select(
            'credito_garantia.*',
          )
          ->get();
        $pdf = PDF::loadView(sistema_view().'/desembolso/pdf_pagare',[
            'users_prestamo'    => $users_prestamo,
            'tienda' => $tienda,
            'credito' => $credito,
            'usuario' => $usuario,
            'asesor' => $asesor,
            'garantias' => $garantias,
            'garantiasaval' => $garantiasaval,
            'tipo_garantia1' => $tipo_garantia1,
            'tipo_garantia2' => $tipo_garantia2,
            'tipo_garantia3' => $tipo_garantia3,
            'aval' => $aval,
            'users_prestamo_aval' => $users_prestamo_aval,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('RESUMEN.pdf');
      }
      
      else if( $request->input('view') == 'pdf_ticket' ){

        $credito_formapago = DB::table('credito_formapago')
            ->where('credito_formapago.idcredito',$credito->id)
            ->first();
        $idformapago = 0;
        $banco = '';
        $bancocuenta = '';
        $numerooperacion = '';
        $operacion = '';
        if($credito_formapago){
            $operacion = $credito_formapago->codigo;
            $banco = $credito_formapago->banco;
            $bancocuenta = $credito_formapago->cuenta;
            $numerooperacion = $credito_formapago->numerooperacion;
            $idformapago = $credito_formapago->idformapago;
        }
        $cajero = DB::table('users')->where('users.id',$credito->idcajero)->first();
        $garantias = DB::table('credito_garantia')->where('idcredito', $credito->id)->get();
        $pdf = PDF::loadView(sistema_view().'/desembolso/pdf_ticket',[
            'users_prestamo'    => $users_prestamo,
            'tienda' => $tienda,
            'credito' => $credito,
            'usuario' => $usuario,
            'asesor' => $asesor,
            'cajero' => $cajero,
            'garantias' => $garantias,
            'operacion' => $operacion,
            'banco' => $banco,
            'bancocuenta' => $bancocuenta,
            'numerooperacion' => $numerooperacion,
            'idformapago' => $idformapago,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('DECLARACION.pdf');
      }
      else if( $request->input('view') == 'pdf_ticketprendario' ){

        $cajero = DB::table('users')->where('users.id',$credito->idcajero)->first();
        $garantias = DB::table('credito_garantia')
          ->leftJoin('garantias','garantias.id','credito_garantia.idgarantias')
          ->where('credito_garantia.id', $request->idgarantia)
          ->where('credito_garantia.tipo', 'CLIENTE')
          ->select(
            'garantias.*'
          )
          ->first();
        $pdf = PDF::loadView(sistema_view().'/desembolso/pdf_ticketprendario',[
            'users_prestamo'    => $users_prestamo,
            'tienda' => $tienda,
            'credito' => $credito,
            'usuario' => $usuario,
            'asesor' => $asesor,
            'cajero' => $cajero,
            'garantias' => $garantias,
            'num' => $request->num,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('DECLARACION.pdf');
      }
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if( $request->input('view') == 'realizar_desembolo' ) {
           
              if($request->idformapago==2){
                  $rules = [
                      'idbanco' => 'required',                  
                      'numerooperacion' => 'required',                       
                  ];

                  $messages = [
                      'idbanco.required' => 'El Campo Banco es Obligatorio.',
                      'numerooperacion.required' => 'El Campo Número de Operación es Obligatorio.',
                  ];
                  $this->validate($request,$rules,$messages);
              }
          
              $credito = DB::table('credito')->whereId($id)->first();

              $consolidadooperaciones = consolidadooperaciones($tienda,$idtienda,now()->format('Y-m-d'));
              if($request->idformapago==1){
                  if($consolidadooperaciones['saldos_caja']<$credito->monto_solicitado){
                      return response()->json([
                          'resultado' => 'ERROR',
                          'mensaje'   => 'No hay saldo suficiente en CAJA.<br><b>Saldo Actual: S/. '.$consolidadooperaciones['saldos_caja'].'.</b>'
                      ]);
                  }
              }
              elseif($request->idformapago==2){
                  foreach($consolidadooperaciones['saldos_cuentabanco_bancos'] as $value){
                      if($value['banco_id']==$request->idbanco && $value['banco']<$credito->monto_solicitado){
                          return response()->json([
                              'resultado' => 'ERROR',
                              'mensaje'   => 'No hay saldo suficiente en Cuenta Bancaria.'
                          ]);
                      }
                  } 
              }
          
              $credito_ult = DB::table('credito')
                  ->orderBy('credito.cuenta','desc')
                  ->limit(1)
                  ->first();
              $codigo = 1;
              if($credito_ult!=''){
                  $codigo = $credito_ult->cuenta+1;
              }
          
          
              $monto_desembolsado = 0;
              $descuento_saldo = 0;
              $neto_entregar = 0;
              if($credito->idmodalidad_credito==2){
                  $monto_desembolsado = $request->monto_desembolsado;
                  $descuento_saldo = $request->descuento_saldo;
                  $neto_entregar = $request->neto_entregar;
                
                  //realizar cancelacion de cobranza
                
                  $credito_propuesta = DB::table('credito_propuesta')->where('idcredito',$credito->id)->first();
                
                  $monto_compra_deuda_det = json_decode($credito_propuesta->monto_compra_deuda_det,true);
            
                  if($monto_compra_deuda_det!=''){
                      foreach($monto_compra_deuda_det as $value_det){

                          $id_credito_ant = DB::table('credito')
                              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                              ->where('credito.id',$value_det['idcredito'])
                              ->select(
                                  'credito.*',
                                  'credito_prendatario.modalidad as modalidadproductocredito',
                                  'credito_prendatario.nombre as nombreproductocredito',
                              )
                              ->first();
                          // descuento cuota
                          $credito_descuentocuotas = DB::table('credito_descuentocuota')
                                ->where('credito_descuentocuota.idcredito',$id_credito_ant->id)
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
                              if(1000>=$credito_descuentocuotas->numerocuota_fin){
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
                              $id_credito_ant->id,
                              $id_credito_ant->idforma_credito,
                              $id_credito_ant->modalidadproductocredito,
                              1000,
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

                          $credito_cargo = DB::table('credito_cargo')
                            ->where('credito_cargo.idestadocredito_cargo',1)
                            ->where('credito_cargo.idcredito',$id_credito_ant->id)
                            ->first();

                          $idcredito_cargo = 0; 
                          $total_cargo = 0;   
                          if($credito_cargo){
                              $idcredito_cargo = $credito_cargo->id;
                              $total_cargo = $credito_cargo->importe;
                          }

                          $idcredito_cobranzacuota = DB::table('credito_cobranzacuota')->insertGetId([
                              'fecharegistro' => Carbon::now(),
                              'codigo' => $codigo,
                              'total_pagar' => $cronograma['select_totalcuota'],
                              'total_recibido' => $cronograma['select_totalcuota'],
                              'vuelto' => 0,
                              'numerooperacion' => $request->numerooperacion!=''?$request->numerooperacion:'',
                              'banco' => $banco,
                              'cuenta' => $cuenta,
                              'proximo_vencimiento' => $cronograma['proximo_vencimiento'],
                              'pago_cuota' => $pago_cuota,
                              'pago_diasatraso' => $pago_diasatraso,
                              'opcion_pago' => 'PAGO_TOTAL',
                              'estadocargo' => 'on',
                              'cobrar_cargo' => $total_cargo,
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
                              'idcredito' => $id_credito_ant->id,
                              'idformapago' => $request->idformapago,
                              'idbanco' => $request->idbanco!=null?$request->idbanco:0,
                              'idestadocredito_cobranzacuota' => 1, 
                              'idestadoextorno' => 0, // 0 = sin extornar, 2 = extornado
                              'id_credito_ampliado' => $id,
                              'idtienda' => $idtienda,
                              'idestado' => 1,
                          ]);

                          if($idcredito_descuentocuota>0){
                              DB::table('credito_descuentocuota')
                                ->whereId($idcredito_descuentocuota)
                                ->update([
                                  'idcredito_cobranzacuota'        => $idcredito_cobranzacuota,
                                  'idestadocredito_descuentocuota' => 2,
                              ]);
                          }

                          if($idcredito_cargo>0){
                              DB::table('credito_cargo')
                                ->whereId($idcredito_cargo)
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

                          // actualziar ultimo saldo
                          DB::table('credito')
                            ->whereId($id_credito_ant->id)
                            ->update([
                              'saldo_pendientepago' => $cronograma['cuota_pendiente'],
                          ]);

                          $count_credito_cronograma = DB::table('credito_cronograma')
                              ->where('credito_cronograma.idcredito',$id_credito_ant->id)
                              ->where('credito_cronograma.idestadocredito_cronograma',1)
                              ->count();

                          $idestadocredito = 1;

                          if($count_credito_cronograma==0){

                              DB::table('credito')
                                ->whereId($id_credito_ant->id)
                                ->update([
                                  'idestadocredito' => 2,
                              ]);

                              $idestadocredito = 2;
                          }

                          //if($request->entregargarantia=='on'){

                              DB::table('credito_garantia')
                                ->where('credito_garantia.idcredito',$id_credito_ant->id)
                                ->update([
                                  'fechaentrega' => Carbon::now(),
                                  'idestadoentrega' => 2,
                              ]);

                          //}

                          // pagar saldo pendiente

                          $cronograma = select_cronograma(
                              $idtienda,
                              $id_credito_ant->id,
                              $id_credito_ant->idforma_credito,
                              $id_credito_ant->modalidadproductocredito,
                              $id_credito_ant->cuotas,
                              0,
                              0,
                              0,
                              0,
                              0,
                              0,
                              0
                          );

                          DB::table('credito_cobranzacuota')
                            ->whereId($idcredito_cobranzacuota)
                            ->update([
                                  'saldo_pendientepago' => $cronograma['cuota_pendiente'],
                          ]);
                      }
                  }
                             
              }
              
              DB::table('credito')->whereId($id)->update([
                'cuenta' => $codigo,
                'monto_desembolsado' => $monto_desembolsado,
                'descuento_saldo' => $descuento_saldo,
                'neto_entregar' => $neto_entregar,
                'idcajero' => Auth::user()->id,
                'estado' => 'DESEMBOLSADO',
                'fecha_desembolso' => Carbon::now(),
              ]);
          
          
              $credito_formapago = DB::table('credito_formapago')
                  ->orderBy('credito_formapago.codigo','desc')
                  ->limit(1)
                  ->first();
              $codigo = 1;
              if($credito_formapago!=''){
                  $codigo = $credito_formapago->codigo+1;
              }
          
              $bancoo = DB::table('banco')->where('banco.id',$request->idbanco)->first();
              
              $banco = '';
              $cuenta = '';
              if($bancoo!=''){
                  $banco = $bancoo->nombre;
                  $cuenta = $bancoo->cuenta;
              }
          
              DB::table('credito_formapago')->insert([
                  'fechapago' => Carbon::now(),
                  'codigo' => $codigo,
                  'numerooperacion' => $request->numerooperacion!=''?$request->numerooperacion:'',
                  'banco' => $banco,
                  'cuenta' => $cuenta,
                  'idbanco' => $request->idbanco!=''?$request->idbanco:0,
                  'idformapago' => $request->idformapago,
                  'idcredito' => $id,
              ]);
          
            // -----> CREDITO REFINANCIADO
          
            // CANCELAR CREDITO
            $ultimocredito = DB::table('credito')
                  ->whereId($id)
                  ->first();
          
            $credito = DB::table('credito')
                    ->join('users as cliente','cliente.id','credito.idcliente')
                    ->leftjoin('users as aval','aval.id','credito.idaval')
                    ->join('forma_credito','forma_credito.id','credito.idforma_credito')
                    ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
                    ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
                    ->join('tipo_destino_credito','tipo_destino_credito.id','credito.idtipo_destino_credito')
                    ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
                    ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                    ->leftjoin('tipo_credito','tipo_credito.id','credito_prendatario.idtipo_credito')
                    ->where('credito.id',$ultimocredito->idcredito_refinanciado)
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
                        'tipo_credito.nombre as tipo_creditonombre',
                    )
                    ->orderBy('credito.id','desc')
                    ->first();
          
            if($ultimocredito->idcredito_refinanciado!=0){
                $total_cuota = $ultimocredito->monto_solicitado;
                $total_pagar = $ultimocredito->monto_solicitado;
                $total_recibido = $ultimocredito->monto_solicitado;

                // descuento cuota
                $credito_descuentocuotas = DB::table('credito_descuentocuota')
                      ->where('credito_descuentocuota.idcredito',$credito->id)
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
                    //if($request->numerocuota>=$credito_descuentocuotas->numerocuota_fin){
                        $total_descuento_capital = $credito_descuentocuotas->capital;
                        $total_descuento_interes = $credito_descuentocuotas->interes;
                        $total_descuento_comision = $credito_descuentocuotas->comision;
                        $total_descuento_cargo = $credito_descuentocuotas->cargo;
                        $total_descuento_penalidad = $credito_descuentocuotas->penalidad;
                        $total_descuento_tenencia = $credito_descuentocuotas->tenencia;
                        $total_descuento_compensatorio = $credito_descuentocuotas->compensatorio;
                        $total_descuento_total = $credito_descuentocuotas->total;
                    //}
                }

                $cronograma = select_cronograma(
                    $idtienda,
                    $credito->id,
                    $credito->idforma_credito,
                    $credito->modalidad_calculo,
                    $credito->cuotas,
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

                $credito_cobranzacuota = DB::table('credito_cobranzacuota')
                    ->orderBy('credito_cobranzacuota.codigo','desc')
                    ->limit(1)
                    ->first();
                $codigo = 1;
                if($credito_cobranzacuota!=''){
                    $codigo = $credito_cobranzacuota->codigo+1;
                }

                //$bancoo = DB::table('banco')->where('banco.id',$request->idbanco!=null?$request->idbanco:0)->first();

                $banco = '';
                $cuenta = '';
                /*if($bancoo!=''){
                    $banco = $bancoo->nombre;
                    $cuenta = $bancoo->cuenta;
                }*/


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
                    'vuelto' => 0,
                    'numerooperacion' => '',
                    'banco' => $banco,
                    'cuenta' => $cuenta,

                    'proximo_vencimiento' => $cronograma['proximo_vencimiento'],
                    'saldo_pendientepago' => $cronograma['cuota_pendiente'],
                    'pago_cuota' => $pago_cuota,
                    'pago_diasatraso' => $pago_diasatraso,
                    'opcion_pago' => '',
                    'estadocargo' => '',
                    'cobrar_cargo' => '0.00',

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
                    'idcredito' => $credito->id,
                    'idcajero' =>  Auth::user()->id,
                    'idformapago' => 0,
                    'idbanco' => 0,
                    'idestadocredito_cobranzacuota' => 1,
                    'idestadoextorno' => 0, // 0 = sin extornar, 2 = extornado
                    'idtienda' => $idtienda,
                    'idestado' => 1,
                ]);

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
                  }
                  elseif($value['acuenta']>0){

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
                         'idcredito'            => $credito->id,
                         'idcredito_cronograma' => $value['id'],
                         'idcredito_cobranzacuota' => $idcredito_cobranzacuota,
                         'idestadocredito_adelanto'=> 1,
                         'idresponsable'        => Auth::user()->id,
                         'idtienda'             => $idtienda,
                         'idestado'             => 1,
                      ]);
                  }


                }


                // actualziar ultimo saldo
                $count_credito_cronograma = DB::table('credito_cronograma')
                    ->where('credito_cronograma.idcredito',$credito->id)
                    ->where('credito_cronograma.idestadocredito_cronograma',1)
                    ->count();

                $idestadocredito = 1;

                if($count_credito_cronograma==0){ // credito cancelado

                    DB::table('credito')
                      ->whereId($credito->id)
                      ->update([
                        'idcredito_cobranzacuota' => $idcredito_cobranzacuota,
                        'fecha_cancelado' => Carbon::now(),
                        'idestadocredito' => 2,
                    ]);

                    $idestadocredito = 2;
                }

                DB::table('credito_garantia')
                  ->where('credito_garantia.idcredito',$credito->id)
                  ->update([
                    'fechaentrega' => Carbon::now(),
                    'idestadoentrega' => 2,
                ]);

                $cronograma = select_cronograma(
                    $idtienda,
                    $credito->id,
                    $credito->idforma_credito,
                    $credito->modalidad_calculo,
                    $credito->cuotas,
                    0,
                    0,
                    0,
                    0,
                    0,
                    0,
                    0,
                    0,
                    1,
                    'detalle_cobranza'
                );

                DB::table('credito')
                  ->whereId($credito->id)
                  ->update([
                    'saldo_pendientepago' => $cronograma['cuota_pendiente'],
                ]);

                DB::table('credito_cobranzacuota')
                  ->whereId($idcredito_cobranzacuota)
                  ->update([
                        'saldo_pendientepago' => $cronograma['cuota_pendiente'],
                ]);
            }
            // FIN CANCELAR CREDITO
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha desembolsado correctamente.',
            ]);
        }
        
    
    }

    public function destroy(Request $request, $idtienda, $id)
    {
    }
}
