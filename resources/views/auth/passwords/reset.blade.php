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
        <h3 style="text-align: center;"><span>{{ __('Restablecer') }} <strong>{{ __('Contraseña') }}</strong></span></h3>
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
        <div id="tabs-container" style="margin-top: 0px;">
          <div class="tab">
              <div id="tab-1" class="tab-content">
                  <div class="custom-form">
                      <form method="POST" action="{{ route('password.update') }}">
                           <input type="hidden" name="token" value="{{ $token }}">
                          @csrf
                          <label for="email">{{ __('Correo Electronico') }}  </label>
                          <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                          @error('email')
                            <span class="mensaje-error" role="alert">
                               {{ $message }}
                            </span>
                          @enderror
                        
                          <label for="password">{{ __('Contraseña Nueva') }}  </label>
                          <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                           @error('password')
                              <span class="mensaje-error" role="alert">
                                 {{ $message }}
                              </span>
                          @enderror
                        
                          <label for="password-confirm">{{ __('Confirmar Contraseña Nueva') }}  </label>
                          <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                        
                          <button type="submit"  class="log-submit-btn"><span> {{ __('Restablecer Contraseña') }}</span></button>
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
