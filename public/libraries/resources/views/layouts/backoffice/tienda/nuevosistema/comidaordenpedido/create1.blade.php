<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Orden de Pedido</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_ordenpedido()"><i class="fa fa-angle-left"></i> Pedidos</a>
      <a class="btn btn-success" href="javascript:;" onclick="ir_categorias()" style="display:none;" id="btn_categorias">Categorias</a>
    </div>
</div>

<a class="btn btn-success" href="javascript:;" onclick="ir_mesas()" style="display:none;" id="btn_mesas">Mesas</a>
<a href="javascript:;" id="ir_tablapedido" onclick="ir_tablapedido();" style="display: block;">
  <div class="mensaje-success">
    <i class="fa fa-list-ul"></i> Pedidos (0)
  </div>
</a>
<a href="javascript:;" id="ir_realizarpedido" onclick="ir_realizarpedido();" style="display: none;">
  <div class="mensaje-info">
    <i class="fa fa-edit"></i> Realizar Pedido
  </div>
</a>

<div class="profile-edit-container" id="container-tabla-ordenpedido" style="display: none;">
  <div class="statistic-container fl-wrap">
    <div class="table-responsive">
      <table class="table" id="tabla-contenido">
          <thead class="thead-dark">
            <tr>
              <th width="15%">Código</th>
              <th>Producto</th>
              <th width="60px">Cantidad</th>
              <th width="110px">P. Unitario</th>
              <th width="110px">P. Total</th> 
              <th width="10px"></th>
            </tr>
          </thead>
          <tbody num="0">
          </tbody>
      </table>
    </div>
    <div class="row">
      <div class="col-md-4">
      </div>
      <div class="col-md-4">
        <div style="font-weight: bold;font-size: 18px;">Total:</div>
        <input type="text" id="subtotal" value="0.00" style="font-size: 30px; font-weight: bold; padding-top: 5px; padding-bottom: 5px; text-align: center;" disabled>
      </div>    
    </div> 
    <div class="custom-form">
      <a href="javascript:;" class="log-submit-btn mx-realizar-pago" onclick="enviar_ordenpedido()"><span>Enviar el Pedido</span> <i class="fa fa-angle-right"></i></a>
    </div>
  </div>
</div>

<div class="profile-edit-container" id="container-realizar-ordenpedido" style="display: block;">
    <div class="statistic-container fl-wrap">
        @foreach($pisos as $valuepiso)
            <a href="javascript:;" class="statistic-item-wrap" onclick="seleccionar_piso({{$valuepiso->id}},'Pisos')" id="numero_piso">
                <div class="statistic-item gradient-bg fl-wrap">
                    <i class="fa fa-home"></i>
                    <div class="statistic-item-numder" style="font-size: 32px;text-align: center;">Piso {{str_pad($valuepiso->nombre, 2, "0", STR_PAD_LEFT)}}</div>
                </div>
            </a>
            <div class="cont_ambiente" id="cont_ambiente_{{$valuepiso->id}}" style="display:none;">
            <?php
            $ambientes = DB::table('s_comida_ambiente')
                ->where([
                  ['s_comida_ambiente.idtienda', $tienda->id],
                  ['s_comida_ambiente.idpiso', $valuepiso->id]
                ])
                ->get();
            ?>
            @foreach($ambientes as $valueambiente)
                <a href="javascript:;" class="statistic-item-wrap" onclick="seleccionar_ambiente({{$valueambiente->id}},'Ambientes')" id="numero_ambiente">
                    <div class="statistic-item gradient-bg fl-wrap">
                        <i class="fa fa-home"></i>
                        <div class="statistic-item-numder" style="font-size: 32px;text-align: center;">Ambiente {{str_pad($valueambiente->nombre, 2, "0", STR_PAD_LEFT)}}</div>
                    </div>
                </a>
                <div class="cont_mesa" id="cont_mesa_{{$valueambiente->id}}" style="display:none;">
                <?php
                $mesas = DB::table('s_comida_mesa')
                    ->where([
                      ['s_comida_mesa.idtienda', $tienda->id],
                      ['s_comida_mesa.idambiente', $valueambiente->id]
                    ])
                    ->get();
                ?>
                @foreach($mesas as $valuemesa)
                    <a href="javascript:;" class="statistic-item-wrap" onclick="seleccionar_mesa({{$valuemesa->id}},'Mesas')" id="numero_mesa">
                        <div class="statistic-item gradient-bg fl-wrap">
                            <i class="fa fa-utensils"></i>
                            <div class="statistic-item-numder" style="font-size: 32px;text-align: center;">Mesa {{str_pad($valuemesa->numero_mesa, 2, "0", STR_PAD_LEFT)}}</div>
                        </div>
                    </a>
                    <div class="cont_categoria" id="cont_categoria_{{$valuemesa->id}}" style="display:none;">
                        <?php
                        $categorias = DB::table('s_categoria')
                            ->where('s_categoria.idtienda',$tienda->id)
                            ->where('s_categoria.s_idcategoria',0)
                            ->orderBy('s_categoria.nombre','asc')
                            ->get();
                        ?>
                        @foreach($categorias as $valuecategoria)
                            <a href="javascript:;" class="statistic-item-wrap" onclick="seleccionar_categoria('{{$valuemesa->id}}{{$valuecategoria->id}}','Categorias')" id="numero_categoria">
                                <div class="statistic-item gradient-bg fl-wrap" style="background: -webkit-linear-gradient(top, #33d928, #17782c);">
                                    <i class="fa fa-utensils"></i>
                                    <div class="statistic-item-numder" style="font-size: 25px;text-align: center;">{{$valuecategoria->nombre}}</div>
                                </div>
                            </a>

                            <div class="cont_producto" id="cont_producto_{{$valuemesa->id}}{{$valuecategoria->id}}" style="display:none;">
                                <?php
                                $productos = DB::table('s_producto')
                                    ->where('s_producto.idtienda',$tienda->id)
                                    ->where('s_producto.s_idcategoria1',$valuecategoria->id)
                                    ->orderBy('s_producto.nombre','asc')
                                    ->get();
                                ?>
                                @foreach($productos as $valueproducto)
                                <a href="javascript:;" class="statistic-item-wrap" id="modal-agregarproducto" onclick="seleccionar_producto(
                                                                                                                       {{$valueproducto->id}},
                                                                                                                       '{{$valueproducto->nombre}}',
                                                                                                                       '{{$valueproducto->precioalpublico}}',
                                                                                                                       {{$valuepiso->id}},
                                                                                                                       {{$valueambiente->id}},
                                                                                                                       {{$valuemesa->id}},
                                                                                                                       )">
                                    <div class="statistic-item gradient-bg fl-wrap" style="background: -webkit-linear-gradient(top, #2c3b5a, #008cea);">
                                        <i class="fa fa-utensils"></i>
                                        <div class="statistic-item-numder" style="font-size: 20px;text-align: center;">{{$valueproducto->nombre}}</div>
                                        <h5 style="text-align: center;">S/. {{$valueproducto->precioalpublico}}</h5>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endforeach
                </div>
            @endforeach
            </div>
        @endforeach
    </div>  
</div>
<style>
.statistic-item-wrap {
    width: 50%;
}
</style>

<script>
function ir_tablapedido() {
  $('#ir_tablapedido, #container-realizar-ordenpedido').css('display', 'none');
  $('#ir_realizarpedido, #container-tabla-ordenpedido').css('display', 'block');
}
function ir_realizarpedido() {
  $('#ir_tablapedido, #container-realizar-ordenpedido').css('display', 'block');
  $('#ir_realizarpedido, #container-tabla-ordenpedido').css('display', 'none');
}
// enviando pedidos a la base de datos
function enviar_ordenpedido() {
  
}
</script>
<script>
modal({click:'#modal-agregarproducto'});
function seleccionar_piso(idpiso,nombre){
    $('#cont_ambiente_'+idpiso).css('display','block');
    $('#btn_pisos').css('display','block');
    $('#btn_pisos').html(nombre);
  
    $('a#numero_piso').css('display','none');
    $('a#numero_ambiente').css('display','block');
}
function seleccionar_ambiente(idambiente,nombre){
    $('#cont_mesa_'+idambiente).css('display','block');
    $('#btn_ambientes').css('display','block');
    $('#btn_ambientes').html(nombre);
  
    $('a#numero_ambiente').css('display','none');
    $('a#numero_mesa').css('display','block');
}
function seleccionar_mesa(idmesa,nombre){
    $('#cont_categoria_'+idmesa).css('display','block');
    $('#btn_mesas').css('display','block');
    $('#btn_mesas').html(nombre);
    
    $('a#numero_mesa').css('display','none');
    $('a#numero_categoria').css('display','block');
}
function seleccionar_categoria(idcategoria,nombre){
    $('#cont_producto_'+idcategoria).css('display','block');
    $('#btn_categorias').css('display','block');
    $('#btn_categorias').html(nombre);
  
    $('a#numero_categoria').css('display','none');
    $('a#numero_producto').css('display','block');
}
function seleccionar_producto(idproducto,nombre,precio,idpiso,idambiente,idmesa){
    $('#pedido_producto_nombre').html(nombre);
    $('#pedido_producto_precio').html('S/. '+precio);
  
    $('#producto_precio').val(precio);
    $('#idproducto').val(idproducto);
    $('#idpiso').val(idpiso);
    $('#idambiente').val(idambiente);
    $('#idmesa').val(idmesa);
    $('#cantidadpedido').val(1)
    total_ordenpedido();
}
function ir_pisos(){
    $('#btn_pisos').css('display','none');
    $('#btn_ambientes').css('display','none');
    $('#btn_mesas').css('display','none');
    $('#btn_categorias').css('display','none');
  
    $('a#numero_piso').css('display','block');
  
    $('.cont_ambiente').css('display','none');
    $('.cont_mesa').css('display','none');
    $('.cont_categoria').css('display','none');
    $('.cont_producto').css('display','none');
}
function ir_ambientes(){
    $('#btn_ambientes').css('display','none');
    $('#btn_mesas').css('display','none');
    $('#btn_categorias').css('display','none');
  
    $('a#numero_ambiente').css('display','block');
  
    $('.cont_mesa').css('display','none');
    $('.cont_categoria').css('display','none');
    $('.cont_producto').css('display','none');
}
function ir_mesas(){
    $('#btn_mesas').css('display','none');
    $('#btn_categorias').css('display','none');
  
    $('a#numero_mesa').css('display','block');
  
    $('.cont_categoria').css('display','none');
    $('.cont_producto').css('display','none');
}
function ir_categorias(){
    $('#btn_categorias').css('display','none');
  
    $('a#numero_categoria').css('display','block');
  
    $('.cont_producto').css('display','none');
}
// function operador_cantidad(operador){
//   let cantidad = $('#cantidadpedido').val();
//   if(operador == '-'){
//     $('#cantidadpedido').val(parseFloat(cantidad)-1);
//   }else if(operador == '+'){
//     $('#cantidadpedido').val(parseFloat(cantidad)+1);
//   }
//   total_ordenpedido();
// }
// function total_ordenpedido(){
//   let precio = $('#producto_precio').val();
//   let cantidad = $('#cantidadpedido').val();
//   $('#totalpedido').val((parseFloat(cantidad) * parseFloat(precio)).toFixed(2));
// }
</script>