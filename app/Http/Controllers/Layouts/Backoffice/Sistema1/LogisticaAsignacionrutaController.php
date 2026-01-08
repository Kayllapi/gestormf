<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class LogisticaAsignacionrutaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $where = [];
        $where[] = ['s_logisticaasignacionruta.nombre','LIKE','%'.$request->input('nombre').'%'];
        
        
        $s_logisticaasignacionruta = DB::table('s_logisticaasignacionruta')
            ->where('idtienda',$idtienda)
            ->where('idestado',1)
            ->where($where)
            ->select(
                's_logisticaasignacionruta.*'
            )
            ->orderBy('s_logisticaasignacionruta.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/logisticaasignacionruta/index',[
            'tienda' => $tienda,
            's_logisticaasignacionruta' => $s_logisticaasignacionruta
        ]);
    }

    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/sistema/logisticaasignacionruta/create',[
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

            DB::table('s_logisticaasignacionruta')->insert([
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

    public function edit(Request $request, $idtienda, $idlogisticaasignacionruta)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $s_logisticaasignacionruta = DB::table('s_logisticaasignacionruta')->whereId($idlogisticaasignacionruta)->first();
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'editar') {
          return view('layouts/backoffice/tienda/sistema/logisticaasignacionruta/edit',[
            's_logisticaasignacionruta' => $s_logisticaasignacionruta,
            'tienda' => $tienda
          ]);
          
        }elseif($request->input('view') == 'eliminar') {
          return view('layouts/backoffice/tienda/sistema/logisticaasignacionruta/delete',[
            's_logisticaasignacionruta' => $s_logisticaasignacionruta,
            'tienda' => $tienda
          ]);
          
        }
    }

    public function update(Request $request, $idtienda, $s_idlogisticaasignacionruta)
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

        DB::table('s_logisticaasignacionruta')->whereId($s_idlogisticaasignacionruta)->update([
            'nombre' => $request->input('nombre'),
        ]);
        return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje'   => 'Se ha actualizado correctamente.'
        ]);
      }
    }

    public function destroy(Request $request, $idtienda, $s_idlogisticaasignacionruta)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
            DB::table('s_logisticaasignacionruta')
                ->where('idtienda',$idtienda)
                ->where('id',$s_idlogisticaasignacionruta)
                ->update(['idestado'=>2]);
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }
    }
}
