@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Perfil</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio') }}"><i class="fa fa-angle-left"></i> Ir a Inicio</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{$tienda->id}}/inicio/{{ $usuario->id }}',
        method: 'PUT'
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/1/edit?view=editperfil') }}';              
    },this)">
    <input type="hidden" value="editperfil" id="view"/>
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                  <label>Indentificación (DNI)</label>
                  <input type="text" value="{{ $usuario->identificacion }}" id="identificacion"/>
                </div>
                <div class="col-md-12">
                  <label>Nombre *</label>
                  <input type="text" value="{{ $usuario->nombre }}" id="nombre"/>
                </div>
                <div class="col-md-12">
                  <label>Apellidos *</label>
                  <input type="text" value="{{ $usuario->apellidos }}" id="apellidos"/>
                </div>
                <div class="col-md-12">
                  <label>Número de Teléfono</label>
                  <input type="text" value="{{ $usuario->numerotelefono }}" id="numerotelefono"/>
                </div>
                <div class="col-md-12">
                  <label>Correo Electrónico</label>
                  <input type="text" value="{{ $usuario->email }}" id="email">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <div class="col-md-12">
                <label>Logo (300x300)</label>
                <div class="fuzone" id="cont-fileupload-logo" style="height: 194px;">
                  <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                    <input type="file" class="upload" id="imagen">
                    <div id="resultado-logo"></div>
                </div>
                </div>
                <div class="col-md-12">
                  <label>Ubicación (Ubigeo)</label>
                  <select id="idubigeo" >
                      <option></option>
                      @foreach($ubigeos as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                      @endforeach
                  </select>
                </div>
                <div class="col-md-12">
                  <label>Dirección</label>
                  <input type="text" value="{{ $usuario->direccion }}" id="direccion"/>
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
@section('subscripts')
<script>
$("#idubigeo").select2({
    placeholder: "---  Seleccionar ---"
}).val({{ $usuario->idubigeo==0 ? $value->id==1026 : $usuario->idubigeo }}).trigger("change");

uploadfile({
  input: "#imagen",
  cont: "#cont-fileupload-logo",
  result: "#resultado-logo",
  ruta: "{{ url('/public/backoffice/tienda/'.$tienda->id.'/sistema/') }}",
  image: "{{ $usuario->imagen }}"
});
</script>
@endsection