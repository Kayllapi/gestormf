<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class MarcaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/marca/tabla',[
                'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->view == 'registrar') {
            return view(sistema_view().'/marca/create',[
                'tienda' => $tienda
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'nombre' => 'required'
            ];
            $messages = [
                'nombre.required' => 'El "Nombre" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
           
            $imagen = uploadfile('','',$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');
          
            $idmarca = DB::table('s_marca')->insertGetId([
                'fecharegistro' => Carbon::now(),
                'nombre' => $request->nombre,
                'imagen' => $imagen,
                'idtienda' => $idtienda,
                'idestado' => 1,
            ]);
            
            json_marca($idtienda); 
          
            /*json_marca($idtienda,[
                'json'    => 'insert',
                'id'      => $idmarca,
                'nombre'  => $request->nombre,
                'imagen'  => $imagen,
            ]);*/
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.',
                'idmarca'   => $idmarca,
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        //
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $s_marca = DB::table('s_marca')
            ->where('s_marca.id',$id)
            ->first();
      
        if($request->input('view') == 'editar') {
            return view(sistema_view().'/marca/edit',[
              'tienda' => $tienda,
              's_marca' => $s_marca,
            ]);
        }
        elseif($request->input('view') == 'eliminar') {
            return view(sistema_view().'/marca/delete',[
              'tienda' => $tienda,
              's_marca' => $s_marca,
            ]);
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'editar') {
            $rules = [
              'nombre' => 'required'
            ];
            $messages = [
              'nombre.required' => 'El "Nombre" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
          
            $s_marca = DB::table('s_marca')->whereId($id)->first();
            $imagen = uploadfile($s_marca->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');
          
            DB::table('s_marca')->whereId($id)->update([
               'nombre' => $request->nombre,
               'imagen' => $imagen
            ]);
          
            /*json_marca($idtienda,[
                'json'    => 'update',
                'id'      => $id,
                'nombre'  => $request->nombre,
                'imagen'  => $imagen,
            ]);*/
            
            json_marca($idtienda); 
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
            
    }


    public function destroy(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {

            $s_marca = DB::table('s_marca')->whereId($id)->first();
            uploadfile_eliminar($s_marca->imagen,'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            DB::table('s_marca')
                ->where('idtienda',$idtienda)
                ->where('id',$id)
                ->delete();
          
            /*json_marca($idtienda,[
                'json'    => 'delete',
                'id'      => $id,
            ]);*/
            
            json_marca($idtienda); 
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
