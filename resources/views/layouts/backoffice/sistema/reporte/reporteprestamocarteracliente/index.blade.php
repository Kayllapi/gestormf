@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte de Cartera de Clientes</span>
    </div>
</div>
      <div class="row">
         <div class="col-md-6">
            @if(modulo($tienda->id,Auth::user()->id,'reportecarteracliente_listarporasesor')['resultado']=='CORRECTO')
                <label>Asesor</label>
                <select id="idasesor" name="idasesor" disabled>
                    <option value="{{Auth::user()->id}}">{{Auth::user()->identificacion}} - {{Auth::user()->apellidos}}, {{Auth::user()->nombre}}</option>
                </select>
            @else
                <label>Asesor *</label>
                <select id="idasesor" name="idasesor">
                    <option></option>
                </select>
            @endif
         </div>
         <div class="col-md-6">
                <div class="row">
                  <div class="col-md-12">
                    <label>Tipo</label>
                    <select id="idtipo" name="idtipo">
                        <option></option>
                        <option value="1">CLIENTE</option>
                        <option value="2">AVAL</option>
                        <option value="3">CLIENTE/AVAL</option>
                    </select>
                  </div>
                  <div class="col-md-12">
                    <label>Estado</label>
                    <select id="idestado" name="idestado">
                        <option></option>
                        <option value="1">ACTIVOS</option>
                        <option value="2">INACTIVOS</option>
                    </select>
                  </div>
                </div>
          </div>
    </div>
    <a href="javascript:;" onclick="reporte()" class="btn mx-btn-post" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
    <div id="iframe-carga"></div>
    <div id="iframe-tablapdf"></div>
@endsection
@section('subscripts')
<script>
function reporte(){
  if($('#idasesor').val()==''){
      $('#iframe-tablapdf').html('<div class="mensaje-danger">El campo "Asesor" es obligatorio.</div>');
      return false;
  }
      
  load('#iframe-carga'); 
  $('#iframe-tablapdf').html('<iframe onload="iframeload();" src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reporte/reporteprestamocarteracliente/showtablapdf') }}'+
                              '?idasesor='+($('#idasesor').val()!=null?$('#idasesor').val():'')+
                              '&idtipo='+($('#idtipo').val()!=null?$('#idtipo').val():'')+
                              '&idestado='+($('#idestado').val()!=null?$('#idestado').val():'')+
                              '" frameborder="0" width="100%" height="600px"></iframe>');
}

function iframeload(){
    $('#iframe-carga').html('');
}    
$("#idasesor").select2({
  @include('app.select2_acceso')
});  
  
$('#idtipo').select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
}); 
$('#idestado').select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
}); 
</script>
@endsection