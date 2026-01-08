@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Mesas</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidamesa/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th>Número de Mesa</th>
        <th width="10px"></th>
      </tr>
    </thead>
    @include('app.tablesearch',[
        'searchs'=>['numero'],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidamesa')
    ])
    <tbody>
      @foreach($s_comida_mesa as $value)
        <tr>
          <td>{{ str_pad($value->numero, 3, "0", STR_PAD_LEFT) }}</td>
          <td>
            <div class="header-user-menu menu-option" id="menu-opcion">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <ul>
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidamesa/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a></li>
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidamesa/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a></li>
                </ul>
            </div>
          </td>
        </tr>
      @endforeach 
    </tbody>
</table>
</div>
{{ $s_comida_mesa->links('app.tablepagination', ['results' => $s_comida_mesa]) }}
@endsection
