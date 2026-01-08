<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class LogisticaRutaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $where = [];
        $where[] = ['s_logisticaruta.nombre','LIKE','%'.$request->input('nombre').'%'];
        
        
        $s_logisticaruta = DB::table('s_logisticaruta')
            ->where('idtienda',$idtienda)
            ->where('idestado',1)
            ->where($where)
            ->select(
                's_logisticaruta.*'
            )
            ->orderBy('s_logisticaruta.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/logisticaruta/index',[
            'tienda' => $tienda,
            's_logisticaruta' => $s_logisticaruta
        ]);
    }

    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/sistema/logisticaruta/create',[
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

            DB::table('s_logisticaruta')->insert([
                'fecharegistro' => Carbon::now(),
                'nombre' => $request->input('nombre'),
                'idtienda' => $request->input('idtienda'),
                'idestado' => 1,
            ]);
            return response()->json([
  							'resultado' => 'CORRECTO',
  							'mensaje'   => 'Se ha registrado correctamente.'
  					]);
        }
    }

    public function show(Request $request, $idtienda)
    {
       $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    public function edit(Request $request, $idtienda, $idlogisticaruta)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $s_logisticaruta = DB::table('s_logisticaruta')->whereId($idlogisticaruta)->first();
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'editar') {
          return view('layouts/backoffice/tienda/sistema/logisticaruta/edit',[
            's_logisticaruta' => $s_logisticaruta,
            'tienda' => $tienda
          ]);
          
        }
        elseif($request->input('view') == 'asignarnegocio') {
          return view('layouts/backoffice/tienda/sistema/logisticaruta/asignarnegocio',[
            's_logisticaruta' => $s_logisticaruta,
            'tienda' => $tienda
          ]);
          
        }
        elseif($request->input('view') == 'asignarvendedor') {
          return view('layouts/backoffice/tienda/sistema/logisticaruta/asignarvendedor',[
            's_logisticaruta' => $s_logisticaruta,
            'tienda' => $tienda
          ]);
          
        }
        elseif($request->input('view') == 'eliminar') {
          return view('layouts/backoffice/tienda/sistema/logisticaruta/delete',[
            's_logisticaruta' => $s_logisticaruta,
            'tienda' => $tienda
          ]);
          
        }
    }

    public function update(Request $request, $idtienda, $s_idlogisticaruta)
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

        DB::table('s_logisticaruta')->whereId($s_idlogisticaruta)->update([
            'nombre' => $request->input('nombre'),
        ]);
        return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha actualizado correctamente.'
        ]);
      }
    }

    public function destroy(Request $request, $idtienda, $s_idlogisticaruta)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
            DB::table('s_logisticaruta')
                ->where('idtienda',$idtienda)
                ->where('id',$s_idlogisticaruta)
                ->update(['idestado'=>2]);
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }
    }
}
