@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>CONCEPTO MOVIMIENTOS</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/conceptomovimiento/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
                <thead class="thead-dark">
                  <tr>
                    <th width="80%">NOMBRE</th>
                    <th width="20%">TIPO DE MOVIMIENTO</th>
                    <th width="15px"></th>
                  </tr>
                </thead>
                @include('app.tablesearch',[
                    'searchs'=>['movementName','movementType'],
                    'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/conceptomovimiento')
                ])
                <tbody>
                    @foreach($movementConcept as $value)
                    <tr>
                      <td>{{$value->nombre}}</td>
                      <td>{{$value->tipoMovimiento}}</td>
                      <td>
                        <div class="dropdown">
                          <a href="javascript:;" id="btneliminar1" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                          <div class="dropdown-content">
                            <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/conceptomovimiento/'.$value->id.'/edit?view=editar') }}" id="btneliminar1"><i class="fa fa-edit"></i> Editar</a>
                            <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/conceptomovimiento/'.$value->id.'/edit?view=eliminar') }}" id="btneliminar1"><i class="fa fa-trash"></i> Eliminar</a>
                          </div>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
{{ $movementConcept->links('app.tablepagination', ['results' => $movementConcept]) }}
</div>
@endsection
