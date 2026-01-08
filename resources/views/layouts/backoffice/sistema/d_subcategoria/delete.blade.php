@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Eliminar Categoría</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/subcategoria') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/subcategoria/{{ $s_categoria->id }}',
        method: 'DELETE',
        data:{
            view: 'eliminar'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/subcategoria') }}';                                                                            
    },this)">
 <div class="row">
      <div class="col-sm-6">

          <label>Categoria *</label>
          <select id="idcategoria" disabled>
              <option></option>
              <option value="{{ $s_categoria->id }}">{{ $s_categoria->categorianombre }}</option>
          </select>
            <label>Nombre *</label>
            <input type="text" id="nombre" value="{{$s_categoria->nombre}}" disabled/>
            <label>Estado *</label>
                            <select id="idestado" disabled>
                                <option></option>
                                <option value="1">Activado</option>
                                <option value="2">Desactivado</option>
             </select>
      </div>
      <div class="col-sm-6">
            <label>Imagen</label>              
            <div class="fuzone" id="cont-fileupload" style="height:120px">
                <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                <input type="file" class="upload" id="imagen">
                <div id="resultado-fileupload"></div>
            </div>
      </div>
    </div>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de eliminar?</b>
    </div>
    <button type="submit" class="btn mx-btn-post">Eliminar</button>
</form>                  
@endsection
@section('subscripts')
<script>
    $("#idcategoria").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    }).val({{ $s_categoria->id }}).trigger("change");
    uploadfile({
      input:"#imagen",
      cont:"#cont-fileupload",
      result:"#resultado-fileupload",
      ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/') }}",
      image: "{{ $s_categoria->imagen }}"
    });
    $("#idestado").select2({
      placeholder: "---  Seleccionar ---",
      minimumResultsForSearch: -1
  }).val({{$s_categoria->idestado}}).trigger("change");
    
</script>
@endsection