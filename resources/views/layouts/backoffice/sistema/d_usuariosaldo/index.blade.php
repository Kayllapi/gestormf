@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Saldo de Usuarios',
    'botones'=>[
        'registrar:/usuariosaldo/create: Registrar'
    ]
])
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th width="150px">Fecha Registro</th>
        <th width="10px">RUC</th>
        <th>Usuario Asignado</th>
        <th width="100px">Monto</th>
        <th width="100px">Responsable</th>
        <th width="200px">Motivo</th>
        <th width="150px">Fecha Anulación </th>
        <th width="10px">Estado</th>
        <th width="10px"></th>
      </tr>
    </thead>
    <tbody>
      @foreach($usuariosaldo as $value)
          <td>{{ $value->fecharegistro }}</td>
          <td>{{ $value->usuariosaldoruc }}</td>
          <td>{{ $value->usuariosaldoapellidos }},{{ $value->usuariosaldonombre }}</td>
          <td>{{ $value->monto }}</td>
          <td>{{ $value->responsablenombre }}</td>
          <td>{{ $value->motivo }}</td>
          <td>{{ $value->fechaanulacion }}</td>
          <td>
              @if($value->idestado==1)
                <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Registrado</span></div>
              @else
                <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fas fa-ban"></i> Anulado</span></div> 
              @endif
          </td>
          <td>
            <div class="header-user-menu menu-option" id="menu-opcion">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <ul> 
                 @if($value->idestado==1)
                  <li>
                    <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/usuariosaldo/'.$value->id.'/edit?view=detalle') }}">
                      <i class="fa fa-list-alt"></i> Detalle
                    </a>
                  </li>
                  <li>
                    <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/usuariosaldo/'.$value->id.'/edit?view=anular') }}">
                      <i class="fa fa-ban"></i> Anular
                    </a>
                  </li>
                 @else
                  <li>
                    <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/usuariosaldo/'.$value->id.'/edit?view=detalle') }}">
                      <i class="fa fa-list-alt"></i> Detalle
                    </a>
                  </li>
                 @endif 
                </ul>
            </div>
          </td>
        </tr>
      @endforeach 
    </tbody>
</table>
</div>
{{ $usuariosaldo->links('app.tablepagination', ['results' => $usuariosaldo]) }}
@endsection
