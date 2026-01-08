@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<?php $role_user = DB::table('role_user')->where('role_id',1)->where('user_id',$idusers)->first(); ?>
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>CATEGORIAS</span>
      <a class="btn btn-warning" href="{{ url('backoffice/categoria/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
          <tr>
            <th>Nombre</th>
            <th>Cantidad</th>
            <th width="10px"></th>
          </tr>
        </thead>
        @include('app.tablesearch',[
            'searchs'=>['nameCategory'],
            'search_url'=> url('backoffice/categoria')
        ])
        <tbody>
          @foreach($categorias as $value)
          <?php $counttiendas = DB::table('tienda')->where('idcategoria',$value->id)->count();?>
            <tr>
              <td>{{ $value->nombre }}</td>
              <td>{{ $counttiendas }}</td>
              <td>
                <div class="dropdown">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <div class="dropdown-content">
                    <a href="{{ url('backoffice/categoria/'.$value->id.'/edit?view=informacion') }}"><i class="fa fa-edit"></i> Informaciòn</a>
                    <a href="{{ url('backoffice/categoria/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a>
                  </div>
                </div>
          </td>
            </tr>
          @endforeach
        </tbody>
    </table>
    {{ $categorias->links('app.tablepagination', ['results' => $categorias]) }}
</div>  

@endsection