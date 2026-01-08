@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte de Compras</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reportecompra') }}" method="GET"> 
    <div class="custom-form">
       <div class="row">
           <div class="col-md-6">
              <label>Listar por *</label>
              <select id="listarpor">
                  <option></option>
                  <!--option value="1">TODO</option-->
                  <option value="2">PROVEEDOR</option>
                  <option value="3">RESPONSABLE</option>
              </select>
              <div id="cont-2" style="display:none;">
                  <label>Proveedor</label>
                  <select id="idproveedor">
                      <option></option>
                  </select>
              </div>
              <div id="cont-3" style="display:none;">
                  <label>Responsable</label>
                  <select id="idresponsable">
                      <option></option>
                  </select>
              </div>
           </div>
           <div class="col-md-6">
              <label>Fecha inicio *</label>
              <input type="date" name="fechainicio" id="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}">
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
  if($('#listarpor').val()==''){
      $('#iframe-tablapdf').html('<div class="mensaje-danger">El campo "Listar por" es obligatorio.</div>');
      return false;
  }
  if($('#fechainicio').val()==''){
      $('#iframe-tablapdf').html('<div class="mensaje-danger">El campo "Fecha Inicio" es obligatorio.</div>');
      return false;
  }
  if($('#fechafin').val()==''){
      $('#iframe-tablapdf').html('<div class="mensaje-danger">El campo "Fecha Fin" es obligatorio.</div>');
      return false;
  }
      
  load('#iframe-carga'); 
  $('#iframe-tablapdf').html('<iframe onload="iframeload();" src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reporte/reportecompra/showtablapdf') }}'+
                              '?listarpor='+$('#listarpor').val()+
                              '&idproveedor='+($('#idproveedor').val()!=null?$('#idproveedor').val():'')+
                              '&idproveedor_detalle='+($('#idproveedor_detalle').val()!=null?$('#idproveedor_detalle').val():'')+
                              '&idresponsable='+($('#idresponsable').val()!=null?$('#idresponsable').val():'')+
                              '&idresponsable_detalle='+($('#idresponsable_detalle').val()!=null?$('#idresponsable_detalle').val():'')+
                              '&fechainicio='+($('#fechainicio').val()!=null?$('#fechainicio').val():'')+
                              '&fechafin='+($('#fechafin').val()!=null?$('#fechafin').val():'')+
                              '" frameborder="0" width="100%" height="600px"></iframe>');
}

function iframeload(){
    $('#iframe-carga').html('');
}    
  
$("#listarpor").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
}).on("change", function(e) {
    $('#cont-1').css('display','none');
    $('#cont-2').css('display','none');
    $('#cont-3').css('display','none');
    $('#cont-4').css('display','none');
    $('#cont-5').css('display','none');
    if(e.currentTarget.value==1){
        $('#cont-1').css('display','block');
    }
    else if(e.currentTarget.value==2){
        $('#cont-2').css('display','block');
    }
    else if(e.currentTarget.value==3){
        $('#cont-3').css('display','block');
    }
    else if(e.currentTarget.value==4){
        $('#cont-4').css('display','block');
    }
    else if(e.currentTarget.value==5){
        $('#cont-5').css('display','block');
    }
});

$("#idproveedor").select2({
  @include('app.select2_cliente')
});
$("#idresponsable").select2({
  @include('app.select2_acceso')
}); 
</script>
@endsection