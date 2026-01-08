@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Resumen Diario</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionresumendiario') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div id="carga-facturacionresumen">
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
        <div id="cont-facturacionresumen-carga"></div>
        <div id="cont-form-facturacionresumen" style="display:none;">
            <form class="js-validation-signin px-30" 
                  action="javascript:;" 
                  onsubmit="callback({
                    route: 'backoffice/tienda/sistema/{{ $tienda->id }}/facturacionresumendiario',
                    method: 'POST',
                    data:{
                        view: 'registrar',
                        comprobantes: selectproductos(),
                        idagencia: $('#idagencia').val(),
                        carga: '#carga-facturacionresumen'
                    }
                    },
                    function(resultado){
                        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionresumendiario') }}';
                    },this)"> 
                <div class="table-responsive">
                    <table class="table" id="tabla-facturacionresumen"  style="margin-bottom: 5px;">
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
                              <th>Estado</th>
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
                    url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionresumendiario/show-selecionarserie')}}",
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
                    url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionresumendiario/show-selecionarserie')}}",
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
$('#facturador_serie').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
});
   //Tipo en resumen 
    $('#tipoemisionresumen').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1
      }).on("change", function(e) {
          $('#cont-tipoemisionresumen-facturaboleta').css('display','none');
          $('#cont-tipoemisionresumen-venta').css('display','none');
          $('#cont-facturacionresumen').css('display','none');
          $('#cont-facturacionresumen-btn').css('display','none');
          $('#cont-facturacionresumen-carga').html('');
          if(e.currentTarget.value==1){
              $('#cont-tipoemisionresumen-facturaboleta').css('display','block');
          }else if(e.currentTarget.value==2){
              $('#cont-tipoemisionresumen-venta').css('display','block');
          }
      }).val(1).trigger('change');
 
    //Motivo del Resumen
    $('#idmotivoresumen').select2({
        placeholder: '-- Seleccionar Motivo --',
        minimumResultsForSearch: -1
    }).on("change", function(e) {
        if($('#tipoemisionresumen').val()==1){
            cargarboletafacturaitem($('#idboletafacturaventa').val(),e.currentTarget.value);
        }else if($('#tipoemisionresumen').val()==2){
            cargarventaitem($('#idboletafacturaventa').val(),e.currentTarget.value);
        }
    });
  
$('#facturador_correlativo').keyup( function(e) {
    if(e.keyCode == 13){
        cargarventa_boletafactura( $('#facturador_estado').val(), $('#idagencia').val(), $('#facturador_serie').val(), $('#facturador_correlativo').val());
    }
})


function cargarventa_boletafactura(facturador_estado,idagencia,facturador_serie,facturador_correlativo){
  
    load('#cont-facturacionresumen-carga');
    var cant_tr = $("#tabla-facturacionresumen > tbody > tr").length;
    if(cant_tr==0){
        $('#cont-form-facturacionresumen').css('display','none');
    }
    
    $.ajax({
        url:  "{{url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionresumendiario/show-seleccionarboletafactura')}}",
        type:'GET',
        data: {
            idagencia               : idagencia,
            facturador_serie        : facturador_serie,
            facturador_correlativo  : facturador_correlativo,
            facturador_estado       : facturador_estado,
            tipodocumento          : $('#facturador_serie option:selected').attr('tipodocumento'),
        },
        success: function (respuesta){
            if(respuesta['resultado']=='ERROR') {
                $('#cont-facturacionresumen-carga').html(`<div class="alert alert-danger" style="font-size: 20px;padding-top: 10px;padding-bottom: 10px;margin-bottom: 5px;border-radius: 5px;">${respuesta['mensaje']}</div>`);
            }else{
                // validar repetidos
                var valid = 0;
                $("#tabla-facturacionresumen > tbody > tr").each(function() {
                    if($(this).attr('tipo')==respuesta['data']["tipo"] && $(this).attr('idventa_boletafactura')==respuesta['data']["id"]){
                        valid = 1;
                    }
                });
                // fin validar repetidos
                if(valid==1){
                    $('#cont-facturacionresumen-carga').html(`<div class="alert alert-danger" style="font-size: 20px;padding-top: 10px;padding-bottom: 10px;margin-bottom: 5px;border-radius: 5px;">El comprobante "${respuesta['data']["serie"]}-${respuesta['data']["correlativo"]}", ya esta agregado en la lista!</div>`);
                }else {
                    $('#cont-form-facturacionresumen').css('display','block');
                    $('#cont-facturacionresumen-carga').html('');
                    agregarproducto({
                        tipo:         respuesta['data']["tipo"],
                        id:           respuesta['data']["id"],
                        serie:        respuesta['data']["serie"],
                        correlativo:  respuesta['data']["correlativo"],
                        cliente:      respuesta['data']["cliente"],
                        emision:      respuesta['data']["emision"],
                        moneda:      respuesta['data']["moneda"],
                        opgravada:    respuesta['data']["venta_montooperaciongravada"],
                        igv:          respuesta['data']["venta_montoigv"],
                        total:        respuesta['data']["venta_montoimpuestoventa"],
                    });
                }
            }
        }
    });
}
    //Agregar productos
    function agregarproducto(data){

        let num = $("#tabla-facturacionresumen tbody").attr('num');

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
                            <td>
                              <select id="facturador_estado${num}">
                                  <option></option>
                                  <option value="1">Adicionar</option>
                                  <option value="2">Modificar</option>
                                  <option value="3">Anular</option>
                              </select>
                            </td>
                            <td class="with-btn" width="10px"><a id="del${num}" href="javascript:;" onclick="eliminarproducto(${num})" class="btn btn-danger big-btn">
                              <i class="fas fa-trash-alt"></i> Quitar</a>
                            </td>
                         </tr>`;

        $("#tabla-facturacionresumen tbody").append(nuevaFila);
        $("#tabla-facturacionresumen tbody").attr('num',parseInt(num)+1);
      
        $('#facturador_estado'+num).select2({
            placeholder: '-- Seleccionar --',
            minimumResultsForSearch: -1
        });
    }
    //Seleccionar productos
    function selectproductos(){
        var data = [];
        $("#tabla-facturacionresumen > tbody > tr").each(function() {
            var id = $(this).attr('id');
            data.push({
              idventa_boletafactura : $(this).attr('idventa_boletafactura'),
              facturador_estado : $("#facturador_estado"+id).val(),
              tipo : $(this).attr('tipo'),
            });
        });
        return JSON.stringify(data);
     }

   // Funcion para eliminar una fila de los productos
   function eliminarproducto(num){
       $("#tabla-facturacionresumen tbody tr#"+num).remove();
    }
 
</script>
@endsection