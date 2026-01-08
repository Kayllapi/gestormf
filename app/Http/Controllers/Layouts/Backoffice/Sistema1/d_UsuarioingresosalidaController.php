<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class UsuarioingresosalidaController extends Controller
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
      
        $horario = DB::table('usuarioingresosalida')
            ->join('users as usuario','usuario.id','usuarioingresosalida.idusario')
            ->select(
                'usuarioingresosalida.*',
                'usuario.nombre as nombreusuario',
                'usuario.apellidos as apellidosusuario',
                'usuario.identificacion as identificacionusuario'
            )
            ->orderBy('usuarioingresosalida.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/usuarioingresosalida/index',[
            'tienda' => $tienda,
            'horario' => $horario
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
        return view('layouts/backoffice/tienda/sistema/usuarioingresosalida/create',[
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
      
        if($request->input('view') == 'registrar'){
           
            $users = DB::table('users')->whereId($request->input('idusario'))->first();
            
             DB::table('usuarioingresosalida')->insert([
                    'fecharegistro'          => Carbon::now(),
                    'idtienda'               => $idtienda,
                    'idusario'               => $users->id,
             ]);
           
            return response()->json([
                  'resultado'  => 'CORRECTO',
                  'mensaje'    => 'Se ha registrado correctamente.',
            ]);
        }
      
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
      
      if($id == 'show-seleccionaridentificacion'){
             $ingresosalida = DB::table('users')
                ->where('users.idtienda', $idtienda)
                ->whereId(Auth::user()->id)
                ->where('users.identificacion', $request->input('identificacion'))
                ->first();
         return [ 
             'ingresosalida' => $ingresosalida,
                 ];
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $horario = DB::table('usuarioingresosalida')
          ->join('users as usuario','usuario.id','usuarioingresosalida.idusario')
          ->where('usuarioingresosalida.id',$id)
          ->select(
                'usuarioingresosalida.*',
                'usuario.nombre as nombreusuario',
                'usuario.apellidos as apellidosusuario',
                'usuario.identificacion as identificacionusuario'
            )
          ->first();
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'detalle') {
          
          return view('layouts/backoffice/tienda/sistema/usuarioingresosalida/detalle',[
            'horario' => $horario,
            'tienda' => $tienda
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
    public function update(Request $request, $idtienda)
    {
      $request->user()->authorizeRoles($request->path(),$idtienda);
      
    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        
    }
}
