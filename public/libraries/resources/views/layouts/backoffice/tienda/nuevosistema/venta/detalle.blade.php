<div class="tabla-detalle">
<div id="carga-venta">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="tabs-container" id="tab-carritocompra">
              <ul class="tabs-menu">
                  <li class="current"><a href="#tab-carritocompra-0" id="tab-pedido">Pedido</a></li>
                  <li><a href="#tab-carritocompra-1" id="tab-entrega">Entrega</a></li>
                  <li><a href="#tab-carritocompra-2" id="tab-facturacion">Facturación</a></li>
              </ul>
              <div class="tab">
                  <div id="tab-carritocompra-0" class="tab-content" style="display: block;">
                      <div class="table-responsive">
                        <table class="table" id="tabla-contenido">
                            <thead class="thead-dark">
                              <tr>
                                <th width="15%">Código</th>
                                <th>Producto</th>
                                @if($configuracion['estadostock']==1)
                                <th width="50px">Stock</th>
                                @endif
                                <th width="60px">Cantidad</th>
                                <th width="110px">P. Unitario</th>
                                <th width="110px">P. Total</th> 
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($ventadetalles as $value)
                                <tr style="background-color: #008cea;color: #fff;height: 40px;">
                                <td>{{$value->productocodigo}}</td>
                                <td>{{$value->productonombre}}</td>
                                <td>{{$value->cantidad}}</td>
                                <td>{{$value->preciounitario}}</td>
                                <td>{{$value->total}}</td>       
                                </tr>
                              @endforeach 
                            </tbody>
                            @if($configuracion['estadodescuento']==1 && $venta->totaldescuento>0)
                            <tbody>
                              <tr style="background-color: #066aad;color: #fff;height: 40px;">
                                <td colspan="4" style="text-align: right;padding-right: 10px;">Total Venta:</td>
                                <td>{{ $venta->totalventa }}</td>    
                                </tr>
                            </tbody>
                            <tbody>
                              @foreach($ventadescuentos as $value)
                                <?php 
                                $ventadescuentodetalles = DB::table('s_ventadescuentodetalle')
                                    ->join('s_producto','s_producto.id','s_ventadescuentodetalle.s_idproducto')
                                    ->where('s_ventadescuentodetalle.s_idventadescuento',$value->id)
                                    ->select(
                                      's_producto.nombre as productonombre',
                                      DB::raw('COUNT(s_producto.nombre) as cantidadrepetido')
                                    )
                                    ->groupBy('s_producto.nombre')
                                    ->orderBy('s_ventadescuentodetalle.id','asc')
                                    ->get();
                                ?>
                                <tr style="background-color: #73808c;color: #fff;height: 40px;">
                                  <td></td>
                                  <td colspan="3">
                                    @foreach($ventadescuentodetalles as $descvalue)
                                    <div style="float: left;background-color: #535e67;padding: 5px;border-radius: 5px;margin-right: 3px;">({{$descvalue->cantidadrepetido}}) {{$descvalue->productonombre}}</div>
                                    @endforeach 
                                  </td>
                                  <td>{{$value->montodescuento}}</td>   
                                </tr>
                              @endforeach 
                            </tbody>
                            <tbody>
                              <tr style="background-color: #5b666f;color: #fff;height: 40px;">
                                  <td colspan="4" style="text-align: right;padding-right: 10px;">Total Descuento:</td>
                                  <td>{{$venta->totaldescuento}}</td>      
                              </tr>
                            </tbody>
                            @endif
                        </table>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                        </div> 
                        <div class="col-md-4">
                          <div style="font-weight: bold;font-size: 18px;">Total:</div>
                          <input type="text" id="subtotal" value="{{ $venta->subtotal }}" style="font-size: 30px;
                                  font-weight: bold;
                                  padding-top: 5px;
                                  padding-bottom: 5px;
                                  text-align: center;" disabled>
                              @if($venta->s_idtipoentrega==2)
                              <label>Costo de Envio</label>
                              <input type="number" value="{{$venta->envio}}" style="text-align: center;font-size: 16px;" id="costoenvio" step="0.01" disabled>
                              <label>Total</label>
                              <input type="text" id="total" value="{{ $venta->total }}" style="font-size: 30px;
                                  font-weight: bold;
                                  padding-top: 5px;
                                  padding-bottom: 5px;
                                  text-align: center;" disabled>
                              @endif
                              <label>Total Redondeado</label>
                              <input type="text" id="total_redondeado" value="{{ $venta->totalredondeado }}" style="font-size: 30px;
                                  font-weight: bold;
                                  padding-top: 5px;
                                  padding-bottom: 5px;
                                  text-align: center;" disabled>
                              <label>Monto Recibido</label>
                              <input type="number" value="{{$venta->montorecibido}}" id="montorecibido" step="0.01" style="text-align: center;" disabled>
                              <label>Vuelto</label>
                              <input type="text" value="{{$venta->vuelto}}" id="vuelto" value="0.00" style="text-align: center;" disabled> 
                        </div>    
                      </div> 
                      <div class="custom-form">
                      <a href="javascript:;" onclick="$('#tab-entrega').click()" class="log-submit-btn" style="margin-right: 10px;"><span>Siguiente</span> <i class="fa fa-angle-right"></i></a>
                      </div>
                  </div>
                  <div id="tab-carritocompra-1" class="tab-content" style="display: none;">
                      <div class="mensaje-info">
                        <i class="fa fa-exclamation-circle"></i> Estos datos son unicamente para la entrega de pedido.
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                            <label>Tipo de entrega</label>
                            <select id="idtipoentrega" disabled>
                                <option></option>
                                    <option value="{{ $tipoentregas->id }}">{{ $tipoentregas->nombre }}</option>
                            </select>
                          <div id="cont-tipo-delivery-info" style="display:none;">
                          <label>Entrega de pedido</label>
                          <select id="idestadoenvio" disabled>
                              <option></option>
                              <option value="1">Enviar ahora</option>
                              <option value="2">Enviar despues</option>
                          </select>
                          <div id="cont-estadoenvio" style="display:none;" disabled>
                          <label>Fecha y Hora de entrega</label>
                          <div class="row">
                              <div class="col-md-6">
                                  <input type="date" value="{{ $ventadelivery!=''?$ventadelivery->fecha:Carbon\Carbon::now()->format('Y-m-d') }}" id="delivery_fecha" disabled>
                              </div>
                              <div class="col-md-6">
                                  <input type="time" value="{{ $ventadelivery!=''?$ventadelivery->hora:Carbon\Carbon::now()->format('h:s:i') }}" id="delivery_hora" step="1" disabled>
                              </div>
                          </div>
                          </div>
                          <label>Nombre de persona a entregar</label>
                          <input type="text" value="{{ $ventadelivery!=''?$ventadelivery->nombre:'' }}" id="delivery_pernonanombre" disabled>
                          <label>Número de celular de entrega</label>
                          <input type="text" value="{{ $ventadelivery!=''?$ventadelivery->telefono:'' }}" id="delivery_numerocelular" disabled>
                          <label>Dirección de entrega</label>
                          <input type="text" value="{{ $ventadelivery!=''?$ventadelivery->direccion:'' }}" id="delivery_direccion" disabled>
                          </div>
                        </div>
                        <div id="cont-tipo-delivery-mapa" style="display:none;">
                        <div class="col-md-6">
                          <label>Ubicación de entrega (Referencia)</label>
                          <div id="singleMap"></div>
                          <input type="hidden" value="{{ $ventadelivery!=''?$ventadelivery->mapa_ubicacion_lat:'' }}" id="mapa_ubicacion_lat" disabled>
                          <input type="hidden" value="{{ $ventadelivery!=''?$ventadelivery->mapa_ubicacion_lng:'' }}" id="mapa_ubicacion_lng" disabled>
                        </div>
                        </div>
                      </div>
                      <a href="javascript:;" onclick="$('#tab-pedido').click()" class="log-submit-btn" style="margin-right: 10px;"><i class="fa fa-angle-left"></i> <span>Atras</span></a> 
                      <a href="javascript:;" onclick="$('#tab-facturacion').click()" class="log-submit-btn" style="margin-right: 10px;"><span>Siguiente</span> <i class="fa fa-angle-right"></i></a>
                  </div>
                  <div id="tab-carritocompra-2" class="tab-content" style="display: none;">
                      <div class="mensaje-info">
                        <i class="fa fa-exclamation-circle"></i> Rellene correctamente su información, para poder emitir su comprobante.
                      </div>
                      <div class="row">
                         <div class="col-md-6">
                            <label>Facturación - Cliente</label>
                            <div class="row">
                               <div class="col-md-12">
                                  <select id="idcliente" disabled>
                                      <option value="{{ $venta->idcliente }}">{{ $venta->cliente }}</option>
                                  </select>
                               </div>
                            </div>
                            <label>Facturación - Dirección</label>
                            <input type="text" id="direccion" value="{{$venta->clientedireccion}}" disabled>
                            <label>Facturación - Ubicación (Ubigeo)</label>
                            <select id="idubigeo" disabled>
                                <option value="{{ $venta->idubigeo }}">{{ $venta->ubigeonombre }}</option>
                            </select>
                         </div>
                         <div class="col-md-6">
                           <label>Empresa</label>
                            <select id="idagencia" disabled>
                                <option></option>
                                @foreach($agencia as $value)
                                <option value="{{ $value->id }}"?>{{ $value->ruc }} - {{ $value->nombrecomercial }}</option>
                                @endforeach
                            </select>
                            <label>Moneda</label>
                            <select id="idmoneda" disabled>
                                <option></option>
                                @foreach($monedas as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                            <label>Comprobante</label>
                            <select id="idcomprobante" disabled>
                                <option></option>
                                @foreach($comprobante as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                         </div>
                       </div>
                       <a href="javascript:;" onclick="$('#tab-entrega').click()" class="log-submit-btn" style="margin-right: 10px;"><i class="fa fa-angle-left"></i> <span>Atras</span></a> 
                  </div>
              </div>
          </div>
        </div>
    </div>
</div>
</div>
<style>
#singleMap {
    height: 317px;
}
</style>
<style>
  input, table {
    font-weight: bold;
  }
  #resultado-numeromesa {
    background-color: {{$tienda->ecommerce_color}};
    padding: 10px;
    border-radius: 5px;
    color: #fff;
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 5px;
    display: block;
  }
</style>
<script> 
tab({click:'#tab-carritocompra'});

function realizarpago(){
    $('#tab-pago').click();
    $('#montorecibido').select();
}
  
$("#idcliente").select2({
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2
});

$("#idubigeo").select2({
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2
});

$("#idagencia").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{ $venta->s_idagencia }}).trigger("change");

$("#idmoneda").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{ $venta->s_idmoneda }}).trigger("change");

$("#idcomprobante").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{ $venta->s_idcomprobante }}).trigger("change");
 
$("#idtipoentrega").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-tipo-delivery-info').css('display','none');
    $('#cont-tipo-delivery-mapa').css('display','none');
    $('#cont-costoenvio').css('display','none');
    if(e.currentTarget.value == 2) {
        $('#cont-tipo-delivery-info').css('display','block');
        $('#cont-tipo-delivery-mapa').css('display','block');
        $('#cont-costoenvio').css('display','block');
    }
}).val({{ $venta->s_idtipoentrega }}).trigger("change");

$("#idestadoenvio").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-estadoenvio').css('display','none');
    if(e.currentTarget.value == 1) {
    }else if(e.currentTarget.value == 2) {
        $('#cont-estadoenvio').css('display','block');
    }
}).val({{ $ventadelivery!=''?$ventadelivery->s_idestadoenvio:1 }}).trigger("change");

</script>
@if($venta->s_idtipoentrega==2)
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAyFtw-yEfh6GtiPyx_4ZQWt3g_vUCu5eQ"></script>
<script>
    function singleMap() {
        var myLatLng = {
            lat: {{ $ventadelivery!=''?$ventadelivery->mapa_ubicacion_lat:-12.071871667822409 }},
            lng: {{ $ventadelivery!=''?$ventadelivery->mapa_ubicacion_lng:-75.21026847919165 }},
        };
        var single_map = new google.maps.Map(document.getElementById('singleMap'), {
            zoom: 16,
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
@endif
