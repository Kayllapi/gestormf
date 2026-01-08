@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Portadas</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/ecommerceportada/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th>Título</th>
        <th>Descripción</th>
        <th width="10px">Imagen</th>
        <th width="10px">Estado</th>
        <th width="10px"></th>
      </tr>
    </thead>
    @include('app.tablesearch',[
        'searchs'=>['titulo','descripcion'],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/ecommerceportada')
    ])
    <tbody>
      @foreach($s_ecommerceportada as $value)
        <tr>
          <td>{{$value->titulo}}</td>
          <td>{{$value->descripcion}}</td>
          <td>
            @if($value->imagen!='')
            <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/ecommerceportada/'.$value->imagen) }}" height="40px">
            @endif
          </td>
          <td>
            @if($value->s_idestado==1)
              <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Activado</span></div>
            @else
              <div class="td-badge"><span class="badge badge-pill badge-dark"><i class="fas fa-sync-alt"></i> Desactivado</span></div> 
            @endif
          </td>
          <td>
            <div class="header-user-menu menu-option" id="menu-opcion">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <ul>
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/ecommerceportada/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a></li>
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/ecommerceportada/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a></li>
                </ul>
            </div>
          </td>
        </tr>
      @endforeach 
    </tbody>
</table>
</div>
{{ $s_ecommerceportada->links('app.tablepagination', ['results' => $s_ecommerceportada]) }}
@endsection
