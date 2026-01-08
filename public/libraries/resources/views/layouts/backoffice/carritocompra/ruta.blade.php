@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Ruta de envio</span>
      <a class="btn btn-success" href="{{ url('backoffice/carritocompra') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
  
    <div class="profile-edit-container">
    <div class="custom-form">
      
      <div class="row">
        <div class="col-md-6">
          <label>Entrega de pedido *</label>
          <select id="idestadoenvio" disabled>
              <option></option>
              <option value="1">Enviar ahora</option>
              <option value="2">Enviar despues</option>
          </select>
          <div id="cont-estadoenvio" style="display:none;">
          <label>Fecha y Hora de entrega *</label>
          <div class="row">
              <div class="col-md-6">
                  <input type="date" value="{{ $s_ventadelivery!=''?$s_ventadelivery->fecha:'' }}" id="delivery_fecha" disabled>
              </div>
              <div class="col-md-6">
                  <input type="time" value="{{ $s_ventadelivery!=''?$s_ventadelivery->hora:'' }}" id="delivery_hora" disabled>
              </div>
          </div>
          </div>
          <label>Nombre de persona a entregar *</label>
          <input type="text" value="{{ $s_ventadelivery!=''?$s_ventadelivery->nombre:'' }}" id="delivery_pernonanombre" disabled>
        </div>
        <div class="col-md-6">
          <label>Número de celular de entrega *</label>
          <input type="text" value="{{ $s_ventadelivery!=''?$s_ventadelivery->telefono:'' }}" id="delivery_numerocelular" disabled>
          <label>Dirección de entrega *</label>
          <input type="text" value="{{ $s_ventadelivery!=''?$s_ventadelivery->direccion:'' }}" id="delivery_direccion" disabled>
        </div>
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-6">
              <label>Modo de Ruta</label>
              <select id="modoruta">
                <option value="DRIVING">Conduciendo</option>
                <option value="WALKING">Caminando</option>
              </select>
            </div>
            <div class="col-md-6">
              <label>Distancia - Tiempo</label>
              <div style="background-color: #008cea;
                    color: #fff;
                    padding: 10px;
                    padding: 12.5px;
                    border-radius: 5px;
                    font-size: 16px;
                    font-weight: bold;
                    float: left;
                    width: 100%;"><span id="ubi_distancia">O KM </span> - <strong id="ubi_tiempo">---</strong>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-8">
          <label>Ubicación de entrega (Referencia) *</label>
          <div id="singleMap"></div>
          <input type="hidden" value="{{ $s_ventadelivery!=''?$s_ventadelivery->mapa_ubicacion_lat:'' }}" id="mapa_ubicacion_lat"/>
          <input type="hidden" value="{{ $s_ventadelivery!=''?$s_ventadelivery->mapa_ubicacion_lng:'' }}" id="mapa_ubicacion_lng"/>
        </div>
        <div class="col-md-4">
          <label>Dirección</label>
          <div id="panelderecho" style="text-align: left;"> </div>
        </div>
      </div>
    </div>
    </div>

<style>
#singleMap {
    height: 350px;
}
</style>
@endsection
@section('scriptsbackoffice')
<script>
$("#idestadoenvio").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-estadoenvio').css('display','none');
    if(e.currentTarget.value == 1) {
    }else if(e.currentTarget.value == 2) {
        $('#cont-estadoenvio').css('display','block');
    }
}).val({{ $s_ventadelivery!=''?$s_ventadelivery->s_idestadoenvio:'' }}).trigger("change");
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAyFtw-yEfh6GtiPyx_4ZQWt3g_vUCu5eQ"></script>
<script>
initMapUbicacion();
function initMapUbicacion() {
    var infoWindow = new google.maps.InfoWindow;
    var directionsRenderer = new google.maps.DirectionsRenderer({suppressMarkers : true});
    var directionsService = new google.maps.DirectionsService;
    var imagemarker = '{{ url('public/backoffice/sistema/marker.png') }}';
    var coordenada_tienda = {
        lat: {{ $s_ventadelivery->mapa_ubicacion_lat }},
        lng: {{ $s_ventadelivery->mapa_ubicacion_lng }},
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
        handleLocationError(true, infoWindow, map.getCenter());
      });
    } else {
      handleLocationError(false, infoWindow, map.getCenter());
    }
}
function handleLocationError(browserHasGeolocation, infoWindow, coordenada_posicion) {
    infoWindow.setPosition(coordenada_posicion);
    infoWindow.setContent(browserHasGeolocation ?
        'Error: The Geolocation service failed.' :
        'Error: Your browser doesn\'t support geolocation.');
    infoWindow.open(map);
}
function maps_marcar_ruta(map,directionsService, directionsRenderer, star, end) {  
    var selectedMode = document.getElementById('modoruta').value;
    directionsRenderer.setMap(map);
  
    directionsRenderer.setPanel(document.getElementById('panelderecho'));
  
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
@endsection