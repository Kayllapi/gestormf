@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Accesos</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/acceso/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
                <thead class="thead-dark">
                  <tr>
                    <th width="110px">TIPO</th>
                    <th width="100px">RUC/DNI	</th>
                    <th>CLIENTE</th>
                    <th>CORREO ELECTRÓNICO</th>
                    <th width="100px">TELÉFONO</th>
                    <th width="10px"></th>
                  </tr>
                </thead>
                @include('app.tablesearch',[
                    'searchs'=>['tipo','identificacion','cliente','correo','telefono',''],
                    'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/acceso')
                ])
                <tbody>
                    @foreach($usuarios as $value)
                    <tr>
                      <td>{{ $value->tipopersonanombre }}</td>
                      <td>{{ $value->identificacion }}</td>
                      <td>
                        @if($value->idtipopersona==1)
                          {{ $value->apellidos }}, {{ $value->nombre }}
                        @else
                          {{ $value->nombre }}
                        @endif  
                      </td>
                      <td>{{ $value->email!=''?$value->email:'---' }}</td>
                      <td>{{ $value->numerotelefono!=''?$value->numerotelefono:'---' }}</td>
                      <td>
                        <div class="dropdown">
                          <a href="javascript:;" id="btneliminar1" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                          <div class="dropdown-content">
                            <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/acceso/'.$value->id.'/edit?view=editar') }}" id="btneliminar1"><i class="fa fa-edit"></i> Editar</a>
                            <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/acceso/'.$value->id.'/edit?view=eliminar') }}" id="btneliminar1"><i class="fa fa-trash"></i> Eliminar</a>
                          </div>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
{{ $usuarios->links('app.tablepagination', ['results' => $usuarios]) }}
</div>
@endsection
