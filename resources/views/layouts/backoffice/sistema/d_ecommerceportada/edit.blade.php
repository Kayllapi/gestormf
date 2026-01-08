@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Portada</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/ecommerceportada') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/ecommerceportada/{{$s_ecommerceportada->id}}',
        method: 'PUT',
        data:{
            view: 'editar',
            idtienda: '{{ $tienda->id }}'
        }
    },
    function(resultado){
       location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/ecommerceportada') }}';                                                                            
    },this)">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-sm-6">
                <label>Enlazar Producto (Opcional)</label>
                <select id="idproducto">
                    <option value="{{$s_ecommerceportada->s_idproducto!=0?$s_ecommerceportada->s_idproducto:''}}" >{{$s_ecommerceportada->s_idproducto!=0?$s_ecommerceportada->productonombre:''}}</option>
                </select>
                <label>Título *</label>
                <input type="text" value="{{$s_ecommerceportada->titulo}}" id="titulo"/>
                <label>Descripción *</label>
                <input type="text" value="{{$s_ecommerceportada->descripcion}}" id="descripcion"/>
            </div>
            <div class="col-sm-6">
                <label>Imagen (350x910)</label>
                <div class="fuzone" id="cont-fileupload" style="height:177px">
                    <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                    <input type="file" class="upload" id="imagen">
                    <div id="resultado-fileupload"></div>
                </div>
                <label>Estado de portada *</label>
                <select id="idestado">
                    <option></option>
                    <option value="1">Activado</option>
                    <option value="2">Desactivado</option>
                </select>
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
  result:"#resultado-fileupload",
  ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/ecommerceportada/') }}",
  image: "{{ $s_ecommerceportada->imagen }}"
});
$("#idestado").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$s_ecommerceportada->s_idestado}}).trigger("change");
$("#idproducto").select2({
    @include('app.select2_producto',[
        'idtienda'=>$tienda->id
    ])
}).on("change", function(e) {
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/ecommerceportada/showseleccionarproducto')}}",
        type:'GET',
        data: {
            idproducto : e.currentTarget.value
        },
        success: function (respuesta){
          /*if(respuesta["producto"]!=null){
              $('#titulo').val(respuesta["producto"].titulo);
              $('#nombre').val(respuesta["producto"].nombre);
              $('#descripcion').val(respuesta["producto"].descripcion);
          }*/
        }
    })
});
</script>
@endsection