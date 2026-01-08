@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte de Productos</span>
    </div>
    <div class="row">
       <div class="col-md-6">
          <label>Producto</label>
          <select id="idproducto">
              <option></option>
          </select> 
          <label>Categoría</label>
          <select id="idcategoria">
             <option></option>
             @foreach($categoria as $value)
               <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                  <?php
                    $subcategorias = DB::table('s_categoria')
                        ->where('s_categoria.s_idcategoria',$value->id)
                        ->orderBy('s_categoria.nombre','asc')
                        ->get();
                  ?>
                  @foreach($subcategorias as $subvalue)
                      <option value="{{$subvalue->id}}">{{ $value->nombre }} / {{ $subvalue->nombre }}</option>
                 @endforeach
               @endforeach
          </select>
       </div>
       <div class="col-md-6">
          <label>Marca</label>
          <select id="idmarca">
             <option></option>
            @foreach($marca as $value)
             <option value="{{ $value->id }}">{{ $value->nombre }}</option>
            @endforeach
          </select>
          <label>Estado</label>
          <select id="idestado" name="idestado">
               <option></option>
               <option value="1">Activado</option>
               <option value="2">Desactivado</option>
          </select>
       </div>
     </div>
<!--        <div class="col-md-6">
          <div class="row">
            <div class="col-md-6">
                <a href="javascript:;" onclick="reporte('reporte')" class="btn  big-btn  color-bg flat-btn" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
            </div>
            <div class="col-md-6">
                  <a href="javascript:;" onclick="reporte('excel')" class="btn  big-btn  color-bg flat-btn" style="margin-bottom:10px;"><i class="fa fa-file-excel"></i>  Exportar Excel</a>
            </div>
          </div>
       </div> -->
       <div class="col-md-6">
         <a href="javascript:;" onclick="reporte('reporte')" class="btn mx-btn-post" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
       </div>
      <div class="col-md-6">
        <a href="javascript:;" onclick="reporte('excel')" class="btn mx-btn-post" style="margin-bottom:10px;"><i class="fa fa-file-excel"></i>  Exportar Excel</a>
      </div>
    <div id="iframe-carga"></div>
    <div id="iframe-tablapdf"></div>

@endsection
@section('subscripts')
<script>
  
$("#idproducto").select2({
  @include('app.select2_producto',[
      'idtienda'=>$tienda->id
  ])
});
  
function reporte(tipo){
//     if($('#idproducto').val()==''){
//         $('#iframe-tablapdf').html('<div class="mensaje-danger">El campo "Producto" es obligatorio.</div>');
//         return false;
//     }
//     if($('#idcategoria').val()==''){
//         $('#iframe-tablapdf').html('<div class="mensaje-danger">El campo "Categoria" es obligatorio.</div>');
//         return false;
//     }  
//     if($('#idmarca').val()==''){
//         $('#iframe-tablapdf').html('<div class="mensaje-danger">El campo "Marca" es obligatorio.</div>');
//         return false;
//     }
//    if($('#idestado').val()==''){
//         $('#iframe-tablapdf').html('<div class="mensaje-danger">El campo "Estado" es obligatorio.</div>');
//         return false;
//     }

  if(tipo=='reporte'){
      load('#iframe-carga'); 
      $('#iframe-tablapdf').html('<iframe onload="iframeload();" src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reporte/reporteproductos/showtablapdf') }}'+
                                '?tipo='+tipo+
                                '&idcategoria='+($('#idcategoria').val()!=null?$('#idcategoria').val():'')+
                                '&idproducto='+($('#idproducto').val()!=null?$('#idproducto').val():'')+
                                '&idmarca='+($('#idmarca').val()!=null?$('#idmarca').val():'')+
                                '&idestado='+($('#idestado').val()!=null?$('#idestado').val():'')+
                                '" frameborder="0" width="100%" height="600px"></iframe>');
  }
  else if(tipo=='excel'){
        window.location.href = '{{url('backoffice/tienda/sistema/'.$tienda->id.'/reporte/reporteproductos/showtablaexcel')}}?'+
                                'tipo='+tipo+
                                '&idcategoria='+($('#idcategoria').val()!=null?$('#idcategoria').val():'')+
                                '&idproducto='+($('#idproducto').val()!=null?$('#idproducto').val():'')+
                                '&idmarca='+($('#idmarca').val()!=null?$('#idmarca').val():'')+
                                '&idestado='+($('#idestado').val()!=null?$('#idestado').val():'');
  }
}
  
function iframeload(){
    $('#iframe-carga').html('');
}    
  $("#idestado").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
  }).val({{isset($_GET['idestado'])?($_GET['idestado']!=''?$_GET['idestado']:'0'):'0'}}).trigger("change");

  $("#idcategoria").select2({
    placeholder: "--  Seleccionar --",
    allowClear: true
    }).val({{isset($_GET['idcategoria'])?($_GET['idcategoria']!=''?$_GET['idcategoria']:'0'):'0'}}).trigger("change");
  
  $("#idmarca").select2({
    placeholder: "--  Seleccionar --",
    allowClear: true
    }).val({{isset($_GET['idmarca'])?($_GET['idmarca']!=''?$_GET['idmarca']:'0'):'0'}}).trigger("change");
</script>
@endsection