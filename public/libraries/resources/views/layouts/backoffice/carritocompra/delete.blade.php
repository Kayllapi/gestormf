@extends('layouts.backoffice.master')
@section('cuerpobackoffice')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Eliminar Pedido</span>
      <a class="btn btn-success" href="{{ url('backoffice/carritocompra') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/carritocompra/{{ $venta->id }}',
        method: 'DELETE',
        data:{
            view: 'eliminar'
        }
    },
    function(resultado){
        location.href = '{{ url('backoffice/carritocompra') }}';                                                                            
    },this)">
  
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
             <div class="col-md-6">
                <label>Facturación - Cliente</label>
                <input type="text" value="{{ $s_facturacion->cliente_identificacion }} - {{ $s_facturacion->cliente_nombre }}" disabled/>
                <label>Facturación - Dirección</label>
                <input type="text" value="{{ $s_facturacion->cliente_direccion }}" disabled/>
                <label>Facturación - Ubicación (Ubigeo)</label>
                <input type="text" value="{{ $s_facturacion->cliente_ubigeo }}" disabled/>
             </div>
             <div class="col-md-6">
                <label>Comprobante</label>
                <input type="text" value="{{ $venta->comprobantenombre }}" disabled/>
               <label>Estado de venta</label>
                <input type="text" value="{{ $venta->s_idestado==1?'En Proceso':'Pendiente' }}" disabled/>
                <label>Tipo de entrega</label>
                <input type="text" value="{{ $venta->tipoentreganombre }}" disabled/>
             </div>
          </div>
          <div class="table-responsive">
            <table class="table" id="tabla-contenido">
                <thead class="thead-dark">
                  <tr>
                    <th width="15%">Código</th>
                    <th>Producto</th>
                    <th width="60px">Cantidad</th>
                    <th width="110px">P. Unitario</th>
                    <th width="110px">Descuento</th>
                    <th width="110px">P. Total</th>
                  </tr>
                </thead>
                <tbody num="0"></tbody>
            </table>
          </div>
          <div class="row">
            <div class="col-md-3">
              <label>Sub Total</label>
              <input type="text" id="subtotal" placeholder="0.00" disabled>
            </div>   
            <div class="col-md-3">
              <label>Descuento</label>
              <input type="text" id="descuento" placeholder="0.00" disabled>
            </div>   
            <div id="cont-costoenvio" <?php echo $venta->s_idtipoentrega==2?'style="display:block;"':'style="display:none;"'?>>
            <div class="col-md-3">
              <label>Envio</label>
              <input type="number" value="{{ $venta->envio }}" id="costoenvio" step="0.01" min="0" disabled/>
            </div>   
            </div> 
            <div class="col-md-3">
              <label>Total</label>
              <input type="text" id="total" placeholder="0.00" disabled>
            </div>      
          </div> 

          <div class="row">
            <div class="col-md-6">
              <div id="cont-tipo-delivery-info" <?php echo $venta->s_idtipoentrega==2?'style="display:block;"':'style="display:none;"'?>>
              <label>Entrega de pedido</label>
              <select id="idestadoenvio" disabled>
                  <option></option>
                  <option value="1">Enviar ahora</option>
                  <option value="2">Enviar despues</option>
              </select>
              <div id="cont-estadoenvio" style="display:none;">
              <label>Fecha y Hora de entrega</label>
              <div class="row">
                  <div class="col-md-6">
                      <input type="date" value="{{ $s_ventadelivery!=''?$s_ventadelivery->fecha:'' }}" id="delivery_fecha" disabled>
                  </div>
                  <div class="col-md-6">
                      <input type="time" value="{{ $s_ventadelivery!=''?$s_ventadelivery->hora:'' }}" id="delivery_hora" disabled>
                  </div>
              </div>
              </div>
              <label>Nombre de persona a entregar</label>
              <input type="text" value="{{ $s_ventadelivery!=''?$s_ventadelivery->nombre:'' }}" id="delivery_pernonanombre" disabled>
              <label>Número de celular de entrega</label>
              <input type="text" value="{{ $s_ventadelivery!=''?$s_ventadelivery->telefono:'' }}" id="delivery_numerocelular" disabled>
              <label>Dirección de entrega</label>
              <input type="text" value="{{ $s_ventadelivery!=''?$s_ventadelivery->direccion:'' }}" id="delivery_direccion" disabled>
              </div>
            </div>
            <div id="cont-tipo-delivery-mapa" <?php echo $venta->s_idtipoentrega==2?'style="display:block;"':'style="display:none;"'?>>
            <div class="col-md-6">
              <label>Ubicación de entrega (Referencia)</label>
              <div id="singleMap"></div>
              <input type="hidden" value="{{ $s_ventadelivery!=''?$s_ventadelivery->mapa_ubicacion_lat:'' }}" id="mapa_ubicacion_lat"/>
              <input type="hidden" value="{{ $s_ventadelivery!=''?$s_ventadelivery->mapa_ubicacion_lng:'' }}" id="mapa_ubicacion_lng"/>
            </div>
            </div>
          </div>
        </div>
    </div>
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¿Esta seguro de eliminar este Pedido de venta?
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
            <button type="submit" class="btn  big-btn  color-bg flat-btn btn-danger"><i class="fa fa-trash"></i> Eliminar</button>
        </div>
    </div> 
</form>    
<style>
#singleMap {
    height: 272px;
}
</style>
@endsection
@section('scriptsbackoffice')
<script>
@foreach($s_ventadetalles as $value)
agregarproducto('{{ $value->s_idproducto }}','{{ $value->productocodigo }}','{{ $value->productonombre }}','{{ $value->cantidad }}','{{ $value->preciounitario }}','{{ $value->descuento }}');
@endforeach
function agregarproducto(idproducto,codigo,nombre,cantidad,precioalpublico,descuento){
      $("#codigoproducto").val('');
      $("#idproducto").html('');
      var num = $("#tabla-contenido tbody").attr('num');
      var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'" style="background-color: #0ec529;color: #fff;">';
          nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+='<td>'+nombre+'</td>';
          nuevaFila+='<td class="mx-td-input"><input id="productCant'+num+'" type="number" value="'+cantidad+'" disabled></td>';
          nuevaFila+='<td class="mx-td-input"><input id="productUnidad'+num+'" type="number" value="'+precioalpublico+'" disabled></td>';
          nuevaFila+='<td class="mx-td-input"><input id="productDescuento'+num+'" type="number" value="'+descuento+'" disabled></td>';
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
        var productDescuento = parseFloat($("#productDescuento"+num).val());
        var subtotal = ((productCant*productUnidad)-productDescuento).toFixed(2);
        $("#productTotal"+num).val(parseFloat(subtotal).toFixed(2));
        total = total+parseFloat((productCant*productUnidad).toFixed(2));
        descuento = descuento+parseFloat(productDescuento);
    });
    var costoenvio = parseFloat($("#costoenvio").val());
    if($("#costoenvio").val()==''){
        costoenvio = 0;
    }
    $("#subtotal").val((parseFloat(total)).toFixed(2));
    $("#descuento").val((parseFloat(descuento)).toFixed(2));
    totalfinal = (parseFloat(total)).toFixed(2) - (parseFloat(descuento)).toFixed(2);
    $("#total").val((parseFloat(totalfinal)+costoenvio).toFixed(2));  
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
@if($venta->s_idtipoentrega==2)
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
}).val({{ $s_ventadelivery!=''?$s_ventadelivery->s_idestadoenvio:'0' }}).trigger("change");
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAyFtw-yEfh6GtiPyx_4ZQWt3g_vUCu5eQ"></script>
<script>
    function singleMap() {
        var myLatLng = {
            lat: {{ $s_ventadelivery!=''?$s_ventadelivery->mapa_ubicacion_lat:'' }},
            lng: {{ $s_ventadelivery!=''?$s_ventadelivery->mapa_ubicacion_lng:'' }},
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
            map: single_map,
            icon: markerIcon2,
            title: 'Your location'
        });
    }
    var single_map = document.getElementById('singleMap');
    if (typeof (single_map) != 'undefined' && single_map != null) {
        google.maps.event.addDomListener(window, 'load', singleMap);
    } 
</script>
@endif
@endsection