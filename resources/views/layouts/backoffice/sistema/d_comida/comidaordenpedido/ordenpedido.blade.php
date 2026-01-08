<div id="mx-carga-ordenpedido">
                      <form action="javascript:;" 
                          onsubmit="callback({
                                        route:  'backoffice/tienda/sistema/{{ $tienda->id }}/comida/comidaordenpedido',
                                        method: 'POST',
                                        carga:  '#mx-carga-ordenpedido',
                                        data:{
                                            view: 'registrarordenpedido',
                                            productos: selectproductos(),
                                            numeromesa: {{$numeromesa}},
                                            idordenpedido: {{$pedido!=''?$pedido->id:0}},
                                        }
                                    },
                                    function(resultado){
                                      //console.log(resultado);
                                      removecarga({input:'#mx-carga-ordenpedido'});
                                      //cargar_mesa();
                                      if(resultado['idordenpedido']!=0){
                                          modal_ordenpedido(resultado['idordenpedido'],resultado['cantidad_pedido']);
                                      }
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
                              <button type="submit" class="btn mx-btn-post">Comandar Pedido</button> 
                      </form> 
</div>
<style>
.mycheck {
      border: 3px solid red;
      background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
      background-size: 100% 100%;
      background-position: center;
      background-repeat: no-repeat;
      width: 300px;
      height: 300px;
      appearance: none;
      display: inline-block;
      vertical-align: middle;
      background-origin: border-box;
      padding: 0;
      user-select: none;
      flex-shrink: 0;
      color: #2563eb;
      background-color: #888;
      border-color: #6b7280;
      border-width: 1px;
    }

    .mycheck {
      cursor: pointer;
      background-color: #fff;
      background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
      width: 40px;
      height: 40px;
      border-radius: 5px;
      appearance: none;
      border: 2px solid #888;
      transition: background-color 0.3s ease-in-out;
      margin-bottom: 20px !important;
      margin-top: 0px;
    }

    .mycheck:checked {
      background-color: #2ecc71;
    }

    .mycheck:focus {
      border-color: rgb(80, 67, 250);
    }

    .mycheck:disabled {
      background-color: rgb(198, 198, 198);
      background-image: none;
    }

    .mycheck:disabled:checked {
      background-color: rgb(198, 198, 198);
      background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
    }


</style>
<script>
  
@foreach($pedidodetalle as $value)
    agregarproducto(
        '{{$value->idproducto}}',
        '{{$value->producto}}',
        '{{$value->precio}}',
        '{{$value->cantidad}}',
        '{{$value->observacion}}',
        '{{$value->idestadoenviococina}}',
        '{{$value->id}}',
    );
@endforeach
function modal_ordenpedido(idordenpedido,cantidad_pedido){
    $('#modal-ordenarpedido').css('display','block');
    var btn_enviarcocina = '';
    var iframe_enviarcocina = '';
    if(cantidad_pedido==1){
        btn_enviarcocina = '<a href="javascript:;" class="btn mx-btn-post" onclick="enviar_cocina('+idordenpedido+')" style="background: #2ecc71;">Enviar a Cocina</a>';
        iframe_enviarcocina = '<iframe id="imprimir-ordenpedido" src=\'{{ url('backoffice/tienda/sistema/'.$tienda->id.'/comida/comidaordenpedido') }}/'+idordenpedido+'/edit?view=ticketpdf#toolbar=0&zoom=130\' frameborder=\'0\' width=\'100%\' height=\'600px\' style=\'overflow-Y:hidden\'></iframe>';
    }
    $('#contenido-producto-ordenarpedido').html('<div class="cont-confirm" style="margin-top: 15px;">'+
                           '<div class="confirm"><i class="fa fa-check"></i></div>'+
                           '<div class="confirm-texto">¡Correcto!</div>'+
                           '<div class="confirm-subtexto">Se ha registrado correctamente.</div></div>'+btn_enviarcocina+
            '<!--a href="javascript:;" class="btn mx-btn-post" onclick="agregar_pedido()" style="background-color: #2ecc71;">Agregar más Pedido</a-->'+
            '<a href="javascript:;" class="btn mx-btn-post" onclick="cambiar_mesa()" style="background-color: #343a40;">Cerrar</a>'+iframe_enviarcocina); 
}
function agregarproducto(idproducto,nombre,precioalpublico,cantidad=1,observacion='',idestadoenviococina=0,idpedidodetalle=0){

      $("#idproducto").val(null).trigger('change');
      var num = $("#tabla-contenido > tbody#tbody1").attr('num');
  
      var estadostyle = '';
      var btnenviarcocina = '<input type="checkbox" class="mycheck" id="enviarcocina'+num+'">';
      var btneliminar = '<a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+','+idproducto+')" class="btn btn-danger big-btn" style="padding: 10px 15px;">'+
                    '<i class="fa fa-close"></i></a>';
      if(idpedidodetalle!=0){
          estadostyle = 'disabled';
          btneliminar = '';
          if(idestadoenviococina==2){
          btnenviarcocina = '<input type="checkbox" class="mycheck" id="enviarcocina'+num+'" checked>';
          }
          else if(idestadoenviococina==3){
          btnenviarcocina = '<input type="checkbox" class="mycheck" id="enviarcocina_enviado'+num+'" disabled checked>';
          }
      }

      var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'">'+
                    '<td colspan="3" style="padding-top: 12px;padding-bottom: 12px;background-color: #dfe2e6;text-align: center;">'+nombre+idestadoenviococina+'</td>'+
                    '<td rowspan="3" style="border-bottom: 2px solid #aaa;background-color: #dfe2e6;">'+
                    btnenviarcocina+
                    btneliminar+
                    '</td>'+
                    '</tr>'+
                    '<tr class="num'+num+'" style="background-color: #dfe2e6;">'+
                    '<td class="mx-td-input" colspan="3">'+
                    '<input id="productObservacion'+num+'" type="text" value="'+observacion+'" placeholder="Observación" onkeyup="texto_mayucula(this)" '+estadostyle+'>'+
                    '</td>'+
                    '</tr>'+
                    '<tr class="num'+num+'" style="background-color: #dfe2e6;">'+
                    '<td class="mx-td-input" style="border-bottom: 2px solid #aaa;"><input id="productCant'+num+'" type="number" value="'+cantidad+'" style="text-align: center;" onkeyup="calcularmonto();" onclick="calcularmonto();" '+estadostyle+'></td>'+
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
        var enviarcocina = $("#enviarcocina"+num+':checked').val();
        var enviarcocina_enviado = $("#enviarcocina_enviado"+num+':checked').val();
        data = data+'/&/'+idproducto+'/,/'+productCant+'/,/'+productUnidad+'/,/'+productTotal+'/,/'+productObservacion+'/,/'+enviarcocina+'/,/'+enviarcocina_enviado;
        }  
    });
    return data;
} 
</script>