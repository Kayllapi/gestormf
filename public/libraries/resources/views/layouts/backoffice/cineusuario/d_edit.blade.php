@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>EDITAR USUARIO</span>
      <a class="btn btn-success" href="{{ url('backoffice/cineusuario') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
          route: 'backoffice/cineusuario/{{ $usuario->id }}',
          method: 'PUT',
          data: {
              view : 'editar'
          }
      },
      function(resultado){
          location.href = '{{ url('backoffice/cineusuario') }}';                                                   
      },this)" enctype="multipart/form-data">
    <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
              <div class="col-md-6">
                  <label>Nombre *</label>
                  <input type="text" id="nombre" value="{{ $usuario->nombre }}"/>
                  <label>Estado *</label>
                  <select class="form-control" id="idestado">
                    <option value="1">Activado</option>
                    <option value="2">Desactivado</option>
                  </select>
              </div>
              <div class="col-md-6">
                  <?php $listuser = explode('@',$usuario->usuario) ?>
                  <label>Usuario *</label>
                  <input type="text" id="usuario" value="{{ $listuser[0] }}"/>
                  <label>Cambiar Contraseña</label>
                  <input type="text" id="password"/>
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
<script>
$("#idestado").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$usuario->idestado}}).trigger("change");
</script>
@endsection