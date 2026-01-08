<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

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
        json_caja($idtienda,$request->name_modulo)   ;

        return view('layouts/backoffice/tienda/nuevosistema/caja/index',[
            'tienda' => $tienda,
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
        return view('layouts/backoffice/tienda/nuevosistema/caja/create',[
          'tienda' => $tienda
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
              's_idestado'=> 1,
           ]);
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
    }

    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        if ($id == 'show-moduloactualizar') {
              json_caja($idtienda,$request->name_modulo)   ;
        }
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
      
        $s_caja = DB::table('s_caja')->whereId($idcaja)->first();
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
    

        if($request->input('view') == 'editar') {
            return view('layouts/backoffice/tienda/nuevosistema/caja/edit',[
                'tienda' => $tienda,
                's_caja' => $s_caja,
            ]);
        }
        elseif($request->input('view') == 'detalle') {
            return view('layouts/backoffice/tienda/nuevosistema/caja/detalle',[
                'tienda' => $tienda,
                's_caja' => $s_caja,
            ]);
        }
        elseif($request->input('view') == 'eliminar') {
            return view('layouts/backoffice/tienda/nuevosistema/caja/delete',[
                'tienda' => $tienda,
                's_caja' => $s_caja,
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
              's_idestado' => 'required'
            ];
            $messages = [
              'nombre.required'=>'El "Nombre" es Obligatorio.',
              's_idestado.required' => 'El "Estado" es Obligatorio.'
            ];
            $this->validate($request,$rules,$messages);
          
            if ($request->s_idestado == 1) {
              $fechaconfirmacion = null;
            } else {
              $fechaconfirmacion = Carbon::now();
            }
          
            DB::table('s_caja')->whereId($idcaja)->update([
                'fechaconfirmacion' => $fechaconfirmacion,
                'nombre' => $request->input('nombre'),
                's_idestado' => $request->s_idestado
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
              ->where('id',$idcaja)
              ->where('idtienda',$idtienda)
              ->update([
                'fechaeliminado' => Carbon::now(),
                's_idestado' => 3
              ]);
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }
    }
}
