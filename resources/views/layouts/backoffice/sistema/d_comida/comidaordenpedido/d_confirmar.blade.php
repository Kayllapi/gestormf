<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Confirmar Orden de Pedido</span>
      <a class="btn btn-success" href="javascript:;" onclick="index_ordenpedido()"><i class="fa fa-angle-left"></i> Atras</a>
    </div>
</div>

<div id="mx-carga-ordenpedido">
  <form class="js-validation-signin px-30" 
        action="javascript:;" 
        onsubmit="callback({
                      route:  'backoffice/tienda/sistema/{{ $tienda->id }}/comidaordenpedido/{{ $ordenpedido->id }}',
                      method: 'PUT',
                      carga:  '#mx-carga-ordenpedido',
                      data:{
                          view: 'confirmarordenpedido'
                      }
                  },
                  function(resultado){
                    index_ordenpedido();
                  },this)">
    <div class="profile-edit-container">
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
      </div>
      <div class="custom-form">
        <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
      </div>
    </div>
  </form> 
</div>

<script>
@foreach ($ordenpedidodetalles as $value)
agregarproductoConfirmar(
  '{{ $value->productocodigo }}',
  '{{ $value->productonombre }}',
  '{{ $value->cantidad }}',
  '{{ $value->precio }}',
  '{{ $value->total }}',
  '{{ $value->idproducto }}',
);
@endforeach
function agregarproductoConfirmar(codigo, nombre, cantidad, precio, total, idproducto) {
  var num = $("#tabla-contenido > tbody").attr('num');
  var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'">';
      nuevaFila+='<td>'+codigo+'</td>';
      nuevaFila+='<td>'+nombre+'</td>';
      nuevaFila+='<td class="mx-td-input"><input id="productCant'+num+'" type="number" value="'+cantidad+'" disabled></td>';
      nuevaFila+='<td class="mx-td-input"><input id="productUnidad'+num+'" type="number" value="'+precio+'" step="0.01" min="0" disabled></td>';
      nuevaFila+='<td class="mx-td-input"><input id="productTotal'+num+'" type="number" value="'+total+'" step="0.01" min="0" disabled></td>';       
      nuevaFila+='</tr>';
  $("#tabla-contenido > tbody").append(nuevaFila);
  $("#tabla-contenido > tbody").attr('num',parseInt(num)+1);
  calcularmonto();
}
function calcularmonto() {
    var total = 0;
    $("#tabla-contenido > tbody > tr").each(function() {
        var num = $(this).attr('id');        
        var productCant = parseFloat($("#productCant"+num).val());
        var productUnidad = parseFloat($("#productUnidad"+num).val());
        var subtotal = ((productCant*productUnidad)).toFixed(2);
        $("#productTotal"+num).val(parseFloat(subtotal).toFixed(2));
        total = total+parseFloat((productCant*productUnidad).toFixed(2));
    });
    var totalfinal = (parseFloat(total)).toFixed(2);
    $("#subtotal").val(totalfinal);
}
</script>