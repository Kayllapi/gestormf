@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>TIENDAS</span>
      <a class="btn btn-success" href="{{ url('backoffice/usuario') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
          <tr>
            <th width="100px">ID</th>
            <th>NOMBRE</th>
            <th width="10px"></th>
          </tr>
        </thead>
        @include('app.tablesearch',[
            'searchs'=>['id','nombreTienda'],
            'search_url'=> url('backoffice/usuario')
        ])
        <tbody>
          @foreach($tiendas as $value)
            <tr>
              <td>{{ $value->id }}</td>
              <td>{{ $value->nombre }}</td>
              <td>
                <a href="{{ url('backoffice/usuario/'.$value->id.'/edit?view=transferir') }}" class="btn btn-info"><i class="fas fa-check"></i> Transferir</a>
              </td>
            </tr>
          @endforeach
        </tbody>
    </table>
    {{ $tiendas->links('app.tablepagination', ['results' => $tiendas]) }}
</div>
@endsection
