<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class CajaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $where = [];
        $where[] = ['s_caja.nombre','LIKE','%'.$request->input('nameBox').'%'];
       
        $box  = DB::table('s_caja') ->orderBy('s_caja.id','desc')
            ->where('s_caja.idtienda',$idtienda)
            ->where('s_caja.idestado',1)
            ->where($where)
            ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/caja/index',[
            'tienda'=> $tienda,
            'box'  => $box
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/sistema/caja/create',[
            'tienda' => $tienda,
        ]);
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
              $rules = [
                  'nombre' => 'required',
              ];
          }
          $messages = [
              'nombre.required'   => 'El "Nombre" es Obligatorio.',
          ];
          $this->validate($request,$rules,$messages);
      
          DB::table('s_caja')->insert([
              'fecharegistro' => Carbon::now(),
              'nombre'=> $request->input('nombre'),
              'idtienda'=> $idtienda,
              'idestado'=> 1,
           ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
    }

    public function show(Request $request, $idtienda)
    {
       $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $idcaja)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
      
        if($request->input('view') == 'editar') {
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $box = DB::table('s_caja')->whereId($idcaja)->first();

            return view('layouts/backoffice/tienda/sistema/caja/edit',[
                'tienda' => $tienda,
                'box' => $box,
            ]);
        }elseif($request->input('view') == 'detalle') {
            return view('layouts/backoffice/tienda/nuevosistema/caja/detalle',[
                'tienda' => $tienda,
                's_caja' => $s_caja,
            ]);
        }
      elseif($request->input('view') == 'eliminar') {
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $box = DB::table('s_caja')->whereId($idcaja)->first();
          
            return view('layouts/backoffice/tienda/sistema/caja/delete',[
                'tienda' => $tienda,
                'box' => $box,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idtienda, $idcaja)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'editar') {
            $rules = [
                'nombre'=>'required', 
            ];
            $messages = [
                'nombre.required'=>'El "Nombre" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            DB::table('s_caja')->whereId($idcaja)->update([
                'nombre'=> $request->input('nombre'),
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtienda, $idcaja)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
            DB::table('s_caja')
                ->whereId($idcaja)
                ->where('idtienda',$idtienda)
                ->update([
                    'fechaeliminado' => Carbon::now(),
                    'idestado' => 2
                ]);
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }
    }
}
