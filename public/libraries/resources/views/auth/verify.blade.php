@extends('layouts.master')
@section('cuerpo')
 <section class="parallax-section" data-scrollax-parent="true">
                        <div class="bg"  data-bg="{{ url('public/backoffice/sistema/confirm-mail.png') }}" data-scrollax="properties: { translateY: '100px' }"></div>
                        <div class="overlay co lor-overlay"></div>
                        <div class="container">
                            <div class="intro-item fl-wrap">
                                <h2>{{ __('Verifique su dirección de correo electrónico') }}</h2><br>
                                @if (session('resent'))
                                    <div class="alert alert-success" role="alert" style="border-radius: 10px;padding-top: 20px;padding-bottom: 20px;margin-bottom: 20px;color: #fff;font-weight: bold;font-size: 15px;">
                                        {{ __('Se ha enviado un nuevo enlace de verificación a su dirección de correo electrónico.') }}
                                    </div>
                                @endif
                                <h3>Antes de continuar, revise su correo electrónico <span class="badge badge-pill badge-success"><i class="fas fa-sync-alt"></i> {{Auth::user()->email}}</span>, para obtener un enlace de verificación.</h3><br>
                                <h3>Si no recibiste el correo electrónico.</h3>
                                <a class="trs-btn" href="{{ route('verification.resend') }}">{{ __('Haga clic aquí para solicitar otro') }}</a>
                            </div>
                        </div>
                    </section>

@endsection
