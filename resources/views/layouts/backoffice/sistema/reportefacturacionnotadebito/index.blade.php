@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte Notas de Débito</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reportefacturacionnotadebito') }}" method="GET"> 
    <div class="custom-form">
      <div class="row">
          <div class="col-md-6">
            <label>Empresa</label>
              <select id="idagencia" name="idagencia">
                  <option></option>
                  @foreach($agencia as $value)
                  <option value="{{ $value->id }}"?>{{ $value->nombrecomercial }}</option>
                  @endforeach
              </select>
            <div class="row">
            <div class="col-md-6">
              <label>Serie</label>
                  <input class="form-control" type="text"  name="serie" id="serie" value="{{isset($_GET['serie'])?($_GET['serie']!=''?$_GET['serie']:''):''}}">
              </div>
              <div class="col-md-6">
                <label>Correlativo</label>
                  <input class="form-control" type="text"  name="correlativo" id="correlativo" value="{{isset($_GET['correlativo'])?($_GET['correlativo']!=''?$_GET['correlativo']:''):''}}">
              </div>
            </div>
            <label>Moneda</label>
              <select name="moneda" id="moneda">
                  <option></option>
                  @foreach($moneda as $value)
                  <option value="{{ $value->nombre }}"> {{ $value->nombre }}</option>
                 @endforeach
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
           
               <label>Correlativo Modificado</label>
                  <input class="form-control" type="text"  name="afectado" id="afectado"  value="{{isset($_GET['afectado'])?($_GET['afectado']!=''?$_GET['afectado']:''):''}}">
            <label>Estado</label>
            <select id="idestado" name="idestado">
                <option></option>
                <option value="aceptada">Aceptada</option>
                <option value="observaciones">Observaciones</option>
                <option value="rechazada">Rechazada</option>
                <option value="excepción">Excepción</option>
              <option value="error">No enviado</option>
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
          <th>Serie</th>
          <th>Correlativo</th>
          <th>Base Imp.</th>
          <th>IGV	</th>
          <th>Total</th>
          <th>Moneda</th>
          <th>Fecha de Emisión</th>
          <th>DNI/RUC</th>
          <th>Cliente</th>
          <th>RUC</th>
          <th>Emisor</th>
          <th>Responsable</th>
          <th>Modificado</th>
          <th>Motivo</th>
          <th>SUNAT</th>
        </tr>
      </thead>
      <tbody>
       @foreach($facturacionnotadebito as $value)
          <tr>
               <td>{{ $value->notadebito_serie}}</td>
               <td>{{ str_pad($value->notadebito_correlativo, 8, "0", STR_PAD_LEFT) }}</td>
               <td>{{ $value->notadebito_valorventa}}</td>
               <td>{{ $value->notadebito_totalimpuestos}}</td>
               <td>{{ $value->notadebito_montoimpuestoventa}}</td>
               <td>
                 @if($value->notadebito_tipomoneda=='PEN')
                    SOLES
                @elseif($value->notadebito_tipomoneda=='USD')
                    DOLARES
                @endif
               </td>
              <td>{{ date_format(date_create($value->notadebito_fechaemision), 'd/m/Y h:i:s A') }}</td>
               <td>{{ $value->cliente_numerodocumento}}</td>
               <td>{{ $value->clienteapellido}},{{ $value->clientenombre}}</td>
               <td>{{ $value->emisor_ruc}}</td>
               <td>{{ $value->emisor_nombrecomercial}}</td>
               <td>{{ $value->responsablenombre}}</td>
               <td>{{ $value->notadebito_numerodocumentoafectado}}</td>
               <td>{{ $value->notadebito_descripcionmotivo}}</td>
            <td>
              @if($value->respuestaestado=='ACEPTADA')
                <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Aceptada</span></div>
              @elseif($value->respuestaestado=='OBSERVACIONES')
                <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Observaciones</span></div> 
              @elseif($value->respuestaestado=='RECHAZADA')
                <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Rechazada</span></div> 
              @elseif($value->respuestaestado=='EXCEPCION')
                <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-sync-alt"></i> Excepción</span></div>
              @else
                <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> No enviado</span></div>
              @endif
            </td>
           </tr>
        @endforeach
      </tbody>
  </table>
</div>
<style>
  td{
    padding: 7px !important;
  }
</style>
{{ $facturacionnotadebito->links('app.tablepagination', ['results' => $facturacionnotadebito]) }}

@endsection
@section('subscripts')
<script>
       function reporte(tipo){
        window.location.href = '{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportefacturacionnotadebito')}}?'+
                                'tipo='+tipo+
                                '&idestado='+($('#idestado').val()!=null?$('#idestado').val():'')+
                                '&idcliente='+($('#idcliente').val()!=null?$('#idcliente').val():'')+
                                '&idresponsable='+($('#idresponsable').val()!=null?$('#idresponsable').val():'')+
                                '&moneda='+($('#moneda').val()!=null?$('#moneda').val():'')+
                                '&idagencia='+($('#idagencia').val()!=null?$('#idagencia').val():'')+
                                '&afectado='+$('#afectado').val()+
                                '&serie='+$('#serie').val()+
                                '&correlativo='+$('#correlativo').val()+
                                '&fechainicio='+$('#fechainicio').val()+
                                '&fechafin='+$('#fechafin').val();
    }
  $("#idagencia").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
}).val({{isset($_GET['idagencia'])?($_GET['idagencia']!=''?$_GET['idagencia']:'0'):'0'}}).trigger("change");
  $("#idcliente").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportefacturacionnotadebito/showlistarusuario')}}",
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
   $("#idresponsable").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportefacturacionnotadebito/showlistarusuario')}}",
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
    $("#moneda").select2({
      placeholder: "--  Seleccionar --",
      minimumResultsForSearch: -1,
      allowClear: true
  }).val('{{isset($_GET['moneda'])?($_GET['moneda']!=''?$_GET['moneda']:'0'):'0'}}').trigger("change");
  
  $("#tipoCompbrobante").select2({
      placeholder: "--  Seleccionar --",
      minimumResultsForSearch: -1,
      allowClear: true
  }).val('{{isset($_GET['tipoCompbrobante'])?($_GET['tipoCompbrobante']!=''?$_GET['tipoCompbrobante']:'0'):'0'}}').trigger("change");
  
   $("#idestado").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
}).val({{isset($_GET['idestado'])?($_GET['idestado']!=''?$_GET['idestado']:'0'):'0'}}).trigger("change");

</script>
@endsection