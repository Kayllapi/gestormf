@extends('layouts.master')
@section('cuerpo')
<div id="cont-menu2-tienda" style="    background-color: rgb(0, 0, 0);
    display: none;
    border-top-right-radius: 5px;
    border-bottom-right-radius: 5px;
    margin-top: 5px;
    position: absolute;
    z-index: 1;">
    <a href="javascript:;" onclick="abrir_mapa('false')" style="color: #fff;
    display: block;
    padding: 12px;"><i class="fa fa-list"></i> Ver Lista</a>
</div>
<div class="col-list-wrap left-list" id="cont-menu1-tienda">
    <div class="listsearch-options fl-wrap" id="lisfw" >
            <div class="listsearch-header fl-wrap">
                <h3>Buscando: <span>{{ isset($_GET['search']) ? ($_GET['search']!='' ? $_GET['search'] : 'Todo') : 'Todo' }}</span></h3>
            </div>
    </div>
    <div class="list-main-wrap fl-wrap card-listing">
        <div class="container">
            <?php $numitem=0 ?>
            @foreach($tiendas as $value)
            <div class="listing-item list-layout">
                <article class="geodir-category-listing fl-wrap">
                        <?php
                        $rutaimagen = getcwd().'/public/backoffice/tienda/'.$value->id.'/portada/'.$value->imagenportada;
                        $imagenportada = url('public/backoffice/sistema/sin_imagen_cuadrado.png');
                        if(file_exists($rutaimagen) && $value->imagenportada!=''){
                            $imagenportada =  url('/public/backoffice/tienda/'.$value->id.'/portada/'.$value->imagenportada);
                        }
                        ?>
                    <div class="geodir-category-img"
                        style="background-image: url({{ $imagenportada }});
                                background-repeat: no-repeat;
                                background-size: cover;
                                background-position: center;">
                        <div class="overlay"></div>
                        <?php
                        $recomendaciones = DB::table('recomendacion')->where('idtienda',$value->id)->count();
                        ?>

                        <div class="listing-avatar">
                            <a href="{{ url($value->link) }}">
                            <?php 
                            $rutaimagen = getcwd().'/public/backoffice/tienda/'.$value->id.'/logo/'.$value->imagen;
                            ?>
                            @if(file_exists($rutaimagen) && $value->imagen!='')
                                <img src="{{ url('public/backoffice/tienda/'.$value->id.'/logo/'.$value->imagen) }}" alt="{{ $value->nombre }}">
                            @else
                                <img src="{{ url('public/backoffice/sistema/sin_imagen_redondo.png') }}" alt="">
                            @endif
                            </a>
                        </div>
                        <a href="{{ url($value->link) }}" class="list-post-counter"><span>{{ $recomendaciones }}</span><i class="fa fa-heart"></i></a>
                    </div>
                    <div class="geodir-category-content fl-wrap">
                        <h3><a href="{{ url($value->link) }}">{{ $value->nombre }}</a></h3>
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
                            <div class="geodir-category-location">
                                <a href="#{{ $numitem }}" onclick="abrir_mapa('true')" class="map-item btn-localizacion{{ $numitem }}">
                                <div>
                                    <i class="fa fa-map-marker"></i>{{ $value->ubigeonombre }}<br>
                                    <i class="fa fa-phone"></i>{{ $value->numerotelefono }}
                                </div> 
                                </a>
                            </div>
                        </div>
                        <a class="listing-geodir-category" href="{{ url($value->link) }}"><i class="fa fa-home"></i> Ver Tienda</a>
                        <a class="listing-geodir-category" href="javascript:;" onclick="click_abrir_mapa('{{ $numitem }}')"  style="background: -webkit-linear-gradient(top, #31353D, #5a5e65);"><i class="fa fa-map-marker"></i> Ver Ubicación</a>
                    </div>
                </article>
            </div>
            <?php $numitem++ ?>
            @endforeach
        </div>
    </div>
    {{ $tiendas->links('app.tablepagination', ['results' => $tiendas]) }}
</div>
<div class="map-container column-map right-pos-map">
    <div id="map-main"></div>
    <ul class="mapnavigation">
        <li><a href="#" class="prevmap-nav">Atras</a></li>
        <li><a href="#" class="nextmap-nav">Siguiente</a></li>
    </ul>
    <div class="scrollContorl mapnavbtn" title="Enable Scrolling"><span><i class="fa fa-lock"></i></span></div>                         
</div>
<div class="limit-box fl-wrap"></div>
@endsection
@section('scripts')
<style>
  /* imagen mapa */
.map-popup img {
    height: 170px;
}
.card-listing .geodir-category-location a {
    color: #130101;
}
</style>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAyFtw-yEfh6GtiPyx_4ZQWt3g_vUCu5eQ&libraries=places&callback=initAutocomplete"></script>
<script src="{{ url('public/layouts/js/map_infobox.js') }}"></script>
<script src="{{ url('public/layouts/js/markerclusterer.js') }}"></script>  
<script>
function abrir_mapa(estado){
    var width_body = $('body').width();
    if(width_body<=1045){
        if(estado=='true'){
            $('#cont-menu1-tienda').css('display','none');
            $('#cont-menu2-tienda').css('display','block');
        }else{
            $('#cont-menu1-tienda').css('display','block');
            $('#cont-menu2-tienda').css('display','none');
        } 
    }  
}
function click_abrir_mapa(item){
    $('.btn-localizacion'+item).click()  
}

/* ------------ REDIMENSIONAR IMAGEN --------------*/
/*redimensionar(); 
$(window).resize(function(){
    redimensionar(); 
})
function redimensionar(){
    var ancho = $(".geodir-category-img").width();
    $('.geodir-category-img>img').each(function() { 
        var idtienda = $(this).attr('idtienda');
        if(idtienda!=undefined){
            var imagenportada = $(this).attr('imagenportada');
            $(this).attr('src','{{ url('redimensionar/tienda/portada/') }}/'+ancho+'/170/'+idtienda+'/'+imagenportada);
        }
            
    }); 
}*/
/* ------------ GOOGLE MAPS --------------*/
  
(function ($) {
    "use strict";
    var markerIcon = {
        anchor: new google.maps.Point(22, 16),
        url: '{{ url('public/backoffice/sistema/marker.png') }}',
    }

    function mainMap() {
        function locationData(tienda_link, categoria_nombre, url_imgportada, tienda_nombre, tienda_direccion, tienda_ubigeo, tienda_numerotelefono, suma_calificacion, total_calificacion) {
            return ('<div class="map-popup-wrap"><div class="map-popup"><div class="infoBox-close"><i class="fa fa-times"></i></div><div class="map-popup-category">' + categoria_nombre + '</div><a href="' + tienda_link + '" class="listing-img-content fl-wrap"><div style="background-image: url(' + url_imgportada + ');background-repeat: no-repeat;background-size: cover;background-position: center;height: 170px;background-color: #fff;"></div></a> <div class="listing-content fl-wrap"><div class="card-popup-raining map-card-rainting" data-staRrating="' + suma_calificacion + '"><span class="map-popup-reviews-count">( ' + total_calificacion + ' calificaciones )</span></div><div class="listing-title fl-wrap"><h4><a href=' + tienda_link + '>' + tienda_nombre + '</a></h4><span class="map-popup-location-info"><i class="fa fa-map-marker"></i> ' + tienda_direccion + '</span><span class="map-popup-location-info"><i class="fa fa-globe"></i>' + tienda_ubigeo + '</span><span class="map-popup-location-phone"><i class="fa fa-phone"></i>' + tienda_numerotelefono + '</span></div></div></div></div>')
        }
        var locations = [
            <?php $i = 0 ?>
            @foreach($tiendas as $value)
            [locationData(
              '{{ url($value->link) }}', 
              '{{ $value->categorianombre }}', 
              '<?php 
                $rutaimagen = getcwd().'/public/backoffice/tienda/'.$value->id.'/portada/'.$value->imagenportada;
                $imagenportada = url('public/backoffice/sistema/sin_imagen_cuadrado.png');
                if(file_exists($rutaimagen) && $value->imagenportada!=''){
                    $imagenportada =  url('/public/backoffice/tienda/'.$value->id.'/portada/'.$value->imagenportada);
                }
                echo $imagenportada;
                ?>',
              "{{ $value->nombre }}", 
              "{{ $value->direccion }}", 
              "{{ $value->ubigeonombre }}", 
              "{{ $value->numerotelefono }}", 
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
              "{{ $totalsumacalificacion }}", 
              "{{ $cantidadcalificacion }}"
            ),{{ $value->mapa_ubicacion_lat }}, {{ $value->mapa_ubicacion_lng }}, {{ $i }}, markerIcon],
            <?php $i++ ?>
            @endforeach
        ];

        var map = new google.maps.Map(document.getElementById('map-main'), {
            zoom: 9,
            scrollwheel: false,
            center: new google.maps.LatLng(-12.06863, -75.210192),
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            zoomControl: false,
            mapTypeControl: false,
            scaleControl: false,
            panControl: false,
            fullscreenControl: true,
            navigationControl: false,
            streetViewControl: false,
            animation: google.maps.Animation.BOUNCE,
            gestureHandling: 'cooperative',
            styles: [{
                "featureType": "administrative",
                "elementType": "labels.text.fill",
                "stylers": [{
                    "color": "#444444"
                }]
            }]
        });


        var boxText = document.createElement("div");
        boxText.className = 'map-box'
        var currentInfobox;
        var boxOptions = {
            content: boxText,
            disableAutoPan: true,
            alignBottom: true,
            maxWidth: 0,
            pixelOffset: new google.maps.Size(-145, -45),
            zIndex: null,
            boxStyle: {
                width: "260px"
            },
            closeBoxMargin: "0",
            closeBoxURL: "",
            infoBoxClearance: new google.maps.Size(1, 1),
            isHidden: false,
            pane: "floatPane",
            enableEventPropagation: false,
        };
        var markerCluster, marker, i;
        var allMarkers = [];
        var clusterStyles = [{
            textColor: 'white',
            url: '',
            height: 50,
            width: 50
        }];


        for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                icon: locations[i][4],
                id: i
            });
            allMarkers.push(marker);
            var ib = new InfoBox();
            google.maps.event.addListener(ib, "domready", function () {
                cardRaining()
            });
            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                return function () {
                    ib.setOptions(boxOptions);
                    boxText.innerHTML = locations[i][0];
                    ib.close();
                    ib.open(map, marker);
                    currentInfobox = marker.id;
                    var latLng = new google.maps.LatLng(locations[i][1], locations[i][2]);
                    map.panTo(latLng);
                    map.panBy(0, -180);
                    google.maps.event.addListener(ib, 'domready', function () {
                        $('.infoBox-close').click(function (e) {
                            e.preventDefault();
                            ib.close();
                        });
                    });
                }
            })(marker, i));
        }
        var options = {
            imagePath: 'images/',
            styles: clusterStyles,
            minClusterSize: 2
        };
        markerCluster = new MarkerClusterer(map, allMarkers, options);
        google.maps.event.addDomListener(window, "resize", function () {
            var center = map.getCenter();
            google.maps.event.trigger(map, "resize");
            map.setCenter(center);
        });

        $('.nextmap-nav').click(function (e) {
            e.preventDefault();
            map.setZoom(15);
            var index = currentInfobox;
            if (index + 1 < allMarkers.length) {
                google.maps.event.trigger(allMarkers[index + 1], 'click');
            } else {
                google.maps.event.trigger(allMarkers[0], 'click');
            }
        });
        $('.prevmap-nav').click(function (e) {
            e.preventDefault();
            map.setZoom(15);
            if (typeof (currentInfobox) == "undefined") {
                google.maps.event.trigger(allMarkers[allMarkers.length - 1], 'click');
            } else {
                var index = currentInfobox;
                if (index - 1 < 0) {
                    google.maps.event.trigger(allMarkers[allMarkers.length - 1], 'click');
                } else {
                    google.maps.event.trigger(allMarkers[index - 1], 'click');
                }
            }
        });
        $('.map-item').click(function (e) {
            e.preventDefault();
     		map.setZoom(15);
            var index = currentInfobox;
            var marker_index = parseInt($(this).attr('href').split('#')[1], 10);
            google.maps.event.trigger(allMarkers[marker_index], "click");
			if ($(this).hasClass("scroll-top-map")){
			  $('html, body').animate({
				scrollTop: $(".map-container").offset().top+ "-80px"
			  }, 500)
			}
			else if ($(window).width()<1064){
			  $('html, body').animate({
				scrollTop: $(".map-container").offset().top+ "-80px"
			  }, 500)
			}
        });
      // Scroll enabling button
      var scrollEnabling = $('.scrollContorl');

      $(scrollEnabling).click(function(e){
          e.preventDefault();
          $(this).toggleClass("enabledsroll");

          if ( $(this).is(".enabledsroll") ) {
             map.setOptions({'scrollwheel': true});
          } else {
             map.setOptions({'scrollwheel': false});
          }
      });		
        var zoomControlDiv = document.createElement('div');
        var zoomControl = new ZoomControl(zoomControlDiv, map);

        function ZoomControl(controlDiv, map) {
            zoomControlDiv.index = 1;
            map.controls[google.maps.ControlPosition.RIGHT_CENTER].push(zoomControlDiv);
            controlDiv.style.padding = '5px';
            var controlWrapper = document.createElement('div');
            controlDiv.appendChild(controlWrapper);
            var zoomInButton = document.createElement('div');
            zoomInButton.className = "mapzoom-in";
            controlWrapper.appendChild(zoomInButton);
            var zoomOutButton = document.createElement('div');
            zoomOutButton.className = "mapzoom-out";
            controlWrapper.appendChild(zoomOutButton);
            google.maps.event.addDomListener(zoomInButton, 'click', function () {
                map.setZoom(map.getZoom() + 1);
            });
            google.maps.event.addDomListener(zoomOutButton, 'click', function () {
                map.setZoom(map.getZoom() - 1);
            });
        }


    }
    var map = document.getElementById('map-main');
    if (typeof (map) != 'undefined' && map != null) {
        google.maps.event.addDomListener(window, 'load', mainMap);
    }
})(this.jQuery);</script>

@endsection
