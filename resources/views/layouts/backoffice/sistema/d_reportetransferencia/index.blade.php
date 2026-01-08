@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<?php
$get_idtiendaorigen = isset($_GET['idtiendaorigen']) ? $_GET['idtiendaorigen'] : 1;
$get_idusersorigen = isset($_GET['idusersorigen']) ? $_GET['idusersorigen'] : '';

?>
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte de Transferencia</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reportetransferencia') }}" method="GET"> 
    <div class="custom-form">
      <div class="row">
         <div class="col-md-6">
           <label>Tienda</label>
           <select name="tienda" id="tienda">
                      <option></option>
                      @foreach($tiendas as $value)
                      <option value="{{ $value->id }}"> {{ $value->nombre }}</option>
                      @endforeach
           </select>
           <label>Código de Transferencia</label>
            <input class="form-control" type="number" id="codigo" name="codigo" value="{{isset($_GET['codigo'])?($_GET['codigo']!=''?$_GET['codigo']:''):''}}">
            <label>Estado</label>
            <select id="idestado" name="idestado">
                      <option></option>
                      <option value="1">Solicitud </option>
                      <option value="2">Envio</option>
                      <option value="3">Recepcionado</option>
                  </select>
         </div>
         <div class="col-md-6">
           <label>Motivo</label>
                  <input class="form-control" type="text" id="motivo" name="motivo" value="{{isset($_GET['motivo'])?($_GET['motivo']!=''?$_GET['motivo']:''):''}}">
                  <label>Fecha de Inicio</label>
                  <input class="form-control" type="date" name="fechainicio" id="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}">
                  <label>Fecha de Fin</label>
                  <input class="form-control" type="date" name="fechafin" id="fechafin" value="{{isset($_GET['fechafin'])?($_GET['fechafin']!=''?$_GET['fechafin']:''):''}}">
         </div>
            
       </div>
      <div class="col-md-6">
          <div class="row">
            <div class="col-md-6">
                <a href="javascript:;" onclick="reporte('reporte')" class="btn  big-btn  color-bg flat-btn" style="margin-bottom:10px;"><i class="fa fa-search"></i> Filtrar reporte</a>
            </div>
            <div class="col-md-6">
                  <a href="javascript:;" onclick="reporte('excel')"class="btn  big-btn  color-bg flat-btn" style="margin-bottom:10px;"><i class="fa fa-file-excel"></i>  Exportar Excel</a>
            </div>
          </div>
        </div>
    </div>
</form>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th width="90px">Código</th>
        <th width="10px">Fecha Solicitud</th>
        <th width="10px">Fecha Envio</th>
        <th width="10px">Fecha Recepción</th>
        <th >Tienda Origen (Responsable)</th>
        <th>Tienda Destino (Responsable)</th>
        <th width="90px">Motivo</th>
        <th width="10px">Estado</th>
      </tr>
    </thead>
    <tbody>
       @foreach($productotransferencia as $value)
    <tr>
      <td>{{ str_pad($value->codigo, 6, "0", STR_PAD_LEFT) }}</td>
      <td>{{ ($value->idestadotransferencia==1 or $value->idestadotransferencia==2 or $value->idestadotransferencia==3)?date_format(date_create($value->fechasolicitud), 'd/m/Y - h:i A'):'---' }}</td>
      <td>{{ ($value->idestadotransferencia==2 or $value->idestadotransferencia==3)?date_format(date_create($value->fechaenvio), 'd/m/Y - h:i A'):'---' }}</td>
      <td>{{ $value->idestadotransferencia==3?date_format(date_create($value->fecharecepcion), 'd/m/Y - h:i A'):'---' }}</td>
      <td>{{ $value->tienda_origen_nombre}} {{ $value->idusersorigen!=0?'('.$value->user_origen_nombre.')':'' }}</td>
      <td>{{ $value->tienda_destino_nombre}} {{ $value->idusersdestino!=0?'('.$value->user_destino_nombre.')':'' }}</td>
      <td>{{ $value->motivo}}</td>
      <td>
      @if($value->idestadotransferencia==1)
          <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Solicitud</span></div> 
      @elseif($value->idestadotransferencia==2)
          <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-share"></i> Envio</span></div>
      @elseif($value->idestadotransferencia==3)
          <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Recepcionado</span></div>
      @endif 
      </td>
    </tr>
    @endforeach
    </tbody>
</table>
</div>
@endsection
@section('subscripts')
<script>
   function reporte(tipo){
        window.location.href = '{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportetransferencia')}}?'+
                                'tipo='+tipo+
                                '&tienda='+($('#tienda').val()!=null?$('#tienda').val():'')+
                                '&idestado='+($('#idestado').val()!=null?$('#idestado').val():'')+
                                '&codigo='+$('#codigo').val()+
                                '&fechainicio='+$('#fechainicio').val()+
                                '&fechafin='+$('#fechafin').val();
    }
$("#tienda").select2({
      placeholder: "--  Seleccionar --",
      allowClear: true
  }).val({{$tienda->id}}).trigger("change");
  
  $("#idtiendaorigen").select2({
      placeholder: "--  Seleccionar --",
      allowClear: true
  }).val({{isset($_GET['idtiendaorigen'])?($_GET['idtiendaorigen']!=''?$_GET['idtiendaorigen']:'0'):'0'}}).trigger("change");

  $("#idtiendadestino").select2({
      placeholder: "--  Seleccionar --",
      allowClear: true
  }).val({{isset($_GET['idtiendadestino'])?($_GET['idtiendadestino']!=''?$_GET['idtiendadestino']:'0'):'0'}}).trigger("change");

  $("#idestado").select2({
      placeholder: "--  Seleccionar --",
      minimumResultsForSearch: -1,
      allowClear: true
  }).val({{isset($_GET['idestado'])?($_GET['idestado']!=''?$_GET['idestado']:'0'):'0'}}).trigger("change");
</script>
@endsection