@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="profile-edit-container">
    <div class="statistic-container fl-wrap">
                <?php 
                $counttienda = DB::table('tienda')
                    ->where('idusers',$users->id)
                    ->count();
                ?>
     <div class="statistic-item-wrap"> 
     <div class="statistic-item gradient-bg fl-wrap">
         <i class="fa fa-building"></i>
            <div class="statistic-item-numder">{{ $counttienda }}</div>
            <h5>Tiendas</h5>
        </div>
      </div>
                <?php 
                $countcalificacion = DB::table('calificacion')
                    ->where('idusers',$users->id)
                    ->count();
                ?>
     <div class="statistic-item-wrap"> 
     <div class="statistic-item gradient-bg fl-wrap">
         <i class="fa fa fa-star"></i>
            <div class="statistic-item-numder">{{ $countcalificacion }}</div>
            <h5>Calificaciones</h5>
        </div>
        </div>
                <?php 
                $countrecomendacion = DB::table('recomendacion')
                    ->where('idusers',$users->id)
                    ->count();
                ?>
     <div class="statistic-item-wrap"> 
     <div class="statistic-item gradient-bg fl-wrap">
         <i class="fa fa-heart"></i>
            <div class="statistic-item-numder">{{ $countrecomendacion }}</div>
            <h5>Favoritos</h5>
        </div>
        </div>
                <?php 
                $counttiendacomentario = DB::table('tiendacomentario')
                    ->where('idusers',$users->id)
                    ->count();
                ?>
     <div class="statistic-item-wrap"> 
     <div class="statistic-item gradient-bg fl-wrap">
         <i class="fa fa-comments"></i>
            <div class="statistic-item-numder">{{ $counttiendacomentario }}</div>
            <h5>Comentarios</h5>
        </div>
        </div>    
    </div>   
  <div class="list-single-main-media fl-wrap">
  
      <img src="{{ url('public/backoffice/sistema/sistema-banner-2.png') }}" id="imgbanner">
                                            
                                            <a href="https://www.youtube.com/embed/lR-8hkr3q-M" class="promo-link gradient-bg image-popup" style="margin-top: -135px;"><i class="fa fa-play"></i><span>¿Que es Kayllapi?</span></a>
                                            <a href="{{ url('public/backoffice/negocio/kayllapi_catalogo.pdf') }}" target="_blank" class="promo-link gradient-bg" style="background-color: #30cb71;
    background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#30cb71), to(#30cb71));
    background: -webkit-linear-gradient(top, #30cb71, #7fe0a7);"><i class="fa fa-play"></i><span> Nosotros te Capacitamos</a>
                                        </div>
        
</div>
 <style>
  body:not(.lg-from-hash) .lg-outer.lg-start-zoom .lg-item.lg-complete .lg-object {
    padding: 5px;
}
   .promo-link {
      float: right;
    margin-right: 20px;
}
/*   .imgbanner{
      background-image: url({{ url('public/backoffice/sistema/sistema-banner-1.png') }});
      background-repeat: no-repeat;
      background-size: cover;
      background-position: center;
      width: 100%;
      height: 650px;
   }
@media only screen and (max-width: 500px){
    .imgbanner{
      background-image: url({{ url('public/backoffice/sistema/sistema-bannermovil-2.png') }});
   }
}*/
  </style>
@endsection
@section('scriptsbackoffice')
<script>
  $(window).resize(function() {
    var width = $(window).width();
    if (width <= 450){
      $('#imgbanner').attr('src','{{ url('public/backoffice/sistema/sistema-bannermovil-2.png') }}');
    }else{
      $('#imgbanner').attr('src','{{ url('public/backoffice/sistema/sistema-banner-2.png') }}');
    }
  });
</script>
@endsection