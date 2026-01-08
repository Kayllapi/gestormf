@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Usuario</span>
      <a class="btn btn-success" href="{{ url('backoffice/usuario') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
          route: 'backoffice/usuario/{{ $usuario->id }}',
          method: 'PUT',
          data: {
              view : 'editarusuario'
          }
      },
      function(resultado){
          location.href = '{{ url('backoffice/usuario') }}';                                                   
      },this)" enctype="multipart/form-data">
    <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
              <div class="col-md-6">
                  <label>DNI (8 Digitos) *</label>
                  <input type="text" id="dni" value="{{ $usuario->identificacion }}"/>
                  <label>Nombre *</label>
                  <input type="text" id="nombre" value="{{ $usuario->nombre }}"/>
                  <label>Apellidos *</label>
                  <input type="text" id="apellidos" value="{{ $usuario->apellidos }}"/>
                  <label>Número de Teléfono *</label>
                  <input type="text" id="numerotelefono" value="{{ $usuario->numerotelefono }}"/>
              </div>
              <div class="col-md-6">
                  <label>Correo Electrónico (Usuario) *</label>
                  <input type="text" id="email" value="{{ $usuario->email }}"/>
                  <label>Contraseña Anterior</label>
                  <input type="text" value="{{ $usuario->clave }}" disabled/>
                  <label>Cambiar Contraseña</label>
                  <input type="text" id="clave"/>
                  <label>Estado de Acceso *</label>
                  <select class="form-control" id="idestadousuario">
                    <option value="1">Activado</option>
                    <option value="2">Desactivado</option>
                  </select>
              </div>
          </div>
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;">Guardar Cambios</button>
        </div>
    </div>
</form>
@endsection
@section('scriptsbackoffice')
<script>
$("#idestadousuario").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$usuario->idestadousuario}}).trigger("change");
</script>
@endsection