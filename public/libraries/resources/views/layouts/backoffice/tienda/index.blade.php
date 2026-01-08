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
    <?php
    /*$counttiendagalerias = DB::table('tiendagaleria')
        ->where('idtienda',$value->id)
        ->count();
    $counts_categorias = DB::table('s_categoria')
        ->where('idtienda',$value->id)
        ->count();*/
    $countoferta = DB::table('oferta')
        ->where('idtienda',$value->id)
        ->count();
    $calificacion = DB::table('calificacion')
        ->where('idtienda',$value->id)
        ->select('idtienda',DB::raw('CONCAT(SUM(numero)/COUNT(*)) as total'),DB::raw('COUNT(*) as cantidad'))
        ->groupBy('idtienda')
        ->first();
    $totalsumacalificacion = 5;
    $cantidadcalificacion = 0;
    if($calificacion!=''){
        $totalsumacalificacion = floor($calificacion->total);
        $cantidadcalificacion = $calificacion->cantidad;
    }
    ?> 
    <div class="dashboard-list">
        <div class="dashboard-message">
            <div class="dashboard-listing-table-image">
                <a href="{{url($value->link)}}" target="_blank">
                  <?php 
                  $rutaimagen = getcwd().'/public/backoffice/tienda/'.$value->id.'/portada/'.$value->imagenportada; 
                  $imagenportada = url('public/backoffice/sistema/sin_imagen_cuadrado.png');
                  if(file_exists($rutaimagen) AND $value->imagenportada!=''){
                      $imagenportada = url('/public/backoffice/tienda/'.$value->id.'/portada/'.$value->imagenportada);
                  }
                  ?>
                   <div class="geodir-category-img"
                        style="background-image: url({{ $imagenportada }});
                                background-repeat: no-repeat;
                                background-size: cover;
                                background-position: center;
                                height: 162px;
                                border-radius: 5px;">
                    </div>
                </a>
            </div>
            <div class="dashboard-listing-table-text">
                <h4><a href="{{url($value->link)}}" target="_blank">{{$value->nombre}}</a></h4>
                <span class="dashboard-listing-table-address">
                  <i class="fa fa-phone"></i><a href="javascript:;">{{$value->numerotelefono}}</a><br>
                  <i class="fa fa-map-marker"></i><a href="javascript:;">{{$value->direccion}}</a><br>
                  <i class="fa fa-link"></i><a href="javascript:;">{{$value->paginaweb!=''?$value->paginaweb:'---'}}</a>
                </span>
                <div class="listing-rating card-popup-rainingvis fl-wrap" data-starrating2="{{ $totalsumacalificacion }}">
                    <span>({{$cantidadcalificacion}} calificaciones)
                    @if($idusers==1)
                      - {{ $value->vendedorusuario }}
                    @endif</span>
                </div>
                <ul class="dashboard-listing-table-opt  fl-wrap">
                    <li><a href="{{ url('backoffice/tienda/'.$value->id.'/edit?view=informacion') }}"><i class="fa fa-pencil-square-o"></i> Información</a></li>
                    <li><a href="{{ url('backoffice/tienda/'.$value->id.'/edit?view=galeria') }}"><i class="fa fa-image"></i> Galeria</a></li>
                    <!--li><a href="{{ url('backoffice/tienda/'.$value->id.'/edit?view=ofertaindex') }}" style="background: #f1c40f;"><i class="fa fa-th-list"></i> Ofertas ({{ $countoferta }})</a></li-->
                    <li><a href="{{ url('backoffice/tienda/sistema/'.$value->id.'/inicio') }}" style="background-color: #2196F3;"><i class="fa fa-cloud"></i> Sistema</a></li>
                    <li><a href="{{ url('backoffice/tienda/'.$value->id.'/edit?view=dominiopersonalizado') }}" style="background-color: #e0b609;"><i class="fa fa-cogs"></i> Configuración</a></li>
                    <!--li><a href="{{ url('backoffice/tienda/'.$value->id.'/edit?view=eliminar') }}" class="del-btn"><i class="fa fa-trash"></i> Eliminar</a></li-->
                    <li><a href="{{ url('backoffice/tienda/'.$value->id.'/edit?view=resetear') }}" style="background-color: #2c3b5a;" class="del-btn"><i class="fa fa-trash"></i> Resetear</a></li>
                </ul>
            </div>
        </div>
    </div>
    @endforeach
</div>  

{{ $tiendas->links('app.tablepagination', ['results' => $tiendas]) }}

@endsection
 
