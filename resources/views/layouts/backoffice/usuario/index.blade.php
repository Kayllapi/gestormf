@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Usuarios</span>
      <a class="btn btn-warning" href="{{ url('backoffice/usuario/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
          <tr>
            <th>Apellidos y Nombres</th>
            <th>Correo Electrónico (Usuario)</th>
            <th>Permiso / Acceso</th>
            <th width="10px">Estado</th>
            <th width="10px"></th>
          </tr>
        </thead>
        @include('app.tablesearch',[
            'searchs'=>['cliente','usuario','','',''],
            'search_url'=> url('backoffice/usuario')
        ])
        <tbody>
          @foreach($usuarios as $value)
            <tr>
              <td>{{ $value->nombrecompleto }}</td>
              <td>{{ $value->usuario }}</td>
              <td>{{ $value->descriptionrole }} / {{ $value->idestadousuario==1?'Activado':'Desactivado' }}</td>
              <td>
                @if($value->email_verified_at!='')
                  <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>
                @else
                  <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Pendiente</span></div> 
                @endif
              </td>
              <td>
                <div class="dropdown">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <div class="dropdown-content">
                    <?php $page = (isset($_GET['page'])?'&page='.$_GET['page']:'') ?>
                    @if($value->email_verified_at!='')
                        <a href="{{ url('backoffice/usuario/'.$value->id.'/edit?view=tienda'.$page) }}"><i class="fa fa-home"></i> Tiendas</a>
                        <a href="{{ url('backoffice/usuario/'.$value->id.'/edit?view=editarusuario'.$page) }}"><i class="fa fa-edit"></i> Editar</a>
                    @else
                        <a href="{{ url('backoffice/usuario/'.$value->id.'/edit?view=confirmar'.$page) }}"><i class="fas fa-check"></i> Confirmar</a>
                        <a href="{{ url('backoffice/usuario/'.$value->id.'/edit?view=editarusuario'.$page) }}"><i class="fa fa-edit"></i> Editar</a>
                    @endif 
                    <a href="{{ url('backoffice/usuario/'.$value->id.'/edit?view=eliminar'.$page) }}"><i class="fa fa-trash"></i> Eliminar</a>
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
