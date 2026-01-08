<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class ValorizacionDescuentoController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $tipo_joyas = DB::table('tipo_joyas')->where('tipo_joyas.estado','ACTIVO')->get();
        
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/valorizaciondescuento/tabla',[
              'tienda' => $tienda,
              'tipo_joyas' => $tipo_joyas,
              
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $tipo_joyas = DB::table('tipo_joyas')->where('tipo_joyas.estado','ACTIVO')->get();
        $descuento_joya = DB::table('descuento_joya')->get();
        if($request->view == 'registrar') {
            return view(sistema_view().'/valorizaciondescuento/create',[
                'tienda' => $tienda,
                'tipo_joyas' => $tipo_joyas,
                'descuento_joya' => $descuento_joya,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
          
            $rules = [       
                'iddescuento_joya' => 'required',         
                'detalle_descuento' => 'required',         
                'descuento' => 'required',        
            ];
          
            $messages = [
                'iddescuento_joya.required'   => 'El campo es Obligatorio.',
                'detalle_descuento.required'  => 'La campo es Obligatorio.',
                'descuento.required'          => 'El campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('valorizacion_descuento')->insert([
               'iddescuento_joya'   => $request->input('iddescuento_joya'),
               'detalle_descuento'  => $request->input('detalle_descuento'),
               'descuento'          => $request->input('descuento'),
               
            ]);

          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {

        if($id == 'showlistavalorizaciondescuento'){
          
            $where[] = ['valorizacion_descuento.iddescuento_joya',$request->iddescuento_joya];
            
            $valorizacion = DB::table('valorizacion_descuento')
                            
                            ->join('descuento_joya','descuento_joya.id','valorizacion_descuento.iddescuento_joya')
                            ->where($where)
                            ->select(
                                'valorizacion_descuento.*',
                                'descuento_joya.nombre as nombre_descuento'
                            )
                            ->orderBy('valorizacion_descuento.id','desc')
                            ->get();
          
          $html = '';
          foreach($valorizacion as $value){
              $html .= "<tr data-valor-columna='{$value->id}' onclick='show_editar_valorizacion(this)'>
                            <td>{$value->nombre_descuento}</td>
                            <td>{$value->detalle_descuento}</td>
                            <td>{$value->descuento}</td>
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
      
      $descuento_joya = DB::table('descuento_joya')->get();
  
      
      $valorizacion = DB::table('valorizacion_descuento')
                      ->where('valorizacion_descuento.id',$id)
                      ->select(
                          'valorizacion_descuento.*'
                      )
                      ->orderBy('valorizacion_descuento.id','desc')
                      ->first();
      
      if($request->input('view') == 'editar') {

        return view(sistema_view().'/valorizaciondescuento/edit',[
          'tienda' => $tienda,
          'descuento_joya' => $descuento_joya,
          'valorizacion' => $valorizacion,
          'idtienda' => $idtienda,
        ]);
      }
      else if($request->input('view') == 'eliminar'){
        return view(sistema_view().'/valorizaciondescuento/delete',[
          'tienda' => $tienda,
          'descuento_joya' => $descuento_joya,
          'valorizacion' => $valorizacion,
          'idtienda' => $idtienda,
        ]);
      }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        if($request->input('view') == 'editar') {

            $rules = [
                      
                'iddescuento_joya' => 'required',         
                'detalle_descuento' => 'required',         
                'descuento' => 'required',        
            ];
          
            $messages = [
                
                'iddescuento_joya.required'   => 'El campo es Obligatorio.',
                'detalle_descuento.required'  => 'La campo es Obligatorio.',
                'descuento.required'          => 'El campo es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('valorizacion_descuento')->whereId($id)->update([
               
               'iddescuento_joya'   => $request->input('iddescuento_joya'),
               'detalle_descuento'  => $request->input('detalle_descuento'),
               'descuento'          => $request->input('descuento'),
               
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
        DB::table('valorizacion_descuento')->whereId($id)->delete();
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha elimino correctamente.'
        ]);
      }
      
    
    }
}
