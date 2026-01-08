@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Notas de D&eacute;bito</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionnotadebito/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
        <tr>
            <th width="45px">Serie</th>
            <th width="85px">Correlativo</th>
            <th width="80px">Base Imp.</th>
            <th width="80px">IGV</th>
            <th width="80px">Total</th>
            <th width="85px">Moneda</th>
            <th width="170px">Fecha de Emisión</th>
            <th width="85px">DNI/RUC</th>
            <th>Cliente</th>
            <th width="100px">RUC</th>
            <th>Emisor</th>
            <th width="100px">Responsable</th>
            <th width="90px">Modificado</th>
            <th>Motivo</th>
            <th width="10px">SUNAT</th>
            <th width="10px"></th>
        </tr>
    </thead>
    <tbody>       
      @foreach($facturacionnotadebito as $value)
        <tr>
          <td>{{ $value->notadebito_serie }}</td>
          <td>{{ str_pad($value->notadebito_correlativo, 8, "0", STR_PAD_LEFT) }}</td>
          <td>{{ $value->notadebito_valorventa }}</td>
          <td>{{ $value->notadebito_totalimpuestos }}</td>
          <td>{{ $value->notadebito_montoimpuestoventa }}</td>
          <td>
              @if($value->notadebito_tipomoneda=='PEN')
                  SOLES
              @elseif($value->notadebito_tipomoneda=='USD')
                  DOLARES
              @endif
          </td>
          <td>{{ date_format(date_create($value->notadebito_fechaemision), 'd/m/Y h:i:s A') }}</td>
          <td>{{ $value->cliente_numerodocumento }}</td>
          <td>{{ $value->cliente_razonsocial }}</td>
          <td>{{ $value->emisor_ruc }}</td>
          <td>{{ $value->emisor_nombrecomercial }}</td>
          <td>{{ $value->responsablenombre }}</td>
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
          <td>
            <div class="header-user-menu menu-option" id="menu-opcion">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <ul>
                  <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionnotadebito/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-list-alt"></i> Detalle</a></li>
                  <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionnotadebito/'.$value->id.'/edit?view=ticket') }}"><i class="fa fa-receipt"></i> Comprobante</a></li>
                </ul>
            </div>
          </td>
        </tr>
      @endforeach 
    </tbody>
</table>
  {{ $facturacionnotadebito->links('app.tablepagination', ['results' => $facturacionnotadebito]) }}
</div>
@endsection