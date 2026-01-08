<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Comida;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class ComidaMesaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $where = [];
        if($request->input('numero')!=''){
            $where[] = ['s_comida_mesa.numero',$request->input('numero')];
        }
      
        $s_comida_mesa = DB::table('s_comida_mesa')
            ->where('s_comida_mesa.idtienda',$idtienda)
            ->where($where)
            ->select(
                's_comida_mesa.*'
            )
            ->orderBy('s_comida_mesa.numero','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/comida/comidamesa/index',[
            'tienda' => $tienda,
            's_comida_mesa' => $s_comida_mesa
        ]);
    }

    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/sistema/comida/comidamesa/create',[
            'tienda' => $tienda
        ]);
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
            $rules = [
                'numero' => 'required'
            ];
            $messages = [
                'numero.required' => 'El "Número" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);

            DB::table('s_comida_mesa')->insert([
                'fecharegistro' => Carbon::now(),
                'numero' => $request->input('numero'),
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

    public function edit(Request $request, $idtienda, $idmesa)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $s_comida_mesa = DB::table('s_comida_mesa')
            ->whereId($idmesa)
            ->first();
      
        if($request->input('view') == 'editar') {
            return view('layouts/backoffice/tienda/sistema/comida/comidamesa/edit',[
              's_comida_mesa' => $s_comida_mesa,
              'tienda' => $tienda
            ]);
        }
        elseif($request->input('view') == 'eliminar') {
            return view('layouts/backoffice/tienda/sistema/comida/comidamesa/delete',[
              's_comida_mesa' => $s_comida_mesa,
              'tienda' => $tienda
            ]);
        }
    }

    public function update(Request $request, $idtienda, $s_idmesa)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);

        if($request->input('view') == 'editar') {
            $rules = [
              'numero' => 'required'
            ];
            $messages = [
              'numero.required' => 'El "Número" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('s_comida_mesa')->whereId($s_idmesa)->update([
               'numero' => $request->input('numero'),
            ]);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
    }

    public function destroy(Request $request, $idtienda, $s_idmesa)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
          
            DB::table('s_comida_mesa')
                ->where('idtienda',$idtienda)
                ->where('id',$s_idmesa)
                ->delete();
          
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }
    }
}
