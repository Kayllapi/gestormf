<div class="mx-modal-cuerpo">
    <div id="contenido-dato-carritocompra">
        <form class="js-validation-signin px-30" 
              action="javascript:;" 
              onsubmit="callback({
                  route: '0/tienda',
                  method: 'POST',
                  data:{
                      view: 'registrar',
                      //productos: selectproductos()
                  }
              },
              function(resultado){
                  //location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta') }}';  
                  Cookies.remove('cookaddproducto');  
                  $('#cantidad-pedido-carritocompra').html('0');
                  $('#tabla-tiendaproducto > tbody').html('');
                  $('#contenido-dato-carritocompra').css('display','none');
                  confirm({
                    input:'#contenido-dato-carritocompra-resultado',
                    resultado:'CORRECTO',
                    mensaje:'Se ha enviado tu pedido correctamente.',
                    cerrarmodal:'.modal-carritocompra'
                  });
              },this)"> 
          <div class="tabs-container" id="tab-carritocompra">
              <ul class="tabs-menu">
                  <li class="current"><a href="#tab-carritocompra-0" id="tab-pedido">Pedido</a></li>
                  <li><a href="#tab-carritocompra-1" id="tab-entrega">Entrega</a></li>
                  <li><a href="#tab-carritocompra-2" id="tab-facturacion">Facturación</a></li>
                  <li><a href="#tab-carritocompra-3" id="tab-pago">Pago</a></li>
              </ul>
              <div class="tab">
                  <div id="tab-carritocompra-0" class="tab-content" style="display: block;">
                      <div class="mensaje-info">
                        <i class="fa fa-exclamation-circle"></i> Puede solicitar productos de diferentes tiendas en un mismo carrito de compra.
                      </div>
                      <div class="custom-form">
                      <table class="table" id="tabla-tiendaproducto">
                          <thead class="thead-dark">
                              <tr>
                                  <th>Producto / Detalle</th>
                              </tr>
                          </thead>
                          <tbody num="0"></tbody>
                      </table>
                          <label>Total a Pagar</label>
                          <input type="text" value="0.00" id="carrtiocompra_totalapagar" readonly />
                      <a href="javascript:;" onclick="$('#tab-entrega').click()" class="log-submit-btn"><span>Empezar el pedido</span> <i class="fa fa-angle-right"></i></a>
                      </div>
                  </div>
                  @if(Auth::user()) 
                  <?php
                    $s_usuariofacturacion = DB::table('s_usuariofacturacion')
                        ->leftJoin('ubigeo','ubigeo.codigo','=','s_usuariofacturacion.facturacion_ubigeocodigo')
                        ->where('s_usuariofacturacion.idusers',Auth::user()->id)
                        ->select(
                            's_usuariofacturacion.*',
                            'ubigeo.id as idubigeo',
                            'ubigeo.nombre as ubigeonombre'
                        )
                        ->orderBy('s_usuariofacturacion.id','desc')
                        ->limit(1)
                        ->first();
                    $users = DB::table('users')
                        ->leftJoin('ubigeo','ubigeo.id','=','users.idubigeo')
                        ->where('users.id',Auth::user()->id)
                        ->select(
                            'users.*',
                            'ubigeo.nombre as ubigeonombre'
                        )
                        ->first();
                  ?>
                  @if(Auth::user()->idtienda==0) 
                  <div id="tab-carritocompra-1" class="tab-content" style="display: none;">
                      <div class="mensaje-info">
                        <i class="fa fa-exclamation-circle"></i> Estos datos son unicamente para la entrega de pedido.
                      </div>
                      <div class="custom-form">
                          <label>Entrega de pedido *</label>
                          <select id="delivery_idestadoenvio">
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
                          <input type="text" value="{{ $s_usuariofacturacion!=''?$s_usuariofacturacion->facturacion_nombre:$users->nombre }}" id="delivery_personanombre">
                          <label>Número de celular de entrega *</label>
                          <input type="text" value="{{ $users->numerotelefono }}"id="delivery_numerocelular">
                          <label>Dirección de entrega *</label>
                          <input type="text" value="{{ $s_usuariofacturacion!=''?$s_usuariofacturacion->facturacion_direccion:$users->direccion }}" id="delivery_direccion">
                          <label>Ubicación de entrega (Arrastre el Marcador) *</label>
                          <div id="carritocompra_singleMap"></div>
                          <input type="hidden" id="delivery_mapa_ubicacion_lat"/>
                          <input type="hidden" id="delivery_mapa_ubicacion_lng"/>
                          <a href="javascript:;" onclick="$('#tab-pedido').click()" class="log-submit-btn" style="margin-right: 10px;"><i class="fa fa-angle-left"></i> <span>Atras</span></a> 
                          <a href="javascript:;" onclick="$('#tab-facturacion').click()" class="log-submit-btn"><span>Siguiente</span> <i class="fa fa-angle-right"></i></a>
                      </div>
                  </div>
                  <div id="tab-carritocompra-2" class="tab-content" style="display: none;">
                      <div class="mensaje-info">
                        <i class="fa fa-exclamation-circle"></i> Rellene correctamente su información, para poder emitir su comprobante.
                      </div>
                      <div class="custom-form">
                          <label>DNI/RUC *
                                  <div class="filter-tags" id="cont-check-cliente" style="float:right;">
                                  <input id="check-cliente" value="1" type="checkbox" style="margin-bottom: 0px;"><label for="check-cliente">Guardar</label>
                                  </div>  
                          </label>
                          <input type="text" value="{{ $s_usuariofacturacion!=''?$s_usuariofacturacion->facturacion_identificacion:$users->identificacion }}" id="delivery_facturacionidentificacion"/>
                          <label>Cliente *</label>
                          <input type="text" value="{{ $s_usuariofacturacion!=''?$s_usuariofacturacion->facturacion_nombre:$users->nombre.' '.$users->apellidos }}" id="delivery_facturacioncliente"/>
                          <label>Dirección *</label>
                          <input type="text" value="{{ $s_usuariofacturacion!=''?$s_usuariofacturacion->facturacion_direccion:$users->direccion }}" id="delivery_facturaciondireccion"/>
                          <label>Departamento/Provincia/Distrito *</label>
                          <select id="delivery_facturacionidubigeo">
                              <option value="{{ $s_usuariofacturacion!=''?$s_usuariofacturacion->idubigeo:$users->idubigeo }}">
                                {{ $s_usuariofacturacion!=''?$s_usuariofacturacion->facturacion_ubigeo:$users->ubigeonombre }}
                              </option>
                          </select>
                          <a href="javascript:;" onclick="$('#tab-entrega').click()" class="log-submit-btn" style="margin-right: 10px;"><i class="fa fa-angle-left"></i> <span>Atras</span></a> 
                          <a href="javascript:;" onclick="$('#tab-pago').click()" class="log-submit-btn"><span>Siguiente</span> <i class="fa fa-angle-right"></i></a>
                      </div>
                  </div>
                  <div id="tab-carritocompra-3" class="tab-content" style="display: none;">
                      <div class="custom-form">
                          <label>Detalle de Pedido</label>
                          <pre style="background-color: #e0dede;
                              float: left;
                              width: 100%;
                              text-align: left;
                              border-radius: 5px;
                              padding: 10px;
                              margin-bottom: 10px;
                              padding-top: 5px;
                              padding-bottom: 5px;" id="cont-detallepedido">
                          </pre>
                          <label>Método de pago *</label>
                          <select id="delivery_idmetodopago">
                              <option></option>
                              <option value="1">Pagar con Visa o Mastercard</option>
                              <option value="2">Pagar cuando reciba el producto</option>
                          </select>
                          <div id="cont-metodopago-1" style="display:none;">
                              <div class="mensaje-danger">
                                <i class="fa fa-exclamation-circle"></i> Aun no ha configurado este método de pago la tienda, contactese con algún encargado.
                              </div>
                              <!--button type="button" id="pagaconculqi" style="float: none;background-color: #2ecc71;" class="price-link"> Pargar con CULQI</button-->
                          </div>
                          <div id="cont-metodopago-2" style="display:none;">
                              <div class="mensaje-success">
                                Esta a un paso de solicitar su pedido, no espere más solicite.
                              </div>
                              <button type="submit" id="pagacontraentrega" style="float: none;background-color: #343a40;" class="price-link"> Solicitar Pedido</button>
                          </div>
                          <a href="javascript:;" onclick="$('#tab-facturacion').click()" class="log-submit-btn"><i class="fa fa-angle-left"></i> <span>Atras</span></a>
                      </div>
                  </div>
                  @else
                  <div id="tab-carritocompra-1" class="tab-content" style="display: none;">
                      <div class="mensaje-info">
                        <i class="fa fa-exclamation-circle"></i> Para continuar el pedido, tienes que acceder con una cuenta (BackOffice).
                      </div>
                      <div style="width: 168px;overflow: hidden;margin: auto;">
                        <a href="javascript:;" onclick="document.getElementById('logout-form-sistema').submit()" class="add-list" style="top: 0px;background-color: #31353d;"><span><i class="fa fa-power-off"></i></span> Cerrar Sesión</a>
                      </div>
                  </div>
                  <div id="tab-carritocompra-2" class="tab-content" style="display: none;">
                      <div class="mensaje-info">
                        <i class="fa fa-exclamation-circle"></i> Para continuar el pedido, tienes que acceder con una cuenta (BackOffice).
                      </div>
                      <div style="width: 168px;overflow: hidden;margin: auto;">
                        <a href="javascript:;" onclick="document.getElementById('logout-form-sistema').submit()" class="add-list" style="top: 0px;background-color: #31353d;"><span><i class="fa fa-power-off"></i></span> Cerrar Sesión</a>
                      </div>
                  </div>
                  <div id="tab-carritocompra-3" class="tab-content" style="display: none;">
                      <div class="mensaje-info">
                        <i class="fa fa-exclamation-circle"></i> Para continuar el pedido, tienes que acceder con una cuenta (BackOffice).
                      </div>
                      <div style="width: 168px;overflow: hidden;margin: auto;">
                        <a href="javascript:;" onclick="document.getElementById('logout-form-sistema').submit()" class="add-list" style="top: 0px;background-color: #31353d;"><span><i class="fa fa-power-off"></i></span> Cerrar Sesión</a>
                      </div>
                  </div>
                  @endif
                  @else
                  <div id="tab-carritocompra-1" class="tab-content" style="display: none;">
                      <div class="mensaje-info">
                        <i class="fa fa-exclamation-circle"></i> Para continuar el pedido, tienes que acceder con una cuenta (BackOffice).
                      </div>
                      <div style="width: 146px;overflow: hidden;margin: auto;">
                        <a href="javascript:;" class="add-list" id="modal-iniciarsesion-master" style="top: 0px;"><span><i class="fa fa-sign-in"></i></span> BackOffice</a>
                      </div>
                  </div>
                  <div id="tab-carritocompra-2" class="tab-content" style="display: none;">
                      <div class="mensaje-info">
                        <i class="fa fa-exclamation-circle"></i> Para continuar el pedido, tienes que acceder con una cuenta (BackOffice).
                      </div>
                      <div style="width: 146px;overflow: hidden;margin: auto;">
                        <a href="javascript:;" class="add-list" id="modal-iniciarsesion-master" style="top: 0px;"><span><i class="fa fa-sign-in"></i></span> BackOffice</a>
                      </div>
                  </div>
                  <div id="tab-carritocompra-3" class="tab-content" style="display: none;">
                      <div class="mensaje-info">
                        <i class="fa fa-exclamation-circle"></i> Para continuar el pedido, tienes que acceder con una cuenta (BackOffice).
                      </div>
                      <div style="width: 146px;overflow: hidden;margin: auto;">
                        <a href="javascript:;" class="add-list" id="modal-iniciarsesion-master" style="top: 0px;"><span><i class="fa fa-sign-in"></i></span> BackOffice</a>
                      </div>
                  </div>
                  @endif
              </div>
          </div>
        </form>
    </div>
    <div id="contenido-dato-carritocompra-resultado" style="float: left;width: 100%;"></div>           
</div>
@if(Auth::user()) 
<form method="POST" id="logout-form-sistema" action="{{ route('logout') }}">
  @csrf 
  <input type="hidden" value="0" name="logoutidtienda">
  <input type="hidden" value="" name="logoutlink">
</form>
@endif
<style>
#carritocompra_singleMap {
    width: 100%;
    height: 250px;
    border-radius: 5px;
    border:1px solid #aaa;
    margin-bottom:10px;
}</style>
<script>
modal({click:'#modal-iniciarsesion-master'});
tab({click:'#tab-carritocompra'});
// carrito de compra
carga_carritocompra();
function carga_carritocompra(){
    $('#contenido-dato-carritocompra').css('display','block');
    $('#contenido-dato-carritocompra-resultado').html('');
    if(Cookies.get('cookaddproducto')!=undefined){
        var cookaddproducto = JSON.parse(Cookies.get('cookaddproducto'));
        //ordenar por tienda
        cookaddproducto.sort(function (a, b) {
            if (a.producto_nombre > b.producto_nombre) {
              return 1;
            }
            if (a.producto_nombre < b.producto_nombre) {
              return -1;
            }
            return 0;
        });
        cookaddproducto.sort(function (a, b) {
            if (a.tienda_nombre > b.tienda_nombre) {
              return 1;
            }
            if (a.tienda_nombre < b.tienda_nombre) {
              return -1;
            }
            return 0;
        });
        // fin ordenar por tienda
        $.each(cookaddproducto, function( i, val ) {
            /*// quitar repetidos
            if(myArr.includes(val.tienda_nombre)==false){
                myArr.push(val.tienda_nombre);
            }else{
                tienda_nom = ''; 
            }
            // fin quitar repetidos*/
            if(val!=null){
                addproducto(
                    val.idproducto,
                    val.idtienda,
                    val.tienda_link,
                    val.tienda_nombre,
                    val.producto_nombre,
                    val.producto_preciopormayor,
                    val.producto_precioalpublico,
                    val.producto_cantidad
                );
            }    
        });
        guardar_carritocompra();
    }  
}
function guardar_carritocompra(){
    @if(Auth::user()) 
    @if(Auth::user()->idtienda==0) 
    var cookaddproducto = JSON.parse(Cookies.get('cookaddproducto'));
    $.ajax({
        url:"{{url('0/tienda/showcarritocompra')}}",
        type:'GET',
        data: {
            productos : cookaddproducto
        },
        success: function (respuesta){
            $('#cont-detallepedido').html(respuesta);
            $("#delivery_idmetodopago1").select2({
                placeholder: "---  Seleccionar ---",
                minimumResultsForSearch: -1
            }).on("change", function(e) {
                $('#cont-metodopago-2').css('display','none');
                $('#cont-metodopago-1').css('display','none');
                if(e.currentTarget.value == 1) {
                    $('#cont-metodopago-1').css('display','block');
                }else if(e.currentTarget.value == 2) {
                    $('#cont-metodopago-2').css('display','block');
                }
            }).val(2).trigger("change");
        }
    })
    @endif
    @endif
}
// fin carrito de compra
function addproducto(idproducto,idtienda,tienda_link,tienda_nombre,producto_nombre,producto_preciopormayor,producto_precioalpublico,producto_cantidad){
    $('#contenido-producto').html('<div class="mx-alert-load"><img src="{{ url('/public/libraries/app/img/loading.gif') }}"></div>');  
    var num = $("#tabla-tiendaproducto > tbody").attr('num');
    $('#tabla-tiendaproducto > tbody')
        .append('<tr id="'+num+'" idproducto="'+idproducto+'" producto_nombre="'+producto_nombre+'" idtienda="'+idtienda+'" tienda_link="'+tienda_link+'" tienda_nombre="'+tienda_nombre+'">'+
            '<td>'+
              '<table>'+
                  '<tr>'+
                    '<td style="border:0px" colspan="4">'+
                      '<div style="padding: 3px 0px;"><i class="fa fa-angle-right"></i> <b>Tienda: </b><a href="'+tienda_link+'" target="_blank" style="font-weight: bold;color: #0a40d8;">'+tienda_nombre+'</a></div>'+
                      '<div style="padding: 3px 0px;"><i class="fa fa-angle-right"></i> <b>Producto: </b><a href="javascript:;" id="modal-tiendaproducto" onclick="selectproducto('+idproducto+')" style="font-weight: bold;color: #0a40d8;">'+producto_nombre+'</a></div>'+
                      '<div style="padding: 3px 0px;"><i class="fa fa-angle-right"></i> <b>Precio: </b>'+producto_precioalpublico+'</div>'+
                      '<!--div style="padding: 3px 0px;"><i class="fa fa-angle-right"></i> <b>Precio con Oferta: </b>'+producto_preciopormayor+'</div-->'+
                    '</td>'+
                  '</tr>'+
                  '<tr>'+
                    '<td style="border:0px"><input type="number" value="'+producto_cantidad+'" id="producto_cantidad'+num+'" onkeyup="calcularmonto();guardar_carritocompra();" onclick="calcularmonto();guardar_carritocompra();" style="padding: 12px;"></td>'+
                    '<td style="border:0px"><input type="text" value="'+producto_precioalpublico+'" id="producto_precioalpublico'+num+'" style="padding: 12px;" disabled></td>'+
                    '<td style="border:0px"><input type="text" value="0.00" id="producto_total'+num+'" style="padding: 12px;" disabled></td>'+
                    '<td style="border:0px">'+
                    '<a href="javascript:;" onclick="eliminaraddproducto('+num+','+idproducto+')" class="btn btn-danger" style="padding: 12.5px 15px;"><i class="fa fa-close"></i></a>'+
                    '</td>'+
                  '</tr>'+
              '</table>'+
            '</td>'+
        '</tr>');
    modal({click:'#modal-tiendaproducto'});
    $("#tabla-tiendaproducto > tbody").attr('num',parseInt(num)+1);
    calcularmonto();

    /*confirm({
      input:'#contenido-producto',
      resultado:'CORRECTO',
      mensaje:'Se ha agreado correctamente.',
      cerrarmodal:'.modal-tiendaproducto'
    });*/
}
/*function cerraraddproducto(){
    $('.modal-tiendaproducto .close-reg').click();
}*/
function eliminaraddproducto(num,idproducto){
    $("#tabla-tiendaproducto > tbody > tr#"+num).remove();
    calcularmonto();
    if(Cookies.get('cookaddproducto')!=undefined){
        var cant_cookaddproducto = JSON.parse(Cookies.get('cookaddproducto'));
        $('#cantidad-pedido-carritocompra').html(cant_cookaddproducto.length);
        guardar_carritocompra();
    }
}
function calcularmonto(){
    var total = 0;
    $("#tabla-tiendaproducto > tbody > tr").each(function() {
        var num = $(this).attr('id');        
        var producto_cantidad = parseFloat($("#producto_cantidad"+num).val());
        var producto_precioalpublico = parseFloat($("#producto_precioalpublico"+num).val());
        var subtotal = (producto_cantidad*producto_precioalpublico).toFixed(2);
        $("#producto_total"+num).val(parseFloat(subtotal).toFixed(2));
        total = total+parseFloat(subtotal);
    });
    $("#carrtiocompra_totalapagar").val((parseFloat(total)).toFixed(2)); 
    actualizar_cookies_carritocompra();
}
function actualizar_cookies_carritocompra(){
        var cookaddproducto = [];
        $("#tabla-tiendaproducto > tbody > tr").each(function() {
            var num = $(this).attr('id');        
            var idproducto = $(this).attr('idproducto');
            var idtienda = $(this).attr('idtienda');
            var tienda_link = $(this).attr('tienda_link');
            var tienda_nombre = $(this).attr('tienda_nombre');
            var producto_nombre = $(this).attr('producto_nombre');
            var producto_cantidad = $("#producto_cantidad"+num).val();
            var producto_precioalpublico = $("#producto_precioalpublico"+num).val();
            cookaddproducto.push({
                idproducto: idproducto,
                idtienda: idtienda,
                tienda_link: tienda_link,
                tienda_nombre: tienda_nombre,
                producto_nombre: producto_nombre,
                producto_preciopormayor: '0.00',
                producto_precioalpublico: producto_precioalpublico,
                producto_cantidad: producto_cantidad
            });
        });
        Cookies.set('cookaddproducto', cookaddproducto, {expires: 30});
        // actualziar bd
}
</script>
@if(Auth::user()) 
<script>
$("#delivery_idestadoenvio").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-estadoenvio').css('display','none');
    if(e.currentTarget.value == 1) {
    }else if(e.currentTarget.value == 2) {
        $('#cont-estadoenvio').css('display','block');
    }
}).val(1).trigger("change");
/*$("#delivery_idtipocomprobante").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-juridica').css('display','none');
    $('#cont-natural').css('display','none');
    if(e.currentTarget.value == 2) {
        $('#cont-natural').css('display','block');
        $("#delivery_idtipopersona").select2({
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
    }else if(e.currentTarget.value == 3) {
        $('#cont-juridica').css('display','block');
        $("#delivery_idtipopersona").select2({
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
        }).val(2).trigger("change"); 
    }
}).val(2).trigger("change");*/
<?php
$idtipopersona = 1;
if(strlen($s_usuariofacturacion!=''?$s_usuariofacturacion->facturacion_identificacion:$users->identificacion)==11){
    $idtipopersona = 2;
}
?>
$("#delivery_facturacionidubigeo").select2({
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
$("#delivery_idmetodopago").select2({
    placeholder: "---  Seleccionar ---",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-metodopago-2').css('display','none');
    $('#cont-metodopago-1').css('display','none');
    if(e.currentTarget.value == 1) {
        $('#cont-metodopago-1').css('display','block');
    }else if(e.currentTarget.value == 2) {
        $('#cont-metodopago-2').css('display','block');
    }
}).val(2).trigger("change");
</script>
<script>
// CULQI
Culqi.publicKey = 'pk_live_FKfkgTUBL9ln7nMG';
Culqi.settings({
    title: 'KAYLLAPI PERÚ',
    currency: 'PEN',
    description: 'El mejor sitio para comprar y vender. Compra Segura y Fácil.',
    amount: (parseFloat(0)*100)
});
$('#pagaconculqi').on('click', function(e) {
    Culqi.open();
    e.preventDefault();
});
function culqi() {
    if (Culqi.token) {
        var token = Culqi.token.id;
        callback({
            route: 'backoffice/inicio',
            method: 'POST',
            data:{
                view : 'pagoculqi',
                token : token,
                email_patrocinador : $('#email_patrocinador').val(),
                idplan : $('#idplan').val()
            }
        },
        function(resultado){
            if (resultado.resultado == 'CORRECTO') {
                location.href = '{{ Request::fullUrl() }}';      
            }
        });
    } else { // ¡Hubo algún problema!
        alert(Culqi.error.user_message);
    }
};
// FIN CULQI
</script>
<script>
carritocompra_singleMap();
function carritocompra_singleMap() {
    var infoWindow = new google.maps.InfoWindow;
    var directionsRenderer = new google.maps.DirectionsRenderer({suppressMarkers : true});
    var directionsService = new google.maps.DirectionsService;
    var imagemarker = '{{ url('public/backoffice/sistema/marker.png') }}';
    var coordenada_tienda = {
        lat: {{ $tienda->mapa_ubicacion_lat!=''?$tienda->mapa_ubicacion_lat:'-12.071871667822409' }},
        lng: {{ $tienda->mapa_ubicacion_lng!=''?$tienda->mapa_ubicacion_lng:'-75.21026847919165' }},
    };
    var map = new google.maps.Map(document.getElementById('carritocompra_singleMap'), {
        zoom: 16,
        center: coordenada_tienda
    });
    var marker = new google.maps.Marker({
        position: coordenada_tienda,
        draggable: true,
        map: map,
        icon: imagemarker
    });
    google.maps.event.addListener(marker, 'dragend', function (event) {
        $('#delivery_mapa_ubicacion_lat').val(event.latLng.lat());
        $('#delivery_mapa_ubicacion_lng').val(event.latLng.lng());
    });		
}
</script>
@endif