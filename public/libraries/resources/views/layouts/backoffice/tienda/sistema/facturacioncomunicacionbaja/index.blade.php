@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Comunicaci&oacute;n de Baja</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacioncomunicacionbaja/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
        <tr>
            <th width="85px">Correlativo</th>
            <th width="170px">Fecha de Generación</th>
            <th width="170px">Fecha de Comunicación</th>
            <th width="100px">RUC</th>
            <th>Emisor</th>
            <th width="140px">Comprobante</th>
            <th width="100px">Serie-Correlativo</th>
            <th width="100px">DNI/RUC</th>
            <th>Cliente</th>
            <th>Motivo</th>
            <th width="10px">SUNAT</th>
            <th width="10px"></th>
        </tr>
    </thead>
 <!--   @include('app.tablesearch',[
        'searchs'=>['codigoventa','date:fechaemision','serie','correlativo','clienteidentificacion','cliente','moneda'],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/facturacioncomunicacionbaja')
    ])-->
    <tbody>
      @foreach($facturacioncomunicacionbaja as $value)
        <tr>
          <td>{{ str_pad($value->comunicacionbaja_correlativo, 8, "0", STR_PAD_LEFT) }}</td>
          <td>{{ date_format(date_create($value->comunicacionbaja_fechageneracion), 'd/m/Y h:i:s A') }}</td>
          <td>{{ date_format(date_create($value->comunicacionbaja_fechacomunicacion), 'd/m/Y h:i:s A') }}</td>
          <td>{{ $value->emisor_ruc }}</td>
          <td>{{ $value->emisor_nombrecomercial }}</td>
          <td>{{ $value->tipodocumento=='01'?'FACTURA':($value->tipodocumento=='07'?'NOTA DE CRÉDITO':'---') }}</td>
          <td>{{ $value->serie }}-{{ $value->correlativo }}</td>
          <td>{{ $value->clienteidentificacion }}</td>
          <td>{{ $value->cliente }}</td>
          <td>{{ $value->descripcionmotivobaja }}</td>
          <td>
            @if($value->respuestaestado=='ACEPTADA')
              <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Aceptada</span></div>
            @elseif($value->respuestaestado=='OBSERVACIONES')
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Observaciones</span></div> 
            @elseif($value->respuestaestado=='RECHAZADA')
              @if($value->respuestacodigo==2324)
              <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Aceptada</span></div>
              @else
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Rechazada</span></div> 
              @endif
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
                  <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacioncomunicacionbaja/'.$value->idfacturacioncomunicacionbaja.'/edit?view=detalle') }}"><i class="fa fa-list-alt"></i> Detalle</a></li>
                  
                  <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacioncomunicacionbaja/'.$value->idfacturacioncomunicacionbaja.'/edit?view=ticket') }}"><i class="fa fa-receipt"></i> Comprobante</a></li>
                </ul>
            </div>
          </td>
        </tr>
      @endforeach 
    </tbody>
</table>
   {{ $facturacioncomunicacionbaja->links('app.tablepagination', ['results' => $facturacioncomunicacionbaja]) }}
</div>
@endsection
