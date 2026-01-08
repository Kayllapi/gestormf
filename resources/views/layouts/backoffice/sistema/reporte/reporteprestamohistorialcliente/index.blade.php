@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte de Historial de Cliente</span>
    </div>
</div>
      <div class="row">
         <div class="col-md-12">
            <label>Cliente *</label>
            <select id="idcliente">
                <option></option>
            </select>
         </div>
         <div class="col-md-12">
          <a href="javascript:;" onclick="reporte('reporte')" class="btn mx-btn-post" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
         </div>
    </div>
    <div id="iframe-carga"></div>
    <div id="iframe-tablapdf"></div>
@endsection
@section('subscripts')
<script>
function reporte(tipo){
  if($('#idcliente').val()==''){
      $('#iframe-tablapdf').html('<div class="mensaje-danger">El campo "Cliente" es obligatorio.</div>');
      return false;
  }
      
  load('#iframe-carga'); 
  $('#iframe-tablapdf').html('<iframe onload="iframeload();" src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reporte/reporteprestamohistorialcliente/showtablapdf') }}'+
                              '?idcliente='+($('#idcliente').val()!=null?$('#idcliente').val():'')+
                              '" frameborder="0" width="100%" height="600px"></iframe>');
}
  
function iframeload(){
    $('#iframe-carga').html('');
}    
$("#idcliente").select2({
  @include('app.select2_cliente')
}); 
</script>
@endsection