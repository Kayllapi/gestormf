<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use DateTime;
use PDF;

class EstadocuentaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/estadocuenta/tabla',[
              'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->view == 'registrar') {
          
            $credito = DB::table('credito')
                ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                ->where('credito.id',$request->idcredito)
                ->select(
                    'credito.*',
                    'credito_prendatario.modalidad as modalidadproductocredito'
                )
                ->first();
            //dd($request->numerocuota);
            $cronograma = select_cronograma(
                $idtienda,
                $request->idcredito,
                $credito->idforma_credito,
                $credito->modalidadproductocredito,
                $request->numerocuota
            );
            
            
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->where('users_permiso.idpermiso',1)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
          
            return view(sistema_view().'/estadocuenta/create',[
                'tienda' => $tienda,
                'credito' => $credito,
                'cronograma' => $cronograma,
                'usuarios' => $usuarios,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'descuento_capital' => 'required',          
                'descuento_interes' => 'required',  
                'descuento_comision' => 'required',  
                'descuento_cargo' => 'required',               
                'descuento_penalidad' => 'required',                 
                'descuento_tenencia' => 'required',              
                'descuento_compensatorio' => 'required',             
                'descuento_total' => 'required',        
                'idresponsable' => 'required',          
                'responsableclave' => 'required',                 
            ];
          
            $messages = [
                'descuento_capital.required' => 'El "Capital" es Obligatorio.',
                'descuento_interes.required' => 'El "Interes de Garantia" es Obligatorio.',
                'descuento_comision.required' => 'El "Comisión" es Obligatorio.',
                'descuento_cargo.required' => 'El "Cargo" es Obligatorio.',
                'descuento_penalidad.required' => 'La "Penalidad" es Obligatorio.',
                'descuento_tenencia.required' => 'El "Tenencia" es Obligatorio.',
                'descuento_compensatorio.required' => 'El "Compensatorio" es Obligatorio.',
                'descuento_total.required' => 'El "Total" es Obligatorio.',
                'idresponsable.required' => 'El "Responsable" es Obligatorio.',
                'responsableclave.required' => 'La "Contraseña" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $valid = 0;
            if($request->data_capital<0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto Descuento de capital debe ser mayor ó igual a 0.00.'
                ]);
            }
            elseif($request->data_capital<$request->descuento_capital){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto Descuento de capital debe ser máximo '.$request->data_capital.'.'
                ]);
            }else{
                $valid = 1;
            }
            if($request->data_interes<0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto Descuento de interes debe ser mayor ó igual a 0.00.'
                ]);
            }
            elseif($request->data_interes<$request->descuento_interes){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto Descuento de interes debe ser máximo '.$request->data_interes.'.'
                ]);
            }else{
                $valid = 1;
            }
            if($request->data_comision<0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto Descuento de comisión debe ser mayor ó igual a 0.00.'
                ]);
            }
            elseif($request->data_comision<$request->descuento_comision){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto Descuento de comisión debe ser máximo '.$request->data_comision.'.'
                ]);
            }else{
                $valid = 1;
            }
            if($request->data_cargo<0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto Descuento de cargo debe ser mayor ó igual a 0.00.'
                ]);
            }
            elseif($request->data_cargo<$request->descuento_cargo){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto Descuento de cargo debe ser máximo '.$request->data_cargo.'.'
                ]);
            }else{
                $valid = 1;
            }
            if($request->data_penalidad<0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto Descuento de penalidada debe ser mayor ó igual a 0.00.'
                ]);
            }
            elseif($request->data_penalidad<$request->descuento_penalidad){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto Descuento de penalidada debe ser máximo '.$request->data_penalidad.'.'
                ]);
            }else{
                $valid = 1;
            }
            if($request->data_tenencia<0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto Descuento de tenencia debe ser mayor ó igual a 0.00.'
                ]);
            }
            elseif($request->data_tenencia<$request->descuento_tenencia){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto Descuento de tenencia debe ser máximo '.$request->data_tenencia.'.'
                ]);
            }else{
                $valid = 1;
            }
            if($request->data_compensatorio<0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto Descuento de Compensatorio debe ser mayor ó igual a 0.00.'
                ]);
            }
            elseif($request->data_compensatorio<$request->descuento_compensatorio){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Monto Descuento de Compensatorio debe ser máximo '.$request->data_compensatorio.'.'
                ]);
            }else{
                $valid = 1;
            }
          
            if($valid==0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Debe ingresar mínimo un descuento.'
                ]);
            }
            
            $credito_descuentocuotas = DB::table('credito_descuentocuota')
                ->where('credito_descuentocuota.idcredito',$request->idcredito)
                ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                ->first();
            if($credito_descuentocuotas){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Ya existe un descuento pendiente!!.'
                ]);
            }
            
            
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
            
            $credito_descuentocuota = DB::table('credito_descuentocuota')
                ->orderBy('credito_descuentocuota.codigo','desc')
                ->limit(1)
                ->first();
            $codigo = 1;
            if($credito_descuentocuota!=''){
                $codigo = $credito_descuentocuota->codigo+1;
            }
          
            DB::table('credito_descuentocuota')->insert([
               'fecharegistro'        => Carbon::now(),
               'codigo'               => $codigo,
               'numerocuota'          => $request->data_numerocuota,
               'numerocuota_inicio'   => $request->data_numerocuota_inicio,
               'numerocuota_fin'      => $request->data_numerocuota_fin,
               'capital'              => $request->descuento_capital,
               'interes'              => $request->descuento_interes,
               'comision'             => $request->descuento_comision,
               'cargo'                => $request->descuento_cargo,
               'penalidad'            => $request->descuento_penalidad,
               'tenencia'             => $request->descuento_tenencia,
               'compensatorio'        => $request->descuento_compensatorio,
               'total'                => $request->descuento_total,
               'idcredito'            => $request->idcredito,
               'idestadocredito_descuentocuota'=> 1,
               'idresponsable'        => $idresponsable,
               'idtienda'             => $idtienda,
               'idestado'             => 1,
            ]);

          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'show_credito'){
          /*$creditos = DB::table('credito')
                            ->join('users as cliente','cliente.id','credito.idcliente')
                            ->where('credito.estado','DESEMBOLSADO')
                            ->where('cliente.identificacion','LIKE','%'.$request->buscar.'%')
                            ->orWhere('credito.estado','DESEMBOLSADO')
                            ->where('cliente.nombrecompleto','LIKE','%'.$request->buscar.'%')
                            ->select(
                                'cliente.id as idcliente',
                                'cliente.identificacion as identificacion',
                                'cliente.nombrecompleto as nombrecliente',
                            )
                            ->distinct()
                            ->orderBy('credito.fecha_desembolso','asc')
                            ->get();*/
          
          $creditos = DB::table('users')
                            ->where('users.identificacion','LIKE','%'.$request->buscar.'%')
                            ->orWhere('users.nombrecompleto','LIKE','%'.$request->buscar.'%')
                            ->select(
                                'users.id as idcliente',
                                'users.identificacion as identificacion',
                                'users.nombrecompleto as nombrecliente',
                            )
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
          
          $s_listanegra = DB::table('s_listanegra')->where('idcliente',$request->idcliente)->first();
          $estado_listanegra = 1;
          if($s_listanegra){
              $estado_listanegra = 2;
          }
          $creditos = DB::table('credito')
                            ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
                            ->join('users as cliente','cliente.id','credito.idcliente')
                            ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
                            ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
                            ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                            ->where('credito.estado','DESEMBOLSADO')
                            ->where('cliente.id',$request->idcliente)
                            ->whereIn('credito.idestadocredito',[1,2])
                            ->select(
                                'credito.*',
                                'cliente.identificacion as identificacion',
                                'cliente.nombrecompleto as nombrecliente',
                            )
                            ->orderBy('credito.idestadocredito','asc')
                            ->orderBy('credito.fecha_desembolso','desc')
                            ->get();
          $html = '';
          foreach($creditos as $value){
              $estadocredito = 'PEND.';
              $color = '#E8E585';
              if($value->idestadocredito==2){
                  $estadocredito = 'CANC.';
                  $color = '';
              }
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
              $fechadesemobolso = date_format(date_create($value->fecha_desembolso),'d-m-Y h:i:s A');
              $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data(this)'>
                            <td style='text-align: right;width: 70px;'>S/ {$value->monto_solicitado}</td>
                            <td style='width: 20px;'>{$cp}</td>
                            <td style='width: 20px;'><span style='background-color: {$color};'>{$estadocredito}</span></td>
                            <td style='font-size: 11px;'>{$fechadesemobolso}</td>
                        </tr>";
          }
          if(count($creditos)==0){
              $html .= "<tr colspan='4'>
                            <td>Sin Créditos!!</td>
                        </tr>";
          }
          
          $ultimocredito_resumida = DB::table('credito')
              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
              ->where('credito.idcliente',$request->idcliente)
              ->where('credito.idevaluacion',1)
              //->whereIn('credito.idestadocredito',[1])
              ->where('credito.estado','DESEMBOLSADO')
              ->whereIn('credito.idestadocredito',[1,2])
              ->select(
                  'credito.*',
                  'credito_prendatario.conevaluacion as conevaluacion',
              )
              ->orderBy('credito.idestadocredito','asc')
              ->orderBy('credito.fecha_desembolso','desc')
              ->limit(1)
              ->first();
         
          $idultimocredito_resumida = 0;
          if($ultimocredito_resumida){
              if($ultimocredito_resumida->conevaluacion == 'SI'){
                  $idultimocredito_resumida = $ultimocredito_resumida->id;
              }
          }
          
          $ultimocredito_completa = DB::table('credito')
              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
              ->where('credito.idcliente',$request->idcliente)
              ->where('credito.idevaluacion',2)
              ->where('credito.estado','DESEMBOLSADO')
              ->whereIn('credito.idestadocredito',[1,2])
              ->select(
                  'credito.*',
                  'credito_prendatario.conevaluacion as conevaluacion',
              )
              ->orderBy('credito.idestadocredito','asc')
              ->orderBy('credito.fecha_desembolso','desc')
              ->limit(1)
              ->first();
         
          $idultimocredito_completa = 0;
          if($ultimocredito_completa){
              if($ultimocredito_completa->conevaluacion == 'SI'){
                  $idultimocredito_completa = $ultimocredito_completa->id;
              }
          }
          
          return array(
            'cliente' => $cliente,
            'idultimocredito_resumida' => $idultimocredito_resumida,
            'idultimocredito_completa' => $idultimocredito_completa,
            'html' => $html,
            'estado_listanegra' => $estado_listanegra
          );
          
        }
        else if($id == 'show_cronograma'){
   
          $credito = DB::table('credito')
              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
              ->where('credito.id',$request->idcredito)
              ->select(
                  'credito.*',
                  'credito_prendatario.modalidad as modalidadproductocredito',
              )
              ->first();
          
          $html = '<table class="table table-bordered" id="table-detalle-cronograma">
              <thead style="position: sticky;top: 0;">
              <tr>
                <th style="width:5px;"></th>
                <th width="10px">N° Cuota</th>
                <th>Fecha</th>
                <th>Capital</th>
                <th>Interes</th>
                <th>Servicios</th>
                <th>Cargo</th>
                <th>Cuota</th>
                <th><span style="background-color: #cd0909 !important;font-weight: bold;">Atraso</span></th>
                <th>Tenencia</th>
                <th>Penalidad</th>
                <th style="width:10px;">Interes Moratoria</th>
                <th>Cuota Total</th>
              </tr>
              </thead>
              <tbody>';
          
          
            // descuentos
            $credito_descuentocuotas = DB::table('credito_descuentocuota')
                ->where('credito_descuentocuota.idcredito',$request->idcredito)
                ->where('credito_descuentocuota.idestadocredito_descuentocuota',1)
                ->first();
        
          $cronograma = select_cronograma(
              $idtienda,
              $request->idcredito,
              $credito->idforma_credito,
              $credito->modalidadproductocredito,
              $request->numerocuota,
              /*0,
              $credito_descuentocuotas?$credito_descuentocuotas->interes:0,
              $credito_descuentocuotas?$credito_descuentocuotas->penalidad:0,
              $credito_descuentocuotas?$credito_descuentocuotas->tenencia:0,
              $credito_descuentocuotas?$credito_descuentocuotas->compensatorio:0*/
          );

          
          foreach($cronograma['cronograma'] as $value){
            
              $html .= '<tr class="'.$value['selected'].' '.$value['seleccionar'].'" 
                            data-id="'.$value['id'].'" 
                            data-numerocuota="'.$value['numerocuota'].'">
                            <td style="'.$value['style'].'">
                                <div class="form-check">
                                  <input style="font-size: 1rem;margin-left: -17px;height: 20px;width: 20px;" class="form-check-input" 
                                  type="checkbox" name="seleccionar_cuota" id="numerocuotaselect" 
                                  onclick="show_data_cronograma('.$value['numerocuota'].')" '.$value['checked'].' '.$value['disabled'].'>
                                </div>
                            </td>
                            <td style="'.$value['style'].'text-align:center">'.$value['numerocuota'].'</td>
                            <td style="'.$value['style'].'text-align:center">'.$value['fecha'].'</td>
                            <td style="'.$value['style'].'text-align:right">'.$value['amortizacion'].'</td>
                            <td style="'.$value['style'].'text-align:right">'.$value['interes'].'</td>
                            <td style="'.$value['style'].'text-align:right">'.$value['comision'].'</td>
                            <td style="'.$value['style'].'text-align:right">'.$value['cargo'].'</td>
                            <td style="'.$value['style'].'text-align:right">'.$value['cuota'].'</td>
                            <td style="'.$value['style'].'text-align:right"><span style="'.($value['atraso_dias']>0?'color: #ff4343 !important;':'').'font-weight: bold;">
                            '.$value['atraso_dias'].'</span></td></td>
                            <td style="'.$value['style'].'text-align:right">'.$value['tenencia'].'</td>
                            <td style="'.$value['style'].'text-align:right">'.$value['penalidad'].'</td>
                            <td style="'.$value['style'].'text-align:right">'.$value['compensatorio'].'</td>
                            <td style="'.$value['style'].'text-align:right;background-color: #efefef !important;">'.$value['totalcuota'].'</td>
                        </tr>';
            
          }
          $html .= '</tbody>
              <thead style="position: sticky;top: 0;">
              <tr>
                <th></th>
                <th></th>
                <th></th>
                <th style="text-align:right">'.$cronograma['total_amortizacion'].'</th>
                <th style="text-align:right">'.$cronograma['total_interes'].'</th>
                <th style="text-align:right">'.$cronograma['total_comision'].'</th>
                <th style="text-align:right">'.$cronograma['total_cargo'].'</th>
                <th style="text-align:right">'.$cronograma['total_cuota'].'</th>
                <th></th>
                <th style="text-align:right">'.$cronograma['total_penalidad'].'</th>
                <th style="text-align:right">'.$cronograma['total_tenencia'].'</th>
                <th style="text-align:right">'.$cronograma['total_compensatorio'].'</th>
                <th style="text-align:right">'.$cronograma['total_totalcuota'].'</th>
              </tr>
              </thead>
              </table>';
          
          return array(
              'html' => $html,
              'select_ultimacuotacancelada' => $cronograma['select_ultimacuotacancelada'],
          );
          
        }
        else if($id == 'show_descuentodecuotas'){
            
            $where = [];
            if($request->idestado==1){
                $where[] = ['credito_descuentocuota.idestadocredito_descuentocuota',1];
            }
          $credito_descuentocuotas = DB::table('credito_descuentocuota')
                ->where('credito_descuentocuota.idcredito',$request->idcredito)
                ->where($where)
                ->orderBy('credito_descuentocuota.numerocuota','asc')
                ->get();
          
          $html = '<table class="table table-bordered" id="table-detalle-descuentodecuotas">
              <thead>
              <tr>
                <th style="width:100px;">Fecha de Registro</th>
                <th style="width:5px;">N° Cuota</th>
                <th>Capital</th>
                <th>Interes</th>
                <th>Comisión</th>
                <th>Cargo</th>
                <th>Tenencia</th>
                <th>Penalidad</th>
                <th>Interes Moratoria</th>
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
          
          foreach($credito_descuentocuotas as $value){
               $fecharegistro = date_format(date_create($value->fecharegistro),'d-m-Y H:i:s A');
              $html .= "<tr data-valor-columna='{$value->id}' onclick='show_select_descuentodecuotas(this)'>
                            <td style='text-align:center'>{$fecharegistro}</td>
                            <td style='text-align:center'>{$value->numerocuota}</td>
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
                <th></th>
                <th></th>
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
                    ->join('users as cliente','cliente.id','credito.idcliente')
                    ->join('ubigeo as clienteubigeo','clienteubigeo.id','cliente.idubigeo')
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
                        'cliente.direccion as direccionclientecredito',
                        'clienteubigeo.nombre as clienteubigeonombre',
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
      
      if($request->input('view') == 'editar') {
        
        return view(sistema_view().'/estadocuenta/edit',[
            'tienda' => $tienda,
            'credito' => $credito,
        ]);
      }
      elseif($request->input('view') == 'pdf_estado'){
            
   
        
        $cliente = DB::table('users')->where('users.id',$id)->first();
          $creditos = DB::table('credito')
              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
              ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
              ->leftJoin('tienda','tienda.id','credito.idtienda')
              ->where('credito.idcliente',$id)
              //->where('credito.idestadocredito',1)
              ->whereIn('credito.estado',['DESEMBOLSADO'])
              ->whereIn('credito.idestadocredito',[1,2])
              ->select(
                  'credito.*',
                  'credito_prendatario.nombre as nombreproductocredito',
                  'forma_pago_credito.nombre as forma_pago_credito_nombre',
                  'tienda.nombre as tiendanombre',
                  'tienda.nombreagencia as nombreagencia',
              )
              ->orderBy('credito.idestadocredito','asc')
              ->orderBy('credito.fecha_desembolso','desc')
              ->get();
          
          $pdf = PDF::loadView(sistema_view().'/estadocuenta/pdf_estado',[
              'tienda' => $tienda,
              'creditos' => $creditos,
              'cliente' => $cliente,
          ]); 
          $pdf->setPaper('A4', 'landscape');
          return $pdf->stream('ESTADO_DE_CUENTA.pdf');
        }
      elseif($request->input('view') == 'pdf_credito'){
        
        $aval = DB::table('users')
            ->join('ubigeo','ubigeo.id','users.idubigeo')
            ->where('users.id',$credito->idaval)
            ->select(
              'users.*',
              'ubigeo.nombre as clienteubigeonombre',
            )
            ->first();
        $users_prestamo = DB::table('s_users_prestamo')->where('s_users_prestamo.id_s_users',$credito->idcliente)->first();
        $users_prestamo_aval = DB::table('s_users_prestamo')->where('s_users_prestamo.id_s_users',$credito->idaval)->first();

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
        
          $asesor = DB::table('users')->where('users.id',$credito->idasesor)->first();
        
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
              $credito->id,
              $credito->idforma_credito,
              $credito->modalidad_calculo,
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
          
          $html = '<table style="width:100%;">
              <thead style="border-top:1px solid #000;">
              <tr >
                <th style="border-bottom:1px solid #000;width:10px;">N° Cuo.</th>
                <th style="border-bottom:1px solid #000;">Fecha</th>
                <th style="border-bottom:1px solid #000;">Amortiz.</th>
                <th style="border-bottom:1px solid #000;">Cuota</th>
                <th style="border-bottom:1px solid #000;">Cust.</th>
                <th style="border-bottom:1px solid #000;width:10px;">I. Comp.</th>
                <th style="border-bottom:1px solid #000;width:10px;">I. Mor.</th>
                <th style="border-bottom:1px solid #000;">Total</th>
                <th style="border-bottom:1px solid #000;">F. de Cancel.</th>
                <th style="border-bottom:1px solid #000;">Atraso</th>
                <th style="border-bottom:1px solid #000;">Estado</th>
              </tr>
              </thead>
              <tbody>';

          
          $primera_cuota_pendiente = 0;
          foreach($cronograma['cronograma'] as $value){
            
              if($value['idestadocredito_cronograma']==1 && $primera_cuota_pendiente==0){
                  $primera_cuota_pendiente = $value['numerocuota'];
              }
            
              
              //  adelanto
              $credito_adelanto = DB::table('credito_adelanto')
                  ->join('credito_cobranzacuota','credito_cobranzacuota.id','credito_adelanto.idcredito_cobranzacuota')
                  ->where('credito_adelanto.idcredito_cronograma',$value['id'])
                  ->where('credito_adelanto.idestadocredito_adelanto',1)
                  ->get();
            
              $totaladelanto = 0;
              $ultimafechaadelanto = '';
              foreach($credito_adelanto as $valueade){
                  $totaladelanto = $valueade->total_pagar;
                  $ultimafechaadelanto = $valueade->fecharegistro;
              }
              
              $fechacobranza_fecharegistro = '';
              $estado = 'Pend.';
              if($totaladelanto>=$value['totalcuota']){
                  $fechacobranza_fecharegistro = date_format(date_create($ultimafechaadelanto),'d-m-Y h:i:s A');
              
                  $estado = 'Canc.';
              }
              // fin adelanto
            
          
              $html .= '<tr>
                            <td style="width:10px;text-align:center;">'.$value['numerocuota'].'</td>
                            <td style="text-align:center;">'.$value['fecha'].'</td>
                            <td style="text-align:right;">'.$value['amortizacion'].'</td>
                            <td style="text-align:right;">'.$value['cuota'].'</td>
                            <td style="text-align:right;">'.$value['tenencia'].'</td>
                            <td style="text-align:right;">'.$value['penalidad'].'</td>
                            <td style="text-align:right;">'.$value['compensatorio'].'</td>
                            <td style="text-align:right;">'.$value['totalcuota'].'</td>
                            <td style="text-align:right;">'.$fechacobranza_fecharegistro.'</td>
                            <td style="text-align:right;">'.$value['atraso_dias'].'</td>
                            <td style="text-align:right;">'.$estado.'</td>
                        </tr>';
            
          }
          $html .= '</tbody>
              <thead style="border-bottom:1px solid #000;">
              <tr>
                <th style="border-top:1px solid #000;"></th>
                <th style="border-top:1px solid #000;"></th>
                <th style="border-top:1px solid #000;text-align:right;">'.$cronograma['total_amortizacion'].'</th>
                <th style="border-top:1px solid #000;text-align:right;">'.$cronograma['total_cuota'].'</th>
                <th style="border-top:1px solid #000;text-align:right;">'.$cronograma['total_tenencia'].'</th>
                <th style="border-top:1px solid #000;text-align:right;">'.$cronograma['total_penalidad'].'</th>
                <th style="border-top:1px solid #000;text-align:right;">'.$cronograma['total_compensatorio'].'</th>
                <th style="border-top:1px solid #000;text-align:right;">'.$cronograma['total_totalcuota'].'</th>
                <th style="border-top:1px solid #000;"></th>
                <th style="border-top:1px solid #000;"></th>
                <th style="border-top:1px solid #000;"></th>
              </tr>
              </thead>
              </table>';
        
          $total_cargo = DB::table('credito_cargo')
              ->where('credito_cargo.idestadocredito_cargo',1)
              ->where('credito_cargo.idcredito',$credito->id)
              ->sum('credito_cargo.importe');
        
          $pdf = PDF::loadView(sistema_view().'/estadocuenta/pdf_credito',[
              'tienda' => $tienda,
              'credito' => $credito,
              'garantias' => $garantias,
              'garantiasaval' => $garantiasaval,
              'tipo_garantia1' => $tipo_garantia1,
              'tipo_garantia2' => $tipo_garantia2,
              'tipo_garantia3' => $tipo_garantia3,
              'asesor' => $asesor,
              'users_prestamo' => $users_prestamo,
              'users_prestamo_aval' => $users_prestamo_aval,
              'aval' => $aval,
              'html_historial_pago' => $html,
              'numero_cuota_cancelada' => $cronograma['numero_cuota_cancelada'],
              'numero_cuota_pendiente' => $cronograma['numero_cuota_pendiente'],
              'numero_cuota_vencida' => $cronograma['numero_cuota_vencida'],
              'pagocuota_adelantado' => $cronograma['pagocuota_adelantado'],
              'pagocuota_vencido' => $cronograma['pagocuota_vencido'],
              'pagocuota_puntual' => $cronograma['pagocuota_puntual'],
              'cuota_pagada' => $cronograma['cuota_pagada'],
              'cuota_pendiente' => $cronograma['cuota_pendiente'],
              'saldo_vencido' => $cronograma['cuota_vencida'],
              'saldo_capital' => $cronograma['saldo_capital'],
              'descuento_porcobrar' => number_format($total_cargo,2,'.',''),
          ]); 
        
          //$pdf->setPaper('A4', 'landscape');
          return $pdf->stream('ESTADO_DE_CUENTA.pdf');
        }
      else if($request->input('view') == 'eliminar'){
        return view(sistema_view().'/estadocuenta/delete',[
            'tienda' => $tienda,
            'credito' => $credito,
            'idcredito_descuentocuota' => $id,
        ]);
      }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        //
    
    }


    public function destroy(Request $request, $idtienda, $id)
    {

    }
}
