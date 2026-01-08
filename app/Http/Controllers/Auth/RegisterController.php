<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
       return Validator::make($data, [
            'nombre' => ['required', 'string', 'max:255'],
            //'apellidos' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'numerotelefono' => ['required', 'string', 'max:15'],
            'password' => ['required', 'string', 'min:3'], //, 'confirmed'
            //'regis_password_confirmation' => 'required|required_with:passwordcsame:password|string|min:3'

            //'terminos' => ['required'],
        ],[
            'nombre.required' => 'El "Nombre" es Obligatorio.',
            //'apellidos.required' => 'Los "Apellidos" son Obligatorio.',
            'email.required' => 'El "Correo Electrónico" es Obligatorio.',
            'email.email' => 'El "Correo Electrónico" es Invalido.',
            'email.unique' => 'El "Correo Electrónico" ya existe.',
            'numerotelefono.required' => 'El "Número de teléfono" es Obligatorio.',
            'numerotelefono.unique' => 'El "Número de teléfono" ya existe.',
            'password.required' => 'La "Contraseña" es Obligatorio.',
            //'regis_password_confirmation.required' => 'El "Confirmar Contraseña" es Obligatorio.',
       ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'nombre'         => $data['nombre'],
            'apellidos'      => '',
            'identificacion' => '00000000',
            'email'          => $data['email'],
            //'email_verified_at'=> Carbon::now(),
            'usuario'        => $data['email'].'@@@wankaplus.com',
            'clave'          => $data['password'],
            'password'       => Hash::make($data['password']),
            'numerotelefono' => $data['numerotelefono'],
            'direccion'      => '',
            'imagen'         => '',
            'iduserspadre'   => isset($data['idpatrocinador'])?$data['idpatrocinador']:0,
            'idubigeo'       => 0,
            'idtipopersona'  => 1,
            'idtipousuario'  => $data['idtipousuario'], // 3 = usuarios cinema
            'idtienda'       => $data['idtienda'],
            'idestadousuario'=> 1,
            'idestado'       => 1,
        ]);

        if($data['idtipousuario']==3){
            $user->roles()->attach(Role::where('name', 'usercinema')->first());
        }
        //dd($user->id);
        /*if($data['idtienda']==0){
            if(isset($data['idpatrocinador'])){
              
                $idred = DB::table('consumidor_red')->insertGetId([
                    'fecharegistro' => Carbon::now(),
                    'cantidad_totalafiliado' => 0,
                    'iduserspatrocinador' => $data['idpatrocinador'],
                    'iduserspadre' => $data['idpatrocinador'],
                    'idusershijo' => $user->id,
                    'idestadored' => 1
                ]);
                
            }
        }*/
        

        
      
        /*$subject = '';
        $datos = [
          'nombres' => $data['nombre'].' '.$data['apellidos']
        ];
        Mail::send('layouts/backoffice/usuario/email',['datos' => $datos], function($msj) use($subject, $data){
            $msj->from('ventas@kayllapi.com', 'Kayllapi');
            $msj->to($data['email'])->subject('Bienvenido '.$data['nombre'].' '.$data['apellidos']);
        });*/
        return $user;
    }
}
