@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Anuncios</span>
      <a class="btn btn-warning" href="{{ url('backoffice/consumidor/adminanuncio/create') }}"><i class="fa fa-angle-right"></i> Registrar</a>
    </div>
</div>
<div class="table-responsive">
    <table class="table" id="tabla-contenido">
        <thead class="thead-dark">
          <tr>
            <th>Nombre</th>
            <th>Precio Normal</th>
            <th>Precio Descontado</th>
            <th>Stock</th>
            <th>Fecha de registro</th>
            <th width="10px"></th>
            <th width="10px"></th>
            <th width="10px"></th>
          </tr>
        </thead>
        @include('app.tablesearch',[
            'searchs'=>['nameCategory'],
            'search_url'=> url('backoffice/consumidor/adminanuncio')
        ])
        <tbody>
          @foreach($anuncios as $value)
            <tr>
              <td>{{ $value->nombre }}</td>
              <td>{{ $value->precionormal }} KAY</td>
              <td>{{ $value->preciodescuento }} KAY</td>
              <td>{{ $value->stock }}</td>
              <td>{{ date_format(date_create($value->fecharegistro), "d/m/Y h:i:s A") }}</td>
              <td>
                @if($value->imagen!='')
                <img src="{{ url('public/backoffice/consumidor/anuncio/'.$value->imagen) }}" height="40px">
                @endif
              </td>
              <td>
                @if($value->idestadoanuncio==1)
                  <div class="td-badge"><span class="badge badge-pill badge-success"><i class="fa fa-sync"></i> Pendiente</span></div>
                @else
                  <div class="td-badge"><span class="badge badge-pill badge-warning"><i class="fa fa-check"></i> Confirmado</span></div> 
                @endif
              </td>
              <td>
                <div class="dropdown">
                  <a href="javascript:;" class="btn btn-info">Opción <i class="fa fa-angle-down"></i></a>
                  <div class="dropdown-content">
                    <a href="{{ url('backoffice/consumidor/adminanuncio/'.$value->id.'/edit?view=editar') }}"><i class="fa fa-edit"></i> Editar</a>
                    <a href="{{ url('backoffice/consumidor/adminanuncio/'.$value->id.'/edit?view=confirmar') }}"><i class="fa fa-check"></i> Confirmar</a>
                    <a href="{{ url('backoffice/consumidor/adminanuncio/'.$value->id.'/edit?view=eliminar') }}"><i class="fa fa-trash"></i> Eliminar</a>
                  </div>
                </div>
          </td>
            </tr>
          @endforeach
        </tbody>
    </table>
    {{ $anuncios->links('app.tablepagination', ['results' => $anuncios]) }}
</div>  

@endsection