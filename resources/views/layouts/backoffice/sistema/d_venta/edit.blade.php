@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar la Venta</span>
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
          @if(configuracion($tienda->id,'sistema_nivelventa')['valor']==2)
          <div class="box" style="width: 330px;">
            <input class="Switcher__checkbox sr-only" id="idestado" type="checkbox" <?php echo $venta->s_idestado==2? 'checked="checked"':'' ?>>
            <label class="Switcher" for="idestado">
              <div class="Switcher__trigger" data-value="Actualizar Pedido"></div>
              <div class="Switcher__trigger" data-value="Confirmar Pedido"></div>
            </label>
          </div>
          <style>
          .Switcher::before {
            transform: translateX(-75%);
          }
          .Switcher__checkbox:checked + .Switcher::before {
            transform: translateX(23.5%);
          }
          </style>
          @else
          <div class="box" style="width: 330px;">
            <input class="Switcher__checkbox sr-only" id="idestado" type="checkbox" <?php echo $venta->s_idestado==3? 'checked="checked"':'' ?>>
            <label class="Switcher" for="idestado">
              <div class="Switcher__trigger" data-value="Actualizar Pedido"></div>
              <div class="Switcher__trigger" data-value="Realizar Venta"></div>
            </label>
          </div>
          <style>
          .Switcher::before {
            transform: translateX(-75%);
          }
          .Switcher__checkbox:checked + .Switcher::before {
            transform: translateX(23.5%);
          }
          </style>
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
                                <th colspan="2">Producto</th>
                                <th width="10px"></th>
                                @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
                                <th width="1px"></th>
                                @endif
                                @if(configuracion($tienda->id,'sistema_estadostock')['valor']==1)
                                <th width="50px">Stock</th>
                                @endif
                                <th width="60px">Cantidad</th>
                                <th width="110px">P. Unitario</th>
                                <th width="110px">P. Total</th> 
                                <th width="10px"></th>
                              </tr>
                              <tr>
                                  <td class="mx-td-input"><input type="text" id="buscarcodigoproducto"/></td>
                                  <td colspan="{{configuracion($tienda->id,'sistema_estadostock')['valor']==1?'6':'5'}}" class="mx-td-input">
                                    <select id="idproducto">
                                        <option></option>
                                    </select>
                                  </td>
                                  <td width="auto"></td>
                              </tr>
                            </thead>
                            <tbody num="0" id="tbody1"></tbody>
                            <tbody num="0" id="tbody_totalventa"></tbody>
                            <tbody num="0" id="tbody2"></tbody>
                            <tbody num="0" id="tbody_totaldescuento"></tbody>
                        </table>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                        </div> 
                        <div class="col-md-4">
                          <div style="font-weight: bold;font-size: 18px;">Total:</div>
                          <input type="text" id="subtotal" placeholder="0.00" style="font-size: 30px;
                                  font-weight: bold;
                                  padding-top: 5px;
                                  padding-bottom: 5px;
                                  text-align: center;" disabled>
                        </div>    
                      </div> 
                      <div class="custom-form">
                      <a href="javascript:;" onclick="$('#tab-entrega').click()" class="log-submit-btn" style="margin-right: 10px;"><span>Siguiente</span> <i class="fa fa-angle-right"></i></a>
                      <a href="javascript:;" onclick="realizarpago()" class="log-submit-btn mx-realizar-pago"><span>Registrar la Venta</span> <i class="fa fa-angle-right"></i></a>
                      </div>
                  </div>
                  <div id="tab-carritocompra-1" class="tab-content" style="display: none;">
                      <div class="mensaje-info">
                        <i class="fa fa-exclamation-circle"></i> Estos datos son unicamente para la entrega de pedido.
                      </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Tipo de entrega *</label>
                            <select id="idtipoentrega">
                                <option></option>
                                @foreach($tipoentregas as $value)
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                          <div id="cont-tipo-delivery-info" style="display:none;">
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
                                  <input type="date" value="{{ $ventadelivery!=''?$ventadelivery->fecha:Carbon\Carbon::now()->format('Y-m-d') }}" id="delivery_fecha">
                              </div>
                              <div class="col-md-6">
                                  <input type="time" value="{{ $ventadelivery!=''?$ventadelivery->hora:Carbon\Carbon::now()->format('h:s:i') }}" id="delivery_hora" step="1">
                              </div>
                          </div>
                          </div>
                          <label>Nombre de persona a entregar *</label>
                          <input type="text" value="{{ $ventadelivery!=''?$ventadelivery->nombre:'' }}" id="delivery_pernonanombre">
                          <label>Número de celular de entrega *</label>
                          <input type="text" value="{{ $ventadelivery!=''?$ventadelivery->telefono:'' }}" id="delivery_numerocelular">
                          <label>Dirección de entrega *</label>
                          <input type="text" value="{{ $ventadelivery!=''?$ventadelivery->direccion:'' }}" id="delivery_direccion">
                          </div>
                        </div>
                        <div id="cont-tipo-delivery-mapa" style="display:none;">
                        <div class="col-md-6">
                          <label>Ubicación de entrega (Referencia) *</label>
                          <div id="singleMap"></div>
                          <input type="hidden" value="{{ $ventadelivery!=''?$ventadelivery->mapa_ubicacion_lat:'' }}" id="mapa_ubicacion_lat"/>
                          <input type="hidden" value="{{ $ventadelivery!=''?$ventadelivery->mapa_ubicacion_lng:'' }}" id="mapa_ubicacion_lng"/>
                        </div>
                        </div>
                      </div>
                      <a href="javascript:;" onclick="$('#tab-pedido').click()" class="log-submit-btn" style="margin-right: 10px;"><i class="fa fa-angle-left"></i> <span>Atras</span></a> 
                      <a href="javascript:;" onclick="$('#tab-facturacion').click()" class="log-submit-btn" style="margin-right: 10px;"><span>Siguiente</span> <i class="fa fa-angle-right"></i></a>
                      <a href="javascript:;" onclick="realizarpago()" class="log-submit-btn mx-realizar-pago"><span>Registrar la Venta</span> <i class="fa fa-angle-right"></i></a>
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
                                  <a href="javascript:;" id="modal-registrarcliente" class="btn btn-warning"><i class="fa fa-plus"></i> Agregar</a>
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
                       <a href="javascript:;" onclick="realizarpago()" class="log-submit-btn mx-realizar-pago"><span>Registrar la Venta</span> <i class="fa fa-angle-right"></i></a>
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
                              <label>Costo de Envio *</label>
                              <input type="number" value="{{$venta->envio}}" style="text-align: center;font-size: 16px;" id="costoenvio" step="0.01" onkeyup="calcularmonto()" onclick="calcularmonto()"/>
                          </div>  
                          <div id="cont-montorecibido" <?php echo $venta->s_idestado!=3? 'style="display:none;"':'' ?>>
                              <label>Total</label>
                              <input type="text" id="total" placeholder="0.00" style="font-size: 30px;
                                  font-weight: bold;
                                  padding-top: 5px;
                                  padding-bottom: 5px;
                                  text-align: center;" disabled>
                              <label>Total Redondeado</label>
                              <input type="text" id="total_redondeado" placeholder="0.00" style="font-size: 30px;
                                  font-weight: bold;
                                  padding-top: 5px;
                                  padding-bottom: 5px;
                                  text-align: center;" disabled>
                              <label>Monto Recibido *</label>
                              <input type="number" id="montorecibido" step="0.01">
                              <label>Vuelto</label>
                              <input type="text" id="vuelto" value="0.00" disabled>
                          </div>   
                        </div>    
                      </div> 
                      <a href="javascript:;" onclick="registrar_venta()" id="cont-btnventa" class="btn  big-btn  color-bg flat-btn mx-realizar-pago">
                          @if($venta->s_idestado==1) 
                            Actualizar Pedido
                          @elseif($venta->s_idestado==2) 
                            Confirmar Pedido
                          @elseif($venta->s_idestado==3) 
                            Realizar Venta
                          @else
                            Registrar
                          @endif 
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
@include('app.modal_usuario_registrar',[
    'nombre'            =>'Registrar Cliente',
    'modal'             =>'registrarcliente',
    'idusuario'         =>'idcliente',
    'usuariodireccion'  =>'direccion',
    'usuarioubigeo'     =>'idubigeo'
])
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
    @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
      <div class="main-register-wrap modal-seleccionardescuento">
          <div class="main-overlay"></div>
          <div class="main-register-holder">
              <div class="main-register fl-wrap">
                  <div class="close-reg"><i class="fa fa-times"></i></div>
                  <h3>Descuentos</h3>
                  <div class="mx-modal-cuerpo" id="contenido-seleccionardescuento" style="float: left;width:100%;">
                    <div id="mx-carga-descuento">
                    <form class="js-validation-signin px-30" 
                        action="javascript:;" 
                        onsubmit="callback({
                          route: 'backoffice/tienda/sistema/{{ $tienda->id }}/venta',
                          method: 'POST',
                          carga: '#mx-carga-descuento',
                          data:{
                              view: 'seleccionardescuento'
                          }
                      },
                      function(resultado){
                          //     
                      },this)">
                      <div class="profile-edit-container">
                          <div id="resultado-descuento" style="margin-bottom: 5px;"></div>
                      </div>
                  </form> 
                  </div>
                  </div>
                  <div class="mx-modal-cuerpo" id="contenido-confirmar-seleccionardescuento"></div>
              </div>
          </div>
      </div>
    @endif
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
 .tdstock > div {
      margin-top: -6px;
      margin-bottom: -6px;
  }
  
  #tddescuentocarga{
      background-color: #343a40;
  }
  #tddescuentocarga > div {
      text-align: center;
  }
  #tddescuentocarga > div > img{
      height: 38px;
  }
  .tddescuento > div {
      text-align: center;
  }
  .tddescuento > div > img{
      height: 38px;
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
// carrito de compra
$("#costoenvio").keyup(function() {
    var subtotal = $("#subtotal").val();
    var costoenvio = $("#costoenvio").val();
      if(costoenvio==''){
          costoenvio = 0;
      }
    var total = parseFloat(subtotal)+parseFloat(costoenvio);
    $("#total").val(total.toFixed(2));
  
    //total
    var total =  parseFloat($("#total_redondeado").val());
    var montorecibido =  parseFloat($("#montorecibido").val());
      if($("#montorecibido").val()==''){
          montorecibido = 0;
      }
      var suma = montorecibido - total;
      $("#vuelto").val(parseFloat(suma).toFixed(2));
});


@if(configuracion($tienda->id,'sistema_nivelventa')['valor']==2)
$("#idestado").change(function() {
    var idestado = $("#idestado:checked").val();
    if(idestado=='on'){
        $('#cont-montorecibido').css('display','none');
        $('#cont-btnventa').html('Confirmar Pedido');
    }else{
        $('#cont-montorecibido').css('display','none');
        $('#cont-btnventa').html('Actualizar Pedido');
    }
});
@else
$("#idestado").change(function() {
    var idestado = $("#idestado:checked").val();
    if(idestado=='on'){
        $('#cont-montorecibido').css('display','block');
        $('#cont-btnventa').html('Realizar Venta');
    }else{
        $('#cont-montorecibido').css('display','none');
        $('#cont-btnventa').html('Actualizar Pedido');
    }
});
@endif
 
  
@foreach($ventadetalles as $value)
                agregarproducto(
                     '{{$value->idproducto}}',
                     '{{$value->productocodigo}}',
                     '{{$value->productoimagen}}',
                     '{{$value->productonombre}}',
                     '{{$value->productoprecioalpublico}}',
                     '{{$value->idtienda}}',
                     '{{$value->tiendalink}}',
                     '{{$value->tiendanombre}}',
                     '{{$value->cantidad}}',
                     '{{$value->idestadodetalle}}',
                     '{{$value->detalle}}'
                );
@endforeach  
  
carga_carritocompradetalle();
function carga_carritocompradetalle(){
        var subtotal = 0;
        var total = 0;
        var totaldescuento = 0;
        var item = 1;
        var detallepedido = '<b style="font-size: 15px;">DETALLE DE PEDIDO</b><br>';
        detallepedido = detallepedido+'<hr style="border: 1px dashed #31353d;margin-top: 5px;margin-bottom: 5px;">';
        $("#tabla-contenido > tbody#tbody1 > tr").each(function() {
                var num = $(this).attr('id');        
                var producto_codigo = $(this).attr('producto_codigo');  
                var producto_nombre = $(this).attr('producto_nombre');  
                var productCant = parseFloat($("#productCant"+num).val()).toFixed(3);
                var productUnidad = parseFloat($("#productUnidad"+num).val()).toFixed(2);
          
                // descuento
                var descuento = 0;
                @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
                descuento = $("#tabla_total_descuento").val();
                totaldescuento = totaldescuento+parseFloat(descuento).toFixed(2);
                @endif
          
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
        @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
        detallepedido = detallepedido+'<b style="font-size: 22px;">Total Venta: '+total.toFixed(2)+'</b><br>';
        detallepedido = detallepedido+'<b style="font-size: 22px;">Total Descuento: -'+parseFloat(totaldescuento).toFixed(2)+'</b><br>';
        @endif
        detallepedido = detallepedido+'<b style="font-size: 22px;">Total: '+(total-parseFloat(totaldescuento)).toFixed(2)+'</b><br>';
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
            view: 'editar',
            productos: selectproductos(),
            @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
            productos_descuento: selectproductos_descuento(),
            totalventa: $('#tabla_total_venta').val(),
            totaldescuento: $('#tabla_total_descuento').val(),
            @endif
            ventacomida_numeromesa: $('#ventacomida_numeromesa').val(),
            idestado: $('#idestado:checked').val(),
            idcliente: $('#idcliente').val(),
            direccion: $('#direccion').val(),
            idubigeo: $('#idubigeo').val(),
            idagencia: $('#idagencia').val(),
            idcomprobante: $('#idcomprobante').val(),
            idtipoentrega: $('#idtipoentrega').val(),
            subtotal: $('#subtotal').val(),
            costoenvio: $('#costoenvio').val(),
            total: $('#total').val(),
            total_redondeado: $('#total_redondeado').val(),
            montorecibido: $('#montorecibido').val(),
            vuelto: $('#vuelto').val(),
            idestadoenvio: $('#idestadoenvio').val(),
            delivery_fecha: $('#delivery_fecha').val(),
            delivery_hora: $('#delivery_hora').val(),
            delivery_pernonanombre: $('#delivery_pernonanombre').val(),
            delivery_numerocelular: $('#delivery_numerocelular').val(),
            delivery_direccion: $('#delivery_direccion').val(),
            mapa_ubicacion_lat: $('#mapa_ubicacion_lat').val(),
            mapa_ubicacion_lng: $('#mapa_ubicacion_lng').val(),
        }
    },
    function(resultado){
          $('#modal-ventarealizada').css('display','block');
          var imprimir = '';
          if(resultado['idestado']==3){
              imprimir = '<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta') }}/'+resultado['idventa']+'/edit?view=ticketpdf#zoom=130" frameborder="0" width="100%" height="600px"></iframe>';
          }
          $('#contenido-producto').html('<div class="cont-confirm" style="margin-top: 15px;">'+
                           '<div class="confirm"><i class="fa fa-check"></i></div>'+
                           '<div class="confirm-texto">¡Correcto!</div>'+
                           '<div class="confirm-subtexto">Se ha actualizado correctamente.</div></div>'+
                           '<div class="custom-form" style="text-align: center;margin-bottom: 5px;">'+
                           '<button type="button" class="btn big-btn color-bg flat-btn mx-realizar-pago" style="margin: auto;float: none;" onclick="editar_venta('+resultado['idventa']+')">'+
                           '<i class="fa fa-check"></i> Volver a Editar la Venta</button></div>'+
                           '<div class="custom-form" style="text-align: center;margin-bottom: 5px;">'+
                           '<button type="button" class="btn big-btn color-bg flat-btn mx-realizar-pago" style="margin: auto;float: none;" onclick="realizar_nueva_venta()">'+
                           '<i class="fa fa-check"></i> Realizar Nueva Venta</button></div>'+
                           '<div class="custom-form" style="text-align: center;margin-bottom: 5px;">'+
                           '<button type="button" class="btn big-btn color-bg flat-btn" style="margin: auto;float: none;" onclick="iraventas()">'+
                           '<i class="fa fa-check"></i> Ir a las Ventas</button></div>'+imprimir); 
      
          removecarga({input:'#carga-venta'});
    })
}
function editar_venta(idventa){
    $('#modal-ventarealizada').css('display','none');
    $('#tab-pedido').click()
    $('#buscarcodigoproducto').select();
    //location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta') }}/'+idventa+'/edit?view=editar';
}
function realizar_nueva_venta(){
    location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta/create') }}';
}
function iraventas(){
    location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta') }}';
}
// seleccionar productos
function agregarproductounidad(idproducto){
    removecarga({input:'#mx-carga-productounidad'});
    $('#contenido-registrarproductounidad').css('display','none');
    $('#contenido-confirmar-registrarproductounidad').html('');
    $('#agregarproducto_cantidad').val('');
    $('#cont-registrarproductounidadstock').css('display','none');
  
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/showseleccionarunidadproducto')}}",
        type:'GET',
        data: {
            idproducto : idproducto
        },
        beforeSend: function (data) {
            load('#contenido-confirmar-registrarproductounidad');
        },
        success: function (respuesta){
          if(respuesta["producto"]!=''){
              $('#contenido-confirmar-registrarproductounidad').html('');
              $('#contenido-registrarproductounidad').css('display','block');
              $("#agregarproducto_idproducto").html(respuesta["producto"]);
          }else{
              $('#contenido-confirmar-registrarproductounidad').html('<div class="mensaje-warning" style="margin-bottom: 0;">'+
                                                                     '<i class="fa fa-warning"></i> '+
                                                                     '¡El Producto no tiene otra presentación!!'+
                                                                     '</div>');
          }
              
        }
    });
}

$("#agregarproducto_idproducto").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-registrarproductounidadstock').css('display','none');
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/showstockproducto')}}",
        type:'GET',
        data: {
            idproducto : e.currentTarget.value
        },
        beforeSend: function (data) {
            load('#contenido-confirmar-registrarproductounidad');
        },
        success: function (respuesta){
            $('#contenido-confirmar-registrarproductounidad').html('');
            $('#cont-registrarproductounidadstock').css('display','block');
            $("#agregarproducto_stock").val(respuesta["stock"]);
        }
    });
});
// fin seleccionar productos
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

function json_productos() {
  var json = null;
  $.ajax({
    'async': false,
    'global': false,
    'url': "{{url('public/backoffice/tienda/'.$tienda->id.'/productojson/productos.json')}}",
    'dataType': "json",
    'success': function(data) {
        json = data;
    }
  });
  return json['data'];
};
var datajson_productos = json_productos();
  
$("#idproducto").select2({
    data: datajson_productos,
    placeholder: "--  Seleccionar Producto --",
    allowClear: true,
    minimumInputLength: 2,
    templateResult: function (state) {
        if (!state.id) {
            return state.text;
        }
        var urlimagen = '{{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }}';
        if(state.imagen!=null){
            urlimagen = '{{ url('public/backoffice/tienda') }}/'+state.idtienda+'/producto/40/'+state.imagen;
        }
        return $('<div>'+
                 '<div style="background-image: url('+urlimagen+');'+
                            'background-repeat: no-repeat;'+
                            'background-size: contain;'+
                            'background-position: center;'+
                            'width: 40px;'+
                            'height: 40px;'+
                            'float: left;'+
                            'margin-right: 5px;'+
                            'margin-top: -5px;">'+
                          '</div><div>'+state.nombre+'</div><div>'+state.unidadmedida+' - '+state.precioalpublico+'</div>');
    }
}).on("change", function(e) {
    if(e.currentTarget.value!=''){
        var producto = datajson_productos.find(val => val.id === parseFloat(e.currentTarget.value));
        agregarproducto(
            producto.id,
            producto.codigo,
            producto.imagen,
            producto.nombre,
            producto.precioalpublico,
            producto.idtienda,
            producto.tiendalink,
            producto.tiendanombre,
            1,
            producto.idestadodetalle
        );
    }  
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
    if($(pthis).val()!=''){
        var producto = datajson_productos.find(val => val.codigo === $(pthis).val());
        if(producto!=undefined){
            agregarproducto(
                producto.id,
                producto.codigo,
                producto.imagen,
                producto.nombre,
                producto.precioalpublico,
                producto.idtienda,
                producto.tiendalink,
                producto.tiendanombre,
                1,
                producto.idestadodetalle
            );
            $('#buscarcodigoproducto').val('');
        }
    }
}
function agregarproducto(idproducto,codigo,imagen,nombre,precioalpublico,idtienda,tienda_link,tienda_nombre,cantidad=1,idestadodetalle,detalle){
      $("#codigoproducto").val('');
      $("#idproducto").val(null).trigger('change');
  
      var num = $("#tabla-contenido > tbody#tbody1").attr('num');
      var style = 'background-color: #008cea;color: #fff;';
  
      var tdstock = '';
      @if(configuracion($tienda->id,'sistema_estadostock')['valor']==1)
      tdstock = '<td style="text-align: center" class="tdstock" id="tdstock'+num+'">---</td>';
      @endif
      var tddescuento = '';
      @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
      tddescuento = '<td style="text-align: center" id="tddescuento'+num+'" class="tddescuento"></td>';
      @endif
  
      var productDetalle = '<td colspan="2">'+nombre+'</td>';
      if(idestadodetalle==1){
          productDetalle = '<td>'+nombre+'</td><td><input id="productDetalle'+num+'" value="'+detalle+'" type="text"></td>';
      }
  
      var newimagen = '{{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }}';
      if(imagen!=null && imagen!=''){
          newimagen = '{{ url('public/backoffice/tienda') }}/'+idtienda+'/producto/40/'+imagen;
      }
      var imagentd = '<div style="background-image: url('+newimagen+');'+
                            'background-repeat: no-repeat;'+
                            'background-size: contain;'+
                            'background-position: center;'+
                            'width: 50px;'+
                            'height: 34px;">'+
                          '</div>';
  
      var nuevaFila='<tr id="'+num+'" idestadodetalle="'+idestadodetalle+'" idproducto="'+idproducto+'" producto_codigo="'+codigo+'" producto_nombre="'+nombre+'" idtienda="'+idtienda+'" tienda_link="'+tienda_link+'" tienda_nombre="'+tienda_nombre+'" nombreproducto="'+codigo+' - '+nombre+'" style="'+style+'">';
          nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+=productDetalle+tddescuento+'<td>'+imagentd+'</td>'+tdstock;
          nuevaFila+='<td class="mx-td-input"><input id="productCant'+num+'" type="number" value="'+cantidad+'" onkeyup="calcularmonto();" onclick="calcularmonto();"></td>';
          nuevaFila+='<td class="mx-td-input"><input id="productUnidad'+num+'" type="number" value="'+precioalpublico+'" onkeyup="calcularmonto();" onclick="calcularmonto();" step="0.01" min="0" <?php echo configuracion($tienda->id,'sistema_estadopreciounitario')['valor']==1?'':'disabled' ?>></td>';
          nuevaFila+='<td class="mx-td-input"><input id="productTotal'+num+'" type="text" value="0.00" disabled></td>';       
          nuevaFila+='<td><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>'
          nuevaFila+='</tr>';
      $("#tabla-contenido > tbody#tbody1").append(nuevaFila);
      $("#tabla-contenido > tbody#tbody1").attr('num',parseInt(num)+1);
  
      @if(configuracion($tienda->id,'sistema_estadostock')['valor']==1)
      $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/showstock')}}",
            type:'GET',
            data: {
                idproducto : idproducto
            },
            beforeSend: function (data) {
                load('#tdstock'+num);  
            },
            success: function (respuesta){
                if(respuesta['stock']<1){
                    $('tr#'+num).css('background-color','#ce0e00');
                    $('tr#'+num).css('color','#fff');
                }else{
                    $('tr#'+num).css('background-color','#0ec529');
                    $('tr#'+num).css('color','#fff');
                }
                $('#tdstock'+num).html(respuesta['stock']);
            }
      })
      @endif
  
      @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
      cargar_descuento(idproducto,num);
      @endif
  
      setTimeout(function(){ $('#productCant'+num).select(); }, 100);
  
  
      @if(configuracion($tienda->id,'sistema_estadopreciounitario')['valor']==1)
      $('#productCant'+num).keyup( function(e) {
          if(e.keyCode == 13){
              $('#productUnidad'+num).select();
          }
          if(e.keyCode == 27){
              $('#tab-pago').click();
              $('#montorecibido').focus();
              $('#montorecibido').select();
          }
      })
      $('#productUnidad'+num).keyup( function(e) {
          if(e.keyCode == 13){
              $('#buscarcodigoproducto').select();
          }
          if(e.keyCode == 27){
              $('#tab-pago').click();
              $('#montorecibido').focus();
              $('#montorecibido').select();
          }
      })
      @else
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
      @endif
  
      calcularmonto();
      // modal
      modal({click:'a#modal-registrarproductounidad'});
      // fin modal
}

@if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
      function cargar_descuento(idproducto,num){
          $.ajax({
                url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/showdescuento')}}",
                type:'GET',
                data: {
                    idproducto : idproducto
                },
                beforeSend: function (data) {
                    load('#tddescuento'+num); 
                },
                success: function (respuesta){
                    var cupon = '';
                    if(respuesta[0]['lista_descuento'].length>0){
                        var cupon = '<a href="javascript:;" id="modal-seleccionardescuento" class="lista_descuento'+num+'" onclick="seleccionardescuento('+idproducto+')" array_descuento=\''+JSON.stringify(respuesta[0]['lista_descuento'])+'\'"><img src="{{url('public/backoffice/sistema/icono-cupon-descuento.png')}}" style="height: 30px;"></a>';
                        $('#tddescuento'+num).html(cupon);
                        calcularmonto();
                        modal({click:'#modal-seleccionardescuento'});
                    }else{
                        $('#tddescuento'+num).html('');
                    }
                }
          })      
      }
      function actualizar_descuento(total_venta){
                        // TOTAL VENTA 
                        var nuevaFila='<tr style="background-color: #066aad;color: #fff;">';
                                nuevaFila+='<td colspan="7" style="text-align: right;padding-right: 10px;">Total Venta:</td>';
                                nuevaFila+='<td class="mx-td-input"><input id="tabla_total_venta" type="number" value="'+total_venta+'" step="0.01" min="0" disabled></td>';       
                                nuevaFila+='<td></td>'
                                nuevaFila+='</tr>';
                        $("#tabla-contenido > tbody#tbody_totalventa").html(nuevaFila);
                        // DATA DESCUENTO
                        $("#tabla-contenido > tbody#tbody2").html('');
                        var array_tabla =[];
                        var array_tabla_producto =[];
                        var x = 0;
                        $("#tabla-contenido > tbody#tbody1 > tr").each(function() {
                            var num = $(this).attr('id');    
                            var productCant = parseFloat($("#productCant"+num).val());
                            var lista_descuento = $(".lista_descuento"+num).attr('array_descuento');
                            if(lista_descuento!=undefined){
                                var idproductomaster = parseInt($(this).attr('idproducto'));
                                for(var i = 0; i<productCant; i++){
                                    array_tabla_producto.push({
                                        idproducto:idproductomaster,
                                        estado:'no'
                                    });
                                    array_tabla.push({
                                        data:JSON.parse(lista_descuento)
                                    });
                                    x++;
                                }
                            } 
                        });
                        // JUNTAR DESCUENTOS
                        const array_descuentos=[];
                        $.each(array_tabla, function( keytabla, valuetabla ) {
                            $.each(valuetabla.data, function( key, value ) {
                                  var array_descuentos_data = [];
                                  $.each(value.detalle, function( keydetalle, valuedetalle ) {
                                      array_descuentos_data.push({
                                          estado:'no',
                                          idproducto:valuedetalle.idproducto,
                                          productonombre:valuedetalle.productonombre
                                      });
                                  });
                                  array_descuentos.push({
                                      total:value.total,
                                      montodescuento:value.montodescuento,
                                      totalpack:value.totalpack,
                                      data:array_descuentos_data
                                  });
                            });
                        });
                        // MARCAR LOS DECUENTOS VALIDOS
                        $.each(array_descuentos, function( keytabla, valuetabla ) {
                            var contdc = 0;
                            $.each(valuetabla.data, function( key, value ) {
                                if(!!array_tabla_producto.find(function(valueproduct, index) {
                                    var result = false;
                                    if(valueproduct.idproducto==value.idproducto && valueproduct.estado==='no'){
                                        valueproduct.estado = 'exit'
                                        result = true;
                                        contdc++;
                                    }
                                    return result;
                                })){
                                    value.estado = 'correcto';
                                }
                            });
                            if((valuetabla.data.length)>contdc){
                                $.each(valuetabla.data, function( key, value ) {
                                    if(value.estado == 'correcto'){
                                        value.estado = 'no';
                                        array_tabla_producto.push({
                                            idproducto:value.idproducto,
                                            estado:'no'
                                        });
                                    }
                                });
                            }else if((valuetabla.data.length)==contdc){
                                valuetabla.estado = 'correcto';
                            }
                        });
                        // LIMPIAR Y SOLO MOSTRAR LOS DESCUENTOS
                        var array_descuentos_ultimo = [];
                        $.each(array_descuentos, function( keytabla, valuetabla ) {
                            if(valuetabla.estado=='correcto'){
                                var array_descuentos_ultimo_data = [];
                                var cont=0;
                                $.each(valuetabla.data, function( key, value ) {
                                    if(value.estado=='correcto'){
                                        array_descuentos_ultimo_data.push({
                                            'cantidad' : 1,
                                            'idproducto' : value.idproducto,
                                            'productonombre' : value.productonombre
                                        });
                                    }else{
                                        cont++;
                                    }
                                }); 
                                if(cont==0){
                                    var data_idproducto = '';
                                    // Sumar duplicados
                                    const miCarritoSinDuplicados = array_descuentos_ultimo_data.reduce((acumulador, valorActual) => {
                                        const elementoYaExiste = acumulador.find(elemento => elemento.idproducto === valorActual.idproducto);
                                        data_idproducto = data_idproducto+','+valorActual.idproducto;
                                        if (elementoYaExiste) {
                                            return acumulador.map((elemento) => {
                                                if (elemento.idproducto === valorActual.idproducto) {
                                                    return {
                                                      ...elemento,
                                                      cantidad: elemento.cantidad + valorActual.cantidad
                                                    }
                                                }
                                                return elemento;
                                            });
                                        }

                                        return [...acumulador, valorActual];
                                    }, []);
                                    // Fin Sumar duplicados
                                    array_descuentos_ultimo.push({
                                        'data_idproducto' : data_idproducto,
                                        'total':valuetabla.total,
                                        'montodescuento':valuetabla.montodescuento,
                                        'totalpack':valuetabla.totalpack,
                                        'data' : miCarritoSinDuplicados
                                    });
                                }
                            } 
                        });
                        //MOSTRAR EN TABLA EL RESULTADO
                        var total_descuento = 0;
                        $.each(array_descuentos_ultimo, function( keytabla, valuetabla ) {
                                var producto_nombres = '';
                                var producto_idproducto = '';
                                var num2 = $("#tabla-contenido > tbody#tbody2").attr('num');
                                var cont=0;
                                $.each(valuetabla.data, function( key, value ) {
                                        producto_nombres = producto_nombres+'<div style="float: left;background-color: #535e67;padding: 5px;border-radius: 5px;margin-right: 3px;">('+value.cantidad+') '+value.productonombre+'</div>';
                                        producto_idproducto = producto_idproducto+'/-/'+value.idproducto;
                                }); 
                                    var nuevaFila='<tr id="'+num2+'" idproducto="'+valuetabla.data_idproducto+'" total="'+valuetabla.total+'" montodescuento="'+valuetabla.montodescuento+'" totalpack="'+valuetabla.totalpack+'" style="background-color: #73808c;color: #fff;">';
                                            nuevaFila+='<td></td>';
                                            nuevaFila+='<td colspan="6">'+producto_nombres+'</td>';
                                            nuevaFila+='<td class="mx-td-input"><input id="productTotal'+num2+'" type="number" value="'+valuetabla.montodescuento+'" step="0.01" min="0" disabled></td>';       
                                            nuevaFila+='<td></td>'
                                            nuevaFila+='</tr>';
                                    $("#tabla-contenido > tbody#tbody2").append(nuevaFila);
                                    $("#tabla-contenido > tbody#tbody2").attr('num',parseInt(num2)+1); 
                                total_descuento = total_descuento+parseFloat(valuetabla.montodescuento);
                    
                        });
                        // TOTAL VENTA 
                        var nuevaFila='<tr style="background-color: #5b666f;color: #fff;">';
                                nuevaFila+='<td colspan="7" style="text-align: right;padding-right: 10px;">Total Descuento:</td>';
                                nuevaFila+='<td class="mx-td-input"><input id="tabla_total_descuento" type="number" value="'+total_descuento.toFixed(2)+'" step="0.01" min="0" disabled></td>';       
                                nuevaFila+='<td></td>'
                                nuevaFila+='</tr>';
                        $("#tabla-contenido > tbody#tbody_totaldescuento").html(nuevaFila);
      }
      function seleccionardescuento(idproducto){
          $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/showdescuento')}}",
            type:'GET',
            data: {
                idproducto : idproducto
            },
            beforeSend: function (data) {
                load('#resultado-descuento');  
            },
            success: function (respuesta){
                var table_descuento = '';
                $.each(respuesta[0]['lista_descuento'], function( key, value ) {
                    table_descuento = table_descuento+'<div class="car_cont">';
                    $.each(value.detalle, function( keydetalle, valuedetalle ) {
                        table_descuento = table_descuento+'<div style="float: left;width: 100%;"><div class="car_producto" style="margin-bottom: 2px;">'+(valuedetalle.productocodigo!=''?valuedetalle.productocodigo+' - ':'')+valuedetalle.productonombre+' / '+valuedetalle.precioalpublico+'</div></div>';
                    });
                    table_descuento = table_descuento+'<div style="float: left;width: 100%;">'+
                      '<div class="car_cantidad"><b>Total:</b> '+value.total+'</div>'+
                      '<div class="car_subtotal"><b>Descuento:</b> '+value.montodescuento+'</div>'+
                      '<div class="car_total"><b>T. Pack:</b> '+value.totalpack+'</div>'+
                      '</div><div style="float: left;width: 100%;"><a href="javascript:;" class="btn  big-btn  color-bg flat-btn" style="margin-top: 2px;background-color: #094379;padding: 8px;">Agregar Todos</a></div></div>';
                });
                $('#resultado-descuento').html(table_descuento);
              

            }
        })
    }
@endif
function calcularmonto(){
    var total = 0;
    $("#tabla-contenido > tbody#tbody1 > tr").each(function() {
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
    var totalfinal = (parseFloat(total)).toFixed(2);
    // Descuento
    var totaldescuento = 0;
    @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
    actualizar_descuento(totalfinal);
    totaldescuento = $("#tabla_total_descuento").val();
    @endif
    var totalfinal = (totalfinal-parseFloat(totaldescuento)).toFixed(2);
    var total = (parseFloat(totalfinal)+costoenvio).toFixed(2);
    
    $("#subtotal").val(totalfinal);
    $("#total").val(total);
    $("#total_redondeado").val((Math.round10(total, -1)).toFixed(2));
  
    carga_carritocompradetalle();  
}
function selectproductos(){
    var data = '';
    $("#tabla-contenido > tbody#tbody1 > tr").each(function() {
        var num = $(this).attr('id');        
        var idproducto = $(this).attr('idproducto');
        var productCant = $("#productCant"+num).val();
        var productUnidad = $("#productUnidad"+num).val();
        var nombreproducto = $(this).attr('nombreproducto');
        var productDetalle = $("#productDetalle"+num).val();
        var idestadodetalle = $(this).attr('idestadodetalle');
        data = data+'/&/'+idproducto+'/,/'+productCant+'/,/'+productUnidad+'/,/'+nombreproducto+'/,/'+(productDetalle!=undefined?productDetalle:'')+'/,/'+idestadodetalle;
    });
    return data;
}
@if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
function selectproductos_descuento(){
    var data = '';
    $("#tabla-contenido > tbody#tbody2 > tr").each(function() {
        var num = $(this).attr('id');        
        var idproducto = $(this).attr('idproducto');
        var total = $(this).attr('total');
        var montodescuento = $(this).attr('montodescuento');
        var totalpack = $(this).attr('totalpack');
        data = data+'/&/'+total+'/,/'+montodescuento+'/,/'+totalpack+'/,/'+idproducto;
    });
    return data;
} 
@endif
function eliminarproducto(num){
    $("#tabla-contenido > tbody#tbody1 > tr#"+num).remove();
    calcularmonto();
}
  
$("#montorecibido").keyup(function() {
      var total =  parseFloat($("#total_redondeado").val());
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