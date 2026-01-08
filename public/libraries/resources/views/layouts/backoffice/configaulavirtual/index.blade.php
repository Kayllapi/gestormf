@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Cursos</span>
      <a class="btn btn-success" href="{{ url('backoffice/configaulavirtual') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
      <a class="btn btn-warning" href="{{ url('backoffice/configaulavirtual/create?view=createcurso') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
          <tr>
            <th>Curso</th>
            <th>Video</th>
            <th>Imagen</th>
            <th>Modulos</th>
            <th>Estado</th>
            <th width="10px"></th>
          </tr>
        </thead>
        @include('app.tablesearch',[
            'searchs'=>['cursonombre','','','',''],
            'search_url'=> url('backoffice/configaulavirtual')
        ])
        <tbody>
          @foreach($cursos as $value)
          <?php $countcursomodulos = DB::table('cursomodulo')->where('idcurso',$value->id)->count();?>
            <tr>
              <td>{{ $value->nombre }}</td>
              <td>{{ $value->urlvideo }}</td>
              <td>
                @if($value->imagen!='')
                <img src="{{ url('/public/backoffice/usuario/'.$value->idusers.'/aulavirtual/'.$value->imagen) }}" height="35px">
                @endif
              </td>
              <td>{{ $countcursomodulos }}</td>
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
                    <a href="{{ url('backoffice/configaulavirtual/'.$value->id.'/edit?view=indexmodulo') }}"><i class="fa fa-book"></i> Modulos</a>
                    <a href="{{ url('backoffice/configaulavirtual/'.$value->id.'/edit?view=cursoeditar') }}"><i class="fa fa-edit"></i> Editar</a>
                    @if($countcursomodulos==0)
                    <a href="{{ url('backoffice/configaulavirtual/'.$value->id.'/edit?view=cursoeliminar') }}"><i class="fa fa-trash"></i> Eliminar</a>
                    @endif
                  </div>
                </div>
          </td>
            </tr>
          @endforeach
        </tbody>
    </table>
    {{ $cursos->links('app.tablepagination', ['results' => $cursos]) }}
</div>  

@endsection
@section('scriptsbackoffice')
@endsection