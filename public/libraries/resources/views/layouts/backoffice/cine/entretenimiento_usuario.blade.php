@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="mx-contpuntskay">
  <i class="fa fa-users"></i> 11 Usuarios
  <br>
  <a href="javascript:;" id="modal-recepcionar-kay">
    <span class="badge badge-pill badge-light" style="font-size: 14px;margin-top: 5px;">
    <i class="fa fa-user"></i> 0 Activos</span>
  </a>
  <a href="javascript:;" id="modal-recepcionar-kay">
    <span class="badge badge-pill badge-light" style="font-size: 14px;margin-top: 5px;">
    <i class="fa fa-user"></i> 11 Libres</span>
  </a>
</div>
<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Usuarios</span>
    <a class="btn btn-warning" href="{{ url('backoffice/cineusuario/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
  </div>
</div>
<div class="table-responsive">
  <table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th>Nombre</th>
        <th>Usuario</th>
        <th>Teléfono</th>
        <th width="10px">Estado</th>
        <th width="10px"></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($invitados as $value)
        <tr>
          <td>{{ $value->nombre }}</td>
          <td>{{ $value->usuario }}</td>
          <td>{{ $value->numerotelefono }}</td>
          <td>
            <?php
              $estado_vaucher = DB::table('userscine')->where('userscine.idusers', $value->id)->first();
            ?>
            @if (!is_null($estado_vaucher))
              @if ($estado_vaucher->idestado == 1)
                <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-syn"></i> Sin confirmar</span></div>
              @elseif ($estado_vaucher->idestado == 2)
                <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fa fa-check"></i> Confirmado</span></div>
              @else
                <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-ban"></i> Sin Vaucher</span></div>
              @endif
            @else
              <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-ban"></i> Sin Vaucher</span></div>
            @endif
          </td>
          <td>
            <div class="dropdown">
              <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
              <div class="dropdown-content">
                @if (!is_null($estado_vaucher))
                  @if ($estado_vaucher->idestado == 1)
                    <a href="{{ url('backoffice/cine/'.$value->id.'/edit?view=entretenimiento_confirmarvaucher') }}"><i class="fa fa-check"></i> Confirmar</a>
                  @elseif ($estado_vaucher->idestado == 2)
                    <a href="{{ url('backoffice/cine/'.$value->id.'/edit?view=entretenimiento_detallevaucher') }}"><i class="fa fa-detail"></i> Detalle</a>
                  @endif
                @else
                  <a href="{{ url('backoffice/cine/'.$value->id.'/edit?view=entretenimiento_habilitarvaucher') }}"><i class="fa fa-edit"></i> Habilitar Vaucher</a>
                @endif
              </div>
            </div>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  {{ $invitados->links('app.tablepagination', ['results' => $invitados]) }}
</div>
@endsection
@section('scriptsbackoffice')
@endsection