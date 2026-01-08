@if($ordenpedido=='')

    <div class="mensaje-danger">
      <i class="fa fa-warning"></i> No existe ningún pedido con este código, ingrese otro por favor.
    </div>
@else
    <?php
    $s_venta = DB::table('s_venta')
                ->where('s_venta.idtienda',$tienda->id)
                ->where('s_venta.s_idcomida_ordenpedido',$ordenpedido->id)
                ->limit(1)
                ->first();
    ?>
    @if($s_venta!='')
        <div class="mensaje-danger">
          <i class="fa fa-warning"></i> El código de pedido ya tiende una venta registrada, ingrese otro por favor.
        </div>
    @else
    <div id="carga-venta">
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
                            <label>Comprobante *</label>
                            <select id="idcomprobante">
                                <option></option>
                                @foreach($comprobante as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                         </div>
                       </div>
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
                            <tbody num="0">
                            @foreach($ordenpedidodetalles as $value)
                                <tr style="background-color: #eeeeee;">
                                    <td>{{$value->productocodigo}}</td>
                                    <td style="padding: 10px;">{{$value->productonombre}}</td>
                                    <td>{{$value->cantidad}}</td>
                                    <td>{{$value->precio}}</td>
                                    <td>{{$value->total}}</td>       
                                </tr>
                            @endforeach  
                            </tbody>

                        </table>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                        </div> 
                        <div class="col-md-4">
                          <label>Total</label>
                          <input type="text" id="total" value="{{$ordenpedido->total}}" style="text-align: center;" disabled>
                          <label>Total Redondeado</label>
                          <input type="number" id="total_redondeado" value="{{ number_format(round($ordenpedido->total, 1), 2, '.', '') }}" step="0.01" style="font-size: 20px;
                                  font-weight: bold;
                                  padding-top: 5px;
                                  padding-bottom: 5px;
                                  text-align: center;" disabled>
                          <label>Monto Recibido *</label>
                          <input type="number" id="montorecibido" style="text-align: center;" step="0.01">
                          <label>Vuelto</label>
                          <input type="text" id="vuelto" value="0.00" style="text-align: center;" disabled> 
                        </div>    
                      </div> 
                      <a href="javascript:;" onclick="registrar_venta()" id="cont-btnventa" class="btn mx-btn-post">
                        Realizar la Venta
                      </a>
    </div>
    @endif
<style>
  .mx-realizar-pago {
    background-color: #343a40 !important;
  }
  .mx-realizar-pago:hover {
    background-color: #202327 !important;
  }
</style>
<script> 
  $('#montorecibido').select();
  modal({click:'#modal-ventarealizada'});
  // registrar venta
  
  $('#montorecibido').keyup( function(e) {
    if(e.keyCode == 13 && $('#montorecibido').val()!=''){
        registrar_venta();
    }
  })
  
function registrar_venta(){
    callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/venta',
        method: 'POST',
        carga: '#carga-venta',
        data:{
            view: 'registrarpedido',
            idordenpedido: {{$ordenpedido->id}},
            idcliente: $('#idcliente').val(),
            direccion: $('#direccion').val(),
            idubigeo: $('#idubigeo').val(),
            idmoneda: $('#idmoneda').val(),
            idagencia: $('#idagencia').val(),
            idcomprobante: $('#idcomprobante').val(),
            total: $('#total').val(),
            total_redondeado: $('#total_redondeado').val(),
            montorecibido: $('#montorecibido').val(),
            vuelto: $('#vuelto').val(),
        }
    },
    function(resultado){
        $('#codigo_pedido').val('');
        $('#codigo_pedido').select();
        $('#modal-ventarealizada').css('display','block');
        var imprimir = '';
        if(resultado['idestado']==3){
            if(resultado['idcomprobante']==1){
               imprimir = '<div id="iframeventa"><iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta') }}/'+resultado['idventa']+'/edit?view=ticketpdf#zoom=130" frameborder="0" width="100%" height="600px"></iframe></div>';
            }else if(resultado['idcomprobante']==2 || resultado['idcomprobante']==3){
               imprimir = '<iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionboletafactura') }}/'+resultado['idfacturacionboletafactura']+'/edit?view=ticketpdf#zoom=130" frameborder="0" width="100%" height="600px"></iframe>';
            }
        }
        $('#contenido-producto').html('<div class="cont-confirm" style="margin-top: 15px;">'+
                           '<div class="confirm"><i class="fa fa-check"></i></div>'+
                           '<div class="confirm-texto">¡Correcto!</div>'+
                           '<div class="confirm-subtexto">Se ha registrado correctamente.</div></div>'+
                           '<div class="custom-form" style="text-align: center;margin-bottom: 5px;">'+
                           '<button type="button" class="btn big-btn color-bg flat-btn mx-realizar-pago" style="margin: auto;float: none;" onclick="realizar_nueva_venta()">'+
                           '<i class="fa fa-check"></i> Realizar Nueva Venta</button></div>'+
                           '<div class="custom-form" style="text-align: center;margin-bottom: 5px;">'+
                           '<button type="button" class="btn big-btn color-bg flat-btn" style="margin: auto;float: none;" onclick="iraventas()">'+
                           '<i class="fa fa-check"></i> Ir a las Ventas</button></div>'+
                           //'<input type="hidden" id="btncerrarmodal">'+
                           imprimir); 
      
        //cerrar modal
        $("body").attr('id','modalventa');
        $('#modalventa').keyup( function(e) {
            if(e.keyCode == 13 || e.keyCode == 27){
                $("body").removeAttr('id');
                realizar_nueva_venta();
            }
        })
        removecarga({input:'#carga-venta'});
    })
}
function realizar_nueva_venta(){
    location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta') }}/create?view=ventapedido';
    /*$('#iframeventa').html('');
    $('#codigo_pedido').val('');
  
    $('#modal-ventarealizada').css('display','none');
    $('#codigo_pedido').select();
    $('#load_buscarcodigopedido').html('');*/
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
    @include('app.select2_ubigeo')
});
// fin cliente

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
          // delivery
          $('#delivery_pernonanombre').val(respuesta['usuario'].nombre);
          $('#delivery_numerocelular').val(respuesta['usuario'].numerotelefono);
          $('#delivery_direccion').val(respuesta['usuario'].direccion);
        }
    })
});

$("#idubigeo").select2({
    @include('app.select2_ubigeo')
});

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
@endif