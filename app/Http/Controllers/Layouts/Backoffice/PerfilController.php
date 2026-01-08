<?php

namespace App\Http\Controllers\Layouts\Backoffice;

use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class PerfilController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
  
    public function index(Request $request)
    {
        $ubigeos = DB::table('ubigeo')->get();
        $usuario = DB::table('users')->whereId(Auth::user()->id)->first();
        return view('layouts/backoffice/perfil/index',[
          'ubigeos' => $ubigeos,
          'usuario' => $usuario
        ]);
    }

    public function create(Request $request)
    {
        //
    }

    public function store(Request $request)
    {
        if($request->input('view')=='monedaskay_registrar'){
            $rules = [
                'mendakay-cantidad' => 'required',
                'mendakay-monto' => 'required',
            ];
            $messages = [
                'mendakay-cantidad.required' => 'La "Cantidad" es Obligatorio.',
                'mendakay-monto.required' => 'El "Monto" es Obligatorio.',
            ];
            $this->validate($request,$rules,$messages);
          
            $imagen = '';
            if($request->file('mendakayimagen')!='') {
                $imagen = uploadfile('','',$request->file('mendakayimagen'),'/public/backoffice/consumidor/voucher/');
            }else{
                return response()->json([
                  'resultado' => 'ERROR',
                  'mensaje' => 'Debe Subir un Voucher ó Comprobante.'
                ]);
            }

            DB::table('consumidor_puntoskay')->insert([
                'fecharegistro' => Carbon::now(),
                'cantidad' => $request->input('mendakay-cantidad'),
                'precio' => 1,
                'total' => $request->input('mendakay-monto'),
                'voucher' => $imagen,
                'token' => '',
                'idmotivopuntoskay' => $request->input('idmotivopuntoskay'),
                'idusers' => Auth::user()->id,
                'idusersresponsable' => 0,
                'idestadosolicitud' => 1,
                'idestadopuntoskay' => 1,
                'idestado' => 1
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha registrado correctamente.'
            ]);
        }
        elseif($request->input('view')=='monedaskay_registrarculqui'){
          
            DB::table('consumidor_puntoskay')->insert([
                'fecharegistro' => Carbon::now(),
                'cantidad' => $request->input('mendakay_cantidad'),
                'precio' => 1,
                'total' => $request->input('mendakay_monto'),
                'voucher' => '',
                'token' => $request->input('token'),
                'idmotivopuntoskay' => $request->input('idmotivopuntoskay'),
                'idusers' => Auth::user()->id,
                'idusersresponsable' => 0,
                'idestadosolicitud' => 1,
                'idestadopuntoskay' => 1,
                'idestado' => 1
            ]);

            return response()->json([
                'resultado' => 'CORRECTO',
                'mensaje' => 'Se ha registrado correctamente.'
            ]);
        }
    }

    public function show(Request $request, $id)
    {
        //
    }

    public function edit(Request $request, $id)
    {
        if($request->input('view')=='editcambiarclave'){
            $ubigeos = DB::table('ubigeo')->get();
            $usuario = DB::table('users')->whereId(Auth::user()->id)->first();
            return view('layouts/backoffice/perfil/editpassword',[
              'ubigeos' => $ubigeos,
              'usuario' => $usuario
            ]);
        }elseif($request->input('view')=='editmetodopago'){
            $ubigeos = DB::table('ubigeo')->get();
            $usuario = DB::table('users')
                ->join('usersmetodopago','usersmetodopago.idusers','users.id')
                ->where('users.id',Auth::user()->id)
                ->select(
                    'usersmetodopago.*'
                )
                ->first();
            $bancos = DB::table('banco')->get();
            return view('layouts/backoffice/perfil/editmetodopago',[
              'ubigeos' => $ubigeos,
              'usuario' => $usuario,
              'bancos' => $bancos
            ]);  
        }elseif($request->input('view')=='monedaskay'){
            $puntoskays = DB::table('consumidor_puntoskay')
                ->join('users','users.id','=','consumidor_puntoskay.idusers')
                ->where('consumidor_puntoskay.idusers',Auth::user()->id)
                ->select(
                    'consumidor_puntoskay.*',
                    'users.nombre as usersnombre',
                    'users.apellidos as usersapellidos',
                    'users.email as usersemail'
                )
                ->orderBy('consumidor_puntoskay.id','desc')
                ->paginate(10);
            return view('layouts/backoffice/perfil/monedaskay',[
                'puntoskays' => $puntoskays,
            ]);  
        }elseif($request->input('view')=='monedaskay_registrar'){

            return view('layouts/backoffice/perfil/monedaskay_registrar',[
              
            ]);  
        }
            
    }

    public function update(Request $request, $id)
    {
        if($request->input('view')=='editperfil') {
            $rules = [
								'nombre' => 'required',
								'apellidos' => 'required',
								'email' => 'required',
								'idubigeo' => 'required',
								'direccion' => 'required',
						];
						$messages = [
								'nombre.required' => 'El "Nombre" es Obligatorio.',
								'apellidos.required' => 'Los "Apellidos" es Obligatorio.',
								'email.required' => 'El "Correo Electrónico" es Obligatorio.',
								'idubigeo.required' => 'El "Ubicación (Departamento/Provincia/Distrito)" es Obligatorio.',
								'direccion.required' => 'La "Dirección" es Obligatorio.',
						];
						$this->validate($request,$rules,$messages);
          
            if($request->input('imagenant')!='') {
              $imagen = $request->input('imagenant');
            }else{
              $usuario = DB::table('users')->whereId($id)->first();
              $rutaimagen = getcwd().'/public/backoffice/usuario/'.$id.'/perfil/'.$usuario->imagen;
              if(file_exists($rutaimagen) && $usuario->imagen!='') {
                  unlink($rutaimagen);
              }
              $imagen = '';
              if($request->file('imagen')!='') {
                if ($request->file('imagen')->isValid()) {                  
                    list($nombre,$ext) = explode(".", $request->file('imagen')->getClientOriginalName());
                    $imagen = Carbon::now()->format('dmYhms').rand(100000, 999999).'.'.$ext;
                    $request->file('imagen')->move(getcwd().'/public/backoffice/usuario/'.$id.'/perfil/', $imagen);
                }
              }
            }

            DB::table('users')->whereId($id)->update([
								'nombre' => $request->input('nombre'),
                'apellidos' => $request->input('apellidos'),
                'identificacion' => $request->input('identificacion')!=null?$request->input('identificacion'):'',
                'email' => $request->input('email'),
                'numerotelefono' => $request->input('numerotelefono')!=null?$request->input('numerotelefono'):'',
                'direccion' => $request->input('direccion')!=null?$request->input('direccion'):'',
                'imagen' => $imagen,
                'idubigeo' => $request->input('idubigeo')
						]);
          
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje' => 'Se ha registrado correctamente.'
						]);
        }elseif($request->input('view')=='editpassword') {
            $rules = [
								'antpassword' => 'required',
								'password' => 'required|string|min:3|confirmed',
								'password_confirmation' => 'required|required_with:passwordcsame:password|string|min:3',
						];
						$messages = [
								'antpassword.required' => 'La "Contraseña Actual" es Obligatorio.',
								'password.required' => 'La "Nueva Contraseña" es Obligatorio.',
								'password_confirmation.required' => 'El "Confirmar Nueva Contraseña" es Obligatorio.',
						];
						$this->validate($request,$rules,$messages);
          
            $user = DB::table('users')->whereId(Auth::user()->id)->where('clave',$request->input('antpassword'))->first();
            if($user==''){
                return response()->json([
                    'resultado' => 'ERROR',
                    'mensaje' => 'la "Contraseña Actual" no es correcta.'
                ]);
            }

            DB::table('users')->whereId(Auth::user()->id)->update([
								'clave' => $request->input('password'),
                'password' => Hash::make($request->input('password')),
						]);
          
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje' => 'Se ha registrado correctamente.'
						]);
        }elseif($request->input('view')=='editmetodopago'){
 
            $usuario = DB::table('users')
                ->join('usersmetodopago','usersmetodopago.idusers','users.id')
                ->where('users.id',Auth::user()->id)
                ->select(
                    'usersmetodopago.*'
                )
                ->first();
          
            if($usuario!=''){
                DB::table('usersmetodopago')->whereId($usuario->id)->update([
                    'numerocuenta' => $request->input('numerocuenta')!=''?$request->input('numerocuenta'):'',
                    'numerocuentainterbancario' => $request->input('numerocuentainterbancario')!=''?$request->input('numerocuentainterbancario'):'',
                    'idbanco' => $request->input('idbanco')!=null?$request->input('idbanco'):0,
                ]);
            }else{
                DB::table('usersmetodopago')->insert([
                    'numerocuenta' => $request->input('numerocuenta')!=''?$request->input('numerocuenta'):'',
                    'numerocuentainterbancario' => $request->input('numerocuentainterbancario')!=''?$request->input('numerocuentainterbancario'):'',
                    'idbanco' => $request->input('idbanco')!=null?$request->input('idbanco'):0,
                    'idusers' => Auth::user()->id,
                ]);
            }
            return response()->json([
								'resultado' => 'CORRECTO',
								'mensaje' => 'Se ha registrado correctamente.'
						]);
        }
    }

    public function destroy(Request $request, $id)
    {
        //
    }
}
