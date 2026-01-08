@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Refinanciaciones</span>
    <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamorefinanciacion/create') }}"><i class="fa fa-angle-right"></i> Registrar</a>
  </div>
</div>
<div class="table-responsive">
  <table class="table" id="tabla-contenido">
      <thead class="thead-dark">
        <tr>
          <th>Fecha Pre-Aprobado</th>
          <th>Monto</th>
          <th>Cuotas</th>
          <th>Fecha de Inicio</th>
          <th>Frecuencia</th>
          <th>Tasa</th>
          <th>Interes</th>
          <th>Asesor</th>
          <th>Cliente</th>
          <th width = '10px'>Estado</th>
          <th width = '10px'></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($refinanciaciones as $value)
          <tr>
            <td>{{ $value->fecharegistro }}</td>
            <td>{{ $value->monto }}</td>
            <td>{{ $value->numerocuota }}</td>
            <td>{{ $value->fechainicio }}</td>
            <td>{{ $value->frecuencia_nombre }}</td>
            <td>
              @if ($value->idprestamo_tipotasa==1)
                    Fija
              @elseif ($value->idprestamo_tipotasa==2)
                    Efectiva
              @endif
            </td>
            <td>{{ $value->tasa }}</td>
            <td>{{ $value->asesor_nombre }}</td>
            <td>{{ $value->cliente }}</td>
            <td>
              @if ($value->idestadocredito==2)
                    <span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Correcto</span>
              @endif
            </td>
            <td>
              <div class="header-user-menu menu-option" id="menu-opcion">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <ul>
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamorefinanciacion/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-list"></i> Detalle</a></li>
                  </ul>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
  </table>
</div>
{{ $refinanciaciones->links('app.tablepagination', ['results' => $refinanciaciones]) }}
@endsection
@section('subscripts')
@endsection