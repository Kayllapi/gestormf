@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Boletas y Facturas</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionboletafactura/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th width="100px">Fecha de Emisión</th>
        <th width="120px">Comprobante</th>
        <th width="70px">Total</th>
        <th>DNI/RUC - Cliente</th>
        <th>RUC - Emisor (Razón Social)</th>
        <th width="90px">Código de Venta</th>
        <th width="10px">Estado de Envio</th>
        <th width="10px"></th> 
      </tr>
    </thead>
     <!-- @include('app.tablesearch',[
        'searchs'=>['','date:fechaemision','responsable','select:comprobante/01=FACTURA,03=BOLETA','serie','correlativo','clienteidentificacion','cliente','moneda'],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionboletafactura')
      ])-->
    <tbody>
      @foreach($facturacionboletafactura as $value)
        <tr>
          <td>
            {{ date_format(date_create($value->venta_fechaemision), 'd/m/Y') }}
            <br>
            {{ date_format(date_create($value->venta_fechaemision), 'h:i:s A') }}
          </td>
          <td>
              @if($value->venta_tipodocumento=='03')
                  BOLETA
              @elseif($value->venta_tipodocumento=='01')
                  FACTURA
              @endif
              <br>
              {{ $value->venta_serie }}-{{ str_pad($value->venta_correlativo, 8, "0", STR_PAD_LEFT) }}
          </td>
          <td>
              {{ $value->venta_montoimpuestoventa }} 
              <br>
              @if($value->venta_tipomoneda=='PEN')
                  SOLES
              @elseif($value->venta_tipomoneda=='USD')
                  DOLARES
              @endif
          </td>
          <td>
            {{ $value->cliente_numerodocumento }} -
            {{ $value->cliente_razonsocial }}
          </td>
          <td>
            {{ $value->emisor_ruc }} -
            {{ $value->emisor_razonsocial }}
          </td>
          <td>{{ $value->idventa !='' ? str_pad($value->idventa, 8, "0", STR_PAD_LEFT) : '' }}</td>
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
          <td>
            <div class="header-user-menu menu-option" id="menu-opcion">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <ul>  
                  <li>
                    <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionboletafactura/'.$value->id.'/edit?view=detalle') }}">
                      <i class="fa fa-list-alt"></i> Detalle
                    </a>
                  </li>
                  <li>
                    <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionboletafactura/'.$value->id.'/edit?view=ticket') }}">
                      <i class="fa fa-receipt"></i> Comprobante
                    </a>
                  </li>
                  <!--li>
                    <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionboletafactura/'.$value->id.'/edit?view=delete') }}">
                      <i class="fa fa-trash"></i> Eliminar
                    </a>
                  </li-->
                </ul>
            </div>
          </td>
        </tr>
      @endforeach 
    </tbody>
</table>
   {{ $facturacionboletafactura->links('app.tablepagination', ['results' => $facturacionboletafactura]) }}
</div>
@endsection
