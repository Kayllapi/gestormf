@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Productos en Oferta</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productooferta/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th >Nombre</th>
        <th >Imagen</th>
        <th width="10px"></th>
      </tr>
    </thead>
    @include('app.tablesearch',[
        'searchs'=>['nombre'],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/productooferta')
    ])
    <tbody>
      @foreach($s_productooferta as $value)
        <tr>
          <td>{{$value->nombre}}</td>
          <td>{{$value->imagen}}</td>
          <td>
            <div class="header-user-menu menu-option" id="menu-opcion">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <ul>
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productooferta/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a></li>
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/productooferta/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a></li>
                </ul>
            </div>
          </td>
        </tr>
      @endforeach 
    </tbody>
</table>
</div>
{{ $s_productooferta->links('app.tablepagination', ['results' => $s_productooferta]) }}
@endsection
