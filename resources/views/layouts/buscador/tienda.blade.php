@extends('layouts.master')
@section('cuerpo')
<div class="container">
    <div class="list-main-wrap fl-wrap card-listing">
        <!-- listing-item -->
        <?php $numitem=0 ?>
        @foreach($tiendas as $value)
        <div class="listing-item">
            <article class="geodir-category-listing fl-wrap">
                <div class="geodir-category-img">
                    <?php
                    $rutaimagen = getcwd().'/public/backoffice/tienda/'.$value->id.'/portada/'.$value->imagenportada;
                    ?>
                    @if(file_exists($rutaimagen) && $value->imagenportada!='')
                        <img id="imagenportada{{ $value->id }}" src="{{ url('redimensionar/tienda/portada/380/170/'.$value->id.'/'.$value->imagenportada) }}" idtienda="{{ $value->id }}" imagenportada="{{ $value->imagenportada }}" style="height: 170px;">
                    @else
                        <img src="{{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }}" style="height: 170px;">
                    @endif
                    <div class="overlay"></div>
                </div>
                <div class="geodir-category-content fl-wrap">
                    <div class="listing-avatar">
                        <a href="{{ url($value->link) }}?referencia=<?php echo isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '' ?><?php echo isset($_GET['user'])? '&user='.$_GET['user']:'' ?>">
                        <?php 
                        $rutaimagen = getcwd().'/public/backoffice/tienda/'.$value->id.'/logo/'.$value->imagen;
                        ?>
                        @if(file_exists($rutaimagen) && $value->imagen!='')
                          <img src="{{ url('public/backoffice/tienda/'.$value->id.'/logo/'.$value->imagen) }}" alt="{{ $value->nombre }}">
                        @else
                          <img src="{{ url('public/backoffice/sistema/sin_imagen_redondo.png') }}" alt="">
                        @endif
                        </a>
                        <!--span class="avatar-tooltip">Tel.:  <strong>{{ $value->numerotelefono }}</strong></span-->
                    </div>
                    <h3><a href="{{ url($value->link) }}?referencia=<?php echo isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '' ?><?php echo isset($_GET['user'])? '&user='.$_GET['user']:'' ?>">
                      {{ $value->nombre }}</a></h3>
                    <div class="geodir-category-options fl-wrap">
                          <?php
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
                          <div class="listing-rating card-popup-rainingvis" data-starrating2="{{ $totalsumacalificacion }}">
                              <span>({{ $cantidadcalificacion }} calificaciones)</span>
                          </div>
                    </div>
                </div>
            </article>
        </div>
        <?php $numitem++ ?>
        @endforeach
      
        <div style="background-color: #3498db;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 10px;
    color: #fff;
    float: left;
    width: 100%;">
            <i class="fa fa-check"></i> Para ver más tiendas, vuelve a actualizar.</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<style>
.listing-item {
    float: left;
    width: 33.3%;
}
.card-listing .listing-geodir-category {
    top: -30px;
}
.card-listing .geodir-category-content {
    padding: 30px 10px 10px;
    margin-top: -105px;
    background-color: transparent;
}
.card-listing .geodir-category-content h3 a {
    background-color: #000000b3;
    color: #fff;
    padding: 5px;
    padding-left: 10px;
    padding-right: 10px;
    border-radius: 8px;
    font-size: 15px;
}
.card-listing .listing-rating {
    background-color: #000000b3;
    padding: 10px;
    padding-bottom: 6px;
    margin-top: 0px;
    border-radius: 8px;
    width: unset;
}
.card-listing .listing-rating span, .dashboard-listing-table-text .listing-rating span {
    color: #fff;
}
  
.card-listing .listing-avatar {
    top: -50px;
    right: 10px;
    width: 80px;
    height: 80px;
}
.card-listing .listing-avatar img {
    width: 80px;
    height: 80px;
}
</style>
@endsection
