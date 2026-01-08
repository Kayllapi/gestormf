@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
	<section class="section section--details section--bg" data-bg="https://kayllapi.com/public/layouts/cinema/backoffice/img/section/section.jpg">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="section__wrap" style="margin-bottom: 10px;">
						<!-- section title -->
						<h1 class="section__title">{{ $cinepelicula->nombre }}</h1>
						<!-- end section title -->

						<!-- breadcrumb -->
						<ul class="breadcrumb">
							<li class="breadcrumb__item"><a href="{{url('backoffice/cine')}}">Ir a Inicio</a></li>
						</ul>
						<!-- end breadcrumb -->
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-12 col-xl-6">
					<div class="card card--details">
						<div class="row">
							<div class="col-12 col-sm-5 col-md-4 col-lg-3 col-xl-5">
								<div class="card__cover">
                        <?php
                        $urlimagen = url('/public/backoffice/web/cinepelicula/'.$cinepelicula->imagen);
                        if($cinepelicula->imagen==''){
                            $urlimagen = url('public/backoffice/sistema/sin_imagen_cuadrado.png');
                        }
                        ?>
									<img src="{{ $urlimagen }}">
								</div>
                <?php
                  $urlvideo = explode('&',$cinepelicula->urlvideotrailer);
                  $nueva_url = $cinepelicula->urlvideotrailer;
                  if(count($urlvideo)>1){
                      $nueva_url = $urlvideo[0];
                  }                
                ?>
                @if($cinepelicula->idcine_tipo==1)
								<a href="{{ $nueva_url }}" class="card__trailer"><i class="icon ion-ios-play-circle"></i> ver el trailer</a>
                @endif
							</div>
							<div class="col-12 col-md-8 col-lg-9 col-xl-7">
								<div class="card__content" >
									<ul class="card__meta">
										<li><span>Género:</span>
                      @foreach($cinepeliculacategorias as $value)
										  <a href="#">{{ $value->nombre }}</a>
                      @endforeach
                    </li>
										<li><span>Año de lanzamiento:</span>{{ $cinepelicula->fechapublicacion }}</li>
                    @if($cinepelicula->idcine_tipo==1)
										<li><span>Duración:</span> {{ $cinepelicula->duracionvideo }} min</li>
                    @endif
                    <li><span>Idioma:</span>{{ $cinepelicula->idioma }} </li>
									  <div class="card__description">
										    {{ $cinepelicula->descripcion }}
								  	</div>
                  </ul>
								</div>
							</div>
						</div>
					</div>
				</div>
             <!-- player -->
				<div class="col-12 col-xl-6">
          @if($cinepelicula->idcine_tipo==1)
          <?php $urlvideo = explode('drive.google.com',$cinepelicula->urlvideo) ?>
          @if(count($urlvideo)>1)
            <iframe src="{{ $cinepelicula->urlvideo }}" width="640" height="430" allow="autoplay" style="border-radius: 5px;"></iframe>
          @else
					<video controls playsinline id="player">
						<source src="{{ $cinepelicula->urlvideo }}" type="video/mp4" size="576">
						<source src="{{ $cinepelicula->urlvideo }}" type="video/webm" size="576">
						<source src="{{ $cinepelicula->urlvideo }}" type="video/flv" size="576">
            <object type="application/x-shockwave-flash" data="http://flv-player.net/medias/player_flv.swf" width="320" height="240" id="flashplayer">
               <param name="movie" value="player_flv.swf" />
               <param name="FlashVars" value="flv={{ $cinepelicula->urlvideo }}&autoload=1&autoplay=1" />
            </object>
					</video>
          @endif
          @elseif($cinepelicula->idcine_tipo==2)
                <div class="dashbox">
                  <?php $ii = 0; ?>
                          <?php
                          $cine_episodios = DB::table('cine_episodio')
                            ->where('idcine_pelicula',$cinepelicula->id)
                            ->select(
                                'cine_episodio.*'
                            )
                            ->orderBy('cine_episodio.orden','asc')
                            ->get();
                          $countm = count($cine_episodios);
                          $i = 1;
                          ?>
                

                  <div class="dashbox__table-wrap mCustomScrollbar _mCS_2 modulo" id="modulo{{ $ii }}" style="padding: 5px 15px;">
                    <div id="mCSB_2" class="mCustomScrollBox mCS-custom-bar2 mCSB_horizontal mCSB_outside" style="max-height: none;" tabindex="0">
                      <div id="mCSB_2_container" class="mCSB_container" style="position: relative; top: 0px; left: 0px; width: 501px; min-width: 100%; overflow-x: inherit;" dir="ltr">
                    <table class="main__table main__table--dash">
                      <tbody>
                          @foreach($cine_episodios as $valuemodulo)
                        <tr>
                          <td>
                            <div class="main__table-text">
                              <a href="{{ $valuemodulo->urlvideo }}" class="modal_video"> <i class="icon ion-ios-play-circle"></i> {{ str_pad($valuemodulo->orden, 2, "0", STR_PAD_LEFT) }} - {{ $valuemodulo->nombre }}</a>
                            </div>
                          </td>
                        </tr>
                          <?php $i++; ?>
                          @endforeach
                      </tbody>
                    </table>
                  </div></div>
                  </div>
                  <?php $ii++; ?>
                </div>
          @endif
				</div>
				<!-- end player -->    
				</div>
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

@if($cinepelicula->idcine_tipo==1)
<script>
  var player = document.getElementById("player");
  player.addEventListener('error', function() {
    player.parentNode.appendChild( document.getElementById("flashplayer") ); 
    player.parentNode.removeChild(player);
  }, true);
</script>

<style>
 /* ---- video ----- */
.plyr {
    height: 100%;
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
@elseif($cinepelicula->idcine_tipo==2)
<style>
 /* ---- video ----- */
.plyr {
    min-height: 350px !important;
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
    var video = '<video controls playsinline id="player">'+
        '<source src="'+url+'" type="video/mp4" size="576"></video>';
    $('#cont-videocurso').html(video);
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
    closeOnBgClick: false,
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
@endif
@endsection