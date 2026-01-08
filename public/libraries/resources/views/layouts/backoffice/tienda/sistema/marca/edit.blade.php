@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Marca</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/marca') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/marca/{{ $s_marca->id }}',
        method: 'PUT',
        data:{
            view: 'editar'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/marca') }}';                                                                            
    },this)">
    <div class="profile-edit-container">
      <div class="custom-form">
        <div class="row">
          <div class="col-sm-6">
                <label>Nombre *</label>
                <input type="text" id="nombre" value="{{$s_marca->nombre}}" onkeyup="texto_mayucula(this)"/>
                <label>Imagen</label>
                <div class="fuzone" id="cont-fileupload" style="height:120px">
                    <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                    <input type="file" class="upload" id="imagen">
                    <div id="resultado-logo"></div>
                </div>
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
@section('subscripts')
<script>
uploadfile({
  input:"#imagen",
  cont:"#cont-fileupload",
  result:"#resultado-logo",
  ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/') }}",
  image: "{{ $s_marca->imagen }}"
});
</script>
@endsection