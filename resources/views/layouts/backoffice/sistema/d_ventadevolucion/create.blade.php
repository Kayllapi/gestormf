@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
        <span>Registrar Venta de Devolución</span>
        <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/ventadevolucion') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<div class="profile-edit-container">
   <div class="custom-form">
        <div class="profile-edit-container">
          <div class="row">
             <div class="col-md-3"></div>
             <div class="col-md-6">
               <input class="form-control" type="text" id="ventacodigo" style="text-align: center;" placeholder="Codigo de Venta"/>
             </div>
           </div>
        </div>
        <div id="carga-devolucionventa"></div>
        <div id="cont-form-ventadevolucion"  style="display:none;">
            <form class="js-validation-signin px-30" 
                  action="javascript:;" 
                  onsubmit="callback({
                    route: 'backoffice/tienda/sistema/{{ $tienda->id }}/ventadevolucion',         
                    method: 'POST',
                    data:{
                        view: 'registrar',
                        carga: '#carga-ventadevolucion',
                        productos: selectproductos(),
                        idtienda: '{{ $tienda->id }}'
                         }
                    },
                    function(resultado){
                        location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/ventadevolucion') }}';
                    },this)">
                <div class="row">
                    <input type="hidden" id="idventa" value="0" disabled>
                    <div class="col-sm-6">
                        <label>Cliente</label>
                        <input type="text" id="cliente" disabled>
                        <label>Tipo de entrega</label>
                        <input type="text" id="tipoentrega" disabled>
                    </div>
                    <div class="col-sm-6">
                        <label>Fecha de venta</label>
                        <input type="text" id="fechaemision" disabled>
                        <label>Motivo de Devolución *</label> 
                        <input type="text" id="motivo" onkeyup="texto_mayucula(this)">
                    </div>
                </div>      
                <div class="table-responsive">
                    <table class="table" id="tabla-contenido-ventadevolucion">
                        <thead class="thead-dark">
                            <tr>
                                <th width="100px" rowspan="2">Código</th>
                                <th rowspan="2">Producto</th>
                                <th colspan="2">VENTA</th>
                                <th colspan="3">VENTA DEVOLUCIÓN</th>
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
                </div>
                <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;" >Guardar Cambios</button>
            </form>
        </div>
    </div>
</div>
@endsection
@section('subscripts')
<script>
$('#ventacodigo').keyup( function(e) {
    if(e.keyCode == 13){
        buscar_venta( $('#ventacodigo').val())
    }
})
function buscar_venta(ventacodigo){
     load('#carga-devolucionventa'); 
      $('#cont-form-ventadevolucion').css('display', 'none');  
      $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/ventadevolucion/show_buscarventa')}}",
            type:'GET',
            data: {
                ventacodigo : ventacodigo
            },
            success: function (respuesta){
                if(respuesta['venta'] != undefined){
                     $('#cont-form-ventadevolucion').css('display', 'block');
                     $('#carga-devolucionventa').html('');
                     $('#idventa').val(respuesta['venta'].id);
                     $('#cliente').val(respuesta['venta'].cliente);
                     var tipoentrega = '';
                      if(respuesta['venta'].s_idtipoentrega==01){
                          tipoentrega = 'DIRECTA';
                      }else if(respuesta['venta'].s_idtipoentrega==02){
                          tipoentrega = 'DELIVERY';
                      }else if(respuesta['venta'].s_idtipoentrega==03){
                          tipoentrega = 'RESERVA';
                      }
                      $('#tipoentrega').val(tipoentrega);
                      $('#fechaemision').val(respuesta['venta'].fecharegistro);
                      $('#total').val(respuesta['venta'].totalventa);
                      $('#subtotal').val(respuesta['venta'].subtotal);
                      $('#tabla-contenido-ventadevolucion > tbody').html(respuesta['ventadetalle']);
                   calcularmonto(); 
                }else {
                    $('#carga-devolucionventa').html(`<div class="alert alert-danger" style="font-size: 20px;
                                                                                                        padding-top: 10px;
                                                                                                        padding-bottom: 10px;
                                                                                                        margin-bottom: 15px;
                                                                                                        margin-top: 5px;">
                                                                    No existe Código Venta!
                                                                 </div>`);
                } 
            }
      })
}
  
function calcularmonto(){
      var subtotal  = 0;
      var total     = 0;
      var igv       = 0;
          $("#tabla-contenido-ventadevolucion > tbody > tr").each(function() {
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
         $("#tabla-contenido-ventadevolucion > tbody > tr").each(function() {
              var id = $(this).attr('id');  
              data.push({
                idproducto:       $(this).attr('idproducto'),
                idventadetalle:   $(this).attr('idventadetalle'),
                productCant:      $("#productCant"+id).val(),
                productUnidad:    $("#productUnidad"+id).val(),
                productTotal:     $("#productTotal"+id).val(),
              });
          });
          return JSON.stringify(data);
}
  
function eliminarproducto(id){
         $("#tabla-contenido-ventadevolucion > tbody > tr#"+id).remove();
         calcularmonto();
}
  
</script>
@endsection