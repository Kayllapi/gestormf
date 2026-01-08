@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>{{$s_categoria_1->nombre}} / {{$s_categoria->nombre}} / Categorías</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/categoria/'.$s_categoria_1->id.'/edit?view=indexsubcategoria') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/categoria/'.$s_categoria->id.'/edit?view=registrarsubsubcategoria&idcategoria_1='.$s_categoria_1->id) }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th>Nombre</th>
        <th width="10px"></th>
        <th width="10px"></th>
      </tr>
    </thead>
    @include('app.tablesearch',[
        'searchs'=>['nombre'],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/categoria')
    ])
    <tbody>
      @foreach($s_categorias as $value)
        <tr>
          <td>{{$value->nombre}}</td>
          <td>
            @if($value->imagen!='')
            <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$value->imagen) }}" height="40px">
            @endif
          </td>
          <td>
                <div class="header-user-menu menu-option" id="menu-opcion">
                    <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                    <ul>
                        <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/categoria/'.$value->id.'/edit?view=editarsubsubcategoria&idcategoria_1='.$s_categoria_1->id.'&idcategoria_2='.$s_categoria->id) }}"><i class="fa fa-edit"></i> Editar</a></li>
                        <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/categoria/'.$value->id.'/edit?view=eliminarsubsubcategoria&idcategoria_1='.$s_categoria_1->id.'&idcategoria_2='.$s_categoria->id) }}"><i class="fa fa-trash"></i> Eliminar</a></li>
                    </ul>
                </div>
          </td>
        </tr>
      @endforeach 
    </tbody>
</table>
</div>
{{ $s_categorias->links('app.tablepagination', ['results' => $s_categorias]) }}
@endsection
