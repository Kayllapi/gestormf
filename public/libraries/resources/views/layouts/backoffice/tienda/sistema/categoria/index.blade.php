@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Categorías</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/categoria/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th>Nombre</th>
        <th width="10px"></th>
        <th>Productos</th>
        <th>Sub Categorias</th>
        <th width="10px"></th>
      </tr>
    </thead>
    @include('app.tablesearch',[
        'searchs'=>['nombre'],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/categoria')
    ])
    <tbody>
      @foreach($s_categorias as $value)
        <?php
         $countproductos = DB::table('s_producto')
                ->join('s_categoria','s_categoria.id','s_producto.s_idcategoria1')
                ->where('s_categoria.idtienda',$tienda->id)
                ->where('s_producto.s_idcategoria1',$value->id)
                ->count();
        ?>
        <?php
         $countcategorias_1 = DB::table('s_categoria')
                ->where('idtienda',$tienda->id)
                ->where('s_idcategoria',$value->id)
                ->count();
        ?>
        <tr>
          <td>{{$value->nombre}}</td>
          <td>
            @if($value->imagen!='')
            <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$value->imagen) }}" height="40px">
            @endif
          </td>
          <td>{{$countproductos}}</td>
          <td>{{$countcategorias_1}}</td>
          <td>
             <div class="header-user-menu menu-option" id="menu-opcion">
                 <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                 <ul>
                     <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/categoria/'.$value->id.'/edit?view=indexsubcategoria') }}"><i class="fa fa-th-list"></i> Sub Categorias</a></li>
                     <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/categoria/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a></li>
                     @if($countcategorias_1==0)
                     <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/categoria/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a></li>
                     @endif
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
