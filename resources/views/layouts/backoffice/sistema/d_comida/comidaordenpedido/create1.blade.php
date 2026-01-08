@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Registrar Pedido',
    'botones'=>[
        'atras:/'.$tienda->id.'/comida/comidaordenpedido: Ir Atras'
    ]
])

<div id="cont-mesas">
    <div class="table-responsive">
        <table class="table" id="tabla-mesa">
            <thead class="thead-dark">
              <tr>
                <th>Código</th>
                <th>Mesa</th>
                <th>Tiempo x minutos</th>
                <th>Responsable</th>
                <th width="60px">Total</th>
                <th width="10px"></th>
                <th width="10px"></th>
                <th width="10px" style="padding: 0px;padding-right: 1px;">
                <a href="javascript:;" class="btn  color-bg flat-btn" id="modal-agregarmesa" onclick="limpiar_mesa()"><i class="fa fa-angle-right"></i> Agregar</a>
                </th>
              </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<div id="carga-mesas"></div>
<!-- Tabla orden de pedido -->
<div id="cont-pedido" style="display: none;">
  <a href="javascript:;" onclick="ir_realizarpedido();">
    <div class="mensaje-success">
      <i class="fa fa-table"></i> <span id="cont-nombre-mesa"></span>
    </div>
  </a>
  <div id="mx-carga-ordenpedido">
      <input type="hidden" id="pedido_producto_idpedido">
      <div class="statistic-container fl-wrap">
        <div class="table-responsive">
          <table class="table" id="tabla-producto">
              <thead class="thead-dark">
                <tr>
                  <th>Fecha Registro</th>
                  <th>Fecha Comandado</th>
                  <th>Producto</th>
                  <th width="200px">Observación</th>
                  <th width="100px">Tiempo x Minuto</th>
                  <th width="100px">Estado</th>
                  <th width="140px">Cantidad</th>
                  <th width="110px">P. Unitario</th>
                  <th width="110px">P. Total</th> 
                  <th width="10px" style="padding: 0px;padding-right: 1px;">
                  <a href="javascript:;" class="btn  color-bg flat-btn" id="modal-agregarproducto" onclick="limpiar_producto()"><i class="fa fa-angle-right"></i> Agregar</a>
                  </th>
                </tr>
              </thead>
              <tbody num="0">
              </tbody>
          </table>
        </div>
        <div id="carga-productos"></div>
        <div id="cont-producto-total" style="display:none;">
        <div class="row">
          <div class="col-md-4">
          </div>
          <div class="col-md-4">
            <div style="font-weight: bold;font-size: 18px;">Total:</div>
            <input type="text" id="subtotal" value="0.00" style="font-size: 30px; font-weight: bold; padding-top: 5px; padding-bottom: 5px; text-align: center;" disabled>
            
            <div style="font-weight: bold;font-size: 18px;">Observación (opcional)</div>
            <input type="text" id="observacion" value="" style="font-size: 30px; font-weight: bold; padding-top: 5px; padding-bottom: 5px; text-align: center;">
          </div>    
        </div> 
        <div class="custom-form">
          <a href="javascript:;" class="btn mx-btn-post" onclick="enviar_ordenpedido()"><span>Finalizar Pedido</span></a>
        </div>
        </div>
      </div>
  </div>
</div>
<div id="cont-pedido-eliminar" style="display: none;">
    <div class="mensaje-warning">
        <i class="fa fa-warning"></i> ¿Esta seguro de Eliminar la Mesa?
    </div>
    <a href="javascript:;" onclick="mesa_eliminar()" class="btn mx-btn-post">
      Eliminar
    </a>
</div>

@endsection
@section('htmls')
<!--  modal agregarmesa --> 
<div class="main-register-wrap modal-agregarmesa" id="modal-agregarmesa">
    <div class="main-overlay"></div>
    <div class="main-register-holder" style="margin: 10px auto 50px;">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Agregar Mesa</h3>
            <div class="mx-modal-cuerpo">
            <div id="cont-mesa-mensaje"></div>
            <div id="cont-mesa-piso">
                <div class="statistic-container fl-wrap">
                    @foreach($pisos as $valuepiso)
                        {{-- @if(count($pisos)>1)--}}
                        <a href="javascript:;" class="statistic-item-wrap" onclick="seleccionar_piso({{$valuepiso->id}},'Piso {{str_pad($valuepiso->nombre, 2, "0", STR_PAD_LEFT)}}')" id="numero_piso">
                            <div class="statistic-item gradient-bg fl-wrap">
                                <i class="fa fa-home"></i>
                                <div class="statistic-item-numder" style="font-size: 20px;text-align: center;">Piso {{str_pad($valuepiso->nombre, 2, "0", STR_PAD_LEFT)}}</div>
                            </div>
                        </a>
                        {{-- @endif--}}
                        <?php
                        $ambientes = DB::table('s_comida_ambiente')
                            ->where([
                              ['s_comida_ambiente.idtienda', $tienda->id],
                              ['s_comida_ambiente.idpiso', $valuepiso->id]
                            ])
                            ->get();
                        ?>
                        <div class="cont_ambiente" id="cont_ambiente_{{$valuepiso->id}}" style="display:none;">
                        @foreach($ambientes as $valueambiente)
                            {{-- @if(count($ambientes)>1) --}}
                            <a href="javascript:;" class="statistic-item-wrap" onclick="seleccionar_ambiente({{$valueambiente->id}},'Ambiente {{str_pad($valueambiente->nombre, 2, "0", STR_PAD_LEFT)}}')" id="numero_ambiente">
                                <div class="statistic-item gradient-bg fl-wrap">
                                    <i class="fa fa-home"></i>
                                    <div class="statistic-item-numder" style="font-size: 20px;text-align: center;">Ambiente {{str_pad($valueambiente->nombre, 2, "0", STR_PAD_LEFT)}}</div>
                                </div>
                            </a>
                            {{--  @endif --}}
                            <div class="cont_mesa" id="cont_mesa_{{$valueambiente->id}}" <?php //echo (count($pisos)==1&&count($ambientes)==1)?'style="display: block;"':'style="display:none;"'?> style="display:none;">
                            <?php
                            $mesas = DB::table('s_comida_mesa')
                                ->where([
                                  ['s_comida_mesa.idtienda', $tienda->id],
                                  ['s_comida_mesa.idambiente', $valueambiente->id]
                                ])
                                ->get();
                            ?>
                            @foreach($mesas as $valuemesa)
                                <a href="javascript:;" class="statistic-item-wrap" onclick="registrar_mesa({{$valuepiso->id}},{{$valueambiente->id}},{{$valuemesa->id}})" id="numero_mesa">
                                    <div class="statistic-item gradient-bg fl-wrap">
                                        <i class="fa fa-utensils"></i>
                                        <div class="statistic-item-numder" style="font-size: 20px;text-align: center;">Mesa {{str_pad($valuemesa->numero_mesa, 2, "0", STR_PAD_LEFT)}}</div>
                                    </div>
                                </a>
                            @endforeach
                            </div>
                        @endforeach
                        </div>
                    @endforeach
                </div> 
            </div>
            </div>
        </div>
    </div>
</div>
<!--  fin modal agregarmesa -->
<!--  modal ticketmesa --> 
<div class="main-register-wrap modal-ticketmesa" id="modal-ticketmesa">
    <div class="main-overlay"></div>
    <div class="main-register-holder" style="margin: 10px auto 50px;">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Ticket de Pedido</h3>
            <div class="mx-modal-cuerpo">
                <iframe id="iframe-ticket" frameborder="0" width="100%" height="600px"></iframe>
            </div>
        </div>
    </div>
</div>
<!--  fin modal ticketmesa --> 
<!--  modal eliminarmesa --> 
<div class="main-register-wrap modal-eliminarmesa" id="modal-eliminarmesa">
    <div class="main-overlay"></div>
    <div class="main-register-holder" style="margin: 10px auto 50px;">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Agregar Producto</h3>
            <div class="mx-modal-cuerpo">
                <div id="carga-mesa-eliminar">
                      <div class="mensaje-warning">
                        <i class="fa fa-warning"></i> ¿Esta seguro de Eliminar la Mesa?
                      </div>
                      <a href="javascript:;" onclick="mesa_eliminar()" class="btn mx-btn-post" style="float: left;">
                          Eliminar
                      </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  fin modal eliminarmesa --> 
<!--  modal agregarcategoriaproducto --> 
<div class="main-register-wrap modal-agregarproducto" id="modal-agregarproducto">
    <div class="main-overlay"></div>
    <div class="main-register-holder" style="margin: 10px auto 50px;">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Agregar Producto</h3>
            <div class="mx-modal-cuerpo">
            <div id="cont-producto-mensaje"></div>
            <div id="cont-producto-categoria">
                <div id="cont-titulo-categoria" style="display:none;">
                <a href="javascript:;" onclick="limpiar_producto()">
                <div class="mensaje-success">
                  <i class="fa fa-table"></i> <span id="cont-nombre-mesa-categoria"></span>
                </div>
                </a>
                </div>
                <div class="statistic-container fl-wrap">
                        <?php
                        $categorias = DB::table('s_categoria')
                            ->where('s_categoria.idtienda',$tienda->id)
                            ->where('s_categoria.s_idcategoria',0)
                            ->orderBy('s_categoria.nombre','asc')
                            ->get();
                        ?>
                        @foreach($categorias as $valuecategoria)
                            <?php
                            $productos = DB::table('s_producto')
                                ->where('s_producto.idtienda',$tienda->id)
                                ->where('s_producto.s_idcategoria1',$valuecategoria->id)
                                ->orderBy('s_producto.nombre','asc')
                                ->get();
                            ?>
                            @if(count($productos)>0)
                            <a href="javascript:;" class="statistic-item-wrap" onclick="seleccionar_categoria('{{$valuemesa->id}}{{$valuecategoria->id}}','{{$valuecategoria->nombre}}')" id="numero_categoria">
                                <div class="statistic-item gradient-bg fl-wrap" style="background: -webkit-linear-gradient(top, #33d928, #17782c);padding: 20px 10px;">
                                    <i class="fa fa-utensils"></i>
                                    <div class="statistic-item-numder" style="font-size: 16px;text-align: center;">{{$valuecategoria->nombre}}</div>
                                </div>
                            </a>
                            @endif

                            <div class="cont_producto" id="cont_producto_{{$valuemesa->id}}{{$valuecategoria->id}}" style="display:none;">
                                @if(count($productos)>0)
                                @foreach($productos as $valueproducto)
                                <a href="javascript:;" class="statistic-item-wrap" 
                                   onclick="seleccionar_producto(
                                                                 {{$valueproducto->id}},
                                                                 '{{$valueproducto->nombre}}',
                                                                 '{{$valueproducto->precioalpublico}}',
                                                                 )">
                                    <div class="statistic-item gradient-bg fl-wrap" style="background: -webkit-linear-gradient(top, #2c3b5a, #008cea);padding: 20px 10px;">
                                        <i class="fa fa-utensils"></i>
                                        <div class="statistic-item-numder" style="font-size: 16px;text-align: center;">{{$valueproducto->nombre}}</div>
                                        <h5 style="text-align: center;">S/. {{$valueproducto->precioalpublico}}</h5>
                                    </div>
                                  </a>
                                @endforeach
                                @else
                                <div class="mensaje-warning">
                                  No hay ningún producto!!.
                                </div>
                                @endif
                            </div>
                        @endforeach
                </div> 
            </div> 
            <div id="cont-producto-cantidad" style="display:none;">
                <div class="statistic-container fl-wrap">
                    <a href="javascript:;" class="statistic-item-wrap" style="width: 100%;">
                       <div class="statistic-item gradient-bg fl-wrap" style="background: -webkit-linear-gradient(top, #2c3b5a, #008cea);">
                           <i class="fa fa-utensils"></i>
                           <div class="statistic-item-numder" style="font-size: 20px;text-align: center;" id="pedido_producto_nombre"></div>
                           <h5 style="text-align: center;" id="pedido_producto_precio">0.00</h5>
                           <input type="hidden" id="pedido_producto_id">
                       </div>
                   </a>
                </div>
                <div class="custom-form">
                    <label>Cantidad *</label>
                    <div class="quantity fl-wrap">
                        <div class="quantity-item">
                            <input type="button" value="-" class="minus" onclick="operador_cantidad('-')">
                            <input type="text" id="pedido_producto_cantidad" class="qty" min="1" step="1" 
                                   value="1" style="padding-left: 0px;" onkeyup="total_ordenpedido()" onclick="total_ordenpedido()">
                            <input type="button" value="+" class="plus" onclick="operador_cantidad('+')">
                        </div>
                    </div>
                    <label>Observación (opcional)</label>
                    <input type="text" id="pedido_producto_observacion">
                    <label>Total </label>
                    <input type="number" id="pedido_producto_total" step="0.01" style="text-align: center;font-size: 20px;font-weight: bold;" disabled>
                    <div class="row">
                      <div class="col-md-6"><button type="button" class="btn mx-btn-post" onclick="registrar_producto()"><i class="fa fa-check"></i> Agregar a Pedido</button> </div>
                      <div class="col-md-6"><button type="button" class="btn big-btn color-bg flat-btn" onclick="limpiar_producto()" style="background-color: #31353d;width:100%;"><i class="fa fa-cart-plus"></i> Seguir Agregando</button></div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
<!--  fin modal agregarcategoriaproducto -->

<!--  modal ordenpedidorealizada --> 
<div class="main-register-wrap modal-ordenpedidorealizada" id="modal-ordenpedidorealizada">
    <div class="main-overlay"></div>
    <div class="main-register-holder" style="margin: 10px auto 50px;">
        <div class="main-register fl-wrap">
            <div class="mx-modal-cuerpo">
            <div id="contenido-ordenpedido"></div>
            </div>
        </div>
    </div>
</div>
<!--  fin modal ordenpedidorealizada --> 
@endsection
@section('subscripts')
<style>
.statistic-item-wrap {
    width: 50%;
}
</style>
<script>
  @if(isset($_GET['idpedido']))
  <?php
      $pedido = DB::table('s_comida_ordenpedido')
                ->join('s_comida_mesa as mesa', 'mesa.id', 's_comida_ordenpedido.idmesa')
                ->where('s_comida_ordenpedido.id',$_GET['idpedido'])
                ->select(
                    'mesa.numero_mesa as nombremesa',
                )
                ->first();
  ?> 
  @if($pedido!='')
  mostrar_producto({{$_GET['idpedido']}},'Mesa {{str_pad($pedido->nombremesa, 2, "0", STR_PAD_LEFT)}}');
  @else
  seleccionar_mesa();
  @endif
  @else
  seleccionar_mesa();
  @endif
  modal({click:'#modal-agregarmesa'});

  
  function seleccionar_piso(idpiso,nombre){
      $('#cont_ambiente_'+idpiso).css('display','block');
      $('a#numero_piso').css('display','none');
      $('a#numero_ambiente').css('display','block');
  }
  function seleccionar_ambiente(idambiente,nombre){
      $('#cont_mesa_'+idambiente).css('display','block');
      $('a#numero_ambiente').css('display','none');
      $('a#numero_mesa').css('display','block');
  }
  
  function limpiar_mesa(){
      $('#cont-mesa-mensaje').html('');
      $('#cont-mesa-piso').css('display', 'block');
      $('a#numero_piso').css('display', 'block');
      $('.cont_ambiente').css('display', 'none');
      $('a#numero_mesa').css('display','block');
  } 
  
  function registrar_mesa(idpiso,idambiente,idmesa){
      load('#cont-mesa-mensaje'); 
      $('#cont-mesa-piso').css('display', 'none');
      callback({
          route: 'backoffice/tienda/sistema/{{ $tienda->id }}/comida/comidaordenpedido',
          method: 'POST',
          data:{
              view: 'registrarmesa',
              idpiso : idpiso,
              idambiente : idambiente,
              idmesa : idmesa
          }
      },
      function(resultado){
          $('#cont-mesa-mensaje').html('<div class="cont-confirm" style="margin-top: 15px;">'+
                           '<div class="confirm"><i class="fa fa-check"></i></div>'+
                           '<div class="confirm-texto">¡Correcto!</div>'+
                           '<div class="confirm-subtexto">Se ha agregado correctamente.</div></div>'+
                           '<div class="row">'+
                           '<div class="col-md-6"><button type="button" class="btn mx-btn-post" onclick="limpiar_mesa()" style="border: 0px;cursor: pointer;margin-bottom: 5px;">'+
                           '<i class="fa fa-plus"></i> Seguir Agregando</button></div>'+
                           '<div class="col-md-6"><button type="button" class="btn big-btn color-bg flat-btn" onclick="location.reload()" style="background-color: #31353d;width:100%;border: 0px;cursor: pointer;">'+
                           '<i class="fa fa-check"></i> Finalizar</button></div>'+
                           '</div>');
      })
  }
  
  function seleccionar_mesa(){
      load("#carga-mesas"); 
      $("#tabla-mesa > tbody").html('');
      $.ajax({
          url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidaordenpedido/show-seleccionarmesa')}}",
          type:'GET',
          success: function (respuesta){
              $("#carga-mesas").html(''); 
              $("#tabla-mesa > tbody").html(respuesta['pedidos']);
              modal({click:'#modal-eliminarmesa'});
              modal({click:'#modal-ticketmesa'});
          }
      })
  }
 function mesa_modal_eliminar(idpedido){
      $('#pedido_producto_idpedido').val(idpedido);
      
  }
  function ticket_producto(idpedido){
      $('#iframe-ticket').attr('src','{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidaordenpedido') }}/'+idpedido+'/edit?view=ticketpdf#zoom=130');
      
  }
  function mesa_eliminar(){
      callback({
          route: 'backoffice/tienda/sistema/{{ $tienda->id }}/comida/comidaordenpedido/'+$('#pedido_producto_idpedido').val(),
          method: 'DELETE',
          carga: '#carga-mesa-eliminar',
          data:{
              view: 'eliminarmesa',
          }
      },
      function(resultado){
          $('#modal-eliminarmesa .close-reg').click();
          $('#pedido_producto_idpedido').val('');
          seleccionar_mesa();
      })
  }

  modal({click:'#modal-agregarproducto'});
  
  function seleccionar_categoria(idcategoria,nombre){
      $('#cont_producto_'+idcategoria).css('display','block');
      $('a#numero_categoria').css('display','none');
      $('a#numero_producto').css('display','block');
      $('#cont-titulo-categoria').css('display','block');
      $('#cont-nombre-mesa-categoria').html(nombre+' - Ir a Categorias');
      
  }
  function seleccionar_producto(idproducto,nombre,precio){
      $('#pedido_producto_nombre').html(nombre);
      $('#pedido_producto_precio').html(precio);
      $('#pedido_producto_id').val(idproducto);
      $('#pedido_producto_cantidad').val(1)
      total_ordenpedido();

      $('#cont-producto-mensaje').html('');
      $('#cont-producto-categoria').css('display', 'none');
      $('#cont-producto-cantidad').css('display', 'block');
  }
  
  function limpiar_producto(){
      $('#cont-producto-mensaje').html('');
      $('#cont-producto-categoria').css('display', 'block');
      $('#cont-producto-cantidad').css('display', 'none');
      $('a#numero_categoria').css('display','block');
      $('.cont_producto').css('display','none');
      $('#cont-titulo-categoria').css('display','none');
      $('#pedido_producto_cantidad').val('1');
      $('#pedido_producto_observacion').val('');
  }
  
  // calculando orden de pedido
    function operador_cantidad(operador){
      let cantidad = $('#pedido_producto_cantidad').val();
      if(operador == '-'){
        $('#pedido_producto_cantidad').val(parseFloat(cantidad)-1);
      }else if(operador == '+'){
        $('#pedido_producto_cantidad').val(parseFloat(cantidad)+1);
      }
      total_ordenpedido();
    }
    function total_ordenpedido(){
      let precio = $('#pedido_producto_precio').html();
      let cantidad = $('#pedido_producto_cantidad').val();
      $('#pedido_producto_total').val((parseFloat(cantidad) * parseFloat(precio)).toFixed(2));
    }
    //detalle
    function operador_cantidad_lista(idpedido,operador,num){
      let cantidad = $('#pedido_producto_cantidad_lista'+num).val();
      let precio = $('#pedido_producto_precio_lista'+num).val();
      if(operador == '-'){
          cantidad = parseFloat(cantidad)-1;
          $('#pedido_producto_cantidad_lista'+num).val(cantidad);
      }else if(operador == '+'){
          cantidad = parseFloat(cantidad)+1;
          $('#pedido_producto_cantidad_lista'+num).val(cantidad);
      }
      calcularmonto();
      registrar_producto_cantidad(idpedido,num,cantidad,precio);
    }
    // fin calculando orden de pedido
  
    // almacenar pedidos
    function registrar_producto() {
      load('#cont-producto-mensaje'); 
      $('#cont-producto-cantidad').css('display', 'none');
      callback({
          route: 'backoffice/tienda/sistema/{{ $tienda->id }}/comida/comidaordenpedido',
          method: 'POST',
          data:{
              view: 'registrarproducto',
              idpedido : $('#pedido_producto_idpedido').val(),
              idproducto : $('#pedido_producto_id').val(),
              precio : $('#pedido_producto_precio').html(),
              cantidad : $('#pedido_producto_cantidad').val(),
              observacion : $('#pedido_producto_observacion').val(),
          }
      },
      function(resultado){
          mostrar_producto(idpedido,nombre='');
          $('#cont-producto-mensaje').html('<div class="cont-confirm" style="margin-top: 15px;">'+
                           '<div class="confirm"><i class="fa fa-check"></i></div>'+
                           '<div class="confirm-texto">¡Correcto!</div>'+
                           '<div class="confirm-subtexto">Se ha agregado correctamente.</div></div>'+
                           '<div class="row">'+
                           '<div class="col-md-6"><button type="button" class="btn mx-btn-post" onclick="limpiar_producto()" style="border: 0px;cursor: pointer;margin-bottom: 5px;">'+
                           '<i class="fa fa-plus"></i> Seguir Agregando</button></div>'+
                           '<div class="col-md-6"><button type="button" class="btn big-btn color-bg flat-btn" onclick="confirm_cerrar(\'#modal-agregarproducto\')" style="background-color: #31353d;width:100%;border: 0px;cursor: pointer;">'+
                           '<i class="fa fa-check"></i> Finalizar</button></div>'+
                           '</div>');
          mostrar_producto($('#pedido_producto_idpedido').val());
      });
      
    }
  
    
    function registrar_producto_cantidad(idpedido,idpedidodetalle,cantidad,precio) {
      callback({
          route: 'backoffice/tienda/sistema/{{ $tienda->id }}/comida/comidaordenpedido',
          method: 'POST',
          data:{
              view: 'registrarproductocantidad',
              idpedido : idpedido,
              idpedidodetalle : idpedidodetalle,
              cantidad : cantidad,
              precio : precio,
          }
      },
      function(resultado){
          
      })
    }
  
    function mostrar_producto(idpedido,nombre='') {
      history.replaceState(null, null, window.location.pathname +'?idpedido='+idpedido);
      $('#cont-mesas').css('display', 'none');
      $('#cont-pedido').css('display', 'block');
      $('#cont-producto-total').css('display','none');
      if(nombre!=''){
          $('#cont-nombre-mesa').html(nombre+' - Seleccionar otra Mesa');
      }
      $("#tabla-producto > tbody").html('');
      $('#pedido_producto_idpedido').val('');
      load("#carga-productos"); 
      $.ajax({
          url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidaordenpedido/show-seleccionarproducto')}}",
          type:'GET',
          data:{
              idpedido : idpedido,
          },
          success: function (respuesta){
            $("#carga-productos").html(''); 
            $('#pedido_producto_idpedido').val(respuesta['pedido'].id);
            if(respuesta['pedidodetalles']!=''){
              $("#tabla-producto > tbody").html(respuesta['pedidodetalles']);
              $('#cont-producto-total').css('display','block');
              $("#subtotal").val(respuesta['pedido'].total);
            }else{
              $("#carga-productos").html('<div class="mensaje-warning">No hay ningún producto!!.</div>');
            }
          }
      })
    }
    function producto_eliminar(idpedidodetalle){
      $("#tabla-producto > tbody").html('');
      $('#cont-producto-total').css('display','none');
      load("#carga-productos"); 
      callback({
          route: 'backoffice/tienda/sistema/{{ $tienda->id }}/comida/comidaordenpedido/'+idpedidodetalle,
          method: 'DELETE',
          data:{
              view: 'registrarproductocantidadeliminar',
              idpedido: $('#pedido_producto_idpedido').val(),
          }
      },
      function(resultado){
            mostrar_producto($('#pedido_producto_idpedido').val());
      })
    }
  
  
    function calcularmonto(){
        var total = 0;
        $("#tabla-producto > tbody > tr").each(function() {
            var num = $(this).attr('id');        
            var productCant = parseFloat($("#pedido_producto_cantidad_lista"+num).val());
            var productUnidad = parseFloat($("#pedido_producto_precio_lista"+num).val());
            var subtotal = ((productCant*productUnidad)).toFixed(2);
            $("#pedido_producto_total_lista"+num).val(parseFloat(subtotal).toFixed(2));
            total = total+parseFloat(parseFloat(subtotal).toFixed(2));
        });
        $("#subtotal").val(parseFloat(total).toFixed(2));
    }
    
    function selectproductos(){
        var data = '';
        $("#tabla-producto > tbody > tr").each(function() {
            var num = $(this).attr('id');        
            var idproducto = $(this).attr('idproducto');
            var productCant = $("#pedido_producto_cantidad_lista"+num).val();
            var productUnidad = $("#pedido_producto_precio_lista"+num).val();
            var productObservacion = $("#pedido_producto_observacion_lista"+num).val();
            data = data+'/&/'+idproducto+'/,/'+productCant+'/,/'+productUnidad+'/,/'+productObservacion;
        });
        return data;
    } 
    // fin almacenar pedidos
</script>
<script>
modal({click:'#modal-ordenpedidorealizada'});
function ir_realizarpedido() {
  $('#cont-mesas').css('display', 'block');
  $('#cont-pedido').css('display', 'none');
  seleccionar_mesa();
  history.replaceState(null, null, window.location.pathname);
}

function enviar_ordenpedido() {
    callback({
        route:  'backoffice/tienda/sistema/{{ $tienda->id }}/comida/comidaordenpedido/'+$("#pedido_producto_idpedido").val(),
        method: 'PUT',
        carga:  '#mx-carga-ordenpedido',
        data:{
            view: 'enviar_pedido',
            selectproductos : selectproductos(),
            observacion: $('#observacion').val()
        }
    },
    function(resultado){
      $('#modal-ordenpedidorealizada').css('display','block');
      $('#contenido-ordenpedido').html('<div class="cont-confirm" style="margin-top: 15px;">'+
                           '<div class="confirm"><i class="fa fa-check"></i></div>'+
                           '<div class="confirm-texto">¡Correcto!</div>'+
                           '<div class="confirm-subtexto">Se ha registrado correctamente.</div>'+
                           '<div style="font-size: 25px;margin-top: 10px;">Código</div>'+
                           '<div style="border: 2px dashed #1aa12c;border-radius: 5px;margin-top: 10px;background-color: #dbffe1;">'+resultado['codigopedido']+'</div></div>'+
                           '<div class="row">'+
                           '<div class="col-md-12"><button type="button" class="btn big-btn color-bg flat-btn" onclick="cerrar_ordenpedido()" style="background-color: #31353d;width:100%;border: 0px;cursor: pointer;">'+
                           '<i class="fa fa-check"></i> Finalizar</button></div>'+
                           '</div>');
    },this)
  
}
function cerrar_ordenpedido(){
    $('#modal-ordenpedidorealizada').css('display','none');
    removecarga({input:'#mx-carga-ordenpedido'});
    seleccionar_mesa();
    ir_realizarpedido();
} 
</script>
@endsection