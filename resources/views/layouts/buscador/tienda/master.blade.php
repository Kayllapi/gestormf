@extends('layouts.master')
@section('cuerpo')
<?php
$tienda = tienda_link();
$recomendaciones = DB::table('recomendacion')
      ->where('idtienda',$tienda->id)
      ->where('idtiporecomendacion',1)
      ->count();
$s_categorias = DB::table('s_categoria')
      ->where('idtienda',$tienda->id)
      ->where('s_idcategoria',0)
      ->orderBy('s_categoria.nombre','asc')
      ->get();
?>
<section class="parallax-section single-par list-single-section" data-scrollax-parent="true" id="sec-inicio">
    <div class="bg par-elem"
         <?php $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/portada/'.$tienda->imagenportada; ?>
         @if(file_exists($rutaimagen))
            data-bg="{{ url('public/backoffice/tienda/'.$tienda->id.'/portada/'.$tienda->imagenportada) }}"
         @else
            data-bg="{{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }}"
         @endif
         data-scrollax="properties: { translateY: '30%' }"></div>
    <div class="overlay"></div>
    <div class="bubble-bg"></div>
    <div class="list-single-header absolute-header fl-wrap">
        <div class="container">
            <div class="list-single-header-item" style="margin-bottom: 20px;margin-left: 5px;margin-right: 5px;">
                <div class="list-single-header-item-opt fl-wrap">
                    <div class="list-single-header-cat fl-wrap" id="cont-galery-logo">
                        <?php 
                        $rutaimagen = getcwd().'/public/backoffice/tienda/'.$tienda->id.'/logo/'.$tienda->imagen;
                        $imglogo = url('public/backoffice/sistema/sin_imagen_redondo.png');
                        if(file_exists($rutaimagen)){
                            $imglogo = url('public/backoffice/tienda/'.$tienda->id.'/logo/'.$tienda->imagen);
                        }
                        ?>
                        <a href="{{ $imglogo }}" class="popup-image-logo">
                              <img src="{{ $imglogo }}">
                        </a>
                        <span>  Abierto <i class="fa fa-check"></i></span>
                    </div>
                </div>
                <h2>{{ $tienda->nombre }} <span> - </span><a href="{{ url($tienda->link) }}">{{ $tienda->categorianombre }}</a> </h2>
                <span class="section-separator"></span>
                @if($tienda->link!='')
                <?php
                $calificacion = DB::table('calificacion')
                    ->where('idtienda',$tienda->id)
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
                    <span>({{ $cantidadcalificacion }})</span>
                </div>
                    <a href="#sec-calificacion" class="custom-scroll-link list-post-counter single-list-post-counter">
                      <span id="spancalificacion">@if(Auth::user())
                                    <?php
                                    $calificacion = DB::table('calificacion')
                                        ->where('idtienda',$tienda->id)
                                        ->where('idusers',Auth::user()->id)
                                        ->first();
                                    $numerocalificacion = 0;
                                    if($calificacion!=''){
                                        $numerocalificacion = $calificacion->numero;
                                    }
                                    echo 'Calificación - '.$numerocalificacion;
                                    ?>
                                @else
                                Calificar
                                @endif</span><i class="fa fa-star"></i></a>
                @if(Auth::user())
                    <?php
                    $recomendacionvalid = DB::table('recomendacion')
                        ->where('idtienda',$tienda->id)
                        ->where('idusers',Auth::user()->id)
                        ->where('idtiporecomendacion',1)
                        ->count();
                    if($recomendacionvalid>0){
                        $stylecont = 'style="background-color:#82f3b278;"';
                        $styleicon = 'style="color: #2ecc71;"';
                    }else{
                        $stylecont = '';
                        $styleicon = '';
                    }
                    ?>
                    <form class="form-recomendar" action="javascript:;" 
                      onsubmit="callback({
                            route: 'backoffice/tienda',
                            method: 'POST',
                            carga: '',
                            data: {
                              idtienda: '{{ $tienda->id }}',
                              idusers: '{{ Auth::user()->id }}',
                              view: 'recomendar',
                            }
                        },
                        function(resultado){
                            if(resultado.estadoregistrar==1){
                                var countrecomendar = parseInt($('#countrecomendar>span').html());       
                                $('#countrecomendar>span').html(countrecomendar+1);
                                $('#countrecomendar').css('background-color','#82f3b278');
                                $('#countrecomendar>i').css('color','#2ecc71');
                            }else if(resultado.estadoregistrar==2){
                                var countrecomendar = parseInt($('#countrecomendar>span').html());       
                                $('#countrecomendar>span').html(countrecomendar-1);
                                $('#countrecomendar').removeAttr('style');
                                $('#countrecomendar>i').removeAttr('style');
                            }
                        },this)">
                    </form>
                    <a href="javascript:;" class="list-post-counter single-list-post-counter" <?php echo $stylecont ?> onclick="recomendar()" id="countrecomendar">
                      <span>{{ $recomendaciones }}</span><i class="fa fa-heart" <?php echo $styleicon ?>></i>
                    </a>  
                @else
                    <a href="javascript:;" class="list-post-counter single-list-post-counter"><span>{{ $recomendaciones }}</span><i class="fa fa-heart"></i></a>
                @endif
                @endif
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-7 ">
                        <div class="list-single-header-contacts fl-wrap">
                            <ul>
                                @if($tienda->numerotelefono!='')
                                <li><i class="fa fa-phone"></i><a  href="https://wa.me/{{ $tienda->codigotelefonicocodigo }}{{ str_replace(' ','',$tienda->numerotelefono) }}?text=Hola!+Le+escribo+de+la+tienda+virtual+de+{{ $tienda->nombre }}!" target="_blank">
                                  ({{ $tienda->codigotelefonicocodigo }}) {{ $tienda->numerotelefono }}</a></li>
                                @endif
                                @if($tienda->direccion!='')
                                <li><i class="fa fa-map-marker"></i><a href="javascript:;" id="modal-ubicacion" onclick="initMapUbicacion()">{{ $tienda->direccion }}</a></li>
                                @endif
                                @if($tienda->paginaweb!='')
                                <li><i class="fa fa-globe"></i><a href="{{ $tienda->paginaweb }}" target="_blank">{{ $tienda->paginaweb }}</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="fl-wrap list-single-header-column">
                            @if(Auth::user()) 
                            @if(Auth::user()->idtienda==$tienda->id)
                            <div class="share-holder hid-share">
                                <a href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio') }}" class="btn  big-btn circle-btn dec-btn flat-btn"><i class="fa fa-cloud"></i> Ir a Sistema</a>
                            </div>
                            @else
                                <?php
                                $sistematienda = DB::table('tienda')
                                  ->where('tienda.link',$tienda->link)
                                  ->where('tienda.idusers',Auth::user()->id)
                                  ->limit(1)
                                  ->first();
                                ?>
                                @if($sistematienda!='')
                                <div class="share-holder hid-share">
                                    <a href="{{ url('backoffice/tienda/sistema/'.$sistematienda->id.'/inicio') }}" class="btn  big-btn circle-btn dec-btn flat-btn"><i class="fa fa-cloud"></i> Ir a Sistema</a>
                                </div>
                                @endif
                            @endif
                            @else
                            <div class="share-holder hid-share">
                                <a href="{{ url($tienda->link.'/login') }}" class="btn  big-btn circle-btn dec-btn flat-btn btn-primary"><i class="fa fa-user"></i> Iniciar Sesión</a>
                            </div>
                            @endif
                            <a href="javascript:;" id="modal-ubicacion" class="custom-scroll-link" onclick="initMapUbicacion()"> <i class="fa fa-map-marker"></i> Ver Ubicación</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>  
<!--  section  -->
<div style="background-color: #31353D;
    float: left;
    width: 100%;">
<div class="container">
    <div class="row">
        <div class="col-md-7">
            <div class="cssmenu" id="cssmenu">
              <ul>
                  <?php $listurl = explode('/',$_SERVER["REQUEST_URI"]) ?>
                 <li class="<?php echo (!isset($_GET['pagina'])&&count($listurl)==2)?'active':'' ?>"><a href="{{ url($tienda->link) }}">Inicio</a></li>
                 <li class="<?php echo (!isset($_GET['pagina'])&&count($listurl)==3)?'active':'' ?> has-sub"><a href="javascript:;">Productos/Servicios</a>
                    <ul>
                        @foreach($s_categorias as $value)
                            <?php 
                            $subcategorias = DB::table('s_categoria')
                                ->where('s_idcategoria',$value->id)
                                ->get(); 
                            ?>
                            <li>
                                <a href="{{ url($tienda->link.'/categoria/'.str_replace(' ','-',mb_strtolower($value->nombre))) }}">{{ ucfirst(mb_strtolower($value->nombre)) }}</span></a>
                                @if(count($subcategorias)>0)
                                <ul>
                                    @foreach($subcategorias as $subvalue)
                                    <li><a href="{{ url($tienda->link.'/categoria/'.str_replace(' ','-',mb_strtolower($value->nombre)).'/'.str_replace(' ','-',mb_strtolower($subvalue->nombre))) }}">{{ ucfirst(mb_strtolower($subvalue->nombre)) }}</a></li>
                                    @endforeach
                                </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                 </li>
                 <li class="<?php echo isset($_GET['pagina'])?($_GET['pagina']=='informacion'?'active':''):'' ?> has-sub"><a href="javascript:;">Nosotros</a>
                    <ul>
                        <li class="<?php echo isset($_GET['pagina'])?($_GET['pagina']=='informacion'?'active':''):'' ?>"><a href="{{ url($tienda->link) }}/pagina/informacion">Información</a></li>
                        <li class="<?php echo isset($_GET['pagina'])?($_GET['pagina']=='comprobante'?'active':''):'' ?>"><a href="{{ url($tienda->link) }}/pagina/comprobante">Comprobante</a></li>
                        <li class="<?php echo isset($_GET['pagina'])?($_GET['pagina']=='comentario'?'active':''):'' ?>"><a href="{{ url($tienda->link) }}/pagina/comentario">Comentarios</a></li>
                    </ul>
                 </li>
              </ul>
            </div>
        </div>
        <div class="col-md-3">
            <div class="box-widget search-widget" style="border: 0px;margin-top: 4px;margin-bottom: 2px;">
                <form action="{{ url($tienda->link.'/pagina/buscar') }}" class="fl-wrap">
                    <input type="text" value="{{ isset($_GET['search']) ? $_GET['search'] : '' }}" name="search" class="search" placeholder="Buscar en la Tienda.."/>
                    <button class="search-submit" id="submit_btn"><i class="fa fa-search transition"></i> </button>
                </form>
            </div>
        </div>
        <div class="col-md-2" style="float: left;">
            <a href="javascript:;" class="btn btn-info" id="modal-carritocompra" onclick="selectcarritocompra()" style="margin-top: 4px;margin-bottom: 4px;padding: 15px;">
                <i class="fa fa-shopping-cart"></i> Carrito de compra 
                  @if(Session::has('carritocompra'))
                    (<span id="cantidad-pedido-carritocompra">{{count(session('carritocompra'))}}</span>)
                  @else
                    (<span id="cantidad-pedido-carritocompra">0</span>)
                  @endif
              
            </a>
        </div>
    </div>
</div>
</div> 
<section class="gray-section no-top-padding">
    <div class="container">
        @yield('cuerpotienda')
    </div>
</section>
<!--  section end --> 
<div class="limit-box fl-wrap"></div>
@endsection
@section('htmls')
<!--  modal reportar --> 
<div class="main-register-wrap modal-reportar">
    <div class="main-overlay"></div>
    <div class="main-register-holder">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Reportar la Tienda</h3>
            <div class="custom-form">
                <div class="col-md-12">
                    <form action="javascript:;" 
                          onsubmit="callback({
                              route: 'backoffice/tienda',
                              method: 'POST',
                              data: {
                                view: 'reportartienda',
                                idtienda: {{$tienda->id}}
                              }
                          },
                          function(resultado){
                              if (resultado.resultado == 'CORRECTO') {
                                location.href = '{{ Request::fullUrl() }}';                                                  
                              }
                          },this)">
                        <label>Tienda a reportar </label>
                        <input id="nombretienda" type="text" value="{{ $tienda->nombre }}" disabled>
                        <label >Motivo * </label>
                        <textarea id="motivo" cols="30" rows="10"></textarea>
                        <button type="submit"  class="log-submit-btn"><span>Enviar</span></button>
                        <div class="clearfix"></div>
                    </form>
                </div>
              </div>
        </div>
    </div>
</div>
<!--  fin modal reportar --> 
<!--  modal ubicacion --> 
<div class="main-register-wrap modal-ubicacion">
    <div class="main-overlay"></div>
    <div class="main-register-holder">
        <div class="main-register fl-wrap" style="padding: 0px;">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Ubicación</h3>
            <div class="mx-modal-cuerpo">
              <div class="custom-form">
              <label>Modo de Ruta</label>
              <select id="modoruta">
                <option value="DRIVING">Conduciendo</option>
                <option value="WALKING">Caminando</option>
                <!--option value="BICYCLING">En Bicicleta</option>
                <option value="TRANSIT">Tránsito</option-->
              </select>
              <div style="background-color: #008cea;
                    color: #fff;
                    padding: 10px;
                    margin-bottom: 10px;
                    border-radius: 5px;
                    font-size: 16px;
                    font-weight: bold;"><span id="ubi_distancia">O KM </span> - <strong id="ubi_tiempo">---</strong>
              </div>
              </div>
              <div class="map-container">
                  <div id="singleMap"></div>
              </div>
            </div>
        </div>
    </div>
</div>
<!--  modal ubicacion --> 
@if(!Auth::user())
@endif
@endsection
@section('scripts')
<link rel="stylesheet" href="{{ url('public/layouts/css/menuhorizontal_1.css') }}">
<script src="{{ url('public/layouts/js/menuhorizontal_1.js') }}"></script>
<style>
  .color-bg,
  .sw-btn,
  .tabs-menu li.current a, .tabs-menu li a:hover,
  .tabs-menu li.current div, .tabs-menu li div:hover,
  .custom-form .log-submit-btn,
  .close-reg,
  .lg-actions .lg-prev,
   .lg-actions .lg-next,
  .showshare,
  .cssmenu ul ul li a,
  .btn.flat-btn,
  .pagination a.current-page,
  .box-item a.gal-link,
.search-widget .search-submit,
#menu-line,
.list-single-main-wrapper .breadcrumbs,
.btn-info,
.price-link,
.section-separator:before,
.parallax-section .section-separator:before,
  .btn.transparent-btn:hover{
    background: {{$tienda->ecommerce_color}};
}
  .radio input[type="radio"]:checked + span:before,
  .reviews-comments-item-date i,
  .list-single-main-item-title span,
  .list-single-header-cat span i,
  .post-opt li i,
  .cssmenu > ul > li:hover > a, .cssmenu > ul > li.active > a,
  .list-single-header-column .custom-scroll-link i,
  .list-single-header-contacts li i,
  .list-post-counter.single-list-post-counter i{
    color: {{$tienda->ecommerce_color}};
  }
.btn.transparent-btn,
.cssmenu > ul > li.has-sub:hover > a::after {
    border-color: {{$tienda->ecommerce_color}};
}

</style>
<style>
.mx-gallery-item {
    width: 100%;
}
#singleMap {
    height: 500px;
    border-radius: 5px;
    border:1px solid #aaa;
}</style>
<script>
//   logo------------------
    var o = $("#cont-galery-logo"),
        p = o.data("looped");
    o.lightGallery({
        selector: "#cont-galery-logo a.popup-image-logo",
        cssEasing: "cubic-bezier(0.25, 0, 0.25, 1)",
        download: false,
        loop: false,
		    counter: false
    });
  
modal({click:'#modal-reportar'});
modal({click:'#modal-ubicacion'}); 
tab({click:'#tab-iniciarsesion'});
function recomendar(){
    $(".form-recomendar").submit();
}
function calificar(){
    $(".form-calificar").submit();
    $('#tab-inicio').click();
}
//carrito de compra

/*function selectproducto(idproducto){
    $('#contenido-producto').html('<div class="mx-alert-load"><img src="{{ url('/public/libraries/app/img/loading.gif') }}"></div>');  
    $.ajax({
        url:"{{ url($tienda->link.'/'.(isset($s_categoria->id)?$s_categoria->id:'0').'/') }}/"+idproducto,
        type:'GET',
        data: {
            view : 'selectproducto'
        },
        success: function (respuesta){
            $('#contenido-producto').html(respuesta);
        }
    })
}*/
</script>
<script>
/*function select_carritocompra(){
    var data = '';
    $("#tabla-tiendaproducto > tbody > tr").each(function() {
        var num = $(this).attr('id');        
        var idproducto = $(this).attr('idproducto');
        var producto_cantidad = $("#producto_cantidad"+num).val();
        var producto_precioalpublico = $("#producto_precioalpublico"+num).val();
        data = data+'&'+idproducto+','+producto_cantidad+','+producto_precioalpublico;
    });
    return data;
}*/
 
function click_addproducto(idproducto,idtienda,tienda_link,tienda_nombre,producto_codigo,producto_nombre,producto_preciopormayor,producto_precioalpublico,producto_cantidad){
  
    $('#contenido-producto-carritocompra-load').html('<div class="mx-alert-load"><img src="{{ url('/public/libraries/app/img/loading.gif') }}"></div>');
    $('#contenido-producto-detalle').css('display','none');
    $.ajax({
        url:"{{ url('pagina/carritocompra/showinsertarcarritocompra') }}",
        type:'GET',
        data: {
            idproducto : idproducto,
            idtienda : idtienda,
            tienda_link : tienda_link,
            tienda_nombre : tienda_nombre,
            producto_codigo : producto_codigo,
            producto_nombre : producto_nombre,
            producto_preciopormayor : producto_preciopormayor,
            producto_precioalpublico : producto_precioalpublico,
            producto_cantidad : producto_cantidad
        },
        success: function (respuesta){
            $('#contenido-producto-carritocompra').css('display','block');
            $('#contenido-producto-carritocompra-load').html('');
            $('#cantidad-pedido-carritocompra').html(respuesta);
        }
    })
  
    /*if(Cookies.get('cookaddproducto')!=undefined){
        var cookaddproducto = JSON.parse(Cookies.get('cookaddproducto'));
    }else{
        var cookaddproducto = [];
    }
    cookaddproducto.push({
        idproducto: idproducto,
        idtienda: idtienda,
        tienda_link: tienda_link,
        tienda_nombre: tienda_nombre,
        producto_codigo: producto_codigo,
        producto_nombre: producto_nombre,
        producto_preciopormayor: producto_preciopormayor,
        producto_precioalpublico: producto_precioalpublico,
        producto_cantidad: producto_cantidad
    });
    Cookies.set('cookaddproducto', cookaddproducto, {expires: 30});
    var cant_cookaddproducto = JSON.parse(Cookies.get('cookaddproducto'));
    $('#cantidad-pedido-carritocompra').html(cant_cookaddproducto.length);
    $('#contenido-producto-detalle').css('display','none');
    $('#contenido-producto-carritocompra').css('display','block');*/
}

</script>



<script>
function initMapUbicacion() {
    var infoWindow = new google.maps.InfoWindow;
    var directionsRenderer = new google.maps.DirectionsRenderer({suppressMarkers : true});
    var directionsService = new google.maps.DirectionsService;
    var imagemarker = '{{ url('public/backoffice/sistema/marker.png') }}';
    var coordenada_tienda = {
        lat: {{ $tienda->mapa_ubicacion_lat }},
        lng: {{ $tienda->mapa_ubicacion_lng }},
    };
    var map = new google.maps.Map(document.getElementById('singleMap'), {
        zoom: 16,
        center: coordenada_tienda
    });
    var marker = new google.maps.Marker({
        position: coordenada_tienda,
        map: map,
        icon: imagemarker
    }); 
    $("#modoruta").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    }).on("change", function(e) {
        maps_ubicar_posicion(infoWindow, map, directionsService, directionsRenderer, coordenada_tienda)
    }).val('DRIVING').trigger("change");
}
  
function maps_ubicar_posicion(infoWindow, map, directionsService, directionsRenderer, coordenada_tienda) {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
          var coordenada_posicion = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
          };
          maps_marcar_ruta(map, directionsService, directionsRenderer,coordenada_posicion, coordenada_tienda);
      }, function() {
        handleLocationError(true, infoWindow, map.getCenter(),map);
      });
    } else {
      handleLocationError(false, infoWindow, map.getCenter(),map);
    }
}
function handleLocationError(browserHasGeolocation, infoWindow, coordenada_posicion,map) {
    /*infoWindow.setPosition(coordenada_posicion);
    infoWindow.setContent(browserHasGeolocation ?
        'Error: The Geolocation service failed.' :
        'Error: Your browser doesn\'t support geolocation.');
    infoWindow.open(map);*/
}
function maps_marcar_ruta(map,directionsService, directionsRenderer, star, end) {  
    var selectedMode = document.getElementById('modoruta').value;
    directionsRenderer.setMap(map);
    directionsService.route({
        origin: star,
        destination: end,  
        travelMode: google.maps.TravelMode[selectedMode]
    }, function(response, status) {
      if (status == 'OK') {
        directionsRenderer.setDirections(response);
        maps_tiempo_distancia(response);
      } else {
        window.alert('Directions request failed due to ' + status);
      }
    });
}
function maps_tiempo_distancia(response) {
    var duracion = 0;
    var distancia = 0;
    response.routes[0].legs.forEach(function (leg) {
      duracion += leg.duration.value;
      distancia += leg.distance.value;
    });
    var hours = Math.floor( duracion / 3600 );  
    var minutes = Math.floor( (duracion % 3600) / 60 );
    var seconds = duracion % 60;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;
    var result = hours + ":" + minutes + ":" + seconds;
    $('#ubi_distancia').html((distancia/1000).toFixed(2) + " KM");
    $('#ubi_tiempo').html(result);
}
</script>

@section('tiendascripts')
@show
@endsection
