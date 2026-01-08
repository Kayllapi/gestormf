<?php

namespace App\Http\Controllers\Layouts\Backoffice\NuevoSistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class AperturaycierreController extends Controller
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
      
        json_aperturaycierre($idtienda,$request->name_modulo);
        return view('layouts/backoffice/tienda/nuevosistema/aperturaycierre/index',[
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
      
        $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
        $users = DB::table('users')->where('idtienda',$idtienda)->get();
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        return view('layouts/backoffice/tienda/nuevosistema/aperturaycierre/create',[
            'tienda' => $tienda,
            's_cajas' => $s_cajas,
            'users' => $users,
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
      
        if($request->input('view') == 'registrar' && Auth::user()->idtienda!=0) {
            $rules = [
                'idcaja' => 'required',
                'montoasignar' => 'required',
                'montoasignar_dolares' => 'required',
                'idusersresponsable' => 'required',
                'idusers' => 'required',
            ];
            $messages = [
                'idcaja.required' => 'La "Caja" es Obligatorio.',
                'montoasignar.required' => 'El "Monto a asignar en Soles" es Obligatorio.',
                'montoasignar_dolares.required' => 'El "Monto a asignar en Dolares" es Obligatorio.',
                'idusersresponsable.required' => 'El "Persona responsable" es Obligatorio.',
                'idusers.required' => 'El "Persona a asignar" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            /*$efectivocaja = efectivocaja($idtienda,$request->input('idcaja'));
            if($efectivocaja['total']<$request->input('montoasignar')){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No hay Saldo Suficiente, ingrese otro monto porfavor.'
                ]);
            }*/
          
            $s_aperturacierre = DB::table('s_aperturacierre')
                ->where('s_aperturacierre.s_idestado',1)
                ->where('s_aperturacierre.idusersrecepcion',$request->input('idusers'))
                ->orWhere('s_aperturacierre.s_idestado',2)
                ->where('s_aperturacierre.idusersrecepcion',$request->input('idusers'))
                ->limit(1)
                ->first();
            if($s_aperturacierre!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La persona a asignar ya esta asignado, ingrese otro porfavor.'
                ]);
            }

            DB::table('s_aperturacierre')->insert([
                'fecharegistro' => Carbon::now(),
                'montoasignar' => $request->input('montoasignar'),
                'montoasignar_dolares' => $request->input('montoasignar_dolares'),
                'montocierre' => '0.00',
                'montocierre_dolares' => '0.00',
                'idusersresponsable' => $request->input('idusersresponsable'),
                'idusersrecepcion' => $request->input('idusers'),
                's_idcaja' => $request->input('idcaja'),
                's_idestado' => 2,
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
    public function show(Request $request, $idtienda, $s_idcaja, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
       if($request->input('view') == 'saldoanterior') {
            $efectivocaja = efectivocaja($idtienda,$s_idcaja);
            return [
                'saldoactual' => $efectivocaja['total']
            ];
       }
      if($id=='show-actualizar'){
            json_aperturaycierre($idtienda,$request->name_modulo);

      }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $idtienda, $s_idaperturacierre)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $s_aperturacierre = DB::table('s_aperturacierre')
            ->join('users as usersresponsable','usersresponsable.id','s_aperturacierre.idusersresponsable')
            ->join('users as usersrecepcion','usersrecepcion.id','s_aperturacierre.idusersrecepcion')
            ->join('s_caja','s_caja.id','s_aperturacierre.s_idcaja')
            ->where('s_aperturacierre.id',$s_idaperturacierre)
            ->select(
                's_aperturacierre.*',
                'usersresponsable.nombre as usersresponsablenombre',
                'usersresponsable.apellidos as usersresponsableapellidos',
                'usersrecepcion.nombre as usersrecepcionnombre',
                'usersrecepcion.apellidos as usersrecepcionapellidos',
                's_caja.nombre as cajanombre'
            )
            ->first();

        if($request->input('view') == 'editar') {
              
            $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            return view('layouts/backoffice/tienda/nuevosistema/aperturaycierre/edit',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                'users' => $users,
                's_aperturacierre' => $s_aperturacierre,
            ]);
          
        }elseif($request->input('view') == 'confirmarenvio') {
              
//             $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->first();
//             $users = DB::table('users')->where('idtienda',$idtienda)->first();
//             $tienda = DB::table('tienda')->whereId($idtienda)->first();
//             return view('layouts/backoffice/tienda/nuevosistema/aperturaycierre/confirmarenvio',[
//                 'tienda' => $tienda,
//                 's_cajas' => $s_cajas,
//                 'users' => $users,
//                 's_aperturacierre' => $s_aperturacierre,
//             ]);
           $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            return view('layouts/backoffice/tienda/nuevosistema/aperturaycierre/confirmarenvio',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                'users' => $users,
                's_aperturacierre' => $s_aperturacierre,
            ]);
          
        }elseif($request->input('view') == 'anularenvio') {
              
            $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            return view('layouts/backoffice/tienda/nuevosistema/aperturaycierre/anularenvio',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                'users' => $users,
                's_aperturacierre' => $s_aperturacierre,
            ]);
        }elseif($request->input('view') == 'confirmarrecepcion') {
              
            $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            return view('layouts/backoffice/tienda/nuevosistema/aperturaycierre/confirmarrecepcion',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                'users' => $users,
                's_aperturacierre' => $s_aperturacierre,
            ]);
          
        }elseif($request->input('view') == 'confirmarcierre') {
            $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $caja = caja($idtienda,Auth::user()->id);
            $fectivo = efectivo($idtienda,$caja['apertura']->id);
            return view('layouts/backoffice/tienda/nuevosistema/aperturaycierre/confirmarcierre',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                'users' => $users,
                's_aperturacierre' => $s_aperturacierre,
                'fectivo' => $fectivo,
            ]);
        }elseif($request->input('view') == 'confirmarrecepcioncierre') {
            $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            return view('layouts/backoffice/tienda/nuevosistema/aperturaycierre/confirmarrecepcioncierre',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                'users' => $users,
                's_aperturacierre' => $s_aperturacierre,
            ]);
          
        }elseif($request->input('view') == 'anularenviocierre') {
            $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            return view('layouts/backoffice/tienda/nuevosistema/aperturaycierre/anularenviocierre',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                'users' => $users,
                's_aperturacierre' => $s_aperturacierre,
            ]);
        }elseif($request->input('view') == 'detalle') {
            $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            return view('layouts/backoffice/tienda/nuevosistema/aperturaycierre/detalle',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                'users' => $users,
                's_aperturacierre' => $s_aperturacierre,
            ]);
          
        }elseif($request->input('view') == 'detallecierre') {
              
            $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            return view('layouts/backoffice/tienda/nuevosistema/aperturaycierre/detallecierre',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                'users' => $users,
                's_aperturacierre' => $s_aperturacierre,
            ]);
          
        }elseif($request->input('view') == 'detalleapertura') {
              
            $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            return view('layouts/backoffice/tienda/nuevosistema/aperturaycierre/detalleapertura',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                'users' => $users,
                's_aperturacierre' => $s_aperturacierre,
            ]);
          
        }elseif($request->input('view') == 'detallediario') {
            $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            $caja = caja($idtienda,Auth::user()->id);
             $fectivo = efectivo($idtienda,$caja['apertura']->id);
            return view('layouts/backoffice/tienda/nuevosistema/aperturaycierre/detallediario',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                'users' => $users,
                's_aperturacierre' => $s_aperturacierre,
                 'fectivo' => $fectivo,
            ]);
        }elseif($request->input('view') == 'eliminar') {
            $tienda = DB::table('tienda')->whereId($idtienda)->first();
            return view('layouts/backoffice/tienda/nuevosistema/aperturaycierre/delete',[
                'tienda' => $tienda,
                's_aperturacierre' => $s_aperturacierre,
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
    public function update(Request $request, $idtienda, $s_idaperturacierre)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'editar') {
            $rules = [
                'idcaja' => 'required',
                'montoasignar' => 'required',
                'idusers' => 'required',
            ];
            $messages = [
                'idcaja.required' => 'La "Caja" es Obligatorio.',
                'montoasignar.required' => 'El "Monto a asignar" es Obligatorio.',
                'idusers.required' => 'El "Persona a asignar" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            $efectivocaja = efectivocaja($idtienda,$request->input('idcaja'));
            if($efectivocaja['total']<$request->input('montoasignar')){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'No hay Saldo Suficiente, ingrese otro monto porfavor.'
                ]);
            }

            DB::table('s_aperturacierre')->whereId($s_idaperturacierre)->update([
                'montoasignar' => $request->input('montoasignar'),
                'idusersresponsable' => Auth::user()->id,
                'idusersrecepcion' => $request->input('idusers'),
                's_idcaja' => $request->input('idcaja'),
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }elseif($request->input('view') == 'confirmarenvio') {

            DB::table('s_aperturacierre')->whereId($s_idaperturacierre)->update([
                's_idestado' => 2
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'se ha cofirmado correctamente.'
            ]);
        }elseif($request->input('view') == 'anularenvio') {

            DB::table('s_aperturacierre')->whereId($s_idaperturacierre)->update([
                'montoasignar' => '0.00',
                's_idestado' => 1
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha rechazado correctamente.'
            ]);
        }elseif($request->input('view') == 'confirmarrecepcion') {

            DB::table('s_aperturacierre')->whereId($s_idaperturacierre)->update([
                'fechaconfirmacion' => Carbon::now()
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha cofirmado correctamente.'
            ]);
        }elseif($request->input('view') == 'confirmarcierre') {

            $caja = caja($idtienda,Auth::user()->id);
            $fectivo = efectivo($idtienda,$caja['apertura']->id);
            DB::table('s_aperturacierre')->whereId($s_idaperturacierre)->update([
                'fechacierre' => Carbon::now(),
                'montocierre' => $fectivo['total'],
                's_idestado' => 3
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha cofirmado correctamente.'
            ]);
        }elseif($request->input('view') == 'confirmarrecepcioncierre') {

            DB::table('s_aperturacierre')->whereId($s_idaperturacierre)->update([
                'fechacierreconfirmacion' => Carbon::now()
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha cofirmado correctamente.'
            ]);
        }elseif($request->input('view') == 'anularenviocierre') {

            DB::table('s_aperturacierre')->whereId($s_idaperturacierre)->update([
                'montocierre' => '0.00',
                'montocierre_dolares' => '0.00',
                's_idestado' => 2
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha rechazado correctamente.'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $idtienda, $s_idaperturacierre)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
            DB::table('s_aperturacierre')
                ->join('s_caja','s_caja.id','s_aperturacierre.s_idcaja')
                ->where('s_caja.idtienda',$idtienda)
                ->where('s_aperturacierre.id',$s_idaperturacierre)
                ->delete();
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }
    }
}
