@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Tienda</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/{{ $tienda->id }}',
        method: 'PUT'
    },
    function(resultado){
        location.href = '{{ url('backoffice/tienda') }}';                                                                            
    },this)" enctype="multipart/form-data">
        <div class="custom-form">
          <input type="hidden" value="editinformacion" id="view"/>
  
          <div class="tabs-container" id="tab-tiendainformacion">
              <ul class="tabs-menu">
                  <li class="current"><a href="#tab-tiendainformacion-0" id="tab-general">General</a></li>
                  <li><a href="#tab-tiendainformacion-1" id="tab-">Portada</a></li>
                  <li><a href="#tab-tiendainformacion-2" id="tab-contacto21">Contacto</a></li>
              </ul>
              <div class="tab">
                  <div id="tab-tiendainformacion-0" class="tab-content" style="display: block;">
                      <div class="row">
                        <div class="col-md-6">
                          <label>Nombre *</label>
                          <input type="text" value="{{ $tienda->nombre }}" id="nombre"/>
                          <label>Link (Tienda) *</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text" id="basic-addon3">{{ url('/') }}/</span>
                            </div>
                            @if(Auth::user()->id==1)
                            <input type="text" value="{{ $tienda->link }}" id="link">
                            @else
                            <input type="text" value="{{ $tienda->link }}" id="link" disabled>
                            @endif
                          </div>
                          <label>Descripción Resumida (Motores de Busqueda)</label>
                          <textarea id="contenido" style="height: 120px;margin-bottom: 10px;">{{ $tienda->contenido }}</textarea>
                        </div>
                        <div class="col-md-6">
                          <label>Categoria *</label>
                          <select id="idcategoria" >
                              <option></option>
                              @foreach($categorias as $value)
                              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                              @endforeach
                          </select>
                          <label>Logo (300x300)</label>
                          <div class="fuzone" id="cont-fileupload-logo" style="height: 188px;">
                              <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar</span></div>
                              <input type="file" class="upload" id="imagen">
                              <div id="resultado-logo"></div>
                          </div>
                        </div>
                      </div>
                  </div>
                  <div id="tab-tiendainformacion-1" class="tab-content" style="display: none;">
                      <div class="row">
                        <div class="col-md-12">
                          <label>Imagen de Portada (1900x400)</label>
                            <div class="fuzone" id="cont-fileupload-portada" style="height: 238px;">
                                <div class="fu-text"><span><i class="fa fa-picture-o"></i> Haga clic aquí o suelte para cargar {{ $tienda->imagenportada }}</span></div>
                                <input type="file" class="upload" id="imagenportada">
                                <div id="resultado-portada"></div>
                              <style>#resultado-portada > img{max-width: 100% !important;}</style>
                            </div>
                        </div>
                      </div>
                  </div>
                  <div id="tab-tiendainformacion-2" class="tab-content" style="display: none;">
                      <div class="row">
                        <div class="col-md-6">
                          <label>Correo Electrónico</label>
                          <input type="text" value="{{ $tienda->correo }}" id="correo"/>
                          <label>Número de Teléfono *</label>
                          <div class="row">
                              <div class="col-md-4">
                              <select id="idcodigotelefonico" >
                                  <option></option>
                                  @foreach($codigotelefonicos as $value)
                                  <option value="{{ $value->id }}">{{ $value->nombrepais }} ({{ $value->codigopais }})</option>
                                  @endforeach
                              </select>
                              </div>
                              <div class="col-md-8">
                                  <input type="text" value="{{ $tienda->numerotelefono }}" id="numerotelefono"/>
                              </div>
                          </div>
                          <label>Pagina Web</label>
                          <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text" id="basic-addon3">http://</span>
                            </div>
                            <input type="text" value="{{ $tienda->paginaweb }}" id="paginaweb">
                          </div>
                          <label>Ubicación (Ubigeo)</label>
                          <select id="idubigeo" >
                              <option></option>
                              @foreach($ubigeos as $value)
                              <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                              @endforeach
                          </select>
                          <label>Dirección *</label>
                          <input type="text" value="{{ $tienda->direccion }}" id="direccion"/>
                          <label>Referencia</label>
                          <input type="text" value="{{ $tienda->referencia }}" id="referencia"/>
                        </div>
                        <div class="col-md-6">
                          <label>Ubicación (Mapa)</label>
                          <div id="singleMap" style="height: 386px;"></div>
                          <input type="hidden" value="{{ $tienda->mapa_ubicacion_lat!=''?$tienda->mapa_ubicacion_lat:'-12.071871667822409' }}" id="mapa_ubicacion_lat"/>
                          <input type="hidden" value="{{ $tienda->mapa_ubicacion_lng!=''?$tienda->mapa_ubicacion_lng:'-75.21026847919165' }}" id="mapa_ubicacion_lng"/>
                        </div>
                      </div>
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
<script>
tab({click:'#tab-tiendainformacion'});
  
$("#idcategoria").select2({
    placeholder: "---  Seleccionar ---"
}).val({{$tienda->idcategoria}}).trigger("change");
  
$("#idcodigotelefonico").select2({
    placeholder: "---  Seleccionar ---"
}).val({{$tienda->idcodigotelefonico}}).trigger("change");
  
$("#idubigeo").select2({
    placeholder: "---  Seleccionar ---",
    allowClear: true
}).val({{$tienda->idubigeo}}).trigger("change");

uploadfile({
  input:"#imagenportada",
  cont:"#cont-fileupload-portada",
  result:"#resultado-portada",
  ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/portada/') }}",
  image: "{{ $tienda->imagenportada }}"
});
uploadfile({
  input:"#imagen",
  cont:"#cont-fileupload-logo",
  result:"#resultado-logo",
  ruta: "{{ url('public/backoffice/tienda/'.$tienda->id.'/logo/') }}",
  image: "{{ $tienda->imagen }}"
});
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAyFtw-yEfh6GtiPyx_4ZQWt3g_vUCu5eQ&callback=initMap"></script>
<script>
    function singleMap() {
        var myLatLng = {
            lat: {{ $tienda->mapa_ubicacion_lat!='' ? $tienda->mapa_ubicacion_lat : '-12.071871667822409' }},
            lng: {{ $tienda->mapa_ubicacion_lng!='' ? $tienda->mapa_ubicacion_lng : '-75.21026847919165' }},
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