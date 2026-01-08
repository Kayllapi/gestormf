<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class FeriadosController extends Controller
{
    public function __construct()
    {
        
    }
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/feriados/tabla',[
              'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->view == 'registrar') {
            return view(sistema_view().'/feriados/create',[
                'tienda' => $tienda,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
      
        if($request->input('view') == 'registrar') {
            $rules = [                
                'fecha_feriado' => 'required',                 
                'motivo_feriado' => 'required',                 
            ];
          
            $messages = [
                'fecha_feriado.required' => 'El Campo es Obligatorio.',
                'motivo_feriado.required' => 'El Campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
              
            DB::table('feriados')->insertGetId([
              'fecha_feriado'  => $request->input('fecha_feriado'),
              'motivo_feriado' => $request->input('motivo_feriado'),
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showferiados'){
          
          
          $feriados = DB::table('feriados')
                            ->select(
                                'feriados.*'          
                            )
                            ->orderBy('feriados.fecha_feriado','asc')
                            ->get();
          
          $html = '';
          foreach($feriados as $key => $value){
              
              $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data(this)'>
                            <td>".($key+1)."</td>
                            <td>{$value->fecha_feriado}</td>
                            <td>{$value->motivo_feriado}</td>
                        </tr>";
          }
          return array(
            'html' => $html,
            'cantidadferiados' => count($feriados)
          );
          
        }

    }

    public function edit(Request $request, $idtienda, $id)
    {
        
      
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
      $feriado = DB::table('feriados')
                            ->where('feriados.id',$id)
                            ->select(
                                'feriados.*'          
                            )
                            ->first();
      
      
      if($request->input('view') == 'editar') {

        return view(sistema_view().'/feriados/edit',[
          'tienda' => $tienda,
          'feriado' => $feriado,
        ]);
      }
      else if($request->input('view') == 'eliminar'){
        return view(sistema_view().'/feriados/delete',[
          'tienda' => $tienda,
          'feriado' => $feriado,
          
        ]);
      }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        if($request->input('view') == 'editar') {
          $rules = [                
                'fecha_feriado' => 'required',                 
                'motivo_feriado' => 'required',                 
            ];
          
            $messages = [
                'fecha_feriado.required' => 'El Campo es Obligatorio.',
                'motivo_feriado.required' => 'El Campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
            DB::table('feriados')->whereId($id)->update([
              'fecha_feriado'  => $request->input('fecha_feriado'),
              'motivo_feriado' => $request->input('motivo_feriado'),
            ]);
          return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
           
        }
    
    }


    public function destroy(Request $request, $idtienda, $id)
    {
      
      if( $request->input('view') == 'eliminar' ){
        DB::table('feriados')->whereId($id)->delete();
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha elimino correctamente.'
        ]);
      }
      
    
    }
}
