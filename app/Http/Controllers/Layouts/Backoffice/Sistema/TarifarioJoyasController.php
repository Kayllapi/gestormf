<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class TarifarioJoyasController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $tipo_garantia = DB::table('tipo_garantia')->where('tipo_garantia.estado','ACTIVO')->get();
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/tarifariojoyas/tabla',[
              'tienda' => $tienda,
              'tipo_garantia' => $tipo_garantia,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        
        if($request->view == 'registrar') {
            return view(sistema_view().'/tarifariojoyas/create',[
                'tienda' => $tienda,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        //$request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'tipo' => 'required',         
                'cobertura' => 'required',         
                'precio' => 'required',          
                'valormercado' => 'required',         
            ];
          
            $messages = [
                'tipo.required' => 'El "Tipo de Oro" es Obligatorio.',
                'cobertura.required' => 'La "Cobertura de C.(%)" es Obligatorio.',
                'precio.required' => 'El "Precio Por Gramo (S/)" es Obligatorio.',
                'valormercado.required' => 'El "Valor de mercado (S/)" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('tarifario_joyas')->insert([
               'tipo'        => $request->input('tipo'),
               'cobertura'   => $request->input('cobertura'),
               'precio'      => $request->input('precio'),
               'valormercado'      => $request->input('valormercado'),
               
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

            $tarifario = DB::table('tarifario_joyas')
                            ->select(
                                'tarifario_joyas.*'
                            )
                            ->orderBy('tarifario_joyas.id','desc')
                            ->paginate($request->length,'*',null,($request->start/$request->length)+1);
          

            $tabla = [];
            foreach($tarifario as $value){
                
              $tabla[]=[
                  'id'        => $value->id,
                  'tipo'      => $value->tipo,
                  'cobertura' => $value->cobertura,
                  'valormercado' => $value->valormercado,
                  'precio'    => $value->precio,
                  'fecha'    => $value->fecha_actualizacion,
                  'opcion' => [
                     [
                      'nombre' => 'Editar',
                      'onclick' => '/'.$idtienda.'/tarifariojoyas/'.$value->id.'/edit?view=editar',
                      'icono' => 'edit',
                    ],
                    [
                      'nombre' => 'Eliminar',
                      'onclick' => '/'.$idtienda.'/tarifariojoyas/'.$value->id.'/edit?view=eliminar',
                      'icono' => 'trash',
                    ]
                  ],
              ];
            }
            
            return response()->json([
                'start'           => $request->start,
                'draw'            => $request->draw,
                'recordsTotal'    => $request->length,
                'recordsFiltered' => $tarifario->total(),
                'data'            => $tabla,
            ]);
        }
        
    }

    public function edit(Request $request, $idtienda, $id)
    {
        
      
      $tienda = DB::table('tienda')->whereId($idtienda)->first();
  
      
      $tarifario = DB::table('tarifario_joyas')
                      ->where('tarifario_joyas.id',$id)
                      ->select(
                          'tarifario_joyas.*',
                      )
                      ->orderBy('tarifario_joyas.id','desc')
                      ->first();
      
      if($request->input('view') == 'editar') {

        return view(sistema_view().'/tarifariojoyas/edit',[
          'tienda' => $tienda,
          'tarifario' => $tarifario,
          'idtienda' => $idtienda,
        ]);
      }
      else if($request->input('view') == 'eliminar'){
        return view(sistema_view().'/tarifariojoyas/delete',[
          'tienda' => $tienda,
          'tarifario' => $tarifario,
          'idtienda' => $idtienda,
        ]);
      }
       
    }

    public function update(Request $request, $idtienda, $id)
    {
        
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        if($request->input('view') == 'editar') {

            $rules = [
                'tipo' => 'required',         
                'cobertura' => 'required',         
                'precio' => 'required', 
                'valormercado' => 'required',           
            ];
          
            $messages = [
                'tipo.required' => 'El "Tipo de Oro" es Obligatorio.',
                'cobertura.required' => 'La "Cobertura de C.(%)" es Obligatorio.',
                'precio.required' => 'El "Precio Por Gramo (S/)" es Obligatorio.',
                'valormercado.required' => 'El "Valor de mercado (S/)" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
          
            DB::table('tarifario_joyas')->whereId($id)->update([
               'tipo'        => $request->input('tipo'),
               'cobertura'   => $request->input('cobertura'),
               'precio'      => $request->input('precio'),
               'valormercado'      => $request->input('valormercado'),
               'fecha_actualizacion' => Carbon::now()
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
        DB::table('tarifario_joyas')->whereId($id)->delete();
        return response()->json([
          'resultado' => 'CORRECTO',
          'mensaje'   => 'Se ha elimino correctamente.'
        ]);
      }
      
    
    }
}
