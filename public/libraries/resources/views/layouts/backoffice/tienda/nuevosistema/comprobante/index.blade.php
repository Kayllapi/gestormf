@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>COMPROBANTE</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comprobante/create') }}">
        <i class="fa fa-angle-right"></i> Registrar
      </a>
    </div>
</div>
<div class="table-responsive">
<table class="table" id="tabla-contenido">
                <thead class="thead-dark">
                  <tr>
                    <th>NOMBRE</th>
                    <th width="15px"></th>
                  </tr>
                </thead>
                @include('app.tablesearch',[
                    'searchs'=>['nombre'],
                    'search_url'=> url('backoffice/tienda/sistema/'.$tienda->id.'/comprobante')
                ])
                <tbody>
                    @foreach($listar_comprobante as $value)
                    <tr>
                      <td>{{$value->nombre}}</td>
                      <td>
                        <div class="dropdown">
                          <a href="javascript:;" id="btneliminar1" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                          <div class="dropdown-content">
                            <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comprobante/'.$value->id.'/edit?view=editar') }}">
                              <i class="fa fa-edit"></i> Editar
                            </a>
                            <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comprobante/'.$value->id.'/edit?view=eliminar') }}">
                              <i class="fa fa-trash"></i> Eliminar
                            </a>
                          </div>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

</div>
@endsection
