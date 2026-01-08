<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class BancoController extends Controller
{
    public function __construct()
    {
        
    }
    public function index(Request $request,$idtienda)
    {
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/banco/tabla',[
              'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        if($request->view == 'registrar') {
            return view(sistema_view().'/banco/create',[
                'tienda' => $tienda,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
      
        if($request->input('view') == 'registrar') {
            $rules = [                
                'nombre' => 'required',                 
                'cuenta' => 'required',                 
            ];
          
            $messages = [
                'nombre.required' => 'El Campo es Obligatorio.',
                'cuenta.required' => 'El Campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
              
            DB::table('banco')->insertGetId([
              'nombre'  => $request->input('nombre'),
              'cuenta' => $request->input('cuenta'),
                'estado' => 'ACTIVO'
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showbanco'){
          
          
          $banco = DB::table('banco')
                            ->select(
                                'banco.*'          
                            )
                            ->orderBy('banco.nombre','asc')
                            ->get();
          
          $html = '';
          foreach($banco as $key => $value){
              
              $html .= "<tr data-valor-columna='{$value->id}' onclick='show_data(this)'>
                            <td>".($key+1)."</td>
                            <td>{$value->nombre}</td>
                            <td>{$value->cuenta}</td>
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
      $feriado = DB::table('banco')
                            ->where('banco.id',$id)
                            ->select(
                                'banco.*'          
                            )
                            ->first();
      
      
      if($request->input('view') == 'editar') {

        return view(sistema_view().'/banco/edit',[
          'tienda' => $tienda,
          'feriado' => $feriado,
        ]);
      }
      else if($request->input('view') == 'eliminar'){
        return view(sistema_view().'/banco/delete',[
          'tienda' => $tienda,
          'feriado' => $feriado,
          
        ]);
      }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        if($request->input('view') == 'editar') {
          $rules = [                
                'nombre' => 'required',                 
                'cuenta' => 'required',                 
            ];
          
            $messages = [
                'nombre.required' => 'El Campo es Obligatorio.',
                'cuenta.required' => 'El Campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
            DB::table('banco')->whereId($id)->update([
              'nombre'  => $request->input('nombre'),
              'cuenta' => $request->input('cuenta'),
                'estado' => $request->input('estado')
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
        DB::table('banco')->whereId($id)->delete();
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha elimino correctamente.'
        ]);
      }
      
    
    }
}
