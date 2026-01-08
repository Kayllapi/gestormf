@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>PERMISOS</span>
      <a class="btn btn-warning" href="{{ url('backoffice/permiso/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
          <tr>
            <th>Nombre</th>
            <th>Categoria</th>
            <th>Tipo</th>
            <th>Módulos</th>
            <th width="10px"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($permisos as $value)
          <?php $countrolesmodulos = DB::table('rolesmodulo')->where('idroles',$value->id)->count();?>
            <tr>
              <td>{{ $value->description }}</td>
              <td>{{ $value->categorianombre }}</td>
              <td>
                @if($value->idtipo==1)
                    Master
                @else
                    Sistema
                @endif
              </td>
              <td>{{ $countrolesmodulos }}</td>
              <td>
                <div class="dropdown">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <div class="dropdown-content">
                    <a href="{{ url('backoffice/permiso/'.$value->id.'/edit?view=editarmodulo') }}"><i class="fa fa-list-alt"></i> Módulos</a>
                    <a href="{{ url('backoffice/permiso/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a>
                   
                    <a href="{{ url('backoffice/permiso/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a>
              
                  </div>
                </div>
          </td>
            </tr>
          @endforeach
        </tbody>
    </table>
</div>    
@endsection