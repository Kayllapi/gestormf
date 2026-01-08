@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Peliculas y Series</span>
      <a class="btn btn-warning" href="{{ url('backoffice/cinepelicula/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
          <tr>
            <th width="100px">Tipo</th>
            <th>Nombre</th>
            <th>Publicación</th>
            <th width="10px">Imagen</th>
            <th width="10px"></th>
          </tr>
        </thead>
        @include('app.tablesearch',[
            'searchs'=>['tipo','nombre','fechapublicacion','','',''],
            'search_url'=> url('backoffice/cinepelicula')
        ])
        <tbody>
          @foreach($cinepeliculas as $value)
            <tr>
              <td>
                @if($value->idcine_tipo==1)
                  <div class="td-badge"><span class="badge badge-pill badge-success">{{$value->tiponombre}} </span></div>
                @else
                  <div class="td-badge"><span class="badge badge-pill badge-info">{{$value->tiponombre}} </span></div> 
                @endif
              </td>
              <?php
              $cine_episodios = DB::table('cine_episodio')
                  ->where('idcine_pelicula',$value->id)
                  ->count();
              ?>
              <td>{{ $value->nombre }} 
                @if($cine_episodios>0)
                ({{ $cine_episodios}} Episodios)
                @endif
              </td>
              <td>{{ $value->fechapublicacion }}</td>
              <td>
                  @if($value->imagen!='')
                  <img src="{{ url('/public/backoffice/web/cinepelicula/'.$value->imagen) }}" height="40px">
                  @endif
              </td>
              <td>
                <div class="dropdown">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <div class="dropdown-content">
                    <a href="{{ url('backoffice/cinepelicula/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a>
                    @if($value->idcine_tipo==2)
                    <a href="{{ url('backoffice/cinepelicula/'.$value->id.'/edit?view=episodio') }}"><i class="fa fa-list"></i> Episodios</a>
                    @endif
                    @if($cine_episodios==0)
                    <a href="{{ url('backoffice/cinepelicula/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a>
                    @endif
                  </div>
                </div>
          </td>
            </tr>
          @endforeach
        </tbody>
    </table>
    {{ $cinepeliculas->links('app.tablepagination', ['results' => $cinepeliculas]) }}
</div>  

@endsection