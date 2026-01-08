@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte de Aperturas y Cierres</span>
    </div>
</div>
      <div class="row">
         <div class="col-md-12">
            <label>Listar por *</label>
            <select id="listarpor">
                <option></option>
                <option value="1">CAJA</option>
                <option value="2">PERSONA RESPONSABLE</option>
                <option value="3">PERSONA ASIGNANDO</option>
            </select>
         </div>
         <div class="col-md-6">
            <div id="cont-1" style="display:none;">
                <label>Caja</label>
                <select name="idcaja" id="idcaja">
                    <option></option>
                    @foreach($s_cajas as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div id="cont-2" style="display:none;">
                <label>Persona responsable</label>
                <select name="idusersresponsable" id="idusersresponsable">
                    <option></option>
                </select>
            </div>
            <div id="cont-3" style="display:none;">
                <label>Persona asignado</label>
                <select name="idusersrecepcion" id="idusersrecepcion">
                    <option></option>
                </select>
            </div>
         </div>
         <div class="col-md-6">
            <di id="cont-fecha" style="display:none;">
                <div class="row">
                  <div class="col-md-6">
                    <label>Fecha inicio</label>
                    <input type="date" name="fechainicio" id="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}">
                  </div>
                  <div class="col-md-6">
                    <label>Fecha fin</label>
                    <input type="date" name="fechafin" id="fechafin" value="{{isset($_GET['fechafin'])?($_GET['fechafin']!=''?$_GET['fechafin']:''):''}}">
                  </div>
                </div>
            </div>
          </div>
        <a href="javascript:;" onclick="reporte('reporte')" class="btn mx-btn-post" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
    </div>
    <div id="iframe-carga"></div>
    <div id="iframe-tablapdf"></div>
@endsection
@section('subscripts')
<script>
function reporte(tipo){
  if($('#listarpor').val()==''){
      $('#iframe-tablapdf').html('<div class="mensaje-danger">El campo "Listar por" es obligatorio.</div>');
      return false;
  }
      
  load('#iframe-carga'); 
  $('#iframe-tablapdf').html('<iframe onload="iframeload();" src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reporte/reporteaperturacierre/showtablapdf') }}'+
                              '?listarpor='+$('#listarpor').val()+
                              '&idcaja='+$('#idcaja').val()+
                              '&idusersresponsable='+($('#idusersresponsable').val()!=null?$('#idusersresponsable').val():'')+
                              '&idusersrecepcion='+($('#idusersrecepcion').val()!=null?$('#idusersrecepcion').val():'')+
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
    $('#cont-fecha').css('display','none');
    if(e.currentTarget.value==1){
        $('#cont-1').css('display','block');
        $('#cont-fecha').css('display','block');
    }
    else if(e.currentTarget.value==2){
        $('#cont-2').css('display','block');
        $('#cont-fecha').css('display','block');
    }
    else if(e.currentTarget.value==3){
        $('#cont-3').css('display','block');
        $('#cont-fecha').css('display','block');
    }
});
  
$("#idcaja").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
});

$("#idusersresponsable").select2({
    @include('app.select2_acceso')
});
  
$("#idusersrecepcion").select2({
    @include('app.select2_acceso')
});

</script>
@endsection