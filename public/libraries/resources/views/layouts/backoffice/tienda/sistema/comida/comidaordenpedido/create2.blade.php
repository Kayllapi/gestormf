@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Registrar Pedido',
    'botones'=>[
        'atras:/'.$tienda->id.'/comida/comidaordenpedido: Ir Atras'
    ]
])
<div id="cont-mesas-master" style="float: left;width: 100%;">
<div class="row">
    @if(configuracion($tienda->id,'comida_cantidadmesa')['resultado']=='CORRECTO')
    @for($i=1; $i <= configuracion($tienda->id,'comida_cantidadmesa')['valor']; $i++)
    <div class="col-xs-6 col-sm-3 col-md-2">
    <div class="mesa" onclick="seleccionar_mesa({{$i}})">Mesa {{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</div>
    </div>
    @endfor
    @endif
</div>
</div>
<div id="cont-ordenpedido-master" style="float: left;width: 100%;display:none;">
              <form action="javascript:;" 
                  onsubmit="callback({
                                route:  'backoffice/tienda/sistema/{{ $tienda->id }}/comida/comidaordenpedido',
                                method: 'POST',
                                carga:  '#mx-carga-ordenpedido',
                                data:{
                                    view: 'editarordenpedido',
                                    productos: editar_selectproductos(),
                                }
                            },
                            function(resultado){
                              $('#editar_tbody1').html('');
                              $('#editar_total').val('0.00');
                              $('#editar_numeromesa').val('');
                              removecarga({input:'#mx-carga-ordenpedido'});
                              cambiar_ordenpedido();
                              cargar_ordenpedido();
                              
                            },this)">
                  <input type="hidden" id="numeromesa">
                  <div class="row">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <div onclick="cambiar_mesa()" id="numeromesa_texto" class="mesa"></div>
                    </div>    
                  </div> 
                  <div class="table-responsive">
                     <table class="table" id="editar_tabla-contenido">
                        <thead class="thead-dark">
                          <tr>
                            <th style="text-align: center;">Cantidad</th>
                            <th style="text-align: center;">P. Unitario</th>
                            <th style="text-align: center;">P. Total</th> 
                            <th width="10px"></th> 
                          </tr>
                          <tr>
                              <td colspan="4" class="mx-td-input">
                                <select id="editar_idproducto">
                                    <option></option>
                                </select>
                              </td>
                          </tr>
                        </thead>
                        <tbody num="0" id="editar_tbody1"></tbody>
                        <tbody num="0" id="editar_tbodycarga"></tbody>
                     </table>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <div style="font-weight: bold;font-size: 18px;">Total:</div>
                        <input type="text" id="editar_total" value="0.00" style="font-size: 30px; font-weight: bold; padding-top: 5px; padding-bottom: 5px; text-align: center;" disabled>
                    </div>    
                  </div> 
                  <button type="submit" class="btn mx-btn-post">Actualizar Pedido</button>
              </form> 
</div>
<style>
  .mesa {
      background-color: <?php echo  configuracion($tienda->id,'sistema_color')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_color')['valor']:'#31353d' ?>;
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
<div id="mx-carga-ordenpedido">
<div class="tabs-container" id="tab-comidaordenpedido">
    <ul class="tabs-menu">
        <li class="current"><a href="#tab-comidaordenpedido-1" id="tab-nuevopedido">Nuevo Pedido</a></li>
        <li><a href="#tab-comidaordenpedido-2" id="tab-pedidopendiente">Pedidos Pendientes</a></li>
    </ul>
    <div class="tab">
        <div id="tab-comidaordenpedido-1" class="tab-content" style="display: block;">
            <form action="javascript:;" 
                  onsubmit="callback({
                                route:  'backoffice/tienda/sistema/{{ $tienda->id }}/comida/comidaordenpedido',
                                method: 'POST',
                                carga:  '#mx-carga-ordenpedido',
                                data:{
                                    view: 'registrarordenpedido',
                                    productos: selectproductos(),
                                }
                            },
                            function(resultado){
                              $('#tbody1').html('');
                              $('#total').val('0.00');
                              $('#numeromesa').val(null).trigger('change');
                              removecarga({input:'#mx-carga-ordenpedido'});
                            
                              $('#modal-ordenarpedido').css('display','block');
                              $('#contenido-producto-ordenarpedido').html('<iframe src=\'{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidaordenpedido') }}/'+resultado['idordenpedido']+'/edit?view=ticketpdf#zoom=130\' frameborder=\'0\' width=\'100%\' height=\'600px\'></iframe>'); 
                            },this)">
                  <div class="table-responsive">
                     <table class="table" id="tabla-contenido">
                        <thead class="thead-dark">
                          <tr>
                            <th style="text-align: center;">Cantidad</th>
                            <th style="text-align: center;">P. Unitario</th>
                            <th style="text-align: center;">P. Total</th> 
                            <th width="10px"></th> 
                          </tr>
                          <tr>
                              <td colspan="4" class="mx-td-input">
                                <select id="idproducto">
                                    <option></option>
                                </select>
                              </td>
                          </tr>
                        </thead>
                        <tbody num="0" id="tbody1"></tbody>
                        <tbody num="0" id="tbodycarga"></tbody>
                     </table>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <div style="font-weight: bold;font-size: 18px;">Total:</div>
                        <input type="text" id="total" value="0.00" style="font-size: 30px; font-weight: bold; padding-top: 5px; padding-bottom: 5px; text-align: center;" disabled>
                    </div>    
                  </div> 
                  <div class="row">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <label>Número de Mesa</label>
                        <select id="numeromesa">
                            <option></option>
                            @if(configuracion($tienda->id,'comida_cantidadmesa')['resultado']=='CORRECTO')
                            @for($i=1; $i < configuracion($tienda->id,'comida_cantidadmesa')['valor']; $i++)
                            <option value="{{$i}}">Mesa {{ str_pad($i, 2, "0", STR_PAD_LEFT) }}</option>
                            @endfor
                            @endif
                        </select>
                    </div>    
                  </div> 
                  <button type="submit" class="btn mx-btn-post">Ordenar Pedido</button>
            </form> 
        </div>
        <div id="tab-comidaordenpedido-2" class="tab-content" style="display: none;">
                
        </div>
    </div>
</div>
</div>
@endsection
@section('htmls')
<!--  modal ordenarpedido --> 
<div class="main-register-wrap modal-ordenarpedido" id="modal-ordenarpedido">
    <div class="main-overlay"></div>
    <div class="main-register-holder" style="margin: 10px auto 50px;">
        <div class="main-register fl-wrap">
            <div class="cont-confirm" style="margin-top: 15px;">
            <div class="confirm"><i class="fa fa-check"></i></div>
            <div class="confirm-texto">¡Correcto!</div>
            <div class="confirm-subtexto">Se ha registrado correctamente.</div></div>
            <div class="custom-form" style="text-align: center;margin-bottom: 5px;">
            <button type="button" class="btn big-btn color-bg flat-btn mx-realizar-pago" style="margin: auto;float: none;" onclick="realizar_nuevo_pedido()">
            <i class="fa fa-check"></i> Realizar Nuevo Pedido</button></div>
            <div class="custom-form" style="text-align: center;margin-bottom: 5px;">
            <button type="button" class="btn big-btn color-bg flat-btn" style="margin: auto;float: none;" onclick="irapedidopendiente()">
            <i class="fa fa-check"></i> Ir a Pedidos Pendientes</button></div>
            <div id="contenido-producto-ordenarpedido"></div>
        </div>
    </div>
</div>
<!--  fin modal ordenarpedido --> 
<!--  modal ordenpedido --> 
<div class="main-register-wrap modal-ordenpedido" id="modal-ordenpedido">
    <div class="main-overlay"></div>
    <div class="main-register-holder">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Orden de Pedido</h3>
            <div class="mx-modal-cuerpo">
                  <div id="contenido-producto"></div>
            </div>
        </div>
    </div>
</div>
<!--  fin modal ordenpedido --> 
@endsection
@section('subscripts')
<script>
  tab({click:'#tab-comidaordenpedido'});
  modal({click:'#modal-ordenarpedido'});
  modal({click:'#modal-ordenpedido'});
  
function seleccionar_mesa(numeromesa){
    $("#numeromesa").val(numeromesa);
    $("#numeromesa_texto").html('Mesa '+numeromesa);
    $('#cont-mesas-master').css('display','none');
    $('#cont-ordenpedido-master').css('display','block');
}
function cambiar_mesa(numeromesa){
    $("#numeromesa").val('');
    $("#numeromesa_texto").html('');
    $('#cont-mesas-master').css('display','block');
    $('#cont-ordenpedido-master').css('display','none');
}  
function realizar_nuevo_pedido(){
    cargar_ordenpedido();
    $('#iframeventa').html('');
    $('#modal-ordenarpedido').css('display','none');
    $('#editar_idproducto').select();
}
function irapedidopendiente(){
    cargar_ordenpedido();
    $('#iframeventa').html('');
    $('#modal-ordenarpedido').css('display','none');
    $('#tab-pedidopendiente').click();
}
  
  cargar_ordenpedido();
 function cargar_ordenpedido(){
        $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidaordenpedido/show-ordenpedido')}}",
            type:'GET',
            beforeSend: function (data) {
                load('#cont-mesas');
            },
            success: function (respuesta){
                $("#cont-mesas").html(respuesta['ordenpedido']);
                $("div#menu-opcion").on("click", function () {
                    $("ul",this).toggleClass("hu-menu-vis");
                    $("i",this).toggleClass("fa-angle-up");
                });
            }
        })
 }
 function finalizarpedido(idordenpedido){
        $('#modal-ordenpedido').css('display','block');
        $('#finalizar_idordenpedido').val(idordenpedido);

        $('#contenido-producto').html('<div id="iframeventa"><iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidaordenpedido') }}/'+idordenpedido+'/edit?view=ticketpdf#zoom=130" frameborder="0" width="100%" height="600px"></iframe></div>'); 
 }
  
// Realizar 
$("#idproducto").select2({
    @include('app.select2_producto',[
        'idtienda'=>$tienda->id
    ])
}).on("change", function(e) {
    if(e.currentTarget.value!=''){
        $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/showseleccionarproducto')}}",
            type:'GET',
            data: {
                idproducto : e.currentTarget.value
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
              }else{
                  agregarproducto(
                      respuesta["producto"].id,
                      respuesta["producto"].codigo,
                      respuesta["producto"].nombre,
                      respuesta["producto"].precioalpublico,
                      1,
                  );
              }
            }
        })
    }  
});
  
$("#numeromesa").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
});
function agregarproducto(idproducto,codigo,nombre,precioalpublico,cantidad=1){

      $("#idproducto").val(null).trigger('change');
      var num = $("#tabla-contenido > tbody#tbody1").attr('num');

      var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'">'+
                    '<td colspan="4" style="padding-top: 10px;padding-bottom: 10px;background-color: #dfe2e6;text-align: center;">'+nombre+'</td>'+
                    '</tr>'+
                    '<tr class="num'+num+'"  style="padding-top: 10px;padding-bottom: 10px;background-color: #dfe2e6;">'+
                    '<td class="mx-td-input"><input id="productCant'+num+'" type="number" value="'+cantidad+'" style="text-align: center;" onkeyup="calcularmonto();" onclick="calcularmonto();"></td>'+
                    '<td class="mx-td-input"><input id="productUnidad'+num+'" type="number" value="'+precioalpublico+'" style="text-align: center;" onkeyup="calcularmonto();" onclick="calcularmonto();" step="0.01" min="0" disabled></td>'+
                    '<td class="mx-td-input"><input id="productTotal'+num+'" type="number" value="0.00" step="0.01" style="text-align: center;" min="0" disabled></td>'+       
                    '<td><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+','+idproducto+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>'+
                    '</tr>';
      $("#tabla-contenido > tbody#tbody1").append(nuevaFila);
      $("#tabla-contenido > tbody#tbody1").attr('num',parseInt(num)+1);  
      setTimeout(function(){ $('#productCant'+num).select(); }, 100);
      calcularmonto();
}
function eliminarproducto(num,idproducto){
    $("#tabla-contenido > tbody#tbody1 > tr#"+num).remove();
    $("#tabla-contenido > tbody#tbody1 > tr.num"+num).remove();
    calcularmonto();
}  
function calcularmonto(){
    var total = 0;
    $("#tabla-contenido > tbody#tbody1 > tr").each(function() {
        var num = $(this).attr('id');     
        if(num!=undefined){
            var productCant = parseFloat($("#productCant"+num).val());
            var productUnidad = parseFloat($("#productUnidad"+num).val());
            var subtotal = ((productCant*productUnidad)).toFixed(2);
            $("#productTotal"+num).val(parseFloat(subtotal).toFixed(2));
            total = total+parseFloat((productCant*productUnidad).toFixed(2));
        }  
    });

    var total = (parseFloat(total)).toFixed(2);
    $("#total").val(total);
}
  
function selectproductos(){
    var data = '';
    $("#tabla-contenido > tbody#tbody1 > tr").each(function() {
        var num = $(this).attr('id');     
        if(num!=undefined){   
        var idproducto = $(this).attr('idproducto');
        var productCant = $("#productCant"+num).val();
        var productUnidad = $("#productUnidad"+num).val();
        var productTotal = $("#productTotal"+num).val();
        data = data+'/&/'+idproducto+'/,/'+productCant+'/,/'+productUnidad+'/,/'+productTotal;
        }  
    });
    return data;
} 
  
// Editar
function cambiar_ordenpedido(){
    $("#cont-mesas").css('display','block');
    $("#cont-editar-ordenpedido").css('display','none');
    $("#editar_numeromesa").val('');
    $("#editar_idordenpedido").val('');
    $("#editar_tabla-contenido > tbody#editar_tbody1").html('');
}
function editar_ordenpedido(idordenpedido,numeromesa,ordenpedidodetalle){
    $("#cont-mesas").css('display','none');
    $("#cont-editar-ordenpedido").css('display','block');
    $("#editar_numeromesa").val(numeromesa);
    $("#editar_idordenpedido").val(idordenpedido);

    $.each(ordenpedidodetalle, function( key, value ) {
                  editar_agregarproducto(
                      value.idproducto,
                      value.codigo,
                      value.producto,
                      value.precio,
                      value.cantidad,
                  );
    });
}
  
$("#editar_idproducto").select2({
    @include('app.select2_producto',[
        'idtienda'=>$tienda->id
    ])
}).on("change", function(e) {
    if(e.currentTarget.value!=''){
        $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/venta/showseleccionarproducto')}}",
            type:'GET',
            data: {
                idproducto : e.currentTarget.value
            },
            beforeSend: function (data) {
                var nuevaFila='<tr style="background-color: #008cea;color: #fff;">';
                                nuevaFila+='<td id="tdcargaproducto"" colspan="9" class="tddescuento"></td>';
                                nuevaFila+='</tr>';
                $("#editar_tabla-contenido > tbody#editar_tbodycarga").html(nuevaFila);
                load('#tdcargaproducto');
            },
            success: function (respuesta){
              $("#editar_tabla-contenido > tbody#editar_tbodycarga").html('');
              if(respuesta["resultado"]=='ERROR'){
              }else{
                  editar_agregarproducto(
                      respuesta["producto"].id,
                      respuesta["producto"].codigo,
                      respuesta["producto"].nombre,
                      respuesta["producto"].precioalpublico,
                      1,
                  );
              }
            }
        })
    }  
});
  
function editar_agregarproducto(idproducto,codigo,nombre,precioalpublico,cantidad=1){

      $("#editar_idproducto").val(null).trigger('change');
      var num = $("#editar_tabla-contenido > tbody#editar_tbody1").attr('num');

      var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'">'+
                    '<td colspan="4" style="padding-top: 10px;padding-bottom: 10px;background-color: #dfe2e6;text-align: center;">'+nombre+'</td>'+
                    '</tr>'+
                    '<tr class="num'+num+'"  style="padding-top: 10px;padding-bottom: 10px;background-color: #dfe2e6;">'+
                    '<td class="mx-td-input"><input id="editar_productCant'+num+'" type="number" value="'+cantidad+'" style="text-align: center;" onkeyup="editar_calcularmonto();" onclick="editar_calcularmonto();"></td>'+
                    '<td class="mx-td-input"><input id="editar_productUnidad'+num+'" type="number" value="'+precioalpublico+'" style="text-align: center;" onkeyup="editar_calcularmonto();" onclick="editar_calcularmonto();" step="0.01" min="0" disabled></td>'+
                    '<td class="mx-td-input"><input id="editar_productTotal'+num+'" type="number" value="0.00" step="0.01" style="text-align: center;" min="0" disabled></td>'+       
                    '<td><a id="del'+num+'" href="javascript:;" onclick="editar_eliminarproducto('+num+','+idproducto+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>'+
                    '</tr>';
      $("#editar_tabla-contenido > tbody#editar_tbody1").append(nuevaFila);
      $("#editar_tabla-contenido > tbody#editar_tbody1").attr('num',parseInt(num)+1);  

      
      setTimeout(function(){ $('#editar_productCant'+num).select(); }, 100);
      editar_calcularmonto();
}
function editar_eliminarproducto(num,idproducto){
    $("#editar_tabla-contenido > tbody#editar_tbody1 > tr#"+num).remove();
    $("#editar_tabla-contenido > tbody#editar_tbody1 > tr.num"+num).remove();
    editar_calcularmonto();
}  
function editar_calcularmonto(){
    var total = 0;
    $("#editar_tabla-contenido > tbody#editar_tbody1 > tr").each(function() {
        var num = $(this).attr('id');     
        if(num!=undefined){
            var productCant = parseFloat($("#editar_productCant"+num).val());
            var productUnidad = parseFloat($("#editar_productUnidad"+num).val());
            var subtotal = ((productCant*productUnidad)).toFixed(2);
            $("#editar_productTotal"+num).val(parseFloat(subtotal).toFixed(2));
            total = total+parseFloat((productCant*productUnidad).toFixed(2));
        }  
    });

    var total = (parseFloat(total)).toFixed(2);
    $("#editar_total").val(total);
}
  
function editar_selectproductos(){
    var data = '';
    $("#editar_tabla-contenido > tbody#editar_tbody1 > tr").each(function() {
        var num = $(this).attr('id');     
        if(num!=undefined){   
        var idproducto = $(this).attr('idproducto');
        var productCant = $("#editar_productCant"+num).val();
        var productUnidad = $("#editar_productUnidad"+num).val();
        var productTotal = $("#editar_productTotal"+num).val();
        data = data+'/&/'+idproducto+'/,/'+productCant+'/,/'+productUnidad+'/,/'+productTotal;
        }  
    });
    return data;
} 
</script>
@endsection