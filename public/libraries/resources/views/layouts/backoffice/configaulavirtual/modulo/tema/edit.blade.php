@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>EDITAR TEMA</span>
      <a class="btn btn-success" href="{{ url('backoffice/configaulavirtual/'.$cursomodulo->id.'/edit?view=indextema') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" action="javascript:;" 
                                         onsubmit="callback({
                                                    route: 'backoffice/configaulavirtual/{{ $cursomodulotema->id }}',
                                                    method: 'PUT',
                                                    data:{
                                                      'view' : 'cursomodulotemaeditar',
                                                      'idcursomodulo' : '{{ $cursomodulo->id }}'
                                                    }
                                                },
                                                function(resultado){
                                                  if (resultado.resultado == 'CORRECTO') {
                                                    location.href = '{{ url('backoffice/configaulavirtual/'.$cursomodulo->id.'/edit?view=indextema') }}';                                                                            
                                                  }
                                                },this)">
  <div class="profile-edit-container">
    <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
              <div class="col-md-6">
                <label>Nombre *</label>
                <input type="text" value="{{ $cursomodulotema->nombre }}" id="nombre">
                <!--label>Descripción</label>
                <textarea id="descripcion">{{ $cursomodulotema->descripcion }}</textarea-->
                <label>URL de video (Youtube / Vimeo) *</label>
                <input type="text" value="{{ $cursomodulotema->urlvideo }}" id="urlvideo" placeholder="https://">
              </div>
              <div class="col-md-6">
                <!--label>Imagen</label>
                <div class="fuzone" id="cont-fileupload" style="height: 197px;">
                      <div class="fu-text"><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</div>
                      <input type="file" class="upload" id="imagen">
                      <div id="resultado-logo"></div>
                  </div-->
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
$("#idestado").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).val({{$cursomodulotema->idestado}}).trigger("change");

uploadfile({
  input: "#imagen",
  cont: "#cont-fileupload",
  result: "#resultado-logo",
  ruta: "{{ url('public/backoffice/sistema/sitioweb/aulavirtual/') }}",
  image: "{{ $cursomodulotema->imagen }}"
});
</script>
@endsection