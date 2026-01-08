@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Compra</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compra') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
@if(Auth::user()->idtienda==0)
    <div class="mensaje-warning">
      <i class="fa fa-warning"></i> ¡Con el usuario Master no puede realizar una compra, ingrese con un usuario de esta tienda!
    </div>
@else
<div id="carga-compra">
    <div class="profile-edit-container">
        <div class="custom-form">
          <div class="row">
             <div class="col-md-6">
                <label>Proveedor *</label>
                <div class="row">
                   <div class="col-md-9">
                      <select id="idproveedor" >
                          <option value="{{$compra->s_idusuarioproveedor}}">{{$compra->proveedoridentificacion}} - {{$compra->proveedorapellidos}}, {{$compra->proveedornombre}}</option>
                      </select>
                   </div>
                   <div class="col-md-3">
                      <a href="javascript:;" id="modal-registrarproveedor" class="btn btn-warning"><i class="fa fa-plus"></i> Agregar</a>
                   </div>
                </div>
                <div class="row">
                  <div class="col-md-7">
                    <label>Comprobante *</label>
                    <select id="idcomprobante">
                        <option></option>
                        @foreach($comprobante as $value)
                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                        @endforeach
                    </select>
                  </div>
                  <div class="col-md-5">
                    <label>Serie - Correlativo *</label>
                    <input type="text" value="{{ $compra->seriecorrelativo }}" id="seriecorrelativo" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                  </div>
                </div>
             </div>
             <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <label>Fecha de Emisión *</label>
                        <input type="date" value="{{ $compra->fechaemision }}" id="fechaemision"/>
                    </div>
                    <div class="col-md-6">
                        <label>Moneda *</label>
                        <select id="idmoneda">
                            <option></option>
                            @foreach($s_monedas as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <label>Estado *</label>
                <select id="idestado">
                    <option value="1">Pedido (Orden de compra)</option>
                    <option value="2">Compra</option>
                    <option></option>
                </select>
             </div>
           </div>
        </div>
    </div>
    <div class="custom-form">
    <div class="table-responsive">
      <table class="table" id="tabla-contenido">
          <thead class="thead-dark">
            <tr>
              <th width="15%">Código</th>
              <th>Producto</th>
              <th width="60px">Cantidad</th>
              <th width="110px">P. Unitario</th>
              <th width="110px">P. Total</th>
              <th width="110px">Fecha de Vencimiento</th>
              <th width="10px"></th>
            </tr>
            <tr>
                <td class="mx-td-input"><input type="text" id="buscarcodigoproducto"/></td>
                <td colspan="5" class="mx-td-input">
                  <select id="idproducto">
                      <option></option>
                  </select>
                <td width="auto"></td>
                </td>
            </tr>
          </thead>
          <tbody num="0"></tbody>

      </table>
    </div>
  </div>
  
  <div class="profile-edit-container">
    <div class="custom-form">
      <div class="row">  
        <div class="col-md-8">
        </div>  
        <div class="col-md-4">
          <label for="">Total</label>
          <input type="text" id="totalsinredondear" placeholder="0.00" disabled>
          <label for="">Total Redondeado</label>
          <input type="text" id="total" placeholder="0.00" disabled>
        </div>       
      </div> 
    </div>
  </div>
  
    <div class="profile-edit-container">
        <div class="custom-form">
            <a href="javascript:;" onclick="actualizar_compra()" id="registrar_compra" class="btn  big-btn  color-bg flat-btn">Registrar Compra</a>
        </div>
    </div>
</div>
</div>
@endif
@endsection
@section('htmls')
@include('app.modal_usuario_registrar',[
    'nombre'    =>'Registrar Proveedor',
    'modal'     =>'registrarproveedor',
    'idusuario' =>'idproveedor'
])
@endsection
@section('subscripts')
<script>
// actualizar compra
function actualizar_compra(){
    callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/compra/{{ $compra->id }}',
        method: 'PUT',
        carga: '#carga-compra',
        data:{
            view: 'editar',
            productos: selectproductos(),
            idproveedor: $('#idproveedor').val(),
            idcomprobante: $('#idcomprobante').val(),
            seriecorrelativo: $('#seriecorrelativo').val(),
            fechaemision: $('#fechaemision').val(),
            idmoneda: $('#idmoneda').val(),
            total: $('#totalsinredondear').val(),
            totalredondeado: $('#total').val(),
            idestado: $('#idestado').val(),
            total: $('#total').val()
        }
    },
    function(resultado){
         location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/compra') }}';                                                  
    })
}
$("#idproveedor").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/compra/showlistarusuario')}}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                  buscar: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    placeholder: "--  Seleccionar --",
    minimumInputLength: 2
});
  
$("#idmoneda").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{ $compra->s_idmoneda }}).trigger("change");
  
$("#idcomprobante").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$compra->s_idcomprobante}}).trigger("change");

$("#idestado").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{$compra->s_idestado}}).trigger("change");

$("#idproducto").select2({
    ajax: {
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/compra/showlistarproducto')}}",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                  buscar: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data
            };
        },
        cache: true
    },
    placeholder: "--  Seleccionar Producto --",
    allowClear: true,
    minimumInputLength: 2
}).on("change", function(e) {
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/compra/showseleccionarproducto')}}",
        type:'GET',
        data: {
            idproducto : e.currentTarget.value
        },
        success: function (respuesta){
          if(respuesta["producto"]!=null){
            agregarproducto(
              respuesta["producto"].id,
              respuesta["producto"].codigo,
              respuesta["producto"].nombre,
              '1',
              '0.00',
              '',
              '0.00'
            );
          }
        }
    })
});
  
$('#buscarcodigoproducto').select();
  
$('#buscarcodigoproducto').keyup( function(e) {
    if(e.keyCode == 13){
        buscarcodigo('#buscarcodigoproducto');
    }
    if(e.keyCode == 27){
        $('#registrar_compra').focus();
        $('#registrar_compra').select();
    }
})
  
function buscarcodigo(pthis){
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/compra/showseleccionarproductocodigo')}}",
        type:'GET',
        data: {
            codigoproducto : $(pthis).val()
        },
        beforeSend: function (data) {
            carga({
                input:'#carga-compra',
                color:'info',
                mensaje:'Buscado el Producto, Espere por favor...'
            }); 
        },
        success: function (respuesta){
          $('#buscarcodigoproducto').val('');
          if(respuesta["resultado"]=='ERROR'){
              carga({
                  input:'#carga-compra',
                  color:'danger',
                  mensaje: respuesta['mensaje']
              });
          }else{
              agregarproducto(
                respuesta["producto"].id,
                respuesta["producto"].codigo,
                respuesta["producto"].nombre,
                '1',
                '0.00',
                '',
                '0.00'
              );
              removecarga({input:'#carga-compra'});
          }
        },
        error:function(respuesta){
              carga({
                  input:'#carga-compra',
                  color:'danger',
                  mensaje:formerror({dato:respuesta})
              });
        }
    })
}
@foreach($s_compradetalles as $value)
agregarproducto('{{ $value->s_idproducto }}','{{ $value->productocodigo }}','{{ $value->productonombre }}','{{ $value->cantidad }}','{{ $value->preciounitario }}','{{ $value->fechavencimiento }}','{{ $value->preciototal }}');
@endforeach
function agregarproducto(idproducto,codigo,nombre,cantidad,preciocompra,fechavencimiento,preciototal){
      $("#codigoproducto").val('');
      $("#idproducto").html('');
      var num = $("#tabla-contenido tbody").attr('num');
      var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'" style="background-color: #0ec529;color: #fff;">';
          nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+='<td>'+nombre+'</td>';
          nuevaFila+='<td class="mx-td-input"><input id="productCant'+num+'" type="number" value="'+cantidad+'" onkeyup="calcularmonto(true)" onclick="calcularmonto(true)"></td>';
          nuevaFila+='<td class="mx-td-input"><input id="productUnidad'+num+'" type="number" value="'+preciocompra+'" step="0.001" min="0" disabled></td>'; 
          nuevaFila+='<td class="mx-td-input"><input id="productTotal'+num+'" type="text" value="'+preciototal+'" onkeyup="calcularmonto(true)" onclick="calcularmonto(true)" step="0.01" min="0"></td>';    
          nuevaFila+='<td class="mx-td-input"><input id="productFechavencimiento'+num+'" type="date" value="'+fechavencimiento+'" style="padding: 8px;"></td>';
          nuevaFila+='<td><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>'
          nuevaFila+='</tr>';
      $("#tabla-contenido").append(nuevaFila);
      $("#tabla-contenido tbody").attr('num',parseInt(num)+1);
  
      $('#productCant'+num).select();
      $('#productCant'+num).keyup( function(e) {
          if(e.keyCode == 13){
              $('#productTotal'+num).select();
          }
          if(e.keyCode == 27){
              $('#registrar_compra').focus();
              $('#registrar_compra').select();
          }
      })
      /*$('#productUnidad'+num).keyup( function(e) {
          if(e.keyCode == 13){
              $('#productTotal'+num).select();
          }
          if(e.keyCode == 27){
              $('#registrar_compra').focus();
              $('#registrar_compra').select();
          }
      })*/
      $('#productTotal'+num).keyup( function(e) {
          if(e.keyCode == 13){
              $('#productFechavencimiento'+num).select();
          }
          if(e.keyCode == 27){
              $('#registrar_compra').focus();
              $('#registrar_compra').select();
          }
      })
      $('#productFechavencimiento'+num).keyup( function(e) {
          if(e.keyCode == 13){
              $('#buscarcodigoproducto').select();
          }
          if(e.keyCode == 27){
              $('#registrar_compra').focus();
              $('#registrar_compra').select();
          }
      })
  
      calcularmonto();
}

function calcularmonto(val=false){
    if(val==true){
        var total = 0;
        $("#tabla-contenido tbody tr").each(function() {
            var num = $(this).attr('id');        
            var productCant = parseFloat($("#productCant"+num).val());
            var productTotal = parseFloat($("#productTotal"+num).val());
            var subtotal = (productTotal/productCant).toFixed(3);
            $("#productUnidad"+num).val(parseFloat(subtotal).toFixed(3));
            total = total+parseFloat(productTotal);
        });
        $("#total").val((Math.round10(total, -1)).toFixed(2)); 
        $("#totalsinredondear").val((parseFloat(total)).toFixed(2));  
    }else{
        var total = 0;
        $("#tabla-contenido tbody tr").each(function() {
            var num = $(this).attr('id');        
            var productCant = parseFloat($("#productCant"+num).val());
            var productUnidad = parseFloat($("#productUnidad"+num).val());
            var subtotal = (productCant*productUnidad).toFixed(2);
            $("#productTotal"+num).val(parseFloat(subtotal).toFixed(2));
            total = total+parseFloat(subtotal);
        });
        $("#total").val((Math.round10(total, -1)).toFixed(2)); 
        $("#totalsinredondear").val((parseFloat(total)).toFixed(2));  
    } 
}

function selectproductos(){
    var data = '';
    $("#tabla-contenido tbody tr").each(function() {
        var num = $(this).attr('id');        
        var idproducto = $(this).attr('idproducto');
        var productCant = $("#productCant"+num).val();
        var productUnidad = $("#productUnidad"+num).val();
        var productTotal = $("#productTotal"+num).val();
        var productFechavencimiento = $("#productFechavencimiento"+num).val();
        data = data+'&'+idproducto+','+productCant+','+productUnidad+','+productFechavencimiento+','+productTotal;
    });
    return data;
}
  
function eliminarproducto(num){
    $("#tabla-contenido tbody tr#"+num).remove();
    calcularmonto();
}
  
$("#montorecibido").keyup(function() {
      var total =  parseFloat($("#total").val());
      var montorecibido =  parseFloat($("#montorecibido").val());
      if($("#montorecibido").val()==''){
          montorecibido = 0;
      }
      if($("#total").val()==''){
          total = 0;
      }
      var suma = montorecibido - total;
      $("#vuelto").val(parseFloat(suma).toFixed(2));
});
</script>
@endsection