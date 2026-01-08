@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Comunicación de Baja</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacioncomunicacionbaja') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div class="profile-edit-container">
    <div class="custom-form">
        <div class="row">
            <div class="col-sm-3">
            </div>
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
                <div class="col-sm-3">
                </div>
                <div class="col-sm-6">
                  <label>Fecha de Emisión de Comprobante*</label>
                  <input id="fechaemision" class="form-control" type="date">
                </div>
<!--                 <div class="col-sm-2">
                    <label>Serie *</label>
                    <select id="facturador_serie">
                    </select>
                </div>
                <div class="col-sm-2">
                    <label>Correlativo *</label>
                    <input type="text"  id="facturador_correlativo">
                </div> -->
            </div>
        </div>
        <div class="mensaje-warning" style="display: none;">
          <i class="fa fa-warning"></i>
        </div>    
    
        <div id="select-seriecorrelativo" style="display: none;">
            <div class="row">
              <div class="col-sm-3">
              </div>
              <div class="col-sm-6">
                  <label>Serie - Correlativo *</label>
                  <select name="seriecorrelativo" id="seriecorrelativo">
                    <option value=""></option>
                  </select>
              </div>
            </div>
        </div>
  
        <div id="cont-facturacioncomunicacionbaja-carga"></div>
        <div id="cont-form-facturacioncomunicacionbaja" style="display:none;">
            <form class="js-validation-signin px-30" 
                  action="javascript:;" 
                  onsubmit="callback({
                    route: 'backoffice/tienda/sistema/{{ $tienda->id }}/facturacioncomunicacionbaja',
                    method: 'POST',
                    data:{
                        view: 'registrar',
                        comprobantes: selectproductos(),
                        idagencia: $('#idagencia').val(),
                        fechaemision: $('#fechaemision').val(),
                        carga: '#carga-facturacioncomunicacionbaja'
                    }
                    },
                    function(resultado){
                        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacioncomunicacionbaja') }}';
                    },this)"> 
                <div class="table-responsive">
                    <table class="table" id="tabla-facturacioncomunicacionbaja"  style="margin-bottom: 5px;">
                        <thead class="thead-dark">
                            <tr>
                              <th>Fecha de Emisión</th>
                              <th>Comprobante</th>
                              <th>Serie</th>
                              <th>Correlativo</th>
                              <th>Cliente</th>
                              <th>Moneda</th>
                              <th>Sub Total</th>
                              <th>IGV</th>
                              <th>Total</th>
                              <th>Motivo</th>
                              <th width="10px"></th>
                            </tr>
                        </thead>
                        <tbody num="0"></tbody>
                    </table>
                </div>
                <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;">Guardar Cambios</button>
            </form>
        </div>
    </div>
</div>
@endsection
@section('subscripts')
<script>
$('#seriecorrelativo').select2({
  placeholder: '-- Seleccionar --'
}).on('change', function (e) {
  
   let facturadorMotivo      = $('#facturador_motivo').val();
   let facturadorSerie       = $('#seriecorrelativo option:selected').attr('factura-serie');
   let facturadorCorrelativo = $('#seriecorrelativo option:selected').attr('factura-correlativo');
  
   cargarventa_boletafactura(facturadorMotivo, $('#idagencia').val(), facturadorSerie, facturadorCorrelativo);
   $('#seriecorrelativo option:selected').remove();
   
});  
  
$('#fechaemision').change(function (e) {
  $('#seriecorrelativo').html('');
  $('.mensaje-warning').css('display', 'none');
  $('.mensaje-warning').text('');
  $('#select-seriecorrelativo').css('display', 'none')
  
  $.ajax({
        url:  "{{url('backoffice/tienda/sistema/'.$tienda->id.'/facturacioncomunicacionbaja/show-seleccionarboletafactura-fecha')}}",
        type:'GET',
        data: {
            fechaemision: e.currentTarget.value,
        },
        success: function (respuesta){
           if(respuesta['resultado']=='CORRECTO') {
             $('#select-seriecorrelativo').css('display', 'block');
             
             respuesta['facturas'].forEach( (value, key) => {
               $('#seriecorrelativo').append(`
                  <option value=""></option>
                  <option value="${value.venta_serie}"
                          factura-serie="${value.venta_serie}"
                          factura-correlativo="${value.venta_correlativo}"
                          tipodocumento="${value.tipodocumento}">
                      ${value.venta_serie}-${value.venta_correlativo} / ${value.cliente} / ${value.venta_subtotal}
                  </option>
               `);
             });
           }
           else {
             $('.mensaje-warning').css('display', 'block');
             $('.mensaje-warning').text(respuesta['mensaje']);
           }
        }
    });
}); 
  
@if(configuracion($tienda->id,'facturacion_empresapordefecto')['resultado']=='CORRECTO')
$("#idagencia").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-agenciaserie').css('display','none');
    $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/facturacioncomunicacionbaja/show-selecionarserie')}}",
            type:'GET',
            data: {
                idagencia : e.currentTarget.value
            },
            beforeSend: function (data) {
                load('#cont-facturacioncomunicacionbaja-carga');
            },
            success: function (respuesta){
              $('#cont-agenciaserie').css('display','block');
              $("#cont-facturacioncomunicacionbaja-carga").html('');
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
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/facturacioncomunicacionbaja/show-selecionarserie')}}",
            type:'GET',
            data: {
                idagencia : e.currentTarget.value
            },
            beforeSend: function (data) {
                load('#cont-facturacioncomunicacionbaja-carga');
            },
            success: function (respuesta){
              $('#cont-agenciaserie').css('display','block');
              $("#cont-facturacioncomunicacionbaja-carga").html('');
              $("#facturador_serie").html(respuesta['agenciaoption']);
            }
        })
});
@endif
$('#facturador_serie').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
});
  
$('#facturador_correlativo, #facturador_motivo').keyup( function(e) {
    if(e.keyCode == 13){
        cargarventa_boletafactura($('#facturador_motivo').val(), $('#idagencia').val(), $('#facturador_serie').val(), $('#facturador_correlativo').val());
    }
})

function cargarventa_boletafactura(facturador_motivo, idagencia, facturador_serie, facturador_correlativo){
    load('#cont-facturacioncomunicacionbaja-carga');
    var cant_tr = $("#tabla-facturacioncomunicacionbaja > tbody > tr").length;
    if(cant_tr==0){
        $('#cont-form-facturacioncomunicacionbaja').css('display','none');
    }
    
    $.ajax({
        url:  "{{url('backoffice/tienda/sistema/'.$tienda->id.'/facturacioncomunicacionbaja/show-seleccionarboletafactura')}}",
        type:'GET',
        data: {
            idagencia               : idagencia,
            facturador_serie        : facturador_serie,
            facturador_correlativo  : facturador_correlativo,
            facturador_motivo       : facturador_motivo,
            tipodocumento           : $('#seriecorrelativo option:selected').attr('tipodocumento'),
//             tipodocumento          : $('#facturador_serie option:selected').attr('tipodocumento'),
        },
        success: function (respuesta){
            if(respuesta['resultado']=='ERROR') {
                $('#cont-facturacioncomunicacionbaja-carga').html(`<div class="alert alert-danger" style="font-size: 20px;padding-top: 10px;padding-bottom: 10px;margin-bottom: 5px;border-radius: 5px;">${respuesta['mensaje']}</div>`);
            }else{
                // validar repetidos
                var valid = 0;
                $("#tabla-facturacioncomunicacionbaja > tbody > tr").each(function() {
                    if($(this).attr('tipo')==respuesta['data']["tipo"] && $(this).attr('idventa_boletafactura')==respuesta['data']["id"]){
                        valid = 1;
                    }
                });
                // fin validar repetidos
                if(valid==1){
                    $('#cont-facturacioncomunicacionbaja-carga').html(`<div class="alert alert-danger" style="font-size: 20px;padding-top: 10px;padding-bottom: 10px;margin-bottom: 5px;border-radius: 5px;">El comprobante "${respuesta['data']["serie"]}-${respuesta['data']["correlativo"]}", ya esta agregado en la lista!</div>`);
                }else {
                    $('#cont-form-facturacioncomunicacionbaja').css('display','block');
                    $('#cont-facturacioncomunicacionbaja-carga').html('');
                    agregarproducto({
                        tipo:         respuesta['data']["tipo"],
                        id:           respuesta['data']["id"],
                        serie:        respuesta['data']["serie"],
                        correlativo:  respuesta['data']["correlativo"],
                        cliente:      respuesta['data']["cliente"],
                        emision:      respuesta['data']["emision"],
                        moneda:       respuesta['data']["moneda"],
                        opgravada:    respuesta['data']["venta_montooperaciongravada"],
                        igv:          respuesta['data']["venta_montoigv"],
                        total:        respuesta['data']["venta_montoimpuestoventa"],
                    });
                }
            }
        }
    });
}
  // Funcion para eliminar una fila de los productos
    function deleteProducto(num){
        $("#tabla-facturacioncomunicacionbaja tbody tr#"+num).remove();
        calcularmonto();
    }
    //Agregar productos
    function agregarproducto(data){

        let num = $("#tabla-facturacioncomunicacionbaja tbody").attr('num');

        let nuevaFila = `<tr id="${num}" idventa_boletafactura="${data.id}" tipo="${data.tipo}">
                            <td>${data.emision}</td>
                            <td>${data.tipo}</td>
                            <td>${data.serie}</td>
                            <td>${data.correlativo}</td>
                            <td>${data.cliente}</td>
                            <td>${data.moneda}</td>
                            <td>${parseFloat(data.opgravada).toFixed(2)}</td>
                            <td>${parseFloat(data.igv).toFixed(2)}</td>
                            <td>${parseFloat(data.total).toFixed(2)}</td>
                            <td><input type="text"  id="facturador_motivo${num}" onkeyup="texto_mayucula(this)"></td>
                            <td class="with-btn" width="10px">
                              <a id="del${num}" href="javascript:;" onclick="deleteProducto(${num})" class="btn btn-danger big-btn">
                              <i class="fas fa-trash-alt"></i> Quitar</a>
                            </td>
                         </tr>`;

        $("#tabla-facturacioncomunicacionbaja tbody").append(nuevaFila);
        $("#tabla-facturacioncomunicacionbaja tbody").attr('num',parseInt(num)+1);
        $('#facturador_motivo'+num).focus();
    }
   // Funcion para calcular el monto
   function calcularmonto(){
        var total     = 0;
        $("#tabla-contenido tbody tr").each(function() {
            var id       = $(this).attr('id');
            var cantidad  = parseFloat($("#productCant"+id).val());
            var precio    = parseFloat($("#productUnidad"+id).val());
            var totalFila = ((cantidad*precio)).toFixed(2);
            $("#productTotal"+id).val(parseFloat(totalFila).toFixed(2));
            total = total+parseFloat((cantidad*precio).toFixed(2));
        });
        var total = parseFloat(total).toFixed(2);
        var subtotal = parseFloat(total/1.18).toFixed(2);
        var igv = parseFloat(total-subtotal).toFixed(2);
        $("#subtotalventa").val(subtotal);
        $("#igvventa").val(igv); 
        $("#totalventa").val(total);  
    
    }
    function selectproductos(){
        var data = [];
        $("#tabla-facturacioncomunicacionbaja > tbody > tr").each(function() {
            var id = $(this).attr('id');
            data.push({
              idventa_boletafactura : $(this).attr('idventa_boletafactura'),
              facturador_motivo : $("#facturador_motivo"+id).val(),
              tipo : $(this).attr('tipo'),
            });
        });
        return JSON.stringify(data);
     }
  
    
 
</script>
@endsection
