@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Confirmar la Venta</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
@if(Auth::user()->idtienda==0)
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¡Con el usuario Master no puede realizar una venta, ingrese con un usuario de esta tienda!
    </div>
@else
<div id="carga-venta">
    <div class="profile-edit-container">
        <div class="custom-form">
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
                                @if($configuracion['estadostock']==1)
                                <th width="50px">Stock</th>
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
                      <a href="javascript:;" onclick="realizarpago()" class="log-submit-btn mx-realizar-pago"><span>Realizar la Venta</span> <i class="fa fa-angle-right"></i></a>
                      
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
                      <a href="javascript:;" onclick="realizarpago()" class="log-submit-btn mx-realizar-pago"><span>Realizar la Venta</span> <i class="fa fa-angle-right"></i></a>
                  </div>
                  <div id="tab-carritocompra-2" class="tab-content" style="display: none;">
                      <div class="mensaje-info">
                        <i class="fa fa-exclamation-circle"></i> Rellene correctamente su información, para poder emitir su comprobante.
                      </div>
                      <div class="row">
                         <div class="col-md-6">
                            <label>Facturación - Cliente *</label>
                            <div class="row">
                               <div class="col-md-9">
                                  <select id="idcliente">
                                      <option value="{{ $venta->idcliente }}">{{ $venta->cliente }}</option>
                                  </select>
                               </div>
                               <div class="col-md-3">
                                  <a href="javascript:;" id="modal-registrarcliente" onclick="agregarcliente()" class="btn btn-warning"><i class="fa fa-plus"></i> Agregar</a>
                               </div>
                            </div>
                            <label>Facturación - Dirección</label>
                            <input type="text" id="direccion" value="{{$venta->clientedireccion}}"/>
                            <label>Facturación - Ubicación (Ubigeo)</label>
                            <select id="idubigeo">
                                <option value="{{ $venta->idubigeo }}">{{ $venta->ubigeonombre }}</option>
                            </select>
                         </div>
                         <div class="col-md-6">
                           <label>Empresa *</label>
                            <select id="idagencia">
                                <option></option>
                                @foreach($agencia as $value)
                                <option value="{{ $value->id }}"?>{{ $value->ruc }} - {{ $value->nombrecomercial }}</option>
                                @endforeach
                            </select>
                            <label>Comprobante *</label>
                            <select id="idcomprobante">
                                <option></option>
                                @foreach($comprobante as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                         </div>
                       </div>
                       <a href="javascript:;" onclick="$('#tab-entrega').click()" class="log-submit-btn" style="margin-right: 10px;"><i class="fa fa-angle-left"></i> <span>Atras</span></a> 
                       <a href="javascript:;" onclick="realizarpago()" class="log-submit-btn" style="margin-right: 10px;"><span>Siguiente</span> <i class="fa fa-angle-right"></i></a>
                       <a href="javascript:;" onclick="realizarpago()" class="log-submit-btn mx-realizar-pago"><span>Realizar la Venta</span> <i class="fa fa-angle-right"></i></a>
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
                                margin-bottom: 10px;
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
                          <label>Monto Recibido *</label>
                          <input type="number" id="montorecibido" step="0.01">
                          <label>Vuelto</label>
                          <input type="text" id="vuelto" value="0.00" disabled> 
                        </div>    
                      </div> 
                      <a href="javascript:;" onclick="registrar_venta()" id="cont-btnventa" class="btn  big-btn  color-bg flat-btn mx-realizar-pago">
                        Realizar la Venta
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
                              <label>DNI *</label>
                              <input type="text" id="cliente_dni"/>
                              <label>Nombre *</label>
                              <input type="text" id="cliente_nombre"/>
                              <label>Apellidos *</label>
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
modal({click:'#modal-ventapuntoconsumo'});
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
  
carga_carritocompradetalle();
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
                var productCant = parseFloat($("#productCant"+num).val());
                var productUnidad = parseFloat($("#productUnidad"+num).val());
          
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
function registrar_venta(){
    callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/venta/{{ $venta->id }}',
        method: 'PUT',
        carga: '#carga-venta',
        data:{
            view: 'confirmar',
            idcliente: $('#idcliente').val(),
            direccion: $('#direccion').val(),
            idubigeo: $('#idubigeo').val(),
            idagencia: $('#idagencia').val(),
            idcomprobante: $('#idcomprobante').val(),
            subtotal: $('#subtotal').val(),
            costoenvio: $('#costoenvio').val(),
            total: $('#total').val(),
            montorecibido: $('#montorecibido').val(),
            vuelto: $('#vuelto').val(),
        }
    },
    function(resultado){
          $('#modal-ventarealizada').css('display','block');
          $('#contenido-producto').html('<div class="cont-confirm" style="margin-top: 15px;">'+
                           '<div class="confirm"><i class="fa fa-check"></i></div>'+
                           '<div class="confirm-texto">¡Correcto!</div>'+
                           '<div class="confirm-subtexto">Se ha actualizado correctamente.</div></div>'+
                           '<div class="custom-form" style="text-align: center;margin-bottom: 5px;">'+
                           '<button type="button" class="btn big-btn color-bg flat-btn mx-realizar-pago" style="margin: auto;float: none;" onclick="realizar_nueva_venta()">'+
                           '<i class="fa fa-check"></i> Realizar Nueva Venta</button></div>'+
                           '<div class="custom-form" style="text-align: center;margin-bottom: 5px;">'+
                           '<button type="button" class="btn big-btn color-bg flat-btn" style="margin: auto;float: none;" onclick="iraventas()">'+
                           '<i class="fa fa-check"></i> Ir a las Ventas</button></div>');                                                   
    })
}
function realizar_nueva_venta(){
    location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta/create') }}';
}
function iraventas(){
    location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta') }}';
}
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
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/showlistarubigeo')}}",
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
    minimumInputLength: 2
});
// fin cliente

$("#idcliente").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/showlistarusuario')}}",
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
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/showseleccionarusuario')}}",
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
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/showlistarubigeo')}}",
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
  
$("#idproducto").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/showlistarproducto')}}",
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
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/showseleccionarproducto')}}",
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
                respuesta["producto"].idtienda,
                respuesta["producto"].tiendalink,
                respuesta["producto"].tiendanombre,
            );
          }
        }
    })
});
 
$('#buscarcodigoproducto').select();
  
$('#buscarcodigoproducto').keyup( function(e) {
    if(e.keyCode == 13){
        buscarcodigo('#buscarcodigoproducto');
    }
    if(e.keyCode == 27){
        $('#tab-pago').click();
        $('#montorecibido').select();
    }
})
$('#montorecibido').keyup( function(e) {
    if(e.keyCode == 13){
        registrar_venta();
    }
})
function buscarcodigo(pthis){
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/showseleccionarproductocodigo')}}",
        type:'GET',
        data: {
            codigoproducto : $(pthis).val()
        },
        beforeSend: function (data) {
            carga({
                input:'#carga-venta',
                color:'info',
                mensaje:'Buscado el Producto, Espere por favor...'
            }); 
        },
        success: function (respuesta){
          $('#buscarcodigoproducto').val('');
          if(respuesta["resultado"]=='ERROR'){
              carga({
                  input:'#carga-venta',
                  color:'danger',
                  mensaje: respuesta['mensaje']
              });
          }else{
              agregarproducto(
                respuesta["producto"].id,
                respuesta["producto"].codigo,
                respuesta["producto"].nombre,
                respuesta["stock"],
                respuesta["producto"].precioalpublico,
                respuesta["producto"].idtienda,
                respuesta["producto"].tiendalink,
                respuesta["producto"].tiendanombre,
                1
              );
              removecarga({input:'#carga-venta'});
          }
        },
        error:function(respuesta){
              carga({
                  input:'#carga-venta',
                  color:'danger',
                  mensaje:formerror({dato:respuesta})
              });
        }
    })
}
function agregarproducto(idproducto,codigo,nombre,stock,precioalpublico,idtienda,tienda_link,tienda_nombre,cantidad=1){
      $("#codigoproducto").val('');
      $("#idproducto").html('');
      var style = 'background-color: #0ec529;color: #fff;';
  
      var tdstock = '';
      @if($configuracion['estadostock']==1)
      if(stock<1){
          style = 'background-color: #ce0e00;color: #fff;';
          
      }
      tdstock = '<td style="text-align: center"> '+stock+' </td>';
      @endif
  
      var num = $("#tabla-contenido tbody").attr('num');
  
      var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'" producto_codigo="'+codigo+'" producto_nombre="'+nombre+'" idtienda="'+idtienda+'" tienda_link="'+tienda_link+'" tienda_nombre="'+tienda_nombre+'" nombreproducto="'+codigo+' - '+nombre+'" style="'+style+'">';
          nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+='<td>'+nombre+'</td>'+tdstock;
          nuevaFila+='<td class="mx-td-input"><input id="productCant'+num+'" type="number" value="'+cantidad+'" disabled></td>';
          nuevaFila+='<td class="mx-td-input"><input id="productUnidad'+num+'" type="number" value="'+precioalpublico+'" step="0.01" min="0" disabled></td>';
          nuevaFila+='<td class="mx-td-input"><input id="productTotal'+num+'" type="text" value="0.00" disabled></td>';       
          nuevaFila+='</tr>';
      $("#tabla-contenido").append(nuevaFila);
      $("#tabla-contenido tbody").attr('num',parseInt(num)+1);
  
      $('#productCant'+num).select();
      $('#productCant'+num).keyup( function(e) {
          if(e.keyCode == 13){
              $('#buscarcodigoproducto').select();
          }
          if(e.keyCode == 27){
              $('#tab-pago').click();
              $('#montorecibido').focus();
              $('#montorecibido').select();
          }
      })
  
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

function selectproductos(){
    var data = '';
    $("#tabla-contenido tbody tr").each(function() {
        var num = $(this).attr('id');        
        var idproducto = $(this).attr('idproducto');
        var productCant = $("#productCant"+num).val();
        var productUnidad = $("#productUnidad"+num).val();
        var nombreproducto = $(this).attr('nombreproducto');
        data = data+'/&/'+idproducto+'/,/'+productCant+'/,/'+productUnidad+'/,/'+nombreproducto;
    });
    return data;
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