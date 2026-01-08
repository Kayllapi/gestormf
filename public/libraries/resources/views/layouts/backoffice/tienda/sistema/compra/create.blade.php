@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Registrar Compra</span>
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
                      <select id="idproveedor">
                          <option></option>
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
                    <input type="text" id="seriecorrelativo" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
                  </div>
                </div>
             </div>
             <div class="col-md-6">
                <div class="row">
                    <div class="col-md-6">
                        <label>Fecha de Emisión *</label>
                        <input type="date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" id="fechaemision"/>
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
              <th width="60px">Prec. Actual</th>
              <th width="60px">Prec. Nuevo</th>
              <th width="60px">Cantidad</th>
              <th width="110px">P. Unitario</th>
              <th width="110px">P. Total</th>
              <th width="110px">F. Vencimiento</th>
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
            <a href="javascript:;" onclick="registrar_compra()" id="registrar_compra" class="btn mx-btn-post">Registrar Compra</a>
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

<!-- Inicio modal registrar producto -->
<div class="main-register-wrap modal-registrarproducto" id="modal-registrarproducto">
    <div class="main-overlay"></div>
    <div class="main-register-holder">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Registrar Producto</h3>
            <div class="mx-modal-cuerpo" id="contenido-registrarproducto">
              <div id="mx-carga-registrarproducto">
                  <div class="mensaje-info" id="mensaje-registrarproducto"></div>
                  <form class="js-validation-signin px-30" action="javascript:;" 
                        onsubmit="callback({
                                              route: 'backoffice/tienda/sistema/{{ $tienda->id }}/compra',
                                              method: 'POST',
                                              carga: '#mx-carga-registrarproducto',
                                              data:{
                                                  view: 'registrarproducto'
                                              }
                                            },
                                            function(resultado){
                                              $('#buscarcodigoproducto').val(resultado['producto'].codigo);
                                              $('#contenido-registrarproducto').css('display','none');
                                              $('#contenido-confirmar-registrarproducto').css('display', 'block');
                                            },this)">
                    
                    <div class="profile-edit-container">
                      <div class="custom-form">
                        <div class="row">
                            <label>Código de Producto</label>
                            <input type="text" id="codigo-registrarproducto"/>
                            <label>Nombre de Producto *</label>
                            <input type="text" id="nombre-registrarproducto"/>
                            <label>Precio Público *</label>
                            <input type="number" id="precioalpublico-registrarproducto" step="0.01" min="0"/>
                            <label>Categoría *</label>
                            <select id="idcategoria-registrarproducto">
                                <option></option>
                                @foreach($categorias as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                    <?php
                                    $subcategorias = DB::table('s_categoria')
                                        ->where('s_categoria.s_idcategoria',$value->id)
                                        ->orderBy('s_categoria.nombre','asc')
                                        ->get();
                                    ?>
                                    @foreach($subcategorias as $subvalue)
                                    <option value="{{$subvalue->id}}">{{ $value->nombre }} / {{ $subvalue->nombre }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                            <label>Marca</label>
                            <select id="idmarca-registrarproducto">
                                <option></option>
                                @foreach($marcas as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                         </div>
                      </div>
                    </div>
                    <div class="profile-edit-container">
                      <div class="custom-form">
                        <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
                      </div>
                    </div> 
                </form>
            </div>
          </div>
          <div class="mx-modal-cuerpo" id="contenido-confirmar-registrarproducto" style="display: none;">
            <div class="cont-confirm">
              <div class="confirm"><i class="fa fa-check"></i></div>
              <div class="confirm-texto">¡Correcto!</div>
              <div class="confirm-subtexto">Se ha registrado correctamente!.</div>
            </div>
            <div class="custom-form" style="text-align: center;">
               <button type="button" class="btn big-btn color-bg flat-btn" style="margin: auto;float: none;" onclick="confirm_cerrar_producto()">
               <i class="fa fa-check"></i> Aceptar</button>
            </div>
          </div>
        </div>
    </div>
</div>
<!-- Fin modal registrar producto -->
@endsection
@section('subscripts')
<script>
  // inicio registrar producto modal
  modal({click:'#modal-registrarproducto'});
  
  function confirm_cerrar_producto(){
    $('.modal-registrarproducto .close-reg').click();
    buscarcodigo('#buscarcodigoproducto');
  }
  
  $("#idcategoria-registrarproducto").select2({
      placeholder: "---  Seleccionar ---",
      allowClear: true
  });
  $("#idmarca-registrarproducto").select2({
      placeholder: "---  Seleccionar ---",
      allowClear: true
  });
  function openModalRegistrarProducto(codigo='', mensaje='') {
    removecarga({input:'#mx-carga-registrarproducto'});
    $('#contenido-confirmar-registrarproducto').css('display', 'none');
    $('#modal-registrarproducto, #contenido-registrarproducto').css('display','block');
    $('#codigo-registrarproducto').val(codigo);
    $('#mensaje-registrarproducto').html('<i class="fa fa-exclamation-circle"></i> ' + mensaje);
  }
  // fin registrar producto modal  
  
// registrar compra
function registrar_compra(){
    callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/compra',
        method: 'POST',
        carga: '#carga-compra',
        data:{
            view: 'registrar',
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
    @include('app.select2_cliente')
});
  
$("#idmoneda").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val({{ $configuracion!=''?($configuracion['idmonedapordefecto']!=0?$configuracion['idmonedapordefecto']:1):0 }}).trigger("change");
  
$("#idcomprobante").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
});

$("#idestado").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).val(1).trigger("change");

$("#idproducto").select2({
    @include('app.select2_producto',[
        'idtienda'=>$tienda->id
    ])
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
              respuesta["producto"].precioalpublico
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
    $('#modal-registrarproducto').css('display','none');
    var codigoproducto = $(pthis).val();
    $.ajax({
        url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/compra/showseleccionarproductocodigo')}}",
        type:'GET',
        data: {
            codigoproducto : codigoproducto
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
//               carga({
//                   input:'#carga-compra',
//                   color:'danger',
//                   mensaje: respuesta['mensaje']
//               });
              removecarga({input:'#carga-compra'});
              openModalRegistrarProducto(codigoproducto, respuesta['mensaje']);
          }else{
              agregarproducto(
                respuesta["producto"].id,
                respuesta["producto"].codigo,
                respuesta["producto"].nombre,
                respuesta["producto"].precioalpublico
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
function agregarproducto(idproducto,codigo,nombre,precio){
      $("#codigoproducto").val('');
      $("#idproducto").html('');
      var num = $("#tabla-contenido tbody").attr('num');
      var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'" style="background-color: #0ec529;color: #fff;">';
          nuevaFila+='<td>'+codigo+'</td>';
          nuevaFila+='<td>'+nombre+'</td>';
          nuevaFila+='<td>'+precio+'</td>';
          nuevaFila+='<td class="mx-td-input"><input id="productPrecio'+num+'" type="number" value="'+precio+'"></td>';
          nuevaFila+='<td class="mx-td-input"><input id="productCant'+num+'" type="number" value="1" onkeyup="calcularmonto(true)" onclick="calcularmonto(true)"></td>';
          nuevaFila+='<td class="mx-td-input"><input id="productUnidad'+num+'" type="number" value="0.00" step="0.001" min="0" disabled></td>';
          nuevaFila+='<td class="mx-td-input"><input id="productTotal'+num+'" type="number" value="0.00" onkeyup="calcularmonto(true)" onclick="calcularmonto(true)" step="0.01" min="0"></td>';   
          nuevaFila+='<td class="mx-td-input"><input id="productFechavencimiento'+num+'" type="date" style="padding: 8px;"></td>';
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
        var productPrecio = $("#productPrecio"+num).val();
        data = data+'&'+idproducto+','+productCant+','+productUnidad+','+productFechavencimiento+','+productTotal+','+productPrecio;
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