<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class UsuariosaldoController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $usuariosaldo = DB::table('s_usuariosaldo')
            ->join('users as responsable','responsable.id','s_usuariosaldo.idusuarioresponsable')
           ->join('users as usuariosaldo','usuariosaldo.id','s_usuariosaldo.idusuariosaldo')
            ->select(
                's_usuariosaldo.*',
                'responsable.nombre as responsablenombre',
                'usuariosaldo.identificacion as usuariosaldoruc',
                'usuariosaldo.nombre as usuariosaldonombre',
                'usuariosaldo.apellidos as usuariosaldoapellidos',
            )
            ->orderBy('s_usuariosaldo.id','desc')
            ->paginate(10);
      
        return view('layouts/backoffice/tienda/sistema/usuariosaldo/index',[
            'tienda'       => $tienda,
            'usuariosaldo' => $usuariosaldo
        ]);
    }
    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $tipopersonas = DB::table('tipopersona')->get();
        return view('layouts/backoffice/tienda/sistema/usuariosaldo/create',[
            'tienda' => $tienda,
            'tipopersonas' => $tipopersonas
        ]);
    }
   public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
       if($request->input('view') == 'registrar') {

            $rules = [
                'idcliente' => 'required',
                'monto'     => 'required',
                'motivo'    => 'required',
            ];
            $messages = [
                'idcliente.required' => 'El "Cliente" es Obligatorio.',
                'monto.required'     => 'El "Monto" es Obligatorio.',
                'motivo.required'    => 'El "Motivo" es Obligatorio.',

            ];
            $this->validate($request,$rules,$messages);
         
            if($request->input('monto')<=0){
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'El Monto debe ser mayor o igual a 0.'
                  ]);
              }

            // validar decimales
            $listmonto = explode('.',$request->input('monto'));
            if(count($listmonto)>1){
               if(strlen($listmonto[1])>2){
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'Solo puedes utilizar 2 decimales en el monto.'
                  ]);
               }elseif(substr($listmonto[1], 1, 1)>0){
                  return response()->json([
                      'resultado' => 'ERROR',
                      'mensaje'   => 'Los decimales del monto, debe ser redondeado.'
                  ]);
               }
            }
          
            DB::table('s_usuariosaldo')->insert([
                'fecharegistro'        => Carbon::now(),
                'fechaconfirmacion'    => Carbon::now(),
                'monto'                => $request->input('monto'),
                'motivo'               => $request->input('motivo'),
                'idusuarioresponsable' => Auth::user()->id,
                'idusuariosaldo'       => $request->input('idcliente'),
                'idestado'             => 1,
            ]);
         
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }
     public function show(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);       
    }
    public function edit(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
  
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $usuariosaldo = DB::table('s_usuariosaldo')
            ->join('users as responsable','responsable.id','s_usuariosaldo.idusuarioresponsable')
           ->join('users as usuariosaldo','usuariosaldo.id','s_usuariosaldo.idusuariosaldo')
          ->where('s_usuariosaldo.id',$id)
            ->select(
                's_usuariosaldo.*',
                'responsable.nombre as responsablenombre',
                'usuariosaldo.identificacion as usuariosaldoruc',
                'usuariosaldo.nombre as usuariosaldonombre',
                'usuariosaldo.apellidos as usuariosaldoapellidos',
            )
             ->first();
      
        if($request->input('view') == 'detalle') {
           
            return view('layouts/backoffice/tienda/sistema/usuariosaldo/detalle',[
                'usuariosaldo'    => $usuariosaldo,
                'tienda'          => $tienda
            ]);

        } else if($request->input('view') == 'anular') {
           
            return view('layouts/backoffice/tienda/sistema/usuariosaldo/anular',[
                'usuariosaldo'    => $usuariosaldo,
                'tienda'          => $tienda
            ]);

        } 
       
    }
     public function update(Request $request, $idtienda, $idusuariosaldo)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        
        if($request->input('view') == 'anular') {

            DB::table('s_usuariosaldo')->whereId($idusuariosaldo)->update([
               'fechaanulacion' => Carbon::now(),
               'idestado'       => 3,
            ]);
          
            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha anulado correctamente.'
             ]);
        } 
    }
   
    
}
