<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    const MENSAJE_DESHABILITADO = 'Usuario deshabilitado, comuníquese con el Administrador.';

    /**
     * Where to redirect users after login.
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
        //$this->middleware('guest')->except('logout');
    }
  
    public function username()
    {
        return 'usuario';
    }
  
    protected function validateLogin($request)
    {
            $messages = [
                  $this->username().'.required' => 'El campo "Usuario" es Obligatorio.',
                  'password.required' => 'El campo "Contraseña" es Obligatorio.',
            ];

            $this->validate($request, [
                $this->username() => 'required',
                'password' => 'required',
            ],$messages);
    }
  
    protected function credentials($request)
    {
        return [
            'usuario' => $request->{$this->username()}, 
            'password' => $request->password, 
            'idestadousuario' => 1,
        ];
    }
    
    protected function authenticated(Request $request, $user)
    {
        DB::table('users')->whereId($user->id)->update(['intentos_fallidos' => 0]);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        $usuario = DB::table('users')
            ->where('usuario', $request->{$this->username()})
            ->where('idestadousuario', 1)
            ->first();

        // intentos_maximo = 0 significa sin límite
        if ($usuario && $usuario->intentos_maximo > 0) {
            DB::table('users')->whereId($usuario->id)->increment('intentos_fallidos');
            DB::table('users')->whereId($usuario->id)
                ->whereColumn('intentos_fallidos', '>=', 'intentos_maximo')
                ->update(['idestadousuario' => 2]);

            $restantes = $usuario->intentos_maximo - DB::table('users')->whereId($usuario->id)->value('intentos_fallidos');

            throw ValidationException::withMessages([
                $this->username() => $restantes > 0
                    ? 'Usuario o contraseña incorrectos. Le quedan '.$restantes.' intento(s).'
                    : self::MENSAJE_DESHABILITADO,
            ]);
        }

        $deshabilitado = !$usuario && DB::table('users')
            ->where('usuario', $request->{$this->username()})
            ->where('idestadousuario', 2)
            ->exists();

        if ($deshabilitado) {
            throw ValidationException::withMessages([
                $this->username() => self::MENSAJE_DESHABILITADO,
            ]);
        }

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        return redirect('/login');
    }
}
