<div class="profile-edit-container">
    <div class="custom-form">
        <div class="row">
            <div class="col-sm-3"></div>
            <div class="col-sm-6">
                <label>Empresa *</label>
                <select id="idagencia" {{count($agencias)<=1?'disabled':''}}>
                    <option></option>
                    @foreach($agencias as $value)
                        <option value="{{ $value->id }}"?>{{ $value->ruc }} - {{ $value->nombrecomercial }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div id="cont-agenciaserie" style="display:none;">
            <div class="row">
                <div class="col-sm-4">
                </div>
                <div class="col-sm-2">
                    <label>Serie *</label>
                    <select id="facturador_serie">
                    </select>
                </div>
                <div class="col-sm-2">
                    <label>Correlativo *</label>
                    <input type="number" id="facturador_correlativo"/>
                </div>
            </div>
        </div>
        <div id="load-agenciaserie"></div>
        <div id="cont-form-facturacionnotadebito" style="display:none;">
            <form class="js-validation-signin px-30" 
                  action="javascript:;" 
                  onsubmit="callback({
                    route: 'backoffice/tienda/sistema/{{ $tienda->id }}/facturacionnotadebito',         
                    method: 'POST',
                    data:{
                        view: 'registrar',
                        carga: '#carga-facturacionnotadebito',
                        productos: selectproductos(),
                        idtienda: '{{ $tienda->id }}'
                    }
                    },
                    function(resultado){
                        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionnotadebito') }}';
                    },this)">
                <div class="row">
                    <input type="hidden" id="idfacturacionboletafactura" value="0" disabled>
                    <div class="col-sm-6">
                        <label>Cliente *</label>
                        <input type="text" id="cliente" disabled>

                        <label>Moneda *</label>
                        <input type="text" id="moneda" disabled>
                    </div>
                    <div class="col-sm-6">
                        <label>Fecha de Emisión *</label>
                        <input type="text" id="fechaemision" disabled>
                        <label>Comprobante *</label> 
                        <input type="text" id="comprobante" disabled>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-3">
                            <label>Motivo *</label>
                            <select id="idmotivonotadebito" disabled> 
                                <option value="01">Intereses por mora</option>
                                <option value="02">Aumento en el valor</option>
                                <option value="03">Penalidades/ otros conceptos</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Descripción *</label>
                            <input type="text" id="motivonotadebito_descripcion"/>
                        </div>
                        <div class="col-sm-3"></div>
                    </div>
                </div>      
                <div class="table-responsive">
                    <table class="table" id="tabla-contenido-notadebito">
                        <thead class="thead-dark">
                            <tr>
                                <th width="100px" rowspan="2">Código</th>
                                <th rowspan="2">Producto</th>
                                <th colspan="2">FACTURA/BOLETA</th>
                                <th colspan="3">NOTA DE CRÉDITO</th>
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
                      <input type="hidden" id="igv">
                      <input type="text" id="impuesto" placeholder="0.00" disabled>
                      <label>Total</label>
                      <input type="text" id="totalventa" placeholder="0.0"  disabled>
                    </div>
                    <div class="col-md-4"></div>
                </div>
                <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;">Guardar Cambios</button>
            </form>
        </div>
        <div id="cont-facturacionnotadebito-carga"></div>
    </div>
</div>
<script>
$("#idagencia").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-agenciaserie').css('display','none');
    $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionnotadebito/show-selecionarserie')}}",
            type:'GET',
            data: {
                idagencia : e.currentTarget.value
            },
            beforeSend: function (data) {
                load('#load-agenciaserie');
            },
            success: function (respuesta){
              $('#cont-agenciaserie').css('display','block');
              $("#load-agenciaserie").html('');
              $("#facturador_serie").html(respuesta['agenciaoption']);
            }
        })
}).val({{ $configuracion!=''?($configuracion['idempresapordefecto']!=''?$configuracion['idempresapordefecto']:'null'):'null' }}).trigger("change");
  
$('#idmotivonotadebito').select2({
      placeholder: '-- Tipo --',
      minimumResultsForSearch: -1
}).val('02').trigger("change");
  
$('#facturador_serie').select2({
      placeholder: '-- Serie --',
      minimumResultsForSearch: -1
})
  
$('#facturador_correlativo').keyup( function(e) {
    if(e.keyCode == 13){
        cargarventa_boletafactura( $('#idagencia').val(), $('#facturador_serie').val(), $('#facturador_correlativo').val());
    }
})
    // Funcion para mostrar los datos del facturador
    function cargarventa_boletafactura(idagencia, facturador_serie, facturador_correlativo){
        load('#cont-facturacionnotadebito-carga');
        $('#cont-form-facturacionnotadebito').css('display', 'none');  
        $.ajax({
            url:  "{{url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionnotadebito/show-seleccionarboletafactura')}}",
            type: 'GET',
            data: {
                idagencia               : idagencia,
                facturador_serie        : facturador_serie,
                facturador_correlativo  : facturador_correlativo
            },
            success: function (respuesta){
                if (respuesta["facturacionboletafactura"] != undefined){
                    $('#cont-form-facturacionnotadebito').css('display', 'block');
                    $('#cont-facturacionnotadebito-carga').html('');
                  
                    $('#idfacturacionboletafactura').val(respuesta['facturacionboletafactura'].id);
                    $('#cliente').val(respuesta['facturacionboletafactura'].cliente_numerodocumento+' - '+respuesta["facturacionboletafactura"].cliente_razonsocial);
                    var tipomoneda = '';
                    if(respuesta['facturacionboletafactura'].venta_tipomoneda=='PEN'){
                        tipomoneda = 'SOLES';
                    }else if(respuesta['facturacionboletafactura'].venta_tipomoneda=='USD'){
                        tipomoneda = 'DOLARES';
                    }
                    $('#moneda').val(tipomoneda);
                    $('#fechaemision').val(respuesta['facturacionboletafactura'].venta_fechaemision);
                    var tipodocumento = '';
                    if(respuesta['facturacionboletafactura'].venta_tipodocumento=='01'){
                        tipodocumento = 'FACTURA';
                    }else if(respuesta['facturacionboletafactura'].venta_tipodocumento=='03'){
                        tipodocumento = 'BOLETA';
                    }
                    $('#comprobante').val(tipodocumento);
                    $('#totalventa').val(respuesta['facturacionboletafactura'].venta_montoimpuestoventa);
                    $('#igv').val(respuesta['facturacionboletafactura'].venta_igv);
                    $('#tabla-contenido-notadebito > tbody').html(respuesta['facturacionboletafacturadetalle']);
                    calcularmonto();
                } else {
                    $('#cont-facturacionnotadebito-carga').html(`<div class="alert alert-danger" style="font-size: 20px;
                                                                                                        padding-top: 10px;
                                                                                                        padding-bottom: 10px;
                                                                                                        margin-bottom: 15px;
                                                                                                        margin-top: 5px;">
                                                                    No existe la Boleta/Factura!
                                                                 </div>`);
                } 
            }
        })
    }
   // Funcion para calcular el monto
   function calcularmonto(){
     var total     = 0;
        $("#tabla-contenido-notadebito > tbody > tr").each(function() {
            var id        = $(this).attr('id');
            var cantidad  = parseFloat($("#productCant"+id).val());
            var precio    = parseFloat($("#productUnidad"+id).val());
            var totalFila = ((cantidad*precio)).toFixed(2);
            $("#productTotal"+id).val(parseFloat(totalFila).toFixed(2));
            total = total+parseFloat((cantidad*precio).toFixed(2));
        });
     
        var igv       = (parseFloat($("#igv").val())/100)+1;
        var total     = parseFloat(total).toFixed(2);
        var subtotal  = parseFloat(total/igv).toFixed(2);
        var impuesto  = parseFloat(total-subtotal).toFixed(2);
     
        $("#subtotal").val(subtotal); 
        $("#impuesto").val(impuesto); 
        $("#totalventa").val(total);  
    
    }
    //Seleccionar Productos
    function selectproductos(){
      var data = [];
          $("#tabla-contenido-notadebito > tbody > tr").each(function() {
              var id = $(this).attr('id');  
              data.push({
                idproducto:                         $(this).attr('idproducto'),
                idfacturacionboletafacturadetalle:  $(this).attr('idfacturacionboletafacturadetalle'),
                productCant:                        $("#productCant"+id).val(),
                productUnidad:                      $("#productUnidad"+id).val(),
                productTotal:                       $("#productTotal"+id).val(),
              });
          });
          return JSON.stringify(data);
      }
  
    // Funcion para eliminar una fila de los productos
    function eliminarproducto(id){
        $("#tabla-contenido-notadebito > tbody > tr#"+id).remove();
          calcularmonto();
    }
</script>
