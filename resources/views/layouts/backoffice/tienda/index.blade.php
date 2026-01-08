@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Tiendas</span>
      <a class="btn btn-warning" href="{{ url('backoffice/tienda/create') }}" style="margin-top: 3px;"><i class="fa fa-angle-right"></i> Registrar Nueva Tienda</a></a>
      <div class="custom-form" style="width: 250px;float: right;">
      <form action="{{url('backoffice/tienda')}}" method="GET">
      <input type="text" name="searchtienda" placeholder="Buscar Tienda..." style="border: 0px;margin: 2.5px;float: right;padding: 14px;">
      </form>
      </div>
    </div>
</div>

<div class="dashboard-list-box fl-wrap"> 
    @foreach($tiendas as $value)
    <div class="dashboard-list">
        <div class="dashboard-message">
            <div class="dashboard-listing-table-image">
              
            </div>
            <div class="dashboard-listing-table-text">
                <h4>{{$value->nombre}}</h4>
                <span class="dashboard-listing-table-address">
                  <i class="fa fa-phone"></i><a href="javascript:;">{{$value->numerotelefono}}</a><br>
                  <i class="fa fa-map-marker"></i><a href="javascript:;">{{$value->direccion}}</a><br>
                </span>
                <ul class="dashboard-listing-table-opt  fl-wrap">
                    <li><a href="{{ url('backoffice/tienda/'.$value->id.'/edit?view=informacion') }}"><i class="fa fa-pencil-square-o"></i> Información</a></li>
                    <li><a href="{{ url('backoffice/'.$value->id.'/inicio') }}" style="background-color: #2196F3;"><i class="fa fa-cloud"></i> Sistema</a></li>
                    <li><a href="{{ url('backoffice/tienda/'.$value->id.'/edit?view=resetear') }}" style="background-color: #2c3b5a;" class="del-btn"><i class="fa fa-trash"></i> Resetear</a></li>
                </ul>
            </div>
        </div>
    </div>
    @endforeach
</div>  

{{ $tiendas->links('app.tablepagination', ['results' => $tiendas]) }}

@endsection
 
