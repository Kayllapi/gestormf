<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class DesembolsadoController extends Controller
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
          
            return view(sistema_view().'/desembolsado/tabla',[
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
          if($request->tipo=='asesor'){
              $where[] = ['credito.fecha_desembolso','>=',Carbon::now()->format('Y-m-d').' 00:00:00'];
              $where[] = ['credito.fecha_desembolso','<=',Carbon::now()->format('Y-m-d').' 23:59:59'];
              $where2[] = ['credito.fecha_desembolso','>=',Carbon::now()->format('Y-m-d').' 00:00:00'];
              $where2[] = ['credito.fecha_desembolso','<=',Carbon::now()->format('Y-m-d').' 23:59:59'];
          }else{
              if($request->inicio!='' and $request->fin!=''){
                  $where[] = ['credito.fecha_desembolso','>=',$request->inicio.' 00:00:00'];
                  $where[] = ['credito.fecha_desembolso','<=',$request->fin.' 23:59:59'];
                  $where2[] = ['credito.fecha_desembolso','>=',$request->inicio.' 00:00:00'];
                  $where2[] = ['credito.fecha_desembolso','<=',$request->fin.' 23:59:59'];
              }
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
              ->where($where)
              ->orWhere($where2)
              ->where('credito.estado','DESEMBOLSADO')
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
          $total_refinanciado = 0;
          $total_neto = 0;
          foreach($creditos as $key => $value){
            
              $credito_formapago = DB::table('credito_formapago')->where('credito_formapago.idcredito',$value->id)->first();
              $operacionen = '';
              $idformapago = 0;
              if($credito_formapago){
                  if($credito_formapago->idformapago==1){
                      $operacionen = 'CAJA';
                  }elseif($credito_formapago->idformapago==2){
                      $operacionen = 'BANCO';
                  }
                  $idformapago = $credito_formapago->idformapago;
              }
            
                  
                
              $btn_validar = '';
              if($idformapago==2){
                  $btn_validar = "<button type='button' class='btn btn-success' onclick='validar({$value->id})'><i class='fa-solid fa-check'></i> Validar</button>";
                  if($value->validar_estado==1){
                      $users = DB::table('users')->whereId($value->validar_responsable)->first();
                      $btn_validar = "<i class='fa-solid fa-check'></i> (".$users->codigo.")";
                  }
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
                            <td>{$btn_validar}</td>
                            <td>{$value->nombremodalidadcredito}</td>
                            <td>{$value->codigoasesor}</td>
                            <td>
                              <div class='dropdown' id='menu-opcion'>
                                <button class='btn btn-primary dropdown-toggle'  type='button' data-bs-toggle='dropdown' aria-expanded='false'>Opción</button>
                                <ul class='dropdown-menu dropdown-menu-end'>
                                  <li>
                                    <a class='dropdown-item' href='javascript:;' data-valor-columna='{$value->id}' onclick='show_data(this)'>
                                      <i class='fa fa-money-bill'></i> Garantia, Cronograma y Evaluación
                                    </a>
                                  </li>
                                </ul>
                              </div>
                            </td>
                        </tr>";
              if($value->nombremodalidadcredito=='Regular'){
                  $total_neto += $value->monto_solicitado;
              }elseif($value->nombremodalidadcredito=='Refinanciado'){
                  $total_refinanciado += $value->monto_solicitado;
              }
              $total_desembolsado += $value->monto_solicitado;
          }
          if(count($creditos)==0){
              $html.= '<tr><td colspan="17" style="text-align: center;font-weight: bold;">No hay ningún dato!!</td></tr>';
          }
              $html .= '
                <tr style="position: sticky;bottom: 0;">
                  <td colspan="3" style="background-color: #144081 !important;text-align:right;color:#fff !important;">TOTAL GENERAL S/.</td>
                  <td style="background-color: #144081 !important;text-align:right;color:#fff !important;">'.number_format($total_desembolsado, 2, '.', '').'</td>
                  <td colspan="2" style="background-color: #144081 !important;text-align:right;color:#fff !important;">TOTAL REFINANCIADO S/.: '.number_format($total_refinanciado, 2, '.', '').'</td>
                  <td colspan="2" style="background-color: #144081 !important;text-align:right;color:#fff !important;">TOTAL NETO S/.: '.number_format($total_neto, 2, '.', '').'</td>
                  <td colspan="5" style="background-color: #144081 !important;"></td>
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
                    /*->join('tarifario','tarifario.id','credito.idtarifario')
                    ->join('credito_prendatario','credito_prendatario.id','tarifario.idcredito_prendatario')*/
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
                        /*'tarifario.monto as monto_max_credito',
                        'tarifario.cuotas as coutas_max_credito',
                        'tarifario.tem as tem_producto',
                        'tarifario.tipo_producto_credito as tipo_producto_credito',*/
                        'credito_prendatario.nombre as nombreproductocredito',
                        'credito_prendatario.modalidad as modalidad_calculo',
                        'credito_prendatario.conevaluacion as conevaluacion',
                    )
                    ->orderBy('credito.id','desc')
                    ->first();

      if( $request->input('view') == 'desembolsar' ){

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
        
        return view(sistema_view().'/desembolsado/desembolsar',[
              'tienda' => $tienda,
              'credito' => $credito,
              'usuario' => $usuario,
              'nivel_aprobacion' => $nivel_aprobacion,
              'credito_aprobacion' => $credito_aprobacion,
              'estado' => $request->input('tipo'),
              'garantias' => $garantias,
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
            return view(sistema_view().'/desembolsado/validar',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
                'idcredito' => $id,
            ]);
        }
        else if($request->input('view') == 'exportar') {
            return view(sistema_view().'/desembolsado/exportar',[
                'tienda' => $tienda,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'idagencia' => $request->idagencia,
                'idcliente' => $request->idcliente,
                'idasesor' => $request->idasesor,
                'tipo' => $request->tipo,
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
          
          if($request->tipo=='asesor'){
              $where[] = ['credito.fecha_desembolso','>=',Carbon::now()->format('Y-m-d').' 00:00:00'];
              $where[] = ['credito.fecha_desembolso','<=',Carbon::now()->format('Y-m-d').' 23:59:59'];
              $where2[] = ['credito.fecha_desembolso','>=',Carbon::now()->format('Y-m-d').' 00:00:00'];
              $where2[] = ['credito.fecha_desembolso','<=',Carbon::now()->format('Y-m-d').' 23:59:59'];
          }else{
              if($request->fecha_inicio!='' and $request->fecha_fin!=''){
                  $where[] = ['credito.fecha_desembolso','>=',$request->fecha_inicio.' 00:00:00'];
                  $where[] = ['credito.fecha_desembolso','<=',$request->fecha_fin.' 23:59:59'];
                  $where2[] = ['credito.fecha_desembolso','>=',$request->fecha_inicio.' 00:00:00'];
                  $where2[] = ['credito.fecha_desembolso','<=',$request->fecha_fin.' 23:59:59'];
              }
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
              ->where($where)
              ->orWhere($where2)
              ->where('credito.estado','DESEMBOLSADO')
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
          
            $agencia = DB::table('tienda')->whereId($request->idagencia)->first();
        
            $pdf = PDF::loadView(sistema_view().'/desembolsado/exportar_pdf',[
                'tienda' => $tienda,
                'agencia' => $agencia,
                'creditos' => $creditos,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
            ]); 
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('CREDITO_DESEMBOLSADO.pdf');
        }  
      /*
      else if( $request->input('view') == 'pdf_cronograma' ){

        $credito_cronograma = DB::table('credito_cronograma')
                              ->where('credito_cronograma.idcredito',$credito->id)
                              ->get();
        
        $pdf = PDF::loadView(sistema_view().'/desembolsado/pdf_cronograma',[
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

        $ubigeo_tienda = DB::table('ubigeo')->where('ubigeo.id',$tienda->idubigeo)->first();
        $garantias = DB::table('credito_garantia')->where('idcredito', $credito->id)->get();
        $pdf = PDF::loadView(sistema_view().'/desembolsado/pdf_contrato',[
            'users_prestamo'    => $users_prestamo,
            'tienda' => $tienda,
            'credito' => $credito,
            'usuario' => $usuario,
            'asesor' => $asesor,
            'garantias' => $garantias,
            'ubigeo_tienda' => $ubigeo_tienda,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('CONTRATO.pdf');
      }
      else if( $request->input('view') == 'pdf_resumen' ){

        $tipo_garantia1 = DB::table('tipo_garantia')->offset(0)->limit(3)->get();
        $tipo_garantia2 = DB::table('tipo_garantia')->offset(3)->limit(3)->get();
        $tipo_garantia3 = DB::table('tipo_garantia')->offset(6)->limit(3)->get();
        $garantias = DB::table('credito_garantia')->where('idcredito', $credito->id)->get();
        $pdf = PDF::loadView(sistema_view().'/desembolsado/pdf_resumen',[
            'users_prestamo'    => $users_prestamo,
            'tienda' => $tienda,
            'credito' => $credito,
            'usuario' => $usuario,
            'asesor' => $asesor,
            'garantias' => $garantias,
            'tipo_garantia1' => $tipo_garantia1,
            'tipo_garantia2' => $tipo_garantia2,
            'tipo_garantia3' => $tipo_garantia3,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('RESUMEN.pdf');
      }
      else if( $request->input('view') == 'pdf_declaracion' ){

        $garantias = DB::table('credito_garantia')->where('idcredito', $credito->id)->get();
        $pdf = PDF::loadView(sistema_view().'/desembolsado/pdf_declaracion',[
            'users_prestamo'    => $users_prestamo,
            'tienda' => $tienda,
            'credito' => $credito,
            'usuario' => $usuario,
            'asesor' => $asesor,
            'garantias' => $garantias,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('DECLARACION.pdf');
      }
      
      else if( $request->input('view') == 'pdf_ticket' ){

        $cajero = DB::table('users')->where('users.id',$credito->idcajero)->first();
        $garantias = DB::table('credito_garantia')->where('idcredito', $credito->id)->get();
        $pdf = PDF::loadView(sistema_view().'/desembolsado/pdf_ticket',[
            'users_prestamo'    => $users_prestamo,
            'tienda' => $tienda,
            'credito' => $credito,
            'usuario' => $usuario,
            'asesor' => $asesor,
            'cajero' => $cajero,
            'garantias' => $garantias,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('DECLARACION.pdf');
      }*/
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        if($request->input('view') == 'validar'){
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
          
            DB::table('credito')->whereId($id)->update([
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
