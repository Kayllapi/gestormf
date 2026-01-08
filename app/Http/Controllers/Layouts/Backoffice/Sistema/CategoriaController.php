<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class CategoriaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/categoria/tabla',[
                'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->view == 'registrar') {
            $s_categoria = '';
            if(isset($request->idcategoria)) {
                $s_categoria = DB::table('s_categoria')
                    ->where('s_categoria.id',$request->idcategoria)
                    ->first();
            }
            return view(sistema_view().'/categoria/create',[
                'tienda' => $tienda,
                'categoria' => $s_categoria,
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
          
            $idcategoria = DB::table('s_categoria')->insertGetId([
                'fecharegistro' => Carbon::now(),
                'orden' => 0,
                'nombre' => $request->input('nombre'),
                'imagen' => $imagen,
                's_idcategoria' => isset($request->idcategoria)?$request->idcategoria:0,
                'idtienda' => $idtienda,
                'idestado' => 1,
            ]);
            
            json_categoria($idtienda);
          
            return response()->json([
                'resultado'   => 'CORRECTO',
                'mensaje'     => 'Se ha registrado correctamente.',
                'idcategoria' => $idcategoria,
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
      
        $s_categoria = DB::table('s_categoria')
            ->where('s_categoria.id',$id)
            ->first();
      
        if($request->input('view') == 'editar') {
            $categoria_master = DB::table('s_categoria')
                ->where('s_categoria.id',$s_categoria->s_idcategoria)
                ->first();
            return view(sistema_view().'/categoria/edit',[
                'tienda' => $tienda,
                's_categoria' => $s_categoria,
                'categoria_master' => $categoria_master,
            ]);
        }
        elseif($request->input('view') == 'eliminar') {
            $categoria_master = DB::table('s_categoria')
                ->where('s_categoria.id',$s_categoria->s_idcategoria)
                ->first();
            return view(sistema_view().'/categoria/delete',[
                'tienda' => $tienda,
                's_categoria' => $s_categoria,
                'categoria_master' => $categoria_master,
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
          
            $s_categoria = DB::table('s_categoria')->whereId($id)->first();
            $imagen = uploadfile($s_categoria->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');
          
            DB::table('s_categoria')->whereId($id)->update([
               'nombre' => $request->input('nombre'),
               'imagen' => $imagen
            ]);
          
            json_categoria($idtienda); 
          
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

            $countcategorias_1 = DB::table('s_categoria')
                ->where('idtienda',$idtienda)
                ->where('s_idcategoria',$id)
                ->count();
            if($countcategorias_1>0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Hay Sub categorias, no se puede eliminar.'
                ]);
            }
            $s_categoria = DB::table('s_categoria')->whereId($id)->first();
            uploadfile_eliminar($s_categoria->imagen,'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            DB::table('s_categoria')
                ->where('idtienda',$idtienda)
                ->where('id',$id)
                ->delete();
  
            json_categoria($idtienda); 
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
