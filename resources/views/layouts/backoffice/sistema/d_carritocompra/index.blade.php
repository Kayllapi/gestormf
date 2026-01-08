@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Pedidos</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/carritocompra/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th>Còdigo</th>
        <th>Cliente</th>
        <th>fecha de registro</th>
        <th>fecha de confirmación</th>
        <th>Estado</th>
        <th width="10px"></th>
      </tr>
    </thead>
    @include('app.tablesearch',[
        'searchs'=>['codigo','cliente'],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/carritocompra')
    ])
    <tbody>
      @foreach($s_carritocompra as $value)
        <tr>
          <td>{{$value->codigo}}</td>
          <td>{{$value->clientenombre}}</td>
          <td>{{$value->fecharegistro}}</td>
          <td>{{$value->fechaconfirmacion}}</td>
          <td>
            @if($value->s_idestado==2)
              <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Vendido</span></div>
            @else
              <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fas fa-sync-alt"></i> Pendiente</span></div> 
            @endif
          </td>
          <td>
            <div class="header-user-menu menu-option" id="menu-opcion">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <ul>
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/carritocompra/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a></li>
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/carritocompra/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a></li>
                </ul>
            </div>
          </td>
        </tr>
      @endforeach 
    </tbody>
</table>
</div>
{{ $s_carritocompra->links('app.tablepagination', ['results' => $s_carritocompra]) }}
@endsection
