<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/nuevosistema/{{ $tienda->id }}/facturacionboletafactura',
        method: 'POST',
        data:{
            view:      'registrar',
            idtienda:  '{{ $tienda->id }}',
            productos: selectproductos(),
        } 
    },this)">
  <div id="carga-comprobante">
    <div class="profile-edit-container">
        <div class="custom-form">
            <!-- Formulario para solicitar datos para el comprobante -->
            <div class="row">
                <div class="col-sm-6">
                    <label>Cliente *</label>
                    <div class="row">
                       <div class="col-md-9">
                          <select id="idcliente">
                              @if($configuracion!='')
                              <option value="{{ $configuracion['idclientepordefecto'] }}">{{ $configuracion['clientepordefecto'] }}</option>
                              @else
                              <option></option>
                              @endif
                          </select>
                       </div>
                       <div class="col-md-3">
                          <a href="javascript:;" id="modal-registrarcliente" class="btn btn-warning"><i class="fa fa-plus"></i> Agregar</a>
                       </div>
                    </div> 
                    <label>Dirección *</label>
                      <input type="text" id="direccion" value="{{$configuracion!=''?$configuracion['clientedireccionpordefecto']:''}}"/>                   
                    <label>Ubigeo *</label>
                      <select id="idubigeo">
                          @if($configuracion!='')
                          <option value="{{ $configuracion['clienteidubigeopordefecto'] }}">{{ $configuracion['clientedireccionpordefecto'] }}</option>
                          @else
                          <option></option>
                          @endif
                      </select>
                </div>
                <div class="col-sm-6">
                    <label>Agencia *</label>
                    <select id="idagencia">
                        <option></option>
                        @foreach ($agencias as $item_agencia) 
                        <option value="{{ $item_agencia->id }}">{{ $item_agencia->ruc }} - {{ $item_agencia->nombrecomercial }}</option>
                        @endforeach
                    </select>
                    <label>Moneda *</label>
                    <select id="idmoneda">
                        <option></option>
                        @foreach( $monedas as $item_moneda )
                        <option value="{{ $item_moneda->id }}">{{ $item_moneda->nombre }}</option>
                        @endforeach
                    </select>                    
                    <label>Comprobante *</label>
                    <select id="idtipocomprobante">
                        <option></option>
                        @foreach( $comprobantes as $item_comprobante )
                        <option value="{{ $item_comprobante->id }}">{{ $item_comprobante->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>   
            <!-- Seccion para seleccionar los productos -->
            <div class="table-responsive">
                <table class="table" id="tabla-contenido">
                    <thead class="thead-dark">
                        <tr>
                            <th width="15%">Código</th>
                            <th>Producto</th>
                            @if($configuracion_comer!='')
                                @if($configuracion_comer['estadostock']==1)
                                <th width="50px">Stock</th>
                                @endif
                            @endif
                            <th width="60px">Cantidad</th>
                            <th width="110px">P. Unitario</th>
                            <th width="110px">P. Total</th> 
                            <th width="10px"></th>
                        </tr>
                        <tr>
                            <td class="mx-td-input">
                               <input type="text" id="buscarcodigoproducto"/>
                            </td>
                            <td colspan="{{$configuracion_comer!=''?($configuracion_comer['estadostock']==1?'5':'4'):'5'}}" class="mx-td-input">
                                <select id="idproducto">
                                    <option></option>
                                </select>
                            </td>
                            <td width="auto"></td>
                        </tr>
                    </thead>
                    <tbody num="0" id="tbody"></tbody>
                    <tbody num="0" id="tbodycarga"></tbody>
                </table>
            </div>
            <!-- Seccion mostrando el total, subtotal, igv -->
            <div class="row">
                <div class="col-md-4">
                </div>
                <div class="col-md-4"> 
                    <label>Sub Total</label>
                    <input type="text" id="subtotal" placeholder="0.00" disabled>
                    <label>IGV</label>
                    <input type="text" id="igv" placeholder="0.00" disabled>
                    <label>Total</label>
                    <input type="text" id="total" placeholder="0.00" disabled>
                </div>
                <div class="col-md-4">
                </div>
            </div> 
        </div>
    </div>
    <div class="profile-edit-container">
        <div class="custom-form">
          <a href="javascript:;" onclick="registrar_comprobante()" id="registrar_comprobante" class="btn  big-btn  color-bg flat-btn">Guardar Cambios</a>
        </div>
    </div> 
    </div>
</form>
@include('app.modal_usuario_registrar',[
    'nombre'            =>'Registrar Cliente',
    'modal'             =>'registrarcliente',
    'idusuario'         =>'idcliente',
    'usuariodireccion'  =>'direccion',
    'usuarioubigeo'     =>'idubigeo'
])
<script>
   // Buscador de Clientes
   $('#idcliente').select2({
        ajax: {
            url:      "{{url('backoffice/tienda/nuevosistema/'.$tienda->id.'/facturacionboletafactura/showlistarusuario')}}",
            dataType: 'json',
            delay:    250,
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
        placeholder: '---  Seleccionar ---',
        minimumInputLength: 2
    }).on("change", function(e) {
        $('#registrar_comprobante').css('display','none');
        $.ajax({
            url:  "{{url('backoffice/tienda/nuevosistema/'.$tienda->id.'/facturacionboletafactura/showseleccionarusuario')}}",
            type: 'GET',
            data: {
                idusuario : e.currentTarget.value
            },
            success: function (respuesta){
                $('#registrar_comprobante').css('display','block');
                $('#direccion').val(respuesta['usuario'].direccion);
                if(respuesta['usuario'].idubigeo!=0){
                    $("#idubigeo").html('<option value="'+respuesta['usuario'].idubigeo+'">'+respuesta['usuario'].ubigeonombre+'</option>');
                }else{
                    $("#idubigeo").html('<option></option>');
                }
            }
        })
   });
  //Buscador de Ubigeo
  $('#idubigeo').select2({
        ajax: {
            url:      "{{url('backoffice/tienda/nuevosistema/'.$tienda->id.'/facturacionboletafactura/showlistarubigeo')}}",
            dataType: 'json',
            delay:    250,  
            data: function (params) {
                return {
                      buscar: params.term,
                      view: 'listarubigeo'
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
  // AGENCIA - seleccionando la agencia activa de la tienda
  $('#idagencia').select2({
        placeholder: '---  Seleccionar ---',
        allowClear: true,
        minimumResultsForSearch: -1
  }).val({{ $configuracion!=''?($configuracion['idempresapordefecto']!=''?$configuracion['idempresapordefecto']:'null'):'null' }}).trigger("change");
  //Tipo de moneda
  $('#idmoneda').select2({
        placeholder: '---  Seleccionar ---',
        allowClear: true,
        minimumResultsForSearch: -1
  }).val({{ $configuracion!=''?($configuracion['idmonedapordefecto']!=''?$configuracion['idmonedapordefecto']:1):1 }}).trigger("change");
  //Tipo de comproban
  $('#idtipocomprobante').select2({
        placeholder: '---  Seleccionar ---',
        allowClear: true,
        minimumResultsForSearch: -1
    }); 
  
  //Seleccionar y  Busqueda Producto
   $("#idproducto").select2({
    @include('app.select2_producto',[
        'idtienda'=>$tienda->id
    ])
      }).on("change", function(e) {
        $.ajax({
            url:  "{{url('backoffice/tienda/nuevosistema/'.$tienda->id.'/facturacionboletafactura/showseleccionarproducto')}}",
            type: 'GET',
            data: {
                idproducto : e.currentTarget.value
            },
            success: function (respuesta){
                if(respuesta["producto"]!=null){
                    agregarproducto(
                        respuesta["producto"].id,
                        respuesta["producto"].codigo,
                        respuesta["producto"].nombre,
                        respuesta["stock"],
                        respuesta["producto"].precioalpublico,
                        respuesta["producto"].s_idproducto,
                    );
                }
            }
        })
    });
  
   //Buscador de Codigo
   $('#buscarcodigoproducto').select();
  
   $('#buscarcodigoproducto').keyup( function(e) {
        if(e.keyCode == 13){
            buscarcodigo('#buscarcodigoproducto');
        }
      if(e.keyCode == 27){
        $('#registrar_comprobante').focus();
        $('#registrar_comprobante').select();
    }
    })

  //Buscar Codigo 
  function buscarcodigo(pthis){
    if($(pthis).val()!=''){
        $.ajax({
            url:"{{url('backoffice/tienda/nuevosistema/'.$tienda->id.'/facturacionboletafactura/showseleccionarproductocodigo')}}",
            type:'GET',
            data: {
                codigoproducto : $(pthis).val()
            },
            beforeSend: function (data) {
                var nuevaFila='<tr style="background-color: #008cea;color: #fff;">';
                                nuevaFila+='<td id="tdcargaproducto"" colspan="9" class="tddescuento"></td>';
                                nuevaFila+='</tr>';
                $("#tabla-contenido > tbody#tbodycarga").html(nuevaFila);
                load('#tdcargaproducto');
            },
            success: function (respuesta){
              $("#tabla-contenido > tbody#tbodycarga").html('');
              $('#buscarcodigoproducto').val('');
              if(respuesta["resultado"]=='ERROR'){
                  carga({
                      input:'#carga-comprobante',
                      color:'danger',
                      mensaje: respuesta['mensaje']
                  });
              }else{
                  agregarproducto(
                    respuesta["producto"].id,
                     respuesta["producto"].codigo,
                     respuesta["producto"].nombre,
                     respuesta["stock"],
                     respuesta["producto"].precioalpublico,
                     respuesta["producto"].s_idproducto,
                  );
                  removecarga({input:'#carga-comprobante'});
              }
            },
            error:function(respuesta){
                  carga({
                      input:'#carga-comprobante',
                      color:'danger',
                      mensaje:formerror({dato:respuesta})
                  });
            }
        })
      }      
    }
   //Registrar Comprobante
   function registrar_comprobante(){
      callback({
          route: 'backoffice/tienda/nuevosistema/{{ $tienda->id }}/facturacionboletafactura',
          method: 'POST',
          carga: '#carga-comprobante',
          data:{
                view: 'registrar',
                productos: selectproductos(),
                idcliente: $('#idcliente').val(),
                direccion: $('#direccion').val(),
                idubigeo: $('#idubigeo').val(),
                idagencia: $('#idagencia').val(),
                idmoneda: $('#idmoneda').val(),
                idtipocomprobante: $('#idtipocomprobante').val(),
                subtotal: $('#subtotal').val(),
                igv: $('#igv').val(),
                total: $('#total').val()
            }
        },
        function(resultado){
             location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionboletafactura') }}';                                                  
        })
    }

    // funcion agregarproducto(), agregando en una nueva fila el producto
    function agregarproducto(idproducto,codigo,nombre,stock,precioalpublico,s_idproducto){
        $("#codigoproducto").val('');
        $("#idproducto").html('');
      
        var num = $("#tabla-contenido > tbody#tbody").attr('num');
        var style   = 'background-color: #0ec529;color: #fff;';
        var tdstock = '';
        @if($configuracion_comer!='')
            @if($configuracion_comer['estadostock']==1)
                if(stock<1){
                    style = 'background-color: #ce0e00;color: #fff;';
                }
                tdstock = '<td style="text-align: center"><!--a href="javascript:;" onclick="agregarproductounidad('+s_idproducto+')" id="modal-registrarproductounidad"><i class="fa fa-download"></i></a--> '+stock+' </td>';
            @endif
        @endif
        var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'" nombreproducto="'+codigo+' - '+nombre+'" style="'+style+'">';
            nuevaFila+='<td>'+codigo+'</td>';
            nuevaFila+='<td>'+nombre+'</td>'+tdstock;
            nuevaFila+='<td class="mx-td-input"><input id="productCant'+num+'" type="number" value="1" onkeyup="calcularmonto()" onchange="calcularmonto()"></td>';
            nuevaFila+='<td class="mx-td-input"><input id="productUnidad'+num+'" type="number" value="'+precioalpublico+'" step="0.01" min="0" disabled></td>';
            nuevaFila+='<td class="mx-td-input"><input id="productTotal'+num+'" type="text" value="0.00" disabled></td>';       
            nuevaFila+='<td><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>'
            nuevaFila+='</tr>';
         $("#tabla-contenido > tbody#tbody").append(nuevaFila);
         $("#tabla-contenido > tbody#tbody").attr('num',parseInt(num)+1);  

          setTimeout(function(){ $('#productCant'+num).select(); }, 100);
          $('#productFechavencimiento'+num).keyup( function(e) {
          if(e.keyCode == 13){
              $('#buscarcodigoproducto').select();
          }
          if(e.keyCode == 27){
              $('#registrar_comprobante').focus();
              $('#registrar_comprobante').select();
          }
      })
          calcularmonto();
    }
  
    // Funcion para eliminar una fila de los productos
    function eliminarproducto(num){
        $("#tabla-contenido tbody tr#"+num).remove();
        calcularmonto();
    }
    
    // Funcion para calcular el monto
    function calcularmonto(){
        var subtotal  = 0;
        var total     = 0;
        var igv       = 0;
        $("#tabla-contenido tbody tr").each(function() {
            var num       = $(this).attr('id');
            var cantidad  = parseFloat($("#productCant"+num).val());
            var precio    = parseFloat($("#productUnidad"+num).val());
            var totalFila = ((cantidad*precio)).toFixed(2);
            $("#productTotal"+num).val(parseFloat(totalFila).toFixed(2));
            total = total+parseFloat((cantidad*precio).toFixed(2));
        });
        total    = (parseFloat(total)).toFixed(2);
        subtotal = parseFloat(total/1.18);
        igv      = parseFloat(total - subtotal);
        $("#subtotal").val((parseFloat(subtotal)).toFixed(2));
        $("#igv").val((parseFloat(igv)).toFixed(2));
        $("#total").val((parseFloat(total)).toFixed(2));  
    }

    // Funcion para recorrer los productos
    function selectproductos(){
        var data = '';
        $("#tabla-contenido tbody tr").each(function() {
            var num            = $(this).attr('id');
            var idproducto     = $(this).attr('idproducto');
            var idventa        = 0;
            var productCant    = $("#productCant"+num).val();
            var productUnidad  = $("#productUnidad"+num).val();
            var idunidadmedida = 0;
            var productTotal   = $("#productTotal"+num).val();
            data = data+'/&/'+idproducto+'/,/'+productCant+'/,/'+productUnidad+'/,/'+idunidadmedida+'/,/'+productTotal+'/,/'+idventa;
        });
        return data;
    }
</script>
