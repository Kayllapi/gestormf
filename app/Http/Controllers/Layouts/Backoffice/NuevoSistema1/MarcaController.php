<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

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
        json_marca($idtienda,$request->name_modulo);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/nuevosistema/marca/index',[
            'tienda' => $tienda,
        ]);
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/nuevosistema/marca/create',[
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
          
            DB::table('s_marca')->insert([
               'fecharegistro' => Carbon::now(),
               'nombre' => $request->input('nombre'),
               'imagen' => $imagen,
               'idtienda' => $request->input('idtienda')
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
             json_marca($idtienda,$request->name_modulo);
        }
    }
  
    public function edit(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $s_marca = DB::table('s_marca')->whereId($idmarca)->first();
      
        if($request->input('view') == 'editar') {
          
            return view('layouts/backoffice/tienda/nuevosistema/marca/edit',[
              's_marca' => $s_marca,
              'tienda' => $tienda
            ]);
          
        }
        elseif($request->input('view') == 'detalle') {
          return view('layouts/backoffice/tienda/nuevosistema/marca/detalle',[
            'tienda' => $tienda,
            's_marca' => $s_marca,
          ]);
        }
        elseif($request->input('view') == 'eliminar') {
          
            return view('layouts/backoffice/tienda/nuevosistema/marca/delete',[
              's_marca' => $s_marca,
              'tienda' => $tienda
            ]);
          
        }
    }
  
    public function update(Request $request, $idtienda, $s_idmarca)
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

            $s_marca = DB::table('s_marca')->whereId($s_idmarca)->first();
            $imagen = uploadfile($s_marca->imagen,$request->input('imagenant'),$request->file('imagen'),'/public/backoffice/tienda/'.$idtienda.'/sistema/');

            DB::table('s_marca')->whereId($s_idmarca)->update([
               'nombre' => $request->input('nombre'),
               'imagen' => $imagen
            ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
    }
  
    public function destroy(Request $request, $idtienda, $s_idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {

            DB::table('s_marca')
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
