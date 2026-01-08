@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Ordenes de Pedido',
])
<div id="load-ordenpedido" style="float: left;width: 100%;"></div>
<div id="cont-mesas-master" style="float: left;width: 100%;"></div>
<div id="cont-ordenpedido-master" style="float: left;width: 100%;display:none;">
    <div id="mx-carga-ordenpedido">
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
                              removecarga({input:'#mx-carga-ordenpedido'});
                              cambiar_mesa();
                              if(resultado['idordenpedido']!=0){
                                  modal_ordenpedido(resultado['idordenpedido']);
                              }
                            },this)">
                  <input type="hidden" id="numeromesa">
                  <input type="hidden" id="idordenpedido">
                  <div class="row">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <div onclick="cambiar_mesa()" id="numeromesa_texto" class="mesa" style="background-color: <?php echo  configuracion($tienda->id,'sistema_color')['resultado']=='CORRECTO'?configuracion($tienda->id,'sistema_color')['valor']:'#31353d' ?>;"></div>
                    </div>    
                  </div> 
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
                  <button type="submit" class="btn mx-btn-post">Ordenar Pedido</button>
              </form> 
    </div>
</div>
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
@endsection
@section('htmls')
<!--  modal ordenarpedido --> 
<div class="main-register-wrap modal-ordenarpedido" id="modal-ordenarpedido">
    <div class="main-overlay"></div>
    <div class="main-register-holder" style="margin: 10px auto 50px;">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Orden de Pedido</h3>
            <div class="mx-modal-cuerpo">
            <div id="contenido-producto-ordenarpedido"></div>
            </div>
        </div>
    </div>
</div>
<!--  fin modal ordenarpedido --> 
@endsection
@section('subscripts')
<script>
tab({click:'#tab-comidaordenpedido'});
modal({click:'#modal-ordenarpedido'});
  
cargar_mesa(); 
function cargar_mesa(){
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidaordenpedido/show-cargarmesa')}}",
        type:'GET',
        beforeSend: function (data) {
            load('#cont-mesas-master');
        },
        success: function (respuesta){
            $("#cont-mesas-master").html(respuesta['mesas']);
        }
    })
}  
function seleccionar_mesa(numeromesa,idordenpedido){
    $("#numeromesa").val(numeromesa);
    $("#idordenpedido").val(idordenpedido);
    $("#numeromesa_texto").html('Mesa '+numeromesa);
    $('#cont-mesas-master').css('display','none');
  
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidaordenpedido/show-cargarordenpedido')}}",
        type:'GET',
        data: {
            idordenpedido : idordenpedido
        },
        beforeSend: function (data) {
            load('#load-ordenpedido');
        },
        success: function (respuesta){
            $("#load-ordenpedido").html('');
            $('#cont-ordenpedido-master').css('display','block');
            $("#tabla-contenido > tbody#tbody1").html('');
            $.each(respuesta['ordenpedidodetalle'], function( key, value ) {
                  agregarproducto(
                      value.idproducto,
                      value.codigo,
                      value.producto,
                      value.precio,
                      value.cantidad,
                      value.observacion,
                  );
            });
        }
    })
}
function cambiar_mesa(){
    $("#numeromesa").val('');
    $("#numeromesa_texto").html('');
    $("#total").val('0.00');
    $('#cont-mesas-master').css('display','block');
    $('#cont-ordenpedido-master').css('display','none');
    $('#tbody1').html('');  
    cargar_mesa(); 
}  
function modal_ordenpedido(idordenpedido){
    $('#modal-ordenarpedido').css('display','block');
    $('#contenido-producto-ordenarpedido').html('<iframe src=\'{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidaordenpedido') }}/'+idordenpedido+'/edit?view=ticketpdf#zoom=130\' frameborder=\'0\' width=\'100%\' height=\'600px\'></iframe>'); 
}

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
                  );
              }
            }
        })
    }  
});
  
function agregarproducto(idproducto,codigo,nombre,precioalpublico,cantidad=1,observacion=''){

      $("#idproducto").val(null).trigger('change');
      var num = $("#tabla-contenido > tbody#tbody1").attr('num');

      var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'">'+
                    '<td colspan="3" style="padding-top: 12px;padding-bottom: 12px;background-color: #dfe2e6;text-align: center;">'+nombre+'</td>'+
                    '<td rowspan="3" style="border-bottom: 2px solid #aaa;background-color: #dfe2e6;"><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+','+idproducto+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>'+
                    '</tr>'+
                    '<tr class="num'+num+'" style="background-color: #dfe2e6;">'+
                    '<td class="mx-td-input" colspan="3"><input id="productObservacion'+num+'" type="text" value="'+observacion+'" placeholder="Observación" onkeyup="texto_mayucula(this)"></td>'+
                    '</tr>'+
                    '<tr class="num'+num+'" style="background-color: #dfe2e6;">'+
                    '<td class="mx-td-input" style="border-bottom: 2px solid #aaa;"><input id="productCant'+num+'" type="number" value="'+cantidad+'" style="text-align: center;" onkeyup="calcularmonto();" onclick="calcularmonto();"></td>'+
                    '<td class="mx-td-input" style="border-bottom: 2px solid #aaa;"><input id="productUnidad'+num+'" type="number" value="'+precioalpublico+'" style="text-align: center;" onkeyup="calcularmonto();" onclick="calcularmonto();" step="0.01" min="0" disabled></td>'+
                    '<td class="mx-td-input" style="border-bottom: 2px solid #aaa;"><input id="productTotal'+num+'" type="number" value="0.00" step="0.01" style="text-align: center;" min="0" disabled></td>'+       
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
        var productObservacion = $("#productObservacion"+num).val();
        data = data+'/&/'+idproducto+'/,/'+productCant+'/,/'+productUnidad+'/,/'+productTotal+'/,/'+productObservacion;
        }  
    });
    return data;
} 
</script>
@endsection