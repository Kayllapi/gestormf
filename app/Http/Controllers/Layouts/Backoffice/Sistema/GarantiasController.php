<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class GarantiasController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $tipo_garantia = DB::table('tipo_garantia')->get();
        $estado_garantia = DB::table('estado_garantia')->get();
        $estado_garantia_ref = DB::table('estado_garantia_ref')->get();
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/garantias/tabla',[
              'tienda' => $tienda,
              'tipo_garantia' => $tipo_garantia,
              'estado_garantia' => $estado_garantia,
              'estado_garantia_ref' => $estado_garantia_ref,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $tipo_garantia = DB::table('tipo_garantia')->get();
        $estado_garantia = DB::table('estado_garantia')->get();
        $estado_garantia_ref = DB::table('estado_garantia_ref')->get();
        $tipo_joyas = DB::table('tipo_joyas')->where('tipo_joyas.estado','ACTIVO')->get();
        $metodo_valorizacion = DB::table('metodo_valorizacion')->get();
        $descuento_joya = DB::table('descuento_joya')->get();

      
        if($request->view == 'registrar') {
            return view(sistema_view().'/garantias/create',[
                'idcliente' => $request->idcliente,
                'tienda' => $tienda,
                'tipo_garantia' => $tipo_garantia,
                'metodo_valorizacion' => $metodo_valorizacion,
                'tipo_joyas' => $tipo_joyas,
                'estado_garantia' => $estado_garantia,
                'estado_garantia_ref' => $estado_garantia_ref,
                'descuento_joya' => $descuento_joya,
            ]);
        }
      elseif($request->input('view') == 'depositario') {

        $cliente = DB::table('users')->whereId($request->idcliente)->first();
        
        $credito_gestiondepositario = DB::table('credito_gestiondepositario')
            ->where('estado_id',1)
            ->select(
                'credito_gestiondepositario.constituciongarantia_id as constituciongarantia_id',
                'credito_gestiondepositario.constituciongarantia_nombre as constituciongarantia_nombre',
            )
            ->distinct()
            ->get();
        
        $credito_polizaseguro = DB::table('credito_polizaseguro')->where('id_cliente',$request->idcliente)->get();
        
        return view(sistema_view().'/garantias/depositario',[
          'tienda' => $tienda,
          'credito_gestiondepositario' => $credito_gestiondepositario,
          'credito_polizaseguro' => $credito_polizaseguro,
          'idtienda' => $idtienda,
          'cliente' => $cliente,
        ]);
      }
    }
  
    public function store(Request $request, $idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'idcliente' => 'required',           
                'idtipogarantia' => 'required',              
                'descripcion' => 'required',                
                'serie_motor_partida' => 'required',                
                'modelo_tipo' => 'required',                 
                'idestado_garantia' => 'required',           
                'color' => 'required',    
                'idestado_garantia_ref' => 'required',                    
            ];
          
            $messages = [
                'idcliente.required' => 'El "Cliente" es Obligatorio.',
                'idtipogarantia.required' => 'El "Tipo de Garantia" es Obligatorio.',
                'descripcion.required' => 'La "Descripción" es Obligatorio.',
                'serie_motor_partida.required' => 'La "Serie/Motor/N° Partida" es Obligatorio.',
                'modelo_tipo.required' => 'La "Modelo/Tipo" es Obligatorio.',
                'idestado_garantia.required' => 'El "Estado" es Obligatorio.',
                'color.required' => 'El "Color" es Obligatorio.',
                'idestado_garantia_ref.required' => 'El "Estado de Ref." es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $idgarantia = DB::table('garantias')->insertGetId([
               'idcliente'              => $request->input('idcliente'),
               'idtipogarantia'         => $request->input('idtipogarantia'),
               'fecharegistro'          => Carbon::now(),
               'descripcion'            => $request->input('descripcion'),
               'serie_motor_partida'    => $request->input('serie_motor_partida'),
               'chasis'                 => $request->input('chasis')!=''?$request->input('chasis'):'',
               'modelo_tipo'            => $request->input('modelo_tipo'),
               'otros'                  => $request->input('otros')!=''?$request->input('otros'):'',
               'idestado_garantia'      => $request->input('idestado_garantia'),
               'color'                  => $request->input('color'),
               'fabricacion'            => $request->input('fabricacion')!=''?$request->input('fabricacion'):'',
               'compra'                 => $request->input('compra')!=''?$request->input('compra'):'',
               'placa'                  => $request->input('placa')!=''?$request->input('placa'):'',
               'cobertura'              => $request->input('cobertura'),
               'valorcomercial'         => $request->input('valorcomercial'),
               'accesorio_doc'          => $request->input('accesorio_doc')!=''?$request->input('accesorio_doc'):'',
               'detalle_garantia'       => $request->input('detalle_garantia')!=''?$request->input('detalle_garantia'):'',
               'idestado_garantia_ref'  => $request->input('idestado_garantia_ref'),
              
               'idmetodo_valorizacion'    => $request->input('idmetodo_valorizacion') != 6 ? $request->input('idmetodo_valorizacion') : 0,
               'idtipo_garantia_detalle'  => $request->input('idtipogarantia') != 6 ? $request->input('idtipo_garantia_detalle') : 0,
               'valor_mercado'            => $request->input('valor_mercado'),
               'porcentajecobertura'      => $request->input('porcentajecobertura'),
               'porcentajevalorcomercial' => $request->input('porcentajevalorcomercial'),
              
               'idtipo_joyas'             => $request->input('idtipogarantia') == 6 ? $request->input('idtipo_joyas') : 0,
               'idtarifario_joya'         => $request->input('idtipogarantia') == 6 ? $request->input('idtarifario_joya') : 0,
               'peso_gramos'              => $request->input('peso_gramos'),
              
//                'aplique_contraplacado'    => $request->input('aplique_contraplacado'),
//                'complemntos_cir_rect'     => $request->input('complemntos_cir_rect'),
//                'reloj'                    => $request->input('reloj'),
               'iddescuento_joya'         => $request->input('idtipogarantia') == 6 ? ($request->input('iddescuento_joya')!=''?$request->input('iddescuento_joya'):0) : 0,
               'idvalorizacion_descuento' => $request->input('idtipogarantia') == 6 ? ($request->input('idvalorizacion_descuento')!=''?$request->input('idvalorizacion_descuento'):0) : 0,
              
               'peso_neto'                => $request->input('peso_neto'),
              
               'idresponsable'            => Auth::user()->id,
               
            ]);

          
            /*foreach(json_decode($request->seleccionar_polizaseguro) as $value){
                DB::table('credito_polizaseguro')
                    ->insert([
                        'numero_poliza' => $value->numero_poliza,
                        'aseguradora' => $value->aseguradora,
                        'prima_recio' => $value->prima_recio,
                        'beneficiario' => $value->beneficiario,
                        'asegurado' => $value->asegurado,
                        'tomador' => $value->tomador,
                        'vigencia_desde' => $value->vigencia_desde,
                        'vigencia_hasta' => $value->vigencia_hasta,
                        'id_garantia' => $idgarantia,
                        'idestado' => 1,
                    ]);
            }*/
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id=='show_table'){
          
            $garantias = DB::table('garantias')
                            ->join('users','users.id','garantias.idcliente')
                            ->where('garantias.idestadoeliminado',1)
                            ->select(
                                'garantias.*',
                                'users.nombrecompleto as nombrecliente'
                            )
                            ->orderBy('garantias.id','asc')
                            ->paginate($request->length,'*',null,($request->start/$request->length)+1);
          

            $tabla = [];
            foreach($garantias as $value){
                
              $tabla[]=[
                  'id'          => $value->id,
                  'nombrecliente' => $value->nombrecliente,
                  'descripcion' => $value->descripcion,
                  'cobertura'   => $value->cobertura,
                  'click' => true,
//                   'opcion' => [
//                      [
//                       'nombre' => 'Editar',
//                       'onclick' => '/'.$idtienda.'/garantias/'.$value->id.'/edit?view=editar',
//                       'icono' => 'edit',
//                     ],
//                     [
//                       'nombre' => 'Eliminar',
//                       'onclick' => '/'.$idtienda.'/garantias/'.$value->id.'/edit?view=eliminar',
//                       'icono' => 'trash',
//                     ]
//                   ],
              ];
            }
            
            return response()->json([
                'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $garantias->total(),
                'data'            => $tabla,
            ]);
        }
        else if($id == 'showlistagarantias'){
          $cliente = DB::table('users')->whereId($request->idcliente)->select('users.id','users.nombrecompleto','users.identificacion')->first();
          $garantias = DB::table('garantias')
                            ->where('garantias.idestadoeliminado',1)
                            ->where('garantias.idcliente', $request->idcliente)
                            ->select(
                                'garantias.*'
                            )
                            ->orderBy('garantias.id','asc')
                            ->get();
          $html = '';
          foreach($garantias as $value){
              $garantia_credito = DB::table('credito_garantia')
                  ->join('credito','credito.id','credito_garantia.idcredito')
                  ->where('credito_garantia.idgarantias',$value->id)
                  ->where('credito.idestadocredito',1)
                  ->whereIn('credito.estado',['PENDIENTE','PROCESO','APROBADO','DESEMBOLSADO'])
                  ->first();
              $color_garantia = $garantia_credito ? 'style="background-color:#3cd48d;"' : '';
              if($garantia_credito==''){
                $garantia_credito = DB::table('credito_garantia')
                    ->join('credito','credito.id','credito_garantia.idcredito')
                    ->where('credito_garantia.idgarantias',$value->id)
                    ->where('credito.idestadocredito',2)
                    ->where('credito_garantia.idestadoentrega',1)
                    ->whereIn('credito.estado',['PENDIENTE','PROCESO','APROBADO','DESEMBOLSADO'])
                    ->first();
                $color_garantia = $garantia_credito ? 'style="background-color:#6bc5ff;"' : '';
              }
            
              $html .= "<tr {$color_garantia} data-valor-columna='{$value->id}' onclick='show_data(this)'>
                            <td>{$value->descripcion}</td>
                            <td>S/ {$value->cobertura}</td>
                        </tr>";
          }
          
            $credito_polizaseguro = DB::table('credito_polizaseguro')->where('id_cliente',$request->idcliente)->get();
          
            $html_1 = '<div  class="text-danger" style="margin-top: 10px;">';
            foreach($credito_polizaseguro as $value){
                if($value->vigencia_hasta<now()->format('Y-m-d')){
                    $html_1 .= '<div><img src="'.url('public/backoffice/sistema/icongarantia.png').'" style="height: 15px;"> Póliza de Garantía "'.$value->asegurado.'", venció el '.Carbon::parse($value->vigencia_hasta)->format('d/m/Y').'</div>';
                }
            }
            $html_1 .= '</div>';
            
              
          return array(
            'cliente' => $cliente,
            'html' => $html,
            'credito_polizaseguro' => $html_1
          );
          
        }
        else if($id == 'showtipogarantia'){
//           $valorizacion = DB::table('tipo_garantia_detalle')
//                             ->join('metodo_valorizacion','metodo_valorizacion.id','tipo_garantia_detalle.idmetodo_valorizacion')
//                             ->where('tipo_garantia_detalle.idtipo_garantia',$request->idtipogarantia)
//                             ->select(
//                               'tipo_garantia_detalle.*',
//                               'metodo_valorizacion.nombre as nombremetodo'
//                             )
//                             ->orderBy('tipo_garantia_detalle.id','desc')
//                             ->get();
          

          $valorizacion = DB::table('tipo_garantia_detalle')
                            ->join('metodo_valorizacion','metodo_valorizacion.id','tipo_garantia_detalle.idmetodo_valorizacion')
                            ->where('tipo_garantia_detalle.idtipo_garantia',$request->idtipogarantia)
                            ->where('tipo_garantia_detalle.idmetodo_valorizacion',$request->idmetodovalorizacion)
                            ->select(
                              'tipo_garantia_detalle.*',
                              'metodo_valorizacion.nombre as nombremetodo'
                            )
                            ->orderBy('tipo_garantia_detalle.id','asc')
                            ->get();
           return $valorizacion;
        }
        else if($id == 'showtarifario'){
          $tarifario = DB::table('tarifario_joyas')
                            ->where('tarifario_joyas.estado','ACTIVO')
                            ->select(
                              'tarifario_joyas.*'
                            )
                            ->orderBy('tarifario_joyas.id','desc')
                            ->get();
           return $tarifario;
        }
        else if($id=='showdescuentojoya'){
          $where[] = ['valorizacion_descuento.iddescuento_joya',$request->iddescuento_joya];
          $valorizacion = DB::table('valorizacion_descuento')
                          ->where($where)
                          ->select(
                              'valorizacion_descuento.*'
                          )
                          ->orderBy('valorizacion_descuento.id','desc')
                          ->get();
          return $valorizacion;
        }
        else if($id=='show_constituciongarantia'){
          $where[] = ['credito_gestiondepositario.constituciongarantia_id',$request->constituciongarantia_id];
          $credito_gestiondepositario = DB::table('credito_gestiondepositario')
                          ->where('credito_gestiondepositario.estado_id',1)
                          ->where($where)
                          ->select(
                              'credito_gestiondepositario.custodiagarantia_id as custodiagarantia_id',
                              'credito_gestiondepositario.custodiagarantia_nombre as custodiagarantia_nombre',
                              'credito_gestiondepositario.doeruc as doeruc',
                              'credito_gestiondepositario.nombre as nombre',
                          )
                          ->orderBy('credito_gestiondepositario.custodiagarantia_nombre','asc')
                          ->distinct()
                          ->get();
          return $credito_gestiondepositario;
        }
        else if($id=='show_custodiagarantia'){
          
          $cliente = DB::table('users')->whereId($request->idcliente)->first();
          $cliente_representante = DB::table('s_users_prestamo')->where('id_s_users',$request->idcliente)->first();
          //dd($cliente_representante);
          $credito_gestiondepositario = DB::table('credito_gestiondepositario')
                          ->where('credito_gestiondepositario.constituciongarantia_id',$request->constituciongarantia_id)
                          ->where('credito_gestiondepositario.doeruc',$request->doeruc)
                          ->where('credito_gestiondepositario.nombre',$request->nombre)
                          ->where('credito_gestiondepositario.estado_id',1)
                          ->first();
          
          return [
              'credito_gestiondepositario' => $credito_gestiondepositario,
              'cliente' => $cliente,
              'cliente_representante' => $cliente_representante,
          ];
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        
      
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      $tipo_garantia = DB::table('tipo_garantia')->get();
      $estado_garantia = DB::table('estado_garantia')->get();
      $estado_garantia_ref = DB::table('estado_garantia_ref')->get();
      $tipo_joyas = DB::table('tipo_joyas')->where('tipo_joyas.estado','ACTIVO')->get();
      $metodo_valorizacion = DB::table('metodo_valorizacion')->get();
      $descuento_joya = DB::table('descuento_joya')->get();
      
      $garantias = DB::table('garantias')
          ->join('users','users.id','garantias.idcliente')
          ->leftJoin('users as responsable','responsable.id','garantias.idresponsable')
          ->where('garantias.id',$id)
          ->select(
              'garantias.*',
              'users.nombrecompleto as nombrecliente',
              'responsable.nombrecompleto as responsablenombrecliente'
          )
          ->orderBy('garantias.id','desc')
          ->first();
      
      
      if($request->input('view') == 'editar') {
    
        $propios = DB::table('users')
            ->join('credito','credito.idcliente','users.id')
            ->join('credito_garantia','credito_garantia.idcredito','credito.id')
            ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
            ->where('credito.idestadocredito',1)
            ->whereIn('credito.estado',['PENDIENTE','PROCESO','APROBADO','DESEMBOLSADO'])
            ->where('credito_garantia.idgarantias',$id)
            ->where('credito_garantia.tipo','<>','AVAL')
            ->select(
                'users.*',
                'credito.id as idcredito',
                'credito.idforma_credito as idforma_credito',
                'credito.cuenta as cuenta',
                'credito.monto_solicitado as monto_solicitado',
                'credito_prendatario.modalidad as modalidadproductocredito',
            )
            ->get();
        $garantia_credito = DB::table('credito_garantia')
            ->join('credito','credito.id','credito_garantia.idcredito')
            ->where('credito_garantia.idgarantias',$id)
            ->where('credito.idestadocredito',1)
            ->whereIn('credito.estado',['PENDIENTE','PROCESO','APROBADO','DESEMBOLSADO'])
            ->first();
        
        if($garantia_credito==''){
          $garantia_credito = DB::table('credito_garantia')
              ->join('credito','credito.id','credito_garantia.idcredito')
              ->where('credito_garantia.idgarantias',$id)
              ->where('credito.idestadocredito',2)
              ->where('credito_garantia.idestadoentrega',1)
              ->whereIn('credito.estado',['PENDIENTE','PROCESO','APROBADO','DESEMBOLSADO'])
              ->first();
        }
        $credito_polizaseguro = DB::table('credito_polizaseguro')->where('id_cliente',$garantias->idcliente)->get();

        return view(sistema_view().'/garantias/edit',[
          'tienda' => $tienda,
          'garantias' => $garantias,
          'garantia_credito' => $garantia_credito,
          'tipo_joyas' => $tipo_joyas,
          'tipo_garantia' => $tipo_garantia,
          'estado_garantia' => $estado_garantia,
          'estado_garantia_ref' => $estado_garantia_ref,
          'metodo_valorizacion' => $metodo_valorizacion,
          'descuento_joya' => $descuento_joya,
          'propios' => $propios,
          'idtienda' => $idtienda,
          'credito_polizaseguro' => $credito_polizaseguro,
        ]);
      }
      else if($request->input('view') == 'modificar'){
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1,2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
        return view(sistema_view().'/garantias/modificar',[
          'tienda' => $tienda,
          'garantias' => $garantias,
          'tipo_garantia' => $tipo_garantia,
          'estado_garantia' => $estado_garantia,
          'estado_garantia_ref' => $estado_garantia_ref,
          'idtienda' => $idtienda,
          'usuarios' => $usuarios,
          'val' => $request->val,
        ]);
      }
      else if($request->input('view') == 'modificar_depositario'){

        $cliente = DB::table('users')->whereId($id)->first();
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1,2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
        return view(sistema_view().'/garantias/modificar_depositario',[
          'tienda' => $tienda,
          'cliente' => $cliente,
          'usuarios' => $usuarios,
          'val' => $request->val,
        ]);
      }
      else if($request->input('view') == 'eliminar'){
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->whereIn('users_permiso.idpermiso',[1,2])
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
        return view(sistema_view().'/garantias/delete',[
          'tienda' => $tienda,
          'garantias' => $garantias,
          'tipo_garantia' => $tipo_garantia,
          'estado_garantia' => $estado_garantia,
          'estado_garantia_ref' => $estado_garantia_ref,
          'idtienda' => $idtienda,
          'usuarios' => $usuarios,
        ]);
      }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        if($request->input('view') == 'modificar') {
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
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.',
                'idresponsable'   => $idresponsable
            ]);
        }
        elseif($request->input('view') == 'editar') {

            $rules = [
                'idcliente' => 'required',           
                'idtipogarantia' => 'required',              
                'descripcion' => 'required',                  
                'serie_motor_partida' => 'required',                
                'modelo_tipo' => 'required',              
                'idestado_garantia' => 'required',           
                'color' => 'required',             
                'idresponsable_modificado' => 'required', 
                'idestado_garantia_ref' => 'required', 
                //'constituciongarantia_id' => 'required',  
                //'custodiagarantia_id' => 'required',              
            ];
          
            $messages = [
                'idcliente.required' => 'El "Cliente" es Obligatorio.',
                'idtipogarantia.required' => 'El "Tipo de Garantia" es Obligatorio.',
                'descripcion.required' => 'La "Descripción" es Obligatorio.',
                'serie_motor_partida.required' => 'La "Serie/Motor/N° Partida" es Obligatorio.',
                'modelo_tipo.required' => 'La "Modelo/Tipo" es Obligatorio.',
                'idestado_garantia.required' => 'El "Estado" es Obligatorio.',
                'color.required' => 'El "Color" es Obligatorio.',
                'idresponsable_modificado.required' => 'El "Autorizado" es Obligatorio.',
                'idestado_garantia_ref.required' => 'El "Estado de Ref." es Obligatorio.',
                //'constituciongarantia_id.required' => 'El "Constitución de la Garantía Mobiliaria" es Obligatorio.',
                //'custodiagarantia_id.required' => 'El "Custodia de Garantía" es Obligatorio.',
            ];

            $this->validate($request,$rules,$messages);
          
            DB::table('garantias')->whereId($id)->update([
               'fecharegistro'          => Carbon::now(),
               'idcliente'              => $request->input('idcliente'),
               'idtipogarantia'         => $request->input('idtipogarantia'),
               'descripcion'            => $request->input('descripcion'),
               'serie_motor_partida'    => $request->input('serie_motor_partida'),
               'chasis'                 => $request->input('chasis')!=''?$request->input('chasis'):'',
               'modelo_tipo'            => $request->input('modelo_tipo'),
               'otros'                  => $request->input('otros')!=''?$request->input('otros'):'',
               'idestado_garantia'      => $request->input('idestado_garantia'),
               'color'                  => $request->input('color'),
               'fabricacion'            => $request->input('fabricacion')!=''?$request->input('fabricacion'):'',
               'compra'                 => $request->input('compra')!=''?$request->input('compra'):'',
               'placa'                  => $request->input('placa')!=''?$request->input('placa'):'',
               'cobertura'              => $request->input('cobertura'),
               'valorcomercial'         => $request->input('valorcomercial'),
               'accesorio_doc'          => $request->input('accesorio_doc')!=''?$request->input('accesorio_doc'):'',
               'detalle_garantia'       => $request->input('detalle_garantia')!=''?$request->input('detalle_garantia'):'',
               'idestado_garantia_ref'  => $request->input('idestado_garantia_ref'),
                'idmetodo_valorizacion'    => $request->input('idmetodo_valorizacion') != 6 ? $request->input('idmetodo_valorizacion') : 0,
               'idtipo_garantia_detalle'  => $request->input('idtipogarantia') != 6 ? $request->input('idtipo_garantia_detalle') : 0,
               'valor_mercado'            => $request->input('valor_mercado'),
               'porcentajecobertura'      => $request->input('porcentajecobertura'),
               'porcentajevalorcomercial' => $request->input('porcentajevalorcomercial'),
              
               'idtipo_joyas'             => $request->input('idtipogarantia') == 6 ? $request->input('idtipo_joyas') : 0,
               'idtarifario_joya'         => $request->input('idtipogarantia') == 6 ? $request->input('idtarifario_joya') : 0,
               'peso_gramos'              => $request->input('peso_gramos'),
               'peso_neto'                => $request->input('peso_neto'),
              
               'iddescuento_joya'         => $request->input('idtipogarantia') == 6 ? ($request->input('iddescuento_joya')!='null'?$request->input('iddescuento_joya'):0) : 0,
               'idvalorizacion_descuento' => $request->input('idtipogarantia') == 6 ? ($request->input('idvalorizacion_descuento')!=''?$request->input('idvalorizacion_descuento'):0) : 0,
               'idresponsable'            => $request->idresponsable_modificado,

 
            ]);
          
            // poliza de seguro
          
            /*DB::table('credito_polizaseguro')->where('id_garantia',$id)->delete();
            
          
            foreach(json_decode($request->seleccionar_polizaseguro) as $value){
                DB::table('credito_polizaseguro')
                    ->insert([
                        'numero_poliza' => $value->numero_poliza,
                        'aseguradora' => $value->aseguradora,
                        'prima_recio' => $value->prima_recio,
                        'beneficiario' => $value->beneficiario,
                        'asegurado' => $value->asegurado,
                        'tomador' => $value->tomador,
                        'vigencia_desde' => $value->vigencia_desde,
                        'vigencia_hasta' => $value->vigencia_hasta,
                        'id_garantia' => $id,
                        'idestado' => 1,
                    ]);
            }*/
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'editar_depositario') {

            $rules = [      
                'idresponsable_modificado' => 'required', 
                'constituciongarantia_id' => 'required',  
                'custodiagarantia_id' => 'required',              
            ];
          
            $messages = [
                'idresponsable_modificado.required' => 'El "Autorizado" es Obligatorio.',
                'constituciongarantia_id.required' => 'El "Constitución de la Garantía Mobiliaria" es Obligatorio.',
                'custodiagarantia_id.required' => 'El "Custodia de Garantía" es Obligatorio.',
            ];

            $this->validate($request,$rules,$messages);
          
            DB::table('users')->whereId($id)->update([
               'idresponsable_depositario' => $request->idresponsable_modificado,
                'custodiagarantia_id' => $request->custodiagarantia_id,
                'custodiagarantia_nombre' => $request->custodiagarantia_nombre,
                'gd_nombre' => $request->gd_nombre,
                'gd_doeruc' => $request->gd_doeruc,
                'gd_direccion' => $request->gd_direccion,
                'gd_representante_doeruc' => $request->gd_representante_doeruc,
                'gd_representante_nombre' => $request->gd_representante_nombre,
                'constituciongarantia_id' => $request->constituciongarantia_id,
                'constituciongarantia_nombre' => $request->constituciongarantia_nombre,
            ]);
          
            // poliza de seguro
          
            $credito_polizaseguro = DB::table('credito_polizaseguro')->where('id_cliente',$id)->delete();
            
            /*foreach(json_decode($request->seleccionar_polizaseguro) as $value){
                if($value->custodiagarantia_id==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'La Custodia de Garantía Obligatorio!!.'
                    ]);
                }
                if($value->estado_id==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El Estado Obligatorio!!.'
                    ]);
                }
            }*/
          
            foreach(json_decode($request->seleccionar_polizaseguro) as $value){
                DB::table('credito_polizaseguro')
                    ->insert([
                        'numero_poliza' => $value->numero_poliza,
                        'aseguradora' => $value->aseguradora,
                        'prima_recio' => $value->prima_recio,
                        'beneficiario' => $value->beneficiario,
                        'asegurado' => $value->asegurado,
                        'tomador' => $value->tomador,
                        'vigencia_desde' => $value->vigencia_desde,
                        'vigencia_hasta' => $value->vigencia_hasta,
                        'id_cliente' => $id,
                        'idestado' => 1,
                    ]);
            }
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
    
    }


    public function destroy(Request $request, $idtienda, $id)
    {
//         $request->user()->authorizeRoles($request->path(),$idtienda);
      if( $request->input('view') == 'eliminar' ){
        
        
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
            DB::table('garantias')->whereId($id)->update([
               'fechaeliminado'           => Carbon::now(),
               'idresponsable'            => $idresponsable,
               'idestadoeliminado'        => 2,
            ]);
        
            /*return response()->json([
                'resultado'           => 'ERROR',
                'mensaje'             => 'Se ha eliminado correctamente.',
            ]);*/
        
      }
      
    
    }
}
