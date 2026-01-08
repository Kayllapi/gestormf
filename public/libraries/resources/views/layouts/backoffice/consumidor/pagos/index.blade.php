@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>PAGOS</span>
      <a class="btn btn-warning" href="{{ url('backoffice/usuario/create') }}"><i class="fa fa-angle-right"></i> Registrar</a></a>
    </div>
</div>
<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
          <tr>
            <th width="180px">Fecha de registro</th>
            <th>Apellidos y Nombres</th>
            <th width="100px">Plan Adquirido</th>
            <th width="100px">Costo</th>
            <th width="10px">Estado</th>
            <th width="10px"></th>
          </tr>
        </thead>
        @include('app.tablesearch',[
            'searchs'=>['fecharegistro','cliente','planadquirido','','',''],
            'search_url'=> url('backoffice/pagos')
        ])
        <tbody>
          @foreach($planadquirido as $value)
            <tr>
              <td>{{date_format(date_create($value->fechacompra),'d/m/Y h:i:s A')}}</td>
              <td>{{ $value->nombre }} {{ $value->apellidos }}</td>
              <td>{{ $value->plannombre }}</td>
              <td>S/. {{ $value->costo }}</td>
              <td>
                @if($value->fechaanulacion!='') 
                <div class="td-badge"><span class="badge badge-pill badge-danger"><i class="fa fa-check"></i> Anulado</span></div>
                @elseif($value->fechaconfirmacion!='') 
                <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-check"></i> Confirmado</span></div>
                @else
                <div class="td-badge"><span class="badge badge-pill badge-info"><i class="fa fa-check"></i> Pendiente</span></div>
                @endif
              </td>
              <td>
                @if($value->fechaanulacion=='')
                <div class="dropdown">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <div class="dropdown-content">
                    @if($value->fechaconfirmacion=='')
                    <a href="{{ url('backoffice/pagos/'.$value->id.'/edit?view=confirmar') }}"><i class="fas fa-check"></i> Confirmar</a>
                    @endif
                  </div>
                </div> 
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
    </table>
    {{ $planadquirido->links('app.tablepagination', ['results' => $planadquirido]) }}
</div>                      
@endsection