@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>EDITAR USUARIO</span>
    <a class="btn btn-success" href="{{ url('backoffice/cine') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
  </div>
</div>
<form class="js-validation-signin px-30" action="javascript:;" style="padding: 0 10px;"
      onsubmit="callback({
                            route:  'backoffice/cine/{{ $usuario->id }}',
                            method: 'PUT',
                            data:   {
                              view : 'entretenimiento_editarperfil'
                            }
                          },
                          function(resultado){
                            location.href = '{{ url('backoffice/cine') }}';                                                   
                          },this)" enctype="multipart/form-data">
  <div class="profile-edit-container">
    <div class="custom-form">
      <div class="row">
        <div class="col-md-6">
          <label>Nombre *</label>
          <input type="text" id="nombre" value="{{ $usuario->nombre }}"/>
          <label>Apellidos</label>
          <input type="text" id="apellidos" value="{{ $usuario->apellidos }}"/>
          <label>Número de Teléfono *</label>
          <input type="text" id="numerotelefono" value="{{ $usuario->numerotelefono }}"/>
        </div>
        <div class="col-md-6">
          <label>Correo Electrónico (Usuario)*</label>
          <input type="text" id="email" value="{{ $usuario->email }}"/>
          <label>Código de Invitación</label>
          <input type="text" id="codigoinvitacion" value="https://kayllapi.com/pagina/cine/create?u={{ $usuario->email }}" disabled/>
        </div>
      </div>
    </div>
  </div>
  <div class="profile-edit-container">
    <div class="custom-form">
      <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</button>
    </div>
  </div>
</form>
@endsection
@section('scriptsbackoffice')
@endsection