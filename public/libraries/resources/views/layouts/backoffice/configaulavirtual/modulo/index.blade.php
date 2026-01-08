@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>{{ $curso->nombre }}</span>
      <a class="btn btn-success" href="{{ url('backoffice/configaulavirtual') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
      <a class="btn btn-warning" href="{{ url('backoffice/configaulavirtual/create?view=createmodulo&idcurso='.$curso->id) }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
          <tr>
            <th>Módulo</th>
            <th>Cursos</th>
            <th width="10px"></th>
          </tr>
        </thead>
        @include('app.tablesearch',[
            'searchs'=>['cursomodulonombre','','','',''],
            'search_url'=> url('backoffice/configaulavirtual')
        ])
        <tbody>
          @foreach($cursomodulos as $value)
          <?php $countcursomodulotemas = DB::table('cursomodulotema')->where('idcursomodulo',$value->id)->count();?>
            <tr>
              <td>{{ $value->nombre }}</td>
              <td>{{ $countcursomodulotemas }}</td>
              <td>
                <div class="dropdown">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <div class="dropdown-content">
                    <a href="{{ url('backoffice/configaulavirtual/'.$value->id.'/edit?view=indextema') }}"><i class="fa fa-book"></i> Temas</a>
                    <a href="{{ url('backoffice/configaulavirtual/'.$value->id.'/edit?view=moduloeditar&idcurso='.$curso->id) }}"><i class="fa fa-edit"></i> Editar</a>
                    @if($countcursomodulotemas==0)
                    <a href="{{ url('backoffice/configaulavirtual/'.$value->id.'/edit?view=moduloeliminar&idcurso='.$curso->id) }}"><i class="fa fa-trash"></i> Eliminar</a>
                    @endif
                  </div>
                </div>
          </td>
            </tr>
          @endforeach
        </tbody>
    </table>
    {{ $cursomodulos->links('app.tablepagination', ['results' => $cursomodulos]) }}
</div>  

@endsection
@section('scriptsbackoffice')

@endsection