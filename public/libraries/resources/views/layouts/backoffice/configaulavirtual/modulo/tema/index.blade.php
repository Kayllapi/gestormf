@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>{{ $curso->nombre }} / {{ $cursomodulo->nombre }}</span>
      <a class="btn btn-success" href="{{ url('backoffice/configaulavirtual/'.$curso->id.'/edit?view=indexmodulo') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
      <a class="btn btn-warning" href="{{ url('backoffice/configaulavirtual/create?view=createtema&idcursomodulo='.$cursomodulo->id) }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
          <tr>
            <th>Temas</th>
            <th>Video</th>
            <th>Imagen</th>
            <th>Estado</th>
            <th width="10px"></th>
          </tr>
        </thead>
        @include('app.tablesearch',[
            'searchs'=>['cursonombre','','','',''],
            'search_url'=> url('backoffice/configaulavirtual')
        ])
        <tbody>
          @foreach($cursomodulotemas as $value)
            <tr>
              <td>{{ $value->nombre }}</td>
              <td><a href="{{ $value->urlvideo }}" class="image-popup"><i class="fa fa-play-circle"></i> Ver Video</a></td>
              <td>
                @if($value->imagen!='')
                <img src="{{ url('/public/backoffice/sistema/sitioweb/aulavirtual/'.$value->imagen) }}" height="35px">
                @endif
              </td>
              <td>
                @if($value->idestado==1)
                Activado
                @else
                Desactivado
                @endif
              </td>
              <td>
                <div class="dropdown">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <div class="dropdown-content">
                    <a href="{{ url('backoffice/configaulavirtual/'.$value->id.'/edit?view=cursomodulotemaeditar&idcursomodulo='.$cursomodulo->id) }}"><i class="fa fa-edit"></i> Editar</a>
                    <a href="{{ url('backoffice/configaulavirtual/'.$value->id.'/edit?view=cursomodulotemaeliminar&idcursomodulo='.$cursomodulo->id) }}"><i class="fa fa-trash"></i> Eliminar</a>
                  </div>
                </div>
          </td>
            </tr>
          @endforeach
        </tbody>
    </table>
    {{ $cursomodulotemas->links('app.tablepagination', ['results' => $cursomodulotemas]) }}
</div>  

@endsection
@section('scriptsbackoffice')
@endsection