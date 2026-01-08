@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>EDITAR CURSO</span>
      <a class="btn btn-success" href="{{ url('backoffice/configaulavirtual') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
          route: 'backoffice/configaulavirtual/{{ $curso->id }}',
          method: 'PUT',
          data:{
            'view' : 'cursoeditar'
          }
      },
      function(resultado){
        if (resultado.resultado == 'CORRECTO') {
          location.href = '{{ url('backoffice/configaulavirtual') }}';
        }
      },this)">
  <div class="profile-edit-container">
    <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
              <div class="col-md-6">
                <label>Categoria *</label>
                <select id="idcategoria" >
                    <option></option>
                    @foreach($categorias as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
                <label>Nombre *</label>
                <input type="text" value="{{ $curso->nombre }}" id="nombrecurso">
                <label>Descripción</label>
                <textarea id="descripcion">{{ $curso->descripcion }}</textarea>
                <label>Nombre del Autor *</label>
                <input type="text" value="{{ $curso->autor }}" id="nombreautor">
              </div>
              <div class="col-md-6">
                <label>Imagen</label>
                <div class="fuzone" id="cont-fileupload" style="height: 197px;">
                      <div class="fu-text"><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</div>
                      <input type="file" class="upload" id="imagen">
                      <div id="resultado-logo"></div>
                  </div>
                <label>Idioma *</label>
                <select id="ididioma">
                    <option value="1">Español</option>
                    <option value="2">Ingles</option>
                </select>
                <label>Estado *</label>
                <select id="idestado">
                    <option value="1">Activado</option>
                    <option value="2">Desactivado</option>
                </select>
              </div>
            </div>
          </div>
      </div>
    <div class="custom-form">
        <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</button>
    </div>
  </div>
  
</form>
@endsection
@section('scriptsbackoffice')
<script>
$("#idcategoria").select2({
    placeholder: "---  Seleccionar ---"
}).val({{$curso->idcategoria}}).trigger("change");

$("#ididioma").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$curso->ididioma}}).trigger("change");

$("#idestado").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$curso->idestado}}).trigger("change");

uploadfile({
  input: "#imagen",
  cont: "#cont-fileupload",
  result: "#resultado-logo",
  ruta: "{{ url('public/backoffice/usuario/'.$curso->idusers.'/aulavirtual/') }}",
  image: "{{ $curso->imagen }}"
});
</script>
@endsection