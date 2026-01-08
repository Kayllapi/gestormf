@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte de Margen de Ganancia</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reportemargenganancia') }}" method="GET"> 
    <div class="custom-form">
       <div class="row">
           <div class="col-md-6">
              <label>Fecha inicio *</label>
              <input type="date" name="fechainicio" id="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}">
           </div>
           <div class="col-md-6">
              <label>Fecha fin *</label>
              <input type="date" name="fechafin" id="fechafin" value="{{isset($_GET['fechafin'])?($_GET['fechafin']!=''?$_GET['fechafin']:''):''}}">
           </div>
           <div class="col-md-12">
              <a href="javascript:;" onclick="reporte('reporte')" class="btn mx-btn-post" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
           </div>
      </div>
      <div id="iframe-carga"></div>
      <div id="iframe-tablapdf"></div>
    </div>
</form>
@endsection
@section('subscripts')
<script>
function reporte(tipo){
  if($('#fechainicio').val()==''){
      $('#iframe-tablapdf').html('<div class="mensaje-danger">El campo "Fecha Inicio" es obligatorio.</div>');
      return false;
  }
  if($('#fechafin').val()==''){
      $('#iframe-tablapdf').html('<div class="mensaje-danger">El campo "Fecha Fin" es obligatorio.</div>');
      return false;
  }
      
  load('#iframe-carga'); 
  $('#iframe-tablapdf').html('<iframe onload="iframeload();" src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reporte/reportemargenganancia/showtablapdf') }}'+
                              '?fechainicio='+($('#fechainicio').val()!=null?$('#fechainicio').val():'')+
                              '&fechafin='+($('#fechafin').val()!=null?$('#fechafin').val():'')+
                              '" frameborder="0" width="100%" height="600px"></iframe>');
}

function iframeload(){
    $('#iframe-carga').html('');
}    
</script>
@endsection