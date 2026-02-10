<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use PDF;
use Carbon\Carbon;

class ResponsableAprobacionController extends Controller
{
    public function __construct()
    {
        $this->tipo_giro_economico = DB::table('tipo_giro_economico')->get();
    }
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $permisos = DB::table('permiso')
                    ->select(
                        'permiso.*'
                    )
                    ->orderBy('permiso.rango','asc')
                    ->get();
        $nivelaprobacions_prendario = DB::table('nivelaprobacion')
                    ->where('idtipocredito',1)
                    ->select(
                        'nivelaprobacion.*'
                    )
                    ->orderBy('nivelaprobacion.id','asc')
                    ->get();
      
        $nivelaprobacions_noprendario = DB::table('nivelaprobacion')
                    ->where('idtipocredito',2)
                    ->select(
                        'nivelaprobacion.*'
                    )
                    ->orderBy('nivelaprobacion.id','asc')
                    ->get();

        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/responsableaprobacion/tabla',[
              'tienda' => $tienda,
              'permisos' => $permisos,
              'nivelaprobacions_prendario' => $nivelaprobacions_prendario,
              'nivelaprobacions_noprendario' => $nivelaprobacions_noprendario
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $usuarios = DB::table('users')
                  ->where('users.idestado',1)
                  ->where('users.idtipousuario',1)
                  ->where('users.id','<>',1)
                  ->select(
                      'users.*'
                  )
                  ->orderBy('users.id','desc')
                  ->get();
      
        if($request->view == 'registrar') {
            return view(sistema_view().'/responsableaprobacion/create',[
                'tienda' => $tienda,
                'usuarios' => $usuarios
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            
            $permiso_credito_prendario = json_decode($request->input('permiso_credito_prendario'),2);
            $permiso_credito_noprendario = json_decode($request->input('permiso_credito_noprendario'),2);
            DB::table('nivelaprobacion')->delete();
            foreach($permiso_credito_prendario as $value){
              DB::table('nivelaprobacion')->insert([
                'fecharegistro'   => Carbon::now(),
                'nivelaprobacionnombre' => $value['nombre_aprobacion'],
                'riesgocredito1' => $value['riesgocredito_one'],
                'riesgocredito2' => $value['riesgocredito_two'],
                'nivelaprobacion' => json_encode($value['data_nivelaprobacion']),
                'autonomiaadministracion' => json_encode($value['data_autonomiaadministracion']),
                'autonomiagerencia' => json_encode($value['data_autonomiagerencia']),
                'idtipocredito' => 1,
              ]);
            }
          
            foreach($permiso_credito_noprendario as $value){
              DB::table('nivelaprobacion')->insert([
                'fecharegistro'   => Carbon::now(),
                'nivelaprobacionnombre' => $value['nombre_aprobacion'],
                'riesgocredito1' => $value['riesgocredito_one'],
                'riesgocredito2' => $value['riesgocredito_two'],
                'nivelaprobacion' => json_encode($value['data_nivelaprobacion']),
                'autonomiaadministracion' => json_encode($value['data_autonomiaadministracion']),
                'autonomiagerencia' => json_encode($value['data_autonomiagerencia']),
                'idtipocredito' => 2,
              ]);
            }
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
            dd("---");
            /*$rules = [
                'select_nivelaprobacion' => 'required',            
            ];
          
            $messages = [
                'select_nivelaprobacion.required' => 'El "Responsable" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);*/
          
            $select_creditosprendarios = explode('/&/', $request->input('select_creditosprendarios'));
            for($i = 1;$i <  count($select_creditosprendarios);$i++){
                $item = explode('/,/', $select_creditosprendarios[$i]);
                if($item[0]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El campo NIVELES DE APROBACIÓN es obligatorio.'
                    ]);
                    break;
                }
                if($item[1]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El campo RIESGO CREDITICIO (S/.) es obligatorio.'
                    ]);
                    break;
                }
                if($item[2]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El campo RIESGO CREDITICIO (S/.) es obligatorio.'
                    ]);
                    break;
                }
            } 
          
            $select_creditosnoprendarios = explode('/&/', $request->input('select_creditosnoprendarios'));
            for($i = 1;$i <  count($select_creditosnoprendarios);$i++){
                $item = explode('/,/', $select_creditosnoprendarios[$i]);
                if($item[0]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El campo NIVELES DE APROBACIÓN es obligatorio.'
                    ]);
                    break;
                }
                if($item[1]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El campo RIESGO CREDITICIO (S/.) es obligatorio.'
                    ]);
                    break;
                }
                if($item[2]==''){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El campo RIESGO CREDITICIO (S/.) es obligatorio.'
                    ]);
                    break;
                }
            } 
            
            DB::table('nivelaprobacion')->delete();

            for($i = 1;$i <  count($select_creditosprendarios);$i++){
                $item = explode('/,/', $select_creditosprendarios[$i]);
                DB::table('nivelaprobacion')->insert([
                    'fecharegistro'   => Carbon::now(),
                    'nivelaprobacionnombre' => $item[0],
                    'riesgocredito1' => $item[1],
                    'riesgocredito2' => $item[2],
                    'nivelaprobacion' => $item[3],
                    'autonomiaadministracion' => $item[4],
                    'autonomiagerencia' => $item[5],
                    'idtipocredito' => 1,
                ]);
            } 
          
            for($i = 1;$i <  count($select_creditosnoprendarios);$i++){
                $item = explode('/,/', $select_creditosnoprendarios[$i]);
                DB::table('nivelaprobacion')->insert([
                    'fecharegistro'   => Carbon::now(),
                    'nivelaprobacionnombre' => $item[0],
                    'riesgocredito1' => $item[1],
                    'riesgocredito2' => $item[2],
                    'nivelaprobacion' => $item[3],
                    'autonomiaadministracion' => $item[4],
                    'autonomiagerencia' => $item[5],
                    'idtipocredito' => 2,
                ]);
            } 
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id=='show_table'){
            
           
            $giro = DB::table('responsable_aprobacion')
                            ->join('users','users.id','responsable_aprobacion.idusers')
                            
                            ->select(
                                'responsable_aprobacion.*',
                                'users.nombrecompleto as nombreresponsable'
              
                            )
                            ->orderBy('responsable_aprobacion.id','ASC')
                            ->get();
  
            $html = '';
            foreach($giro as $key => $value){

                $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data(this)'>
                              <td>".($key+1)."</td>
                              <td>{$value->nombreresponsable}</td>
                          </tr>";
            }
            return array(
              'html' => $html
            );
        }
        
    }

    public function edit(Request $request, $idtienda, $id)
    {
        
      
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      $usuarios = DB::table('users')
                  ->where('users.idestado',1)
                  ->where('users.idtipousuario',1)
                  ->where('users.id','<>',1)
                  ->select(
                      'users.*'
                  )
                  ->orderBy('users.id','desc')
                  ->get();
      
      $responsable = DB::table('responsable_aprobacion')
                      ->join('users','users.id','responsable_aprobacion.idusers')
                      ->where('responsable_aprobacion.id',$id)
                      ->select(
                          'responsable_aprobacion.*',
                          'users.nombrecompleto as nombreresponsable'
                      )
                      ->orderBy('responsable_aprobacion.id','desc')
                      ->first();
      
      if($request->input('view') == 'editar') {

        return view(sistema_view().'/responsableaprobacion/edit',[
          'tienda' => $tienda,
          'responsable' => $responsable,
          'usuarios' => $usuarios,
          'idtienda' => $idtienda,
        ]);
      }
      else if($request->input('view') == 'eliminar'){
        return view(sistema_view().'/responsableaprobacion/delete',[
          'tienda' => $tienda,
          'responsable' => $responsable,
          'idtienda' => $idtienda,
        ]);
      }
     
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        if( $request->input('view') == 'editar' ){
          $rules = [
              'idresponsable' => 'required',             
          ];

          $messages = [
              'idresponsable.required' => 'El "Responsable" es Obligatorio.',
          ];
          $this->validate($request,$rules,$messages);

          DB::table('responsable_aprobacion')->whereId($id)->update([
             'idusers' => $request->input('idresponsable'),
          ]);
          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha registrado correctamente.'
          ]);
        }
        
    
    }


    public function destroy(Request $request, $idtienda, $id)
    {
//         $request->user()->authorizeRoles($request->path(),$idtienda);
      if( $request->input('view') == 'eliminar' ){
        DB::table('responsable_aprobacion')->whereId($id)->delete();
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha elimino correctamente.'
        ]);
      }
      
    
    }
}
