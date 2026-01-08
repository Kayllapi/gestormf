<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use PDF;

class PropuestaCreditoController extends Controller
{
    public function __construct()
    {
        $this->tipo_credito = DB::table('tipo_credito')->get();
        $this->modalidad_credito = DB::table('modalidad_credito')->get();
        $this->forma_credito = DB::table('forma_credito')->get();
        $this->tipo_operacion_credito = DB::table('tipo_operacion_credito')->get();
        $this->forma_pago_credito = DB::table('forma_pago_credito')->get();
        $this->tipo_destino_credito = DB::table('tipo_destino_credito')->get();
        $this->giro_economico_evaluacion = DB::table('giro_economico_evaluacion')->get();
        $this->tipo_giro_economico = DB::table('tipo_giro_economico')->get();
        $this->f_tiporeferencia = DB::table('f_tiporeferencia')->get();
        $this->unidadmedida_credito = DB::table('unidadmedida_credito')->get();
        $this->tipo_credito_evaluacion = DB::table('tipo_credito_evaluacion')->get();
        $this->calificacion_cliente = DB::table('calificacion_cliente')->get();
        $this->fenomenos = DB::table('fenomenos')->where('fenomenos.estado','HABILITADO')->get();
    }
    public function index(Request $request,$idtienda)
    {
      
        // ACTUALIZAR e eliminar durante el dia
        $creditos = DB::table('credito')
            ->whereIn('credito.estado',['PROCESO','APROBADO'])
            ->orderBy('credito.id','asc')
            ->get();
        $fecha = Carbon::now();
        foreach($creditos as $value){
            $ultimafecha = date_format(date_create($value->fecha_proceso),"Y-m-d").' 23:59:59';
            if($fecha>=$ultimafecha){
              
                /*$credito = DB::table('credito')
                    ->whereId($value->idcredito_refinanciado)
                    ->first();*/
              
                //if($value->idmodalidad_credito==4){ // refinanciado
                  
                    /*DB::table('credito')
                      ->whereId($value->idcredito_refinanciado)
                      ->update([
                          'idestadocredito'  => 1,
                    ]);
                  
                    // restaurar garantias
                    DB::table('credito_garantia')
                      ->where('credito_garantia.idcredito',$value->idcredito_refinanciado)
                      ->update([
                          'idestadoentrega' => 1,
                    ]);*/
                  
                    /*DB::table('credito')->whereId($value->id)->update([
                      'fecha_eliminado' => Carbon::now(),
                      'idadministrador' => Auth::user()->id,
                      'idcredito_refinanciado'  => 0,
                      'estado' => 'ELIMINADO',
                    ]);
                  
                    DB::table('credito_aprobacion')->where('idcredito',$value->id)->delete();
                    DB::table('credito_formapago')->where('idcredito',$value->id)->delete();*/
                  
                //}else{

                    DB::table('credito')->whereId($value->id)->update([
                      'aprobacion_tipo_validacion' => '',
                      'aprobacion_nivel_validacion' => 0,
                      //'idadministrador' => Auth::user()->id,
                      'estado' => 'PENDIENTE',
                    ]);
                    DB::table('credito_aprobacion')->where('idcredito',$value->id)->delete();
                    DB::table('credito_formapago')->where('idcredito',$value->id)->delete();
                //}
                    
            }
        }
        // FIN ACTUALIZAR 
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->input('view') == 'tabla'){
            $agencias = DB::table('tienda')->get();
            return view(sistema_view().'/propuestacredito/tabla',[
              'tienda' => $tienda,
              'agencias' => $agencias,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->view == 'registrar') {
            return view(sistema_view().'/propuestacredito/create',[
              'tienda' => $tienda,
              'modalidad_credito' => $this->modalidad_credito,
              'tipo_operacion_credito' => $this->tipo_operacion_credito,
              'forma_credito' => $this->forma_credito,
              'tipo_destino_credito' => $this->tipo_destino_credito,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
      
        
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showtable'){
          
          $where = [];
          $where[] = ['credito.idtienda',$request->input('idagencia')];
          $where[] = ['credito.estado','<>','ELIMINADO'];
          
          if($request->input('estado')=='PROCESO'){
              $where[] = ['credito.estado',$request->input('estado')];
              $where[] = ['credito.fecha_proceso','>=',$request->input('inicio').' 00:00:00'];
              $where[] = ['credito.fecha_proceso','<=',$request->input('fin').' 23:59:59'];
          }
          elseif($request->input('estado')=='APROBADO'){
              $where[] = ['credito.estado',$request->input('estado')];
              $where[] = ['credito.fecha_aprobacion','>=',$request->input('inicio').' 00:00:00'];
              $where[] = ['credito.fecha_aprobacion','<=',$request->input('fin').' 23:59:59'];
          }
          elseif($request->input('estado')=='DESAPROBADO'){
              $where[] = ['credito.estado',$request->input('estado')];
              $where[] = ['credito.fecha_desaprobacion','>=',$request->input('inicio').' 00:00:00'];
              $where[] = ['credito.fecha_desaprobacion','<=',$request->input('fin').' 23:59:59'];
          }
          elseif($request->input('estado')=='DESEMBOLSADO'){
              $where[] = ['credito.idestadocredito',1];
              $where[] = ['credito.estado',$request->input('estado')];
              $where[] = ['credito.fecha_desembolso','>=',$request->input('inicio').' 00:00:00'];
              $where[] = ['credito.fecha_desembolso','<=',$request->input('fin').' 23:59:59'];
          }
          elseif($request->input('estado')=='CANCELADO'){
              $where[] = ['credito.idestadocredito',2];
              $where[] = ['credito.estado','DESEMBOLSADO'];
              $where[] = ['credito.fecha_cancelado','>=',$request->input('inicio').' 00:00:00'];
              $where[] = ['credito.fecha_cancelado','<=',$request->input('fin').' 23:59:59'];
          }
          
          $creditos = DB::table('credito')
                            ->join('users as cliente','cliente.id','credito.idcliente')
                            ->join('users as asesor','asesor.id','credito.idasesor')
                            ->leftjoin('users as aval','aval.id','credito.idaval')
                            ->join('modalidad_credito','modalidad_credito.id','credito.idmodalidad_credito')
                            ->join('tipo_operacion_credito','tipo_operacion_credito.id','credito.idtipo_operacion_credito')
                            // ->join('tarifario','tarifario.id','credito.idtarifario')
                            ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                            ->where($where)
                            ->select(
                                'credito.*',
                                'cliente.nombrecompleto as nombrecliente',
                                'asesor.usuario as nombreasesor',
                                'aval.nombrecompleto as nombreaval',
                                'credito_prendatario.nombre as nombreproductocredito',
                                'modalidad_credito.nombre as nombremodalidadcredito' , 
                            )
                            ->orderBy('credito.id','asc')
                            ->get();
          
          $html = '';
          foreach($creditos as $key => $value){
              $fecha = '';
              if($value->estado=='PROCESO'){
                  $fecha = $value->fecha_proceso!=''?date_format(date_create($value->fecha_proceso),"d/m/Y h:i A"):'';
              }
              elseif($value->estado=='APROBADO'){
                  $fecha = $value->fecha_aprobacion!=''?date_format(date_create($value->fecha_aprobacion),"d/m/Y h:i A"):'';
              }
              elseif($value->estado=='DESAPROBADO'){
                  $fecha = $value->fecha_desaprobacion!=''?date_format(date_create($value->fecha_desaprobacion),"d/m/Y h:i A"):'';
              }
              elseif($value->estado=='DESEMBOLSADO'){
                  if($value->idestadocredito==2){
                      $value->estado = 'CANCELADO';
                      $fecha = $value->fecha_cancelado!=''?date_format(date_create($value->fecha_cancelado),"d/m/Y h:i A"):'';
                  }else{
                      $fecha = $value->fecha_desembolso!=''?date_format(date_create($value->fecha_desembolso),"d/m/Y h:i A"):'';
                  }
              }
              $html .= "<tr id='show_data_select' idcredito='{$value->id}' estado='{$value->estado}'>
                            <td>".($key+1)."</td>
                            <td>{$value->nombrecliente}</td>
                            <td>{$value->nombreaval}</td>
                            <td>{$value->nombreproductocredito}</td>
                            <td>{$value->monto_solicitado}</td>
                            <td>{$request->input('estado')}</td>
                            <td>{$value->nombreasesor}</td>
                            <td>".$fecha."</td>
                            <td>{$value->nombremodalidadcredito}</td>
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
//                                   <li>
//                                     <a class='dropdown-item' href='javascript:;' onclick='btnDetalleAprobacion({$value->id})'>
//                                       <i class='fa fa-edit'></i> Detalle Aprobacion
//                                     </a>
//                                   </li>
          }
          return array(
            'html' => $html
          );
          
        }
        else if( $id == 'showpermisos'){
          
          $nivel_aprobacion = DB::table('nivelaprobacion')
                              ->where('nivelaprobacion.id',$request->idnivelaprobacion)
                              ->first();

          $option_nivel_filtro = '<option disabled selected> -- Seleccione tipo filtro -- </option>';

          $option_nivelaprobacion_user_uno = [];
          $option_nivelaprobacion_user_dos = [];

          $option_autonomiaadministracion_user_uno = [];
          $option_autonomiaadministracion_user_dos = [];

          $option_autonomiagerencia_user_uno = [];
          $option_autonomiagerencia_user_dos = [];
          
          if($nivel_aprobacion!=''){
              $nivelaprobacion = json_decode($nivel_aprobacion->nivelaprobacion, true);
              $autonomiaadministracion = json_decode($nivel_aprobacion->autonomiaadministracion, true);
              $autonomiagerencia = json_decode($nivel_aprobacion->autonomiagerencia, true);
          
              if($request->input('campo') == 'nivelaprobacion'){
                if(count($nivelaprobacion[0]['tipo_uno']) > 0){
                  $option_nivel_filtro .= '<option value="tipo_uno">NIVEL 1</option>';
                  foreach($nivelaprobacion[0]['tipo_uno'] as $value){

                    $usuario_permiso = DB::table('users_permiso')
                                        ->join('users','users.id','users_permiso.idusers')
                                        ->join('permiso','permiso.id','users_permiso.idpermiso')
                                        ->where('users_permiso.idpermiso',$value['valor'])
                                        ->where('users_permiso.idtienda',$idtienda)
                                        ->select(
                                          'users_permiso.*',
                                          DB::raw('CONCAT(users.nombrecompleto," (",permiso.nombre,")") as nombre_personal')
                                        )
                                        ->get();
                    $usuarios = [];
                    $estado_validar_master = 0;
                    $array_existe = [];
                    foreach($usuario_permiso as $valueusers){
                          $credito_aprobacion = DB::table('credito_aprobacion')
                                  ->where('credito_aprobacion.idpermiso',$valueusers->idpermiso)
                                  ->where('credito_aprobacion.idusers',$valueusers->idusers)
                                  ->where('credito_aprobacion.idcredito',$request->idcredito)
                                  ->first();
                          $estado_validar = 'NO';
                          $aprobacion_idestado = 0;
                          if($credito_aprobacion!=''){
                              $estado_validar = 'OK';
                              $estado_validar_master = $estado_validar_master+1;
                              $array_existe[] = $credito_aprobacion->id;
                              $aprobacion_idestado = $credito_aprobacion->idestado;
                          }

                        $usuarios[] = [
                            'idusers' => $valueusers->idusers,
                            'idpermiso' => $valueusers->idpermiso,
                            'nombre_personal' => $valueusers->nombre_personal,
                            'estado_validar' => $estado_validar,
                            'idestado' => $aprobacion_idestado,
                            'array_existe' => $array_existe,
                        ];
                    }


                    $option_nivelaprobacion_user_uno[] = [
                      'usuarios' => $usuarios,
                      'permiso' => $value['texto'],
                      'idpermiso' => $value['valor'],
                      'estado_validar_master' => $estado_validar_master
                    ];

                  }

                }
                if(count($nivelaprobacion[0]['tipo_dos']) > 0){
                  $option_nivel_filtro .= '<option value="tipo_dos">NIVEL 2</option>';
                  foreach($nivelaprobacion[0]['tipo_dos'] as $value){

                    $usuario_permiso = DB::table('users_permiso')
                                        ->join('users','users.id','users_permiso.idusers')
                                        ->join('permiso','permiso.id','users_permiso.idpermiso')
                                        ->where('users_permiso.idpermiso',$value['valor'])
                                        ->where('users_permiso.idtienda',$idtienda)
                                        ->select(
                                          'users_permiso.*',
                                          DB::raw('CONCAT(users.nombrecompleto," (",permiso.nombre,")") as nombre_personal')
                                        )
                                        ->get();
                    $usuarios = [];
                    $estado_validar_master = 0;
                    $array_existe = [];
                    foreach($usuario_permiso as $valueusers){
                          $credito_aprobacion = DB::table('credito_aprobacion')
                                  ->where('credito_aprobacion.idpermiso',$valueusers->idpermiso)
                                  ->where('credito_aprobacion.idusers',$valueusers->idusers)
                                  ->where('credito_aprobacion.idcredito',$request->idcredito)
                                  ->first();
                          $estado_validar = 'NO';
                          $aprobacion_idestado = 0;
                          if($credito_aprobacion!=''){
                              $estado_validar = 'OK';
                              $estado_validar_master = $estado_validar_master+1;
                              $array_existe[] = $credito_aprobacion->id;
                              $aprobacion_idestado = $credito_aprobacion->idestado;
                          }

                        $usuarios[] = [
                            'idusers' => $valueusers->idusers,
                            'idpermiso' => $valueusers->idpermiso,
                            'nombre_personal' => $valueusers->nombre_personal,
                            'estado_validar' => $estado_validar,
                            'idestado' => $aprobacion_idestado,
                            'array_existe' => $array_existe,
                        ];
                    }


                    $option_nivelaprobacion_user_dos[] = [
                      'usuarios' => $usuarios,
                      'permiso' => $value['texto'],
                      'idpermiso' => $value['valor'],
                      'estado_validar_master' => $estado_validar_master
                    ];


                  }
                }
              }
              else if($request->input('campo') == 'autonomiaadministracion'){
                if(count($autonomiaadministracion[0]['tipo_uno']) > 0){
                  $option_nivel_filtro .= '<option value="tipo_uno">NIVEL 1</option>';
                  foreach($autonomiaadministracion[0]['tipo_uno'] as $value){

                    $usuario_permiso = DB::table('users_permiso')
                                        ->join('users','users.id','users_permiso.idusers')
                                        ->join('permiso','permiso.id','users_permiso.idpermiso')
                                        ->where('users_permiso.idpermiso',$value['valor'])
                                        ->where('users_permiso.idtienda',$idtienda)
                                        ->select(
                                          'users_permiso.*',
                                          DB::raw('CONCAT(users.nombrecompleto," (",permiso.nombre,")") as nombre_personal')
                                        )
                                        ->get();
                    $usuarios = [];
                    $estado_validar_master = 0;
                    $array_existe = [];
                    foreach($usuario_permiso as $valueusers){
                          $credito_aprobacion = DB::table('credito_aprobacion')
                                  ->where('credito_aprobacion.idpermiso',$valueusers->idpermiso)
                                  ->where('credito_aprobacion.idusers',$valueusers->idusers)
                                  ->where('credito_aprobacion.idcredito',$request->idcredito)
                                  ->first();
                          $estado_validar = 'NO';
                          $aprobacion_idestado = 0;
                          if($credito_aprobacion!=''){
                              $estado_validar = 'OK';
                              $estado_validar_master = $estado_validar_master+1;
                              $array_existe[] = $credito_aprobacion->id;
                              $aprobacion_idestado = $credito_aprobacion->idestado;
                          }

                        $usuarios[] = [
                            'idusers' => $valueusers->idusers,
                            'idpermiso' => $valueusers->idpermiso,
                            'nombre_personal' => $valueusers->nombre_personal,
                            'estado_validar' => $estado_validar,
                            'idestado' => $aprobacion_idestado,
                            'array_existe' => $array_existe,
                        ];
                    }


                    $option_autonomiaadministracion_user_uno[] = [
                      'usuarios' => $usuarios,
                      'permiso' => $value['texto'],
                      'idpermiso' => $value['valor'],
                      'estado_validar_master' => $estado_validar_master
                    ];

                  }
                }
                if(count($autonomiaadministracion[0]['tipo_dos']) > 0){
                  $option_nivel_filtro .= '<option value="tipo_dos">NIVEL 2</option>';
                  foreach($autonomiaadministracion[0]['tipo_dos'] as $value){

                    $usuario_permiso = DB::table('users_permiso')
                                        ->join('users','users.id','users_permiso.idusers')
                                        ->join('permiso','permiso.id','users_permiso.idpermiso')
                                        ->where('users_permiso.idpermiso',$value['valor'])
                                        ->where('users_permiso.idtienda',$idtienda)
                                        ->select(
                                          'users_permiso.*',
                                          DB::raw('CONCAT(users.nombrecompleto," (",permiso.nombre,")") as nombre_personal')
                                        )
                                        ->get();
                    $usuarios = [];
                    $estado_validar_master = 0;
                    $array_existe = [];
                    foreach($usuario_permiso as $valueusers){
                          $credito_aprobacion = DB::table('credito_aprobacion')
                                  ->where('credito_aprobacion.idpermiso',$valueusers->idpermiso)
                                  ->where('credito_aprobacion.idusers',$valueusers->idusers)
                                  ->where('credito_aprobacion.idcredito',$request->idcredito)
                                  ->first();
                          $estado_validar = 'NO';
                          $aprobacion_idestado = 0;
                          if($credito_aprobacion!=''){
                              $estado_validar = 'OK';
                              $estado_validar_master = $estado_validar_master+1;
                              $array_existe[] = $credito_aprobacion->id;
                              $aprobacion_idestado = $credito_aprobacion->idestado;
                          }

                        $usuarios[] = [
                            'idusers' => $valueusers->idusers,
                            'idpermiso' => $valueusers->idpermiso,
                            'nombre_personal' => $valueusers->nombre_personal,
                            'estado_validar' => $estado_validar,
                            'idestado' => $aprobacion_idestado,
                            'array_existe' => $array_existe,
                        ];
                    }


                    $option_autonomiaadministracion_user_dos[] = [
                      'usuarios' => $usuarios,
                      'permiso' => $value['texto'],
                      'idpermiso' => $value['valor'],
                      'estado_validar_master' => $estado_validar_master
                    ];

                  }
                }
              }
              else if($request->input('campo') == 'autonomiagerencia'){
            if(count($autonomiagerencia[0]['tipo_uno']) > 0){
              $option_nivel_filtro .= '<option value="tipo_uno">NIVEL 1</option>';
               foreach($autonomiagerencia[0]['tipo_uno'] as $value){
                 
                $usuario_permiso = DB::table('users_permiso')
                                    ->join('users','users.id','users_permiso.idusers')
                                        ->join('permiso','permiso.id','users_permiso.idpermiso')
                                    ->where('users_permiso.idpermiso',$value['valor'])
                                    ->where('users_permiso.idtienda',$idtienda)
                                    ->select(
                                      'users_permiso.*',
                                      DB::raw('CONCAT(users.nombrecompleto," (",permiso.nombre,")") as nombre_personal')
                                    )
                                    ->get();
                $usuarios = [];
                $estado_validar_master = 0;
                $array_existe = [];
                foreach($usuario_permiso as $valueusers){
                      $credito_aprobacion = DB::table('credito_aprobacion')
                              ->where('credito_aprobacion.idpermiso',$valueusers->idpermiso)
                              ->where('credito_aprobacion.idusers',$valueusers->idusers)
                              ->where('credito_aprobacion.idcredito',$request->idcredito)
                              ->first();
                      $estado_validar = 'NO';
                      $aprobacion_idestado = 0;
                      if($credito_aprobacion!=''){
                          $estado_validar = 'OK';
                          $estado_validar_master = $estado_validar_master+1;
                          $array_existe[] = $credito_aprobacion->id;
                          $aprobacion_idestado = $credito_aprobacion->idestado;
                      }
                  
                    $usuarios[] = [
                        'idusers' => $valueusers->idusers,
                        'idpermiso' => $valueusers->idpermiso,
                        'nombre_personal' => $valueusers->nombre_personal,
                        'estado_validar' => $estado_validar,
                        'idestado' => $aprobacion_idestado,
                        'array_existe' => $array_existe,
                    ];
                }
                
              
                $option_autonomiagerencia_user_uno[] = [
                  'usuarios' => $usuarios,
                  'permiso' => $value['texto'],
                  'idpermiso' => $value['valor'],
                  'estado_validar_master' => $estado_validar_master
                ];
                 
              }
            }
            if(count($autonomiagerencia[0]['tipo_dos']) > 0){
              $option_nivel_filtro .= '<option value="tipo_dos">NIVEL 2</option>';
              foreach($autonomiagerencia[0]['tipo_dos'] as $value){
                
                
                $usuario_permiso = DB::table('users_permiso')
                                    ->join('users','users.id','users_permiso.idusers')
                                        ->join('permiso','permiso.id','users_permiso.idpermiso')
                                    ->where('users_permiso.idpermiso',$value['valor'])
                                    ->where('users_permiso.idtienda',$idtienda)
                                    ->select(
                                      'users_permiso.*',
                                      DB::raw('CONCAT(users.nombrecompleto," (",permiso.nombre,")") as nombre_personal')
                                    )
                                    ->get();
                $usuarios = [];
                $estado_validar_master = 0;
                $array_existe = [];
                foreach($usuario_permiso as $valueusers){
                      $credito_aprobacion = DB::table('credito_aprobacion')
                              ->where('credito_aprobacion.idpermiso',$valueusers->idpermiso)
                              ->where('credito_aprobacion.idusers',$valueusers->idusers)
                              ->where('credito_aprobacion.idcredito',$request->idcredito)
                              ->first();
                      $estado_validar = 'NO';
                      $aprobacion_idestado = 0;
                      if($credito_aprobacion!=''){
                          $estado_validar = 'OK';
                          $estado_validar_master = $estado_validar_master+1;
                          $array_existe[] = $credito_aprobacion->id;
                          $aprobacion_idestado = $credito_aprobacion->idestado;
                      }
                  
                    $usuarios[] = [
                        'idusers' => $valueusers->idusers,
                        'idpermiso' => $valueusers->idpermiso,
                        'nombre_personal' => $valueusers->nombre_personal,
                        'estado_validar' => $estado_validar,
                        'idestado' => $aprobacion_idestado,
                        'array_existe' => $array_existe,
                    ];
                }
                
              
                $option_autonomiagerencia_user_dos[] = [
                  'usuarios' => $usuarios,
                  'permiso' => $value['texto'],
                  'idpermiso' => $value['valor'],
                  'estado_validar_master' => $estado_validar_master
                ];
              }
            }
          }
          }
          
          $usuario = DB::table('users')
            ->join('users_permiso','users_permiso.idusers','users.id')
            ->join('permiso','permiso.id','users_permiso.idpermiso')
            ->where('users.id',Auth::user()->id)
            ->select('users.*','permiso.nombre as permiso','permiso.id as idpermiso')
            ->limit(1)
            ->first();
          
          return array(
            'usuario' => $usuario,
            'nivel_filtro' => $option_nivel_filtro,
            'option_nivelaprobacion_user_uno' => $option_nivelaprobacion_user_uno,
            'option_nivelaprobacion_user_dos' => $option_nivelaprobacion_user_dos,
            'option_autonomiaadministracion_user_uno' => $option_autonomiaadministracion_user_uno,
            'option_autonomiaadministracion_user_dos' => $option_autonomiaadministracion_user_dos,
            'option_autonomiagerencia_user_uno' => $option_autonomiagerencia_user_uno,
            'option_autonomiagerencia_user_dos' => $option_autonomiagerencia_user_dos,
          );
          
          
        }
        else if( $id == 'show_validaridentificacion'){
            $usuario = DB::table('users')
                ->where('users.id',$request->idresponsable)
                ->where('users.clave',$request->responsableclave)
                ->first();
            if($usuario!=''){
                return [
                    'resultado' => 'CORRECTO'
                ];
            }else{
                return [
                    'resultado' => 'ERROR'
                ];
            }
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
                        /*'tarifario.monto as monto_max_credito',
                        'tarifario.cuotas as coutas_max_credito',
                        'tarifario.tem as tem_producto',
                        'tarifario.tipo_producto_credito as tipo_producto_credito',*/
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
      if( $request->input('view') == 'cambiar_estado' ){
              
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->join('tienda','tienda.id','users_permiso.idtienda')
                ->where('users_permiso.idpermiso',2)
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso','tienda.nombreagencia as nombretienda')
                ->get();
        
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
        
        
        
        return view(sistema_view().'/propuestacredito/cambiar_estado',[
          'tienda' => $tienda,
          'credito' => $credito,
          'usuario' => $usuario,
          'usuarios' => $usuarios,
          'nivel_aprobacion' => $nivel_aprobacion,
          'credito_aprobacion' => $credito_aprobacion,
          'estado' => $request->input('tipo'),
          'permiso' => $request->input('permiso'),
        ]);
      }
      else if( $request->input('view') == 'opciones' ){
      
        
        
        return view(sistema_view().'/propuestacredito/opciones',[
          'tienda' => $tienda,
          'credito' => $credito,
          'usuario' => $usuario,
          'users_prestamo' => $users_prestamo,
        ]);
      }
      else if( $request->input('view') == 'acta_aprobacion' ){
        
        return view(sistema_view().'/propuestacredito/acta_aprobacion',[
          'users_prestamo'    => $users_prestamo,
          'tienda' => $tienda,
          'credito' => $credito,
        ]);
      }
      else if( $request->input('view') == 'acta_aprobacionpdf' ){


        $credito_cuantitativa_deudas = DB::table('credito_cuantitativa_deudas')
                                        ->join('forma_pago_credito','forma_pago_credito.id','credito_cuantitativa_deudas.idforma_pago_credito')
                                        ->where('credito_cuantitativa_deudas.idcredito',$id)
                                        ->select(
                                          'credito_cuantitativa_deudas.*',
                                          'forma_pago_credito.nombre as nombre_forma_pago_credito',
                                        )
                                        ->first();
        $credito_evaluacion_resumida = DB::table('credito_evaluacion_resumida')
                                          ->join('tipo_giro_economico','tipo_giro_economico.id','credito_evaluacion_resumida.idtipo_giro_economico')
                                          ->leftJoin('giro_economico_evaluacion','giro_economico_evaluacion.id','credito_evaluacion_resumida.idgiro_economico_evaluacion')
                                          ->where('credito_evaluacion_resumida.idcredito',$id)
                                          ->select(
                                            'credito_evaluacion_resumida.*',
                                            'tipo_giro_economico.nombre as nombretipo_giro_economico',
                                            'giro_economico_evaluacion.nombre as nombregiro_economico_evaluacion'
                                          )
                                          ->first();
        
         $credito_garantias_cliente = DB::table('credito_garantia')
                                      
                                      ->where('credito_garantia.idcredito',$id)
                                      ->where('credito_garantia.idgarantias',0)
                                      ->where('credito_garantia.tipo','CLIENTE')
                                      ->select(
                                          'credito_garantia.*',
                                      )
                                     ->get();
        $credito_garantias_aval = DB::table('credito_garantia')
                                  ->where('credito_garantia.idcredito',$id)
                                  ->where('credito_garantia.idgarantias',0)
                                  ->where('credito_garantia.tipo','AVAL')
                                  ->select(
                                      'credito_garantia.*',
                                  )
                                 ->get();
        
        $credito_propuesta = DB::table('credito_propuesta')->where('credito_propuesta.idcredito',$id)->first();
        $credito_cuantitativa_control_limites = DB::table('credito_cuantitativa_control_limites')->where('credito_cuantitativa_control_limites.idcredito',$id)->first();
        
        $asesor = DB::table('users')->where('users.id',$credito->idasesor)->first();
        $funcionario_aprueba = DB::table('users')->where('users.id',$credito->idadministrador)->first();
        
        
        $credito_aprobacion = DB::table('credito_aprobacion')
                              ->join('permiso','permiso.id','credito_aprobacion.idpermiso')
                              ->join('users','users.id','credito_aprobacion.idusers')
                              ->where('credito_aprobacion.idcredito',$credito->id)
                              ->select(
                                'credito_aprobacion.*',
                                'permiso.nombre as nombre_permiso',
                                'users.nombrecompleto as nombre_usuario',
                                'users.nombre as nombre',
                                'users.apellidopaterno as apellidopaterno',
                              )
                              ->orderBy('permiso.rango','asc')
                              ->get();
        
        $usuario_excepcionyautorizacion = DB::table('users')->where('users.id',$credito->idusuario_excepcionesautorizaciones)->first();
        $usuario_areariesgos = DB::table('users')->where('users.id',$credito->idusuario_areariesgos)->first();
        $usuario_comentariovisita = DB::table('users')->where('users.id',$credito->idusuario_comentariovisita)->first();
        
        $users_prestamo_aval = DB::table('s_users_prestamo')->where('s_users_prestamo.id_s_users',$credito->idaval)->first();
      
        $pdf = PDF::loadView(sistema_view().'/propuestacredito/acta_aprobacionpdf',[
            'users_prestamo'    => $users_prestamo,
            'tienda' => $tienda,
            'credito' => $credito,
            'credito_cuantitativa_deudas' => $credito_cuantitativa_deudas,
            'credito_evaluacion_resumida' => $credito_evaluacion_resumida,
            'credito_garantias_cliente' => $credito_garantias_cliente,
            'credito_garantias_aval' => $credito_garantias_aval,
            'calificacion_cliente' => $this->calificacion_cliente,
            'credito_propuesta' => $credito_propuesta,
            'credito_cuantitativa_control_limites' => $credito_cuantitativa_control_limites,
            'asesor' => $asesor,
            'funcionario_aprueba' => $funcionario_aprueba,
            'credito_aprobacion' => $credito_aprobacion,
            'usuario_excepcionyautorizacion' => $usuario_excepcionyautorizacion,
            'usuario_areariesgos' => $usuario_areariesgos,
            'usuario_comentariovisita' => $usuario_comentariovisita,
            'users_prestamo_aval' => $users_prestamo_aval,
        ]); 
        $pdf->setPaper('A4');
        return $pdf->stream('ACTA_APROBACION.pdf');
      }
      else if( $request->input('view') == 'excepcion_autorizacion' ){
        
        $usuarios = DB::table('users')
            ->join('users_permiso','users_permiso.idusers','users.id')
            ->join('permiso','permiso.id','users_permiso.idpermiso')
            ->where('users_permiso.idpermiso',1)
            ->where('users_permiso.idtienda',$idtienda)
            ->select('users.*','permiso.nombre as nombrepermiso')
            ->get();
        
        return view(sistema_view().'/propuestacredito/excepcion_autorizacion',[
          'users_prestamo'    => $users_prestamo,
          'tienda' => $tienda,
          'credito' => $credito,
          'usuarios' => $usuarios,
        ]);
      }
      else if( $request->input('view') == 'area_riesgos' ){
        
        $usuarios = DB::table('users')
            ->join('users_permiso','users_permiso.idusers','users.id')
            ->join('permiso','permiso.id','users_permiso.idpermiso')
            ->where('users_permiso.idpermiso',5)
            ->where('users_permiso.idtienda',$idtienda)
            ->select('users.*','permiso.nombre as nombrepermiso')
            ->get();
        return view(sistema_view().'/propuestacredito/area_riesgos',[
          'users_prestamo'    => $users_prestamo,
          'tienda' => $tienda,
          'credito' => $credito,
          'usuarios' => $usuarios,
        ]);
      }
      else if( $request->input('view') == 'comentario_visitas' ){
        
        $usuarios = DB::table('users')
            ->join('users_permiso','users_permiso.idusers','users.id')
            ->join('permiso','permiso.id','users_permiso.idpermiso')
            ->whereIn('users_permiso.idpermiso',[1,3])
            ->where('users_permiso.idtienda',$idtienda)
            ->select('users.*','permiso.nombre as nombrepermiso')
            ->get();
        return view(sistema_view().'/propuestacredito/comentario_visitas',[
          'users_prestamo'    => $users_prestamo,
          'tienda' => $tienda,
          'credito' => $credito,
          'usuarios' => $usuarios,
        ]);
      }
      
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        if( $request->input('view') == 'cambiar_estado' ) {
           
            if($request->input('estado')=='PENDIENTE'){
              //---------- restaurar credito
              DB::table('credito_propuesta')->where('credito_propuesta.idcredito',$id)->update([
                  'monto_compra_deuda' => 0,
                  'monto_compra_deuda_det' => '',
              ]);
              
              DB::table('credito')->whereId($id)->update([
                'idadministrador' => Auth::user()->id,
                'estado' => $request->input('estado'),
                'cuenta' => 0,
                'aprobacion_tipo_validacion' => '',
                'aprobacion_nivel_validacion' => 0,
              ]);
              DB::table('credito_aprobacion')->where('idcredito',$id)->delete();
              DB::table('credito_formapago')->where('idcredito',$id)->delete();
              $credito_aprobado = 'CORRECTO';
            }
            if($request->input('estado')=='APROBADO'){
  
              if($request->idusers == 'null' ){
                return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje'   => 'Debe de seleccionar un usuario de aprobación.'
                ]);
              }
       
                if($request->password == ''){
                  return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Debe escribir una contraseña.'
                  ]);
                }
              
              if($request->idestado == 2 && $request->comentario == ''){
                return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje'   => 'Debe escribir un comentario de anulación.'
                ]);
              }

                $usuario = DB::table('users')
                            ->where('users.id',$request->idusers)
                            //->where('users.clave',$value['password'])
                            ->first();
                if($usuario){
                  if($usuario->clave != $request->password){
                    return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'La contraseña de "'.$usuario->nombrecompleto.'" es incorrecta'
                    ]);
                  }
                }
              
              
               $usuario_firma = DB::table('credito_aprobacion')
                  ->where('idusers',$request->idusers)
                  ->where('idcredito',$id)
                  ->first();
                if($usuario_firma){
                    return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'El usuario "'.$usuario->nombrecompleto.'" ya esta asignado, ingrese otro porfavor.'
                    ]);
                }
          
              DB::table('credito_aprobacion')->where('idcredito',$id)->delete();
              $credito_aprobacion = json_decode($request->input('credito_aprobacion'),true);
              
              $valid_aprobacion = 0;
              $valid_desaprobado = 0;
              $can_aprobacion = 0;
              foreach($credito_aprobacion as $value){
                DB::table('credito_aprobacion')->insert([
                  'idcredito' => $id,
                  'idusers'   => $value['idusers']!='null'?$value['idusers']:0,
                  'idpermiso' => $value['idpermiso'],
                  'comentario' => $value['comentario']!=''?$value['comentario']:'',
                  'fecha'     => Carbon::now(),
                  'idestado'  => $value['idestado']
                ]);
                
                if($value['idestado']==2){
                  $valid_desaprobado++;
                }else{
                  if($value['idestado']!=1){
                      $valid_aprobacion++;
                  }else{
                      $can_aprobacion++;
                  }
                }
                    
              }
              
              $count_credito_aprobacion = DB::table('credito_aprobacion')->where('idcredito',$id)->count();
              
                  
              $credito_aprobado = 'NO';
              
              if($count_credito_aprobacion==0){
                  $credito_aprobado = 'NO';
              }else{
                  DB::table('credito')->whereId($id)->update([
                    'aprobacion_tipo_validacion' => $request->tipo_validacion,
                    'aprobacion_nivel_validacion' => $request->nivel_validacion,
                  ]);
                  
                  if($valid_desaprobado>0){
                      DB::table('credito')->whereId($id)->update([
                        'idadministrador' => Auth::user()->id,
                        'estado' => 'DESAPROBADO',
                        'fecha_desaprobacion' => Carbon::now(),
                      ]);
                      $credito_aprobado = 'CORRECTO';
                  }else{
                      if($valid_aprobacion==0 && $can_aprobacion==count($credito_aprobacion)){
                          DB::table('credito')->whereId($id)->update([
                            'idadministrador' => Auth::user()->id,
                            'estado' => 'APROBADO',
                            'fecha_aprobacion' => Carbon::now(),
                          ]);
                          $credito_aprobado = 'CORRECTO';
                      }
                  }
              }  
              
                  
              
                  
              
            }
            if($request->input('estado')=='DESEMBOLSADO'){
              DB::table('credito')->whereId($id)->update([
                'idadministrador' => Auth::user()->id,
                'estado' => $request->input('estado'),
                'fecha_desembolso' => Carbon::now(),
              ]);
              $credito_aprobado = 'CORRECTO';
            }
            if($request->input('estado')=='ELIMINAR' && $request->input('permiso')=='administrador'){
              
              
              //---------- restaurar pago
              
              $ultimocredito = DB::table('credito')
                  ->whereId($id)
                  ->first();
     
               if($ultimocredito->idcredito_refinanciado!=0){
                  if($ultimocredito->estado == 'DESEMBOLSADO'){
                      $credito = DB::table('credito')
                          ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                          ->where('credito.id',$ultimocredito->idcredito_refinanciado)
                          ->select(
                              'credito.*',
                              'credito_prendatario.modalidad as modalidadproductocredito',
                          )
                          ->first();
                      $credito_cobranzacuota = DB::table('credito_cobranzacuota')
                          ->join('credito','credito.id','credito_cobranzacuota.idcredito')
                          ->join('users as cliente','cliente.id','credito.idcliente')
                          ->where('credito_cobranzacuota.id',$credito->idcredito_cobranzacuota)
                          ->select(
                              'credito_cobranzacuota.*',
                              'cliente.nombrecompleto as nombrecliente',
                              'credito.idcliente as idcliente',
                              'credito.idestado_congelarcredito as idestado_congelarcredito',
                              'credito_cobranzacuota.opcion_pago as opcion_pago',
                          )
                          ->first();

                      DB::table('credito_descuentocuota')
                        ->where('credito_descuentocuota.idcredito_cobranzacuota',$credito->idcredito_cobranzacuota)
                        ->update([
                          'idcredito_cobranzacuota'        => 0,
                          'idestadocredito_descuentocuota' => 1,
                      ]);

                      DB::table('credito_cargo')
                        ->where('credito_cargo.idcredito_cobranzacuota',$credito->idcredito_cobranzacuota)
                        ->update([
                          'idcredito_cobranzacuota'  => 0,
                          'idestadocredito_cargo'    => 1,
                      ]);

                      $credito_cronograma = DB::table('credito_cronograma')
                          ->where('credito_cronograma.idcredito',$credito->id)
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
                          ->where('credito_adelanto.idcredito_cobranzacuota',$credito->idcredito_cobranzacuota)
                          ->update([
                            'credito_adelanto.idestadocredito_adelanto' => 3,
                      ]);  
                    
                      
                      // descuento cuota
                      $credito_descuentocuotas = DB::table('credito_descuentocuota')
                            ->where('credito_descuentocuota.idcredito',$ultimocredito->idcredito_refinanciado)
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

                      DB::table('credito_cobranzacuota')
                        ->whereId($credito->idcredito_cobranzacuota)
                        ->update([
                            'fechaextorno' => Carbon::now(),
                            'saldo_pendientepago' => $cronograma['saldo_capital'],
                            'total_pendientepago' => $cronograma['cuota_pendiente'],
                            'idestadoextorno'  => 2,
                            'idresponsableextorno'  => Auth::user()->id,
                      ]);
                 
                      // restaurar estado de credito
                      DB::table('credito')
                        ->whereId($ultimocredito->idcredito_refinanciado)
                        ->update([
                            'saldo_pendientepago' => $cronograma['saldo_capital'],
                            'total_pendientepago' => $cronograma['cuota_pendiente'],
                            'idestadocredito'  => 1,
                      ]);
                  }else{
                 
                      // restaurar estado de credito
                      DB::table('credito')
                        ->whereId($ultimocredito->idcredito_refinanciado)
                        ->update([
                            'idestadocredito'  => 1,
                      ]);
                      
                  }
                  // restaurar garantias
                  DB::table('credito_garantia')
                    ->where('credito_garantia.idcredito',$ultimocredito->idcredito_refinanciado)
                    ->update([
                        'idestadoentrega' => 1,
                  ]);
              }
         
              DB::table('credito')->whereId($id)->update([
                'fecha_eliminado' => Carbon::now(),
                'idadministrador' => Auth::user()->id,
                'idcredito_refinanciado'  => 0,
                'estado' => 'ELIMINADO',
              ]);
              
              $credito_aprobado = 'CORRECTO';
            }
            if($request->input('estado')=='ELIMINAR' && $request->input('permiso')=='institucional'){
              
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
              
              //---------- restaurar pago
              
              $ultimocredito = DB::table('credito')
                  ->whereId($id)
                  ->first();
        
              if($ultimocredito->idcredito_refinanciado!=0){
                  if($ultimocredito->estado == 'DESEMBOLSADO'){
                      $credito = DB::table('credito')
                          ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
                          ->where('credito.id',$ultimocredito->idcredito_refinanciado)
                          ->select(
                              'credito.*',
                              'credito_prendatario.modalidad as modalidadproductocredito',
                          )
                          ->first();
                      $credito_cobranzacuota = DB::table('credito_cobranzacuota')
                          ->join('credito','credito.id','credito_cobranzacuota.idcredito')
                          ->join('users as cliente','cliente.id','credito.idcliente')
                          ->where('credito_cobranzacuota.id',$credito->idcredito_cobranzacuota)
                          ->select(
                              'credito_cobranzacuota.*',
                              'cliente.nombrecompleto as nombrecliente',
                              'credito.idcliente as idcliente',
                              'credito.idestado_congelarcredito as idestado_congelarcredito',
                              'credito_cobranzacuota.opcion_pago as opcion_pago',
                          )
                          ->first();

                      DB::table('credito_descuentocuota')
                        ->where('credito_descuentocuota.idcredito_cobranzacuota',$credito->idcredito_cobranzacuota)
                        ->update([
                          'idcredito_cobranzacuota'        => 0,
                          'idestadocredito_descuentocuota' => 1,
                      ]);

                      DB::table('credito_cargo')
                        ->where('credito_cargo.idcredito_cobranzacuota',$credito->idcredito_cobranzacuota)
                        ->update([
                          'idcredito_cobranzacuota'  => 0,
                          'idestadocredito_cargo'    => 1,
                      ]);

                      $credito_cronograma = DB::table('credito_cronograma')
                          ->where('credito_cronograma.idcredito',$credito->id)
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
                          ->where('credito_adelanto.idcredito_cobranzacuota',$credito->idcredito_cobranzacuota)
                          ->update([
                            'credito_adelanto.idestadocredito_adelanto' => 3,
                      ]);  
                    
                      // descuento cuota
                      $credito_descuentocuotas = DB::table('credito_descuentocuota')
                            ->where('credito_descuentocuota.idcredito',$ultimocredito->idcredito_refinanciado)
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

                      DB::table('credito_cobranzacuota')
                        ->whereId($credito->idcredito_cobranzacuota)
                        ->update([
                            'fechaextorno' => Carbon::now(),
                            'saldo_pendientepago' => $cronograma['saldo_capital'],
                            'total_pendientepago' => $cronograma['cuota_pendiente'],
                            'idestadoextorno'  => 2,
                            'idresponsableextorno'  => Auth::user()->id,
                      ]);
                 
                      // restaurar estado de credito
                      DB::table('credito')
                        ->whereId($ultimocredito->idcredito_refinanciado)
                        ->update([
                            'saldo_pendientepago' => $cronograma['saldo_capital'],
                            'total_pendientepago' => $cronograma['cuota_pendiente'],
                            'idestadocredito'  => 1,
                      ]);
                  }else{
                 
                      // restaurar estado de credito
                      DB::table('credito')
                        ->whereId($ultimocredito->idcredito_refinanciado)
                        ->update([
                            'idestadocredito'  => 1,
                      ]);
                    
                  }
     
                  // restaurar garantias
                  DB::table('credito_garantia')
                    ->where('credito_garantia.idcredito',$ultimocredito->idcredito_refinanciado)
                    ->update([
                        'idestadoentrega' => 1,
                  ]);
              }
              //----------------
              
              DB::table('credito')->whereId($id)->update([
                'fecha_eliminado' => Carbon::now(),
                'idadministrador' => $idresponsable,
                'idcredito_refinanciado'  => 0,
                'estado' => 'ELIMINADO',
              ]);
              $credito_aprobado = 'CORRECTO';
              /*DB::table('credito')->whereId($id)->update([
                'aprobacion_tipo_validacion' => '',
                'aprobacion_nivel_validacion' => 0,
              ]);
              DB::table('credito_aprobacion')->where('idcredito',$id)->delete();*/
            }

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.',
                'credito_aprobado'   => $credito_aprobado
            ]);
        }
        else if( $request->input('view') == 'excepcion_autorizacion' ){
          
            $rules = [
                'excepcionesautorizaciones' => 'required',                         
            ];
          
            $messages = [
                'excepcionesautorizaciones.required' => 'El Campo de Excepciones y autorizaciones.',
            ];
            $this->validate($request,$rules,$messages);
          
              DB::table('credito')->whereId($id)->update([
                'idusuario_excepcionesautorizaciones' => $request->idresponsable,
                'excepcionesautorizaciones' => $request->input('excepcionesautorizaciones'),
              ]);
          
              return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
              ]);
        }
        else if( $request->input('view') == 'area_riesgos' ){
          
            $rules = [
                'areariesgos' => 'required',                         
            ];
          
            $messages = [
                'areariesgos.required' => 'El Campo de area de riesgos.',
            ];
            $this->validate($request,$rules,$messages);
          
              DB::table('credito')->whereId($id)->update([
                'idusuario_areariesgos' => $request->idresponsable,
                'areariesgos' => $request->input('areariesgos'),
              ]);
          
              return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
              ]);
        }
        else if( $request->input('view') == 'comentario_visitas' ){
          
            $rules = [
                'comentariovisita' => 'required',                         
            ];
          
            $messages = [
                'comentariovisita.required' => 'El Campo de Comentario de visitas.',
            ];
            $this->validate($request,$rules,$messages);
          
              DB::table('credito')->whereId($id)->update([
                'idusuario_comentariovisita' => $request->idresponsable,
                'comentariovisita' => $request->input('comentariovisita'),
              ]);
          
              return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
              ]);
        }
        else if( $request->input('view') == 'acta_aprobacion' ){
          return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        
    
    }


    public function destroy(Request $request, $idtienda, $id)
    {
      
      
    
    }
}
