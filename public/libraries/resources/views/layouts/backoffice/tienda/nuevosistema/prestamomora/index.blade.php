@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Moras</span>
    <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomora/create') }}"><i class="fa fa-angle-right"></i> Registrar</a>
  </div>
</div>
<div class="table-responsive">
  <table class="table" id="tabla-contenido">
      <thead class="thead-dark">
        <tr>
          <th>Fecha Registro</th>
          <th>Fecha Confirmación</th>
          <th>Fecha Anulación</th>
          <th>Cliente</th>
          <th>Descuento Mora</th>
          <th>Motivo</th>
          <th>Registro</th>
          <th>Confirmación</th>
          <th width = '10px'>Estado</th>
          <th width = '10px'></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($moras as $value)
          <tr>
            <td>{{ date_format(date_create($value->fecharegistro),"d/m/Y h:i A") }}</td>
            <td>{{ !is_null($value->fechaconfirmacion) ? date_format(date_create($value->fechaconfirmacion),"d/m/Y h:i A") : '---' }}</td>
            <td>{{ !is_null($value->fechaanulacion) ? date_format(date_create($value->fechaanulacion),"d/m/Y h:i A") : '---' }}</td>
            <td>{{ $value->apellidos_cliente }}, {{ $value->nombre_cliente }}</td>
            <td>{{ $value->monto }}</td>
            <td>{{ $value->motivo }}</td>
            <td>{{ $value->nombre_responsableregistro }}</td>
            <td>{{ $value->nombre_responsableconfirmacion }}</td>
            <td>
              @if ($value->idestado == 1)
                <span class="badge badge-pill badge-info"><i class="fa fa-sync-alt"></i> Pendiente</span>
              @elseif ($value->idestado == 2)
                <span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span>
              @elseif ($value->idestado == 3)
                <span class="badge badge-pill badge-dark"><i class="fa fa-ban"></i> Anulado</span>
              @endif
            </td>
            <td>
              <div class="header-user-menu menu-option" id="menu-opcion">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <ul>
                    @if ($value->idestado == 1)
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomora/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a></li>
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomora/'.$value->id.'/edit?view=confirmar') }}"><i class="fa fa-check"></i> Confirmar</a></li>
                    @elseif ($value->idestado == 2)
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomora/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-list"></i> Detalle</a></li>
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomora/'.$value->id.'/edit?view=anular') }}"><i class="fa fa-ban"></i> Anular</a></li>
                    @elseif ($value->idestado == 3)
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamomora/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-list"></i> Detalle</a></li>
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
@section('subscripts')
@endsection