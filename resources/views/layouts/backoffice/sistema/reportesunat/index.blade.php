@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Reporte de SUNAT</span>
    </div>
</div>
<form action="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/reportesunat') }}" method="GET"> 
    <div class="custom-form">
      <div class="row">
         <div class="col-md-6">
            <label>Tipo de Comprobante</label>
              <select id="comprobante" name="comprobante">
                <option></option>
                <option value="01">Factura</option>
                <option value="03">Boleta</option>
                <option value="07">Nota de Credito</option>
                <option value="08">Nota de Debito</option>
                <option value="09">Guia Remisión</option>
                <option value="RA">Comunicacion Baja</option>
                <option value="RC">Resumen Diario</option>
              </select>
           <label>Estado</label>
            <select id="idestado" name="idestado">
                <option></option>
                <option value="ACEPTADA">ACEPTADA</option>
                <option value="OBSERVACIONES">OBSERVACIONES</option>
                <option value="RECHAZADA">RECHAZADA</option>
                <option value="EXCEPCION">EXCEPCION</option>
                <option value="ERROR">ERROR</option>
            </select>
         </div>
         <div class="col-md-6">
            <label>Fecha inicio</label>
              <input type="date" name="fechainicio" id="fechainicio" value="{{isset($_GET['fechainicio'])?($_GET['fechainicio']!=''?$_GET['fechainicio']:''):''}}">
            <label>Fecha fin</label>
              <input type="date" name="fechafin" id="fechafin" value="{{isset($_GET['fechafin'])?($_GET['fechafin']!=''?$_GET['fechafin']:''):''}}">
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
        <th width="200px">Fecha de Registro</th>
        <th>Código</th>
        <th>Agencia</th>
        <th>Mensaje</th>
        <th>Comprobante</th>
        <th>Serie</th>
        <th>Correlativo</th>
        <th width="10px">Estado</th>
      </tr>
    </thead>
    <tbody>
        @foreach($facturacionrespuesta as $value)
        <?php 
        $data = ''; 
        if (!is_null($value->s_idfacturacionboletafactura)){
          $data = DB::table('s_facturacionboletafactura as data')
            ->whereId($value->s_idfacturacionboletafactura)
            ->select(
              'data.*',
              'data.venta_serie as emisor_serie',
              'data.venta_correlativo as emisor_correlativo'
            )
            ->first();
        }
        elseif (!is_null($value->s_idfacturacionnotacredito)) {
          $data = DB::table('s_facturacionnotacredito as data')
            ->whereId($value->s_idfacturacionnotacredito)
            ->select(
              'data.*',
              'data.notacredito_serie as emisor_serie',
              'data.notacredito_correlativo as emisor_correlativo'
            )
            ->first();
        }
        elseif (!is_null($value->s_idfacturacionresumendiario)) {
          $data = DB::table('s_facturacionresumendiario as data')
            ->whereId($value->s_idfacturacionresumendiario)
            ->select(
              'data.*',
              'data.resumen_correlativo as emisor_correlativo'
            )
            ->first();
        }
        elseif (!is_null($value->s_idfacturacioncomunicacionbaja)) {
          $data = DB::table('s_facturacioncomunicacionbaja as data')
            ->whereId($value->s_idfacturacionboletafactura)
            ->select(
              'data.*',
              'data.comunicacionbaja_correlativo as emisor_correlativo'
            )
            ->first();
        }
        elseif (!is_null($value->s_idfacturacionnotadebito)) {
          $data = DB::table('s_facturacionnotadebito as data')
            ->whereId($value->s_idfacturacionnotadebito)
            ->select(
              'data.*',
              'data.notadebito_serie as emisor_serie',
              'data.notadebito_correlativo as emisor_correlativo'
            )
            ->first();
        }
        elseif ((!is_null($value->s_idfacturacionguiaremision))) {
          $data = DB::table('s_facturacionguiaremision as data')
            ->whereId($value->s_idfacturacionboletafactura)
            ->select(
              'data.*',
              'data.despacho_serie as emisor_serie',
              'data.despacho_correlativo as emisor_correlativo'
            )
            ->first();
        }
        ?>
        <tr>
          <td>{{ date_format(date_create($value->fecharegistro),"d/m/Y h:i:s A")}}</td>
          <td>{{ $value->codigo }}</td>
          <td>{{ $data->emisor_ruc ?? '' }} - {{ $data->emisor_razonsocial ?? '' }}</td>
          <td>{{ $value->mensaje }}</td>
          <td>
               @if($value->venta_tipodocumento == '01')
                     FACTURA
               @elseif($value->venta_tipodocumento == '03')
                     BOLETA 
               @elseif($value->notacredito_tipodocumento == '07')
                     NOTA CREDITO
               @elseif($value->notadebito_tipodocumento == '08')
                     NOTA DEBITO
               @elseif($value->despacho_tipodocumento == '09')
                     GUIA REMISIÓN
            @endif
          </td>
          <td>{{ $data->emisor_serie ?? '' }}</td>
          <td>{{ $data->emisor_correlativo ?? '' }}</td>
          <td>
              @if($value->estado == 'ACEPTADA')
                     ACEPTADA
               @elseif($value->estado == 'OBSERVACIONES')
                     OBSERVACIONES 
               @elseif($value->estado == 'RECHAZADA')
                     RECHAZADA
               @elseif($value->estado == 'EXCEPCION')
                     EXCEPCION
               @else
                     ERROR
               @endif 
          </td>
        </tr>
        @endforeach
     </tbody>
</table>
{{ $facturacionrespuesta->links('app.tablepagination', ['results' => $facturacionrespuesta]) }}
</div>
<style>
 td{
    padding: 9px !important;
    }
</style>
@endsection
@section('subscripts')
<script>
   function reporte(tipo){
        window.location.href = '{{url('backoffice/tienda/sistema/'.$tienda->id.'/reportesunat')}}?'+
                                'tipo='+tipo+
                                '&comprobante='+($('#comprobante').val()!=null?$('#comprobante').val():'')+
                                '&idestado='+($('#idestado').val()!=null?$('#idestado').val():'')+
                                '&fechainicio='+$('#fechainicio').val()+
                                '&fechafin='+$('#fechafin').val();
    }
  $("#comprobante").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
    }).val({{isset($_GET['comprobante'])?($_GET['comprobante']!=''?$_GET['comprobante']:'0'):'0'}}).trigger("change");
  
  $("#idestado").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
    allowClear: true
    }).val({{isset($_GET['idestado'])?($_GET['idestado']!=''?$_GET['idestado']:'0'):'0'}}).trigger("change");
  
</script>
@endsection
