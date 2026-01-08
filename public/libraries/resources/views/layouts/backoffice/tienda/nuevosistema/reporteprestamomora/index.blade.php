@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte de Moras</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reporteprestamomora') }}" method="GET"> 
    <div class="custom-form">
      <div class="row">
         <div class="col-md-6">
            <label>Fecha inicio</label>
            <input type="date" name="fechainicio" id="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}"/>
            <label>Fecha fin</label>
            <input type="date" name="fechafin" id="fechafin" value="{{isset($_GET['fechafin'])?($_GET['fechafin']!=''?$_GET['fechafin']:''):''}}"/>
         </div>
         <div class="col-md-6">
            <label>Responsable</label>
            <select id="idresponsable" name="idresponsable">
                @if(isset($_GET['idresponsable']))
                @if($_GET['idresponsable']!='')
                <?php $users = DB::table('users')->where('idtienda',$tienda->id)->where('id',$_GET['idresponsable'])->first();?>
                <option value="{{$users->id}}">{{$users->identificacion}} - {{$users->apellidos}}, {{$users->nombre}}</option>
                @else
                <option></option>
                @endif
                @else
                <option></option>
                @endif
            </select>
            <label>Detalles</label>
            <select id="iddetalle" name="iddetalle">
                <option value="1">SI</option>
                <option value="2">NO</option>
            </select>
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
        <th>Código</th>
        <th>Comprobante</th>
        <th>Correlativo</th>
        <th>Total</th>
        <th>Proveedor</th>
        <th>Fecha de registro</th>
        <th>Responsable</th>
        <th width="10px">Estado</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
</table>
</div>
@endsection
@section('subscripts')
<script>
function reporte(tipo){
  window.location.href = '{{url('backoffice/tienda/sistema/'.$tienda->id.'/reporteprestamomora')}}?'+
                          'tipo='+tipo+
                          '&idresponsable='+($('#idresponsable').val()!=null?$('#idresponsable').val():'')+
                          '&iddetalle='+($('#iddetalle').val()!=null?$('#iddetalle').val():'')+
                          '&fechainicio='+$('#fechainicio').val()+
                          '&fechafin='+$('#fechafin').val();
}
$("#idresponsable").select2({
  @include('app.select2_cliente')
});
  
$("#iddetalle").select2({
  placeholder: "--  Seleccionar --",
  minimumResultsForSearch: -1
}).val({{isset($_GET['iddetalle'])?($_GET['iddetalle']!=''?$_GET['iddetalle']:'0'):'0'}}).trigger("change");
</script>
@endsection