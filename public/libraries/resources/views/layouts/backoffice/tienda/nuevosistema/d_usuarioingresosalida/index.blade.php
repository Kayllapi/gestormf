@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Horario de Entrada y Salida</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/usuarioingresosalida/create') }}"><i class="fa fa-angle-right"></i>Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th width="150px">Fecha de Registro</th>
        <th width="100px">DNI</th>
        <th>Usuario</th>
        <th width="100px">Acceso</th>
      </tr>
    </thead>
    <tbody>
        @foreach($horario as $value)
        <tr>
          <td>{{ $value->fecharegistro }}</td>
          <td>{{ $value->identificacionusuario }}</td>
          <td>{{ $value->apellidosusuario }}, {{ $value->nombreusuario }}</td>
          <td>
            <div class="header-user-menu menu-option" id="menu-opcion">
               <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
               <ul>
                  <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/usuarioingresosalida/'.$value->id.'/edit?view=detalle') }}">
                      <i class="fa fa-list-alt"></i> Detalle
                  </a>
               </ul>
            </div>
          </td>
        </tr>
        @endforeach
    </tbody>
</table>
  {{ $horario->links('app.tablepagination', ['results' => $horario]) }}
</div>
@endsection
