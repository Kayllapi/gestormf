@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Notas de Cr&eacute;ditos</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionnotacredito/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
        <tr>
            <th width="120px">Serie-Correlativo</th>
            <th width="70px">Total</th>
            <th width="100px">Fecha de Emisión</th>
            <th>DNI/RUC - Cliente</th>
            <th>RUC - Emisor (Razón Social)</th>
            <th width="90px">Comprobante Afectado</th>
            <th>Motivo</th>
            <th width="10px">Estado de Envio</th>
            <th width="10px"></th>
        </tr>
    </thead>
  <!--  @include('app.tablesearch',[
        'searchs'=>['codigoventa','date:fechaemision','serie','correlativo','clienteidentificacion','cliente','moneda'],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionnotacredito')
    ])-->
    <tbody>       
      @foreach($facturacionnotacredito as $value)
        <tr>
          <td>{{ $value->notacredito_serie }}-{{ str_pad($value->notacredito_correlativo, 8, "0", STR_PAD_LEFT) }}</td>
          <td>{{ $value->notacredito_montoimpuestoventa }} 
              @if($value->notacredito_tipomoneda=='PEN')
                  SOLES
              @elseif($value->notacredito_tipomoneda=='USD')
                  DOLARES
              @endif
          </td>
          <td>{{ date_format(date_create($value->notacredito_fechaemision), 'd/m/Y h:i:s A') }}</td>
          <td>{{ $value->cliente_numerodocumento }} - {{ $value->cliente_razonsocial }}</td>
          <td>{{ $value->emisor_ruc }} - {{ $value->emisor_razonsocial }}</td>
          <td>
              @if($value->notacredito_tipodocafectado=='03')
                  BOLETA
              @elseif($value->notacredito_tipodocafectado=='01')
                  FACTURA
              @endif
              <br>
              {{ $value->notacredito_numerodocumentoafectado}}
          </td>
          <td>{{ $value->notacredito_descripcionmotivo}}</td>
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
              @if($value->respuestacodigo==1033)
              <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Aceptada</span></div>
              @else
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> No enviado</span></div>
              @endif
            @endif
          </td>
          <td>
            <div class="header-user-menu menu-option" id="menu-opcion">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <ul>
                  <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionnotacredito/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-list-alt"></i> Detalle</a></li>
                  <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionnotacredito/'.$value->id.'/edit?view=ticket') }}"><i class="fa fa-receipt"></i> Comprobante</a></li>
                </ul>
            </div>
          </td>
        </tr>
      @endforeach 
    </tbody>
</table>
  {{ $facturacionnotacredito->links('app.tablepagination', ['results' => $facturacionnotacredito]) }}
</div>
@endsection
