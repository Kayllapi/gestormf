<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class CuentabancariaController extends Controller
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
        $where[] = ['s_cuentabancaria.banco','LIKE','%'.$request->input('banco').'%'];
        
        $s_cuentabancaria = DB::table('s_cuentabancaria')
            ->where('s_cuentabancaria.idtienda', $idtienda)
            ->where('s_cuentabancaria.idestado', 1)
            ->where($where)
            ->select(
                's_cuentabancaria.*'
            )
            ->orderBy('s_cuentabancaria.id','desc')
            ->where($where)
            ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/cuentabancaria/index',[
            'tienda' => $tienda,
            's_cuentabancaria' => $s_cuentabancaria
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
        return view('layouts/backoffice/tienda/sistema/cuentabancaria/create',[
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
                'idtienda' => $request->input('idtienda'),
                'idestado' => 1,
            ]);
            return response()->json([
  							'resultado' => 'CORRECTO',
  							'mensaje'   => 'Se ha registrado correctamente.'
  					]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
    public function edit(Request $request, $idtienda, $idcuentabancaria)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $s_cuentabancaria = DB::table('s_cuentabancaria')
            ->whereId($idcuentabancaria)
            ->first();
      
        if($request->input('view') == 'editar') {
          
          return view('layouts/backoffice/tienda/sistema/cuentabancaria/edit',[
            'tienda' => $tienda,
            's_cuentabancaria' => $s_cuentabancaria,
          ]);
          
        }elseif($request->input('view') == 'eliminar') {
          
          return view('layouts/backoffice/tienda/sistema/cuentabancaria/delete',[
            'tienda' => $tienda,
            's_cuentabancaria' => $s_cuentabancaria,
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
    public function update(Request $request, $idtienda, $s_idcuentabancaria)
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

            DB::table('s_cuentabancaria')->whereId($s_idcuentabancaria)->update([
                'banco' => $request->input('banco'),
                'numerocuenta' => $request->input('numerocuenta'),
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
    public function destroy(Request $request, $idtienda, $s_idcuentabancaria)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
            DB::table('s_cuentabancaria')->whereId($s_idcuentabancaria)->update([
                'fechaeliminado' => Carbon::now(),
                'idestado' => 2,
            ]);
            /*DB::table('s_cuentabancaria')
                ->where('idtienda',$idtienda)
                ->where('id',$s_idcuentabancaria)
                ->delete();*/
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }
    }
}
