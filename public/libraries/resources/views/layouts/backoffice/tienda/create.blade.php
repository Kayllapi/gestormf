@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>REGISTRAR TIENDA</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda',
        method: 'POST'
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda') }}';                                                                            
    },this)">
    <input type="hidden" value="create" id="view"/>
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
            <div class="col-md-6">
              <label>Nombre de Tienda *</label>
              <input type="text" id="nombre" onkeyup="crear_link()"/>
              <label>Link (Tienda) *</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="basic-addon3">{{ url('/') }}/</span>
                </div>
                <input type="text" id="link">
              </div>
              <label>Categoria *</label>
              <select id="idcategoria" >
                  <option></option>
                  @foreach($categorias as $value)
                  <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                  @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label>Número de Teléfono *</label>
              <div class="row">
                  <div class="col-md-4">
                  <select id="idcodigotelefonico">
                      @foreach($codigotelefonicos as $value)
                      <option value="{{ $value->id }}">{{ $value->nombrepais }} ({{ $value->codigopais }})</option>
                      @endforeach
                  </select>
                  </div>
                  <div class="col-md-8">
                      <input type="text" id="numerotelefono"/>
                  </div>
              </div>
              <label>Ubicación (Ubigeo) *</label>
              <select id="idubigeo" >
                  <option ></option>
                  @foreach($ubigeos as $value)
                  <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                  @endforeach
              </select>
              <label>Dirección *</label>
              <input type="text" id="direccion"/>
            </div>
          </div>
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;">Guardar Cambios</button>
        </div>
    </div> 
</form>                             
@endsection
@section('scriptssistema')
<style>
#singleMap {
    height: 463px;
}
.mx-btn-create {
    padding-left: 20px !important;
    width: 100%;
    text-align: center;
}
</style>

<script>
$("#idcategoria").select2({
    placeholder: "---  Seleccionar ---"
});
$("#idubigeo").select2({
    placeholder: "---  Seleccionar ---",
    allowClear: true
});
$("#idcodigotelefonico").select2({
    placeholder: "---  Seleccionar ---"
}).val(139).trigger("change");
uploadfile({
  input:"#imagenportada",
  cont:"#cont-fileupload-portada",
  result:"#resultado-portada"
});
uploadfile({
  input:"#imagen",
  cont:"#cont-fileupload-logo",
  result:"#resultado-logo"
});
function crear_link(){
    var nombre = $('#nombre').val();
    nombre = nombre.replace(/\s+/g, '');
    nombre = nombre.replace(/[^a-zA-Z 0-9.]+/g,'');
    $('#link').val(nombre.toLowerCase());
}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAyFtw-yEfh6GtiPyx_4ZQWt3g_vUCu5eQ&callback=initMap"></script>
<script>
    function singleMap() {
        var myLatLng = {
            lat: -12.071871667822409,
            lng: -75.21026847919165,
        };
        var single_map = new google.maps.Map(document.getElementById('singleMap'), {
            zoom: 14,
            center: myLatLng,
            scrollwheel: false,
            zoomControl: false,
            mapTypeControl: false,
            scaleControl: false,
            panControl: false,
            navigationControl: false,
            streetViewControl: false,
            styles: [{
                "featureType": "landscape",
                "elementType": "all",
                "stylers": [{
                    "color": "#f2f2f2"
                }]
            }]
        });
        var markerIcon2 = {
            url: '{{ url('public/backoffice/sistema/marker.png') }}',
        }
        var marker = new google.maps.Marker({
            position: myLatLng,
			draggable: true,
            map: single_map,
            icon: markerIcon2,
            title: 'Your location'
        });
        var zoomControlDiv = document.createElement('div');
        var zoomControl = new ZoomControl(zoomControlDiv, single_map);

        function ZoomControl(controlDiv, single_map) {
            zoomControlDiv.index = 1;
            single_map.controls[google.maps.ControlPosition.RIGHT_CENTER].push(zoomControlDiv);
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
                single_map.setZoom(single_map.getZoom() + 1);
            });
            google.maps.event.addDomListener(zoomOutButton, 'click', function () {
                single_map.setZoom(single_map.getZoom() - 1);
            });
        }
              google.maps.event.addListener(marker, 'dragend', function (event) {
    
                        $('#mapa_ubicacion_lat').val(event.latLng.lat());
                        $('#mapa_ubicacion_lng').val(event.latLng.lng());
              });		
    }
    var single_map = document.getElementById('singleMap');
    if (typeof (single_map) != 'undefined' && single_map != null) {
        google.maps.event.addDomListener(window, 'load', singleMap);
    } 
        </script>


@endsection