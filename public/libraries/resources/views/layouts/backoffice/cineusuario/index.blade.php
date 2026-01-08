@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<?php
$usuario_activo = count($usuarios);
$planadquirido = planadquirido(Auth::user()->id);
$usuario_cantidad = 0;
if($planadquirido['data']!=''){
$usuario_cantidad = $planadquirido['data']->cantuserscine;
}
$usuario_libre = $usuario_cantidad-$usuario_activo;
?>
<div class="mx-contpuntskay">
  <i class="fa fa-users"></i> {{$usuario_cantidad}} Usuarios
  <br><!--a href="javascript:;" id="modal-comprar-kay">
  <span class="badge badge-pill badge-warning" style="font-size: 14px;margin-top: 5px;">
      <i class="fa fa-shopping-cart"></i> Comprar Usuarios</span></a-->

  <a href="javascript:;" id="modal-recepcionar-kay">
    <span class="badge badge-pill badge-light" style="font-size: 14px;margin-top: 5px;">
    <i class="fa fa-user"></i> {{$usuario_activo}} Activos</span></a>
  <a href="javascript:;" id="modal-recepcionar-kay">
    <span class="badge badge-pill badge-light" style="font-size: 14px;margin-top: 5px;">
    <i class="fa fa-user"></i> {{$usuario_libre}} Libres</span></a>

</div>
  <div class="list-single-main-wrapper fl-wrap">
      <div class="breadcrumbs gradient-bg fl-wrap">
        <span>Usuarios</span>
        <!--a class="btn btn-warning" href="{{ url('backoffice/cineusuario/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a-->
      </div>
  </div>
  <div class="table-responsive">
      <table class="table" id="tabla-contenido">
          <thead class="thead-dark">
            <tr>
              <th>Nombre</th>
              <th>Usuario</th>
              <th>Fecha Inicio</th>
              <th>Fecha Fin</th>
              <th width="10px">Entretenimiento</th>
              <th width="10px"></th>
            </tr>
          </thead>
            @include('app.tablesearch',[
                  'searchs'=>['nombre','usuario','',''],
                  'search_url'=> url('backoffice/usuario')
              ])
          <tbody>
            @foreach($usuarios as $value)
              <tr>
                <td>{{ $value->usuarionombre }}</td>
                <td>{{ $value->usuario }}</td>
                <td>{{ $value->fechainicio }}</td>
                <td>{{ $value->fechafin }}</td>
                <td>
                  @if($value->idestadoentretenimiento==1)
                    <div class="td-badge"><span class="badge badge-pill badge-primary">Pendiente</span></div> 
                  @elseif($value->idestadoentretenimiento==2)
                    <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Activado</span></div>
                  @elseif($value->idestadoentretenimiento==3)
                    <div class="td-badge"><span class="badge badge-pill badge-dark">Anulado</span></div> 
                  @endif
                  
                </td>
                <td>
                  <div class="dropdown">
                    <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                    <div class="dropdown-content">
                      @if($value->idestadoentretenimiento==1)
                          <a href="{{ url('backoffice/cineusuario/'.$value->id.'/edit?view=activarentretenimiento') }}"><i class="fa fa-check"></i> Activar Entretenimeinto</a>
                      @elseif($value->idestadoentretenimiento==2)
                          <a href="{{ url('backoffice/cineusuario/'.$value->id.'/edit?view=detalleentretenimiento') }}"><i class="fa fa-list-alt"></i> Detalle</a>
                      @endif
                    </div>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
      </table>
  </div>
{{ $usuarios->links('app.tablepagination', ['results' => $usuarios]) }}
@endsection