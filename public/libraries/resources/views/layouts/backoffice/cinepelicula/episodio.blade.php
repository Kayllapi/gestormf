@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>{{$cinepelicula->nombre}} / Episodios</span>
      <a class="btn btn-success" href="{{ url('backoffice/cinepelicula') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
      <a class="btn btn-warning" href="{{ url('backoffice/cinepelicula/'.$cinepelicula->id.'/edit?view=episodiocreate') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
          <tr>
            <th>Orden</th>
            <th>Nombre</th>
            <th>Tiempo</th>
            <th width="10px"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($cine_episodios as $value)
            <tr>
              <td>{{ str_pad($value->orden, 2, "0", STR_PAD_LEFT) }}</td>
              <td>{{ $value->nombre }}</td>
              <td>{{ $value->duracionvideo }} min</td>
              <td>
                <div class="dropdown">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <div class="dropdown-content">
                    <a href="{{ url('backoffice/cinepelicula/'.$cinepelicula->id.'/edit?view=episodioedit&idepisodio='.$value->id) }}"><i class="fa fa-edit"></i> Editar</a>
                    <a href="{{ url('backoffice/cinepelicula/'.$cinepelicula->id.'/edit?view=episodiodelete&idepisodio='.$value->id) }}"><i class="fa fa-trash"></i> Eliminar</a>
                  </div>
                </div>
          </td>
            </tr>
          @endforeach
        </tbody>
    </table>
</div>  

@endsection