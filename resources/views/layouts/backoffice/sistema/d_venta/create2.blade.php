@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Venta</span>
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
          <div style="display:none;">
          @if(configuracion($tienda->id,'sistema_nivelventa')['valor']==1)
          <div class="box" style="width: 300px;">
            <input class="Switcher__checkbox sr-only" id="idestado" type="checkbox" <?php echo configuracion($tienda->id,'sistema_estadoventa')['valor']==3? 'checked="checked"':'' ?>>
            <label class="Switcher" for="idestado">
              <div class="Switcher__trigger" data-value="Realizar Pedido"></div>
              <div class="Switcher__trigger" data-value="Realizar Venta"></div>
            </label>
          </div>
          <style>
          .Switcher::before {
            transform: translateX(-75%);
          }
          .Switcher__checkbox:checked + .Switcher::before {
            transform: translateX(23%);
          }
          </style>
          @elseif(configuracion($tienda->id,'sistema_nivelventa')['valor']==2)
          <div class="box" style="width: 310px;">
            <input class="Switcher__checkbox sr-only" id="idestado" type="checkbox" <?php echo configuracion($tienda->id,'sistema_estadoventa')['valor']==2? 'checked="checked"':'' ?>>
            <label class="Switcher" for="idestado">
              <div class="Switcher__trigger" data-value="Realizar Pedido"></div>
              <div class="Switcher__trigger" data-value="Confirmar Pedido"></div>
            </label>
          </div>
          <style>
          .Switcher::before {
            transform: translateX(-76%);
          }
          .Switcher__checkbox:checked + .Switcher::before {
            transform: translateX(23.5%);
          }
          </style>
          @else
          <div class="box" style="width: 300px;">
            <input class="Switcher__checkbox sr-only" id="idestado" type="checkbox" checked="checked">
            <label class="Switcher" for="idestado">
              <div class="Switcher__trigger" data-value="Realizar Pedido"></div>
              <div class="Switcher__trigger" data-value="Realizar Venta"></div>
            </label>
          </div>
          <style>
          .Switcher::before {
            transform: translateX(-75%);
          }
          .Switcher__checkbox:checked + .Switcher::before {
            transform: translateX(23%);
          }
          </style>
          @endif
          </div>
          <div class="tabs-container" id="tab-carritocompra">
              <ul class="tabs-menu">
                  @if($tienda->idcategoria==30)
                  <li class="current"><a href="#tab-carritocompra-4" id="tab-mesa">Mesa</a></li>
                  <li><a href="#tab-carritocompra-0" id="tab-pedido">Pedido</a></li>
                  @else
                  <style>
                  .tabs-menu {
                      border-bottom: 0px solid #aaa;
                  }
                  </style>
                  @endif
              </ul>
              <div class="tab">
                  @if($tienda->idcategoria==30)
                  <div id="tab-carritocompra-4" class="tab-content" style="display: block;">
                      <div id="cont-mesas-master" style="float: left;width: 100%;"></div>
                  </div>
                  @endif
                  <div id="tab-carritocompra-0" class="tab-content" style="display: <?php echo $tienda->idcategoria==30? 'none':'block' ?>;">
                      @if($tienda->idcategoria==30)
                      <div id="comida_load_buscarcodigopedido"></div>
                      <input type="hidden" id="comida_idpedido">
                      @endif
                      <input type="hidden" id="productoquitado">
                      <div class="row">
                         <div class="col-md-6">
                            <label>Cliente *</label>
                            @if(configuracion($tienda->id,'facturacion_clientepordefecto')['resultado']=='CORRECTO')
                                <div class="row">
                                   <div class="col-md-9">
                                      <select id="idcliente">
                                          <?php 
                                          $users = DB::table('users')
                                              ->join('ubigeo','ubigeo.id','users.idubigeo')
                                              ->where('users.id',configuracion($tienda->id,'facturacion_clientepordefecto')['valor'])
                                              ->select(
                                                  'users.*',
                                                  'ubigeo.nombre as ubigeonombre'
                                              )
                                              ->first(); 
                                          ?>
                                          <option value="{{ configuracion($tienda->id,'facturacion_clientepordefecto')['valor'] }}">
                                            @if($users->idtipopersona==1 or $users->idtipopersona==3)
                                            {{ $users->identificacion }} - {{ $users->apellidos }}, {{ $users->nombre }}
                                            @elseif($users->idtipopersona==2)
                                            {{ $users->identificacion }} - {{ $users->nombre }}
                                            @endif
                                          </option>
                                      </select>
                                   </div>
                                   <div class="col-md-3">
                                      <a href="javascript:;" id="modal-registrarcliente" class="btn btn-warning"><i class="fa fa-plus"></i> Agregar</a>
                                   </div>
                                </div>
                                <label>Dirección</label>
                                <input type="text" id="direccion" value="{{$users->direccion}}"/>
                                <label>Ubicación (Ubigeo)</label>
                                <select id="idubigeo">
                                    <option value="{{ $users->idubigeo }}">{{ $users->ubigeonombre }}</option>
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
                      <div class="table-responsive">
                        <table class="table" id="tabla-contenido">
                            <thead class="thead-dark">
                              <tr>
                                <th colspan="3">Código - Producto</th>
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
                                  <td width="15%" class="mx-td-input"><input type="text" id="buscarcodigoproducto" placeholder="Código"/></td>
                                  <td colspan="{{configuracion($tienda->id,'sistema_estadostock')['valor']==1?'7':'6'}}" class="mx-td-input">
                                    <select id="idproducto">
                                        <option></option>
                                    </select>
                                  </td>
                                  <td width="auto"></td>
                              </tr>
                            </thead>
                            <tbody num="0" id="tbody1"></tbody>
                            <tbody num="0" id="tbodycarga"></tbody>
                            <tbody num="0" id="tbody_totalventa"></tbody>
                            <tbody num="0" id="tbody2"></tbody>
                            <tbody num="0" id="tbody_totaldescuento"></tbody>
                            <tbody num="0" id="tbody3">
                              <tr>
                                  @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
                                  <td></td>
                                  @endif
                                  @if(configuracion($tienda->id,'sistema_estadostock')['valor']==1)
                                  <td></td>
                                  @endif
                                  <td colspan="6" style="text-align:right;">SUB TOTAL</td>
                                  <td class="mx-td-input"><input type="text" value="0.00" id="subtotal" disabled/></td>
                                  <td width="auto"></td>
                              </tr>
                              @if(configuracion($tienda->id,'sistema_estadodescuentoventatotal')['valor']==1)
                              <tr>
                                  @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
                                  <td style="border-top: 0px solid white;"></td>
                                  @endif
                                  @if(configuracion($tienda->id,'sistema_estadostock')['valor']==1)
                                  <td style="border-top: 0px solid white;"></td>
                                  @endif
                                  <td colspan="6" style="text-align:right;border-top: 0px solid white;">TOTAL DESCUENTO</td>
                                  <td class="mx-td-input" style="border-top: 0px solid white;"><input type="number" id="descuento_total" placeholder="0.00" onkeyup="calcularmonto()" step="0.01"></td>
                                  <td width="auto" style="border-top: 0px solid white;"></td>
                              </tr>
                              <tr>
                                  @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
                                  <td style="border-top: 0px solid white;"></td>
                                  @endif
                                  @if(configuracion($tienda->id,'sistema_estadostock')['valor']==1)
                                  <td style="border-top: 0px solid white;"></td>
                                  @endif
                                  <td colspan="6" style="text-align:right;border-top: 0px solid white;">TOTAL A PAGAR</td>
                                  <td class="mx-td-input" style="border-top: 0px solid white;"><input type="text" id="descuento_totalapagar" value="0.00" style="font-weight: bold;text-align: center;" disabled></td>
                                  <td width="auto" style="border-top: 0px solid white;"></td>
                              </tr>
                              @endif
                              <tr>
                                  @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
                                  <td style="border-top: 0px solid white;"></td>
                                  @endif
                                  @if(configuracion($tienda->id,'sistema_estadostock')['valor']==1)
                                  <td style="border-top: 0px solid white;"></td>
                                  @endif
                                  <td colspan="6" style="text-align:right;border-top: 0px solid white;">TOTAL</td>
                                  <td class="mx-td-input" style="border-top: 0px solid white;"><input type="text" id="total" value="0.00" disabled></td>
                                  <td width="auto" style="border-top: 0px solid white;"></td>
                              </tr>
                              <tr>
                                  @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
                                  <td style="border-top: 0px solid white;"></td>
                                  @endif
                                  @if(configuracion($tienda->id,'sistema_estadostock')['valor']==1)
                                  <td style="border-top: 0px solid white;"></td>
                                  @endif
                                  <td colspan="6" style="text-align:right;border-top: 0px solid white;">TOTAL REDONDEADO</td>
                                  <td class="mx-td-input" style="border-top: 0px solid white;"><input type="text" id="total_redondeado" value="0.00" disabled></td>
                                  <td width="auto" style="border-top: 0px solid white;"></td>
                              </tr>
                              @if(configuracion($tienda->id,'sistema_estadoformapago')['valor']!=1)
                              <tr>
                                  @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
                                  <td style="border-top: 0px solid white;"></td>
                                  @endif
                                  @if(configuracion($tienda->id,'sistema_estadostock')['valor']==1)
                                  <td style="border-top: 0px solid white;"></td>
                                  @endif
                                  <td colspan="6" style="text-align:right;border-top: 0px solid white;">MONTO RECIBO</td>
                                  <td class="mx-td-input" style="border-top: 0px solid white;"><input type="number" id="montorecibido" step="0.01"></td>
                                  <td width="auto" style="border-top: 0px solid white;"></td>
                              </tr>
                              <tr>
                                  @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
                                  <td style="border-top: 0px solid white;"></td>
                                  @endif
                                  @if(configuracion($tienda->id,'sistema_estadostock')['valor']==1)
                                  <td style="border-top: 0px solid white;"></td>
                                  @endif
                                  <td colspan="6" style="text-align:right;border-top: 0px solid white;">VUELTO</td>
                                  <td class="mx-td-input" style="border-top: 0px solid white;"><input type="text" id="vuelto" value="0.00" disabled></td>
                                  <td width="auto" style="border-top: 0px solid white;"></td>
                              </tr>
                              @endif
                            </tbody>
                        </table>
                      </div>
                      <div class="custom-form">
                      @if(configuracion($tienda->id,'sistema_estadoformapago')['valor']==1)
                      <a href="javascript:;" 
                         class="btn mx-btn-post"
                         id="modal-ventaformapago" 
                         onclick="pagina({route:'{{ url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/create') }}?view=formapago'+
                                  '&efectivo='+$('#total_redondeado').val()+
                                  '&onclick=registrar_venta()',
                                  result:'#cont-formapago'});">
                          Realizar Venta     
                      </a>
                      @else
                      <a href="javascript:;" onclick="registrar_venta()" id="cont-btnventa" class="btn mx-btn-post">
                          @if(configuracion($tienda->id,'sistema_estadoventa')['valor']==1) 
                            Registrar Pedido
                          @elseif(configuracion($tienda->id,'sistema_estadoventa')['valor']==2) 
                            Confirmar Pedido
                          @elseif(configuracion($tienda->id,'sistema_estadoventa')['valor']==3) 
                            Realizar Venta
                          @else
                            Realizar Venta
                          @endif      
                      </a>
                      @endif
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
  .mesa {
      padding: 5px;
      border-radius: 5px;
      line-height: 3;
      margin-bottom: 5px;
      font-size: 15px;
      cursor: pointer;
      color: #ffffff;
      font-weight: bold;
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

@if(configuracion($tienda->id,'sistema_estadoformapago')['valor']==1)
<!--  modal ventaformapago --> 
<div class="main-register-wrap modal-ventaformapago" id="modal-ventaformapago">
    <div class="main-overlay"></div>
    <div class="main-register-holder" style="margin: 10px auto 50px;">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Pago</h3>
            <div class="mx-modal-cuerpo" id="cont-formapago">
            </div>
        </div>
    </div>
</div>
@endif
<!--  fin modal ventaformapago --> 
    @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
      <div class="main-register-wrap modal-seleccionardescuento">
          <div class="main-overlay"></div>
          <div class="main-register-holder">
              <div class="main-register fl-wrap">
                  <div class="close-reg"><i class="fa fa-times"></i></div>
                  <h3>Descuentos</h3>
                  <div class="mx-modal-cuerpo" id="contenido-seleccionardescuento">
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
<style>
  .cont_mesa {
      padding: 5px;
      border-radius: 5px;
      margin-bottom: 5px;
      height: 82px;
  }
  .mesa_numero {
      line-height: 2;
      font-size: 16px;
      color: #ffffff;
      font-weight: bold;
  }
  .mesa_tiempo {
      font-size: 12px;
      color: #ffffff;
  }
  .mesa_mesero {
      font-size: 14px;
      color: #ffffff;
      padding-bottom: 8px;
  }
  
</style>
<style>
  .car_cont{
    overflow: hidden;
    padding-bottom: 1px;
    padding-top: 1px;
    background-color: #c1c0c0;
    margin-bottom: 1px;
    border-radius: 5px;
    padding:2px;
    font-weight:100;
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
    margin-right: 3px;
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
@endsection
@section('subscripts')

@if(configuracion($tienda->id,'sistema_estadoformapago')['valor']==1)
<script>
    modal({click:'#modal-ventaformapago'});
</script>
@endif
<script> 
  /* -------------- COMIDA ------------------*/
  @if($tienda->idcategoria==30)
  cargar_mesa(); 
  function cargar_mesa(){
      $('#tab-mesa').click();
      //$('#cont-mesas-master').css('display','block');
      //$("#comida_cont_mesa").css('display','none');
      $("#comida_load_buscarcodigopedido").html(''); 
      load('#cont-mesas-master');
      $.ajax({
          url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidaordenpedido/create')}}",
          type:'GET',
          data: {
              view : 'mesa'
          },
          success: function (respuesta){
              $("#cont-mesas-master").html(respuesta);
          }
      })
  }  
  
  function cargar_pedido(numeromesa,idordenpedido){
      limpiarventa();
      //$('#cont-mesas-master').css('display','none');
      load('#comida_load_buscarcodigopedido'); 
      $.ajax({
          url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/show-comida-seleccionarproducto')}}",
          type:'GET',
          data:{
              numeromesa : numeromesa,
              idordenpedido : idordenpedido,
          },
          success: function (respuesta){
            $("#comida_load_buscarcodigopedido").html(''); 
            $('#tab-pedido').click();
            if(respuesta['pedido']!=null){
                    $("#comida_load_buscarcodigopedido").html('<div class="mensaje-success">'+
                            ' <a href="javascript:;" onclick="limpiarventa(),cargar_mesa()" style="padding: 6px 20px;border-radius: 25px;color: #fff;cursor: pointer;background-color: #df1355;"><i class="fa fa-arrow-left"></i> Cambiar</a>'+
                            ' | <i class="fa fa-table"></i> '+respuesta['mensaje']+                           
                          '</div>');
                    //$("#comida_cont_mesa").css('display','block');
                    $("#tabla-contenido > tbody#tbody1").html('');
                    $("#tabla-contenido > tbody#tbody2").html('');

                    //$('#comida-cont-nombre-mesa').html(respuesta['numeromesa']);
                    $('#comida_idpedido').val(respuesta['pedido'].id);

                    $.each(respuesta['pedidodetalles'], function( i, val ) {
                        if(val!=null){
                            agregarproducto(
                                val.idproducto,
                                val.productocodigo,
                                val.productoimagen,
                                val.productonombre,
                                val.productopreciopublico,
                                val.idtienda,
                                val.tiendalink,
                                val.tiendanombre,
                                val.cantidad,
                                val.idestadodetalle,
                                val.idordenpedidodetalle
                            );
                        }    
                    });      
                    
            }else{
                $("#comida_load_buscarcodigopedido").html('<div class="mensaje-warning">'+
                            ' <a href="javascript:;" onclick="limpiarventa(),cargar_mesa()" style="padding: 6px 20px;border-radius: 25px;color: #fff;cursor: pointer;background-color: #df1355;"><i class="fa fa-arrow-left"></i> Cambiar</a>'+
                              ' | <i class="fa fa-table"></i> '+respuesta['mensaje']+
                          '</div>');
            }
          }
      });
  }
  @endif
  /* -------------- FIN COMIDA ------------------*/
  
  $('#montorecibido').keyup( function(e) {
    if(e.keyCode == 13){
        registrar_venta();
    }
  })

 $("#montorecibido").keyup(function() {
      var total =  parseFloat($("#montorecibido").val());
      var monto =  parseFloat($("#monto").val());
      if($("#monto").val()==''){
          monto = 0;
      }
      if($("#montorecibido").val()==''){
          total = 0;
      }
      var suma = monto - total;
      $("#montorestante").val(parseFloat(suma).toFixed(2));
});
  
  $('#idtipopago').select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
}).on("change", function (e) {
    $('#div-usuario_saldo').css('display','none');
   // $('#div-usuario_efectivo').css('display','none');
    if (e.currentTarget.value == 2) {
        $('#div-usuario_saldo').css('display','block');
    }
}); 

$("#idusuariosaldo").select2({
    @include('app.select2_cliente')
}).on("change", function(e) {
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/showseleccionarusuariosaldo')}}",
        type:'GET',
        data: {
            idusuariosaldo : e.currentTarget.value
        },
        success: function (respuesta){
          $('#monto').val(respuesta['usuariosaldo'].monto);
        }
    })
});
 
tab({click:'#tab-carritocompra'});
modal({click:'#modal-ventapuntoconsumo'});
modal({click:'#modal-ventarealizada'});
  
$('#descuento_total').keyup( function(e) {
    if(e.keyCode == 27){
        $('#tab-pago').click();
        $('#montorecibido').select();
    }
})
  
function realizarpago(){
    $('#tab-pago').click();
    $('#montorecibido').select();
}
// carrito de compra
/*$("#costoenvio").keyup(function() {
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
});*/

@if(configuracion($tienda->id,'sistema_nivelventa')['valor']==1)
$("#idestado").change(function() {
    var idestado = $("#idestado:checked").val();
    if(idestado=='on'){
        $('#cont-montorecibido').css('display','block');
        $('#cont-btnventa').html('Realizar Venta');
    }else{
        $('#cont-montorecibido').css('display','none');
        $('#cont-btnventa').html('Realizar Pedido');
    }
});
@elseif(configuracion($tienda->id,'sistema_nivelventa')['valor']==2)
$("#idestado").change(function() {
    var idestado = $("#idestado:checked").val();
    if(idestado=='on'){
        $('#cont-montorecibido').css('display','none');
        $('#cont-btnventa').html('Confirmar Pedido');
    }else{
        $('#cont-montorecibido').css('display','none');
        $('#cont-btnventa').html('Realizar Pedido');
    }
});
@endif
  
/*carga_carritocompra();
function carga_carritocompra(){
    if(Cookies.get('cookaddproducto')!=undefined){
        var cookaddproducto = JSON.parse(Cookies.get('cookaddproducto'));
        $.each(cookaddproducto, function( i, val ) {
            if(val!=null){
                agregarproducto(
                    val.idproducto,
                    val.producto_codigo,
                    val.producto_nombre,
                    0,
                    val.producto_precioalpublico,
                    val.idtienda,
                    val.tienda_link,
                    val.tienda_nombre,
                    val.producto_cantidad,
                    val.idestadodetalle
                );
            }    
        });
    }  
}*/
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
        @if(configuracion($tienda->id,'sistema_estadodescuentoventatotal')['valor']==1)
        totaldescuento = $('#descuento_total').val();
        if(totaldescuento==''){
            totaldescuento = 0;
        }
        detallepedido = detallepedido+'<b style="font-size: 22px;">Sub Total: '+total.toFixed(2)+'</b><br>';
        detallepedido = detallepedido+'<b style="font-size: 22px;">Total Descuento: -'+parseFloat(totaldescuento).toFixed(2)+'</b><br>';
        @endif
        detallepedido = detallepedido+'<b style="font-size: 22px;">Total: '+(total-parseFloat(totaldescuento)).toFixed(2)+'</b><br>';
        $('#cont-detallepedido').html(detallepedido);
  
}
// fin carrito de compra
// registrar venta
  
function registrar_venta(){
    callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/venta',
        method: 'POST',
        carga: '#carga-venta',
        data:{
            view: 'registrar',
            productos: selectproductos(),
            @if(configuracion($tienda->id,'sistema_estadoformapago')['valor']==1)
            formapago_contado_seleccionar: formapago_contado_seleccionar(),
            formapago_credito_seleccionar: formapago_credito_seleccionar(),
            @endif
            @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
            productos_descuento: selectproductos_descuento(),
            totalventa: $('#tabla_total_venta').val(),
            totaldescuento: $('#tabla_total_descuento').val(),
            @endif
            @if(configuracion($tienda->id,'sistema_estadodescuentoventatotal')['valor']==1)
            descuento_total: $('#descuento_total').val(),
            descuento_totalapagar: $('#descuento_totalapagar').val(),
            @endif
            idestado: $('#idestado:checked').val(),
            idcliente: $('#idcliente :selected').val(),
            direccion: $('#direccion').val(),
            idubigeo: $('#idubigeo').val(),
            idagencia: $('#idagencia').val(),
            idcomprobante: $('#idcomprobante').val(),
            idmoneda: $('#idmoneda').val(),
            idtipoentrega: $('#idtipoentrega').val(),
            idusuariosaldo: $('#idusuariosaldo').val(),
            montorestante: $('#montorestante').val(),
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
            productoquitado : $('#productoquitado').val(),
            @if($tienda->idcategoria==30)
            comida_idpedido: $('#comida_idpedido').val(),
            @endif
        }
    },
    function(resultado){
        
        $('.modal-ventaformapago').css('display','none');
        $('#montorecibido').attr('disabled','true');
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
        
       /* $('.modal-ventarealizada').focus();
        $('.modal-ventarealizada').keyup( function(e) {
            if(e.keyCode == 13 || e.keyCode == 27){
                realizar_nueva_venta();
            }
        })*/
        removecarga({input:'#carga-venta'});
    })
}
function realizar_nueva_venta(){
        @if($tienda->idcategoria==30)
            limpiarventa();
            cargar_mesa();
        @else
            limpiarventa();
        @endif
    $('#iframeventa').html('');
    $('#modal-ventarealizada').css('display','none');
    $('#buscarcodigoproducto').select();
}
function iraventas(){
    location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta') }}';
}
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
          /*$('#delivery_pernonanombre').val(respuesta['usuario'].nombre);
          $('#delivery_numerocelular').val(respuesta['usuario'].numerotelefono);
          $('#delivery_direccion').val(respuesta['usuario'].direccion);*/
        }
    })
});

$("#idubigeo").select2({
    @include('app.select2_ubigeo')
});
@if(configuracion($tienda->id,'facturacion_empresapordefecto')['resultado']=='CORRECTO')  
    $("#idagencia").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    }).val({{ configuracion($tienda->id,'facturacion_empresapordefecto')['valor'] }}).trigger("change");    
@else
    $("#idagencia").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    });
@endif

@if(configuracion($tienda->id,'facturacion_comprobantepordefecto')['resultado']=='CORRECTO')
    $("#idcomprobante").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    }).val({{ configuracion($tienda->id,'facturacion_comprobantepordefecto')['valor'] }}).trigger("change");   
@else
    $("#idcomprobante").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    });
@endif

@if(configuracion($tienda->id,'facturacion_monedapordefecto')['resultado']=='CORRECTO')
    $("#idmoneda").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    }).val({{ configuracion($tienda->id,'facturacion_monedapordefecto')['valor'] }}).trigger("change");
@else
    $("#idmoneda").select2({
        placeholder: "--  Seleccionar --",
        minimumResultsForSearch: -1
    });
@endif

@if(configuracion($tienda->id,'sistema_tipoentregapordefecto')['resultado']=='CORRECTO')
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
        calcularmonto();
    }).val({{ configuracion($tienda->id,'sistema_tipoentregapordefecto')['valor'] }}).trigger("change");
@else
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
        calcularmonto();
    }).val(1).trigger("change");
@endif
 


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
    @include('app.select2_producto',[
        'idtienda'=>$tienda->id
    ])
}).on("change", function(e) {
    if(e.currentTarget.value!=''){
        /*var producto = datajson_productos.find(val => val.id === parseFloat(e.currentTarget.value));
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
        );*/
        $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/showseleccionarproducto')}}",
            type:'GET',
            data: {
                idproducto : e.currentTarget.value
            },
            beforeSend: function (data) {
                var nuevaFila='<tr class="td-tablaventa" style="color: #fff;">';
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
                      respuesta["producto"].idestadodetalle
                  );
                  removecarga({input:'#carga-venta'});
              }
            }
        })
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

function buscarcodigo(pthis){
    if($(pthis).val()!=''){

        $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/inicio/showseleccionarproductocodigo')}}",
            type:'GET',
            data: {
                codigoproducto : $(pthis).val()
            },
            beforeSend: function (data) {
                var nuevaFila='<tr class="td-tablaventa" style="color: #fff;">';
                                nuevaFila+='<td id="tdcargaproducto"" colspan="9" class="tddescuento"></td>';
                                nuevaFila+='</tr>';
                $("#tabla-contenido > tbody#tbodycarga").html(nuevaFila);
                load('#tdcargaproducto');
            },
            success: function (respuesta){
              
              $("#tabla-contenido > tbody#tbodycarga").html('');
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
                        respuesta["producto"].imagen,
                        respuesta["producto"].nombre,
                        respuesta["producto"].precioalpublico,
                        respuesta["producto"].idtienda,
                        respuesta["producto"].tiendalink,
                        respuesta["producto"].tiendanombre,
                        1,
                        respuesta["producto"].idestadodetalle
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
}
function agregarproducto(idproducto,codigo,imagen,nombre,precioalpublico,idtienda,tienda_link,tienda_nombre,cantidad=1,idestadodetalle,procedencia=''){

      //$("#codigoproducto").val('');
      //$("#idproducto").html('');
      $("#idproducto").val(null).trigger('change');
  
      var num = $("#tabla-contenido > tbody#tbody1").attr('num');
      var style = 'color: #fff;';
  
      var tdstock = '';
      @if(configuracion($tienda->id,'sistema_estadostock')['valor']==1)
      tdstock = '<td style="text-align: center" class="tdstock" id="tdstock'+num+'">---</td>';
      @endif
      var tddescuento = '';
      @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
      tddescuento = '<td style="text-align: center" id="tddescuento'+num+'" class="tddescuento"></td>';
      @endif
      var codigonom = codigo;
      if(codigo!=''){
          codigonom = codigo+' - ';
      }
      var productDetalle = '<td style="white-space: break-spaces;" colspan="3">'+codigonom+nombre+'</td>';
      if(idestadodetalle==1){
          productDetalle = '<td style="white-space: break-spaces;" colspan="2">'+codigonom+nombre+'</td><td><input id="productDetalle'+num+'" type="text" onkeyup="texto_mayucula(this)"></td>';
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

  
      var nuevaFila='<tr class="td-tablaventa" id="'+num+'" procedencia="'+procedencia+'" idestadodetalle="'+idestadodetalle+'" idproducto="'+idproducto+'" producto_codigo="'+codigo+'" producto_nombre="'+nombre+'" idtienda="'+idtienda+'" tienda_link="'+tienda_link+'" tienda_nombre="'+tienda_nombre+'" nombreproducto="'+codigonom+nombre+'" style="'+style+'">';
          //nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+=productDetalle+tddescuento+'<td>'+imagentd+'</td>'+tdstock;
          nuevaFila+='<td class="mx-td-input"><input id="productCant'+num+'" type="number" value="'+cantidad+'" onkeyup="calcularmonto();" onclick="calcularmonto();"></td>';
          nuevaFila+='<td class="mx-td-input"><input id="productUnidad'+num+'" type="number" value="'+precioalpublico+'" onkeyup="calcularmonto();" onclick="calcularmonto();" step="0.01" min="0" <?php echo configuracion($tienda->id,'sistema_estadopreciounitario')['valor']==1?'':'disabled' ?>></td>';
          nuevaFila+='<td class="mx-td-input"><input id="productTotal'+num+'" type="number" value="0.00" step="0.01" min="0" disabled></td>';       
          nuevaFila+='<td><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+','+idproducto+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>'
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
}
@if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
      function cargar_descuento(idproducto,num){
          $.ajax({
                url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/showdescuento')}}",
                type:'GET',
                data: {
                    idproducto : idproducto
                },
                /*beforeSend: function (data) {
                    load('#tddescuento'+num); 
                },*/
                success: function (respuesta){
                    var cupon = '';
                    if(respuesta[0]['lista_descuento'].length>0){
                        var cupon = '<a href="javascript:;" id="modal-seleccionardescuento" class="lista_descuento'+num+'" onclick="seleccionardescuento('+idproducto+')" array_descuento=\''+JSON.stringify(respuesta[0]['lista_descuento'])+'\'"><img src="{{url('public/backoffice/sistema/icono-cupon-descuento.png')}}" style="height: 30px;"></a>';
                        $('#tddescuento'+num).html(cupon);
                        calcularmonto();
                        modal({click:'#modal-seleccionardescuento'});
                    }else{
                        //$('#tddescuento'+num).html('');
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

    var totalfinal = (parseFloat(total)).toFixed(2);
    // Descuento
    var totaldescuento = 0;
    @if(configuracion($tienda->id,'sistema_estadodescuento')['valor']==1)
    actualizar_descuento(totalfinal);
    totaldescuento = $("#tabla_total_descuento").val();
    @endif
  
    var totalfinal = (totalfinal-parseFloat(totaldescuento)).toFixed(2);
    $("#subtotal").val(totalfinal);
  
    @if(configuracion($tienda->id,'sistema_estadodescuentoventatotal')['valor']==1)
      var descuent = $('#descuento_total').val();
      var totalfinal = totalfinal-descuent;
      $('#descuento_totalapagar').val(totalfinal.toFixed(2))
    @endif
  
    var total = (parseFloat(totalfinal)).toFixed(2);
    $("#total").val(total);
    $("#total_redondeado").val((Math.round10(total, -1)).toFixed(2));
  
    carga_carritocompradetalle();  
  
    // deposito
    $('#totalmonto_efectivo').html((Math.round10(total, -1)).toFixed(2));
}
function limpiarventa(){ 
    //location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta/create') }}?view=ventarapida';   
    $('#productoquitado').val('');
    $('#montorecibido').removeAttr('disabled');
    // general
    @if(configuracion($tienda->id,'sistema_nivelventa')['valor']==1)
        var idestado = $("#idestado:checked").val();
        if(idestado=='on'){
            $('#cont-montorecibido').css('display','block');
            $('#cont-btnventa').html('Realizar Venta');
        }else{
            $('#cont-montorecibido').css('display','none');
            $('#cont-btnventa').html('Realizar Pedido');
        }
    @elseif(configuracion($tienda->id,'sistema_nivelventa')['valor']==2)
        var idestado = $("#idestado:checked").val();
        if(idestado=='on'){
            $('#cont-montorecibido').css('display','none');
            $('#cont-btnventa').html('Confirmar Pedido');
        }else{
            $('#cont-montorecibido').css('display','none');
            $('#cont-btnventa').html('Realizar Pedido');
        }
    @endif
    $('#tab-pedido').click();
    $('#buscarcodigoproducto').select();
    // pedido
    $("#tabla-contenido > tbody#tbody1").html('');
    $("#tabla-contenido > tbody#tbody2").html('');
    $("#tabla-contenido > tbody#tbody_totalventa").html('');
    $("#tabla-contenido > tbody#tbody_totaldescuento").html('');
    $("#subtotal").val('0.00');
    $("#total").val('0.00');
    $("#total_redondeado").val('0.00');
    // entrega
    $("#idtipoentrega").val(1).trigger('change');
    $("#idestadoenvio").val(1).trigger('change');
    $("#delivery_fecha").val('{{ Carbon\Carbon::now()->format('Y-m-d') }}');
    $("#delivery_hora").val('{{ Carbon\Carbon::now()->format('h:s:i') }}');
    $("#delivery_pernonanombre").val('');
    $("#delivery_numerocelular").val('');
    $("#delivery_direccion").val('');
    // facturación
    
    @if(configuracion($tienda->id,'facturacion_clientepordefecto')['resultado']=='CORRECTO')
        <?php 
        $users = DB::table('users')
            ->leftJoin('ubigeo','ubigeo.id','users.idubigeo')
            ->where('users.id',configuracion($tienda->id,'facturacion_clientepordefecto')['valor'])
            ->select(
                'users.*',
                'ubigeo.nombre as ubigeonombre'
            )
            ->first(); ?>
        @if($users->idtipopersona==1 or $users->idtipopersona==3)
        $("#idcliente").html('<option value="{{ configuracion($tienda->id,'facturacion_clientepordefecto')['valor'] }}">{{ $users->identificacion }} - {{ $users->apellidos }}, {{ $users->nombre }}</option>');
        $('#direccion').val('{{$users->direccion}}');
        $("#idubigeo").html('<option value="{{$users->idubigeo}}">{{$users->ubigeonombre}}</option>');
        @elseif($users->idtipopersona==2)
        $("#idcliente").html('<option value="{{ configuracion($tienda->id,'facturacion_clientepordefecto')['valor'] }}">{{ $users->identificacion }} - {{ $users->nombre }}</option>'); 
        $('#direccion').val('{{$users->direccion}}');
        $("#idubigeo").html('<option value="{{$users->idubigeo}}">{{$users->ubigeonombre}}</option>'); 
        @endif
    @else
        $("#idcliente").html('<option></option>');
        $("#direccion").val('');
        $("#idubigeo").html('<option></option>');
    @endif
    @if(configuracion($tienda->id,'facturacion_empresapordefecto')['resultado']=='CORRECTO')
        $("#idagencia").val({{ configuracion($tienda->id,'facturacion_empresapordefecto')['valor'] }}).trigger('change');
    @endif
        @if(configuracion($tienda->id,'facturacion_comprobantepordefecto')['resultado']=='CORRECTO')
        $("#idcomprobante").val({{ configuracion($tienda->id,'facturacion_comprobantepordefecto')['valor'] }}).trigger("change");
    @endif
    $("#cont-detallepedido").html('');
    $("#montorecibido").val('');
    $("#vuelto").val('0.00');
  
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
function eliminarproducto(num,idproducto){
    
    // agregar producto quitado
    var productoquitado = $('#productoquitado').val();
    var procedencia = $("#tabla-contenido > tbody#tbody1 > tr#"+num).attr('procedencia');
    $('#productoquitado').val(productoquitado+'/,/'+procedencia);

  
    // eliminar
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
<script> 

    function agregar_formapago(){
      var num = $("#tabla-formapago > tbody#tbody-formapago").attr('num');
      var nuevaFila='<tr id="'+num+'">'+
                         '<td>'+
                           '<table class="table">'+
                             '<tbody>'+
                               '<tr>'+
                                 '<td width="150px">Cuenta Bancaria *</td>'+
                                 '<td colspan="2">'+
                                   '<select id="formapago_idcuentabancaria'+num+'">'+
                                     '<option></option>'+
                                     '@foreach ($cuentabancarias as $value)'+
                                     '<option value="{{ $value->id }}" formapago_banco="{{ $value->banco }}" formapago_numerocuenta="{{ $value->numerocuenta }}">{{ $value->banco }}: {{ $value->numerocuenta }}</option>'+
                                     '@endforeach'+
                                   '</select>'+
                                 '</td>'+
                               '</tr>'+
                               '<tr>'+
                                 '<td>Nro de Operación *</td>'+
                                 '<td colspan="2"><input type="number" id="formapago_numerooperacion'+num+'"/></td>'+
                               '</tr>'+
                               '<tr>'+
                                 '<td>Fecha / Hora *</td>'+
                                 '<td><input type="date" id="formapago_fecha'+num+'"/></td>'+
                                 '<td><input type="time" value="00:00" id="formapago_hora'+num+'"/></td>'+
                               '</tr>'+
                               '<tr>'+
                                 '<td>Monto *</td>'+
                                 '<td colspan="2"><input type="number" id="formapago_montodeposito'+num+'" onkeyup="calcular_montoformapago();" step="0.01"/></td>'+
                               '</tr>'+
                               '<tr>'+
                                 '<td>Voucher *</td>'+
                                 '<td colspan="2">'+
                                   '<div class="file-input">'+
                                     '<label for="formapago_voucher'+num+'">'+
                                       '<i class="fa fa-upload"></i> Subir Voucher'+
                                       '<p id="file-result-formapago_voucher'+num+'"></p>'+
                                     '</label>'+
                                     '<input type="file" id="formapago_voucher'+num+'" class="file">'+
                                   '</div>'+
                                 '</td>'+
                               '</tr>'+
                             '</tbody>'+
                           '</table>'+
                         '</td><td><a href="javascript:;" onclick="eliminar_formapago('+num+')" class="btn btn-danger"><i class="fa fa-trash"></i> Quitar</a></td>'+
                       '</tr>';

        $("#tabla-formapago > tbody#tbody-formapago").append(nuevaFila);
        $("#tabla-formapago > tbody#tbody-formapago").attr('num',parseInt(num)+1);  
      
        $('#formapago_idcuentabancaria'+num).select2({
            placeholder: '-- Seleccionar Cuenta Bancaria --',
            minimumResultsForSearch: -1
        });
      
        // subir voucher
        file({click:'#formapago_voucher'+num});
      
        
    }
  
    function eliminar_formapago(num){
        $("#tabla-formapago > tbody#tbody-formapago > tr#"+num).remove();
        calcular_montoformapago();
    }
  
    function calcular_montoformapago(){
        var total = 0;
        $("#tabla-formapago > tbody#tbody-formapago > tr").each(function() {
            var num = $(this).attr('id');      
            var formapago_montodeposito =  $('#formapago_montodeposito'+num).val()!=''?$('#formapago_montodeposito'+num).val():0;
            total = total+parseFloat(formapago_montodeposito);
        });

        $('#totalmonto_deposito').html(total.toFixed(2));
        var total_redondeado = parseFloat($('#total_redondeado').val());
        var total = total_redondeado-total;
        $('#totalmonto_efectivo').html(parseFloat(total).toFixed(2));
        console.log(parseFloat(total))
        $('.cont-montorecibido').css('display','block');
        $('#cont-vuelto').css('display','block');
        if(parseFloat(total)<=0){
            $('.cont-montorecibido').css('display','none');
            $('#cont-vuelto').css('display','none');
        }
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAyFtw-yEfh6GtiPyx_4ZQWt3g_vUCu5eQ"></script>
<script>
    function singleMap() {
        var myLatLng = {
            lat: -12.071871667822409,
            lng: -75.21026847919165,
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