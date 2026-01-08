<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class GarantiasNoPrendarioController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $tipo_garantia = DB::table('tipo_garantia')->get();
        $estado_garantia = DB::table('estado_garantia')->get();
        $estado_garantia_ref = DB::table('estado_garantia_ref')->get();
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/garantiasnoprendario/tabla',[
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
        $tipo_garantia_noprendaria = DB::table('tipo_garantia_noprendaria')->get();
        
        if($request->view == 'registrar') {
            return view(sistema_view().'/garantiasnoprendario/create',[
                'idcliente' => $request->idcliente,
                'tienda' => $tienda,
                'tipo_garantia_noprendaria' => $tipo_garantia_noprendaria,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
          
            $rules = [
                'idcliente' => 'required',              
                'idtipo_garantia_noprendaria' => 'required',              
                'idsubtipo_garantia_noprendaria' => 'required',              
                'idsubtipo_garantia_noprendaria_ii' => 'required',              
                'descripcion' => 'required',              
                'valor_mercado' => 'required',             
            ];
          
            if($request->input('idtipo_garantia_noprendaria')==1){
                $rules['valor_comercial'] = 'required';
                $rules['valor_realizacion'] = 'required';
            }
          
            $messages = [
                'idcliente.required' => 'El "Cliente" es Obligatorio.',
                'idtipo_garantia_noprendaria.required' => 'El "Tipo Garantia No Prendaria" es Obligatorio.',
                'idsubtipo_garantia_noprendaria.required' => 'El "Sub Tipo I" es Obligatorio.',
                'idsubtipo_garantia_noprendaria_ii.required' => 'El "Sub Tipo II" es Obligatorio.',
                'descripcion.required' => 'La "Descripción de garantía en Propuesta" es Obligatorio.',
                'valor_mercado.required' => 'El "Valor de mercado (S/)" es Obligatorio.',
                'valor_comercial.required' => 'El "Valor comercial (Tasado) (S/)" es Obligatorio.',
                'valor_realizacion.required' => 'El "Valor de realización (Tasado) (S/)" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            if($request->input('valor_mercado')<=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "Valor de mercado (S/)" debe ser mayor a "0.00".'
                ]);
            }
          
            if($request->input('idtipo_garantia_noprendaria')==1){
                if($request->input('valor_comercial')<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Valor comercial (Tasado) (S/)" debe ser mayor a "0.00".'
                    ]);
                }
                if($request->input('valor_realizacion')<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Valor de realización (TasadoTasado) (S/)" debe ser mayor a "0.00".'
                    ]);
                }
            }
          
            DB::table('garantias_noprendarias')->insert([
               'idcliente'                          => $request->input('idcliente'),
               'idtipo_garantia_noprendaria'        => $request->input('idtipo_garantia_noprendaria'),
               'idsubtipo_garantia_noprendaria'     => $request->input('idsubtipo_garantia_noprendaria'),
               'idsubtipo_garantia_noprendaria_ii'  => $request->input('idsubtipo_garantia_noprendaria_ii'),
               'descripcion'                        => $request->input('descripcion'),
               'valor_mercado'                      => $request->input('valor_mercado'),
               'valor_comercial'                    => $request->input('valor_comercial')!=''? $request->input('valor_comercial'): '0.00',
               'valor_realizacion'                  => $request->input('valor_realizacion')!=''? $request->input('valor_comercial'): '0.00',              
               'idresponsable'            => Auth::user()->id,
               
            ]);

          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showlistagarantiasnopredanrio'){
          $cliente = DB::table('users')->whereId($request->idcliente)->select('users.id','users.nombrecompleto','users.identificacion')->first();
          
          $garantias = DB::table('garantias_noprendarias')
                            ->where('garantias_noprendarias.idestadoeliminado',1)
                            ->join('tipo_garantia_noprendaria','tipo_garantia_noprendaria.id','garantias_noprendarias.idtipo_garantia_noprendaria')
                            ->where('garantias_noprendarias.idcliente', $request->idcliente)
                            ->select(
                                'garantias_noprendarias.*',
                                'tipo_garantia_noprendaria.nombre as nombretipogarantia'
                            )
                            ->orderBy('garantias_noprendarias.id','asc')
                            ->get();
          $html = '';
          foreach($garantias as $value){
            
              $garantia_credito = DB::table('credito_garantia')
                  ->join('credito','credito.id','credito_garantia.idcredito')
                  ->where('credito_garantia.idgarantias_noprendarias',$value->id)
                  ->where('credito.idestadocredito',1)
                  ->whereIn('credito.estado',['PENDIENTE','PROCESO','APROBADO','DESEMBOLSADO'])
                  ->first();
            
              $color_garantia = $garantia_credito ? 'style="background-color:#0fb669;"' : '';
              /*if($garantia_credito==''){
                $garantia_credito = DB::table('credito_garantia')
                    ->join('credito','credito.id','credito_garantia.idcredito')
                    ->where('credito_garantia.idgarantias_noprendarias',$value->id)
                    ->where('credito.idestadocredito',2)
                    ->where('credito_garantia.idestadoentrega',1)
                    ->whereIn('credito.estado',['PENDIENTE','PROCESO','APROBADO','DESEMBOLSADO'])
                    ->first();
                $color_garantia = $garantia_credito ? 'style="background-color:#40a7e9;"' : '';
              }*/
              $valormercado = $value->valor_mercado;
              if($value->idtipo_garantia_noprendaria==1){ // 1= A. Garantías Preferidas (G. Real), 2=B. Garantías No Preferidas (G. Personales)
                  $valormercado = $value->valor_realizacion;
              }
              $html .= "<tr {$color_garantia} data-valor-columna='{$value->id}' onclick='show_data(this)'>
                            <td><b>{$value->nombretipogarantia}:</b> {$value->descripcion}</td>
                            <td>S/ {$valormercado}</td>
                        </tr>";
          }
          return array(
            'cliente' => $cliente,
            'html' => $html
          );
          
        }
        else if($id == 'show_subtipo_garantia_noprendaria'){          

          $data = DB::table('subtipo_garantia_noprendaria')
                            ->where('subtipo_garantia_noprendaria.idtipo_garantia_noprendaria',$request->idtipo_garantia_noprendaria)
                            ->select(
                              'subtipo_garantia_noprendaria.*',
                            )
                            ->orderBy('subtipo_garantia_noprendaria.id','asc')
                            ->get();
           return $data;
        } 
        else if($id == 'show_subtipo_garantia_noprendaria_ii'){          

          $data = DB::table('subtipo_garantia_noprendaria_ii')
                            ->where('subtipo_garantia_noprendaria_ii.idsubtipo_garantia_noprendaria',$request->idsubtipo_garantia_noprendaria)
                            ->select(
                              'subtipo_garantia_noprendaria_ii.*',
                            )
                            ->orderBy('subtipo_garantia_noprendaria_ii.id','asc')
                            ->get();
           return $data;
        }
        else if($id == 'showtarifario'){
          $tarifario = DB::table('tarifario_joyas')
                            ->where('tarifario_joyas.estado','ACTIVO')
                            ->select(
                              'tarifario_joyas.*'
                            )
                            ->orderBy('tarifario_joyas.id','asc')
                            ->get();
           return $tarifario;
        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        
      
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      $tipo_garantia_noprendaria = DB::table('tipo_garantia_noprendaria')->get();
  
      
      $garantias = DB::table('garantias_noprendarias')
          ->leftJoin('users as responsable','responsable.id','garantias_noprendarias.idresponsable')
          ->where('garantias_noprendarias.id',$id)
          ->select(
              'garantias_noprendarias.*',
              'responsable.nombrecompleto as responsablenombrecliente'
          )
          ->orderBy('garantias_noprendarias.id','desc')
          ->first();
      
      if($request->input('view') == 'editar') {
        
          $propios = DB::table('users')
              ->join('credito','credito.idcliente','users.id')
              ->join('credito_garantia','credito_garantia.idcredito','credito.id')
              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
              ->where('credito.idestadocredito',1)
              ->whereIn('credito.estado',['PENDIENTE','PROCESO','APROBADO','DESEMBOLSADO'])
              ->where('credito_garantia.idgarantias_noprendarias',$id)
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
        
          $avales = DB::table('users')
              ->join('credito','credito.idcliente','users.id')
              ->join('credito_garantia','credito_garantia.idcredito','credito.id')
              ->join('credito_prendatario','credito_prendatario.id','credito.idcredito_prendatario')
              ->where('credito.idestadocredito',1)
              ->whereIn('credito.estado',['PENDIENTE','PROCESO','APROBADO','DESEMBOLSADO'])
              ->where('credito_garantia.idgarantias_noprendarias',$id)
              ->where('credito_garantia.tipo','AVAL')
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
            ->where('credito_garantia.idgarantias_noprendarias',$id)
            ->where('credito.idestadocredito',1)
            ->whereIn('credito.estado',['PENDIENTE','PROCESO','APROBADO','DESEMBOLSADO'])
            ->first();
        
              if($garantia_credito==''){
                $garantia_credito = DB::table('credito_garantia')
                    ->join('credito','credito.id','credito_garantia.idcredito')
                    ->where('credito_garantia.idgarantias_noprendarias',$id)
                    ->where('credito.idestadocredito',2)
                    ->where('credito_garantia.idestadoentrega',1)
                    ->whereIn('credito.estado',['PENDIENTE','PROCESO','APROBADO','DESEMBOLSADO'])
                    ->first();
              }
        
        return view(sistema_view().'/garantiasnoprendario/edit',[
          'tienda' => $tienda,
          'garantias' => $garantias,
          'garantia_credito' => $garantia_credito,
          'tipo_garantia_noprendaria' => $tipo_garantia_noprendaria,
          'idtienda' => $idtienda,
          'propios' => $propios,
          'avales' => $avales,
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
        return view(sistema_view().'/garantiasnoprendario/modificar',[
          'tienda' => $tienda,
          'garantias' => $garantias,
          'tipo_garantia_noprendaria' => $tipo_garantia_noprendaria,
          'idtienda' => $idtienda,
          'usuarios' => $usuarios,
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
        return view(sistema_view().'/garantiasnoprendario/delete',[
          'tienda' => $tienda,
          'garantias' => $garantias,
          'tipo_garantia_noprendaria' => $tipo_garantia_noprendaria,
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
                'idtipo_garantia_noprendaria' => 'required',              
                'idsubtipo_garantia_noprendaria' => 'required',              
                'idsubtipo_garantia_noprendaria_ii' => 'required',              
                'descripcion' => 'required',              
                'valor_mercado' => 'required',             
            ];
          
            if($request->input('idtipo_garantia_noprendaria')==1){
                $rules['valor_comercial'] = 'required';
                $rules['valor_realizacion'] = 'required';
            }
          
            $messages = [
                'idcliente.required' => 'El "Cliente" es Obligatorio.',
                'idtipo_garantia_noprendaria.required' => 'El "Tipo Garantia No Prendaria" es Obligatorio.',
                'idsubtipo_garantia_noprendaria.required' => 'El "Sub Tipo I" es Obligatorio.',
                'idsubtipo_garantia_noprendaria_ii.required' => 'El "Sub Tipo II" es Obligatorio.',
                'descripcion.required' => 'La "Descripción de garantía en Propuesta" es Obligatorio.',
                'valor_mercado.required' => 'El "Valor de mercado (S/)" es Obligatorio.',
                'valor_comercial.required' => 'El "Valor comercial (Tasado) (S/)" es Obligatorio.',
                'valor_realizacion.required' => 'El "Valor de realización (tasado) (S/)" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            if($request->input('valor_mercado')<=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "Valor de mercado (S/)" debe ser mayor a "0.00".'
                ]);
            }
          
            if($request->input('idtipo_garantia_noprendaria')==1){
                if($request->input('valor_comercial')<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Valor comercial (Tasado) (S/)" debe ser mayor a "0.00".'
                    ]);
                }
                if($request->input('valor_realizacion')<=0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Valor de realización (TasadoTasado) (S/)" debe ser mayor a "0.00".'
                    ]);
                }
            }
          
            DB::table('garantias_noprendarias')->whereId($id)->update([
               'idtipo_garantia_noprendaria'        => $request->input('idtipo_garantia_noprendaria'),
               'idsubtipo_garantia_noprendaria'     => $request->input('idsubtipo_garantia_noprendaria'),
               'idsubtipo_garantia_noprendaria_ii'  => $request->input('idsubtipo_garantia_noprendaria_ii'),
               'descripcion'                        => $request->input('descripcion'),
               'valor_mercado'                      => $request->input('valor_mercado'),
               'valor_comercial'                    => $request->input('valor_comercial')!=''? $request->input('valor_comercial'): '0.00',
               'valor_realizacion'                  => $request->input('valor_realizacion')!=''? $request->input('valor_comercial'): '0.00',
               'idresponsable'                      => $request->idresponsable_modificado,
            ]);
          
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
        
            DB::table('garantias_noprendarias')->whereId($id)->update([
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
