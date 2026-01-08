@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte de Aperturas y Cierres</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reporteaperturacierre') }}" method="GET"> 
    <div class="custom-form">
      <div class="row">
         <div class="col-md-6">
            <label>Caja</label>
              <select name="idcaja" id="idcaja">
                  <option></option>
                  @foreach($s_cajas as $value)
                  <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                  @endforeach
              </select>
            <label>Persona responsable</label>
              <select name="idusersresponsable" id="idusersresponsable">
                  <option></option>
                  @foreach($users as $value)
                  <option value="{{ $value->id }}">{{ $value->apellidos }}, {{ $value->nombre }}</option>
                  @endforeach
              </select>
         </div>
         <div class="col-md-6">
           <label>Persona asignado</label>
              <select name="idusers" id="idusers">
                  <option></option>
                  @foreach($users as $value)
                  <option value="{{ $value->id }}">{{ $value->apellidos }}, {{ $value->nombre }}</option>
                  @endforeach
              </select>
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
    </div>
</form>
<div class="table-responsive">
  <table class="table" id="tabla-contenido">
      <thead class="thead-dark">
        <tr>
          <th>Persona Responsable</th>
          <th>Persona Asignado</th>
          <th>Caja</th>
          <th>Apertura</th>
          <th>Cierre</th>
          <th>Fecha de Apertura</th>
          <th>Fecha de Cierre</th>
          <th width="10px">Estado</th>
        </tr>
      </thead>
      <tbody>
          @foreach($s_aperturacierres as $value)
          <tr>
            <td>{{ $value->usersresponsableapellidos}}, {{$value->usersresponsablenombre}}</td>
            <td>{{ $value->usersrecepcionapellidos}}, {{$value->usersrecepcionnombre}}</td>
            <td>{{ $value->cajanombre}}</td>
            <td>{{ $value->montoasignar}}</td>
            <td>{{ $value->montocierre}}</td>
            <td>{{ date_format(date_create($value->fechaconfirmacion), 'd/m/Y h:i:s A') }}</td>
            <td>{{ date_format(date_create($value->fechacierreconfirmacion), 'd/m/Y h:i:s A') }}</td>
            <td>
              @if($value->s_idestado==1)
                <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-sync-alt"></i> Apertura en Proceso</span></div>
              @elseif($value->s_idestado==2 && $value->fechaconfirmacion=='')
                <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Apertura Pendiente</span></div> 
              @elseif($value->s_idestado==2 && $value->fechaconfirmacion!='')
                <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Aperturado</span></div>
              @elseif($value->s_idestado==3 && $value->fechacierreconfirmacion=='')
                <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Cierre Pendiente</span></div>
              @elseif($value->s_idestado==3 &&$value->fechacierreconfirmacion!='')
                <div class="td-badge"><span class="badge badge-pill badge-primary"><i class="fa fa-check"></i> Cerrado</span></div>
              @endif
            </td>
          </tr>
          @endforeach
      </tbody>
  </table>
</div>
{{ $s_aperturacierres->links('app.tablepagination', ['results' => $s_aperturacierres]) }}
@endsection
@section('subscripts')
<script>
 function reporte(tipo){
     window.location.href = '{{url('backoffice/tienda/sistema/'.$tienda->id.'/reporteaperturacierre')}}?'+
                                'tipo='+tipo+
                                '&idcaja='+($('#idcaja').val()!=null?$('#idcaja').val():'')+
                                '&idusersresponsable='+($('#idusersresponsable').val()!=null?$('#idusersresponsable').val():'')+
                                '&idusers='+($('#idusers').val()!=null?$('#idusers').val():'')+
                                '&fechainicio='+$('#fechainicio').val()+
                                '&fechafin='+$('#fechafin').val();
 }
$("#idcaja").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
}).val({{isset($_GET['idcaja'])?($_GET['idcaja']!=''?$_GET['idcaja']:'0'):'0'}}).trigger("change");

$("#idusers").select2({
    placeholder: "-- Seleccionar --",
    allowClear: true
}).val({{isset($_GET['idusers'])?($_GET['idusers']!=''?$_GET['idusers']:'0'):'0'}}).trigger("change");

$("#idusersresponsable").select2({
    placeholder: "-- Seleccionar --",
    allowClear: true
}).val({{isset($_GET['idusersresponsable'])?($_GET['idusersresponsable']!=''?$_GET['idusersresponsable']:'0'):'0'}}).trigger("change");
</script>
@endsection