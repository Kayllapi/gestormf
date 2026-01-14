<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use Hash;

class UsuarioaccesoController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if($request->input('view') == 'tabla'){
            return view(sistema_view().'/usuarioacceso/tabla',[
                'tienda' => $tienda,
            ]);
        }
    }
  
    public function create(Request $request,$idtienda)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $estadocivil = DB::table('f_estadocivil')->get();
      
        if($request->view == 'registrar') {
            $permisos = DB::table('permiso')
                        ->where('permiso.idtienda',$idtienda)
                        ->get();

            $tiendas = DB::table('tienda')->get();
            return view(sistema_view().'/usuarioacceso/create',[
                'tienda'        => $tienda,
                'tiendas'       => $tiendas,
                'permisos'      => $permisos,
                'estadocivil'   => $estadocivil
            ]);
        }
        else if($request->view == 'autorizacion'){
            $responsable = DB::table('users')->whereId($request->idusuario)->first();
          
            $usuarios = DB::table('users')
                ->join('users_permiso','users_permiso.idusers','users.id')
                ->join('permiso','permiso.id','users_permiso.idpermiso')
                ->where('users_permiso.idpermiso',2)
                ->where('users_permiso.idtienda',$idtienda)
                ->select('users.*','permiso.nombre as nombrepermiso')
                ->get();
            return view(sistema_view().'/usuarioacceso/autorizacion',[
                'tienda' => $tienda,
                'usuarios' => $usuarios,
                'responsable' => $responsable,
            ]);
        }
    }
  
    public function store(Request $request, $idtienda)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if ($request->input('view') == 'registrar') {

            $rules = [
                    // 'idusuario' => 'required',
                    // 'cargo' => 'required',
                    'usuario' => 'required|regex:/^[A-Za-z0-9_.]+$/',
                    'password' => 'required',
                    'idestadousuario' => 'required',
                    'apellido_parterno' => 'required',
                    'apellido_marterno' => 'required',
                    'nombres' => 'required',
                    'identificacion' => 'required',
                    'direccion' => 'required',
                    'idubigeo' => 'required',
                    'fecha_nacimiento' => 'required',
                    'celular' => 'required',
                    'idestadodivil' => 'required',
                    'profesion' => 'required',
            ];
            $messages = [
                    // 'idusuario.required' => 'El "Usuario" es Obligatorio.',
                    'usuario.required' => 'El "Usuario (Login)" es Obligatorio.',
                    'usuario.regex' => 'El "Usuario" es invalido (no puede utilizar caracteres especiales).',
                    'password.required' => 'La "Contraseña" es Obligatorio.',
                    // 'cargo.required' => 'El "Cargo" es Obligatorio.',
                    'idestadousuario.required' => 'El "Estado" es Obligatorio.',
                    'apellido_parterno.required' => 'El es Obligatorio.',
                    'apellido_marterno.required' => 'El es Obligatorio.',
                    'nombres.required' => 'El es Obligatorio.',
                    'identificacion.required' => 'El es Obligatorio.',
                    'direccion.required' => 'El es Obligatorio.',
                    'idubigeo.required' => 'El "Distrito – Provincia – Departamento" es Obligatorio.',
                    'fecha_nacimiento.required' => 'El es Obligatorio.',
                    'celular.required' => 'El es Obligatorio.',
                    'idestadodivil.required' => 'El es Obligatorio.',
                    'profesion.required' => 'El es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            $accesos = json_decode($request->input('accesos'),true);
            $guardados = [];
            foreach($accesos as $value){

                $clave = $value['idtienda'] . '_' . $value['idpermiso'];

                if (!isset($guardados[$clave])) {
                    $guardados[$clave] = true;
                }else{
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El cargo por agencia ya existe!!.'
                    ]);
                }
            }

            // INICIO CREAR USUARIO

            // apellido_parterno
            // apellido_marterno
            // nombres
            // identificacion
            // direccion
            // fecha_nacimiento
            // 
            // celular
            
            // idestadodivil
            // profesion
            // nivel_aprox_credito
            // e_caja
            $user_id = DB::table('users')->insertGetId([
                'codigo'              => '00',
                'idtipopersona'       => 1,
                'nombre'              => $request->input('nombres'),
                'apellidopaterno'     => $request->input('apellido_parterno'),
                'apellidomaterno'     => $request->input('apellido_marterno'),
                'razonsocial'         => '',
                'nombrecompleto'      => $request->input('apellido_parterno').' '.$request->input('apellido_marterno').', '.$request->input('nombres'),
                'identificacion'      => $request->input('identificacion'),
                'email'               => '',
                'imagen'              => '',
                'numerotelefono'      => $request->celular!='' ? $request->celular : '',
                'direccion'           => $request->direccion!='' ? $request->direccion : '',
                'mapa_latitud'        => '',
                'mapa_longitud'       => '',
                'email_verified_at'   => Carbon::now(),
                'usuario'             => Carbon::now()->format("Ymdhisu"),
                'clave'               => $request->input('password'),
                'password'            => Hash::make($request->input('password')),
                'idubigeo'            => $request->idubigeo != null ? $request->idubigeo : 0,
                'iduserspadre'        => 0,
                'idtipousuario'       => 1, // 1=USUARIO | 2=CLIENTE
                'idtienda'            => $idtienda,
                'idestadousuario'     => 2,
                'idestado'            => 1,
              
                'profesion'           => $request->input('profesion'),
                'nivelcredito'        => '',
                'ecaja'               => '',
              
                'idgenero'            => 0,
                'fechanacimiento'     => $request->fecha_nacimiento,
                'idestadocivil'       => $request->idestadodivil != null ? $request->idestadodivil : 0 ,
                'idnivelestudio'      => 0 ,
            ]);
           
            DB::table('users')->whereId($user_id)->update([
                'codigo'       => 'U'.str_pad($user_id, 8, "0", STR_PAD_LEFT),
            ]); 
            // FIN CREAR USUARIO
            
            $usuario = DB::table('users')
                        ->join('role_user','role_user.user_id','users.id')
                        ->join('roles','roles.id','role_user.role_id')
                        ->where('role_user.user_id',$user_id)
                        ->where('users.idtienda',$idtienda)
                        ->where('users.idestado',1)
                        ->first();

            if($usuario){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "Usuario ('.$usuario->nombrecompleto.')" ya tiene un permiso, Ingrese Otro por favor.'
                ]);
            }
          
            $usuario = $request->usuario;
          
            $users = DB::table('users')->where('users.idtienda',$idtienda)->where('usuario',$usuario)->first();
            if($users!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "Usuario" ya existe, ingreso otro.'
                ]);
            }
       
            DB::table('users')->whereId($user_id)->update([
                'usuario' => $usuario,
                'clave' => $request->password,
                'password' => Hash::make($request->password),
                'idestadousuario' => $request->idestadousuario,
            ]);

            $accesos = json_decode($request->input('accesos'),true);
            
            foreach($accesos as $value){
                DB::table('users_permiso')->insert([
                    'idsession' => 1,
                    'idusers' => $user_id,
                    'idpermiso' => $value['idpermiso'],
                    'idtienda' => $value['idtienda'],
                    'idestado' => 1,
                ]);
            }
           
            // DB::table('role_user')->insert([
            //     'role_id' => 3,
            //     'user_id' => $user_id,
            //     'cargo'     => $request->profesion,
            //     'created_at' => Carbon::now(),
            //     'updated_at' => Carbon::now()
            // ]);
          
            //  // actualizar modulos
            //  DB::table('usersrolesmodulo')
            //     ->where('idtienda',$idtienda)
            //     ->where('idsucursal',$request->idsucursal)
            //     ->where('idusers',$user_id)
            //     ->delete();

            // $list = explode(',',$request->input('idmodulos'));
            // $idmodulos = '';
            // for ($i=1; $i < count($list); $i++) { 
            //     $idmodulos = $idmodulos.$list[$i];
            //     DB::table('usersrolesmodulo')->insert([
            //         'idusers' => $user_id,
            //         'idmodulo' => $list[$i],
            //         'idsucursal' => $request->idsucursal,
            //         'idtienda' => $idtienda,
            //         'idestado' => 1
            //     ]);
            // }     
        
            json_usuarioacceso($idtienda);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha registrado correctamente.'
            ]);

        } 
      
        else if($request->input('view') == 'autorizacion'){

            
            $rules = [
                'idresponsable' => 'required',          
                'responsableclave' => 'required',              
            ];
          
            $messages = [
                'idresponsable.required' => 'El "Responsable" es Obligatorio.',
                'responsableclave.required' => 'La "Contraseña" es Obligatorio.',
            ];

            $this->validate($request,$rules,$messages);
          
            $usuario = DB::table('users')
                ->where('users.id',$request->idresponsable)
                ->where('users.clave',$request->responsableclave)
                ->first();
            $idresponsable = 0;
            if($usuario!=''){
                $idresponsable = $usuario->id;
            }else{
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El usuario y/o la contraseña es incorrecta!!.'
                ]);
            }
          
            return response()->json([
                'resultado'           => 'CORRECTO',
                'mensaje'             => 'Contraseña Correcta.',
                'iduser_modificacion' => $idresponsable
            ]);
            
        }
    }

    public function show(Request $request, $idtienda, $id)
    {
        //
    }

    public function edit(Request $request, $idtienda, $id)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        $usuario = DB::table('users')
            ->where('users.id', $id)
            ->select(
                'users.*',
            )
            ->first();

        if($request->input('view') == 'permiso') {
            $estadocivil = DB::table('f_estadocivil')->get();
            $permisos = DB::table('permiso')
                        ->where('permiso.idtienda',$idtienda)
                        ->get();
            $user_permiso = DB::table('users_permiso')->where('users_permiso.idusers',$id)->get();
            $tiendas = DB::table('tienda')->get();
            return view(sistema_view().'/usuarioacceso/permiso',[
                'tienda'        => $tienda,
                'tiendas'       => $tiendas,
                'permisos'      => $permisos,
                'user_permiso'  => $user_permiso,
                'estadocivil'   => $estadocivil,
                'usuario'       => $usuario,
            ]); 
        } 
        
        
    }

    public function update(Request $request, $idtienda, $id)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'editar') {
            dd("editar");
            $rules = [
                // 'idusuario' => 'required',
                // 'cargo' => 'required',
                'usuario' => 'required',
                
                'idestadousuario' => 'required',
                'apellido_parterno' => 'required',
                'apellido_marterno' => 'required',
                'nombres' => 'required',
                'identificacion' => 'required',
                'direccion' => 'required',
                'fecha_nacimiento' => 'required',
                'celular' => 'required',
                'idestadodivil' => 'required',
                'profesion' => 'required',
            ];
            $messages = [
                // 'idusuario.required' => 'El "Usuario" es Obligatorio.',
                'usuario.required' => 'El "Usuario (Login)" es Obligatorio.',
                
                
                // 'cargo.required' => 'El "Cargo" es Obligatorio.',
                'idestadousuario.required' => 'El "Estado" es Obligatorio.',
                'apellido_parterno.required' => 'El es Obligatorio.',
                'apellido_marterno.required' => 'El es Obligatorio.',
                'nombres.required' => 'El es Obligatorio.',
                'identificacion.required' => 'El es Obligatorio.',
                'direccion.required' => 'El es Obligatorio.',
                'fecha_nacimiento.required' => 'El es Obligatorio.',
                'celular.required' => 'El es Obligatorio.',
                'idestadodivil.required' => 'El es Obligatorio.',
                'profesion.required' => 'El es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            $newusuario = $request->input('usuario');
            $users = DB::table('users')->where('idtienda',$idtienda)->where('id','<>',$id)->where('usuario',$newusuario)->first();
            if($users!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "Usuario" ya existe, ingreso otro.'
                ]);
            }
            DB::table('users')->whereId($id)->update([
                'codigo'              => 'U'.str_pad($id, 8, "0", STR_PAD_LEFT),
                'nombre'              => $request->input('nombres'),
                'apellidopaterno'     => $request->input('apellido_parterno'),
                'apellidomaterno'     => $request->input('apellido_marterno'),
                'nombrecompleto'      => $request->input('apellido_parterno').' '.$request->input('apellido_marterno').', '.$request->input('nombres'),
                'identificacion'      => $request->input('identificacion'),
                'numerotelefono'      => $request->celular!='' ? $request->celular : '',
                'direccion'           => $request->direccion!='' ? $request->direccion : '',
                'idtienda'            => $idtienda,
                'fechanacimiento'     => $request->fecha_nacimiento,
                'profesion'           => $request->input('profesion'),
                'nivelcredito'        => '',
                'ecaja'               => '',
                'idestadocivil'       => $request->idestadodivil != null ? $request->idestadodivil : 0 ,
            ]);

            if($request->input('password')!=''){
                 DB::table('users')->whereId($id)->update([
                    'usuario' => $newusuario,
                    'clave' => $request->password,
                    'password' => Hash::make($request->password),
                    'idestadousuario' => $request->idestadousuario,
                ]);
            }else{
                DB::table('users')->whereId($id)->update([
                    'usuario' => $newusuario,
                    'idestadousuario' => $request->idestadousuario,
                ]);
            }
            
            DB::table('role_user')->where('role_user.user_id', $id)->update([
                'role_id' => 3,
                'cargo' => $request->profesion,
            ]);
          
            json_usuarioacceso($idtienda);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);

        } 
        elseif($request->input('view') == 'permiso') {

            // $rules = [
            //     'idsucursal' => 'required',
            // ];
            // $messages = [
            //     'idsucursal.required' => 'La "Sucursal" es Obligatorio.',
            // ];
            // $this->validate($request,$rules,$messages);
          
            // actualizar modulos
            // DB::table('usersrolesmodulo')
            //     ->where('idtienda',$idtienda)
            //     ->where('idsucursal',$request->idsucursal)
            //     ->where('idusers',$id)
            //     ->delete();

            // $list = explode(',',$request->input('idmodulos'));
            // $idmodulos = '';
            // for ($i=1; $i < count($list); $i++) { 
            //     $idmodulos = $idmodulos.$list[$i];
            //     DB::table('usersrolesmodulo')->insert([
            //         'idusers' => $id,
            //         'idmodulo' => $list[$i],
            //         'idsucursal' => $request->idsucursal,
            //         'idtienda' => $idtienda,
            //         'idestado' => 1
            //     ]);
            // }     
           
            // json_usuarioacceso($idtienda);
            $rules = [
                // 'idusuario' => 'required',
                // 'cargo' => 'required',
                'usuario' => 'required',
                
                'idestadousuario' => 'required',
                'apellido_parterno' => 'required',
                'apellido_marterno' => 'required',
                'nombres' => 'required',
                'identificacion' => 'required',
                'direccion' => 'required',
                'idubigeo' => 'required',
                'fecha_nacimiento' => 'required',
                'celular' => 'required',
                'idestadodivil' => 'required',
                'profesion' => 'required',
            ];
            $messages = [
                // 'idusuario.required' => 'El "Usuario" es Obligatorio.',
                'usuario.required' => 'El "Usuario (Login)" es Obligatorio.',
                
                
                // 'cargo.required' => 'El "Cargo" es Obligatorio.',
                'idestadousuario.required' => 'El "Estado" es Obligatorio.',
                'apellido_parterno.required' => 'El es Obligatorio.',
                'apellido_marterno.required' => 'El es Obligatorio.',
                'nombres.required' => 'El es Obligatorio.',
                'identificacion.required' => 'El es Obligatorio.',
                'direccion.required' => 'El es Obligatorio.',
                'idubigeo.required' => 'El "Distrito – Provincia – Departamento" es Obligatorio.',
                'fecha_nacimiento.required' => 'El es Obligatorio.',
                'celular.required' => 'El es Obligatorio.',
                'idestadodivil.required' => 'El es Obligatorio.',
                'profesion.required' => 'El es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            
            $accesos = json_decode($request->input('accesos'),true);
            $guardados = [];
            foreach($accesos as $value){

                $clave = $value['idtienda'] . '_' . $value['idpermiso'];

                if (!isset($guardados[$clave])) {
                    $guardados[$clave] = true;
                }else{
                    return response()->json([
                        'resultado' => 'ERROR',
                        'mensaje'   => 'El cargo por agencia ya existe!!.'
                    ]);
                }
            }

            $newusuario = $request->input('usuario');
            $users = DB::table('users')->where('idtienda',$idtienda)->where('id','<>',$id)->where('usuario',$newusuario)->first();
            if($users!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "Usuario" ya existe, ingreso otro.'
                ]);
            }
            DB::table('users')->whereId($id)->update([
                'fechamodificacion' => Carbon::now(),
                'iduser_modificacion' => $request->iduser_modificacion,
              
                'codigo'              => 'U'.str_pad($id, 8, "0", STR_PAD_LEFT),
                'nombre'              => $request->input('nombres'),
                'apellidopaterno'     => $request->input('apellido_parterno'),
                'apellidomaterno'     => $request->input('apellido_marterno'),
                'nombrecompleto'      => $request->input('apellido_parterno').' '.$request->input('apellido_marterno').', '.$request->input('nombres'),
                'identificacion'      => $request->input('identificacion'),
                'numerotelefono'      => $request->celular!='' ? $request->celular : '',
                'direccion'           => $request->direccion!='' ? $request->direccion : '',
                'idubigeo'            => $request->idubigeo != null ? $request->idubigeo : 0,
                'idtienda'            => $idtienda,
                'fechanacimiento'     => $request->fecha_nacimiento,
                'profesion'           => $request->input('profesion'),
                'nivelcredito'        => '',
                'ecaja'               => '',
                'idestadocivil'       => $request->idestadodivil != null ? $request->idestadodivil : 0 ,
                'estadocreditonoprendario'  => $request->estadocreditonoprendario != null ? $request->estadocreditonoprendario : '' ,
                'estadocreditoprendario'    => $request->estadocreditoprendario != null ? $request->estadocreditoprendario : '' ,
            ]);

            if($request->input('password')!=''){
                 DB::table('users')->whereId($id)->update([
                    'usuario' => $newusuario,
                    'clave' => $request->password,
                    'password' => Hash::make($request->password),
                    'idestadousuario' => $request->idestadousuario,
                ]);
            }else{
                DB::table('users')->whereId($id)->update([
                    'usuario' => $newusuario,
                    'idestadousuario' => $request->idestadousuario,
                ]);
            }
            $accesos = json_decode($request->input('accesos'),true);
            DB::table('users_permiso')->where('users_permiso.idusers',$id)->delete();
            $i = 0;
            foreach($accesos as $value){
                $idsession = 1;
                if($i==0 && $value['idestado']==1){
                    $idsession = 2;
                }
                $i++;
                DB::table('users_permiso')->insert([
                    'idsession' => $idsession,
                    'idusers' => $id,
                    'idpermiso' => $value['idpermiso'],
                    'idtienda' => $value['idtienda'],
                    'idestado' => $value['idestado'],
                ]);
            }

            json_usuarioacceso($idtienda);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);

        } 
        elseif($request->input('view') == 'horario') {

            $rules = [
                'idsucursal' => 'required',
            ];
            $messages = [
                'idsucursal.required' => 'La "Sucursal" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            DB::table('s_usuariohorarioacceso')
                ->where('s_usuariohorarioacceso.idtienda',$idtienda)
                ->where('s_usuariohorarioacceso.idusers',$id)
                ->delete();
  
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_lunes==1?($request->lunes_inicio!=''?$request->lunes_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_lunes==1?($request->lunes_cierre!=''?$request->lunes_cierre:'00:00'):'00.00',
                'dia' => 'LUNES',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_lunes!='null'?$request->idestadousuariohorarioacceso_lunes:0,
                'idusers' => $id,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_martes==1?($request->martes_inicio!=''?$request->martes_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_martes==1?($request->martes_cierre!=''?$request->martes_cierre:'00:00'):'00.00',
                'dia' => 'MARTES',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_martes!='null'?$request->idestadousuariohorarioacceso_martes:0,
                'idusers' => $id,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_miercoles==1?($request->miercoles_inicio!=''?$request->miercoles_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_miercoles==1?($request->miercoles_cierre!=''?$request->miercoles_cierre:'00:00'):'00.00',
                'dia' => 'MIERCOLES',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_miercoles!='null'?$request->idestadousuariohorarioacceso_miercoles:0,
                'idusers' => $id,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_jueves==1?($request->jueves_inicio!=''?$request->jueves_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_jueves==1?($request->jueves_cierre!=''?$request->jueves_cierre:'00:00'):'00.00',
                'dia' => 'JUEVES',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_jueves!='null'?$request->idestadousuariohorarioacceso_jueves:0,
                'idusers' => $id,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_viernes==1?($request->viernes_inicio!=''?$request->viernes_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_viernes==1?($request->viernes_cierre!=''?$request->viernes_cierre:'00:00'):'00.00',
                'dia' => 'VIERNES',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_viernes!='null'?$request->idestadousuariohorarioacceso_viernes:0,
                'idusers' => $id,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_sabado==1?($request->sabado_inicio!=''?$request->sabado_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_sabado==1?($request->sabado_cierre!=''?$request->sabado_cierre:'00:00'):'00.00',
                'dia' => 'SABADO',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_sabado!='null'?$request->idestadousuariohorarioacceso_sabado:0,
                'idusers' => $id,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_domingo==1?($request->domingo_inicio!=''?$request->domingo_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_domingo==1?($request->domingo_cierre!=''?$request->domingo_cierre:'00:00'):'00.00',
                'dia' => 'DOMINGO',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_domingo!='null'?$request->idestadousuariohorarioacceso_domingo:0,
                'idusers' => $id,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_feriado==1?($request->feriado_inicio!=''?$request->feriado_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_feriado==1?($request->feriado_cierre!=''?$request->feriado_cierre:'00:00'):'00.00',
                'dia' => 'FERIADO',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_feriado!='null'?$request->idestadousuariohorarioacceso_feriado:0,
                'idusers' => $id,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            
            json_usuarioacceso($idtienda);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);

        } 
            
    }


    public function destroy(Request $request, $idtienda, $id)
    {
        // $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if($request->input('view') == 'eliminar') {
          
            $newusuario = Carbon::now()->format("Ymdhisu");
          
            DB::table('users')
                ->where('id',$id)
                ->where('idtienda',$idtienda)
                ->update([
                  'usuario'   => $newusuario,
                  'clave'     => '123',
                  'password'  => Hash::make('123'),
                ]);
          
            DB::table('role_user')
                ->where('user_id',$id)
                ->delete();
          
            DB::table('usersrolesmodulo')
                ->where('idtienda',$idtienda)
                ->where('idusers',$id)
                ->delete();
            
            json_usuarioacceso($idtienda);
          
            return response()->json([
                'resultado' => 'CORRECTO',
                    'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
    }
}
