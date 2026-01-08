@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte de Ventas</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reporte/reporteventa') }}" method="GET"> 
    <div class="custom-form">
      <div class="row">
           <div class="col-md-6">
              <label>Listar por *</label>
              <select id="listarpor">
                  <option></option>
                  <!--option value="1">TODO</option-->
                  <option value="2">CLIENTE</option>
                  <option value="3">VENDEDOR</option>
                  <option value="4">CAJERO</option>
              </select>
              <div id="cont-2" style="display:none;">
                  <label>Cliente</label>
                  <select id="idcliente">
                      <option></option>
                  </select>
              </div>
              <div id="cont-3" style="display:none;">
                  <label>Vendedor</label>
                  <select id="idvendedor" name="idvendedor">
                      <option></option>
                  </select>
              </div>
              <div id="cont-4" style="display:none;">
                  <label>Cajero</label>
                  <select id="idcajero" name="idcajero">
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
  $('#iframe-tablapdf').html('<iframe onload="iframeload();" src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reporte/reporteventa/showtablapdf') }}'+
                              '?listarpor='+$('#listarpor').val()+
                              '&idcliente='+($('#idcliente').val()!=null?$('#idcliente').val():'')+
                              '&idvendedor='+($('#idvendedor').val()!=null?$('#idvendedor').val():'')+
                              '&idcajero='+($('#idcajero').val()!=null?$('#idcajero').val():'')+
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
});

$("#idcliente").select2({
  @include('app.select2_cliente')
});
$("#idvendedor").select2({
  @include('app.select2_acceso')
});  
$("#idcajero").select2({
  @include('app.select2_acceso')
});  

</script>
@endsection