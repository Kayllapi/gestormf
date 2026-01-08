@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Solicitudes de Descuento de Moras</span>
    <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomorasolicitud/create?view=create') }}"><i class="fa fa-angle-right"></i> Registrar</a>
  </div>
</div>
<div class="table-responsive">
  <table class="table" id="tabla-contenido">
      <thead class="thead-dark">
        <tr>
          <th>Cod. Crédito</th>
          <th>DNI</th>
          <th>Cliente</th>
          <th>Descuento Mora</th>
          <th>Motivo</th>
          <th>Fecha de Solicitud</th>
          <th width = '10px'>Estado de Mora</th>
          <th width = '10px'></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($moras as $value)
          <tr>
            <td>{{ str_pad($value->creditocodigo, 8, "0", STR_PAD_LEFT) }}</td>
            <td>{{ $value->identificacion_cliente }}</td>
            <td>{{ $value->apellidos_cliente }}, {{ $value->nombre_cliente }}</td>
            <td>{{ $value->total_moradescuento }}</td>
            <td>{{ $value->motivo }}</td>
            <td>{{ date_format(date_create($value->fecharegistro),"d/m/Y h:i A") }}</td>
            <td>
              @if ($value->idestadomora == 1)
                  @if($value->idestadoaprobacion==2)
                      <span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Rechazado</span>
                  @elseif($value->idestadoaprobacion==3)
                      <span class="badge badge-pill badge-danger"><i class="fa fa-ban"></i> Anulado</span>
                  @else
                      <span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span>
                  @endif
              @elseif ($value->idestadomora == 2)
                  <span class="badge badge-pill badge-primary"><i class="fa fa-sync-alt"></i> Solicitando</span>
              @elseif ($value->idestadomora == 3)
                  <span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Aprobado</span>
              @endif
            </td>
            <td>
              <div class="header-user-menu menu-option" id="menu-opcion">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <ul>
                    @if ($value->idestadomora == 1)
                        @if($value->idestadoaprobacion==3)
                            <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomorasolicitud/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-list"></i> Detalle</a></li>
                        @else
                            <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomorasolicitud/'.$value->id.'/edit?view=confirmar') }}"><i class="fa fa-check"></i> Solicitar</a></li>
                        <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomorasolicitud/'.$value->id.'/edit?view=anular') }}"><i class="fa fa-trash"></i> Anular</a></li>
                        @endif
                    @else
                        <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomorasolicitud/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-list"></i> Detalle</a></li>
                    @endif
                  </ul>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
  </table>
</div>
{{ $moras->links('app.tablepagination', ['results' => $moras]) }}
@endsection