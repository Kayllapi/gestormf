@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Accesos / {{ $usuario->nombre }}</span>
      <a class="btn btn-success" href="{{ url('backoffice/cineusuario') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
      <a class="btn btn-warning" href="{{ url('backoffice/cineusuario/'.$usuario->id.'/edit?view=accesoregistrar') }}"><i class="fa fa-angle-right"></i> Ampliar Acceso</a></a>
    </div>
</div> 
<div class="table-responsive">
      <table class="table" id="tabla-contenido">
          <thead class="thead-dark">
            <tr>
              <th>Fecha de registro</th>
              <th>Días de regalo</th>
              <th>Fecha de Inicio</th>
              <th>Fecha de Fin</th>
              <th>Estado</th>
              <th width="10px"></th>
            </tr>
          </thead>
          <tbody>
            @foreach($userscines as $value)
              <tr>
                <td>{{ $value->fecharegistro }}</td>
                <td>{{ $value->diasprueba }}</td>
                <td>{{ $value->fechainicio }}</td>
                <td>{{ $value->fechafin }}</td>
                <td>
                  @if($value->idestado==2)
                    <div class="td-badge"><span class="badge badge-pill badge-danger"><i class="fas fa-ban"></i> Anulado</span></div> 
                  @else
                    <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Correcto</span></div>
                  @endif
                </td>
                <td>
                  <div class="dropdown">
                    <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                    <div class="dropdown-content">
                          <a href="{{ url('backoffice/cineusuario/'.$usuario->id.'/edit?view=accesodetalle&idacceso='.$value->id) }}"><i class="fa fa-list-alt"></i> Detalle</a>
                          @if($value->idestado!=2)
                          <!--a href="{{ url('backoffice/cineusuario/'.$usuario->id.'/edit?view=accesoanular&idacceso='.$value->id) }}"><i class="fa fa-ban"></i> Anular</a-->
                          @endif
                    </div>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
      </table>
  </div>
{{ $userscines->links('app.tablepagination', ['results' => $userscines]) }}
@endsection