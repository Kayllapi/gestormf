@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
	<section class="section section--details section--bg" style="background-color: #19181e">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="section__wrap" style="margin-bottom: 10px;">
						<!-- section title -->
						<h1 class="section__title">{{ $curso->nombre }}</h1>
						<!-- end section title -->

						<!-- breadcrumb -->
						<ul class="breadcrumb">
							<li class="breadcrumb__item"><a href="{{url('backoffice/aulavirtual')}}">Ir a Inicio</a></li>
						</ul>
						<!-- end breadcrumb -->
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12 col-xl-6">
					<div class="card card--details">
                  <div id="cont-videocurso" style="display:none;width: 100%;">
                    <video id="player" controls playsinline>
                      <source id="source1" src="" type="video/mp4" size="576">
                      <source id="source2" src="" type="video/webm" size="576">
                      <source id="source3" src="" type="video/flv" size="576">
                      <object type="application/x-shockwave-flash" data="http://flv-player.net/medias/player_flv.swf" width="320" height="240" id="flashplayer">
                         <param name="movie" value="player_flv.swf" />
                         <param name="FlashVars" id="object" value="flv=&autoload=1&autoplay=1" />
                      </object>
                    </video>
                  </div>
                  <div id="cont-imagencurso">
                    <div class="card__cover" style="height: 350px;">
                      <?php 
                       $rutaimagen = getcwd().'/public/backoffice/usuario/'.$curso->idusers.'/aulavirtual/'.$curso->imagen;
                       if(file_exists($rutaimagen)){
                          $imagen = url('/public/backoffice/usuario/'.$curso->idusers.'/aulavirtual/'.$curso->imagen);
                       }else{
                          $imagen = url('public/backoffice/sistema/sin_imagen_cuadrado.png');
                       }
                       ?>
                      <img src="{{ $imagen }}" style="border-radius: 5px;">
                    </div>
                  </div>
                
                <div class="dashbox" style="width: 100%;">
                  <div class="dashbox__title">
                    <h3 style="font-size: 16px;">Categoria: {{ $curso->categorianombre }}</h3>
                  </div>
                  <div class="dashbox__title">
                    <h3 style="font-size: 16px;">Autor: {{ $curso->autor }}</h3>
                  </div>
                  <div class="dashbox__title">
                    <h3 style="font-size: 16px;">Idioma: {{ $curso->idiomanombre }}</h3>
                  </div>
                </div>
                
								<div class="card__content">
									<ul class="card__meta" style="width: 100%;">
									  <div class="card__description">
										    {{ $curso->descripcion }}
								  	</div>
                  </ul>
								</div>
					</div>
				</div>
             <!-- player -->
				<div class="col-12 col-xl-6" style="padding-left: 20px;padding-right: 20px;">
          
              
                <div class="dashbox">
                  <?php $ii = 0; ?>
                  @foreach($modulos as $value)
                  <a href="javascript:;" class="dashbox__title" onclick="mostrar_modulo({{ $ii }})">
                    <h3><i class="icon ion-ios-film"></i> {{ $value->nombre }}</h3>
                  </a>

                  <div class="dashbox__table-wrap mCustomScrollbar _mCS_2 modulo" style="overflow: visible;<?php echo $ii==0?'display:block;':'display:none;' ?>" id="modulo{{ $ii }}">
                    <div id="mCSB_2" class="mCustomScrollBox mCS-custom-bar2 mCSB_horizontal mCSB_outside" style="max-height: none;" tabindex="0">
                      <div id="mCSB_2_container" class="mCSB_container" style="position: relative; top: 0px; left: 0px; width: 501px; min-width: 100%; overflow-x: inherit;" dir="ltr">
                    <table class="main__table main__table--dash">
                      <tbody>
                          <?php
                          $cursomodulotemas = DB::table('cursomodulotema')
                            ->where('idcursomodulo',$value->id)
                            ->orderBy('id','asc')
                            ->get();
                          $countm = count($cursomodulotemas);
                          $i = 1;
                          ?>
                          @foreach($cursomodulotemas as $valuemodulo)
                        <tr>
                          <td>
                            <div class="main__table-text">
                              <a href="javascript:;" onclick="vervideo('{{ $valuemodulo->urlvideo }}')" style="width: 100%;"> <i class="icon ion-ios-play-circle"></i> {{ $valuemodulo->nombre }}</a>
                            </div>
                          </td>
                        </tr>
                          <?php $i++; ?>
                          @endforeach
                      </tbody>
                    </table>
                  </div></div>
                    <!--div id="mCSB_2_scrollbar_horizontal" class="mCSB_scrollTools mCSB_2_scrollbar mCS-custom-bar2 mCSB_scrollTools_horizontal" style="display: block;">
                    <div class="mCSB_draggerContainer">
                      <div id="mCSB_2_dragger_horizontal" class="mCSB_dragger" style="position: absolute; min-width: 30px; width: 499px; left: 0px; display: block; max-width: 490px;">
                    <div class="mCSB_dragger_bar"></div>
                    <div class="mCSB_draggerRail"></div></div></div></div-->
                  </div>
                  <?php $ii++; ?>
                  @endforeach
                </div>
          
				</div>
				<!-- end player -->    
				</div>
			</div>
	</section>
@endsection
@section('scriptsbackoffice')
        <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/bootstrap-reboot.min.css') }}">
        <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/bootstrap-grid.min.css') }}">
        <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/jquery.mCustomScrollbar.min.css') }}">
        <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/nouislider.min.css') }}">
        <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/ionicons.min.css') }}">
        <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/magnific-popup.css') }}">
        <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/plyr.css') }}">
        <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/photoswipe.css') }}">
        <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/default-skin.css') }}">
        <link rel="stylesheet" href="{{ url('public/layouts/cinema/backoffice/css/main.css') }}">

        <script src="{{ url('public/layouts/cinema/backoffice/js/jquery-3.5.1.min.js') }}"></script>
        <script src="{{ url('public/layouts/cinema/backoffice/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ url('public/layouts/cinema/backoffice/js/owl.carousel.min.js') }}"></script>
        <script src="{{ url('public/layouts/cinema/backoffice/js/jquery.magnific-popup.min.js') }}"></script>
        <script src="{{ url('public/layouts/cinema/backoffice/js/jquery.mousewheel.min.js') }}"></script>
        <script src="{{ url('public/layouts/cinema/backoffice/js/jquery.mCustomScrollbar.min.js') }}"></script>
        <script src="{{ url('public/layouts/cinema/backoffice/js/wNumb.js') }}"></script>
        <script src="{{ url('public/layouts/cinema/backoffice/js/nouislider.min.js') }}"></script>
        <script src="{{ url('public/layouts/cinema/backoffice/js/plyr.min.js') }}"></script>
        <script src="{{ url('public/layouts/cinema/backoffice/js/photoswipe.min.js') }}"></script>
        <script src="{{ url('public/layouts/cinema/backoffice/js/photoswipe-ui-default.min.js') }}"></script>
        <script src="{{ url('public/layouts/cinema/backoffice/js/main.js') }}"></script>
<style>
  /* ---- video ----- */
.plyr {
    height: 350px;
    min-height: 350px !important;
}
@media (min-width: 1440px){
    .plyr video {
        height:100% !important;
    } 
}
@media (min-width: 1200px){
    .plyr video {
        height:100% !important;
    }
}
.plyr__video-wrapper {
    height:100%;
}
 /* ---- fin video ----- */
 .page-content {
      background-color:#1a191f;
}
.content {
      background-color:#1a191f;
}
 .section {
   padding: 20px 0px 40px 0
  }
</style>
<script>
function vervideo(url){
    $('#cont-videocurso').css('display','block');
    $('#cont-imagencurso').css('display','none');
  
    $('#player').attr('src',url);
    $('#source1').attr('src',url);
    $('#source2').attr('src',url);
    $('#source3').attr('src',url);
    $('#object').attr('src','flv='+url+'&autoload=1&autoplay=1');
}
function mostrar_modulo(num){
    $('.modulo').css('display','none');
    $('#modulo'+num).css('display','block');
}
  
$('.modal_video').magnificPopup({
		disableOn: 500,
		fixedContentPos: true,
		type: 'iframe',
		preloader: false,
		removalDelay: 300,
		mainClass: 'mfp-fade',
		callbacks: {
			open: function() {
				if ($(window).width() > 1200) {
					$('.header').css('margin-left', "-" + (getScrollBarWidth()/2) + "px");
				}
			},
			close: function() {
				if ($(window).width() > 1200) {
					$('.header').css('margin-left', 0);
				}
			}
		}
	});
</script>
@endsection