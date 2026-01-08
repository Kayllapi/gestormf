<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class CajaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/caja/tabla',[
                'tienda' => $tienda,
            ]);
        }
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->view == 'registrar') {
            return view(sistema_view().'/caja/create',[
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
          
            DB::table('s_caja')->insert([
                'fecharegistro' => Carbon::now(),
                'nombre' => $request->input('nombre'),
                'idtienda' => $idtienda,
                'idestado' => 1,
            ]);
            
            json_caja($idtienda);
          
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
  
        $s_caja = DB::table('s_caja')
            ->whereId($id)
            ->first();
        if($request->input('view') == 'editar') {
            return view(sistema_view().'/caja/edit',[
              'tienda' => $tienda,
              's_caja' => $s_caja,
            ]);
        }
        elseif($request->input('view') == 'eliminar') {
            return view(sistema_view().'/caja/delete',[
              'tienda' => $tienda,
              's_caja' => $s_caja,
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
          
            DB::table('s_caja')->whereId($id)->update([
               'nombre' => $request->input('nombre'),
            ]);
          
            json_caja($idtienda); 
          
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
          
            $efectivocaja_soles = sistema_caja_efectivo([
                'idtienda'  => $idtienda,
                'idcaja'    => $id,
                'idmoneda'  => 1,
            ]);
            $efectivocaja_dolares = sistema_caja_efectivo([
                'idtienda'  => $idtienda,
                'idcaja'    => $id,
                'idmoneda'  => 2,
            ]);
          
            if($efectivocaja_soles['total']!=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Total en Soles debe estar en 0 para poder eliminar.'
                ]);
            }
            elseif($efectivocaja_dolares['total']!=0){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El Total en Dolares debe estar en 0 para poder eliminar.'
                ]);
            }
            
            DB::table('s_caja')
                ->whereId($id)
                ->where('idtienda',$idtienda)
                ->delete();
          
            json_caja($idtienda);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
