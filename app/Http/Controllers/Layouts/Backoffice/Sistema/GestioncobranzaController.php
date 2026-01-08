<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class GestioncobranzaController extends Controller
{
    public function __construct()
    {
        $this->modalidad_credito = DB::table('modalidad_credito')->get();
        $this->forma_credito = DB::table('forma_credito')->get();
        $this->tipo_operacion_credito = DB::table('tipo_operacion_credito')->get();
        $this->forma_pago_credito = DB::table('forma_pago_credito')->get();
        $this->tipo_destino_credito = DB::table('tipo_destino_credito')->get();
    }
    public function index(Request $request,$idtienda)
    {
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            
            $agencias = DB::table('tienda')->get();
          
            return view(sistema_view().'/gestioncobranza/tabla',[
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
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showtable'){
          $where = [];
          if($request->idagencia!=''){
              $where[] = ['credito.idtienda',$request->idagencia];
          }
          if($request->idasesor!=0){
              $where[] = ['credito.idasesor',$request->idasesor];
          }
          
          $creditos = DB::table('credito')
              ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
              ->join('users as cliente','cliente.id','credito.idcliente')
              ->join('ubigeo','ubigeo.id','cliente.idubigeo')
              ->leftjoin('users as cajero','cajero.id','credito.idcajero')
              ->leftjoin('users as asesor','asesor.id','credito.idasesor')
              ->leftjoin('users as administrador','administrador.id','credito.idadministrador')
              ->leftjoin('users as aval','aval.id','credito.idaval')
              ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
              ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
              ->where('credito.estado','DESEMBOLSADO')
              ->where('credito.idestadocredito',1)
              ->where($where)
              ->select(
                  'credito.*',
                  'cliente.identificacion as identificacioncliente',
                  'cliente.nombrecompleto as nombrecliente',
                  'cliente.numerotelefono as telefonocliente',
                  'cliente.direccion as direccioncliente',
                  'aval.identificacion as identificacionaval',
                  'aval.nombrecompleto as nombreaval',
                  'credito_prendatario.nombre as nombreproductocredito' ,
                  'credito_prendatario.modalidad as modalidadproductocredito',
                  'modalidad_credito.nombre as nombremodalidadcredito' ,
                  'forma_pago_credito.nombre as frecuencianombre' ,
                  'cajero.usuario as codigocajero',
                  'asesor.codigo as codigoasesor',
                  'administrador.nombrecompleto as nombreadministrador',
                  'ubigeo.nombre as ubigeonombre',
              )
              ->orderBy('credito.fecha_desembolso','asc')
              ->get();
          
          $dias_tolerancia = configuracion($request->idagencia,'dias_tolerancia_garantia')['valor'];
          $html = '';
          $data = [];
          
          foreach($creditos as $key => $value){
            
              // descuento cuota
              $credito_descuentocuotas = DB::table('credito_descuentocuota')
                    ->where('credito_descuentocuota.idcredito',$value->id)
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
                  $value->idtienda,
                  $value->id,
                  $value->idforma_credito,
                  $value->modalidadproductocredito,
                  $value->cuotas,
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
            
              if(($request->dias_retencion_desde<=$cronograma['ultimo_atraso'] or $request->dias_retencion_desde=='') && 
                ($request->dias_retencion_hasta>=$cronograma['ultimo_atraso'] or $request->dias_retencion_hasta=='') &&
                $cronograma['ultimo_atraso']>=0){
                  
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
                
                  $clasificacion = '';

                  if($cronograma['ultimo_atraso']<=8){
                      $clasificacion = 'NORMAL';
                  }
                  elseif($cronograma['ultimo_atraso']>8 && $cronograma['ultimo_atraso']<=30){
                      $clasificacion = 'CPP';
                  }
                  elseif($cronograma['ultimo_atraso']>30 && $cronograma['ultimo_atraso']<=60){
                      $clasificacion = 'DIFICIENTE';
                  }
                  elseif($cronograma['ultimo_atraso']>60 && $cronograma['ultimo_atraso']<=120){
                      $clasificacion = 'DUDOSO';
                  }
                  elseif($cronograma['ultimo_atraso']>120){
                      $clasificacion = 'PÉRDIDA';
                  }
                
                  $color_estado = '';

                  if($cronograma['ultimo_atraso']>0 && $cronograma['ultimo_atraso']<=$dias_tolerancia){
                      $color_estado = 'background-color:#b6e084;';
                  }
                  elseif($cronograma['ultimo_atraso']>$dias_tolerancia){
                      $color_estado = 'background-color:#ff9d9d;';
                  }
                  elseif($cronograma['ultimo_atraso']==0){
                      $color_estado = 'background-color:#fff;';
                  }

                  $credito_compromisopago = DB::table('credito_compromisopago')
                      ->where('idcredito',$value->id)
                      ->orderBy('id','desc')
                      ->limit(1)
                      ->first();

                  if($credito_compromisopago!=''){
                      if($credito_compromisopago->fechacompromiso<=Carbon::now()->format('Y-m-d')){
                            $color_estado = 'background-color:#ffb549;';
                      }else{
                            $color_estado = 'background-color:#f86b6b;';
                      } 
                  }
                
                  //  adelanto
                  $credito_cobranzacuotas = DB::table('credito_cobranzacuota')
                      ->where('credito_cobranzacuota.idcredito',$value->id)
                      ->get();

                  $totaladelanto = 0;
                  $ultimafechaadelanto = 0;
                  foreach($credito_cobranzacuotas as $valueade){
                      $totaladelanto = $valueade->total_pagar;
                      $ultimafechaadelanto = date_format(date_create($valueade->fecharegistro),'d-m-Y h:i:s A');
                  }

                  $fechacobranza_fecharegistro = '';
                  if($totaladelanto>0){
                      $fechacobranza_fecharegistro = $ultimafechaadelanto;
                  }
                  // fin adelanto
                
                  $credito_garantia = DB::table('credito_garantia')
                      ->where('credito_garantia.idcredito',$value->id)
                      ->where('credito_garantia.estado_listagarantia',1)
                      ->count();

                  $data[] = [
                      'id' => $value->id,
                      'estado' => $value->estado,
                      'key' => ($key+1),
                      'gp' => $credito_garantia>0?'R':'--',
                      'cuenta' => $value->cuenta,
                      'identificacioncliente' => $value->identificacioncliente,
                      'nombrecliente' => $value->nombrecliente,
                      'identificacionaval' => $value->identificacionaval,
                      'nombreaval' => $value->nombreaval,
                      'fecha_desembolso' => date_format(date_create($value->fecha_desembolso),'d-m-Y H:i:s A'),
                      'monto_solicitado' => $value->monto_solicitado,
                      'saldo_pendientepago' => $value->saldo_pendientepago,
                      'cuota_vencida' => $cronograma['cuota_vencida'],
                      'frecuencianombre' => $value->frecuencianombre,
                      'cuotas' => $cronograma['numero_cuota_vencida'],
                      'cp' => $cp,
                      'ultimo_atraso' => $cronograma['ultimo_atraso'],
                      'clasificacion' => $clasificacion,
                      'nombreproductocredito' => $value->nombreproductocredito,
                      'nombremodalidadcredito' => $value->nombremodalidadcredito,
                      'telefonocliente' => $value->telefonocliente,
                      'direccioncliente' => $value->direccioncliente.', '.$value->ubigeonombre,
                    
                      //'total_pendientepago' => $value->total_pendientepago,
                      'fechacompromiso' => ($credito_compromisopago!=''?date_format(date_create($credito_compromisopago->fechacompromiso),'d-m-Y'):''),
                      'comentario' => ($credito_compromisopago!=''?$credito_compromisopago->comentario:''),
                      //'fechacobranza_fecharegistro' => $fechacobranza_fecharegistro,
                      //'fecha_ultimopago' => date_format(date_create($value->fecha_ultimopago),'d-m-Y'),
                      'codigoasesor' => $value->codigoasesor,
                      'color_estado' => $color_estado,
                  ];
                                              
              }

                  
          }
            
          $creditos_ordenado = sistema_order_array($data, 'ultimo_atraso',SORT_DESC);
          $total_monto_solicitado = 0;
          $total_saldo_pendientepago = 0;
          
          foreach($creditos_ordenado as $key => $value){

              $html .= "<tr id='show_data_select' idcredito='{$value['id']}' estado='{$value['estado']}'>
                  <td style='".$value['color_estado']."white-space: nowrap;'>
                    <div class='dropdown' id='menu-opcion'>
                      <button class='btn btn-primary dropdown-toggle'  type='button' data-bs-toggle='dropdown' aria-expanded='false'>Opción</button>
                      <ul class='dropdown-menu dropdown-menu-end'>
                        <li>
                          <a class='dropdown-item' href='javascript:;' estadocuenta-valor-columna='{$value['id']}' onclick='show_estadocuenta(this)'>
                            <i class='fa fa-check'></i> Estado de Cuenta / Historial
                          </a>
                          <a class='dropdown-item' href='javascript:;' data-valor-columna='{$value['id']}' onclick='show_data(this)'>
                            <i class='fa fa-check'></i> Compromiso de Pago
                          </a>
                          <a class='dropdown-item' href='javascript:;' notificacion-valor-columna='{$value['id']}' onclick='show_notificacion(this)'>
                            <i class='fa fa-check'></i> Notificación
                          </a>
                        </li>
                      </ul>
                    </div>
                  </td>
                  <td style='".$value['color_estado']."white-space: nowrap;'>".($key+1)."</td>
                  <td style='".$value['color_estado']."white-space: nowrap;'>{$value['gp']}</td>
                  <td style='".$value['color_estado']."white-space: nowrap;'>C{$value['cuenta']}</td>
                  <td style='".$value['color_estado']."white-space: nowrap;'>{$value['identificacioncliente']}</td>
                  <td style='".$value['color_estado']."white-space: nowrap;'>{$value['nombrecliente']}</td>
                  <td style='".$value['color_estado']."white-space: nowrap;'>{$value['fecha_desembolso']}</td>
                  <td style='".$value['color_estado']."white-space: nowrap;text-align: right;'>{$value['monto_solicitado']}</td>
                  <td style='".$value['color_estado']."white-space: nowrap;'>{$value['frecuencianombre']}</td>
                  <td style='".$value['color_estado']."white-space: nowrap;text-align: right;'>{$value['cuota_vencida']}</td>
                  <td style='".$value['color_estado']."white-space: nowrap;text-align: right;'>".$value['ultimo_atraso']."</td>
                  <td style='".$value['color_estado']."white-space: nowrap;'>".$value['cp']."</td>
                  <td style='".$value['color_estado']."white-space: nowrap;text-align: right;'>".$value['cuotas']."</td>
                  <td style='".$value['color_estado']."white-space: nowrap;'>{$value['telefonocliente']}</td>
                  <td style='".$value['color_estado']."white-space: nowrap;'>".$value['fechacompromiso']."</td>
                  <td style='".$value['color_estado']."white-space: nowrap;'>".$value['comentario']."</td>
                  <td style='".$value['color_estado']."white-space: nowrap;'>{$value['direccioncliente']}</td>
                  <td style='".$value['color_estado']."white-space: nowrap;'>{$value['clasificacion']}</td>
                  <td style='".$value['color_estado']."white-space: nowrap;'>{$value['nombreproductocredito']}</td>
                  <td style='".$value['color_estado']."white-space: nowrap;'>{$value['nombremodalidadcredito']}</td>
                  <td style='".$value['color_estado']."white-space: nowrap;'>{$value['identificacionaval']}</td>
                  <td style='".$value['color_estado']."white-space: nowrap;'>{$value['nombreaval']}</td>
                  <td style='".$value['color_estado']."white-space: nowrap;'>{$value['codigoasesor']}</td>

              </tr>"; 
            
              $total_monto_solicitado = $total_monto_solicitado+$value['monto_solicitado'];
              $total_saldo_pendientepago = $total_saldo_pendientepago+$value['cuota_vencida'];
          }
              $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="7" style="background-color: #144081 !important;text-align:right;color:#fff !important;">TOTAL S/.</td>
                  <td style="background-color: #144081 !important;text-align:right;color:#fff !important;">'.number_format($total_monto_solicitado, 2, '.', '').'</td>
                  <td style="background-color: #144081 !important;"></td>
                  <td style="background-color: #144081 !important;text-align:right;color:#fff !important;">'.number_format($total_saldo_pendientepago, 2, '.', '').'</td>
                  <td colspan="14" style="background-color: #144081 !important;"></td>
                </tr>';
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
                    ->leftjoin('ubigeo as ubigeocliente','ubigeocliente.id','cliente.idubigeo')
                    ->leftjoin('users as aval','aval.id','credito.idaval')
                    ->leftjoin('ubigeo as ubigeoaval','ubigeoaval.id','aval.idubigeo')
                    ->join('forma_credito','forma_credito.id','credito.idforma_credito')
                    ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
                    ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
                    ->join('tipo_destino_credito','tipo_destino_credito.id','credito.idtipo_destino_credito')
                    ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
                    ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                    ->leftjoin('tipo_credito','tipo_credito.id','credito_prendatario.idtipo_credito')
                    ->where('credito.id',$id)
                    ->select(
                        'credito.*',
                        'cliente.codigo as codigo_cliente',
                        'cliente.identificacion as docuementocliente',
                        'cliente.nombrecompleto as nombreclientecredito',
                        'cliente.direccion as direccioncliente',
                        'ubigeocliente.distrito as distritoubigeocliente',
                        'ubigeocliente.provincia as provinciaubigeocliente',
                        'ubigeocliente.departamento as departamentoubigeocliente',
                        'aval.identificacion as documentoaval',
                        'aval.nombrecompleto as nombreavalcredito',
                        'aval.direccion as direccionaval',
                        'ubigeoaval.distrito as distritoubigeoaval',
                        'ubigeoaval.provincia as provinciaubigeoaval',
                        'ubigeoaval.departamento as departamentoubigeoaval',
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

        
        if( $request->input('view') == 'compromisopago' ){
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
            $users_prestamo = DB::table('s_users_prestamo')->where('s_users_prestamo.id_s_users',$credito->idcliente)->first();
          return view(sistema_view().'/gestioncobranza/compromisopago',[
            'tienda' => $tienda,
            'credito' => $credito,
            'usuario' => $usuario,
            'users_prestamo' => $users_prestamo,
          ]);
        }
        elseif( $request->input('view') == 'estadocuenta' ){
          return view(sistema_view().'/gestioncobranza/estadocuenta',[
            'tienda' => $tienda,
            'credito' => $credito,
          ]);
        }
        elseif( $request->input('view') == 'notificacion' ){
          return view(sistema_view().'/gestioncobranza/notificacion',[
            'tienda' => $tienda,
            'credito' => $credito,
          ]);
        }
      elseif($request->input('view') == 'notificacion_pdf'){
          $ubigeo_tienda = DB::table('ubigeo')->where('ubigeo.id',$tienda->idubigeo)->first();
          $pdf = PDF::loadView(sistema_view().'/gestioncobranza/notificacion_pdf',[
              'tienda' => $tienda,
              'credito' => $credito,
              'ubigeo_tienda' => $ubigeo_tienda,
          ]); 
          //$pdf->setPaper('A4', 'landscape');
          return $pdf->stream('ESTADO_DE_CUENTA.pdf');
      }
        else if($request->input('view') == 'exportar') {
            return view(sistema_view().'/gestioncobranza/exportar',[
                'tienda' => $tienda,
                'dias_retencion_desde' => $request->dias_retencion_desde,
                'dias_retencion_hasta' => $request->dias_retencion_hasta,
                'idagencia' => $request->idagencia,
                'idasesor' => $request->idasesor,
            ]);
        }
        else if( $request->input('view') == 'exportar_pdf' ){
              
          $where = [];
          if($request->idagencia!=''){
              $where[] = ['credito.idtienda',$request->idagencia];
          }
          
          $creditos = DB::table('credito')
              ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
              ->join('users as cliente','cliente.id','credito.idcliente')
              ->join('ubigeo','ubigeo.id','cliente.idubigeo')
              ->leftjoin('users as cajero','cajero.id','credito.idcajero')
              ->leftjoin('users as asesor','asesor.id','credito.idasesor')
              ->leftjoin('users as administrador','administrador.id','credito.idadministrador')
              ->leftjoin('users as aval','aval.id','credito.idaval')
              ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
              ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
              ->where('credito.estado','DESEMBOLSADO')
              ->where('credito.idestadocredito',1)
              ->where($where)
              ->select(
                  'credito.*',
                  'cliente.identificacion as identificacioncliente',
                  'cliente.nombrecompleto as nombrecliente',
                  'cliente.numerotelefono as telefonocliente',
                  'cliente.direccion as direccioncliente',
                  'aval.identificacion as identificacionaval',
                  'aval.nombrecompleto as nombreaval',
                  'credito_prendatario.nombre as nombreproductocredito' ,
                  'credito_prendatario.modalidad as modalidadproductocredito',
                  'modalidad_credito.nombre as nombremodalidadcredito' ,
                  'forma_pago_credito.nombre as frecuencianombre' ,
                  'cajero.usuario as codigocajero',
                  'asesor.codigo as codigoasesor',
                  'administrador.nombrecompleto as nombreadministrador',
                  'ubigeo.nombre as ubigeonombre',
              )
              ->orderBy('credito.fecha_desembolso','asc')
              ->get();
          
          $dias_tolerancia = configuracion($request->idagencia,'dias_tolerancia_garantia')['valor'];
          $html = '';
          $data = [];
          
          foreach($creditos as $key => $value){
            
              // descuento cuota
              $credito_descuentocuotas = DB::table('credito_descuentocuota')
                    ->where('credito_descuentocuota.idcredito',$value->id)
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
                  $value->idtienda,
                  $value->id,
                  $value->idforma_credito,
                  $value->modalidadproductocredito,
                  $value->cuotas,
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
            
              if(($request->dias_retencion_desde<=$cronograma['ultimo_atraso'] or $request->dias_retencion_desde=='') && 
                ($request->dias_retencion_hasta>=$cronograma['ultimo_atraso'] or $request->dias_retencion_hasta=='' &&
                $cronograma['ultimo_atraso']>=0)){
                  
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
                
                  $clasificacion = '';

                  if($cronograma['ultimo_atraso']<=8){
                      $clasificacion = 'NORMAL';
                  }
                  elseif($cronograma['ultimo_atraso']>8 && $cronograma['ultimo_atraso']<=30){
                      $clasificacion = 'CPP';
                  }
                  elseif($cronograma['ultimo_atraso']>30 && $cronograma['ultimo_atraso']<=60){
                      $clasificacion = 'DIFICIENTE';
                  }
                  elseif($cronograma['ultimo_atraso']>60 && $cronograma['ultimo_atraso']<=120){
                      $clasificacion = 'DUDOSO';
                  }
                  elseif($cronograma['ultimo_atraso']>120){
                      $clasificacion = 'PÉRDIDA';
                  }
                
                  $color_estado = '';

                  if($cronograma['ultimo_atraso']>0 && $cronograma['ultimo_atraso']<=$dias_tolerancia){
                      $color_estado = 'background-color:#44d24b;';
                  }
                  elseif($cronograma['ultimo_atraso']>$dias_tolerancia){
                      $color_estado = 'background-color:#ff9d9d;';
                  }
                  elseif($cronograma['ultimo_atraso']==0){
                      $color_estado = 'background-color:#fff;';
                  }

                  $credito_compromisopago = DB::table('credito_compromisopago')
                      ->where('idcredito',$value->id)
                      ->first();

                  if($credito_compromisopago!=''){
                      if($credito_compromisopago->fechacompromiso<=Carbon::now()->format('Y-m-d')){
                            $color_estado = 'background-color:#ffb549;';
                      }else{
                            $color_estado = 'background-color:#b6e084;';
                      } 
                  }
                
                  //  adelanto
                  $credito_cobranzacuotas = DB::table('credito_cobranzacuota')
                      ->where('credito_cobranzacuota.idcredito',$value->id)
                      ->get();

                  $totaladelanto = 0;
                  $ultimafechaadelanto = 0;
                  foreach($credito_cobranzacuotas as $valueade){
                      $totaladelanto = $valueade->total_pagar;
                      $ultimafechaadelanto = date_format(date_create($valueade->fecharegistro),'d-m-Y h:i:s A');
                  }

                  $fechacobranza_fecharegistro = '';
                  if($totaladelanto>0){
                      $fechacobranza_fecharegistro = $ultimafechaadelanto;
                  }
                  // fin adelanto

                  $data[] = [
                      'id' => $value->id,
                      'estado' => $value->estado,
                      'key' => ($key+1),
                      'cuenta' => $value->cuenta,
                      'identificacioncliente' => $value->identificacioncliente,
                      'nombrecliente' => $value->nombrecliente,
                      'identificacionaval' => $value->identificacionaval,
                      'nombreaval' => $value->nombreaval,
                      'fecha_desembolso' => date_format(date_create($value->fecha_desembolso),'d-m-Y H:i:s A'),
                      'monto_solicitado' => $value->monto_solicitado,
                      'saldo_pendientepago' => $value->saldo_pendientepago,
                      'cuota_vencida' => $cronograma['cuota_vencida'],
                      'frecuencianombre' => $value->frecuencianombre,
                      'cuotas' => $cronograma['numero_cuota_vencida'],
                      'cp' => $cp,
                      'ultimo_atraso' => $cronograma['ultimo_atraso'],
                      'clasificacion' => $clasificacion,
                      'nombreproductocredito' => $value->nombreproductocredito,
                      'nombremodalidadcredito' => $value->nombremodalidadcredito,
                      'telefonocliente' => $value->telefonocliente,
                      'direccioncliente' => $value->direccioncliente.', '.$value->ubigeonombre,
                    
                      //'total_pendientepago' => $value->total_pendientepago,
                      'fechacompromiso' => ($credito_compromisopago!=''?date_format(date_create($credito_compromisopago->fechacompromiso),'d-m-Y'):''),
                      'comentario' => ($credito_compromisopago!=''?$credito_compromisopago->comentario:''),
                      //'fechacobranza_fecharegistro' => $fechacobranza_fecharegistro,
                      //'fecha_ultimopago' => date_format(date_create($value->fecha_ultimopago),'d-m-Y'),
                      'codigoasesor' => $value->codigoasesor,
                      'color_estado' => $color_estado,
                  ];
                                              
              }

                  
          }
            
          $creditos_ordenado = sistema_order_array($data, 'ultimo_atraso',SORT_DESC);

            $agencia = DB::table('tienda')->whereId($request->idagencia)->first();
            $asesor = DB::table('users')->whereId($request->idasesor)->first();
        
            $pdf = PDF::loadView(sistema_view().'/gestioncobranza/exportar_pdf',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'asesor' => $asesor,
                'creditos' => $creditos,
                'dias_retencion_desde' => $request->dias_retencion_desde,
                'dias_retencion_hasta' => $request->dias_retencion_hasta,
                'creditos_ordenado' => $creditos_ordenado,
                'idformacredito' => $request->idformacredito,
            ]); 
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('GESTION_COBRANZA.pdf');
        } 
    }

    public function update(Request $request, $idtienda, $id)
    {
        if($request->input('view') == 'gestioncobranza') {
          
            $rules = [
                'fecha_compromiso' => 'required',                
                'comentario' => 'required',                         
            ];
          
            $messages = [
                'fecha_compromiso.required' => 'El Campo es Obligatorio.',
                'comentario.required' => 'El Campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('credito_compromisopago')->insertGetId([
                'fechacompromiso'         => $request->input('fecha_compromiso'),
                'comentario'              => $request->input('comentario'),
                'idcredito'               => $id,
                'idestadocompromisopago'  => 1,
                'idtienda'                => user_permiso()->idtienda,
                'idestado'                => 1,
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    
    }

    public function destroy(Request $request, $idtienda, $id)
    {
    }
}
