@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Pedido Mesa N° '.$numero_mesa,
    'botones'=>[
        'atras:/'.$tienda->id.'/comidaordenpedido/:Ir Atras'
    ]
])
<form action="javascript:;" 
      onsubmit="callback({
                            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/agencia',
                            method: 'POST',
                            data: {
                              view: 'registrar'
                            }
                          },
                          function(resultado){
                            pagina({route:'backoffice/tienda/sistema/{{$tienda->id}}/agencia',result:'#mx-subcuerpo'});
                          },this)"> 
  <div class="row">
    <div class="col-md-4">
      <div id="cont-load-1"></div>
      <div id="cont-general">
        <div id="cont-button-atras" style="display: none;">
          <a class="btn btn-dark" href="javascript:;" onclick="index_categoria()" style="margin-bottom: 10px;"><i class="fa fa-angle-left"></i> Regresar</a></a>
        </div>
        <div id="cont-item" class="container-movil">
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <!-- <div id="cont-load-2"></div> -->
      <div id="cont-venta-general">
        <div class="tabs-container" id="tab-carritocompra" style="display: none;">
              <ul class="tabs-menu">
                  <li class="current"><a href="#tab-carritocompra-0" id="tab-pedido">Pedido</a></li>
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
                                @if($configuracion['estadodescuento']==1)
                                <th width="1px"></th>
                                @endif
                                @if($configuracion['estadostock']==1)
                                <th width="50px">Stock</th>
                                @endif
                                <th width="60px">Cantidad</th>
                                <th width="110px">P. Unitario</th>
                                <th width="110px">P. Total</th> 
                                <th width="10px"></th>
                              </tr>
                            </thead>
                            <tbody num="0" id="tbody1"></tbody>
                            <tbody num="0" id="tbodycarga"></tbody>
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
                          <input type="text" id="subtotal" value="0.00" style="font-size: 30px;
                                  font-weight: bold;
                                  padding-top: 5px;
                                  padding-bottom: 5px;
                                  text-align: center;" disabled>
                        </div>    
                      </div> 
                      <div class="custom-form">
                      <a href="javascript:;" onclick="$('#tab-facturacion').click()" class="log-submit-btn" style="margin-right: 10px;"><span>Siguiente</span> <i class="fa fa-angle-right"></i></a>
                      <a href="javascript:;" onclick="realizarpago()" class="log-submit-btn mx-realizar-pago"><span>Realizar el Pago</span> <i class="fa fa-angle-right"></i></a>
                      
                      </div>
                  </div>
                  <div id="tab-carritocompra-2" class="tab-content" style="display: none;">
                      <div class="mensaje-info">
                        <i class="fa fa-exclamation-circle"></i> Rellene correctamente su información, para poder emitir su comprobante.
                      </div>
                      <div class="row">
                         <div class="col-md-6">
                            <label>Facturación - Cliente *</label>
                            @if($configuracion_facturacion['idclientepordefecto']!=null)
                                <div class="row">
                                   <div class="col-md-9">
                                      <select id="idcliente">
                                          <option value="{{ $configuracion_facturacion['idclientepordefecto'] }}">{{ $configuracion_facturacion['clientepordefecto'] }}</option>
                                      </select>
                                   </div>
                                   <div class="col-md-3">
                                      <a href="javascript:;" id="modal-registrarcliente" class="btn btn-warning"><i class="fa fa-plus"></i> Agregar</a>
                                   </div>
                                </div>
                                <label>Facturación - Dirección</label>
                                <input type="text" id="direccion" value="{{$configuracion_facturacion['clientedireccionpordefecto']}}"/>
                                <label>Facturación - Ubicación (Ubigeo)</label>
                                <select id="idubigeo">
                                    <option value="{{ $configuracion_facturacion['clienteidubigeopordefecto'] }}">{{ $configuracion_facturacion['clienteubigeopordefecto'] }}</option>
                                </select>
                            @else
                                <div class="row">
                                   <div class="col-md-9">
                                      <select id="idcliente">
                                          <option></option>
                                      </select>
                                   </div>
                                   <div class="col-md-3">
                                      <a href="javascript:;" id="modal-registrarcliente" class="btn btn-warning"><i class="fa fa-plus"></i> Agregar</a>
                                   </div>
                                </div>
                                <label>Facturación - Dirección</label>
                                <input type="text" id="direccion">
                                <label>Facturación - Ubicación (Ubigeo)</label>
                                <select id="idubigeo">
                                    <option></option>
                                </select>
                            @endif
                         </div>
                         <div class="col-md-6">
                            <label>Moneda *</label>
                            <select id="idmoneda">
                                <option></option>
                                @foreach($monedas as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                           <label>Empresa *</label>
                            <select id="idagencia">
                                <option></option>
                                @foreach($agencia as $value)
                                <option value="{{ $value->id }}"?>{{ $value->ruc }} - {{ $value->nombrecomercial }}</option>
                                @endforeach
                            </select>
                            <?php $comprobante = DB::table('s_tipocomprobante')->get(); ?>
                            <label>Comprobante *</label>
                            <select id="idcomprobante">
                                <option></option>
                                @foreach($comprobante as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                         </div>
                       </div>
                       <a href="javascript:;" onclick="$('#tab-pedido').click()" class="log-submit-btn" style="margin-right: 10px;"><i class="fa fa-angle-left"></i> <span>Atras</span></a> 
                       <a href="javascript:;" onclick="realizarpago()" class="log-submit-btn" style="margin-right: 10px;"><span>Siguiente</span> <i class="fa fa-angle-right"></i></a>
                       <a href="javascript:;" onclick="realizarpago()" class="log-submit-btn mx-realizar-pago"><span>Realizar el Pago</span> <i class="fa fa-angle-right"></i></a>
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
                                padding-bottom: 5px;    
                                        overflow-x: auto;" id="cont-detallepedido">
                            </pre>
                        </div> 
                        <div class="col-md-4">
                          <label>Tipo de Pago </label>
                          <select id="idtipopago">
                                <option></option>
                                @foreach($tipopago as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                           <div class="col-sm-12"  id="div-usuario_saldo" style="display:none;">
                             <label>Cliente *</label>
                                  <select id="idusuariosaldo">
                                      <option></option>
                                  </select>
                            <label>Saldo de Cliente</label>
                              <input type="text" id="monto" disabled>
                             <div style="display:none;">
                               <label>Monto Restante *</label>
                                 <input type="text" id="montorestante">
                             </div>
                           </div>
                        </div>
                        
                        <div class="col-md-4">
                          <div id="cont-costoenvio" style="display:none;">
                              <label>Costo de Envio *</label>
                              <input type="number" id="costoenvio" style="text-align: center;font-size: 16px;" step="0.01" onkeyup="calcularmonto()" onclick="calcularmonto()"/>
                              
                          </div>   
                          <div id="cont-montorecibido" <?php echo ($configuracion['estadoventa']==1 or $configuracion['estadoventa']==2)? 'style="display:none;"':'' ?>>
                              <label>Total </label>
                              <input type="text" id="total" value="0.00" style="font-size: 30px;
                                  font-weight: bold;
                                  padding-top: 5px;
                                  padding-bottom: 5px;
                                  text-align: center;" disabled>
                              <label>Total Redondeado</label>
                              <input type="text" id="total_redondeado" value="0.00" style="font-size: 30px;
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
                          @if($configuracion['estadoventa']==1) 
                            Registrar Pedido
                          @elseif($configuracion['estadoventa']==2) 
                            Confirmar Pedido
                          @elseif($configuracion['estadoventa']==3) 
                            Realizar Venta
                          @endif      
                      </a>
                  </div>
              </div>
          </div>
        <div id="tab-load-carritocompra"></div>
      </div>
    </div>
  </div>
  <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>
<style>
  .container-movil {
    height: 500px;
    overflow: scroll;
  }
</style>
@endsection
@section('subscripts')
<script>
  tab({click:'#tab-carritocompra'});
</script>
<script>
  index_categoria();
  function index_categoria() {
    $('#cont-button-atras').css('display', 'none');
    $('#cont-item').html('');
    load('#cont-load-1');
    
    $.ajax({
      url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comidaordenpedido/show-categoria') }}",
      type: 'GET',
      data: {},
      success: function (res) {
        $('#cont-load-1').html('');
        $('#cont-item').html(res['html']);
      }
    });
  }
  function index_producto(idcategoria) {
    $('#cont-button-atras, #tab-carritocompra').css('display', 'none');
    $('#cont-item').html('');
    load('#cont-load-1');
    
    $.ajax({
      url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comidaordenpedido/show-producto') }}",
      type: 'GET',
      data: {
        idcategoria: idcategoria
      },
      success: function (res) {
        $('#cont-load-1').html('');
        $('#cont-button-atras, #tab-carritocompra').css('display', 'block');
        $('#cont-item').html(res['html']);
      }
    });
  }
  
  // Agregando Productos
  function select_producto(idproducto) {
    $.ajax({
      url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comidaordenpedido/show-seleccionar-producto') }}",
      type: 'GET',
      data: {
        idproducto : idproducto
      },
      beforeSend: function (data) {
          var nuevaFila='<tr style="background-color: #008cea;color: #fff;">';
                          nuevaFila+='<td id="tdcargaproducto"" colspan="9" class="tddescuento"></td>';
                          nuevaFila+='</tr>';
          $("#tabla-contenido > tbody#tbodycarga").html(nuevaFila);
          load('#tdcargaproducto');
      },
      success: function (respuesta){
        $("#tabla-contenido > tbody#tbodycarga").html('');
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
                respuesta["producto"].imagen,
                respuesta["producto"].nombre,
                respuesta["producto"].precioalpublico,
                respuesta["producto"].idtienda,
                respuesta["producto"].tiendalink,
                respuesta["producto"].tiendanombre,
                1,
                respuesta["producto"].s_idestadodetalle
            );
            removecarga({input:'#carga-venta'});
        }
      }
    })
  }
  
  function agregarproducto(idproducto,codigo,imagen,nombre,precioalpublico,idtienda,tienda_link,tienda_nombre,cantidad=1,idestadodetalle) {
    $("#codigoproducto").val('');
    $("#idproducto").val(null).trigger('change');

    var num = $("#tabla-contenido > tbody#tbody1").attr('num');
    var style = 'background-color: #008cea;color: #fff;';

    var tdstock = '';
    @if($configuracion['estadostock']==1)
    tdstock = '<td style="text-align: center" class="tdstock" id="tdstock'+num+'">---</td>';
    @endif
    var tddescuento = '';
    @if($configuracion['estadodescuento']==1)
    tddescuento = '<td style="text-align: center" id="tddescuento'+num+'" class="tddescuento"></td>';
    @endif

    var productDetalle = '<td colspan="2">'+nombre+'</td>';
    if(idestadodetalle==1){
        productDetalle = '<td>'+nombre+'</td><td><input id="productDetalle'+num+'" type="text"></td>';
    }

    var newimagen = '{{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }}';
    if(imagen!=null){
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
        nuevaFila+='<td class="mx-td-input"><input id="productUnidad'+num+'" type="number" value="'+precioalpublico+'" step="0.01" min="0" disabled></td>';
        nuevaFila+='<td class="mx-td-input"><input id="productTotal'+num+'" type="number" value="0.00" step="0.01" min="0" disabled></td>';       
        nuevaFila+='<td><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+','+idproducto+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>'
        nuevaFila+='</tr>';
    $("#tabla-contenido > tbody#tbody1").append(nuevaFila);
    $("#tabla-contenido > tbody#tbody1").attr('num',parseInt(num)+1);  

    @if($configuracion['estadostock']==1)
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

    @if($configuracion['estadodescuento']==1)
      cargar_descuento(idproducto,num);
    @endif

    setTimeout(function(){ $('#productCant'+num).select(); }, 100);

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
    $("#tabla-contenido > tbody#tbody1 > tr").each(function() {
        var num = $(this).attr('id');        
        var productCant = parseFloat($("#productCant"+num).val());
        var productUnidad = parseFloat($("#productUnidad"+num).val());
        var subtotal = ((productCant*productUnidad)).toFixed(2);
        $("#productTotal"+num).val(parseFloat(subtotal).toFixed(2));
        total = total+parseFloat((productCant*productUnidad).toFixed(2));
    });
    console.log(total);
    var costoenvio = parseFloat($("#costoenvio").val());
    if($("#costoenvio").val()=='' || $("#idtipoentrega").val()==1){
        costoenvio = 0;
    }
    var totalfinal = (parseFloat(total)).toFixed(2);
    // Descuento
    var totaldescuento = 0;
    @if($configuracion['estadodescuento']==1)
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
      @if($configuracion['estadodescuento']==1)
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
    @if($configuracion['estadodescuento']==1)
    detallepedido = detallepedido+'<b style="font-size: 22px;">Total Venta: '+total.toFixed(2)+'</b><br>';
    detallepedido = detallepedido+'<b style="font-size: 22px;">Total Descuento: -'+parseFloat(totaldescuento).toFixed(2)+'</b><br>';
    @endif
    detallepedido = detallepedido+'<b style="font-size: 22px;">Total: '+(total-parseFloat(totaldescuento)).toFixed(2)+'</b><br>';
    $('#cont-detallepedido').html(detallepedido);
  }
  
  @if($configuracion['estadodescuento']==1)
    function cargar_descuento(idproducto,num){
      $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/showdescuento')}}",
            type:'GET',
            data: {
                idproducto : idproducto
            },
            success: function (respuesta){
                var cupon = '';
                if(respuesta[0]['lista_descuento'].length>0){
                    var cupon = '<a href="javascript:;" id="modal-seleccionardescuento" class="lista_descuento'+num+'" onclick="seleccionardescuento('+idproducto+')" array_descuento=\''+JSON.stringify(respuesta[0]['lista_descuento'])+'\'"><img src="{{url('public/backoffice/sistema/icono-cupon-descuento.png')}}" style="height: 30px;"></a>';
                    $('#tddescuento'+num).html(cupon);
                    calcularmonto();
                    modal({click:'#modal-seleccionardescuento'});
                }else{
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
  // Fin Agregando Productos
</script>

<script>
$("#idcliente").select2({
    @include('app.select2_cliente')
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
        }
    })
});
  
$("#idubigeo").select2({
    @include('app.select2_ubigeo')
});
  
@if($configuracion_facturacion['idmonedapordefecto']!=null)
    $("#idmoneda").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    }).val({{ $configuracion_facturacion['idmonedapordefecto'] }}).trigger("change");
@else
    $("#idmoneda").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    });
@endif
  
@if($configuracion_facturacion['idempresapordefecto']!=null)
    $("#idagencia").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    }).val({{ $configuracion_facturacion['idempresapordefecto'] }}).trigger("change");    
@else
    $("#idagencia").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    });
@endif

@if($configuracion_facturacion['idcomprobantepordefecto']!=null)
    $("#idcomprobante").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    }).val({{ $configuracion_facturacion['idcomprobantepordefecto'] }}).trigger("change");   
@else
    $("#idcomprobante").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    });
@endif
  
$('#idtipopago').select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
}).on("change", function (e) {
    $('#div-usuario_saldo').css('display','none');
    if (e.currentTarget.value == 2) {
        $('#div-usuario_saldo').css('display','block');
    }
});
  
// Inicio botones de control (siguiente-anterior-realizarpago)
function realizarpago(){
    $('#tab-pago').click();
    $('#montorecibido').select();
}
// Fin botones de control (siguiente-anterior-realizarpago)
</script>
@endsection