@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Preprogramaciones</span>
    <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoreprogramacion/create') }}"><i class="fa fa-angle-right"></i> Registrar</a>
  </div>
</div>
<div class="table-responsive">
  <table class="table" id="tabla-contenido">
      <thead class="thead-dark">
        <tr>
          <th>Cliente</th>
          <th>Fecha reprogramado</th>
          <th>Motivo</th>
          <th width='10px'>Estado</th>
          <th width='10px'></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($reprogramaciones as $value)
          <tr>
            <td>{{ $value->cliente }}</td>
            <td>{{ $value->fechainicio }}</td>
            <td>{{ $value->motivo }}</td>
            <td>
              @if ($value->idestado==1)
                    <span class="badge badge-pill badge-info"><i class="fa fa-sync"></i> Pendiente</span>
              @elseif ($value->idestado==2)
                    <span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Correcto</span>
              @endif
            </td>
            <td>
              <div class="header-user-menu menu-option" id="menu-opcion">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <ul>
                      @if ($value->idestado==1)
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoreprogramacion/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a></li>
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoreprogramacion/'.$value->id.'/edit?view=confirmar') }}"><i class="fa fa-check"></i> Confirmar</a></li>
                      @elseif ($value->idestado==2)
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoreprogramacion/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-list"></i> Detalle</a></li>
                      @endif
                  </ul>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
  </table>
</div>
{{ $reprogramaciones->links('app.tablepagination', ['results' => $reprogramaciones]) }}
@endsection
@section('subscripts')
@endsection