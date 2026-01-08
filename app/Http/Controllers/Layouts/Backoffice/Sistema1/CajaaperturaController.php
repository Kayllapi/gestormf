<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class CajaaperturaController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        $where = [];
        $where1 = [];
        if(Auth::user()->idtienda!=0){
            $where[] = ['s_caja.nombre','LIKE','%'.$request->input('cajanombre').'%'];
            $where[] = ['usersresponsable.nombre','LIKE','%'.$request->input('usersresponsable').'%'];
            $where[] = ['usersrecepcion.nombre','LIKE','%'.$request->input('usersrecepcion').'%'];
            $where[] = ['s_aperturacierre.idusersresponsable',Auth::user()->id];
            $where1[] = ['s_caja.nombre','LIKE','%'.$request->input('cajanombre').'%'];
            $where1[] = ['usersresponsable.nombre','LIKE','%'.$request->input('usersresponsable').'%'];
            $where1[] = ['usersrecepcion.nombre','LIKE','%'.$request->input('usersrecepcion').'%'];
            $where1[] = ['s_aperturacierre.idusersrecepcion',Auth::user()->id];
        }else{
            $where[] = ['s_caja.nombre','LIKE','%'.$request->input('cajanombre').'%'];
            $where[] = ['usersresponsable.nombre','LIKE','%'.$request->input('usersresponsable').'%'];
            $where[] = ['usersrecepcion.nombre','LIKE','%'.$request->input('usersrecepcion').'%'];
        }
        $s_aperturacierres = DB::table('s_aperturacierre')
            ->join('users as usersresponsable','usersresponsable.id','s_aperturacierre.idusersresponsable')
            ->join('users as usersrecepcion','usersrecepcion.id','s_aperturacierre.idusersrecepcion')
            ->join('s_caja','s_caja.id','s_aperturacierre.s_idcaja')
            ->where($where)
            ->where('s_caja.idtienda',$idtienda)
            ->orWhere($where1)
            ->where('s_caja.idtienda',$idtienda)
            ->select(
                's_aperturacierre.*',
                'usersresponsable.nombre as usersresponsablenombre',
                'usersresponsable.apellidos as usersresponsableapellidos',
                'usersrecepcion.nombre as usersrecepcionnombre',
                'usersrecepcion.apellidos as usersrecepcionapellidos',
                's_caja.nombre as cajanombre'
            )
            ->orderBy('s_aperturacierre.id','desc')
            ->paginate(10);
      
        $moneda_soles = DB::table('s_moneda')->whereId(1)->first();
        $moneda_dolares = DB::table('s_moneda')->whereId(2)->first();
      
        //revisar apertura
        /*$usersrolesmodulo = DB::table('usersrolesmodulo')
                ->join('modulo','modulo.id','usersrolesmodulo.idmodulo')
                ->where('usersrolesmodulo.idtienda',$idtienda)
                ->where('usersrolesmodulo.idusers',Auth::user()->id)
                ->where('modulo.idestado',1)
                ->where('modulo.opcion','apertura')
                ->first();*/
      
        // aperturacaja
        $caja = caja($idtienda,Auth::user()->id);

        return view('layouts/backoffice/tienda/sistema/cajaapertura/index',[
            'tienda' => $tienda,
            's_aperturacierres' => $s_aperturacierres,
            'moneda_soles' => $moneda_soles,
            'moneda_dolares' => $moneda_dolares,
            'caja' => $caja,
        ]);
    }

    public function create(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'caja') {
            $s_cajas = DB::table('s_caja')->where('idestado',1)->where('idtienda',$idtienda)->get();
            return view('layouts/backoffice/tienda/sistema/cajaapertura/create',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
            ]);
        }
        elseif($request->input('view') == 'cajaauxiliar') {
            $caja = caja($idtienda,Auth::user()->id);
            
            $idaperturacierre = 0;
            if($caja['resultado']=='ABIERTO'){
                $idaperturacierre = $caja['apertura']->id;
            }
          
            $s_aperturacierre = DB::table('s_aperturacierre')
                ->join('s_caja','s_caja.id','s_aperturacierre.s_idcaja')
                ->where('s_aperturacierre.id',$idaperturacierre)
                ->select(
                    's_aperturacierre.*',
                    's_caja.nombre as cajanombre'
                )
                ->first();
            return view('layouts/backoffice/tienda/sistema/cajaapertura/cajaauxiliar',[
                'tienda' => $tienda,
                's_aperturacierre' => $s_aperturacierre,
            ]);
        }
            
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'registrar') {
          
            $rules = [
                'idcaja' => 'required',
                'idusers' => 'required',
            ];
            $montoasignar=0;
            $montoasignar_dolares=0;
            $sistema_moneda_usar=0;
            if(configuracion($idtienda,'sistema_moneda_usar')['valor']==1){
                $rules = array_merge($rules,[
                    'montoasignar' => 'required',
                ]);
                $montoasignar=$request->montoasignar;
                $sistema_moneda_usar=1;
            }elseif(configuracion($idtienda,'sistema_moneda_usar')['valor']==2){
                $rules = array_merge($rules,[
                    'montoasignar_dolares' => 'required',
                ]);
                $montoasignar_dolares=$request->montoasignar_dolares;
                $sistema_moneda_usar=2;
            }elseif(configuracion($idtienda,'sistema_moneda_usar')['valor']==3){
                $rules = array_merge($rules,[
                    'montoasignar' => 'required',
                    'montoasignar_dolares' => 'required',
                ]);
                $montoasignar=$request->montoasignar;
                $montoasignar_dolares=$request->montoasignar_dolares;
                $sistema_moneda_usar=3;
            }else{
                $rules = array_merge($rules,[
                    'montoasignar' => 'required',
                ]);
                $montoasignar=$request->montoasignar;
                $sistema_moneda_usar=1;
            }
            $messages = [
                'idcaja.required' => 'La "Caja" es Obligatorio.',
                'montoasignar.required' => 'El "Monto a asignar en Soles" es Obligatorio.',
                'montoasignar_dolares.required' => 'El "Monto a asignar en Dolares" es Obligatorio.',
                'idusers.required' => 'El "Persona a asignar" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            if(configuracion($idtienda,'prestamo_tipocierrecaja')['valor']==2 or configuracion($idtienda,'prestamo_tipocierrecaja')['valor']==3){
                $efectivocaja_soles = efectivocaja($idtienda,$request->input('idcaja'),1);
                $efectivocaja_dolares = efectivocaja($idtienda,$request->input('idcaja'),2);
                if(configuracion($idtienda,'sistema_moneda_usar')['valor']==1){
                    if($efectivocaja_soles['total']<$request->input('montoasignar')){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay Saldo en Soles Suficiente, ingrese otro monto porfavor.'
                        ]);
                    }
                }elseif(configuracion($idtienda,'sistema_moneda_usar')['valor']==2){
                    if($efectivocaja_dolares['total']<$request->input('montoasignar_dolares')){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay Saldo en Dolares Suficiente, ingrese otro monto porfavor.'
                        ]);
                    }
                }elseif(configuracion($idtienda,'sistema_moneda_usar')['valor']==3){
                    if($efectivocaja_soles['total']<$request->input('montoasignar')){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay Saldo en Soles Suficiente, ingrese otro monto porfavor.'
                        ]);
                    }
                    if($efectivocaja_dolares['total']<$request->input('montoasignar_dolares')){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay Saldo en Dolares Suficiente, ingrese otro monto porfavor.'
                        ]);
                    }
                }else{
                    if($efectivocaja_soles['total']<$request->input('montoasignar')){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay Saldo en Soles Suficiente, ingrese otro monto porfavor.'
                        ]);
                    }
                }
            }
          
            $s_aperturacierre = DB::table('s_aperturacierre')
                ->where('s_aperturacierre.idestadoaperturacierre',1)
                ->where('s_aperturacierre.idusersrecepcion',$request->input('idusers'))
                ->orWhere('s_aperturacierre.idestadoaperturacierre',2)
                ->where('s_aperturacierre.idusersrecepcion',$request->input('idusers'))
                ->limit(1)
                ->first();
            if($s_aperturacierre!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La persona a asignar ya esta asignado, ingrese otro porfavor.'
                ]);
            }
          

            $s_idaperturacierre = DB::table('s_aperturacierre')->insertGetId([
                'fecharegistro' => Carbon::now(),
                'montoasignar' => $montoasignar,
                'montoasignar_dolares' => $montoasignar_dolares,
                'montocierre' => '0.00',
                'montocierre_dolares' => '0.00',
                'montocierre_recibido' => '0.00',
                'montocierre_recibido_dolares' => '0.00',
                'montocobradoauxiliar' => '0.00',
                'montocobradoauxiliar_dolares' => '0.00',
                'config_sistema_moneda_usar' => $sistema_moneda_usar,
                'config_sistema_monedapordefecto' => configuracion($idtienda,'sistema_monedapordefecto')['valor'],
                'config_prestamo_tipocierrecaja' => configuracion($idtienda,'prestamo_tipocierrecaja')['valor'],
                'idusersresponsable' => Auth::user()->id,
                'idusersrecepcion' => $request->input('idusers'),
                'idtipocaja' => 1, // 1=norma, 2=axiliar
                'idaperturacierre' => 0,
                'idtienda' => $idtienda,
                'idestadoaperturacierre' => 2,
                's_idcaja' => $request->input('idcaja'),
                'idestado' => 1,
            ]);
          
            
            if(Auth::user()->id==$request->input('idusers')){
                DB::table('s_aperturacierre')->whereId($s_idaperturacierre)->update([
                    'fechaconfirmacion' => Carbon::now()
                ]);
            }

            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        if($request->input('view') == 'registrar_auxiliar') {
          
            // revisar apertura
            $caja = caja($idtienda,Auth::user()->id);
            $idaperturacierre = 0;
            if($caja['resultado']=='ABIERTO'){
                $s_idaperturacierre = $caja['apertura']->id;
            }
          
            $s_aperturacierre = DB::table('s_aperturacierre')
                ->whereId($s_idaperturacierre)
                ->first();
          
            $rules = [];
            $montoasignar=0;
            $montoasignar_dolares=0;
            if($s_aperturacierre->config_sistema_moneda_usar==1){
                $rules = array_merge($rules,[
                    'montoasignar' => 'required',
                ]);
                $montoasignar=$request->montoasignar;
            }elseif($s_aperturacierre->config_sistema_moneda_usar==2){
                $rules = array_merge($rules,[
                    'montoasignar_dolares' => 'required',
                ]);
                $montoasignar_dolares=$request->montoasignar_dolares;
            }elseif($s_aperturacierre->config_sistema_moneda_usar==3){
                $rules = array_merge($rules,[
                    'montoasignar' => 'required',
                    'montoasignar_dolares' => 'required',
                ]);
                $montoasignar=$request->montoasignar;
                $montoasignar_dolares=$request->montoasignar_dolares;
            }else{
                $rules = array_merge($rules,[
                    'montoasignar' => 'required',
                ]);
                $montoasignar=$request->montoasignar;
            }
            $rules = [
                'idusers' => 'required',
            ];
            $messages = [
                'montoasignar.required' => 'El "Monto a asignar en Soles" es Obligatorio.',
                'montoasignar_dolares.required' => 'El "Monto a asignar en Dolares" es Obligatorio.',
                'idusers.required' => 'El "Persona a asignar" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

        
                $efectivocaja_soles = efectivo($idtienda,$s_idaperturacierre,1);
                $efectivocaja_dolares = efectivo($idtienda,$s_idaperturacierre,2);
          
                if($s_aperturacierre->config_sistema_moneda_usar==1){
                    if($efectivocaja_soles['total']<$request->input('montoasignar')){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay Saldo en Soles Suficiente, ingrese otro monto porfavor.'
                        ]);
                    }
                }elseif($s_aperturacierre->config_sistema_moneda_usar==2){
                    if($efectivocaja_dolares['total']<$request->input('montoasignar_dolares')){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay Saldo en Dolares Suficiente, ingrese otro monto porfavor.'
                        ]);
                    }
                }elseif($s_aperturacierre->config_sistema_moneda_usar==3){
                    if($efectivocaja_soles['total']<$request->input('montoasignar')){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay Saldo en Soles Suficiente, ingrese otro monto porfavor.'
                        ]);
                    }
                    if($efectivocaja_dolares['total']<$request->input('montoasignar_dolares')){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay Saldo en Dolares Suficiente, ingrese otro monto porfavor.'
                        ]);
                    }
                }else{
                    if($efectivocaja_soles['total']<$request->input('montoasignar')){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay Saldo en Soles Suficiente, ingrese otro monto porfavor.'
                        ]);
                    }
                }
          
          
            $verificar_aperturacierre = DB::table('s_aperturacierre')
                ->where('s_aperturacierre.idestadoaperturacierre',1)
                ->where('s_aperturacierre.idusersrecepcion',$request->input('idusers'))
                ->orWhere('s_aperturacierre.idestadoaperturacierre',2)
                ->where('s_aperturacierre.idusersrecepcion',$request->input('idusers'))
                ->limit(1)
                ->first();
            if($verificar_aperturacierre!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'La persona a asignar ya esta asignado, ingrese otro porfavor.'
                ]);
            }

            DB::table('s_aperturacierre')->insert([
                'fecharegistro' => Carbon::now(),
                'montoasignar' => $montoasignar,
                'montoasignar_dolares' => $montoasignar_dolares,
                'montocierre' => '0.00',
                'montocierre_dolares' => '0.00',
                'montocierre_recibido' => '0.00',
                'montocierre_recibido_dolares' => '0.00',
                'montocobradoauxiliar' => '0.00',
                'montocobradoauxiliar_dolares' => '0.00',
                'config_sistema_moneda_usar' => $s_aperturacierre->config_sistema_moneda_usar,
                'config_sistema_monedapordefecto' => $s_aperturacierre->config_sistema_monedapordefecto,
                'config_prestamo_tipocierrecaja' => $s_aperturacierre->config_prestamo_tipocierrecaja,
                'idusersresponsable' => Auth::user()->id,
                'idusersrecepcion' => $request->input('idusers'),
                'idtipocaja' => 2, // 1=norma, 2=axiliar
                'idaperturacierre' => $s_aperturacierre->id,
                'idtienda' => $idtienda,
                'idestadoaperturacierre' => 2,
                's_idcaja' => $s_aperturacierre->s_idcaja,
                'idestado' => 1,
            ]);

            return response()->json([
              'resultado' => 'CORRECTO',
              'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $idtienda, $s_idcaja)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
       if($request->input('view') == 'saldoanterior') {
            $efectivocajasoles = efectivocaja($idtienda,$s_idcaja,1);
            $efectivocajadolares = efectivocaja($idtienda,$s_idcaja,2);
            return [
                'saldoactual_soles' => $efectivocajasoles['total'],
                'saldoactual_dolares' => $efectivocajadolares['total']
            ];
       }
    }

    public function edit(Request $request, $idtienda, $s_idaperturacierre)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
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
            return view('layouts/backoffice/tienda/sistema/cajaapertura/edit',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                's_aperturacierre' => $s_aperturacierre,
            ]);
          
        }
        elseif($request->input('view') == 'confirmarrecepcion') {
              
            $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();
            return view('layouts/backoffice/tienda/sistema/cajaapertura/confirmarrecepcion',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                'users' => $users,
                's_aperturacierre' => $s_aperturacierre,
            ]);
          
        }elseif($request->input('view') == 'anularapertura') {
              
            $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();
            return view('layouts/backoffice/tienda/sistema/cajaapertura/anularapertura',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                'users' => $users,
                's_aperturacierre' => $s_aperturacierre,
            ]);
          
        }elseif($request->input('view') == 'detalleapertura') {
              
            $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();
            return view('layouts/backoffice/tienda/sistema/cajaapertura/detalleapertura',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                'users' => $users,
                's_aperturacierre' => $s_aperturacierre,
            ]);
          
        }elseif($request->input('view') == 'detallecierre') {
              
            $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();
            return view('layouts/backoffice/tienda/sistema/cajaapertura/detallecierre',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                'users' => $users,
                's_aperturacierre' => $s_aperturacierre,
            ]);
          
        }
        elseif($request->input('view') == 'detallediario') {
            return view('layouts/backoffice/tienda/sistema/cajaapertura/detallediario',[
                'tienda' => $tienda,
                's_aperturacierre' => $s_aperturacierre,
            ]);
        }
        elseif($request->input('view') == 'eliminar') {
            $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();
            return view('layouts/backoffice/tienda/sistema/cajaapertura/delete',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                'users' => $users,
                's_aperturacierre' => $s_aperturacierre,
            ]);
        }
      
        // cierre
        elseif($request->input('view') == 'confirmarcierre') {
            $verificar_aperturacierre = DB::table('s_aperturacierre')
                ->where('s_aperturacierre.idestadoaperturacierre',1)
                ->where('s_aperturacierre.idusersresponsable',Auth::user()->id)
                ->where('s_aperturacierre.id','<>',$s_idaperturacierre)
                ->where('s_aperturacierre.idaperturacierre','<>',0)
                ->orWhere('s_aperturacierre.idestadoaperturacierre',2)
                ->where('s_aperturacierre.idusersresponsable',Auth::user()->id)
                ->where('s_aperturacierre.id','<>',$s_idaperturacierre)
                ->where('s_aperturacierre.idaperturacierre','<>',0)
                ->orWhere('s_aperturacierre.idestadoaperturacierre',3)
                ->where('s_aperturacierre.idusersresponsable',Auth::user()->id)
                ->where('s_aperturacierre.id','<>',$s_idaperturacierre)
                ->where('s_aperturacierre.idaperturacierre','<>',0)
                ->whereNull('s_aperturacierre.fechacierreconfirmacion')
                ->limit(1)
                ->first();
         
            return view('layouts/backoffice/tienda/sistema/cajaapertura/confirmarcierre',[
                'tienda' => $tienda,
                's_aperturacierre' => $s_aperturacierre,
                'verificar_aperturacierre' => $verificar_aperturacierre,
            ]);
        }
        elseif($request->input('view') == 'confirmarrecepcioncierre') {
            $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();
            return view('layouts/backoffice/tienda/sistema/cajaapertura/confirmarrecepcioncierre',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                'users' => $users,
                's_aperturacierre' => $s_aperturacierre,
            ]);
          
        }
        elseif($request->input('view') == 'anularenviocierre') {
            $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();
            return view('layouts/backoffice/tienda/sistema/cajaapertura/anularenviocierre',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                'users' => $users,
                's_aperturacierre' => $s_aperturacierre,
            ]);
        }
        elseif($request->input('view') == 'detallecierre') {
            $s_cajas = DB::table('s_caja')->where('idtienda',$idtienda)->get();
            $users = DB::table('users')->where('idtienda',$idtienda)->get();
            return view('layouts/backoffice/tienda/sistema/cajaapertura/detallecierre',[
                'tienda' => $tienda,
                's_cajas' => $s_cajas,
                'users' => $users,
                's_aperturacierre' => $s_aperturacierre,
            ]);
        }
    }

    public function update(Request $request, $idtienda, $s_idaperturacierre)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'editar') {
          
            $s_aperturacierre = DB::table('s_aperturacierre')
                ->whereId($s_idaperturacierre)
                ->first();
          
            $rules = [];
            $montoasignar=0;
            $montoasignar_dolares=0;
            if(configuracion($idtienda,'sistema_moneda_usar')['valor']==1){
                $rules = array_merge($rules,[
                    'montoasignar' => 'required',
                ]);
                $montoasignar=$request->montoasignar;
            }elseif(configuracion($idtienda,'sistema_moneda_usar')['valor']==2){
                $rules = array_merge($rules,[
                    'montoasignar_dolares' => 'required',
                ]);
                $montoasignar_dolares=$request->montoasignar_dolares;
            }elseif(configuracion($idtienda,'sistema_moneda_usar')['valor']==3){
                $rules = array_merge($rules,[
                    'montoasignar' => 'required',
                    'montoasignar_dolares' => 'required',
                ]);
                $montoasignar=$request->montoasignar;
                $montoasignar_dolares=$request->montoasignar_dolares;
            }else{
                $rules = array_merge($rules,[
                    'montoasignar' => 'required',
                ]);
                $montoasignar=$request->montoasignar;
            }
            $messages = [
                'montoasignar.required' => 'El "Monto a asignar en Soles" es Obligatorio.',
                'montoasignar_dolares.required' => 'El "Monto a asignar en Dolares" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            if(configuracion($idtienda,'prestamo_tipocierrecaja')['valor']==2 or configuracion($idtienda,'prestamo_tipocierrecaja')['valor']==3){
                $efectivocaja_soles = efectivocaja($idtienda,$s_aperturacierre->s_idcaja,1);
                $efectivocaja_dolares = efectivocaja($idtienda,$s_aperturacierre->s_idcaja,2);
                if(configuracion($idtienda,'sistema_moneda_usar')['valor']==1){
                    if($efectivocaja_soles['total']<$request->input('montoasignar')){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay Saldo en Soles Suficiente, ingrese otro monto porfavor.'
                        ]);
                    }
                }elseif(configuracion($idtienda,'sistema_moneda_usar')['valor']==2){
                    if($efectivocaja_dolares['total']<$request->input('montoasignar_dolares')){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay Saldo en Dolares Suficiente, ingrese otro monto porfavor.'
                        ]);
                    }
                }elseif(configuracion($idtienda,'sistema_moneda_usar')['valor']==3){
                    if($efectivocaja_soles['total']<$request->input('montoasignar')){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay Saldo en Soles Suficiente, ingrese otro monto porfavor.'
                        ]);
                    }
                    if($efectivocaja_dolares['total']<$request->input('montoasignar_dolares')){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay Saldo en Dolares Suficiente, ingrese otro monto porfavor.'
                        ]);
                    }
                }else{
                    if($efectivocaja_soles['total']<$request->input('montoasignar')){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'No hay Saldo en Soles Suficiente, ingrese otro monto porfavor.'
                        ]);
                    }
                }
            }

            DB::table('s_aperturacierre')->whereId($s_idaperturacierre)->update([
                'fechaconfirmarenvio' => Carbon::now(),
                'montoasignar' => $montoasignar,
                'montoasignar_dolares' => $montoasignar_dolares,
                'config_sistema_moneda_usar' => configuracion($idtienda,'sistema_moneda_usar')['valor'],
                'config_prestamo_tipocierrecaja' => configuracion($idtienda,'prestamo_tipocierrecaja')['valor'],
                'idusersresponsable' => Auth::user()->id,
                'idestadoaperturacierre' => 2,
                's_idcaja' => $s_aperturacierre->s_idcaja,
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'anularenvio') {

            DB::table('s_aperturacierre')->whereId($s_idaperturacierre)->update([
                'fechaanularenvio' => Carbon::now(),
                'idestadoaperturacierre' => 1
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha rechazado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'anularapertura') {
          
            DB::table('s_aperturacierre')->whereId($s_idaperturacierre)->update([
                'fechaanularenvio' => Carbon::now(),
                'idestadoaperturacierre' => 1
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha cofirmado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'confirmarrecepcion') {
          
            $s_aperturacierre = DB::table('s_aperturacierre')
                ->whereId($s_idaperturacierre)
                ->first();
            
            $montocobradoauxiliar = 0;
            $montocobradoauxiliar_dolares = 0;
            if($s_aperturacierre->config_prestamo_tipocierrecaja==3){
            if($s_aperturacierre->idtipocaja==2){ // caja auxiliar
                if($s_aperturacierre->config_sistema_moneda_usar==1){
                    $rules = [
                        'montocobradoauxiliar' => 'required',
                    ];
                    $messages = [
                        'montocobradoauxiliar.required' => 'El "Monto total cobrado en Soles" es Obligatorio.',
                    ];
                  
                  
                    if($request->montocobradoauxiliar<=0){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El "Monto total cobrado en Soles" debe ser mayor a 0.00.'
                        ]);
                    }
                    $montocobradoauxiliar = $request->montocobradoauxiliar;
                }
                elseif($s_aperturacierre->config_sistema_moneda_usar==2){
                    $rules = [
                        'montocobradoauxiliar_dolares' => 'required',
                    ];
                    $messages = [
                        'montocobradoauxiliar_dolares.required' => 'El "Monto total cobrado en Dolares" es Obligatorio.',
                    ];
                    if($request->montocobradoauxiliar_dolares<=0){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El "Monto total cobrado en Dolares" debe ser mayor a 0.00.'
                        ]);
                    }
                    $montocobradoauxiliar_dolares = $request->montocobradoauxiliar_dolares;
                }
                elseif($s_aperturacierre->config_sistema_moneda_usar==3){
                    $rules = [
                        'montocobradoauxiliar' => 'required',
                        'montocobradoauxiliar_dolares' => 'required',
                    ];
                    $messages = [
                        'montocobradoauxiliar.required' => 'El "Monto total cobrado en Soles" es Obligatorio.',
                        'montocobradoauxiliar_dolares.required' => 'El "Monto total cobrado en Dolares" es Obligatorio.',
                    ];                  
                    if($request->montocobradoauxiliar<=0){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El "Monto total cobrado en Soles" debe ser mayor a 0.00.'
                        ]);
                    }
                    if($request->montocobradoauxiliar_dolares<=0){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El "Monto total cobrado en Dolares" debe ser mayor a 0.00.'
                        ]);
                    }
                    $montocobradoauxiliar = $request->montocobradoauxiliar;
                    $montocobradoauxiliar_dolares = $request->montocobradoauxiliar_dolares;
                }
                else{
                    $rules = [
                        'montocobradoauxiliar' => 'required',
                    ];
                    $messages = [
                        'montocobradoauxiliar.required' => 'El "Monto total cobrado en Soles" es Obligatorio.',
                    ];                  
                    if($request->montocobradoauxiliar<=0){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'El "Monto total cobrado en Soles" debe ser mayor a 0.00.'
                        ]);
                    }
                    $montocobradoauxiliar = $request->montocobradoauxiliar;
                }
                    
                $this->validate($request,$rules,$messages);
            }
            }
          
            DB::table('s_aperturacierre')->whereId($s_idaperturacierre)->update([
                'fechaconfirmacion' => Carbon::now(),
                'montocobradoauxiliar' => $montocobradoauxiliar,
                'montocobradoauxiliar_dolares' => $montocobradoauxiliar_dolares,
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha cofirmado correctamente.'
            ]);
        }
        //cierre
        elseif($request->input('view') == 'confirmarcierre') {
            $aperturacierre = DB::table('s_aperturacierre')
                ->whereId($s_idaperturacierre)
                ->first();
            $fectivosoles = efectivo($idtienda,$s_idaperturacierre,1);
            $fectivodolares = efectivo($idtienda,$s_idaperturacierre,2);
            $montocierre_recibido = $fectivosoles['total'];
            $montocierre_recibido_dolares = $fectivodolares['total'];
            if($aperturacierre->config_prestamo_tipocierrecaja==1){
            }elseif($aperturacierre->config_prestamo_tipocierrecaja==3){
                if($aperturacierre->config_sistema_moneda_usar==1){
                $montocierre_recibido = $request->totalsoles;
                $montocierre_recibido_dolares = 0;
                }elseif($aperturacierre->config_sistema_moneda_usar==2){
                $montocierre_recibido = 0;
                $montocierre_recibido_dolares = $request->totaldolares;
                }elseif($aperturacierre->config_sistema_moneda_usar==3){
                $montocierre_recibido = $request->totalsoles;
                $montocierre_recibido_dolares = $request->totaldolares;
                }
            }
          
            if($tienda->idcategoria==30){
                $cant_ordenpedidos = DB::table('s_comida_ordenpedido')
                                ->where('s_comida_ordenpedido.idtienda', $idtienda)
                                ->where('s_comida_ordenpedido.idestado', 1)
                                ->where('s_comida_ordenpedido.idestadoordenpedido', 1)
                                ->count();
          
                if($cant_ordenpedidos>0){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'No puede cerrar la caja, por que hay Ordenes de Pedidos Pendientes.'
                    ]);
                }
            }

            if($aperturacierre->montocobradoauxiliar>0){
            if($montocierre_recibido!=$aperturacierre->montocobradoauxiliar){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Total Efectivo S/" debe ser igual al "Monto total cobrado en Soles".'
                    ]);
            }
            }
            
            if($aperturacierre->montocobradoauxiliar_dolares>0){
            if($montocierre_recibido_dolares!=$aperturacierre->montocobradoauxiliar_dolares){
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El "Total Efectivo $" debe ser igual al "Monto total cobrado en Dolares".'
                    ]);
            }
            }

            $fecharegistro = Carbon::now();
          
            DB::table('s_aperturacierre')->whereId($s_idaperturacierre)->update([
                'fechacierre' => $fecharegistro,
                'montocierre' => $fectivosoles['total'],
                'montocierre_dolares' => $fectivodolares['total'],
                'montocierre_recibido' => $montocierre_recibido,
                'montocierre_recibido_dolares' => $montocierre_recibido_dolares,
                'config_prestamo_tipocierrecaja' => configuracion($idtienda,'prestamo_tipocierrecaja')['valor'],
                'idestadoaperturacierre' => 3
            ]);
          
            $s_aperturacierre = DB::table('s_aperturacierre')
                ->where('s_aperturacierre.id',$s_idaperturacierre)
                ->first();
          
            if(Auth::user()->id==$s_aperturacierre->idusersresponsable){
                DB::table('s_aperturacierre')->whereId($s_idaperturacierre)->update([
                    'fechacierreconfirmacion' => Carbon::now()
                ]);
            }
          
            if(configuracion($idtienda,'prestamo_tipocierrecaja')['valor']==3){
          
                $cierrecantidadsoles1 = $request->cierrecantidadsoles1!=''?$request->cierrecantidadsoles1:0;
                $cierrecantidadsoles2 = $request->cierrecantidadsoles2!=''?$request->cierrecantidadsoles2:0;
                $cierrecantidadsoles3 = $request->cierrecantidadsoles3!=''?$request->cierrecantidadsoles3:0;
                $cierrecantidadsoles4 = $request->cierrecantidadsoles4!=''?$request->cierrecantidadsoles4:0;
                $cierrecantidadsoles5 = $request->cierrecantidadsoles5!=''?$request->cierrecantidadsoles5:0;
                $cierrecantidadsoles6 = $request->cierrecantidadsoles6!=''?$request->cierrecantidadsoles6:0;
                $cierrecantidadsoles7 = $request->cierrecantidadsoles7!=''?$request->cierrecantidadsoles7:0;
                $cierrecantidadsoles8 = $request->cierrecantidadsoles8!=''?$request->cierrecantidadsoles8:0;
                $cierrecantidadsoles9 = $request->cierrecantidadsoles9!=''?$request->cierrecantidadsoles9:0;
                $cierrecantidadsoles10 = $request->cierrecantidadsoles10!=''?$request->cierrecantidadsoles10:0;
                $cierrecantidadsoles11 = $request->cierrecantidadsoles11!=''?$request->cierrecantidadsoles11:0;
              
                $cierrecantidaddolares1 = $request->cierrecantidaddolares1!=''?$request->cierrecantidaddolares1:0;
                $cierrecantidaddolares2 = $request->cierrecantidaddolares2!=''?$request->cierrecantidaddolares2:0;
                $cierrecantidaddolares3 = $request->cierrecantidaddolares3!=''?$request->cierrecantidaddolares3:0;
                $cierrecantidaddolares4 = $request->cierrecantidaddolares4!=''?$request->cierrecantidaddolares4:0;
                $cierrecantidaddolares5 = $request->cierrecantidaddolares5!=''?$request->cierrecantidaddolares5:0;
                $cierrecantidaddolares6 = $request->cierrecantidaddolares6!=''?$request->cierrecantidaddolares6:0;
                $cierrecantidaddolares7 = $request->cierrecantidaddolares7!=''?$request->cierrecantidaddolares7:0;
                $cierrecantidaddolares9 = $request->cierrecantidaddolares9!=''?$request->cierrecantidaddolares9:0;
                $cierrecantidaddolares10 = $request->cierrecantidaddolares10!=''?$request->cierrecantidaddolares10:0;
                $cierrecantidaddolares11 = $request->cierrecantidaddolares11!=''?$request->cierrecantidaddolares11:0;
                $cierrecantidaddolares12 = $request->cierrecantidaddolares12!=''?$request->cierrecantidaddolares12:0;
                $cierrecantidaddolares13 = $request->cierrecantidaddolares13!=''?$request->cierrecantidaddolares13:0;
              
                
                  
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 200,
                    'cantidad' => $cierrecantidadsoles1,
                    'total' => 200*$cierrecantidadsoles1,
                    'idmoneda' => 1,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 100,
                    'cantidad' => $cierrecantidadsoles2,
                    'total' => 100*$cierrecantidadsoles2,
                    'idmoneda' => 1,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 50,
                    'cantidad' => $cierrecantidadsoles3,
                    'total' => 50*$cierrecantidadsoles3,
                    'idmoneda' => 1,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 20,
                    'cantidad' => $cierrecantidadsoles4,
                    'total' => 20*$cierrecantidadsoles4,
                    'idmoneda' => 1,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 10,
                    'cantidad' => $cierrecantidadsoles5,
                    'total' => 10*$cierrecantidadsoles5,
                    'idmoneda' => 1,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 5,
                    'cantidad' => $cierrecantidadsoles6,
                    'total' => 5*$cierrecantidadsoles6,
                    'idmoneda' => 1,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 2,
                    'cantidad' => $cierrecantidadsoles7,
                    'total' => 2*$cierrecantidadsoles7,
                    'idmoneda' => 1,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 1,
                    'cantidad' => $cierrecantidadsoles8,
                    'total' => 1*$cierrecantidadsoles8,
                    'idmoneda' => 1,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 0.5,
                    'cantidad' => $cierrecantidadsoles9,
                    'total' => 0.5*$cierrecantidadsoles9,
                    'idmoneda' => 1,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 0.2,
                    'cantidad' => $cierrecantidadsoles10,
                    'total' => 0.2*$cierrecantidadsoles10,
                    'idmoneda' => 1,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 0.1,
                    'cantidad' => $cierrecantidadsoles11,
                    'total' => 0.1*$cierrecantidadsoles11,
                    'idmoneda' => 1,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
              
                
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 100,
                    'cantidad' => $cierrecantidaddolares1,
                    'total' => 100*$cierrecantidaddolares1,
                    'idmoneda' => 2,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 50,
                    'cantidad' => $cierrecantidaddolares2,
                    'total' => 50*$cierrecantidaddolares2,
                    'idmoneda' => 2,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 20,
                    'cantidad' => $cierrecantidaddolares3,
                    'total' => 20*$cierrecantidaddolares3,
                    'idmoneda' => 2,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 10,
                    'cantidad' => $cierrecantidaddolares4,
                    'total' => 10*$cierrecantidaddolares4,
                    'idmoneda' => 2,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 5,
                    'cantidad' => $cierrecantidaddolares5,
                    'total' => 5*$cierrecantidaddolares5,
                    'idmoneda' => 2,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 2,
                    'cantidad' => $cierrecantidaddolares6,
                    'total' => 2*$cierrecantidaddolares6,
                    'idmoneda' => 2,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 1,
                    'cantidad' => $cierrecantidaddolares7,
                    'total' => 1*$cierrecantidaddolares7,
                    'idmoneda' => 2,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 0.5,
                    'cantidad' => $cierrecantidaddolares9,
                    'total' => 0.5*$cierrecantidaddolares9,
                    'idmoneda' => 2,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 0.25,
                    'cantidad' => $cierrecantidaddolares10,
                    'total' => 0.25*$cierrecantidaddolares10,
                    'idmoneda' => 2,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 0.10,
                    'cantidad' => $cierrecantidaddolares11,
                    'total' => 0.10*$cierrecantidaddolares11,
                    'idmoneda' => 2,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 0.05,
                    'cantidad' => $cierrecantidaddolares12,
                    'total' => 0.05*$cierrecantidaddolares12,
                    'idmoneda' => 2,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
                DB::table('s_aperturacierrebilletaje')->insert([
                    'fecharegistro' => $fecharegistro,
                    'denominacion' => 0.01,
                    'cantidad' => $cierrecantidaddolares13,
                    'total' => 0.01*$cierrecantidaddolares13,
                    'idmoneda' => 2,
                    'idaperturacierre' => $s_idaperturacierre,
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
            }

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha confirmado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'confirmarrecepcioncierre') {
          
            $s_aperturacierre = DB::table('s_aperturacierre')
                ->where('s_aperturacierre.id',$s_idaperturacierre)
                ->first();
            $totalsobrante_soles = 0;
            $totalsobrante_dolares = 0;
            $motivo_cierre = '';
            if($request->estado=='confirmarcierre'){
                $rules = [];
                /*if($s_aperturacierre->config_sistema_moneda_usar==1){
                    $rules = array_merge($rules,[
                        'totalsobrante_soles' => 'required',
                    ]);
                }elseif($s_aperturacierre->config_sistema_moneda_usar==2){
                    $rules = array_merge($rules,[
                        'totalsobrante_dolares' => 'required',
                    ]);
                }elseif($s_aperturacierre->config_sistema_moneda_usar==3){
                    $rules = array_merge($rules,[
                        'totalsobrante_soles' => 'required',
                        'totalsobrante_dolares' => 'required',
                    ]);
                }*/
                $rules = array_merge($rules,[
                    'motivo_cierre' => 'required',
                ]);
                $messages = [
                    'montoasignar.required' => 'El "Monto a asignar en Soles" es Obligatorio.',
                    'montoasignar_dolares.required' => 'El "Monto a asignar en Dolares" es Obligatorio.',
                    'idusers.required' => 'El "Persona a asignar" es Obligatorio.',
                ];
                $this->validate($request,$rules,$messages);
                $totalsobrante_soles = $request->totalsobrante_soles;
                $totalsobrante_dolares = $request->totalsobrante_dolares;
                $motivo_cierre = $request->motivo_cierre;
            }else{
                if($s_aperturacierre->config_prestamo_tipocierrecaja==3){
                    if($s_aperturacierre->montocierre>$s_aperturacierre->montocierre_recibido){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'Monto cerrado en Soles es menor a "Total en Soles"'
                        ]);
                    }
                    if($s_aperturacierre->montocierre_dolares>$s_aperturacierre->montocierre_recibido_dolares){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'Monto cerrado en Dolares es menor a "Total en Dolares"'
                        ]);
                    }

                    if($s_aperturacierre->montocierre<$s_aperturacierre->montocierre_recibido){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'Monto cerrado en Soles es mayor a "Total en Soles"'
                        ]);
                    }
                    if($s_aperturacierre->montocierre_dolares<$s_aperturacierre->montocierre_recibido_dolares){
                        return response()->json([
                            'resultado' => 'ERROR',
                            'mensaje'   => 'Monto cerrado en Dolares es mayor a "Total en Dolares"'
                        ]);
                    }
                } 
            }
          
                
          
            DB::table('s_aperturacierre')->whereId($s_idaperturacierre)->update([
                'fechacierreconfirmacion' => Carbon::now(),
                'totalsobrante_soles' => $totalsobrante_soles,
                'totalsobrante_dolares' => $totalsobrante_dolares,
                'motivo_cierre' => $motivo_cierre,
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha cofirmado correctamente.'
            ]);
        }
        elseif($request->input('view') == 'anularenviocierre') {

            DB::table('s_aperturacierre')->whereId($s_idaperturacierre)->update([
                'montocierre' => '0.00',
                'montocierre_dolares' => '0.00',
                'idestadoaperturacierre' => 2,
                'montocierre_recibido' => 0,
                'montocierre_recibido_dolares' => 0,
            ]);
          
            DB::table('s_aperturacierrebilletaje')->where('idtienda',$idtienda)->where('idaperturacierre',$s_idaperturacierre)->delete();

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
                ->where('s_aperturacierre.idtienda',$idtienda)
                ->where('s_aperturacierre.id',$s_idaperturacierre)
                ->delete();
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha eliminado correctamente.'
						]);
        }
    }
}
