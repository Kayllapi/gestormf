<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class CuentabancariaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/cuentabancaria/tabla',[
                'tienda' => $tienda,
            ]);
        }
            
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->view == 'registrar') {
            return view(sistema_view().'/cuentabancaria/create',[
                'tienda' => $tienda
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'banco' => 'required',
                'numerocuenta' => 'required'
            ];
            $messages = [
                'banco.required' => 'El "banco" es Obligatorio.',
                'numerocuenta.required' => 'El "Número de cuenta" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('s_cuentabancaria')->insert([
                'fecharegistro' => Carbon::now(),
                'banco' => $request->input('banco'),
                'numerocuenta' => $request->input('numerocuenta'),
                'idtienda' => $idtienda,
                'idestado' => 1,
            ]);
            
            json_cuentabancaria($idtienda);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
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
      
        $s_cuentabancaria = DB::table('s_cuentabancaria')
            ->where('s_cuentabancaria.id',$id)
            ->first();
      
        if($request->input('view') == 'editar') {
            return view(sistema_view().'/cuentabancaria/edit',[
              'tienda' => $tienda,
              's_cuentabancaria' => $s_cuentabancaria,
            ]);
        }
        elseif($request->input('view') == 'eliminar') {
            return view(sistema_view().'/cuentabancaria/delete',[
              'tienda' => $tienda,
              's_cuentabancaria' => $s_cuentabancaria,
            ]);
        }
    }

    public function update(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'editar') {
            $rules = [
                'banco' => 'required',
                'numerocuenta' => 'required'
            ];
            $messages = [
                'banco.required' => 'El "banco" es Obligatorio.',
                'numerocuenta.required' => 'El "Número de cuenta" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('s_cuentabancaria')->whereId($id)->update([
                'banco' => $request->input('banco'),
                'numerocuenta' => $request->input('numerocuenta'),
            ]);
          
            json_cuentabancaria($idtienda); 
          
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
          
            DB::table('s_cuentabancaria')
                ->where('idtienda',$idtienda)
                ->where('id',$s_idcuentabancaria)
                ->delete();
  
            json_cuentabancaria($idtienda); 
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
