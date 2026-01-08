<div class="profile-edit-container">
    <div class="custom-form">
       <div class="profile-edit-container">
            <div class="row">
              <div class="col-md-3">
              </div>
               <div class="col-md-6">
                  <input class="form-control" type="text" id="compracodigo" style="text-align: center;" placeholder="Codigo de Compra"/>
               </div>
              </div>
        </div>
        <div id="cont-form-compradevolucion" style="display:none;">
            <form class="js-validation-signin px-30" 
                  action="javascript:;" 
                  onsubmit="callback({
                    route: 'backoffice/tienda/nuevosistema/{{ $tienda->id }}/compradevolucion',         
                    method: 'POST',
                    data:{
                        view: 'registrar',
                        carga: '#carga-devolucioncompra',
                        productos: selectproductos(),
                        idtienda: '{{ $tienda->id }}'
                    }
                    },this)">
                <div class="row">
                    <input type="hidden" id="idcompra" value="0" disabled>
                    <div class="col-sm-6">
                        <label>Proveedor</label>
                        <input type="text" id="proveedor" disabled>
                        <label>Tipo de comprobante</label>
                        <input type="text" id="tipocomprobantenombre" disabled>
                    </div>
                    <div class="col-sm-6">
                        <label>Fecha de compra</label>
                        <input type="text" id="fechaemision" disabled>
                        <label>Motivo de Devolución *</label> 
                        <input type="text" id="motivo" >
                    </div>
                    
                </div>      
                <div class="table-responsive">
                    <table class="table" id="tabla-contenido-compradevolucion">
                        <thead class="thead-dark">
                            <tr>
                                <th rowspan="2">Producto</th>
                                <th colspan="2">COMPRA</th>
                                <th colspan="3">COMPRA DEVOLUCIÓN</th>
                                <th width="10px" rowspan="2"></th>
                            </tr>
                            <tr>
                                <th width="60px">Cantidad</th>
                                <th width="110px">P. Unitario</th>
                                <th width="60px">Cantidad</th>
                                <th width="110px">P. Unitario</th>
                                <th width="110px">P. Total</th> 
                            </tr>
                        </thead>
                        <tbody num="0"></tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-md-4"> 
                        <label>Sub Total</label>
                        <input type="text" id="subtotal" placeholder="0.00" disabled>
                        <label>IGV</label>
                        <input type="text" id="igv" placeholder="0.00" disabled>
                        <label>Total</label>
                        <input type="text" id="total" placeholder="0.00" disabled>
                        <label>Total redondeado</label>
                        <input type="text" id="totalredondeado" placeholder="0.00" disabled>
                    </div>
                    <div class="col-md-4"></div>
                </div>
                <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;" >Guardar Cambios</button>
            </form>
        </div>
       <div id="carga-devolucioncompra"></div>
    </div>
</div>
<script>
$('#compracodigo').keyup( function(e) {
    if(e.keyCode == 13){
        buscar_venta( $('#compracodigo').val())
    }
})
function buscar_venta(compracodigo){
     load('#carga-devolucioncompra'); 
      $('#cont-form-compradevolucion').css('display', 'none');  
      $.ajax({
            url:"{{url('backoffice/tienda/nuevosistema/'.$tienda->id.'/compradevolucion/show_buscarcompra')}}",
            type:'GET',
            data: {
                compracodigo : compracodigo
            },
            success: function (respuesta){
                if(respuesta['compra'] != undefined){
                     $('#cont-form-compradevolucion').css('display', 'block');
                     $('#carga-devolucioncompra').html('');
                     $('#idcompra').val(respuesta['compra'].id);
                     $('#proveedor').val(respuesta['compra'].proveedor);
                     $('#fechaemision').val(respuesta['compra'].fecharegistro);
                     var tipocomprobantenombre = '';
                          if(respuesta['compra'].s_idcomprobante==01){
                              tipocomprobantenombre = 'NOTA DE VENTA';
                          }else if(respuesta['compra'].s_idcomprobante==02){
                              tipocomprobantenombre = 'BOLETA';
                          }else if(respuesta['compra'].s_idcomprobante==03){
                              tipocomprobantenombre = 'FACTURA';
                          }
                     $('#tipocomprobantenombre').val(tipocomprobantenombre);
                     $('#tabla-contenido-compradevolucion > tbody').html(respuesta['compradetalle']);
                    calcularmonto(); 
                }else {
                    $('#carga-devolucioncompra').html(`<div class="alert alert-danger" style="font-size: 20px;
                                                                                                        padding-top: 10px;
                                                                                                        padding-bottom: 10px;
                                                                                                        margin-bottom: 15px;
                                                                                                        margin-top: 5px;">
                                                                    No existe el Código de Compra!
                                                                 </div>`);
                } 
            }
      })
}
  
function calcularmonto(){
         var subtotal  = 0;
         var total     = 0;
         var igv       = 0;
            $("#tabla-contenido-compradevolucion > tbody > tr").each(function() {
               var id        = $(this).attr('id');
               var cantidad  = parseFloat($("#productCant"+id).val());
               var precio    = parseFloat($("#productUnidad"+id).val());
               var totalFila = ((cantidad*precio)).toFixed(2);
                   $("#productTotal"+id).val(parseFloat(totalFila).toFixed(2));
                   total = total+parseFloat((cantidad*precio).toFixed(2));
            });
            total     = parseFloat(total).toFixed(2);
            subtotal  = parseFloat(total/1.18);
            igv       = parseFloat(total - subtotal);
            $("#subtotal").val((parseFloat(subtotal)).toFixed(2));
            $("#igv").val((parseFloat(igv)).toFixed(2));
            $("#total").val((parseFloat(total)).toFixed(2)); 
            $("#totalredondeado").val((Math.round10(total, -1)).toFixed(2));
}
   
function selectproductos(){
         var data = [];
             $("#tabla-contenido-compradevolucion > tbody > tr").each(function() {
                var id = $(this).attr('id');  
                    data.push({
                      idproducto:       $(this).attr('idproducto'),
                      idcompradetalle:   $(this).attr('idcompradetalle'),
                      productCant:      $("#productCant"+id).val(),
                      productUnidad:    $("#productUnidad"+id).val(),
                      productTotal:     $("#productTotal"+id).val(),
                    });
              });
          return JSON.stringify(data);
}
  
function eliminarproducto(id){
         $("#tabla-contenido-compradevolucion > tbody > tr#"+id).remove();
          calcularmonto();
}
</script>
