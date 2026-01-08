<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class PagoprestamoController extends Controller
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
          
            return view(sistema_view().'/pagoprestamo/tabla',[
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

        if($id == 'showtable'){
          $where = [];
          $where2 = [];
          if($request->idagencia!=''){
              $where[] = ['credito.idtienda',$request->idagencia];
              $where2[] = ['credito.idtienda',$request->idagencia];
          }
          if($request->idcliente!=''){
              $where[] = ['credito.idcliente',$request->idcliente];
              $where2[] = ['credito.idcliente',$request->idcliente];
          }
          if($request->idasesor!=''){
              $where[] = ['credito.idasesor',$request->idasesor];
              $where2[] = ['credito.idcajero',$request->idasesor];
          }
          $where[] = ['credito_cobranzacuota.fecharegistro','>=',$request->inicio.' 00:00:00'];
          $where[] = ['credito_cobranzacuota.fecharegistro','<=',$request->fin.' 23:59:59'];
          $where2[] = ['credito_cobranzacuota.fecharegistro','>=',$request->inicio.' 00:00:00'];
          $where2[] = ['credito_cobranzacuota.fecharegistro','<=',$request->fin.' 23:59:59'];
          
          $credito_cobranzacuotas = DB::table('credito_cobranzacuota')
              ->join('credito','credito.id','credito_cobranzacuota.idcredito')
              ->join('users as cliente','cliente.id','credito.idcliente')
              ->leftjoin('users as cajero','cajero.id','credito_cobranzacuota.idcajero')
              ->join('ubigeo','ubigeo.id','cliente.idubigeo')
              ->where('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
              ->where('credito_cobranzacuota.idestadoextorno',0)
              ->where($where)
              ->orWhere('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
              ->where('credito_cobranzacuota.idestadoextorno',0)
              ->where($where2)
              ->select(
                  'credito_cobranzacuota.*',
            
                  'credito.cuenta as cuentacredito',
                  'credito.idmodalidad_credito as idmodalidad_credito',
                  'cliente.id as idcliente',
                  'cliente.nombrecompleto as nombrecliente',
                  'cliente.direccion as clientedireccion',
                  'cajero.codigo as usuariocajero',
                  'ubigeo.nombre as ubigeonombre',
              )
              ->orderBy('credito_cobranzacuota.id','asc')
              ->get();
          
          $html = '<table class="table table-hover table-bordered" id="table-lista-credito">
              <thead class="table-dark" style="position: sticky;top: 0;">
                <tr>
                  <td style="text-align:center">N°</td>
                  <td style="text-align:center">CLIENTE</td>
                  <td style="text-align:center">CUOTAS</td>
                  <td style="text-align:center">C. PAGADO</td>
                  <td style="text-align:center">ACUENTA</td>
                  <td style="text-align:center">INT. COM.</td>
                  <td style="text-align:center">INT. MORA.</td>
                  <td style="text-align:center">CUSTODIA&nbsp;</td>
                  <td style="text-align:center">CXC</td>
                  <td style="text-align:center">TOTAL (S/.)</td>
                  <td style="text-align:center">FECHA</td>
                  <td style="text-align:center">F/L. PAGO</td>
                  <td style="text-align:center">L. BANCO</td>
                  <td style="text-align:center">VALIDACIÓN</td>
                  <td style="text-align:center">N° OPERACIÓN</td>
                  <td style="text-align:center">RESPONSABLE</td>
                </tr>
              </thead>
              <tbody>';
              
              
          $total_amortizacion = 0;
          $total_acuenta = 0;
          $total_penalidad = 0;
          $total_compensatorio = 0;
          $total_tenencia = 0;
          $cobrar_cargo = 0;
          $total_totalcuota = 0;
      
          $total_extorno = 0;
          $total_caja = 0;
          $total_banco = 0;
          
          foreach($credito_cobranzacuotas as $key => $value){
            
              $credito_adelanto = DB::table('credito_adelanto')->where('credito_adelanto.idcredito_cobranzacuota',$value->id)->get();
              $credito_adelanto = DB::table('credito_adelanto')->where('credito_adelanto.idcredito_cobranzacuota',$value->id)->get();
              
              $t_acuenta = 0;
            
              foreach($credito_adelanto as $valueadelanto){
                  $credito_cronograma = DB::table('credito_cronograma')->where('credito_cronograma.id',$valueadelanto->idcredito_cronograma)->first();
                  if($credito_cronograma){
                      if($credito_cronograma->idestadocredito_cronograma==2){
                      }else{
                          if($t_cuotapagado>0){
                              $t_acuenta = $t_acuenta+$valueadelanto->total;
                          }else{
                              $t_acuenta = $t_acuenta+$valueadelanto->capital+$valueadelanto->comision+$valueadelanto->cargo+$valueadelanto->interes;
                          }
                      }
                  }
              }
              $t_acuenta = number_format($t_acuenta, 2, '.', '');
              $operacionen1 = '';
              if($value->idformapago==0){ $operacionen1 = 'TRANSITORIO'; }
              if($value->idformapago==1){ $operacionen1 = 'CAJA'; }
              if($value->idformapago==2){ $operacionen1 = 'BANCO'; }
            
              $cuotas = str_replace(',',', ',$value->pago_cuota);
              $num_operacion =  'OP'.str_pad($value->codigo, 10, "0", STR_PAD_LEFT);
            
              $btn_validar = '';
              if($value->idformapago==2){
                  $btn_validar = "<button type='button' class='btn btn-success' onclick='validar({$value->id})'><i class='fa-solid fa-check'></i> Validar</button>";
                  if($value->validar_estado==1){
                      $users = DB::table('users')->whereId($value->validar_responsable)->first();
                      $btn_validar = "<i class='fa-solid fa-check'></i> (".$users->codigo.")";
                  }
              }  
            
              $html .= "<tr id='show_data_select' idcredito_cobranzacuota='{$value->id}'>
                            <td style='height: 20px;'>".($key+1)."</td>
                            <td style='height: 20px;'>{$value->nombrecliente}</td>
                            <td style='height: 20px;'>{$cuotas}</td>
                            <td style='text-align:right;height: 20px;'>{$value->total_amortizacion}</td>
                            <td style='text-align:right;height: 20px;'>{$t_acuenta}</td>
                            <td style='text-align:right;height: 20px;'>{$value->total_penalidad}</td>
                            <td style='text-align:right;height: 20px;'>{$value->total_compensatorio}</td>
                            <td style='text-align:right;height: 20px;'>{$value->total_tenencia}</td>
                            <td style='text-align:right;height: 20px;'>{$value->cobrar_cargo}</td>
                            <td style='text-align:right;height: 20px;'>{$value->total_totalcuota}</td>
                            <td style='text-align:center;height: 20px;width: 125px;'>{$value->fecharegistro}</td>
                            <td style='height: 20px;'>{$operacionen1}</td>
                            <td style='height: 20px;'>{$value->banco}</td>
                            <td style='width: 100px;'>{$btn_validar}</td>
                            <td style='height: 20px;'>{$num_operacion}</td>
                            <td style='height: 20px;'>{$value->usuariocajero}</td>
                        </tr>";
                        
                    
              $total_amortizacion += $value->total_amortizacion;
              $total_acuenta += $t_acuenta;
              $total_penalidad += $value->total_penalidad;
              $total_compensatorio += $value->total_compensatorio;
              $total_tenencia += $value->total_tenencia;
              $cobrar_cargo += $value->cobrar_cargo;
              $total_totalcuota += $value->total_totalcuota;
            
              if($value->idformapago==0){
                  $total_extorno = $total_extorno+$total_totalcuota;
              }
              if($value->idformapago==1){
                  $total_caja = $total_caja+$total_totalcuota;
              }
              if($value->idformapago==2){
                  $total_banco = $total_banco+$total_totalcuota;
              }
          }
          if(count($credito_cobranzacuotas)==0){
              $html.= '<tr><td colspan="14" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }
              $html .= '</tbody><tfoot class="table-dark" style="position: sticky;bottom: 0;">
                <tr>
                  <td colspan="3" style="text-align:right">TOTAL S/.</td>
                  <td style="text-align:right">'.number_format($total_amortizacion, 2, '.', '').'</td>
                  <td style="text-align:right">'.number_format($total_acuenta, 2, '.', '').'</td>
                  <td style="text-align:right">'.number_format($total_penalidad, 2, '.', '').'</td>
                  <td style="text-align:right">'.number_format($total_compensatorio, 2, '.', '').'</td>
                  <td style="text-align:right">'.number_format($total_tenencia, 2, '.', '').'</td>
                  <td style="text-align:right">'.number_format($cobrar_cargo, 2, '.', '').'</td>
                  <td style="text-align:right">'.number_format($total_totalcuota, 2, '.', '').'</td>
                  <td colspan="6"></td>
                </tr>
                <tr>
                  <td colspan="3" style="background-color: #198754 !important;text-align:right">RESUMEN:</td>
                  <td style="background-color: #198754 !important;text-align:right;width:70px;">CAJA</td>
                  <td style="background-color: #198754 !important;text-align:right;width:70px;">'.number_format($total_caja, 2, '.', '').'</td>
                  <td style="background-color: #198754 !important;text-align:right;width:70px;">BANCO</td>
                  <td style="background-color: #198754 !important;text-align:right;width:70px;">'.number_format($total_banco, 2, '.', '').'</td>
                  <td style="background-color: #198754 !important;text-align:right;width:70px;">TRANSIT.</td>
                  <td style="background-color: #198754 !important;text-align:right;width:70px;">'.number_format($total_extorno, 2, '.', '').'</td>
                  <td style="background-color: #198754 !important;text-align:right;width:70px;">T. EFE. (S/.)</td>
                  <td style="background-color: #198754 !important;text-align:right;width:70px;">'.number_format($total_caja+$total_banco, 2, '.', '').'</td>
                  <td colspan="5"  style="background-color: #198754 !important;"></td>
                </tr>
              </tfoot>
            </table>';
          return array(
            'html' => $html,
            'html1' => ''
          );
          
        }

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
                  'credito.cuotas as cuotas',
              )
              ->first();
                
        if($request->input('view') == 'ticket') {
            return view(sistema_view().'/pagoprestamo/ticket',[
              'tienda' => $tienda,
              'credito_cobranzacuota' => $credito_cobranzacuota,
            ]);
        }
        else if( $request->input('view') == 'pdf_pago' ){
              
            $credito = DB::table('credito')
              ->where('credito.id',$credito_cobranzacuota->idcredito)
              ->first();
          
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
          
            
            $count_credito_cronograma = DB::table('credito_cronograma')
                ->where('credito_cronograma.idcredito',$credito_cobranzacuota->idcredito)
                ->where('credito_cronograma.idestadocredito_cronograma',1)
                ->count();
          
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
                'total_pendientepago' => $credito_cobranzacuota->total_pendientepago,
                'saldo_pendientepago' => $credito_cobranzacuota->saldo_pendientepago,
                'credito_cobranzacuota' => $credito_cobranzacuota,
                'count_creditopendiente' => $count_creditopendiente,
                'count_credito_cronograma' => $count_credito_cronograma,
                'credito' => $credito,
            ]); 
            $pdf->setPaper('A4');
            return $pdf->stream('VOUCHER_PAGO.pdf');
        }   
        else if($request->input('view') == 'ticket_garantia') {
            $count_creditopendiente = DB::table('credito_garantia')
                  ->where('credito_garantia.idcredito',$credito_cobranzacuota->idcredito)
                  ->where('credito_garantia.idestadoentrega',1)
                  ->count();
            return view(sistema_view().'/pagoprestamo/ticket_garantia',[
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
            return view(sistema_view().'/pagoprestamo/exportar',[
                'tienda' => $tienda,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'idagencia' => $request->idagencia,
                'idcliente' => $request->idcliente,
            ]);
        }
        else if( $request->input('view') == 'exportar_pdf' ){
              
            
          $where = [];
          $where2 = [];
          if($request->idagencia!=''){
              $where[] = ['credito.idtienda',$request->idagencia];
              $where2[] = ['credito.idtienda',$request->idagencia];
          }
          if($request->idcliente!=''){
              $where[] = ['credito.idcliente',$request->idcliente];
              $where2[] = ['credito.idcliente',$request->idcliente];
          }
          if($request->idasesor!=''){
              $where[] = ['credito.idasesor',$request->idasesor];
              $where2[] = ['credito.idcajero',$request->idasesor];
          }
          $where[] = ['credito_cobranzacuota.fecharegistro','>=',$request->fecha_inicio.' 00:00:00'];
          $where[] = ['credito_cobranzacuota.fecharegistro','<=',$request->fecha_fin.' 23:59:59'];
          $where2[] = ['credito_cobranzacuota.fecharegistro','>=',$request->fecha_inicio.' 00:00:00'];
          $where2[] = ['credito_cobranzacuota.fecharegistro','<=',$request->fecha_fin.' 23:59:59'];
          
          $credito_cobranzacuotas = DB::table('credito_cobranzacuota')
              ->join('credito','credito.id','credito_cobranzacuota.idcredito')
              ->join('users as cliente','cliente.id','credito.idcliente')
              ->leftjoin('users as cajero','cajero.id','credito_cobranzacuota.idcajero')
              ->join('ubigeo','ubigeo.id','cliente.idubigeo')
              ->where('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
              ->where('credito_cobranzacuota.idestadoextorno',0)
              ->where($where)
              ->orWhere('credito_cobranzacuota.idestadocredito_cobranzacuota',1)
              ->where('credito_cobranzacuota.idestadoextorno',0)
              ->where($where2)
              ->select(
                  'credito_cobranzacuota.*',
            
                  'credito.cuenta as cuentacredito',
                  'cliente.id as idcliente',
                  'cliente.nombrecompleto as nombrecliente',
                  'cliente.direccion as clientedireccion',
                  'ubigeo.nombre as ubigeonombre',
                  'cajero.codigo as usuariocajero',
              )
              ->orderBy('credito_cobranzacuota.id','asc')
              ->get();
          
          
            $agencia = DB::table('tienda')->whereId($request->idagencia)->first();
        
            $pdf = PDF::loadView(sistema_view().'/pagoprestamo/exportar_pdf',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'credito_cobranzacuotas' => $credito_cobranzacuotas,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
            ]); 
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('HISTORIAL_PAGOS_PRESTAMOS.pdf');
        }  
        else if($request->input('view') == 'extornar') {
          
            if($request->tipo == 'admin'){
                $usuarios = DB::table('users')
                    ->join('users_permiso','users_permiso.idusers','users.id')
                    ->join('permiso','permiso.id','users_permiso.idpermiso')
                    ->where('users_permiso.idpermiso',1)
                    ->select('users.*','permiso.nombre as nombrepermiso')
                    ->where('users_permiso.idtienda',$idtienda)
                    ->get();
            }else{
                $usuarios = DB::table('users')
                    ->join('users_permiso','users_permiso.idusers','users.id')
                    ->join('permiso','permiso.id','users_permiso.idpermiso')
                    ->where('users_permiso.idpermiso',2)
                    ->select('users.*','permiso.nombre as nombrepermiso')
                    ->where('users_permiso.idtienda',$idtienda)
                    ->get();
            }
            return view(sistema_view().'/pagoprestamo/extornar',[
              'tienda' => $tienda,
              'credito_cobranzacuota' => $credito_cobranzacuota,
              'usuarios' => $usuarios,
            ]);
        }
        
        elseif($request->input('view') == 'validar') {

            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1,4])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.id as idpermiso','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/pagoprestamo/validar',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
                'idcredito_cobranzacuota' => $id,
            ]);
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        
        if($request->input('view') == 'extornar'){
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
        
          if($credito_cobranzacuota->id_credito_ampliado!=0){
              return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje'   => 'No puede extornar esta cobranza, lo tiene que realizar desde eliminar desembolso!!.',
              ]);
          }
          //------
        
          //Restaurar Crédito
        
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
          //------------------------------
        
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
                      $acuenta = $total_adelanto-$value->acuenta; //3.20-3.20=0
                      $idestadocredito_cronograma = 1;
                      $idestadocronograma_pago = 0;
                  }else{
                      $acuenta = $value->acuenta-$total_adelanto; // 22.80-7.80=15
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
          DB::table('credito')
            ->whereId($credito_cobranzacuota->idcredito)
            ->update([
                'idestadocredito'  => 1,
          ]);
          // restaurar garantias
          DB::table('credito_garantia')
            ->where('credito_garantia.idcredito',$credito_cobranzacuota->idcredito)
            ->update([
              'idestadoentrega' => 1,
          ]);
        
          return response()->json([
              'resultado'           => 'CORRECTO',
              'mensaje'             => 'Se ha elimino correctamente.',
          ]);
        
      }
    
        elseif($request->input('view') == 'validar'){
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
            if($usuario!=''){
                $idresponsable = $usuario->id;
            }else{
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El usuario y/o la contraseña es incorrecta!!.'
                ]);
            }
          
            DB::table('credito_cobranzacuota')->whereId($id)->update([
                'validar_estado' => 1,
                'validar_responsable' => $request->idresponsable,
                'validar_responsable_permiso' => $request->idresponsable_permiso,
                'validar_fecha' => now(),
            ]);
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha validado correctamente.',
              'idresponsable'   => $idresponsable
            ]);
        }
    }

    public function destroy(Request $request, $idtienda, $id)
    {
      
    }
}
