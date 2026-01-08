@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte de Movimientos</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reportemovimiento') }}" method="GET"> 
   <div class="custom-form">
      <div class="row">
         <div class="col-md-6">
           <label>Tipo de Movimiento</label>
             <select name="idconceptomovimiento" id="idconceptomovimiento">
                  <option></option>
                @foreach($s_conceptomovimientos as $value)
                  <option value="{{ $value->id }}">{{ $value->tipo }} - {{ $value->nombre }}</option>
                @endforeach
             </select>
           <label>Responsable</label>
             <select name="idusuarioresponsable" id="idusuarioresponsable">
               @if(isset($_GET['idusuarioresponsable']))
                 @if($_GET['idusuarioresponsable']!='')
                   <?php $users = DB::table('users')->where('idtienda',$tienda->id)->where('id',$_GET['idusuarioresponsable'])->first();?>
                   <option value="{{$users->id}}">{{$users->identificacion}} - {{$users->apellidos}}, {{$users->nombre}}</option>
                 @else
                   <option></option>
                 @endif
               @else
                   <option></option>
               @endif
             </select>
         </div>
         <div class="col-md-6">
           <label>Descripción</label>
             <input type="text" name="concepto" id="concepto"  value="{{isset($_GET['concepto'])?($_GET['concepto']!=''?$_GET['concepto']:''):''}}">
           <div class="row">
             <div class="col-md-6">
                <label>Fecha inicio</label>
                  <input type="date" name="fechainicio"  id="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}">
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
                    <a href="javascript:;" onclick="reporte('excel')" class="btn  big-btn  color-bg flat-btn" style="margin-bottom:10px;"><i class="fa fa-file-excel"></i>  Exportar Excel</a>
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
          <th>Código</th>
          <th>Tipo</th>
          <th>Concepto</th>
          <th>Descripción</th>
          <th>Monto</th>
          <th>Fecha de registro</th>
          <th>Responsable</th>
          <th width="10px">Estado</th>
        </tr>
      </thead>
      <tbody>
          @foreach($s_movimientos as $value)
          <tr>
            <td>{{ str_pad($value->codigo, 8, "0", STR_PAD_LEFT) }}</td>
            <td>{{ $value->conceptomovimientotipo}}</td>
            <td>{{ $value->conceptomovimientonombre}}</td>
            <td>{{ $value->concepto}}</td>
            <td>{{ $value->monto}}</td>
            <td>{{ date_format(date_create($value->fecharegistro), 'd/m/Y h:i:s A') }}</td>
            <td>{{ $value->responsablenombre}}</td>
            <td>
              @if($value->fechaconfirmacion!='')
                <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>
              @else
                <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Pendiente</span></div> 
              @endif
            </td>
          </tr>
          @endforeach
      </tbody>
  </table>
</div>
{{ $s_movimientos->links('app.tablepagination', ['results' => $s_movimientos]) }}
@endsection
@section('subscripts')
<script>
function reporte(tipo){
    window.location.href = '{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportemovimiento')}}?'+
                           'tipo='+tipo+
                           '&idconceptomovimiento='+($('#idconceptomovimiento').val()!=null?$('#idconceptomovimiento').val():'')+
                           '&idusuarioresponsable='+($('#idusuarioresponsable').val()!=null?$('#idusuarioresponsable').val():'')+
                           '&concepto='+$('#concepto').val()+
                           '&fechainicio='+$('#fechainicio').val()+
                           '&fechafin='+$('#fechafin').val();
    }
$("#idusuarioresponsable").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportemovimiento/showlistarusuario')}}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                  buscar: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2,
    allowClear: true
});

$("#idconceptomovimiento").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
}).val({{isset($_GET['idconceptomovimiento'])?($_GET['idconceptomovimiento']!=''?$_GET['idconceptomovimiento']:'0'):'0'}}).trigger("change");
</script>
@endsection