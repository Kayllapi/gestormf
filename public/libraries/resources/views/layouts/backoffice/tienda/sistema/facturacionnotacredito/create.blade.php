@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
        <span>Registrar Nota de Cr&eacute;dito</span>
        <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionnotacredito') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
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
        <div id="cont-form-facturacionnotacredito" style="display:none;">
            <form class="js-validation-signin px-30" 
                  action="javascript:;" 
                  onsubmit="callback({
                    route: 'backoffice/tienda/sistema/{{ $tienda->id }}/facturacionnotacredito',         
                    method: 'POST',
                    data:{
                        view: 'registrar',
                        carga: '#carga-facturacionnotacredito',
                        productos: selectproductos(),
                        idtienda: '{{ $tienda->id }}'
                    }
                    },
                    function(resultado){
                        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionnotacredito') }}';
                    },this)">
                <div class="row">
                    <input type="hidden" id="idfacturacionboletafactura" value="0" disabled>
                    <div class="col-sm-6">
                        <label>Cliente</label>
                        <input type="text" id="cliente" disabled>

                        <label>Moneda</label>
                        <input type="text" id="moneda" disabled>
                    </div>
                    <div class="col-sm-6">
                        <label>Fecha de Emisión</label>
                        <input type="text" id="fechaemision" disabled>
                        <label>Comprobante</label> 
                        <input type="text" id="comprobante" disabled>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-3">
                            <label>Motivo *</label>
                            <select id="idmotivonotacredito" disabled> 
                                <option value="01">Anulación de la operación</option>
                                <option value="02">Anulación por error en el RUC</option>
                                <option value="03">Corrección por error en la descripción</option>
                                <option value="04">Descuento global</option>
                                <option value="05">Descuento por ítem</option>
                                <option value="06">Devolución total</option>
                                <option value="07">Devolución por ítem</option>
                                <option value="08">Bonificación</option>
                                <option value="09">Disminución en el valor</option>
                                <option value="10">Otros Conceptos</option>
                                <option value="11">Ajustes de operaciones de exportación</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>Descripción</label>
                            <input type="text" id="motivonotacredito_descripcion"/>
                        </div>
                        <div class="col-sm-3"></div>
                    </div>
                </div>      
                <div class="table-responsive">
                    <table class="table" id="tabla-contenido-notadecredito">
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
                <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;">Enviar a SUNAT</button>
            </form>
        </div>
        <div id="cont-facturacionnotacredito-carga"></div>
    </div>
</div>
@endsection
@section('subscripts')
<script>

    @if(configuracion($tienda->id,'facturacion_empresapordefecto')['resultado']=='CORRECTO')
        $("#idagencia").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).on("change", function(e) {
            $('#cont-agenciaserie').css('display','none');
            $.ajax({
                    url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionnotacredito/show-selecionarserie')}}",
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
        }).val({{ configuracion($tienda->id,'facturacion_empresapordefecto')['valor'] }}).trigger("change");  
    @else
        $("#idagencia").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).on("change", function(e) {
            $('#cont-agenciaserie').css('display','none');
            $.ajax({
                    url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionnotacredito/show-selecionarserie')}}",
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
        })
    @endif
  
$('#idmotivonotacredito').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1
}).val('07').trigger("change");
  
$('#facturador_serie').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1
})
  
$('#facturador_correlativo').keyup( function(e) {
    if(e.keyCode == 13){
        cargarventa_boletafactura( $('#idagencia').val(), $('#facturador_serie').val(), $('#facturador_correlativo').val());
    }
})
    // Funcion para mostrar los datos del facturador
    function cargarventa_boletafactura(idagencia, facturador_serie, facturador_correlativo){
        load('#cont-facturacionnotacredito-carga');
        $('#cont-form-facturacionnotacredito').css('display', 'none');  
        $.ajax({
            url:  "{{url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionnotacredito/show-seleccionarboletafactura')}}",
            type: 'GET',
            data: {
                idagencia               : idagencia,
                facturador_serie        : facturador_serie,
                facturador_correlativo  : facturador_correlativo
            },
            success: function (respuesta){
                if (respuesta["resultado"] == 'CORRECTO'){
                    $('#cont-form-facturacionnotacredito').css('display', 'block');
                    $('#cont-facturacionnotacredito-carga').html('');
                  
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
                    $('#tabla-contenido-notadecredito > tbody').html(respuesta['facturacionboletafacturadetalle']);
                    calcularmonto();
                } 
                else if (respuesta["resultado"] == 'ERROR'){
                    $('#cont-facturacionnotacredito-carga').html('<div class="alert alert-danger" style="font-size: 20px;padding-top: 10px;padding-bottom: 10px;margin-bottom: 15px;margin-top: 5px;">'+respuesta["mensaje"]+'</div>');
                } 
            }
        })
    }
   // Funcion para calcular el monto
   function calcularmonto(){
     var total     = 0;
        $("#tabla-contenido-notadecredito > tbody > tr").each(function() {
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
          $("#tabla-contenido-notadecredito > tbody > tr").each(function() {
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
        $("#tabla-contenido-notadecredito > tbody > tr#"+id).remove();
          calcularmonto();
    }
</script>
@endsection