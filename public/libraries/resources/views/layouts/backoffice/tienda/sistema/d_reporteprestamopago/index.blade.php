@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte de Pagos</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reporteprestamopago') }}" method="GET"> 
    <div class="custom-form">
      <div class="row">
         <div class="col-md-6">
            <label>Listar por</label>
            <select id="listarpor">
                <option></option>
                <option value="1">TODO</option>
                <option value="2">CLIENTE</option>
                <option value="3">ASESOR</option>
            </select>
            <div id="cont-cliente" style="display:none;">
            <label>Cliente</label>
            <select id="idcliente">
                <option></option>
            </select>
            </div>
            <div id="cont-asesor" style="display:none;">
            <label>Asesor</label>
            <select id="idasesor" name="idasesor">
                <option value="{{Auth::user()->id}}">{{Auth::user()->identificacion}} - {{Auth::user()->apellidos}}, {{Auth::user()->nombre}}</option>
            </select>
            </div>
         </div>
         <div class="col-md-6">
            <label>Fecha Inicio</label>
              <input type="date" name="fechainicio" id="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}">
            <label>Fecha Fin</label>
              <input type="date" name="fechafin" id="fechafin" value="{{isset($_GET['fechafin'])?($_GET['fechafin']!=''?$_GET['fechafin']:''):''}}">
         </div>
       </div>
       <a href="javascript:;" onclick="reporte('reporte')" class="btn mx-btn-post" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
    </div>
</form>
<div id="iframe-tablapdf"></div>


@endsection
@section('subscripts')
<script>
function reporte(tipo){
  $('#iframe-tablapdf').html('<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reporteprestamopago?view=tablapdf') }}'+
                              '&listarpor='+($('#listarpor').val()!=null?$('#listarpor').val():'')+
                              '&idcliente='+($('#idcliente').val()!=null?$('#idcliente').val():'')+
                              '&idasesor='+($('#idasesor').val()!=null?$('#idasesor').val():'')+
                              '&fechainicio='+($('#fechainicio').val()!=null?$('#fechainicio').val():'')+
                              '&fechafin='+($('#fechafin').val()!=null?$('#fechafin').val():'')+
                              '" frameborder="0" width="100%" height="600px"></iframe>');
}
  
$("#idasesor").select2({
  @include('app.select2_acceso')
});
  
$("#listarpor").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
}).on("change", function(e) {
    $('#cont-cliente').css('display','none');
    $('#cont-asesor').css('display','none');
    if(e.currentTarget.value==2){
        $('#cont-cliente').css('display','block');
    }
    if(e.currentTarget.value==3){
        $('#cont-asesor').css('display','block');
    }
}).val(1).trigger("change");
  
$("#idcliente").select2({
  @include('app.select2_cliente')
});
  
</script>
@endsection