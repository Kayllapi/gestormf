@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Carrito de compra</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/carritocompra') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/carritocompra',
        method: 'POST',
        data:{
            view: 'registrar',
            productos: selectproductos()
        }
    },
    function(resultado){
         location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/carritocompra') }}';                                                  
    },this)"> 
    <div class="tabs-container" id="tab-carritocompra">
              <ul class="tabs-menu">
                  <li class="current"><a href="#tab-carritocompra-0" id="tab-pedido">Pedido</a></li>
                  <li><a href="#tab-carritocompra-1" id="tab-facturacion">Facturación</a></li>
                  <li><a href="#tab-carritocompra-2" id="tab-entrega">Entrega</a></li>
              </ul>
              <div class="tab">
                  <div id="tab-carritocompra-0" class="tab-content" style="display: block;">
                      <div class="profile-edit-container">
                        <div class="custom-form">
                          <div class="table-responsive">
                            <table class="table" id="tabla-contenido">
                                <thead class="thead-dark">
                                  <tr>
                                    <th width="15%">Código</th>
                                    <th>Producto</th>
                                    <th width="50px">Stock</th>
                                    <th width="60px">Cantidad</th>
                                    <th width="110px">P. Unitario</th>
                                    <th width="110px">Descuento</th>
                                    <th width="110px">P. Total</th> 
                                    <th width="10px"></th>
                                  </tr>
                                  <tr>
                                      <td class="mx-td-input"><input type="text" id="codigoproducto" onkeyup="buscarcodigo(this)"/></td>
                                      <td colspan="6" class="mx-td-input">
                                        <select id="idproducto">
                                            <option></option>
                                        </select>
                                      <td width="auto"></td>
                                      </td>
                                  </tr>
                                </thead>
                                <tbody num="0"></tbody>
                            </table>
                          </div>
                          <div class="row">
                            <div class="col-md-2">
                              <label>Sub Total</label>
                              <input type="text" id="subtotal" placeholder="0.00" disabled>
                            </div>   
                            <div class="col-md-2">
                              <label>Descuento</label>
                              <input type="text" id="descuento" placeholder="0.00" disabled>
                            </div>   
                            <div class="col-md-2">
                              <label>Total</label>
                              <input type="text" id="total" placeholder="0.00" disabled>
                            </div>          
                          </div> 
                          
                        </div>
                      </div>
                      <div class="profile-edit-container">
                          <div class="custom-form">
                              <a href="javascript:;" onclick="$('#tab-facturacion').click()" class="log-submit-btn"><span>Empezar el pedido</span> <i class="fa fa-angle-right"></i></a>
                          </div>
                      </div>
                  </div>
                  <div id="tab-carritocompra-1" class="tab-content" style="display: none;">
                      <div class="profile-edit-container">
                      <div class="custom-form">
                        <div class="row">
                           <div class="col-md-6">
                              <label>Cliente *</label>
                              <div class="row">
                                 <div class="col-md-9">
                                    <select id="idcliente" >
                                        <option></option>
                                    </select>
                                 </div>
                                 <div class="col-md-3">
                                    <a href="javascript:;" id="modal-registrarcliente" onclick="agregarcliente()" class="btn btn-warning"><i class="fa fa-plus"></i> Agregar</a>
                                 </div>
                              </div>
                              <label>Dirección</label>
                              <input type="text" id="direccion"/>
                           </div>
                           <div class="col-md-6">
                              <label>Ubicación (Ubigeo)</label>
                              <select id="idubigeo">
                                  <option></option>
                              </select>
                           </div>
                         </div>
                      </div>
                    </div> 
                      <div class="profile-edit-container">
                          <div class="custom-form">
                              <a href="javascript:;" onclick="$('#tab-pedido').click()" class="log-submit-btn" style="margin-right: 10px;"><i class="fa fa-angle-left"></i> <span>Atras</span></a> 
                              <a href="javascript:;" onclick="$('#tab-entrega').click()" class="log-submit-btn"><span>Siguiente</span> <i class="fa fa-angle-right"></i></a>
                          </div>
                      </div>
                  </div>
                  <div id="tab-carritocompra-2" class="tab-content" style="display: none;">
                      <div class="profile-edit-container">
                        <div class="custom-form">
                          <div class="row">
                            <div class="col-md-6">
                              <label>Entrega de pedido *</label>
                              <select id="idestadoenvio">
                                  <option></option>
                                  <option value="1">Enviar ahora</option>
                                  <option value="2">Enviar despues</option>
                              </select>
                              <div id="cont-estadoenvio" style="display:none;">
                              <label>Fecha y Hora de entrega *</label>
                              <div class="row">
                                  <div class="col-md-6">
                                      <input type="date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" id="delivery_fecha">
                                  </div>
                                  <div class="col-md-6">
                                      <input type="time" value="{{ Carbon\Carbon::now()->format('h:s:i') }}" id="delivery_hora" step="1">
                                  </div>
                              </div>
                              </div>
                              <label>Nombre de persona a entregar *</label>
                              <input type="text" id="delivery_pernonanombre">
                              <label>Número de celular de entrega *</label>
                              <input type="text" id="delivery_numerocelular">
                              <label>Dirección de entrega *</label>
                              <input type="text" id="delivery_direccion">
                            </div>
                            <div class="col-md-6">
                              <label>Ubicación de entrega (Referencia) *</label>
                              <div id="singleMap"></div>
                              <input type="hidden" id="mapa_ubicacion_lat"/>
                              <input type="hidden" id="mapa_ubicacion_lng"/>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="profile-edit-container">
                          <div class="custom-form">
                              <a href="javascript:;" onclick="$('#tab-facturacion').click()" class="log-submit-btn" style="margin-right: 10px;"><i class="fa fa-angle-left"></i> <span>Atras</span></a> 
                              <button type="submit" class="btn  big-btn  color-bg flat-btn btn-warning">Registrar Pedido</button>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
</div>
</form>
<style>
#singleMap {
    height: 272px;
}
</style>
@endsection
@section('htmls')
<!--  modal registrarcliente --> 
<div class="main-register-wrap modal-registrarcliente">
    <div class="main-overlay"></div>
    <div class="main-register-holder">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Registrar Cliente</h3>
            <div class="mx-modal-cuerpo" id="contenido-registrarcliente">
              <div id="mx-carga-cliente">
              <form class="js-validation-signin px-30" 
                  action="javascript:;" 
                  onsubmit="callback({
                    route: 'backoffice/tienda/sistema/{{ $tienda->id }}/venta',
                    method: 'POST',
                    carga: '#mx-carga-cliente',
                    data:{
                        view: 'registrarcliente'
                    }
                },
                function(resultado){
                    $('#idcliente').html('<option value=\''+resultado['cliente'].id+'\'>'+resultado['cliente'].identificacion+' - '+resultado['cliente'].apellidos+', '+resultado['cliente'].nombre+'</option>');
                    $('#direccion').val(resultado['cliente'].direccion);
                    $('#idubigeo').html('<option></option>');
                    if(resultado['cliente'].idubigeo!=0){
                        $('#idubigeo').html('<option value=\''+resultado['ubigeocliente'].id+'\'>'+resultado['ubigeocliente'].nombre+'</option>');                                                             
                    }
                    $('#contenido-registrarcliente').css('display','none');
                    confirm({
                        input:'#contenido-confirmar-registrarcliente',
                        resultado:'CORRECTO',
                        mensaje:'Se ha registrado correctamente!.',
                        cerrarmodal:'.modal-registrarcliente'
                    });       
                },this)">
                <div class="profile-edit-container">
                    <div class="custom-form">
                            <label>Tipo de Persona *</label>
                            <select id="cliente_idtipopersona">
                                @foreach($tipopersonas as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                            <div id="cont-juridica" style="display:none;">
                                <label>RUC *</label>
                                <input type="text" id="cliente_ruc"/>
                                <label>Nombre Comercial *</label>
                                <input type="text" id="cliente_nombrecomercial"/>
                                <label>Razòn Social *</label>
                                <input type="text" id="cliente_razonsocial"/>
                            </div>
                            <div id="cont-natural" style="display:none;">
                              <label>DNI</label>
                              <input type="text" id="cliente_dni"/>
                              <label>Nombre *</label>
                              <input type="text" id="cliente_nombre"/>
                              <label>Apellidos</label>
                              <input type="text" id="cliente_apellidos"/>
                            </div>
                              <label>Número de Teléfono</label>
                              <input type="text" id="cliente_numerotelefono"/>
                              <label>Correo Electrónico</label>
                              <input type="text" id="cliente_email"/>
                              <label>Ubicación (Ubigeo) *</label>
                              <select id="cliente_idubigeo">
                                  <option></option>
                              </select>
                              <label>Dirección *</label>
                              <input type="text" id="cliente_direccion"/>
                    </div>
                </div>
                <div class="profile-edit-container">
                    <div class="custom-form">
                        <button type="submit" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</button>
                    </div>
                </div> 
            </form> 
            </div>
            </div>
            <div class="mx-modal-cuerpo" id="contenido-confirmar-registrarcliente"></div>
        </div>
    </div>
</div>
<!--  fin modal registrarcliente --> 
@endsection
@section('subscripts')
<script>
tab({click:'#tab-carritocompra'});
// cliente
modal({click:'#modal-registrarcliente'});
function agregarcliente(){
    $('#contenido-registrarcliente').css('display','block');
    $('#contenido-confirmar-registrarcliente').html('');
    removecarga({input:'#mx-carga-cliente'});
    $('#cliente_ruc').val('');
    $('#cliente_nombrecomercial').val('');
    $('#cliente_razonsocial').val('');
    $('#cliente_dni').val('');
    $('#cliente_nombre').val('');
    $('#cliente_apellidos').val('');
    $('#cliente_numerotelefono').val('');
    $('#cliente_email').val('');
    $('#cliente_idubigeo').html('<option></option>');
    $('#cliente_direccion').val('');
}
$("#cliente_idtipopersona").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-juridica').css('display','none');
    $('#cont-natural').css('display','none');
    if(e.currentTarget.value == 1) {
        $('#cont-natural').css('display','block');
    }else if(e.currentTarget.value == 2) {
        $('#cont-juridica').css('display','block');
    }
}).val(1).trigger("change");

$("#cliente_idubigeo").select2({
    ajax: {
        url:"{{url('backoffice/inicio/showlistarubigeo')}}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                  buscar: params.term,
                  view: 'listarubigeo'
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2,
    allowClear: true
});
// fin cliente
$("#idcliente").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/showlistarusuario')}}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                  buscar: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2
}).on("change", function(e) {
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/showseleccionarusuario')}}",
        type:'GET',
        data: {
            idusuario : e.currentTarget.value
        },
        success: function (respuesta){
          $('#direccion').val(respuesta['usuario'].direccion);
          if(respuesta['usuario'].idubigeo!=0){
              $("#idubigeo").html('<option value="'+respuesta['usuario'].idubigeo+'">'+respuesta['usuario'].ubigeonombre+'</option>');
          }else{
              $("#idubigeo").html('<option></option>');
          }
          // delivery
          $('#delivery_pernonanombre').val(respuesta['usuario'].nombre);
          $('#delivery_numerocelular').val(respuesta['usuario'].numerotelefono);
          $('#delivery_direccion').val(respuesta['usuario'].direccion);
        }
    })
});

$("#idubigeo").select2({
    ajax: {
        url:"{{url('backoffice/inicio/showlistarubigeo')}}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                  buscar: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2,
    allowClear: true
});
 
$("#idestadoenvio").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-estadoenvio').css('display','none');
    if(e.currentTarget.value == 1) {
    }else if(e.currentTarget.value == 2) {
        $('#cont-estadoenvio').css('display','block');
    }
}).val(1).trigger("change");

$("#idproducto").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/showlistarproducto')}}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                  buscar: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    placeholder: "--  Seleccionar Producto --",
    allowClear: true,
    minimumInputLength: 2
}).on("change", function(e) {
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/showseleccionarproducto')}}",
        type:'GET',
        data: {
            idproducto : e.currentTarget.value
        },
        success: function (respuesta){
          if(respuesta["producto"]!=null){
            agregarproducto(
              respuesta["producto"].id,
              respuesta["producto"].codigo,
              respuesta["producto"].nombre,
              respuesta["stock"],
              respuesta["producto"].precioalpublico,
              '0.00'
            );
          }
        }
    })
});
  
function buscarcodigo(pthis){
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/showseleccionarproducto')}}",
        type:'GET',
        data: {
            codigoproducto : $(pthis).val()
        },
        success: function (respuesta){
          if(respuesta["producto"]!=null){
            agregarproducto(
              respuesta["producto"].id,
              respuesta["producto"].codigo,
              respuesta["producto"].nombre,
              respuesta["stock"],
              respuesta["producto"].precioalpublico,
              '0.00'
            );
          }
        }
    })
}
function agregarproducto(idproducto,codigo,nombre,stock,precioalpublico,descuento){
      $("#codigoproducto").val('');
      $("#idproducto").html('');
      var style = 'background-color: #0ec529;color: #fff;';
      if(stock<=0){
          style = 'background-color: #ce0e00;color: #fff;';
      }
      var num = $("#tabla-contenido tbody").attr('num');
      var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'" style="'+style+'">';
          nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+='<td>'+nombre+'</td>';
          nuevaFila+='<td>'+stock+'</td>';
          nuevaFila+='<td class="mx-td-input"><input id="productCant'+num+'" type="number" value="1" onkeyup="calcularmonto()"></td>';
          nuevaFila+='<td class="mx-td-input"><input id="productUnidad'+num+'" type="number" value="'+precioalpublico+'" onkeyup="calcularmonto()" step="0.01" min="0"></td>';
          nuevaFila+='<td class="mx-td-input"><input id="productDescuento'+num+'" type="number" value="'+descuento+'" onkeyup="calcularmonto()" step="0.01" min="0"></td>';
          nuevaFila+='<td class="mx-td-input"><input id="productTotal'+num+'" type="text" value="0.00" disabled></td>';       
          nuevaFila+='<td><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+')" class="btn btn-danger big-btn"><i class="fa fa-close"></i></a></td>'
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
        var productDescuento = parseFloat($("#productDescuento"+num).val());
        var subtotal = ((productCant*productUnidad)-productDescuento).toFixed(2);
        $("#productTotal"+num).val(parseFloat(subtotal).toFixed(2));
        total = total+parseFloat((productCant*productUnidad).toFixed(2));
        descuento = descuento+parseFloat(productDescuento);
    });
    $("#subtotal").val((parseFloat(total)).toFixed(2));
    $("#descuento").val((parseFloat(descuento)).toFixed(2));
    totalfinal = (parseFloat(total)).toFixed(2) - (parseFloat(descuento)).toFixed(2);
    $("#total").val((parseFloat(totalfinal)).toFixed(2));  
}

function selectproductos(){
    var data = '';
    $("#tabla-contenido tbody tr").each(function() {
        var num = $(this).attr('id');        
        var idproducto = $(this).attr('idproducto');
        var productCant = $("#productCant"+num).val();
        var productUnidad = $("#productUnidad"+num).val();
        var productDescuento = $("#productDescuento"+num).val();
        data = data+'&'+idproducto+','+productCant+','+productUnidad+','+productDescuento;
    });
    return data;
}
  
function eliminarproducto(num){
    $("#tabla-contenido tbody tr#"+num).remove();
    calcularmonto();
}
  
$("#montorecibido").keyup(function() {
      var total =  parseFloat($("#total").val());
      var montorecibido =  parseFloat($("#montorecibido").val());
      if($("#montorecibido").val()==''){
          montorecibido = 0;
      }
      if($("#total").val()==''){
          total = 0;
      }
      var suma = montorecibido - total;
      $("#vuelto").val(parseFloat(suma).toFixed(2));
});
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
            center: myLatLng
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