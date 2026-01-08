@extends('layouts.master')

@section('cuerpo')
<style>
  .alerta-email {
    color: #fff;
    height: 3.5em;
    background-color: #00d1b2;
    font-size: 1.3em;
    font-weight: 600;
  }
  
  .mensaje-error {
    color: red;
    text-transform: uppercase;
    text-align: left;
    display: block;
    font-weight: 500;
  }
</style>
      <div class="main-register fl-wrap">
<div class="container">
      @if (session('status'))
        <div class="mensaje-success" style="margin-top: 10px;">
            {{ session('status') }}
        </div>
      @endif
          <h3 style="text-align: center;"><span>Recuperar <strong>contraseña</strong></span></h3>
          <p><b>¿Olvidó la contraseña de su cuenta o tiene problemas para iniciar sesión en su equipo?</b> <br>Ingrese su dirección de correo electrónico y le enviaremos un enlace de recuperación.</p>
      <div class="col-md-4">
        </div>
      <div class="col-md-4">
          <div id="tabs-container" style="margin-top: 0px;">
              
              <div class="tab">
                  <div id="tab-1" class="tab-content">
                      <div class="custom-form">
                          <form method="POST" action="{{ route('password.email') }}">
                              @csrf
                              <label for="email">{{ __('Correo Electrónico *') }}  </label>
                              <input id="email" type="text" name="email" value="{{ old('email') }}" autocomplete="email" autofocus>
                               @error('email')
                                    <span class="mensaje-error" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                              <button type="submit"  class="log-submit-btn"><span>{{ __('Enviar a Correo Electrónico') }}</span></button>
                              <div class="clearfix"></div>
                          </form>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>
@endsection
@section('scripts')
<style>
.main-register-holder {
    margin: 50px auto 50px;
}
</style>
@endsection