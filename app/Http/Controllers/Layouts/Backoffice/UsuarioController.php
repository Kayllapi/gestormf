<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use App\Role;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use Hash;
use Mail;

class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
  
    public function index(Request $request)
    {
        $request->user()->authorizeRoles($request->path());
      
        $where = [];
        $where[] = ['users.idtienda',0];
        $where[] = ['users.nombre','LIKE','%'.$request->input('cliente').'%'];
        $where[] = ['users.usuario','LIKE','%'.$request->input('usuario').'%'];
      
        $where1 = [];
        $where1[] = ['users.idtienda',0];
//         $where1[] = ['users.nombrecompleto','LIKE','%'.$request->input('cliente').'%'];
        $where1[] = ['users.usuario','LIKE','%'.$request->input('usuario').'%'];
      
        $usuarios = DB::table('users')
            ->leftJoin('role_user','role_user.user_id','users.id')
            ->leftJoin('roles','roles.id','role_user.role_id')
            ->where($where)
            ->orWhere($where1)
            ->select(
                'users.*',
                'roles.id as idroles',
                'roles.description as descriptionrole'
            )
            ->orderBy('id','desc')
            ->paginate(10);
        return view('layouts/backoffice/usuario/index',[
            'usuarios' => $usuarios
        ]);
    }

    public function create(Request $request)
    {
        $request->user()->authorizeRoles($request->path());

        $ubigeos = DB::table('ubigeo')->get();
        return view('layouts/backoffice/usuario/create',[
            'ubigeos' => $ubigeos
        ]);
    }

    public function store(Request $request)
    {
        $request->user()->authorizeRoles($request->path());

        if($request->view == 'create') {
            $rules = [
								'dni'         => 'required',
								'nombre'         => 'required',
								'apellidos'        => 'required',
								'numerotelefono' => 'required',
								'email'         => 'required|email|unique:users',
								'clave' => 'required',
						];
						$messages = [
								'dni.required'         => 'El "DNI" es Obligatorio.',
								'nombre.required'         => 'El "Nombre" es Obligatorio.',
								'apellidos.required'        => 'Los "Apellidos" es Obligatorio.',
								'numerotelefono.required' => 'El "Número de Teléfono" es Obligatorio.',
								'email.required'         => 'El "Correo Electronico" es Obligatorio.',
                'email.email'         => 'El "Correo Electronico" es Invalido.',
								'clave.required'         => 'La "Clave" es Obligatorio.',
						];
            $this->validate($request,$rules,$messages);
          
            $user = User::create([
                'nombre'         => $request->input('nombre'),
                'apellidos'      => $request->input('apellidos'),
                'identificacion' => $request->input('dni')!=null?$request->input('dni'):'',
                'email'          => $request->input('email'),
                'email_verified_at'   => Carbon::now(),
                'usuario'        => $request->input('email'),
                'clave'          => $request->input('clave'),
                'password'       => Hash::make($request->input('clave')),
                'numerotelefono' => $request->input('numerotelefono'),
                'idtipopersona'  => 1,
                'idtipousuario'  => 1,
                'idestadousuario'=> 1,
                'idtienda'       => 0,
                'idestado'       => 1,
						]);
          
            $user->roles()->attach(Role::where('name', 'user')->first());
       
          
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha registrado correctamente.'
						]);
        }
    }

    public function show(Request $request, $id)
    {
        $request->user()->authorizeRoles($request->path());
    }

    public function edit(Request $request, $id)
    {
        $request->user()->authorizeRoles($request->path());

        $usuario = DB::table('users')->where('users.id', $id)
            ->leftJoin('role_user','role_user.user_id','users.id')
            ->leftJoin('roles','roles.id','role_user.role_id')
            ->select(
              'users.*',
              'roles.id as idroles',
              'roles.description as descriptionrole'
            )
            ->first();
    
        if($request->input('view')=='editarusuario'){
            $ubigeos = DB::table('ubigeo')->get();
            return view('layouts/backoffice/usuario/edit',[
                'ubigeos' => $ubigeos,
                'usuario' => $usuario
            ]);
          
        }
        elseif($request->input('view')=='confirmar'){
            return view('layouts/backoffice/usuario/confirmar',[
                'usuario' => $usuario
            ]);  
        }
        elseif($request->input('view')=='eliminar'){
            $ubigeos = DB::table('ubigeo')->get();
            return view('layouts/backoffice/usuario/delete',[
                'ubigeos' => $ubigeos,
                'usuario' => $usuario
            ]);
        }else if($request->input('view')=='tienda'){
          $tiendas = DB::table('tienda')->where('idusers',$id)->paginate();
          return view('layouts/backoffice/usuario/tienda',[
                'usuario' => $usuario,
                'tiendas' => $tiendas,
            ]);
        }else if($request->input('view')=='transferir'){
          $usuarios = DB::table('users')->where('idtienda',0)->get();
          $tiendas = DB::table('tienda')->whereId($id)->first();
          return view('layouts/backoffice/usuario/transferir',[
                'usuario' => $usuario,
                'tiendas' => $tiendas,
                'usuarios' => $usuarios,
            ]);
        }
    }

    public function update(Request $request, $id)
    { 
      
        $request->user()->authorizeRoles($request->path());
        
        if($request->input('view')=='editarusuario') {

            $rules = [
								'dni'         => 'required',
                'nombre'         => 'required',
                'apellidos'        => 'required',
                'numerotelefono' => 'required',
                'email'         => 'required|email',
                'idestadousuario' => 'required',
            ];
            $messages = [
								'dni.required'         => 'El "DNI" es Obligatorio.',
                'nombre.required'         => 'El "Nombre" es Obligatorio.',
                'apellidos.required'        => 'Los "Apellidos" es Obligatorio.',
                'numerotelefono.required' => 'El "Número de Teléfono" es Obligatorio.',
                'email.required'         => 'El "Correo Electronico" es Obligatorio.',
                'email.email'         => 'El "Correo Electronico" es Invalido.',
                'idestadousuario.required'         => 'El "Estado" es Obligatorio.',
            ];
          
            $this->validate($request,$rules,$messages);
          
           
            if($request->input('clave')!=''){
                DB::table('users')->whereId($id)->update([
                    'nombre'         => $request->input('nombre'),
                    'apellidos'      => $request->input('apellidos'),
                    'identificacion' => $request->input('dni')!=null?$request->input('dni'):'',
                    'email'          => $request->input('email'),
                    'usuario'        => $request->input('email'),
                    'clave'          => $request->input('clave'),
                    'password'       => Hash::make($request->input('clave')),
                    'numerotelefono' => $request->input('numerotelefono'),
                    'idestadousuario' => $request->input('idestadousuario'),
                ]);
            }else{
                DB::table('users')->whereId($id)->update([
                    'nombre'         => $request->input('nombre'),
                    'apellidos'      => $request->input('apellidos'),
                    'identificacion' => $request->input('dni')!=null?$request->input('dni'):'',
                    'email'          => $request->input('email'),
                    'usuario'        => $request->input('email'),
                    'numerotelefono' => $request->input('numerotelefono'),
                    'idestadousuario' => $request->input('idestadousuario'),
                ]);
            }
          
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje'   => 'Se ha actualizado correctamente.'
						]);
          
        }
        elseif($request->input('view')=='confirmar'){
        
          DB::table('users')->whereId($id)->update([
              'email_verified_at' => Carbon::now()
          ]);

          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje' => 'Se ha confirmado correctamente.'
          ]);
        
      }elseif ($request->input('view')=='enviarcorreodemo') { 
            $subject = '';
            $datos = [
              'nombres' => 'GINO GALARZA'
            ];
            Mail::send('layouts/backoffice/usuario/email',['datos' => $datos], function($msj) use($subject){
                $msj->from('ventas@kayllapi.com', 'Kayllapi');
                $msj->to('slipkindo@gmail.com')->subject('Bienvenido DEMO PRUEBA GINO');
            });
        }elseif($request->input('view')=='transferir'){
        
          DB::table('tienda')->whereId($id)->update([
              'idusers' => $request->input('idTransferirTienda'),
          ]);

          return response()->json([
            'resultado' => 'CORRECTO',
            'mensaje' => 'Se ha confirmado correctamente.'
          ]);
        
      }
    }

    public function destroy(Request $request, $id)
    {
        $request->user()->authorizeRoles($request->path());
        if($request->input('view')=='deleteusuario'){
            DB::table('users')->whereId($id)->delete();
            DB::table('role_user')->where('user_id',$id)->delete();
            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha eliminado correctamente.'
            ]);
        }
            
    }
}
