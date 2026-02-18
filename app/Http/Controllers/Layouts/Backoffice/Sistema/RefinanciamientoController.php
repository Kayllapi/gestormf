<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class RefinanciamientoController extends Controller
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
          
            return view(sistema_view().'/refinanciamiento/tabla',[
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
          $where2 = [];
          if($request->idagencia!=''){
              $where[] = ['credito.idtienda',$request->idagencia];
              $where2[] = ['credito.idtienda',$request->idagencia];
          }
          if($request->idcliente!=''){
              $where[] = ['credito.idcliente',$request->idcliente];
              $where2[] = ['credito.idcliente',$request->idcliente];
          }
          
          
          $creditos = DB::table('credito')
              ->join('forma_pago_credito','forma_pago_credito.id','credito.idforma_pago_credito')
              ->join('users as cliente','cliente.id','credito.idcliente')
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
              ->orWhere($where2)
              ->where('credito.estado','DESEMBOLSADO')
              ->where('credito.idestadocredito',1)
              ->select(
                  'credito.*',
                  'cliente.nombrecompleto as nombrecliente',
                  'aval.nombrecompleto as nombreaval',
                  'credito_prendatario.nombre as nombreproductocredito' ,
                  'modalidad_credito.nombre as nombremodalidadcredito' ,
                  'forma_pago_credito.nombre as frecuencianombre' ,
                  'cajero.usuario as codigocajero',
                  'asesor.usuario as codigoasesor',
                  'administrador.nombrecompleto as nombreadministrador',
              )
              ->orderBy('credito.fecha_desembolso','asc')
              ->get();
          
          $html = '';
          $total_desembolsado = 0;
          foreach($creditos as $key => $value){
            
              $credito_formapago = DB::table('credito_formapago')->where('credito_formapago.idcredito',$value->id)->first();
              $operacionen = '';
              if($credito_formapago){
                  if($credito_formapago->idformapago==1){
                      $operacionen = 'CAJA';
                  }elseif($credito_formapago->idformapago==2){
                      $operacionen = 'BANCO';
                  }
              }
            
              $creditorefinanciado = DB::table('credito')
                  ->where('idcredito_refinanciado',$value->id)
                  ->first();

              $opcion = '';
              if($creditorefinanciado){
                  $opcion = 'En Refinanciamiento';
              }else{
                  $opcion = "<div class='dropdown' id='menu-opcion'>
                                <button class='btn btn-primary dropdown-toggle'  type='button' data-bs-toggle='dropdown' aria-expanded='false'>Opción</button>
                                <ul class='dropdown-menu dropdown-menu-end'>
                                  <li>
                                    <a class='dropdown-item' href='javascript:;' refinanciar-valor-columna='{$value->id}' onclick='show_refinanciar(this)'>
                                      <i class='fa fa-check'></i> Refinanciar
                                    </a>
                                  </li>
                                  <li>
                                    <a class='dropdown-item' href='javascript:;' data-valor-columna='{$value->id}' onclick='show_data(this)'>
                                      <i class='fa fa-money-bill'></i> Garantia, Cronograma y Evaluación
                                    </a>
                                  </li>
                                </ul>
                              </div>";
              }
              
              $html .= "<tr id='show_data_select' idcredito='{$value->id}'>
                            <td>".($key+1)."</td>
                            <td>{$value->nombrecliente}</td>
                            <td>{$value->nombreaval}</td>
                            <td style='text-align:right;'>{$value->monto_solicitado}</td>
                            <td style='text-align:right;'>{$value->cuotas}</td>
                            <td>{$value->frecuencianombre}</td>
                            <td>{$value->fecha_desembolso}</td>
                            <td>{$value->codigocajero}</td>
                            <td>{$operacionen}</td>
                            <td>{$value->nombremodalidadcredito}</td>
                            <td>{$value->codigoasesor}</td>
                            <td>{$opcion}</td>
                        </tr>";
              $total_desembolsado += $value->monto_solicitado;
          }
          if(count($creditos)==0){
              $html.= '<tr><td colspan="16" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }
              $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="3" style="text-align:right;font-weight: bold;">TOTAL S/.</td>
                  <td style="text-align:right;font-weight: bold;">'.number_format($total_desembolsado, 2, '.', '').'</td>
                  <td colspan="8" style="font-weight: bold;"></td>
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
                    ->leftjoin('users as aval','aval.id','credito.idaval')
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
        if( $request->input('view') == 'opciones' ){
          return view(sistema_view().'/refinanciamiento/opciones',[
            'tienda' => $tienda,
            'credito' => $credito,
            'usuario' => $usuario,
            'users_prestamo' => $users_prestamo,
          ]);
        }
      else if( $request->input('view') == 'refinanciar' ){
        
      
      $tarifario_producto = DB::table('tarifario')
                            ->join('forma_pago_credito','forma_pago_credito.id','tarifario.idforma_pago_credito')
                            ->where('tarifario.idcredito_prendatario',$credito->idcredito_prendatario)
                            ->select(
                                'tarifario.*',
                                'forma_pago_credito.nombre as nombreformapago',          
                            )
                            ->orderBy('tarifario.id','desc')
                            ->get();
      
        $tipocredito = $credito->idforma_credito== 1 ? 'PRENDARIA' : 'NOPRENDARIA';
        $diasdegracia = DB::table('diasdegracia')->where('diasdegracia.nombre',$tipocredito)->first();
        
        
          
          $cronograma = select_cronograma(
              $tienda->id,
              $credito->id,
              $credito->idforma_credito,
              $credito->modalidad_calculo,
              $credito->cuotas,
          );
        
        return view(sistema_view().'/refinanciamiento/refinanciar',[
          'tienda' => $tienda,
          'usuario' => $usuario,
          'cronograma' => $cronograma,
          'modalidad_credito' => $this->modalidad_credito,
          'tipo_operacion_credito' => $this->tipo_operacion_credito,
          'forma_pago_credito' => $this->forma_pago_credito,
          'tipo_destino_credito' => $this->tipo_destino_credito,
          'tarifario_producto' => $tarifario_producto,
          'credito' => $credito,
          'forma_credito' => $this->forma_credito,
          'diasdegracia' => $diasdegracia->dias,
          'view_detalle' => $request->detalle
        ]);
      }
    }

    public function update(Request $request, $idtienda, $id)
    {
        if($request->input('view') == 'refinanciamiento') {
          
            $rules = [
                'monto_solicitado' => 'required',                
                'idforma_pago_credito' => 'required',                 
                'cuotas' => 'required',                
                'tasa_tem' => 'required',                   
                'dia_gracia' => 'required',               
                'fecha_desembolso' => 'required',                   
            ];
          
            $messages = [
                'monto_solicitado.required' => 'El Campo es Obligatorio.',
                'idforma_pago_credito.required' => 'El Campo es Obligatorio.',
                'cuotas.required' => 'El Campo es Obligatorio.',
                'tasa_tem.required' => 'El Campo es Obligatorio.',
                'dia_gracia.required' => 'El Campo es Obligatorio.',
                'fecha_desembolso.required' => 'El Campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            // GENERAR CRONOGRAMA
            if($request->input('tasa_tem')<=0 || $request->input('tasa_tem')==''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'TEM debe ser mayor a 0.00.'
                ]);
            }
            if($request->input('monto_solicitado')<=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Monto de Prestamo debe ser mayor a 0.00.'
                ]);
            }
            if($request->input('cuotas')<=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Número de Cuotas debe ser mayor a 0.'
                ]);
            }

            if($request->input('dia_gracia')<0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El día de gracia debe ser mayor o igual a 0!!.'
                ]);
            }
          
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
                        'tipo_credito.nombre as tipo_creditonombre',
                    )
                    ->orderBy('credito.id','desc')
                    ->first();
          
            //------- validar cronograma

            /*$montomaximo = DB::table('tarifario')
                  ->where('tarifario.idcredito_prendatario',$credito->idcredito_prendatario)
                  ->where('tarifario.idforma_pago_credito',$request->input('idforma_pago_credito'))
                  ->orderBy('tarifario.monto','desc')
                  ->limit(1)
                  ->first();

            if($montomaximo!=''){
                if($request->input('monto_solicitado')>$montomaximo->monto){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El monto máximo según el tarifario es '.$montomaximo->monto.'.',
                    ]);
                }
            }

            $cuotamaximo = DB::table('tarifario')
                  ->where('tarifario.idcredito_prendatario',$credito->idcredito_prendatario)
                  ->where('tarifario.idforma_pago_credito',$request->input('idforma_pago_credito'))
                  ->orderBy('tarifario.cuotas','desc')
                  ->limit(1)
                  ->first();
            if($cuotamaximo!=''){
                if($request->input('cuotas')>$cuotamaximo->cuotas){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La cuota máxima según el tarifario es '.$cuotamaximo->cuotas.'.',
                    ]);
                }
            }*/

            /*if($credito->idforma_credito == 1){
                if($request->input('monto_solicitado') > $credito->monto_cobertura_garantia){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El monto máximo según la cobertura es '.$credito->monto_cobertura_garantia.'.',
                    ]);
                }
            }*/


            $tasatarifario = DB::table('tarifario')
                  ->where('tarifario.idcredito_prendatario',$credito->idcredito_prendatario)
                  ->where('tarifario.idforma_pago_credito',$request->input('idforma_pago_credito'))
                  ->where('tarifario.monto','>=',$request->input('monto_solicitado'))
                  ->where('tarifario.cuotas','>=',$request->input('cuotas'))
                  ->orderBy('tarifario.cuotas','asc')
                  ->orderBy('tarifario.monto','asc')
                  ->limit(1)
                  ->first();
            $tasa_tem_minima = 0;
            $comision_cargo = 0;
            if($tasatarifario!=''){
                $tasa_tem_minima = $tasatarifario->tem;
                $comision_cargo = $tasatarifario->cargos_otros;
                /*if($request->input('tasa_tem') < $tasa_tem_minima){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El tasa mínima según el tarifario es '.$tasa_tem_minima.'.',
                    ]);
                }*/
            }else{
                /*return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No se asignado ningún tarifario para esta frecuencia de pago!!.',
                ]);*/
            }

            $frecuenciaDiasMap = [
              1 => 26,
              2 => 4,
              3 => 2,
              4 => 1,
            ];
            $dias = $frecuenciaDiasMap[$request->input('idforma_pago_credito')];
            $tasa_tip = number_format(($request->input('tasa_tem') / $dias) * $request->input('cuotas'), 2, '.', '');
            if($credito->modalidad_calculo == 'Interes Compuesto'){
                $tasa_tip = $request->input('tasa_tem');
            }
            
        

            $cronograma = genera_cronograma(
                  $request->input('monto_solicitado'),
                  $request->input('cuotas'),
                  $request->input('fecha_desembolso'),
                  $request->input('idforma_pago_credito'),
                  $tasa_tip,
                  $request->input('tipotasa'),
                  $request->input('dia_gracia'),
                  $comision_cargo,
                  $request->input('cargo')
            );
            //-------- fin validar cronograma
          
            // ---- SELECCIONAR LOS DATOS FILTRADOS
          
            $forma_pago_credito = DB::table('forma_pago_credito')->whereId($request->input('idforma_pago_credito'))->first();
            
            // ---- FIN SELECCIONAR LOS DATOS FILTRADOS
            // FIN GENERAR CRONOGRAMA
          
              
            // ---- SELECCIONAR LOS DATOS FILTRADOS
            $cliente = DB::table('users')->whereId($credito->idcliente)->first();
            $clienteidentificacion = '';
            $clientenombrecompleto = '';
            if($cliente!=''){
                $clienteidentificacion = $cliente->identificacion;
                $clientenombrecompleto = $cliente->nombrecompleto;
            }
            $aval = DB::table('users')->whereId($credito->idaval)->first();
            $avalidentificacion = '';
            $avalnombrecompleto = '';
            if($aval!=''){
                $avalidentificacion = $aval->identificacion;
                $avalnombrecompleto = $aval->nombrecompleto;
            }
            $asesor = DB::table('users')->whereId(Auth::user()->id)->first();
            $asesoridentificacion = '';
            $asesornombrecompleto = '';
            if($asesor!=''){
                $asesoridentificacion = $asesor->identificacion;
                $asesornombrecompleto = $asesor->nombrecompleto;
            }
            //dd($credito);
            $credito_prendatario = DB::table('credito_prendatario')->whereId($credito->idcredito_prendatario)->first();
            $tipo_operacion_credito = DB::table('tipo_operacion_credito')->whereId($credito->idtipo_operacion_credito)->first();
            $forma_credito = DB::table('forma_credito')->whereId($credito->idforma_credito)->first();
            $tipo_destino_credito = DB::table('tipo_destino_credito')->whereId($credito->idtipo_destino_credito)->first();
            $modalidad_credito = DB::table('modalidad_credito')->whereId($credito->idmodalidad_credito)->first();
            
    
          
            $idcreditorefinanciado = DB::table('credito')->insertGetId([
              
                'forma_pago_credito'        => $forma_pago_credito->nombre,
                'saldo_pendientepago'       => $request->input('monto_solicitado'),
                'cuota_pago'                => $cronograma['cuota_pago'],
                'fecha_primerpago'          => $cronograma['fechainicio'],
                'fecha_ultimopago'          => $cronograma['ultimafecha'],
                'fecha'                     => $request->input('fecha_desembolso'),
                'monto_solicitado'          => $request->input('monto_solicitado'),
                'idforma_pago_credito'      => $request->input('idforma_pago_credito'),
                'cuotas'                    => $request->input('cuotas'),
                'dia_gracia'                => $request->input('dia_gracia'),
                'tasa_tem'                  => $request->input('tasa_tem'),
                'tasa_tem_minima'           => $request->input('tasa_tem_minima'),
                'tasa_tip'                  => $request->input('tasa_tip'),
                'tasa_tcem'                 => $request->input('tasa_tcem'),
                'interes_total'             => $request->input('interes_total'),
                'total_pagar'               => $request->input('total_pagar'),
                'total_propuesta'           => $cronograma['total_propuesta'],
                'comision'                  => $request->input('comision'),
                'cargo'                     => $request->input('cargo'),
                'cuota_comision'            => $cronograma['cuota_comision'],
                'cuota_cargo'               => $cronograma['cuota_cargo'],
                'cuota_comisioncargo'       => $cronograma['cuota_comisioncargo'],
                'total_comision'            => $cronograma['total_comision'],
                'total_cargo'               => $cronograma['total_cargo'],
                'total_comisioncargo'       => $cronograma['total_comisioncargo'],
              
              
                'clienteidentificacion'     => $clienteidentificacion,
                'clientenombrecompleto'     => $clientenombrecompleto,
                'avalidentificacion'        => $avalidentificacion,
                'avalnombrecompleto'        => $avalnombrecompleto,
                'credito_prendatario'       => $credito_prendatario->nombre,
                'tipo_operacion_credito'    => $tipo_operacion_credito->nombre,
                'forma_credito'             => $forma_credito->nombre,
                'tipo_destino_credito'      => $tipo_destino_credito->nombre,
                'modalidad_credito'         => $modalidad_credito->nombre,
                'asesoridentificacion'      => $asesoridentificacion,
                'asesornombrecompleto'      => $asesornombrecompleto,
                'participarconyugue_titular'=> $credito->participarconyugue_titular,
                'participarconyugue_aval'   => $credito->participarconyugue_aval,

                'idcliente'                 => $credito->idcliente,
                'idaval'                    => $credito->idaval!=''?$credito->idaval:0,
                'idcredito_prendatario'     => $credito->idcredito_prendatario,
                'idtipo_operacion_credito'  => $credito->idtipo_operacion_credito,
                'idforma_credito'           => $credito->idforma_credito,
                'idtipo_destino_credito'    => $credito->idtipo_destino_credito,
                'idcredito_refinanciado'    => $credito->id,
                //'idforma_pago_credito'      => 1,
                'idmodalidad_credito'       => 4, //Refinanciado
                'fecha'                     => Carbon::now(),
                'idasesor'                  => Auth::user()->id,
                'estado'                    => 'PROCESO',
                'idevaluacion'              => 1,
                'idestadocredito'           => 1,
                'idtienda'                  => user_permiso()->idtienda,
                'idestadorefinanciamiento'  => 1,
            ]);
          

            foreach($cronograma['cronograma'] as $value){
                DB::table('credito_cronograma')->insert([
                    'numerocuota'     => $value['numero'],
                    'fechapago'       => $value['fechanormal'],
                    'capital'         => $value['saldo'],
                    'amortizacion'    => $value['amortizacion'],
                    'interes'         => $value['interes'],
                    'cuotapagar'      => 0,
                    'cuota_real'      => $value['cuotafinal'],
                    'resto_redondeo'  => 0,
                    'comision'        => $value['comision'],
                    'cargo'           => $value['cargo'],
                    'comision_cargo'  => $value['comisioncargo'],
                    'idestadocredito_cronograma' => 1,
                    'idcredito'       => $idcreditorefinanciado,
                ]);
            }
          
            //jalar ultimo registro
          
            $ultimagarantias_cliente = DB::table('credito_garantia')
                    ->where('credito_garantia.idcredito',$credito->id)
                    ->where('credito_garantia.tipo','CLIENTE')
                    ->get();
            foreach($ultimagarantias_cliente as $value){
              
                DB::table('credito_garantia')->insertGetId([
                  'garantias_codigo'              => $value->garantias_codigo,
                  'garantias_tipogarantia'        => $value->garantias_tipogarantia,
                  'garantias_serie_motor_partida' => $value->garantias_serie_motor_partida,
                  'garantias_chasis'              => $value->garantias_chasis,
                  'garantias_modelo_tipo'         => $value->garantias_modelo_tipo,
                  'garantias_otros'               => $value->garantias_otros,
                  'garantias_color'               => $value->garantias_color,
                  'garantias_fabricacion'         => $value->garantias_fabricacion,
                  'garantias_compra'              => $value->garantias_compra,
                  'garantias_placa'               => $value->garantias_placa,
                  'garantias_accesorio_doc'       => $value->garantias_accesorio_doc,
                  'garantias_detalle_garantia'    => $value->garantias_detalle_garantia,
                  'garantias_metodo_valorizacion' => $value->garantias_metodo_valorizacion,
                  'garantias_tipo_joyas'          => $value->garantias_tipo_joyas,
                  'garantias_tarifario_joya'      => $value->garantias_tarifario_joya,
                  'garantias_descuento_joya'      => $value->garantias_descuento_joya,
                  'garantias_valorizacion_descuento'  => $value->garantias_valorizacion_descuento,
                  'clienteidentificacion'     => $value->clienteidentificacion,
                  'clientenombrecompleto'     => $value->clientenombrecompleto,

                  'garantias_noprendarias_tipo_garantia_noprendaria'  => $value->garantias_noprendarias_tipo_garantia_noprendaria,
                  'garantias_noprendarias_subtipo_garantia_noprendaria'  => $value->garantias_noprendarias_subtipo_garantia_noprendaria,
                  'garantias_noprendarias_subtipo_garantia_noprendaria_ii'  => $value->garantias_noprendarias_subtipo_garantia_noprendaria_ii,

                  'idtipo_garantia_noprendaria'  => $value->idtipo_garantia_noprendaria,

                  'idcredito'                 => $idcreditorefinanciado,
                  'idgarantias'               => $value->idgarantias,
                  'idcliente'                 => $value->idcliente,
                  'idgarantias_noprendarias'  => $value->idgarantias_noprendarias,
                  'descripcion'               => $value->descripcion,
                  'valor_mercado'             => $value->valor_mercado,
                  'valor_comercial'           => $value->valor_comercial,
                  'valor_realizacion'         => $value->valor_realizacion,
                  'tipo'                      => 'CLIENTE',
                  'idestadoentrega'           => 1,
                ]);
             } 
            $ultimagarantias_aval = DB::table('credito_garantia')
                    ->where('credito_garantia.idcredito',$credito->id)
                    ->where('credito_garantia.tipo','AVAL')
                    ->get();
            foreach($ultimagarantias_aval as $value){
              
                DB::table('credito_garantia')->insertGetId([
                  'garantias_codigo'              => $value->garantias_codigo,
                  'garantias_tipogarantia'        => $value->garantias_tipogarantia,
                  'garantias_serie_motor_partida' => $value->garantias_serie_motor_partida,
                  'garantias_chasis'              => $value->garantias_chasis,
                  'garantias_modelo_tipo'         => $value->garantias_modelo_tipo,
                  'garantias_otros'               => $value->garantias_otros,
                  'garantias_color'               => $value->garantias_color,
                  'garantias_fabricacion'         => $value->garantias_fabricacion,
                  'garantias_compra'              => $value->garantias_compra,
                  'garantias_placa'               => $value->garantias_placa,
                  'garantias_accesorio_doc'       => $value->garantias_accesorio_doc,
                  'garantias_detalle_garantia'    => $value->garantias_detalle_garantia,
                  'garantias_metodo_valorizacion' => $value->garantias_metodo_valorizacion,
                  'garantias_tipo_joyas'          => $value->garantias_tipo_joyas,
                  'garantias_tarifario_joya'      => $value->garantias_tarifario_joya,
                  'garantias_descuento_joya'      => $value->garantias_descuento_joya,
                  'garantias_valorizacion_descuento'  => $value->garantias_valorizacion_descuento,
                  'clienteidentificacion'     => $value->clienteidentificacion,
                  'clientenombrecompleto'     => $value->clientenombrecompleto,

                  'garantias_noprendarias_tipo_garantia_noprendaria'  => $value->garantias_noprendarias_tipo_garantia_noprendaria,
                  'garantias_noprendarias_subtipo_garantia_noprendaria'  => $value->garantias_noprendarias_subtipo_garantia_noprendaria,
                  'garantias_noprendarias_subtipo_garantia_noprendaria_ii'  => $value->garantias_noprendarias_subtipo_garantia_noprendaria_ii,

                  'idtipo_garantia_noprendaria'  => $value->idtipo_garantia_noprendaria,

                  'idcredito'                 => $idcreditorefinanciado,
                  'idgarantias'               => $value->idgarantia,
                  'idcliente'                 => $value->idcliente,
                  'idgarantias_noprendarias'  => $value->idgarantias_noprendarias,
                  'descripcion'               => $value->descripcion,
                  'valor_mercado'             => $value->valor_mercado,
                  'valor_comercial'           => $value->valor_comercial,
                  'valor_realizacion'         => $value->valor_realizacion,
                  'tipo'                      => 'AVAL',
                  'idestadoentrega'           => 1,
                ]);
            } 
          
            $ultimocredito = DB::table('credito')
                //->where('credito.idevaluacion',1)
                ->where('credito.idcliente',$credito->idcliente)
                ->where('credito.estado','DESEMBOLSADO')
                ->whereIn('credito.idestadocredito',[1,2])
                ->orderBy('credito.idestadocredito','asc')
                ->orderBy('credito.fecha_desembolso','desc')
                ->limit(1)
                ->first();
            
            if($ultimocredito!=''){
              
                // Evaluación Cualiativa
              
                $credito_evaluacion_cualitativa = DB::table('credito_evaluacion_cualitativa')->where('idcredito',$ultimocredito->id)->first();
                
                if($credito_evaluacion_cualitativa!=''){
                DB::table('credito_evaluacion_cualitativa')->insert([
                    'idcredito' => $idcreditorefinanciado,
                    'fecha' => Carbon::now(),
                    'descripcion_actividad' => $credito_evaluacion_cualitativa->descripcion_actividad,
                    'idtipo_giro_economico' => $credito_evaluacion_cualitativa->idtipo_giro_economico,
                    'idgiro_economico_evaluacion' => $credito_evaluacion_cualitativa->idgiro_economico_evaluacion,
                    'ejercicio_giro_economico' => $credito_evaluacion_cualitativa->ejercicio_giro_economico,

                    'referencia' => $credito_evaluacion_cualitativa->referencia,

                    'cantidad_cliente_natural' => $credito_evaluacion_cualitativa->cantidad_cliente_natural,
                    'cantidad_cliente_juridico' => $credito_evaluacion_cualitativa->cantidad_cliente_juridico,
                    'cantidad_pareja_natural' => $credito_evaluacion_cualitativa->cantidad_pareja_natural,
                    'cantidad_pareja_juridico' => $credito_evaluacion_cualitativa->cantidad_pareja_juridico,
                    'total_deuda' => $credito_evaluacion_cualitativa->total_deuda,
                    'experiencia_microempresa' => $credito_evaluacion_cualitativa->experiencia_microempresa,
                    'tiempo_mismo_local' => $credito_evaluacion_cualitativa->tiempo_mismo_local,
                    'instalacion_local' => $credito_evaluacion_cualitativa->instalacion_local,
                    'nro_trabajador_completo' => $credito_evaluacion_cualitativa->nro_trabajador_completo,
                    'nro_trabajador_parcal' => $credito_evaluacion_cualitativa->nro_trabajador_parcal,

                    'saladario_fijo' => $credito_evaluacion_cualitativa->saladario_fijo,
                    'otros_negocios' => $credito_evaluacion_cualitativa->otros_negocios,
                    'alquiler_local' => $credito_evaluacion_cualitativa->alquiler_local,
                    'no_tiene' => $credito_evaluacion_cualitativa->no_tiene,
                    'pensionista' => $credito_evaluacion_cualitativa->pensionista,
                    'registro_ventas_cuentas' => $credito_evaluacion_cualitativa->registro_ventas_cuentas,
                    'pago_impuestos_dia' => $credito_evaluacion_cualitativa->pago_impuestos_dia,
                    'pago_servicios_dia' => $credito_evaluacion_cualitativa->pago_servicios_dia,
                    'politica_orden' => $credito_evaluacion_cualitativa->politica_orden,
                    'normas_municipales' => $credito_evaluacion_cualitativa->normas_municipales,

                    'gasto_alimentacion' => $credito_evaluacion_cualitativa->gasto_alimentacion,
                    'gasto_educacion' => $credito_evaluacion_cualitativa->gasto_educacion,
                    'gasto_vestimenta' => $credito_evaluacion_cualitativa->gasto_vestimenta,
                    'gasto_transporte' => $credito_evaluacion_cualitativa->gasto_transporte,
                    'gasto_salud' => $credito_evaluacion_cualitativa->gasto_salud,
                    'gasto_vivienda' => $credito_evaluacion_cualitativa->gasto_vivienda,
                    'gasto_agua' => $credito_evaluacion_cualitativa->gasto_agua,
                    'gasto_luz' => $credito_evaluacion_cualitativa->gasto_luz,
                    'gasto_telefono_internet' => $credito_evaluacion_cualitativa->gasto_telefono_internet,
                    'gasto_celular' => $credito_evaluacion_cualitativa->gasto_celular,
                    'gasto_cable' => $credito_evaluacion_cualitativa->gasto_cable,
                    'total_servicios' => $credito_evaluacion_cualitativa->total_servicios,
                    'gasto_otros' => $credito_evaluacion_cualitativa->gasto_otros,
                    'gasto_total' => $credito_evaluacion_cualitativa->gasto_total,

                    'total_hijos' => $credito_evaluacion_cualitativa->total_hijos,
                    'total_hijos_dependientes' => $credito_evaluacion_cualitativa->total_hijos_dependientes,
                    'detalle_destino_prestamo' => $credito_evaluacion_cualitativa->detalle_destino_prestamo,
                    'fortalezas_negocio' => $credito_evaluacion_cualitativa->fortalezas_negocio,
                ]);
                }
              
                // Evaluación Cuantitiva
              
                $credito_evaluacion_cuantitativa = DB::table('credito_evaluacion_cuantitativa')->where('idcredito',$ultimocredito->id)->first();
                
                if($credito_evaluacion_cuantitativa!=''){
                DB::table('credito_evaluacion_cuantitativa')->insert([
                    'idcredito' => $idcreditorefinanciado,
                    'fecha' => Carbon::now(),
                    'evaluacion_meses' => $credito_evaluacion_cuantitativa->evaluacion_meses,
                    'margen_venta_calculado' => $credito_evaluacion_cuantitativa->margen_venta_calculado,
                    'balance_general' => $credito_evaluacion_cuantitativa->balance_general,
                    'ganancia_perdida' => $credito_evaluacion_cuantitativa->ganancia_perdida,

                    'dias_ventas_mensual' => $credito_evaluacion_cuantitativa->dias_ventas_mensual,
                    'dias_compras_mensual' => $credito_evaluacion_cuantitativa->dias_compras_mensual,

                    'credito_cobrando_venta_mensual' => $credito_evaluacion_cuantitativa->credito_cobrando_venta_mensual,
                    'credito_porcentaje_venta_mensual' => $credito_evaluacion_cuantitativa->credito_porcentaje_venta_mensual,
                    'contado_cobrando_venta_mensual' => $credito_evaluacion_cuantitativa->contado_cobrando_venta_mensual,
                    'contado_porcentaje_venta_mensual' => $credito_evaluacion_cuantitativa->contado_porcentaje_venta_mensual,
                    'credito_cobrando_compra_mensual' => $credito_evaluacion_cuantitativa->credito_cobrando_compra_mensual,
                    'credito_porcentaje_compra_mensual' => $credito_evaluacion_cuantitativa->credito_porcentaje_compra_mensual,
                    'contado_cobrando_compra_mensual' => $credito_evaluacion_cuantitativa->contado_cobrando_compra_mensual,
                    'contado_porcentaje_compra_mensual' => $credito_evaluacion_cuantitativa->contado_porcentaje_compra_mensual,

                    'ratio_re_negocio' => $credito_evaluacion_cuantitativa->ratio_re_negocio,
                    'ratio_re_unidadfamiliar' => $credito_evaluacion_cuantitativa->ratio_re_unidadfamiliar,
                    'ratio_re_patrimonial' => $credito_evaluacion_cuantitativa->ratio_re_patrimonial,
                    'ratio_re_activos' => $credito_evaluacion_cuantitativa->ratio_re_activos,
                    'ratio_re_ventas' => $credito_evaluacion_cuantitativa->ratio_re_ventas,

                    'ratio_re_prestamo' => $credito_evaluacion_cuantitativa->ratio_re_prestamo,
                    'ratio_re_capital' => $credito_evaluacion_cuantitativa->ratio_re_capital,
                    'ratio_re_liquidez' => $credito_evaluacion_cuantitativa->ratio_re_liquidez,
                    'ratio_re_liquidez_acida' => $credito_evaluacion_cuantitativa->ratio_re_liquidez_acida,
                    'ratio_re_endeudamiento_actual' => $credito_evaluacion_cuantitativa->ratio_re_endeudamiento_actual,
                    'ratio_re_endeudamiento_propuesta' => $credito_evaluacion_cuantitativa->ratio_re_endeudamiento_propuesta,
                    'ratio_re_rotacion_inventario' => $credito_evaluacion_cuantitativa->ratio_re_rotacion_inventario,
                    'ratio_re_promedio_cobranza' => $credito_evaluacion_cuantitativa->ratio_re_promedio_cobranza,
                    'ratio_re_primedio_pago' => $credito_evaluacion_cuantitativa->ratio_re_primedio_pago,
                    //'ratio_re_cuota_total' => $request->input('ratio_re_cuota_total'),
                    'excedente_antes_propuesta' => $credito_evaluacion_cuantitativa->excedente_antes_propuesta,
                    'excedente_propuesta_sin_deduccion' => $credito_evaluacion_cuantitativa->excedente_propuesta_sin_deduccion,
                    'excedente_propuesta_con_deduccion' => $credito_evaluacion_cuantitativa->excedente_propuesta_con_deduccion,
                    'estado_credito' => $credito_evaluacion_cuantitativa->estado_credito,

                    'comentario' => $credito_evaluacion_cuantitativa->comentario,
                ]);
                }
              
                // Deuda
              
                $credito_cuantitativa_deudas = DB::table('credito_cuantitativa_deudas')->where('idcredito',$ultimocredito->id)->first();
              
                if($credito_cuantitativa_deudas!=''){
                DB::table('credito_cuantitativa_deudas')->insert([
                    'idcredito' => $idcreditorefinanciado,
                    'fecha' => Carbon::now(),
                    'entidad_regulada' => $credito_cuantitativa_deudas->entidad_regulada,
                    'total_saldo_capital' => $credito_cuantitativa_deudas->total_saldo_capital,
                    'total_cuota' => $credito_cuantitativa_deudas->total_cuota,
                    'total_corto_plazo' => $credito_cuantitativa_deudas->total_corto_plazo,
                    'total_largo_plazo' => $credito_cuantitativa_deudas->total_largo_plazo,
                    'total_saldo_capital_deducciones' => $credito_cuantitativa_deudas->total_saldo_capital_deducciones,
                    'total_cuota_deducciones' => $credito_cuantitativa_deudas->total_cuota_deducciones,

                    'entidad_noregulada' => $credito_cuantitativa_deudas->entidad_noregulada,
                    'total_noregulada_saldo_capital' => $credito_cuantitativa_deudas->total_noregulada_saldo_capital,
                    'total_noregulada_cuota' => $credito_cuantitativa_deudas->total_noregulada_cuota,
                    'total_noregulada_corto_plazo' => $credito_cuantitativa_deudas->total_noregulada_corto_plazo,
                    'total_noregulada_largo_plazo' => $credito_cuantitativa_deudas->total_noregulada_largo_plazo,
                    'total_noregulada_saldo_capital_deducciones' => $credito_cuantitativa_deudas->total_noregulada_saldo_capital_deducciones,
                    'total_noregulada_cuota_deducciones' => $credito_cuantitativa_deudas->total_noregulada_cuota_deducciones,

                    'linea_credito' => $credito_cuantitativa_deudas->linea_credito,
                    'total_lc_linea_credito' => $credito_cuantitativa_deudas->total_lc_linea_credito,
                    'total_lc_cuotas' => $credito_cuantitativa_deudas->total_lc_cuotas,
                    'resumen' => $credito_cuantitativa_deudas->resumen,

                    'total_resumen_linea_credito' => $credito_cuantitativa_deudas->total_resumen_linea_credito,
                    'total_resumen_cuotas_linea_credito' => $credito_cuantitativa_deudas->total_resumen_cuotas_linea_credito,
                    'total_resumen_cuotas_linea_credito2' => $credito_cuantitativa_deudas->total_resumen_cuotas_linea_credito2,

                    'idforma_pago_credito' => $credito_cuantitativa_deudas->idforma_pago_credito,
                    'propuesta_cuotas' => $credito_cuantitativa_deudas->propuesta_cuotas,
                    'propuesta_monto' => $credito_cuantitativa_deudas->propuesta_monto,
                    'propuesta_tem' => $credito_cuantitativa_deudas->propuesta_tem,

                    'propuesta_servicio_otros' => $credito_cuantitativa_deudas->propuesta_servicio_otros,
                    'propuesta_cargos' => $credito_cuantitativa_deudas->propuesta_cargos,
                    'propuesta_total_pagar' => $credito_cuantitativa_deudas->propuesta_total_pagar,
                    'total_propuesta' => $credito_cuantitativa_deudas->total_propuesta,

                    'riesgo_proyectado_empresa' => $credito_cuantitativa_deudas->riesgo_proyectado_empresa,
                    'riesgo_proyectado_todos' => $credito_cuantitativa_deudas->riesgo_proyectado_todos,
                    /*'excedente_antes_propuesta' => $request->input('excedente_antes_propuesta'),
                    'excedente_propuesta_sin_deduccion' => $request->input('excedente_propuesta_sin_deduccion'),
                    'excedente_propuesta_con_deduccion' => $request->input('excedente_propuesta_con_deduccion'),*/
                    'estado_credito' => $credito_cuantitativa_deudas->estado_credito,
                ]);
                }
              
                // Control Limites
              
                $credito_cuantitativa_control_limites = DB::table('credito_cuantitativa_control_limites')->where('idcredito',$ultimocredito->id)->first();
          
                if($credito_cuantitativa_control_limites!=''){
                DB::table('credito_cuantitativa_control_limites')->insert([
                    'fecha' => Carbon::now(),
                    'idcredito' => $idcreditorefinanciado,
                    'vinculacion_deudor' => $credito_cuantitativa_control_limites->vinculacion_deudor,
                    'total_garantia_cliente' => $credito_cuantitativa_control_limites->total_garantia_cliente,
                    'cantidad_garante_natural' => $credito_cuantitativa_control_limites->cantidad_garante_natural,
                    'cantidad_garante_juridico' => $credito_cuantitativa_control_limites->cantidad_garante_juridico,
                    'cantidad_pareja_natural' => $credito_cuantitativa_control_limites->cantidad_pareja_natural,
                    'cantidad_pareja_juridico' => $credito_cuantitativa_control_limites->cantidad_pareja_juridico,
                    'total_deuda' => $credito_cuantitativa_control_limites->total_deuda,
                    'total_garantia_aval' => $credito_cuantitativa_control_limites->total_garantia_aval,
                    'total_vinculo_deudor' => $credito_cuantitativa_control_limites->total_vinculo_deudor,
                    'comentarios' => $credito_cuantitativa_control_limites->comentarios,

                    'saldo_noprendario_cliente' => $credito_cuantitativa_control_limites->saldo_noprendario_cliente,
                    'propuesta_noprendario_cliente' => $credito_cuantitativa_control_limites->propuesta_noprendario_cliente,
                    'saldo_noprendario_aval' => $credito_cuantitativa_control_limites->saldo_noprendario_aval,
                    'propuesta_noprendario_aval' => $credito_cuantitativa_control_limites->propuesta_noprendario_aval,

                    'reporte_institucional' => $credito_cuantitativa_control_limites->reporte_institucional,
                    'capital_asignado' => $credito_cuantitativa_control_limites->capital_asignado,

                    'total_financiado_deudor' => $credito_cuantitativa_control_limites->total_financiado_deudor,
                    'porcentaje_resultado' => $credito_cuantitativa_control_limites->porcentaje_resultado,
                    'estado_resultado' => $credito_cuantitativa_control_limites->estado_resultado,
                ]);
                }
              
                // ingreso adicional
              
                $credito_cuantitativa_ingreso_adicional = DB::table('credito_cuantitativa_ingreso_adicional')->where('idcredito',$ultimocredito->id)->first();
   
                if($credito_cuantitativa_ingreso_adicional!=''){
                DB::table('credito_cuantitativa_ingreso_adicional')->insert([
                    'idcredito' => $idcreditorefinanciado,
                    'fecha' => Carbon::now(),
                    'idtipo_giro_economico_adiccional' => $credito_cuantitativa_ingreso_adicional->idtipo_giro_economico_adiccional,
                    'idgiro_economico_evaluacion_adicional' => $credito_cuantitativa_ingreso_adicional->idgiro_economico_evaluacion_adicional,
                    'evaluacion_meses' => $credito_cuantitativa_ingreso_adicional->evaluacion_meses,
                    'margen_venta_calculado' => $credito_cuantitativa_ingreso_adicional->margen_venta_calculado,
                    'productos' => $credito_cuantitativa_ingreso_adicional->productos,
                    'total_venta' => $credito_cuantitativa_ingreso_adicional->total_venta,
                    'total_compra' => $credito_cuantitativa_ingreso_adicional->total_compra,
                    'porcentaje_margen' => $credito_cuantitativa_ingreso_adicional->porcentaje_margen,
                    'frecuencia_ventas' => $credito_cuantitativa_ingreso_adicional->frecuencia_ventas,
                    'dias' => $credito_cuantitativa_ingreso_adicional->dias,
                    'venta_total_dias' => $credito_cuantitativa_ingreso_adicional->venta_total_dias,
                    'numero_dias' => $credito_cuantitativa_ingreso_adicional->numero_dias,

                    'venta_mensual' => $credito_cuantitativa_ingreso_adicional->venta_mensual,
                    'recabo_dato_numero' => $credito_cuantitativa_ingreso_adicional->recabo_dato_numero,
                    'recabo_dato_dia' => $credito_cuantitativa_ingreso_adicional->recabo_dato_dia,
                    'recabo_dato_monto' => $credito_cuantitativa_ingreso_adicional->recabo_dato_monto,
                    'estado_muestra' => $credito_cuantitativa_ingreso_adicional->estado_muestra,
                    'margen_ventas' => $credito_cuantitativa_ingreso_adicional->margen_ventas,
                    'subproducto' => $credito_cuantitativa_ingreso_adicional->subproducto,
                    'productos_mensual' => $credito_cuantitativa_ingreso_adicional->productos_mensual,
                    'total_venta_mensual' => $credito_cuantitativa_ingreso_adicional->total_venta_mensual,
                    'total_compra_mensual' => $credito_cuantitativa_ingreso_adicional->total_compra_mensual,
                    'porcentaje_margen_mensual' => $credito_cuantitativa_ingreso_adicional->porcentaje_margen_mensual,
                    'semanas' => $credito_cuantitativa_ingreso_adicional->semanas,
                    'venta_total_mensual' => $credito_cuantitativa_ingreso_adicional->venta_total_mensual,
                    'estado_muestra_mensual' => $credito_cuantitativa_ingreso_adicional->estado_muestra_mensual,
                    'margen_ventas_mensual' => $credito_cuantitativa_ingreso_adicional->margen_ventas_mensual,
                    'subproductomensual' => $credito_cuantitativa_ingreso_adicional->subproductomensual,

                    'inventario' => $credito_cuantitativa_ingreso_adicional->inventario,
                    'total_inventario' => $credito_cuantitativa_ingreso_adicional->total_inventario,
                    'inmuebles' => $credito_cuantitativa_ingreso_adicional->inmuebles,
                    'total_inmuebles' => $credito_cuantitativa_ingreso_adicional->total_inmuebles,
                    'muebles' => $credito_cuantitativa_ingreso_adicional->muebles,
                    'total_muebles' => $credito_cuantitativa_ingreso_adicional->total_muebles,

                    'balance_general' => $credito_cuantitativa_ingreso_adicional->balance_general,
                    'ganancias_perdidas' => $credito_cuantitativa_ingreso_adicional->ganancias_perdidas,

                    'dias_ventas_mensual' => $credito_cuantitativa_ingreso_adicional->dias_ventas_mensual,
                    'dias_compras_mensual' => $credito_cuantitativa_ingreso_adicional->dias_compras_mensual,

                    'credito_cobrando_venta_mensual' => $credito_cuantitativa_ingreso_adicional->credito_cobrando_venta_mensual,
                    'credito_porcentaje_venta_mensual' => $credito_cuantitativa_ingreso_adicional->credito_porcentaje_venta_mensual,
                    'contado_cobrando_venta_mensual' => $credito_cuantitativa_ingreso_adicional->contado_cobrando_venta_mensual,
                    'contado_porcentaje_venta_mensual' => $credito_cuantitativa_ingreso_adicional->contado_porcentaje_venta_mensual,
                    'credito_cobrando_compra_mensual' => $credito_cuantitativa_ingreso_adicional->credito_cobrando_compra_mensual,
                    'credito_porcentaje_compra_mensual' => $credito_cuantitativa_ingreso_adicional->credito_porcentaje_compra_mensual,
                    'contado_cobrando_compra_mensual' => $credito_cuantitativa_ingreso_adicional->contado_cobrando_compra_mensual,
                    'contado_porcentaje_compra_mensual' => $credito_cuantitativa_ingreso_adicional->contado_porcentaje_compra_mensual,
                    'adicional_fijo' => $credito_cuantitativa_ingreso_adicional->adicional_fijo,
                    'total_ingreso_adicional' => $credito_cuantitativa_ingreso_adicional->total_ingreso_adicional,
                    'comentario' => $credito_cuantitativa_ingreso_adicional->comentario,
                ]);
                }
              
                // Margen de venta
              
                $credito_cuantitativa_margen_venta = DB::table('credito_cuantitativa_margen_venta')->where('idcredito',$ultimocredito->id)->first();
        
                if($credito_cuantitativa_margen_venta!=''){
                DB::table('credito_cuantitativa_margen_venta')->insert([
                    'fecha' => Carbon::now(),
                    'idcredito' => $idcreditorefinanciado,
                    'tipo_registro' => $credito_cuantitativa_margen_venta->tipo_registro,
                    'productos' => $credito_cuantitativa_margen_venta->productos,
                    'total_venta' => $credito_cuantitativa_margen_venta->total_venta,
                    'total_compra' => $credito_cuantitativa_margen_venta->total_compra,
                    'porcentaje_margen' => $credito_cuantitativa_margen_venta->porcentaje_margen,
                    'frecuencia_ventas' => $credito_cuantitativa_margen_venta->frecuencia_ventas,
                    'dias' => $credito_cuantitativa_margen_venta->dias,
                    'venta_total_dias' => $credito_cuantitativa_margen_venta->venta_total_dias,
                    'numero_dias' => $credito_cuantitativa_margen_venta->numero_dias,

                    'venta_mensual' => $credito_cuantitativa_margen_venta->venta_mensual,
                    'recabo_dato_numero' => $credito_cuantitativa_margen_venta->recabo_dato_numero,
                    'recabo_dato_dia' => $credito_cuantitativa_margen_venta->recabo_dato_dia,
                    'recabo_dato_monto' => $credito_cuantitativa_margen_venta->recabo_dato_monto,
                    'estado_muestra' => $credito_cuantitativa_margen_venta->estado_muestra,
                    'margen_ventas' => $credito_cuantitativa_margen_venta->margen_ventas,
                    'subproducto' => $credito_cuantitativa_margen_venta->subproducto,
                    'productos_mensual' => $credito_cuantitativa_margen_venta->productos_mensual,
                    'total_venta_mensual' => $credito_cuantitativa_margen_venta->total_venta_mensual,
                    'total_compra_mensual' => $credito_cuantitativa_margen_venta->total_compra_mensual,
                    'porcentaje_margen_mensual' => $credito_cuantitativa_margen_venta->porcentaje_margen_mensual,
                    'semanas' => $credito_cuantitativa_margen_venta->semanas,
                    'venta_total_mensual' => $credito_cuantitativa_margen_venta->venta_total_mensual,
                    'estado_muestra_mensual' => $credito_cuantitativa_margen_venta->estado_muestra_mensual,
                    'margen_ventas_mensual' => $credito_cuantitativa_margen_venta->margen_ventas_mensual,
                    'subproductomensual' => $credito_cuantitativa_margen_venta->subproductomensual,
                    'margen_venta_calculado' => $credito_cuantitativa_margen_venta->margen_venta_calculado,
                ]);
                }
              
                // Resumida
                
                $credito_evaluacion_resumida = DB::table('credito_evaluacion_resumida')->where('idcredito',$ultimocredito->id)->first();
          
                if($credito_evaluacion_resumida!=''){
                DB::table('credito_evaluacion_resumida')->insert([
                    'idcredito' => $idcreditorefinanciado,
                    'fecha' => Carbon::now(),
                    'descripcion_actividad' => $credito_evaluacion_resumida->descripcion_actividad,
                    'idtipo_giro_economico' => $credito_evaluacion_resumida->idtipo_giro_economico,
                    'idgiro_economico_evaluacion' => $credito_evaluacion_resumida->idgiro_economico_evaluacion,
                    'ejercicio_giro_economico' => $credito_evaluacion_resumida->ejercicio_giro_economico,

                    'cantidad_cliente_natural' => $credito_evaluacion_resumida->cantidad_cliente_natural,
                    'cantidad_cliente_juridico' => $credito_evaluacion_resumida->cantidad_cliente_juridico,
                    'cantidad_pareja_natural' => $credito_evaluacion_resumida->cantidad_pareja_natural,
                    'cantidad_pareja_juridico' => $credito_evaluacion_resumida->cantidad_pareja_juridico,
                    'total_deuda' => $credito_evaluacion_resumida->total_deuda,

                    'cantidad_garante_natural' => $credito_evaluacion_resumida->cantidad_garante_natural,
                    'cantidad_garante_juridico' => $credito_evaluacion_resumida->cantidad_garante_juridico,
                    'cantidad_garante_pareja_natural' => $credito_evaluacion_resumida->cantidad_garante_pareja_natural,
                    'cantidad_garante_pareja_juridico' => $credito_evaluacion_resumida->cantidad_garante_pareja_juridico,
                    'total_deuda_garante' => $credito_evaluacion_resumida->total_deuda_garante,

                    'experiencia_microempresa' => $credito_evaluacion_resumida->experiencia_microempresa,
                    'tiempo_mismo_local' => $credito_evaluacion_resumida->tiempo_mismo_local,
                    'instalacion_local' => $credito_evaluacion_resumida->instalacion_local,
                    'nro_trabajador_completo' => $credito_evaluacion_resumida->nro_trabajador_completo,
                    'nro_trabajador_parcal' => $credito_evaluacion_resumida->nro_trabajador_parcal,

                    'referencia' => $credito_evaluacion_resumida->referencia,

                    'venta_diaria' => $credito_evaluacion_resumida->venta_diaria,
                    'venta_total_dias' => $credito_evaluacion_resumida->venta_total_dias,
                    'venta_semanal' => $credito_evaluacion_resumida->venta_semanal,
                    'venta_total_mensual' => $credito_evaluacion_resumida->venta_total_mensual,

                    'ingresos_gastos' => $credito_evaluacion_resumida->ingresos_gastos,
                    'ingresos_op_total' => $credito_evaluacion_resumida->ingresos_op_total,

                    'gasto_alimentacion' => $credito_evaluacion_resumida->gasto_alimentacion,
                    'gasto_educacion' => $credito_evaluacion_resumida->gasto_educacion,
                    'gasto_vestimenta' => $credito_evaluacion_resumida->gasto_vestimenta,
                    'gasto_transporte' => $credito_evaluacion_resumida->gasto_transporte,
                    'gasto_salud' => $credito_evaluacion_resumida->gasto_salud,
                    'gasto_vivienda' => $credito_evaluacion_resumida->gasto_vivienda,
                    'total_servicios' => $credito_evaluacion_resumida->total_servicios,
                    'gasto_agua' => $credito_evaluacion_resumida->gasto_agua,
                    'gasto_luz' => $credito_evaluacion_resumida->gasto_luz,
                    'gasto_telefono_internet' => $credito_evaluacion_resumida->gasto_telefono_internet,
                    'gasto_celular' => $credito_evaluacion_resumida->gasto_celular,
                    'gasto_cable' => $credito_evaluacion_resumida->gasto_cable,
                    'gasto_otros' => $credito_evaluacion_resumida->gasto_otros,
                    'gasto_total' => $credito_evaluacion_resumida->gasto_total,

                    'idforma_pago_credito' => $credito_evaluacion_resumida->idforma_pago_credito,
                    'propuesta_cuotas' => $credito_evaluacion_resumida->propuesta_cuotas,
                    'propuesta_monto' => $credito_evaluacion_resumida->propuesta_monto,
                    'propuesta_tem' => $credito_evaluacion_resumida->propuesta_tem,

                    'propuesta_servicio_otros' => $credito_evaluacion_resumida->propuesta_servicio_otros,
                    'propuesta_cargos' => $credito_evaluacion_resumida->propuesta_cargos,
                    'propuesta_total_pagar' => $credito_evaluacion_resumida->propuesta_total_pagar,
                    'total_propuesta' => $credito_evaluacion_resumida->total_propuesta,

                    'detalle_destino_prestamo' => $credito_evaluacion_resumida->detalle_destino_prestamo,
                    'fortalezas_negocio' => $credito_evaluacion_resumida->fortalezas_negocio,

                    'relacion_cuota_venta_diaria' => $credito_evaluacion_resumida->relacion_cuota_venta_diaria,
                    'relacion_cuota_venta_semanal' => $credito_evaluacion_resumida->relacion_cuota_venta_semanal,
                    'relacion_cuota_venta_quincenal' => $credito_evaluacion_resumida->relacion_cuota_venta_quincenal,
                    'relacion_cuota_venta_mensual' => $credito_evaluacion_resumida->relacion_cuota_venta_mensual,

                    'estado_indicador_solvencia' => $credito_evaluacion_resumida->estado_indicador_solvencia,
                    'estado_indicador_cuota_ingreso' => $credito_evaluacion_resumida->estado_indicador_cuota_ingreso,
                    'estado_indicador_cuota_venta_diario' => $credito_evaluacion_resumida->estado_indicador_cuota_venta_diario,
                    'estado_indicador_cuota_venta_semanal' => $credito_evaluacion_resumida->estado_indicador_cuota_venta_semanal,
                    'estado_indicador_cuota_venta_quincenal' => $credito_evaluacion_resumida->estado_indicador_cuota_venta_quincenal,
                    'estado_indicador_cuota_venta_mensual' => $credito_evaluacion_resumida->estado_indicador_cuota_venta_mensual,
                    'estado_credito_general' => $credito_evaluacion_resumida->estado_credito_general,

                    'indicador_solvencia_excedente' => $credito_evaluacion_resumida->indicador_solvencia_excedente,
                    'indicador_solvencia_cuotas' => $credito_evaluacion_resumida->indicador_solvencia_cuotas,
                    'relacion_cuota_mensual' => $credito_evaluacion_resumida->relacion_cuota_mensual,
                ]);
                }
              
                // Flujo de caja
              
                $credito_flujo_caja = DB::table('credito_flujo_caja')->where('idcredito',$ultimocredito->id)->first();
          
                if($credito_flujo_caja!=''){
                DB::table('credito_flujo_caja')->insert([
                    'idcredito' => $idcreditorefinanciado,
                    'fecha' => Carbon::now(),
                    'encabezado' => $credito_flujo_caja->encabezado,
                    'evaluacion_meses' => $credito_flujo_caja->evaluacion_meses,
                    'flujo_caja' => $credito_flujo_caja->flujo_caja,
                    'entidad_reguladas' => $credito_flujo_caja->entidad_reguladas,
                    'linea_credito' => $credito_flujo_caja->linea_credito,
                    'entidad_noregulada' => $credito_flujo_caja->entidad_noregulada,
                    'comentarios' => $credito_flujo_caja->comentarios,
                ]);
                }
              
                // Evaluación
              
                $credito_formato_evaluacion = DB::table('credito_formato_evaluacion')->where('idcredito',$ultimocredito->id)->first();
          
                if($credito_formato_evaluacion!=''){
                DB::table('credito_formato_evaluacion')->insert([
                    'idcredito' => $idcreditorefinanciado,
                    'fecha' => Carbon::now(),
                    'remuneracion_total_cliente' => $credito_formato_evaluacion->remuneracion_total_cliente,
                    'remuneracion_variable' => $credito_formato_evaluacion->remuneracion_variable,
                    'remuneracion_pareja' => $credito_formato_evaluacion->remuneracion_pareja,
                    'adicional_ingreso_mensual' => $credito_formato_evaluacion->adicional_ingreso_mensual,
                    'total_ingresos_mensuales' => $credito_formato_evaluacion->total_ingresos_mensuales,
                    'numero_total_hijos' => $credito_formato_evaluacion->numero_total_hijos,
                    'total_hijos_dependientes' => $credito_formato_evaluacion->total_hijos_dependientes,

                    'pago_cuotas_deuda' => $credito_formato_evaluacion->pago_cuotas_deuda,
                    'monto_alimentacion' => $credito_formato_evaluacion->monto_alimentacion,
                    'monto_salud' => $credito_formato_evaluacion->monto_salud,
                    'monto_educacion' => $credito_formato_evaluacion->monto_educacion,
                    'monto_alquiler_vivienda' => $credito_formato_evaluacion->monto_alquiler_vivienda,
                    'monto_mobilidad' => $credito_formato_evaluacion->monto_mobilidad,
                    'monto_luz' => $credito_formato_evaluacion->monto_luz,
                    'monto_agua' => $credito_formato_evaluacion->monto_agua,
                    'monto_telefono' => $credito_formato_evaluacion->monto_telefono,
                    'monto_cable' => $credito_formato_evaluacion->monto_cable,
                    'otros_gastos_personales' => $credito_formato_evaluacion->otros_gastos_personales,
                    'monto_pension_alimentos' => $credito_formato_evaluacion->monto_pension_alimentos,
                    'adicional_egresos_mensual' => $credito_formato_evaluacion->adicional_egresos_mensual,
                    'total_egresos_mensuales' => $credito_formato_evaluacion->total_egresos_mensuales,
                    'excedente_mensual_disponible' => $credito_formato_evaluacion->excedente_mensual_disponible,

                    'deudas_financieras' => $credito_formato_evaluacion->deudas_financieras,
                    'saldo_capita_cliente' => $credito_formato_evaluacion->saldo_capita_cliente,
                    'couta_mensual_cliente' => $credito_formato_evaluacion->couta_mensual_cliente,
                    'cuota_ampliacion_cliente' => $credito_formato_evaluacion->cuota_ampliacion_cliente,
                    'saldo_capita_pareja' => $credito_formato_evaluacion->saldo_capita_pareja,
                    'couta_mensual_pareja' => $credito_formato_evaluacion->couta_mensual_pareja,
                    'cuota_ampliacion_pareja' => $credito_formato_evaluacion->cuota_ampliacion_pareja,
                    'total_saldo_capital' => $credito_formato_evaluacion->total_saldo_capital,
                    'total_couta_mensual' => $credito_formato_evaluacion->total_couta_mensual,
                    'total_couta_ampliacion' => $credito_formato_evaluacion->total_couta_ampliacion,
                    'entidad_financiera_cliente' => $credito_formato_evaluacion->entidad_financiera_cliente,
                    'entidad_financiera_pareja' => $credito_formato_evaluacion->entidad_financiera_pareja,
                    'entidad_financiera_total' => $credito_formato_evaluacion->entidad_financiera_total,

                    'idforma_pago_credito' => $credito_formato_evaluacion->idforma_pago_credito,
                    'propuesta_cuotas' => $credito_formato_evaluacion->propuesta_cuotas,
                    'propuesta_monto' => $credito_formato_evaluacion->propuesta_monto,
                    'propuesta_tem' => $credito_formato_evaluacion->propuesta_tem,
                    'propuesta_servicio_otros' => $credito_formato_evaluacion->propuesta_servicio_otros,
                    'propuesta_cargos' => $credito_formato_evaluacion->propuesta_cargos,
                    'propuesta_total_pagar' => $credito_formato_evaluacion->propuesta_total_pagar,
                    'total_propuesta' => $credito_formato_evaluacion->total_propuesta,

                    'resultado_cuota_excedente' => $credito_formato_evaluacion->resultado_cuota_excedente,
                    'estado_evaluacion' => $credito_formato_evaluacion->estado_evaluacion,

                    'referencia' => $credito_formato_evaluacion->referencia,

                    'comentario_centro_laboral' => $credito_formato_evaluacion->comentario_centro_laboral,
                    'comentario_capacidad_pago' => $credito_formato_evaluacion->comentario_capacidad_pago,
                    'sustento_historial_pago' => $credito_formato_evaluacion->sustento_historial_pago,
                    'sustento_destino_credito' => $credito_formato_evaluacion->sustento_destino_credito,
                ]);
                }
              
                // Propuesta
              
                $credito_propuesta = DB::table('credito_propuesta')->where('idcredito',$ultimocredito->id)->first();
              
                if($credito_propuesta!=''){
                DB::table('credito_propuesta')->insert([
                    'idcredito' => $idcreditorefinanciado,
                    'fecha' => Carbon::now(),
                    'monto_compra_deuda' => $credito_propuesta->monto_compra_deuda,

                    'idclasificacion_cliente' => $credito_propuesta->idclasificacion_cliente,
                    'idclasificacion_cliente_pareja' => $credito_propuesta->idclasificacion_cliente_pareja,
                    'idclasificacion_aval' => $credito_propuesta->idclasificacion_aval,
                    'idclasificacion_aval_pareja' => $credito_propuesta->idclasificacion_aval_pareja,

                    'detalle_monto_compra_deuda' => $credito_propuesta->detalle_monto_compra_deuda,
                    'neto_destino_credito' => $credito_propuesta->neto_destino_credito,
                    'fenomenos' => $credito_propuesta->fenomenos,

                    'rentabilidad_patrimonial_res_coment' => $credito_propuesta->rentabilidad_patrimonial_res_coment,
                    'rentabilidad_activos_res_coment' => $credito_propuesta->rentabilidad_activos_res_coment,
                    'solvencia_cuota_total_res_coment' => $credito_propuesta->solvencia_cuota_total_res_coment,
                    'solvencia_capital_trabajo_res_coment' => $credito_propuesta->solvencia_capital_trabajo_res_coment,
                    'limites_financiamiento_vru_res_coment' => $credito_propuesta->limites_financiamiento_vru_res_coment,
                    'limites_numero_entidades' => $credito_propuesta->limites_numero_entidades,
                    'limites_numero_entidades_res' => $credito_propuesta->limites_numero_entidades_res,
                    'limites_numero_entidades_res_coment' => $credito_propuesta->limites_numero_entidades_res_coment,
                    'res_solvencia_relacion_cuota_coment' => $credito_propuesta->res_solvencia_relacion_cuota_coment,
                    'res_ratios_tendencia_comportamiento_res_coment' => $credito_propuesta->res_ratios_tendencia_comportamiento_res_coment,
                ]);
                }
              
                // Inventarios activos
              
                $credito_cuantitativa_inventario = DB::table('credito_cuantitativa_inventario')->where('idcredito',$ultimocredito->id)->first();
          
                if($credito_cuantitativa_inventario){
                  DB::table('credito_cuantitativa_inventario')->insert([
                    'fecha' => Carbon::now(),
                    'idcredito' => $idcreditorefinanciado,
                    'inventario' => $credito_cuantitativa_inventario->inventario,
                    'total_inventario' => $credito_cuantitativa_inventario->total_inventario,
                    'inmuebles' => $credito_cuantitativa_inventario->inmuebles,
                    'total_inmuebles' => $credito_cuantitativa_inventario->total_inmuebles,
                    'muebles' => $credito_cuantitativa_inventario->muebles,
                    'total_muebles' => $credito_cuantitativa_inventario->total_muebles,
                  ]);
                }
          
            }
          
            
          
            // APROBAR CREDITO
          
            DB::table('credito')->whereId($idcreditorefinanciado)->update([
              'estado' => 'PROCESO',
              'fecha_proceso' => Carbon::now(),
              'cuenta' => 0,
              'config_dias_tolerancia' => configuracion($idtienda,'dias_tolerancia')['valor'],
              'config_dias_tolerancia_garantia' => configuracion($idtienda,'dias_tolerancia_garantia')['valor'],
              'config_dias_maximo_penalidad' => configuracion($idtienda,'dias_maximo_penalidad')['valor'],
              'config_penalidad_couta_simple' => configuracion($idtienda,'penalidad_couta_simple')['valor'],
              'config_penalidad_couta_compuesto' => configuracion($idtienda,'penalidad_couta_compuesto')['valor'],
              'config_penalidad_couta_simple_noprendaria' => configuracion($idtienda,'penalidad_couta_simple_noprendaria')['valor'],
              'config_penalidad_couta_compuesto_noprendaria' => configuracion($idtienda,'penalidad_couta_compuesto_noprendaria')['valor'],
              'config_tasa_moratoria' => configuracion($idtienda,'tasa_moratoria')['valor'],
            ]);
            
            //actualizar cronograma
             
            $creditorefinanciado = DB::table('credito')
                  ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                  ->where('credito.id',$idcreditorefinanciado)
                  ->select(
                      'credito.*',
                      'credito_prendatario.modalidad as modalidad_calculo',
                  )
                  ->first();
              
            $tasatarifario = DB::table('tarifario')
                  ->where('tarifario.idcredito_prendatario',$creditorefinanciado->idcredito_prendatario)
                  ->where('tarifario.idforma_pago_credito',$creditorefinanciado->idforma_pago_credito)
                  ->where('tarifario.monto','>=',$creditorefinanciado->monto_solicitado)
                  ->where('tarifario.cuotas','>=',$creditorefinanciado->cuotas)
                  ->orderBy('tarifario.cuotas','asc')
                  ->orderBy('tarifario.monto','asc')
                  ->limit(1)
                  ->first();
          
            $comision_cargo = 0;
            if($tasatarifario!=''){
                $comision_cargo = $tasatarifario->cargos_otros;
            }
          
            $fechaactual = Carbon::now()->format('Y-m-d');
            $cronograma = genera_cronograma(
                  $creditorefinanciado->monto_solicitado,
                  $creditorefinanciado->cuotas,
                  $fechaactual,
                  $creditorefinanciado->idforma_pago_credito,
                  $creditorefinanciado->tasa_tip,
                  $creditorefinanciado->modalidad_calculo == 'Interes Simple' ? 1 : 2,
                  $creditorefinanciado->dia_gracia,
                  $comision_cargo,
                  $creditorefinanciado->cargo
            );
          
          
            DB::table('credito')->whereId($idcreditorefinanciado)->update([
                'fecha'                     => $fechaactual,
                'cuota_pago'                => $cronograma['cuota_pago'],
                'fecha_primerpago'          => $cronograma['fechainicio'],
                'fecha_ultimopago'          => $cronograma['ultimafecha'],
                'total_propuesta'           => $cronograma['total_propuesta'],
                'cuota_comision'            => $cronograma['cuota_comision'],
                'cuota_cargo'               => $cronograma['cuota_cargo'],
                'cuota_comisioncargo'       => $cronograma['cuota_comisioncargo'],
                'total_comision'            => $cronograma['total_comision'],
                'total_cargo'               => $cronograma['total_cargo'],
                'total_comisioncargo'       => $cronograma['total_comisioncargo'],
            ]);
          
            DB::table('credito_cronograma')->where('idcredito',$idcreditorefinanciado)->delete();

            foreach($cronograma['cronograma'] as $value){
                DB::table('credito_cronograma')->insert([
                  'numerocuota'     => $value['numero'],
                  'fechapago'       => $value['fechanormal'],
                  'capital'         => $value['saldo'],
                  'amortizacion'    => $value['amortizacion'],
                  'interes'         => $value['interes'],
                  'cuotapagar'      => 0,
                  'cuota_real'      => $value['cuotafinal'],
                  'resto_redondeo'  => 0,
                  'comision'        => $value['comision'],
                  'cargo'           => $value['cargo'],
                  'comision_cargo'  => $value['comisioncargo'],
                  'idestadocredito_cronograma' => 1,
                  'idcredito'       => $idcreditorefinanciado,
                ]);
            }
          
            
           
            if($creditorefinanciado->idforma_credito==1){
                
                $cliente = DB::table('users')->whereId($creditorefinanciado->idcliente)->first();
                //depositario
                DB::table('credito')->whereId($creditorefinanciado->id)->update([
                  'custodiagarantia_id' => $cliente->custodiagarantia_id,
                  'custodiagarantia_nombre' => $cliente->custodiagarantia_nombre,
                  'gd_nombre' => $cliente->gd_nombre,
                  'gd_doeruc' => $cliente->gd_doeruc,
                  'gd_direccion' => $cliente->gd_direccion,
                  'gd_representante_doeruc' => $cliente->gd_representante_doeruc,
                  'gd_representante_nombre' => $cliente->gd_representante_nombre,
                  'constituciongarantia_id' => $cliente->constituciongarantia_id,
                  'constituciongarantia_nombre' => $cliente->constituciongarantia_nombre,
                ]);

                //poliza de seguros
                $credito_polizaseguro = DB::table('credito_polizaseguro')->where('id_cliente',$creditorefinanciado->idcliente)->get();
                DB::table('credito_polizaseguro_prestamo')->where('id_credito',$creditorefinanciado->id)->delete();
                foreach($credito_polizaseguro as $value){
                    DB::table('credito_polizaseguro_prestamo')
                        ->insert([
                            'numero_poliza' => $value->numero_poliza,
                            'aseguradora' => $value->aseguradora,
                            'prima_recio' => $value->prima_recio,
                            'beneficiario' => $value->beneficiario,
                            'asegurado' => $value->asegurado,
                            'tomador' => $value->tomador,
                            'vigencia_desde' => $value->vigencia_desde,
                            'vigencia_hasta' => $value->vigencia_hasta,
                            'id_credito' => $creditorefinanciado->id,
                        ]);
                }
                
                // representante comun
                $credito_representantecomun = DB::table('credito_representantecomun')->where('estado_id',1)->get();
                DB::table('credito_representantecomun_prestamo')->where('id_credito',$creditorefinanciado->id)->delete();
                foreach($credito_representantecomun as $value){
                    DB::table('credito_representantecomun_prestamo')
                        ->insert([
                            'nombre' => $value->nombre,
                            'doi' => $value->doi,
                            'direccion' => $value->direccion,
                            'ubigeo_id' => $value->ubigeo_id,
                            'ubigeo_nombre' => $value->ubigeo_nombre,
                            'estado_id' => $value->estado_id,
                            'estado_nombre' => $value->estado_nombre,
                            'id_credito' => $creditorefinanciado->id,
                        ]);
                }
              
            }else{

                DB::table('credito')->whereId($creditorefinanciado->id)->update([
                  'custodiagarantia_id' => 0,
                  'custodiagarantia_nombre' => '',
                  'gd_nombre' => '',
                  'gd_doeruc' => '',
                  'gd_direccion' => '',
                  'gd_representante_doeruc' => '',
                  'gd_representante_nombre' => '',
                  'constituciongarantia_id' => 0,
                  'constituciongarantia_nombre' => '',
                ]);
              
                DB::table('credito_polizaseguro_prestamo')->where('id_credito',$creditorefinanciado->id)->delete();
                DB::table('credito_representantecomun_prestamo')->where('id_credito',$creditorefinanciado->id)->delete();
            }
          
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
