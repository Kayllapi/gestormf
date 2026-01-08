@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Habilitar Vaucher</span>
    <a class="btn btn-success" href="{{ url('backoffice/cine/1/edit?view=entretenimiento_usuario') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
  </div>
</div>
<form class="js-validation-signin px-30" action="javascript:;" style="padding: 0 10px;"
      onsubmit="callback({
                            route:  'backoffice/cine/{{ $usuario->id }}',
                            method: 'PUT',
                            data:   {
                              view : 'entretenimiento_habilitarvaucher'
                            }
                          },
                          function(resultado){
                            location.href = '{{ url('backoffice/cine/1/edit?view=entretenimiento_usuario') }}';
                          },this)" enctype="multipart/form-data">
  <div class="mensaje-warning">
    <i class="fa fa-warning"></i> ¡Esta seguro de habilitar el vaucher!
  </div>
  <div class="profile-edit-container">
    <div class="custom-form">
      <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;">Habilitar</button>
    </div>
  </div>
</form>
@endsection
@section('scriptsbackoffice')
@endsection