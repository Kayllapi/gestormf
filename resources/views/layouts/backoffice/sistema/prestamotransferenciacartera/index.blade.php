@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Registros y Transferencias de Clientes</span>
    <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamotransferenciacartera/create') }}"><i class="fa fa-angle-right"></i> Transferir</a>
  </div>
</div>
<div class="table-responsive">
  <table class="table" id="tabla-contenido">
      <thead class="thead-dark">
        <tr>
          <th>Fecha de Registro</th>
          <th>Tipo</th>
          <th>Responsable Anterior</th>
          <th>Responsable Actual</th>
          <th>Cliente</th>
          <th width='10px'></th>
        </tr>
      </thead>
      @include('app.tablesearch',[
          'searchs'=>['date:fecharegistro','select:tipo/1=REGISTRADO,2=TRANSFERIDO','responsableregistro','responsablerecepcion','cliente'],
          'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/prestamotransferenciacartera')
      ])
      <tbody>
        @foreach ($transferenciacarteras as $value)
          <tr>
            <td>{{ date_format(date_create($value->fecharegistro),"d/m/Y h:i:s A") }}</td>
            <td>
              @if($value->idestadotransferenciacartera==1)
              REGISTRADO
              @else
              TRANSFERIDO
              @endif
            </td>
            <td>{{ $value->asesororigen }}</td>
            <td>{{ $value->asesordestino }}</td>
            <td>{{ $value->cliente }}</td>
            <td>
              <div class="header-user-menu menu-option" id="menu-opcion">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <ul>
                      <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamotransferenciacartera/'.$value->id.'/edit?view=detalle') }}"><i class="fa fa-list"></i> Detalle</a></li>
                  </ul>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
  </table>
</div>
{{ $transferenciacarteras->links('app.tablepagination', ['results' => $transferenciacarteras]) }}
@endsection
@section('subscripts')
@endsection