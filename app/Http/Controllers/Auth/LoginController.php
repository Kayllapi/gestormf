<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    
    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        return redirect('/login');
    }
}
