@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Método de Pagos</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/metodopago/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th>Nombre</th>
        <th>Llave Pública</th>
        <th>Llave privada</th>
        <th>Estado</th>
        <th width="10px"></th>
      </tr>
    </thead>
    @include('app.tablesearch',[
        'searchs'=>['tipometodopagonombre'],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/metodopago')
    ])
    <tbody>
      @foreach($s_metodopago as $value)
        <tr>
          <td>{{$value->tipometodopagonombre}}</td>
          <td>{{$value->key_public}}</td>
          <td>{{$value->key_private}}</td>
          <td>
            @if($value->s_idestado==1)
                Activado
            @else
                Desactivado
            @endif  
          </td>
          <td>
            <div class="dropdown">
              <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
              <div class="dropdown-content">
                <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/metodopago/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a>
                <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/metodopago/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a>
              </div>
            </div>
          </td>
        </tr>
      @endforeach 
    </tbody>
</table>
</div>
{{ $s_metodopago->links('app.tablepagination', ['results' => $s_metodopago]) }}
@endsection
