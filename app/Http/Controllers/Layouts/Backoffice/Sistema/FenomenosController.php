<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use PDF;
use Carbon\Carbon;

class FenomenosController extends Controller
{
    public function __construct()
    {
        $this->tipo_giro_economico = DB::table('tipo_giro_economico')->get();
    }
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/fenomenos/tabla',[
              'tienda' => $tienda
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        if($request->view == 'registrar') {
            return view(sistema_view().'/fenomenos/create',[
                'tienda' => $tienda,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'nombre' => 'required',         
            ];
          
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('fenomenos')->insert([
               'nombre' => $request->input('nombre'),
               'estado' => $request->input('estado'),
            ]);

          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id=='show_table'){
            
//             if($request->input('idtipo_giro_economico') != ''){
//               $where[] = ['giro_economico_evaluacion.idtipo_giro_economico', $request->input('idtipo_giro_economico')];  
//             }
//             if($request->input('estado') != ''){
//               $where[] = ['giro_economico_evaluacion.estado', $request->input('estado')];  
//             }
            $giro = DB::table('fenomenos')
//                             ->where($where)
                            ->select(
                                'fenomenos.*',
              
                            )
                            ->orderBy('fenomenos.id','ASC')
                            ->get();
  
            $html = '';
            foreach($giro as $key => $value){

                $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data(this)'>
                              <td>".($key+1)."</td>
                              <td>{$value->nombre}</td>
                              <td>{$value->estado}</td>
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
  
      
      $fenomeno = DB::table('fenomenos')
                      ->where('fenomenos.id',$id)
                      ->select(
                          'fenomenos.*',
                      )
                      ->orderBy('fenomenos.id','desc')
                      ->first();
      
      if($request->input('view') == 'editar') {

        return view(sistema_view().'/fenomenos/edit',[
          'tienda' => $tienda,
          'fenomeno' => $fenomeno,
          'idtienda' => $idtienda,
        ]);
      }
      else if($request->input('view') == 'eliminar'){
        return view(sistema_view().'/fenomenos/delete',[
          'tienda' => $tienda,
          'fenomeno' => $fenomeno,
          'idtienda' => $idtienda,
        ]);
      }

       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        if($request->input('view') == 'editar') {

            $rules = [
                'nombre' => 'required',               
            ];
          
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('fenomenos')->whereId($id)->update([
               'nombre' => $request->input('nombre'),
               'estado' => $request->input('estado'),
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
        DB::table('fenomenos')->whereId($id)->delete();
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha elimino correctamente.'
        ]);
      }
      
    
    }
}
