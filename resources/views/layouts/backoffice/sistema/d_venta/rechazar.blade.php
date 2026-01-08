@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Rechazar la Venta</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<?php 
$ventacomida_estado = 2;
if($configuracion!=''){
    if($configuracion->ventacomida_estado==1){
        $ventacomida_estado = 1;
    }  
}
?>
@if(Auth::user()->idtienda==0)
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¡Con el usuario Master no puede realizar una venta, ingrese con un usuario de esta tienda!
    </div>
@else
<div id="carga-venta">
    <div class="profile-edit-container">
        <div class="custom-form">
          @if($ventacomida_estado==1)
          <a href="javascript:;" id="resultado-numeromesa">Número de Mesa: {{ str_pad($venta->ventacomida_numeromesa, 2, "0", STR_PAD_LEFT) }}</a>
          @endif
          <div class="tabs-container" id="tab-carritocompra">
              <ul class="tabs-menu">
                  <li class="current"><a href="#tab-carritocompra-0" id="tab-pedido">Pedido</a></li>
                  <li><a href="#tab-carritocompra-1" id="tab-entrega">Entrega</a></li>
                  <li><a href="#tab-carritocompra-2" id="tab-facturacion">Facturación</a></li>
                  <li><a href="#tab-carritocompra-3" id="tab-pago">Pago</a></li>
              </ul>
              <div class="tab">
                  <div id="tab-carritocompra-0" class="tab-content" style="display: block;">
                      <div class="table-responsive">
                        <table class="table" id="tabla-contenido">
                            <thead class="thead-dark">
                              <tr>
                                <th width="15%">Código</th>
                                <th>Producto</th>
                                @if($configuracion!='')
                                @if($configuracion->venta_estadostock==1)
                                <th width="50px">Stock</th>
                                @endif
                                @endif
                                <th width="60px">Cantidad</th>
                                <th width="110px">P. Unitario</th>
                                <th width="110px">P. Total</th> 
                              </tr>
                            </thead>
                            <tbody num="0"></tbody>

                        </table>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                        </div> 
                        <div class="col-md-4">
                          <input type="text" id="subtotal" placeholder="0.00" style="font-size: 30px;
                                  font-weight: bold;
                                  padding-top: 5px;
                                  padding-bottom: 5px;
                                  text-align: center;" disabled>
                        </div>    
                      </div> 
                      <div class="custom-form">
                      <a href="javascript:;" onclick="$('#tab-entrega').click()" class="log-submit-btn" style="margin-right: 10px;"><span>Siguiente</span> <i class="fa fa-angle-right"></i></a>
                      <a href="javascript:;" onclick="realizarpago()" class="log-submit-btn mx-realizar-pago"><span>Rechazar la Venta</span> <i class="fa fa-angle-right"></i></a>
                      </div>
                  </div>
                  <div id="tab-carritocompra-1" class="tab-content" style="display: none;">
                      <div class="mensaje-info">
                        <i class="fa fa-exclamation-circle"></i> Estos datos son unicamente para la entrega de pedido.
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                            <label>Tipo de entrega *</label>
                            <select id="idtipoentrega" disabled>
                                <option></option>
                                @foreach($tipoentregas as $value)
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                          <div id="cont-tipo-delivery-info" style="display:none;">
                          <label>Entrega de pedido *</label>
                          <select id="idestadoenvio" disabled>
                              <option></option>
                              <option value="1">Enviar ahora</option>
                              <option value="2">Enviar despues</option>
                          </select>
                          <div id="cont-estadoenvio" style="display:none;" disabled>
                          <label>Fecha y Hora de entrega *</label>
                          <div class="row">
                              <div class="col-md-6">
                                  <input type="date" value="{{ $ventadelivery!=''?$ventadelivery->fecha:Carbon\Carbon::now()->format('Y-m-d') }}" id="delivery_fecha" disabled>
                              </div>
                              <div class="col-md-6">
                                  <input type="time" value="{{ $ventadelivery!=''?$ventadelivery->hora:Carbon\Carbon::now()->format('h:s:i') }}" id="delivery_hora" step="1" disabled>
                              </div>
                          </div>
                          </div>
                          <label>Nombre de persona a entregar *</label>
                          <input type="text" value="{{ $ventadelivery!=''?$ventadelivery->nombre:'' }}" id="delivery_pernonanombre" disabled>
                          <label>Número de celular de entrega *</label>
                          <input type="text" value="{{ $ventadelivery!=''?$ventadelivery->telefono:'' }}" id="delivery_numerocelular" disabled>
                          <label>Dirección de entrega *</label>
                          <input type="text" value="{{ $ventadelivery!=''?$ventadelivery->direccion:'' }}" id="delivery_direccion" disabled>
                          </div>
                        </div>
                        <div id="cont-tipo-delivery-mapa" style="display:none;">
                        <div class="col-md-6">
                          <label>Ubicación de entrega (Referencia) *</label>
                          <div id="singleMap"></div>
                          <input type="hidden" value="{{ $ventadelivery!=''?$ventadelivery->mapa_ubicacion_lat:'' }}" id="mapa_ubicacion_lat" disabled>
                          <input type="hidden" value="{{ $ventadelivery!=''?$ventadelivery->mapa_ubicacion_lng:'' }}" id="mapa_ubicacion_lng" disabled>
                        </div>
                        </div>
                      </div>
                      <a href="javascript:;" onclick="$('#tab-pedido').click()" class="log-submit-btn" style="margin-right: 10px;"><i class="fa fa-angle-left"></i> <span>Atras</span></a> 
                      <a href="javascript:;" onclick="$('#tab-facturacion').click()" class="log-submit-btn" style="margin-right: 10px;"><span>Siguiente</span> <i class="fa fa-angle-right"></i></a>
                      <a href="javascript:;" onclick="realizarpago()" class="log-submit-btn mx-realizar-pago"><span>Rechazar la Venta</span> <i class="fa fa-angle-right"></i></a>
                  </div>
                  <div id="tab-carritocompra-2" class="tab-content" style="display: none;">
                      <div class="mensaje-info">
                        <i class="fa fa-exclamation-circle"></i> Rellene correctamente su información, para poder emitir su comprobante.
                      </div>
                      <div class="row">
                         <div class="col-md-6">
                            <label>Facturación - Cliente *</label>
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
                           <label>Empresa *</label>
                            <select id="idagencia" disabled>
                                <option></option>
                                @foreach($agencia as $value)
                                <option value="{{ $value->id }}"?>{{ $value->ruc }} - {{ $value->nombrecomercial }}</option>
                                @endforeach
                            </select>
                            <label>Comprobante *</label>
                            <select id="idcomprobante" disabled>
                                <option></option>
                                @foreach($comprobante as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                         </div>
                       </div>
                       <a href="javascript:;" onclick="$('#tab-entrega').click()" class="log-submit-btn" style="margin-right: 10px;"><i class="fa fa-angle-left"></i> <span>Atras</span></a> 
                       <a href="javascript:;" onclick="realizarpago()" class="log-submit-btn" style="margin-right: 10px;"><span>Siguiente</span> <i class="fa fa-angle-right"></i></a>
                       <a href="javascript:;" onclick="realizarpago()" class="log-submit-btn mx-realizar-pago"><span>Rechazar la Venta</span> <i class="fa fa-angle-right"></i></a>
                  </div>
                  <div id="tab-carritocompra-3" class="tab-content" style="display: none;">
                      <div class="row">
                        <div class="col-md-12">
                            <pre style="background-color: #e0dede;
                                float: left;
                                width: 100%;
                                text-align: left;
                                border-radius: 5px;
                                padding: 10px;
                                margin-bottom: 5px;
                                padding-top: 5px;
                                padding-bottom: 5px;    overflow-x: auto;" id="cont-detallepedido">
                            </pre>
                        </div> 
                        <div class="col-md-4">
                        </div>
                        <div class="col-md-4">
                          <div id="cont-costoenvio" style="display:none;">
                              <input type="hidden" id="subtotal" placeholder="0.00" disabled>
                              <label>Costo de Envio *</label>
                              <input type="number" value="{{$venta->envio}}" style="text-align: center;font-size: 16px;" id="costoenvio" step="0.01" disabled>
                              <label>Total</label>
                              <input type="text" id="total" placeholder="0.00" style="font-size: 30px;
                                  font-weight: bold;
                                  padding-top: 5px;
                                  padding-bottom: 5px;
                                  text-align: center;" disabled>
                          </div>   
                          <div id="cont-montorecibido" style="display:none;">
                              <label>Monto Recibido *</label>
                              <input type="number" value="{{$venta->montorecibido}}" id="montorecibido" step="0.01">
                              <label>Vuelto</label>
                              <input type="text" value="{{$venta->vuelto}}" id="vuelto" value="0.00" disabled> 
                          </div>
                        </div>    
                      </div> 
                      <div class="mensaje-warning">
                        <i class="fa fa-warning"></i> Esta seguro de Rechazar la Venta</b>!.
                      </div>
                      <a href="javascript:;" onclick="rechazar_venta()" id="cont-btnventa" class="btn  big-btn  color-bg flat-btn mx-realizar-pago" style="float: left;width: 100%;">
                          Rechazar la Venta
                      </a>
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
  .mx-realizar-pago {
    background-color: #343a40 !important;
  }
  .mx-realizar-pago:hover {
    background-color: #202327 !important;
  }
  .mx-print-pago {
    background-color: #0679c5 !important;
  }
  .mx-print-pago:hover {
    background-color: #0d5a8e !important;
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
@endif
@endsection
@section('htmls')
<!--  modal ventarealizada --> 
<div class="main-register-wrap modal-ventarealizada" id="modal-ventarealizada">
    <div class="main-overlay"></div>
    <div class="main-register-holder" style="margin: 10px auto 50px;">
        <div class="main-register fl-wrap">
            <div id="contenido-producto"></div>
        </div>
    </div>
</div>
<!--  fin modal ventarealizada --> 
@endsection
@section('subscripts')
<style>
  .car_cont{
    overflow: hidden;
    padding-bottom: 1px;
    padding-top: 1px;
    background-color: #c1c0c0;
    margin-bottom: 1px;
    border-radius: 5px;
  }
  .car_cantidad{
    color: #f9f9f9;
    background-color: #0964a0;
    padding: 5px;
    border-radius: 5px;
    float: left;
    height: 28px;
    text-align: center;
    margin-right: 5px;
  }
  .car_producto{
    color: #ffffff;
    float: left;
    margin-right: 5px;
    background-color: #31353d;
    padding: 5px;
    border-radius: 5px;
  }
  .car_subtotal{
    float: left;
    background-color: #00a044;
    padding: 5px;
    border-radius: 5px;
    color: white;
    margin-right: 5px;
  }
  .car_total{
    float: left;
    background-color: #908907;
    padding: 5px;
    border-radius: 5px;
    color: white;
  }
</style>
<script> 
tab({click:'#tab-carritocompra'});
modal({click:'#modal-ventarealizada'});

function realizarpago(){
    $('#tab-pago').click();
    $('#montorecibido').select();
}
  
@foreach($ventadetalles as $value)
                agregarproducto(
                     '{{$value->idproducto}}',
                     '{{$value->productocodigo}}',
                     '{{$value->productonombre}}',
                     0,
                     '{{$value->productoprecioalpublico}}',
                     '{{$value->idtienda}}',
                     '{{$value->tiendalink}}',
                     '{{$value->tiendanombre}}',
                     '{{$value->cantidad}}'
                );
@endforeach  
  
function carga_carritocompradetalle(){
        var subtotal = 0;
        var total = 0;
        var item = 1;
        var detallepedido = '<b style="font-size: 15px;">DETALLE DE PEDIDO</b><br>';
        detallepedido = detallepedido+'<hr style="border: 1px dashed #31353d;margin-top: 5px;margin-bottom: 5px;">';
        $("#tabla-contenido tbody tr").each(function() {
      
              
                var num = $(this).attr('id');        
                var producto_codigo = $(this).attr('producto_codigo');  
                var producto_nombre = $(this).attr('producto_nombre');  
                var productCant = parseFloat($("#productCant"+num).val()).toFixed(3);
                var productUnidad = parseFloat($("#productUnidad"+num).val()).toFixed(2);
          
                var subtotal = productUnidad*productCant;
                subtotal = subtotal.toFixed(2);
                total = total+parseFloat(subtotal);
              
                var codigo = '';
                if(producto_codigo!=''){
                    codigo = producto_codigo+' - ';
                }
                
                detallepedido = detallepedido+'<div class="car_cont"><b>'+
                    '<div class="car_cantidad">'+
                    productCant+
                    '</div>'+
                    '<div class="car_producto">'+codigo+producto_nombre+'</div> <div class="car_subtotal">'+productUnidad+'</div> <div class="car_total">'+subtotal+'</div></b></div>';
                item++;
         
        });
        detallepedido = detallepedido+'<hr style="border: 1px dashed #31353d;margin-top: 5px;margin-bottom: 5px;">';
        detallepedido = detallepedido+'<b style="font-size: 22px;">Total: '+total.toFixed(2)+'</b><br>';
        $('#cont-detallepedido').html(detallepedido);
  
}
// fin carrito de compra

// registrar venta
function rechazar_venta(){
    callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/venta/{{ $venta->id }}',
        method: 'PUT',
        carga: '#carga-venta',
        data:{
            view: 'rechazar'
        }
    },
    function(resultado){
          location.href = '{{ url()->previous() }}';                                                     
    })
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

function agregarproducto(idproducto,codigo,nombre,stock,precioalpublico,idtienda,tienda_link,tienda_nombre,cantidad=1){
      $("#codigoproducto").val('');
      $("#idproducto").html('');
  
      var num = $("#tabla-contenido tbody").attr('num');
      var nuevaFila='<tr id="'+num+'" producto_codigo="'+codigo+'" producto_nombre="'+nombre+'" style="background-color: #eeeeee;">';
          nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+='<td>'+nombre+'</td>';
          nuevaFila+='<td class="mx-td-input"><input id="productCant'+num+'" type="number" value="'+cantidad+'" disabled></td>';
          nuevaFila+='<td class="mx-td-input"><input id="productUnidad'+num+'" type="number" value="'+precioalpublico+'" step="0.01" min="0" disabled></td>';
          nuevaFila+='<td class="mx-td-input"><input id="productTotal'+num+'" type="text" value="0.00" disabled></td>';       
          nuevaFila+='</tr>';
      $("#tabla-contenido").append(nuevaFila);
      $("#tabla-contenido tbody").attr('num',parseInt(num)+1);
      calcularmonto();
}

function calcularmonto(){
    var total = 0;
    var descuento = 0;
    var totalfinal = 0;
    $("#tabla-contenido tbody tr").each(function() {
        var num = $(this).attr('id');        
        var productCant = parseFloat($("#productCant"+num).val());
        var productUnidad = parseFloat($("#productUnidad"+num).val());
        var subtotal = ((productCant*productUnidad)).toFixed(2);
        $("#productTotal"+num).val(parseFloat(subtotal).toFixed(2));
        total = total+parseFloat((productCant*productUnidad).toFixed(2));
    });
    var costoenvio = parseFloat($("#costoenvio").val());
    if($("#costoenvio").val()==''){
        costoenvio = 0;
    }
    $("#subtotal").val((parseFloat(total)).toFixed(2));
    totalfinal = (parseFloat(total)).toFixed(2);
    $("#total").val((parseFloat(totalfinal)+costoenvio).toFixed(2));
    carga_carritocompradetalle();  
}
</script>

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
@endsection