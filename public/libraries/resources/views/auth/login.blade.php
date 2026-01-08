@extends('layouts.master')
@section('cuerpo')
<div class="container">
  <div class="row">
      <div class="col-md-4">
      </div>
      <div class="col-md-4">
            <h1 style="font-size:20px;padding-top:50px;padding-bottom:20px;">Inicie Sesión a tu Back Office</h1>
              <div class="custom-form">
                                        <div id="mx-carga-1">
                                        <form action="javascript:;" 
                                            autocomplete="off"
                                            onsubmit="callback({
                                                route: 'login',
                                                method: 'POST',
                                                carga: '#mx-carga-1',
                                                data:{
                                                    idtienda: 0,
                                                    idtipousuario: 1
                                                }
                                            },
                                            function(resultado){
                                                if(resultado.resultado=='ERRORCONFIRMEMAIL'){
                                                    location.href = '{{ url('/email/verify') }}';
                                                }else{
                                                    location.href = '{{ url()->current() }}'; 
                                                    //location.href = '{{ url('/backoffice/inicio') }}';   
                                                }                                            
                                            },this)">
                                            <label>Usuario ó Correo Electrónico * </label>
                                            <input id="usuario" type="text">
                                            <label >Contraseña * </label>
                                            <input id="password" type="password">
                                            <button type="submit"  class="log-submit-btn"><span>Iniciar sesión</span></button>
                                            <!--div class="clearfix"></div>
                                            <div class="filter-tags">
                                                <input id="remember" type="checkbox">
                                                <label for="remember">Recuérdame</label>
                                            </div-->
                                        </form>
                                        <div class="lost_password">
                                            <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                                        </div>
                                        </div>
                                    </div>
      </div>
  </div>
</div>
<div class="hero-static col-md-6 col-xl-8 d-none d-md-flex align-items-md-end">
    <div class="p-30 invisible" data-toggle="appear">
        <p class="font-size-h3 font-w600 text-white">
            Bienvenido <a class="link-effect text-white-op font-w700" href="javascript:void(0)">Ingresa ya</a>!!
        </p>
        <p class="font-italic text-white-op">
            Copyright &copy; <span class="js-year-copy">2019</span>
        </p>
    </div>
</div>

@endsection
@section('scripts')
@show