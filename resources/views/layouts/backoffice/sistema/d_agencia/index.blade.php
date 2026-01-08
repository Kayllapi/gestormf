<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Agencias</span>
      <a class="btn btn-warning" href="{{ url('backoffice/sistema/'.$tienda->id) }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th>RUC</th>
        <th>Nombre Comercial</th>
        <th>Razón Social</th>
        <th>Dirección</th>
        <th width="10px">Logo</th>
        <th width="10px">Facturación</th>
        <th width="15px"></th>
      </tr>
    </thead>
    @include('app.tablesearch',[
        'searchs'=>['ruc','nombrecomercial','razonSocial','direccion',''],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/agencia')
    ])
    <tbody>
        @foreach($s_agencias as $value)
        <tr>
          <td>{{$value->ruc}}</td>
          <td>{{$value->nombrecomercial}}</td>
          <td>{{$value->razonsocial}}</td>
          <td>{{$value->direccion}}</td>
          <td>
            @if($value->logo!='')
            <img src="{{ url('public/backoffice/tienda/'.$tienda->id.'/sistema/'.$value->logo) }}" height="40px">
            @endif
          </td>
          <td>
            @if($value->idestadofacturacion==1)
              <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Habilitado</span></div>
            @else
              <div class="td-badge"><span class="badge badge-pill badge-dark">Desactivado</span></div> 
            @endif
          </td>
          <td>
            <div class="header-user-menu menu-option" id="menu-opcion">
                <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                <ul>
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/agencia/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a></li>
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/agencia/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a></li>
                </ul>
            </div>
          </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
{{ $s_agencias->links('app.tablepagination', ['results' => $s_agencias]) }}
 