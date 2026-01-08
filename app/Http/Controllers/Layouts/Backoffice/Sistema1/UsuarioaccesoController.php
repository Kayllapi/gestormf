<?php

namespace App\Http\Controllers\Layouts\Backoffice\Sistema;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Role;
use App\User;
use Auth;
use Hash;
use DB;
use Image;
use Intervention\Image\ImageManager;

class UsuarioaccesoController extends Controller
{
    public function index(Request $request,$idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        $tienda = DB::table('tienda')->whereId($idtienda)->first();

        $where = [];
        $where[] = ['users.identificacion','LIKE','%'.$request->input('identificacion').'%'];
        $where[] = ['users.nombre','LIKE','%'.$request->input('cliente').'%'];
        $where[] = ['users.usuario','LIKE','%'.$request->input('usuario').'%'];
        $where[] = ['users.idestadousuario','LIKE',$request->input('acceso')];
      
        if($request->input('permiso')!=''){
            $where[] = ['role_user.cargo','LIKE','%'.$request->input('permiso').'%'];
        }
      
        $where1 = [];
        $where1[] = ['users.identificacion','LIKE','%'.$request->input('identificacion').'%'];
        $where1[] = ['users.apellidos','LIKE','%'.$request->input('cliente').'%'];
        $where1[] = ['users.usuario','LIKE','%'.$request->input('usuario').'%'];
        $where1[] = ['users.idestadousuario','LIKE',$request->input('acceso')];
        if($request->input('permiso')!=''){
            $where1[] = ['role_user.cargo','LIKE','%'.$request->input('permiso').'%'];
        }
      
        $usuarios = DB::table('users')
            ->join('role_user','role_user.user_id','users.id')
            ->join('roles','roles.id','role_user.role_id')
            ->where($where)
            ->where('users.idtienda',$idtienda)
            ->where('users.idestado',1)
            ->orWhere($where1)
            ->where('users.idtienda',$idtienda)
            ->where('users.idestado',1)
            ->select(
                'users.*',
                'roles.id as idroles',
                'role_user.cargo as cargo'
            )
            ->orderBy('role_user.id','desc')
            ->paginate(10);
            

        return view('layouts/backoffice/tienda/sistema/usuarioacceso/index', [
            'tienda' => $tienda,
            'usuarios' => $usuarios
        ]);
    }

    public function create(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
      
        if ($request->view == 'registrar') {
            return view('layouts/backoffice/tienda/sistema/usuarioacceso/create', [
                'tienda' => $tienda,
            ]);
        }
    }

    public function store(Request $request, $idtienda)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if ($request->input('view') == 'registrar') {

            $rules = [
                    'idusuario' => 'required',
                    'cargo' => 'required',
                    'usuario' => 'required|regex:/^[A-Za-z0-9_.]+$/',
                    'password' => 'required',
                    'idestadousuario' => 'required',
            ];
            $messages = [
                    'idusuario.required' => 'El "Usuario" es Obligatorio.',
                    'usuario.required' => 'El "Usuario (Login)" es Obligatorio.',
                    'usuario.regex' => 'El "Usuario" es invalido (no puede utilizar caracteres especiales).',
                    'password.required' => 'La "Contraseña" es Obligatorio.',
                    'cargo.required' => 'El "Cargo" es Obligatorio.',
                    'idestadousuario.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            $usuario = DB::table('users')
                ->join('role_user','role_user.user_id','users.id')
                ->join('roles','roles.id','role_user.role_id')
                ->where('role_user.user_id',$request->idusuario)
                ->where('users.idtienda',$idtienda)
                ->where('users.idestado',1)
                ->first();
            if($usuario!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "Usuario ('.$usuario->apellidos.', '.$usuario->nombre.')" ya tiene un permiso, Ingrese Otro por favor.'
                ]);
            }
          
            $usuario = $request->usuario.'@'.$idtienda.'.com';
          
            $users = DB::table('users')->where('users.idtienda',$idtienda)->where('usuario',$usuario)->first();
            if($users!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "Usuario" ya existe, ingreso otro.'
                ]);
            }
       
            DB::table('users')->whereId($request->idusuario)->update([
                'usuario' => $usuario,
                'clave' => $request->password,
                'password' => Hash::make($request->password),
                'idestadousuario' => $request->idestadousuario,
            ]);
           
            DB::table('role_user')->insert([
                'role_id' => 3,
                'user_id' => $request->idusuario,
                'cargo' => $request->cargo,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
          
            // registrando modulos
            $list = explode(',',$request->input('idmodulos'));
            $idmodulos = '';
            for ($i=1; $i < count($list); $i++) { 
                $idmodulos = $idmodulos.$list[$i];
                DB::table('usersrolesmodulo')->insert([
                    'idusers' => $request->idusuario,
                    'idmodulo' => $list[$i],
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
            } 
          
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_lunes==1?($request->lunes_inicio!=''?$request->lunes_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_lunes==1?($request->lunes_cierre!=''?$request->lunes_cierre:'00:00'):'00.00',
                'dia' => 'LUNES',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_lunes!='null'?$request->idestadousuariohorarioacceso_lunes:0,
                'idusers' => $request->idusuario,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_martes==1?($request->martes_inicio!=''?$request->martes_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_martes==1?($request->martes_cierre!=''?$request->martes_cierre:'00:00'):'00.00',
                'dia' => 'MARTES',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_martes!='null'?$request->idestadousuariohorarioacceso_martes:0,
                'idusers' => $request->idusuario,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_miercoles==1?($request->miercoles_inicio!=''?$request->miercoles_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_miercoles==1?($request->miercoles_cierre!=''?$request->miercoles_cierre:'00:00'):'00.00',
                'dia' => 'MIERCOLES',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_miercoles!='null'?$request->idestadousuariohorarioacceso_miercoles:0,
                'idusers' => $request->idusuario,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_jueves==1?($request->jueves_inicio!=''?$request->jueves_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_jueves==1?($request->jueves_cierre!=''?$request->jueves_cierre:'00:00'):'00.00',
                'dia' => 'JUEVES',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_jueves!='null'?$request->idestadousuariohorarioacceso_jueves:0,
                'idusers' => $request->idusuario,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_viernes==1?($request->viernes_inicio!=''?$request->viernes_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_viernes==1?($request->viernes_cierre!=''?$request->viernes_cierre:'00:00'):'00.00',
                'dia' => 'VIERNES',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_viernes!='null'?$request->idestadousuariohorarioacceso_viernes:0,
                'idusers' => $request->idusuario,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_sabado==1?($request->sabado_inicio!=''?$request->sabado_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_sabado==1?($request->sabado_cierre!=''?$request->sabado_cierre:'00:00'):'00.00',
                'dia' => 'SABADO',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_sabado!='null'?$request->idestadousuariohorarioacceso_sabado:0,
                'idusers' => $request->idusuario,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_domingo==1?($request->domingo_inicio!=''?$request->domingo_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_domingo==1?($request->domingo_cierre!=''?$request->domingo_cierre:'00:00'):'00.00',
                'dia' => 'DOMINGO',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_domingo!='null'?$request->idestadousuariohorarioacceso_domingo:0,
                'idusers' => $request->idusuario,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_feriado==1?($request->feriado_inicio!=''?$request->feriado_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_feriado==1?($request->feriado_cierre!=''?$request->feriado_cierre:'00:00'):'00.00',
                'dia' => 'FERIADO',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_feriado!='null'?$request->idestadousuariohorarioacceso_feriado:0,
                'idusers' => $request->idusuario,
                'idtienda' => $idtienda,
                'idestado' => 1
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
        if ($id == 'show-detalle-permiso') {

            $permiso = DB::table('roles')->whereId($request->idrol)->first();
            $modulos = DB::table('modulo')
                ->where('idmodulo','0')
                ->where('idestado',1)
                ->orderBy('orden','asc')
                ->get();
            $html = "<table class='table' id='tabla-contenido'>
                        <thead class='thead-dark'>
                            <tr>
                                <th colspan='4'>Módulos</th>
                            </tr>
                        </thead>  
                        <tbody>";
            foreach ($modulos as $value) {

                $submodulos = DB::table('modulo')
                    ->where('idmodulo',$value->id)
                    ->where('idestado',1)
                    ->orderBy('orden','asc')
                    ->get();

                foreach ($submodulos as $subvalue) {

                    $subsubmodulos = DB::table('modulo')
                        ->where('idmodulo',$subvalue->id)
                        ->orderBy('orden','asc')
                        ->where('idestado',1)
                        ->get();

                    foreach($subsubmodulos as $subsubvalue) {
                        $rolesmodulo = DB::table('rolesmodulo')->where('idroles',$permiso->id)->where('idmodulo',$subsubvalue->id)->limit(1)->first(); 
                        if ($rolesmodulo != '') {
                            $html .= "<tr>
                                        <td width='10px' style='padding: 6px;'>
                                            <i class='fas fa-circle' style='font-size:10px;'></i>
                                        </td>
                                        <td colspan='2'>$subsubvalue->nombre</td>
                                    </tr>";
                        }

                        $sistemamodulos = DB::table('modulo')
                            ->where('idmodulo',$subsubvalue->id)
                            ->orderBy('orden','asc')
                            ->where('idestado',1)
                            ->get();

                        foreach($sistemamodulos as $sistemavalue) {
                            $rolesmodulo = DB::table('rolesmodulo')->where('idroles',$permiso->id)->where('idmodulo',$sistemavalue->id)->limit(1)->first(); 
                            if ($rolesmodulo != '') {
                                $html .= "<tr>
                                            <td></td>
                                            <td width='10px' style='padding: 6px;'>
                                                <i class='fas fa-check' style='font-size:10px;'></i>
                                            </td>
                                            <td>$sistemavalue->nombre</td>
                                        </tr>";
                            }
                        }
                    }
                }
            }
            $html .= "</tbody>
                  </table>";
            return [
                'html' => $html
            ];

        }
    }

    public function edit(Request $request, $idtienda, $id)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
        $tienda = DB::table('tienda')->whereId($idtienda)->first();
        $usuario = DB::table('users')->where('users.id', $id)
            ->join('role_user','role_user.user_id','users.id')
            ->join('roles','roles.id','role_user.role_id')
            ->select(
                'users.*',
                'roles.id as idroles',
                'role_user.cargo as cargo',
                'roles.description as descriptionrole'
            )
            ->first();
      
        $usuariohorarioacceso_lunes = DB::table('s_usuariohorarioacceso')
                ->where('s_usuariohorarioacceso.idtienda',$idtienda)
                ->where('s_usuariohorarioacceso.idusers',$id)
                ->where('s_usuariohorarioacceso.dia','LUNES')
                ->first();
        $usuariohorarioacceso_martes = DB::table('s_usuariohorarioacceso')
                ->where('s_usuariohorarioacceso.idtienda',$idtienda)
                ->where('s_usuariohorarioacceso.idusers',$id)
                ->where('s_usuariohorarioacceso.dia','MARTES')
                ->first();
        $usuariohorarioacceso_miercoles = DB::table('s_usuariohorarioacceso')
                ->where('s_usuariohorarioacceso.idtienda',$idtienda)
                ->where('s_usuariohorarioacceso.idusers',$id)
                ->where('s_usuariohorarioacceso.dia','MIERCOLES')
                ->first();
        $usuariohorarioacceso_jueves = DB::table('s_usuariohorarioacceso')
                ->where('s_usuariohorarioacceso.idtienda',$idtienda)
                ->where('s_usuariohorarioacceso.idusers',$id)
                ->where('s_usuariohorarioacceso.dia','JUEVES')
                ->first();
        $usuariohorarioacceso_viernes = DB::table('s_usuariohorarioacceso')
                ->where('s_usuariohorarioacceso.idtienda',$idtienda)
                ->where('s_usuariohorarioacceso.idusers',$id)
                ->where('s_usuariohorarioacceso.dia','VIERNES')
                ->first();
        $usuariohorarioacceso_sabado = DB::table('s_usuariohorarioacceso')
                ->where('s_usuariohorarioacceso.idtienda',$idtienda)
                ->where('s_usuariohorarioacceso.idusers',$id)
                ->where('s_usuariohorarioacceso.dia','SABADO')
                ->first();
        $usuariohorarioacceso_domingo = DB::table('s_usuariohorarioacceso')
                ->where('s_usuariohorarioacceso.idtienda',$idtienda)
                ->where('s_usuariohorarioacceso.idusers',$id)
                ->where('s_usuariohorarioacceso.dia','DOMINGO')
                ->first();
        $usuariohorarioacceso_feriado = DB::table('s_usuariohorarioacceso')
                ->where('s_usuariohorarioacceso.idtienda',$idtienda)
                ->where('s_usuariohorarioacceso.idusers',$id)
                ->where('s_usuariohorarioacceso.dia','FERIADO')
                ->first();

        if ($request->input('view') == 'editar') {
            return view('layouts/backoffice/tienda/sistema/usuarioacceso/edit',[
                'usuario' => $usuario,
                'tienda' => $tienda,
                'usuariohorarioacceso_lunes' => $usuariohorarioacceso_lunes,
                'usuariohorarioacceso_martes' => $usuariohorarioacceso_martes,
                'usuariohorarioacceso_miercoles' => $usuariohorarioacceso_miercoles,
                'usuariohorarioacceso_jueves' => $usuariohorarioacceso_jueves,
                'usuariohorarioacceso_viernes' => $usuariohorarioacceso_viernes,
                'usuariohorarioacceso_sabado' => $usuariohorarioacceso_sabado,
                'usuariohorarioacceso_domingo' => $usuariohorarioacceso_domingo,
                'usuariohorarioacceso_feriado' => $usuariohorarioacceso_feriado,
            ]); 
        } 
        elseif ($request->input('view')=='eliminar') {
            return view('layouts/backoffice/tienda/sistema/usuarioacceso/delete',[
                'usuario' => $usuario,
                'tienda' => $tienda,
            ]); 
        } 
    }

    public function update(Request $request, $idtienda, $idusuario)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if ($request->input('view') == 'editar') {

             $rules = [
                    'cargo' => 'required',
                    'usuario' => 'required',
                    'idestadousuario' => 'required',
            ];
            $messages = [
                    'cargo.required' => 'El "Cargo" es Obligatorio.',
                    'usuario.required' => 'El "Usuario (Login)" es Obligatorio.',
                    'idestadousuario.required' => 'El "Estado" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);

            $newusuario = $request->input('usuario').'@'.$idtienda.'.com';
            $users = DB::table('users')->where('idtienda',$idtienda)->where('id','<>',$idusuario)->where('usuario',$newusuario)->first();
            if($users!=''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje'   => 'El "Usuario" ya existe, ingreso otro.'
                ]);
            }

            if($request->input('password')!=''){
                 DB::table('users')->whereId($idusuario)->update([
                    'usuario' => $newusuario,
                    'clave' => $request->password,
                    'password' => Hash::make($request->password),
                    'idestadousuario' => $request->idestadousuario,
                ]);
            }else{
                 DB::table('users')->whereId($idusuario)->update([
                    'usuario' => $newusuario,
                    'idestadousuario' => $request->idestadousuario,
                ]);
            }
          
            // actualizar modulos
            DB::table('usersrolesmodulo')->where('idusers',$idusuario)->delete();
            $list = explode(',',$request->input('idmodulos'));
            $idmodulos = '';
            for ($i=1; $i < count($list); $i++) { 
                $idmodulos = $idmodulos.$list[$i];
                DB::table('usersrolesmodulo')->insert([
                    'idusers' => $idusuario,
                    'idmodulo' => $list[$i],
                    'idtienda' => $idtienda,
                    'idestado' => 1
                ]);
            }      
            
            DB::table('role_user')->where('role_user.user_id', $idusuario)->update([
                'role_id' => 3,
                'cargo' => $request->input('cargo'),
            ]);
          
            // actualizarlosa acceso
            DB::table('s_usuariohorarioacceso')
                ->where('s_usuariohorarioacceso.idtienda',$idtienda)
                ->where('s_usuariohorarioacceso.idusers',$idusuario)
                ->delete();
  
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_lunes==1?($request->lunes_inicio!=''?$request->lunes_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_lunes==1?($request->lunes_cierre!=''?$request->lunes_cierre:'00:00'):'00.00',
                'dia' => 'LUNES',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_lunes!='null'?$request->idestadousuariohorarioacceso_lunes:0,
                'idusers' => $idusuario,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_martes==1?($request->martes_inicio!=''?$request->martes_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_martes==1?($request->martes_cierre!=''?$request->martes_cierre:'00:00'):'00.00',
                'dia' => 'MARTES',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_martes!='null'?$request->idestadousuariohorarioacceso_martes:0,
                'idusers' => $idusuario,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_miercoles==1?($request->miercoles_inicio!=''?$request->miercoles_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_miercoles==1?($request->miercoles_cierre!=''?$request->miercoles_cierre:'00:00'):'00.00',
                'dia' => 'MIERCOLES',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_miercoles!='null'?$request->idestadousuariohorarioacceso_miercoles:0,
                'idusers' => $idusuario,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_jueves==1?($request->jueves_inicio!=''?$request->jueves_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_jueves==1?($request->jueves_cierre!=''?$request->jueves_cierre:'00:00'):'00.00',
                'dia' => 'JUEVES',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_jueves!='null'?$request->idestadousuariohorarioacceso_jueves:0,
                'idusers' => $idusuario,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_viernes==1?($request->viernes_inicio!=''?$request->viernes_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_viernes==1?($request->viernes_cierre!=''?$request->viernes_cierre:'00:00'):'00.00',
                'dia' => 'VIERNES',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_viernes!='null'?$request->idestadousuariohorarioacceso_viernes:0,
                'idusers' => $idusuario,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_sabado==1?($request->sabado_inicio!=''?$request->sabado_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_sabado==1?($request->sabado_cierre!=''?$request->sabado_cierre:'00:00'):'00.00',
                'dia' => 'SABADO',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_sabado!='null'?$request->idestadousuariohorarioacceso_sabado:0,
                'idusers' => $idusuario,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_domingo==1?($request->domingo_inicio!=''?$request->domingo_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_domingo==1?($request->domingo_cierre!=''?$request->domingo_cierre:'00:00'):'00.00',
                'dia' => 'DOMINGO',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_domingo!='null'?$request->idestadousuariohorarioacceso_domingo:0,
                'idusers' => $idusuario,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
            DB::table('s_usuariohorarioacceso')->insert([
                'horainicio' => $request->idestadousuariohorarioacceso_feriado==1?($request->feriado_inicio!=''?$request->feriado_inicio:'00:00'):'00.00',
                'horacierre' => $request->idestadousuariohorarioacceso_feriado==1?($request->feriado_cierre!=''?$request->feriado_cierre:'00:00'):'00.00',
                'dia' => 'FERIADO',
                'idestadousuariohorarioacceso' => $request->idestadousuariohorarioacceso_feriado!='null'?$request->idestadousuariohorarioacceso_feriado:0,
                'idusers' => $idusuario,
                'idtienda' => $idtienda,
                'idestado' => 1
            ]);
          
          
            // configuración
            configuracion_update($idtienda,'usuario_estadodescuentointeres',$request->usuario_estadodescuentointeres,$idusuario);
      
          
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje'   => 'Se ha actualizado correctamente.'
            ]);

        } 
    }

    public function destroy(Request $request, $idtienda, $idusuario)
    {
        $request->user()->authorizeRoles($request->path(),$idtienda);
      
        if ($request->input('view') == 'eliminar') {
          
            $newusuario = Carbon::now()->format("Ymdhisu").'@'.$idtienda.'.com';
          
            DB::table('users')
                ->where('id',$idusuario)
                ->where('idtienda',$idtienda)
                ->update([
                  'usuario'   => $newusuario,
                  'clave'     => '123',
                  'password'  => Hash::make('123'),
                ]);
          
            DB::table('role_user')
                ->where('user_id',$idusuario)
                ->delete();
          
            DB::table('usersrolesmodulo')->where('idusers',$idusuario)->delete();
          
            return response()->json([
                'resultado' => 'CORRECTO',
                    'mensaje'   => 'Se ha eliminado correctamente.'
            ]);
        }
       
    }
}
