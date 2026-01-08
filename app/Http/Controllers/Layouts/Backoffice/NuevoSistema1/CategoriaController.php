<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

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
      
        json_categoria($idtienda,$request->name_modulo);
        return view('layouts/backoffice/tienda/nuevosistema/categoria/index',[
            'tienda' => $tienda,
        ]);
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/nuevosistema/categoria/create',[
            'tienda' => $tienda
        ]);
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
          
            DB::table('s_categoria')->insert([
               'fecharegistro' => Carbon::now(),
                'orden' => 0,
                'nombre' => $request->input('nombre'),
                'imagen' => $imagen,
                's_idcategoria' => 0,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if ($id == 'show-moduloactualizar'){  
              json_categoria($idtienda,$request->name_modulo);
        }
    }

    public function edit(Request $request, $idtienda, $idcategoria)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $s_categoria = DB::table('s_categoria')->whereId($idcategoria)->first();
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        if($request->input('view') == 'editar') {
            return view('layouts/backoffice/tienda/nuevosistema/categoria/edit',[
                's_categoria' => $s_categoria,
                'tienda' => $tienda
            ]);
        }
        elseif($request->input('view') == 'detalle') {
            return view('layouts/backoffice/tienda/nuevosistema/categoria/detalle',[
              'tienda' => $tienda,
              's_categoria' => $s_categoria,
            ]);
        }
        elseif($request->input('view') == 'eliminar') {
            return view('layouts/backoffice/tienda/nuevosistema/categoria/delete',[
                's_categoria' => $s_categoria,
                'tienda' => $tienda
            ]);
        }
    }

    public function update(Request $request, $idtienda, $s_idcategoria)
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
          
            $s_categoria = DB::table('s_categoria')->whereId($s_idcategoria)->first();
          
            $imagen = uploadfile($s_categoria->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');
          
            DB::table('s_categoria')->whereId($s_idcategoria)->update([
               'nombre' => $request->input('nombre'),
               'imagen' => $imagen,
               'idestado' => $request->input('idestado')
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
    }

    public function destroy(Request $request, $idtienda, $s_idcategoria)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
            $countcategorias_1 = DB::table('s_categoria')
                ->where('idtienda',$idtienda)
                ->where('s_idcategoria',$s_idcategoria)
                ->count();
            if($countcategorias_1>0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'Hay Sub categorias, no se puede eliminar.'
                ]);
            }

            DB::table('s_categoria')
                ->where('id',$s_idcategoria)
                ->where('idtienda',$idtienda)
                ->update([
                  'fechaeliminado' => Carbon::now(),
                  'idestado'=> 3
                ]);
          
           
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
