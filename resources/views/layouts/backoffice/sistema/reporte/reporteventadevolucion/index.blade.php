@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte de Venta de Devolución</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reporteventadevolucion') }}" method="GET"> 
    <div class="custom-form">
      <div class="row">
        <div class="col-md-6">
            <label>Código de Impresión</label>
             <input type="text" id="codigo" name="codigo" value="{{isset($_GET['codigo'])?($_GET['codigo']!=''?$_GET['codigo']:''):''}}"/>
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
            <label>Cliente</label>
             <select id="idcliente" name="idcliente">
                  @if(isset($_GET['idcliente']))
                  @if($_GET['idcliente']!='')
                  <?php $users = DB::table('users')->where('idtienda',$tienda->id)->where('id',$_GET['idcliente'])->first();?>
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
          <label>Comprobante</label>
            <select id="idcomprobante" name="idcomprobante">
                <option></option>
                @foreach($comprobante as $value)
                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                @endforeach
            </select>
          <label>Estado</label>
            <select id="idestado" name="idestado">
                <option></option>
                <option value="2">Confirmado</option>
                <option value="3">Anulado</option>
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
          <th>Código de Venta</th>
          <th>Código de Devolución</th>
          <th>Total</th>
          <th>Moneda</th>
          <th>Fecha Registro</th>
          <th>Responsable</th>
          <th>Comprobante</th>
          <th>DNI/RUC</th>
          <th>Cliente</th>
          <th>Tipo Entega</th>
          <th>Motivo</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
         @foreach($ventadevoluciondetalle as $value)
        <tr>
          <td>{{ str_pad($value->codigoventa, 8, "0", STR_PAD_LEFT) }}</td>
          <td>{{ str_pad($value->codigoimpresion, 8, "0", STR_PAD_LEFT) }}</td>
          <td>{{ $value->total}}</td>
          <td>{{ $value->nombremoneda}}</td>
          <td>{{ date_format(date_create($value->fechaconfirmacion),"d/m/Y h:i:s A")}}</td>
          <td>{{ $value->apellidosresponsable}},{{ $value->nombreresponsable}}</td>
          <td>{{ $value->nombreComprobante}}</td>
          <td>{{ $value->identificacioncliente}}</td>
          <td>{{ $value->apellidocliente}},{{ $value->nombrecliente}}</td>
          <td>{{ $value->nombretipoentrega}}</td>
          <td>{{ $value->motivo}}</td>
          <td> 
          @if($value->s_idestado==1)
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Pendiente</span></div> 
            @elseif($value->s_idestado==2)
              <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fas fa-check"></i> Confirmado</span></div> 
            @elseif($value->s_idestado==3)
              <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span></div>
            @endif
          </td>
        </tr>
         @endforeach
      </tbody>
  </table>
</div>
{{ $ventadevoluciondetalle->links('app.tablepagination', ['results' => $ventadevoluciondetalle]) }}
@endsection
@section('subscripts')
<script>
   function reporte(tipo){
        window.location.href = '{{url('backoffice/tienda/sistema/'.$tienda->id.'/reporteventadevolucion')}}?'+
                                'tipo='+tipo+
                                '&idresponsable='+($('#idresponsable').val()!=null?$('#idresponsable').val():'')+
                                '&idcliente='+($('#idcliente').val()!=null?$('#idcliente').val():'')+
                                '&idestado='+($('#idestado').val()!=null?$('#idestado').val():'')+
                                '&idcomprobante='+($('#idcomprobante').val()!=null?$('#idcomprobante').val():'')+
                                '&codigo='+$('#codigo').val()+
                                '&fechainicio='+$('#fechainicio').val()+
                                '&fechafin='+$('#fechafin').val();
    }
  $("#idestado").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
  }).val({{isset($_GET['idestado'])?($_GET['idestado']!=''?$_GET['idestado']:'0'):'0'}}).trigger("change");

  $("#idresponsable").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportecompraproducto/showlistarusuario')}}",
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
  
$("#idcliente").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportecompraproducto/showlistarusuario')}}",
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
  
$("#idagencia").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
}).val({{isset($_GET['idagencia'])?($_GET['idagencia']!=''?$_GET['idagencia']:'0'):'0'}}).trigger("change");
  
$("#idcomprobante").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
}).val({{isset($_GET['idcomprobante'])?($_GET['idcomprobante']!=''?$_GET['idcomprobante']:'0'):'0'}}).trigger("change");
</script>
@endsection