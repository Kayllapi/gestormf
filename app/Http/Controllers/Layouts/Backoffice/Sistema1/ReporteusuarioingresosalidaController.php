<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class  ReporteusuarioingresosalidaController extends Controller
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
      
        $where  = [];
      
        if($request->input('idusario')!=''){
           $where[] = ['usuario.id','LIKE','%'.$request->input('idusario').'%'];
        }
      
        if($request->input('identificacion')!=''){
           $where[] = ['s_usuarioingresosalida.identificacioningresada','LIKE','%'.$request->input('identificacion').'%'];
        }
      
        if($request->input('fechainicio')!=''){
            $where[] = ['s_usuarioingresosalida.fecharegistro','>=',$request->input('fechainicio').' 00:00:00'];
        }
      
        if($request->input('fechafin')!=''){
           $where[] = ['s_usuarioingresosalida.fecharegistro','<=',$request->input('fechafin').' 23:59:59'];
        }
      if($request->input('idestado')!=''){
            $where[] = ['s_usuarioingresosalida.idestado',$request->input('idestado')];
        }
      
        $usuarioingresosalida = DB::table('s_usuarioingresosalida')
            ->join('users as usuario','usuario.id','s_usuarioingresosalida.idusario')
            ->where($where)
            ->select(
                's_usuarioingresosalida.*',
                'usuario.nombre as usuarionombre',
                'usuario.apellidos as usuarioapellido',
                'usuario.identificacion as usuarioidentificacion'
                
            )
            ->orderBy('s_usuarioingresosalida.fecharegistro','desc')
            ->paginate(10);
      
      
        return view('layouts/backoffice/tienda/sistema/reporteusuarioingresosalida/index',[
            'tienda'                   => $tienda,
            'usuarioingresosalida'     => $usuarioingresosalida,
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
       
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtienda, $idmarca)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
    }
}
