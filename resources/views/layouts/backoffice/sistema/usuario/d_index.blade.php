@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Usuarios',
    'botones'=>[
        'registrar:/'.$tienda->id.'/usuario/create?view=registrar: Registrar'
    ]
])
<div class="table-responsive">
<table class="table" id="tabla-contenido">
    <thead class="thead-dark">
      <tr>
        <th width="110px">Persona</th>
        <th width="100px">RUC/DNI </th>
        <th>Apellidos y Nombres</th>
        <th>Teléfono</th>
        <th>Distrito - Provincia - Departamento</th>
        <th width="10px"></th>
      </tr>
    </thead>
    @include('app.tablesearch',[
        'searchs'=>['tipo','identificacion','cliente','telefono','ubigeo',''],
        'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/usuario')
    ])
    <tbody>
        @foreach($usuarios as $value)
        <tr>
          <td>{{ $value->tipopersonanombre }}</td>
          <td>{{ $value->identificacion }}</td>
          <td>
            @if($value->idtipopersona==1 or $value->idtipopersona==3)
              {{ $value->apellidos }}, {{ $value->nombre }}
            @else
              {{ $value->nombre }}
            @endif  
          </td>
          <td>{{ $value->numerotelefono }}</td>
          <td>{{ $value->ubigeonombre }}</td>
          <td>
                <div class="header-user-menu menu-option" id="menu-opcion">
                    <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                    <ul>
                        <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/usuario/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a></li>
                        <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/usuario/'.$value->id.'/edit?view=ubicacion') }}"><i class="fa fa-edit"></i> Ubicación</a></li>
                        @if($tienda->idcategoria==13)      
                        <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/usuario/'.$value->id.'/edit?view=bienimportar') }}"><i class="fa fa-check"></i> Garantias</a></li>
                        @endif
                        <li><a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/usuario/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a></li>
                    </ul>
                </div>
          </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
{{ $usuarios->links('app.tablepagination', ['results' => $usuarios]) }}
@endsection
