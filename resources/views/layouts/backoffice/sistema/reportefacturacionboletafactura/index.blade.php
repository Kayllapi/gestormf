@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte Boletas y Facturas</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reportefacturacionboletafactura') }}" method="GET"> 
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
            
            <label>Tipo de Comprobante</label>
                  <select id="tipoCompbrobante" name="tipoCompbrobante">
                      <option></option>
                      <option value="03">Boleta</option>
                      <option value="01">Factura</option>
                  </select>
           <label>Serie</label>
             <input class="form-control" type="text"  name="serie" id="serie" value="{{isset($_GET['serie'])?($_GET['serie']!=''?$_GET['serie']:''):''}}">
           <label>Correlativo</label>
             <input class="form-control" type="text"  name="correlativo" id="correlativo" value="{{isset($_GET['correlativo'])?($_GET['correlativo']!=''?$_GET['correlativo']:''):''}}">
            <label>Moneda</label>
               <select name="moneda" id="moneda">
                   <option></option>
                   @foreach($moneda as $value)
                   <option value="{{ $value->nombre }}"> {{ $value->nombre }}</option>
                   @endforeach
                </select>
          </div>
          <div class="col-md-6">
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
            <label>Estado</label>
            <select id="idestado" name="idestado">
                <option></option>
                <option value="aceptada">Aceptada</option>
                <option value="observaciones">Observaciones</option>
                <option value="rechazada">Rechazada</option>
                <option value="excepción">Excepción</option>
              <option value="error">No enviado</option>
            </select>
            <label>Código Venta</label>
                  <input class="form-control" type="text"  name="venta" id="venta"  value="{{isset($_GET['venta'])?($_GET['venta']!=''?$_GET['venta']:''):''}}">
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
            <div class="col-md-6">
                  <a href="javascript:;" onclick="reporte('excel_SUNAT')"class="btn  big-btn  color-bg flat-btn" style="margin-bottom:10px;"><i class="fa fa-file-excel"></i>  Exportar Excel SUNAT</a>
            </div>
          </div>
        </div>
    </div>
</form>
<div class="table-responsive">
  <table class="table" id="tabla-contenido">
      <thead class="thead-dark">
        <tr>
          <th>Comprobante</th>
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
          <th>Cod. Venta</th>
          <th>SUNAT</th>
        </tr>
      </thead>
      <tbody>
       @foreach($facturacionboletafactura as $value)
        <tr>
             <td>
               @if($value->venta_tipodocumento=='03')
                  BOLETA
              @elseif($value->venta_tipodocumento=='01')
                  FACTURA
              @else
                  TICKET
              @endif
             </td>
             <td>{{ $value->venta_serie}}</td>
             <td>{{ str_pad($value->venta_correlativo, 8, "0", STR_PAD_LEFT) }}</td>
             <td>{{ $value->venta_valorventa}}</td>
             <td>{{ $value->venta_totalimpuestos}}</td>
             <td>{{ $value->venta_montoimpuestoventa}}</td>
             <td>
               @if($value->venta_tipomoneda=='PEN')
                  SOLES
              @elseif($value->venta_tipomoneda=='USD')
                  DOLARES
              @endif
             </td>
             <td>{{ date_format(date_create($value->venta_fechaemision), 'd/m/Y h:i:s A') }}</td>
             <td>{{ $value->cliente_numerodocumento}}</td>
             <td>{{ $value->cliente_razonsocial}}</td>
             <td>{{ $value->emisor_ruc}}</td>
             <td>{{ $value->emisor_razonsocial}}</td>
             <td>{{ $value->responsablenombre}}</td>
             <td>{{ $value->ventacodigo!=''?str_pad($value->ventacodigo, 8, "0", STR_PAD_LEFT):'---' }}</td>
             <td>
              @if($value->comunicacionbaja_correlativo!='' && $value->venta_tipodocumento=='01')
                  <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado (CB: {{$value->comunicacionbaja_correlativo}})</span></div>   
              @elseif($value->resumen_correlativo!='' && $value->venta_tipodocumento=='03')
                  @if($value->resumen_estado=='1')
                    <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Adicionado (RD: {{$value->resumen_correlativo}})</span></div>  
                  @elseif($value->resumen_estado=='2')
                    <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Modificado (RD: {{$value->resumen_correlativo}})</span></div>  
                  @elseif($value->resumen_estado=='3')
                    <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado (RD: {{$value->resumen_correlativo}})</span></div>  
                  @endif  
              @else
                  @if($value->respuestaestado=='ACEPTADA')
                    <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Aceptada</span></div>
                  @elseif($value->respuestaestado=='OBSERVACIONES')
                    <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Observaciones</span></div> 
                  @elseif($value->respuestaestado=='RECHAZADA')
                    <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Rechazada</span></div> 
                  @elseif($value->respuestaestado=='EXCEPCION')
                    @if($value->respuestacodigo==1033)
                    <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Aceptada</span></div>
                    @else
                    <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-sync-alt"></i> Excepción</span></div>
                    @endif
                  @else
                    <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> No enviado</span></div>
                  @endif
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

{{ $facturacionboletafactura->links('app.tablepagination', ['results' => $facturacionboletafactura]) }}
@endsection
@section('subscripts')
<script>
     function reporte(tipo){
        window.location.href = '{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportefacturacionboletafactura')}}?'+
                                'tipo='+tipo+
                                '&idcliente='+($('#idcliente').val()!=null?$('#idcliente').val():'')+
                                '&idresponsable='+($('#idresponsable').val()!=null?$('#idresponsable').val():'')+
                                '&tipoCompbrobante='+($('#tipoCompbrobante').val()!=null?$('#tipoCompbrobante').val():'')+
                                '&moneda='+($('#moneda').val()!=null?$('#moneda').val():'')+
                                '&idagencia='+($('#idagencia').val()!=null?$('#idagencia').val():'')+
                                '&idestado='+($('#idestado').val()!=null?$('#idestado').val():'')+
                                '&venta='+$('#venta').val()+
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
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportefacturacionboletafactura/showlistarusuario')}}",
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
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportefacturacionboletafactura/showlistarusuario')}}",
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