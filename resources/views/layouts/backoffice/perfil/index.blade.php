@extends('layouts.backoffice.master')
@section('cuerpobackoffice')  
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>EDITAR PERFIL</span>
      <a class="btn btn-success" href="{{ url('backoffice/inicio') }}"><i class="fa fa-angle-left"></i> Ir a Inicio</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/perfil/{{ $usuario->id }}',
        method: 'PUT'
    },
    function(resultado){
        location.href = '{{ url('backoffice/perfil') }}';              
    },this)">
    <input type="hidden" value="editperfil" id="view"/>
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <label>Nombre *</label>
                  <input type="text" value="{{ $usuario->nombre }}" id="nombre"/>
                </div>
                <div class="col-md-12">
                  <label>Apellidos *</label>
                  <input type="text" value="{{ $usuario->apellidos }}" id="apellidos"/>
                </div>
                <div class="col-md-12">
                  <label>Indentificación (DNI)</label>
                  <input type="text" value="{{ $usuario->identificacion }}" id="identificacion"/>
                </div>
                <div class="col-md-12">
                  <label>Número de Teléfono</label>
                  <input type="text" value="{{ $usuario->numerotelefono }}" id="numerotelefono"/>
                </div>
                <div class="col-md-12">
                  <label>Correo Electrónico *</label>
                  <input type="text" value="{{ $usuario->email }}" id="email" readonly>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                <label>Logo (300x300)</label>
                <div class="fuzone" id="cont-fileupload-logo">
                  <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                    <input type="file" class="upload" id="imagen">
                    <div id="resultado-logo"></div>
                </div>
                </div>
                <div class="col-md-12">
                  <label>Ubicación (Departamento/Provincia/Distrito) *</label>
                  <select id="idubigeo" >
                      <option></option>
                      @foreach($ubigeos as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                      @endforeach
                  </select>
                </div>
                <div class="col-md-12">
                  <label>Dirección *</label>
                  <input type="text" value="{{ $usuario->direccion }}" id="direccion"/>
                </div>
                <div class="col-md-12">
                  <label>Link para compartir</label>
                  <input type="text" value="{{ url('/register?user='.$usuario->usuario) }}" readonly/>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios
            </button>
        </div>
    </div>  
</form>
@endsection
@section('scriptsbackoffice')
<script>
$("#idubigeo").select2({
    placeholder: "---  Seleccionar ---"
}).val({{ $usuario->idubigeo==0 ? ($value->id==1026?$value->id:'null') : $usuario->idubigeo }}).trigger("change");

<?php $rutaimagen = getcwd().'/public/backoffice/usuario/'.$usuario->id.'/perfil/'.$usuario->imagen; ?>
uploadfile({
  input:"#imagen",
  cont:"#cont-fileupload-logo",
  result:"#resultado-logo",
  @if(file_exists($rutaimagen) && $usuario->imagen!='')
  ruta: "{{ url('public/backoffice/usuario/'.$usuario->id.'/perfil/') }}",
  image: "{{ $usuario->imagen }}"
  @endif
});
</script>
@endsection