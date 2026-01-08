<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema\Transporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class TransporteRutaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $where = [];
        $where[] = ['s_transporteruta.nombre','LIKE','%'.$request->input('nombre').'%'];
        
        
        $s_transporteruta = DB::table('s_transporteruta')
            ->where('idtienda',$idtienda)
            ->where($where)
            ->select(
                's_transporteruta.*'
            )
            ->orderBy('s_transporteruta.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/transporte/transporteruta/index',[
            'tienda' => $tienda,
            's_transporteruta' => $s_transporteruta
        ]);
    }
  
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/sistema/transporte/transporteruta/create',[
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
          
            DB::table('s_transporteruta')->insert([
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

    public function edit(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $s_transporteruta = DB::table('s_transporteruta')->whereId($idmarca)->first();
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'editar') {
          
          return view('layouts/backoffice/tienda/sistema/transporte/transporteruta/edit',[
            's_transporteruta' => $s_transporteruta,
            'tienda' => $tienda
          ]);
          
        }elseif($request->input('view') == 'eliminar') {
          
          return view('layouts/backoffice/tienda/sistema/transporte/transporteruta/delete',[
            's_transporteruta' => $s_transporteruta,
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

        $s_transporteruta = DB::table('s_transporteruta')->whereId($s_idmarca)->first();
        
        DB::table('s_transporteruta')->whereId($s_idmarca)->update([
           'nombre' => $request->input('nombre')
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
            $s_transporteruta = DB::table('s_transporteruta')->whereId($s_idmarca)->first();
            uploadfile_eliminar($s_transporteruta->imagen,'/public/backoffice/tienda/'.$idtienda.'/sistema/');
            DB::table('s_transporteruta')
                ->where('idtienda',$idtienda)
                ->where('id',$s_idmarca)
                ->delete();
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }
    }
}
