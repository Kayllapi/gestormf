<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
        <span>Registrar Guía de Remisión</span>
        <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionguiaremision') }}">
            <i class="fa fa-angle-left"></i> Ir Atras
        </a>
    </div>
</div>
<div id="carga-guiaremision">
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
                <div class="col-sm-12">
                    <div class="col-sm-3">
                    </div>
                    <div class="col-sm-2">
                        <label>Importar Productos *</label>
                        <select id="iddocumento">
                            <option value="venta">Venta</option>
                            <option value="compra">Compra</option>
                            <option value="boletafactura">Boleta o Facturas</option>
                        </select>
                    </div>
                    <div class="col-sm-4"  id="div-buscador-venta">
                        <label>Código de Venta *</label>
                        <input type="text"  id="codigo_venta">
                    </div>
                    <div class="col-sm-4" value id="div-buscador-compra"  style="display:none;">
                        <label>Código de Compra *</label>
                        <input type="text"  id="codigo_compra">
                    </div>
                    <div class="col-sm-2 div-buscador-facturaboleta"  style="display:none;" >
                        <label>Serie *</label>
                        <select id="facturador_serie">
                            <option></option>
                        </select>
                    </div>
                    <div class="col-sm-2 div-buscador-facturaboleta"  style="display:none;">
                        <label>Correlativo *</label>
                        <input type="text"  id="facturador_correlativo">
                    </div>
                </div>
            </div>
        </div>
        <div id="load-agenciaserie"></div>
        <div id="cont-form-facturacionguiaremision">
                <input type="hidden" id="idventa">
                <input type="hidden" id="idfacturacion">
                <input type="hidden" id="idcompra">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="list-single-main-wrapper fl-wrap">
                            <div class="breadcrumbs gradient-bg fl-wrap">
                              <span>Remitente</span>
                            </div>
                        </div>
                        <label>Remitente *</label>
                        <select id="agencia" disabled>
                            <option></option>
                            @foreach($agencias as $value)
                                <option value="{{ $value->id }}"?>{{ $value->ruc }} - {{ $value->nombrecomercial }}</option>
                            @endforeach
                        </select>
                        <label>Punto de Partida *</label>
                        <select id="puntopartida">
                            <option></option>
                        </select>
                        <label>Dirección de Partida *</label>
                        <input type="text" id="direccionpartida"/>
                    </div>
                    <div class="col-sm-4">
                        <div class="list-single-main-wrapper fl-wrap">
                            <div class="breadcrumbs gradient-bg fl-wrap">
                              <span>Destinatario</span>
                            </div>
                        </div>
                        <label>Destinatario *</label>
                        <div class="row">
                          <div class="col-sm-9">
                              <select id="destinatario">
                                  <option></option>
                              </select>
                          </div>
                          <div class="col-sm-3">    
                              <a href="javascript:;" id="modal-registrardestinatario" class="btn btn-warning"><i class="fa fa-plus"></i> Agregar</a>
                          </div>
                        </div>
                        <label>Punto de Llegada *</label>
                        <select id="destinatario_ubigeo">
                            <option></option>
                        </select>
                        <label>Dirección de Llegada *</label>
                        <input type="text" id="destinatario_direccion"/>
                    </div>
                    <div class="col-sm-4">
                        <div class="list-single-main-wrapper fl-wrap">
                            <div class="breadcrumbs gradient-bg fl-wrap">
                              <span>Detalle de Envio</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Motivo *</label>
                                <select id="motivo">
                                    @foreach ($motivos as $value)
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label>Fecha de Traslado *</label>
                                <input type="date" id="fechatraslado"/>
                            </div>
                        </div>
                        <label>Transportista *</label>
                        <div class="row">
                          <div class="col-sm-9">
                              <select id="transportista">
                                  <option></option>
                              </select>
                          </div>
                          <div class="col-sm-3">    
                              <a href="javascript:;" id="modal-registrartransportista" class="btn btn-warning"><i class="fa fa-plus"></i> Agregar</a>
                          </div>
                        </div>
                        <label>Observación *</label>
                        <input type="text" id="observacion"/>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table" id="tabla-contenido" style="margin-bottom: 5px;">
                        <thead class="thead-dark">
                            <tr>
                                <th width="15%">Código</th>
                                <th>Producto</th>
                                <th width="10px"></th>
                                @if($configuracion!='')
                                    @if($configuracion['estadostock']==1)
                                        <th width="50px">Stock</th>
                                    @endif
                                @endif
                                <th width="60px">Cantidad</th>
                                <th width="10px"></th>
                            </tr>
                            <tr>
                                <td class="mx-td-input">
                                    <input type="text" id="buscarcodigoproducto">
                                </td>
                                <td colspan="{{ $configuracion!=''?($configuracion['estadostock']==1?'4':'3'):'4' }}" class="mx-td-input">
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
                <a href="javascript:;" onclick="registrar_guiaremision()" class="btn big-btn color-bg flat-btn" style="width: 100%;">Registrar</a>
        </div>
    </div>
</div>
</div>
@include('app.modal_usuario_registrar',[
    'nombre'          =>'Registrar Destinatario',
    'modal'           =>'registrardestinatario',
    'idusuario'       =>'destinatario',
    'usuariodireccion'=>'destinatario_direccion',
    'usuarioubigeo'   =>'destinatario_ubigeo'
])
@include('app.modal_usuario_registrar',[
    'nombre'    =>'Registrar Transportista',
    'modal'     =>'registrartransportista',
    'idusuario' =>'transportista'
])
<style>
  .tdcarga > div {
      text-align: center;
  }
  .tdcarga > div > img{
      height: 38px;
  }
</style>
<script>
function registrar_guiaremision(){
    callback({
        route: 'backoffice/tienda/nuevosistema/{{ $tienda->id }}/facturacionguiaremision',
        method: 'POST',
        carga: '#carga-guiaremision',
        data:{
            view: 'registrar',
            idtienda: '{{ $tienda->id }}',
            productos: selectproductos(),
            idventa: $('#idventa').val(),
            idfacturacion: $('#idfacturacion').val(),
            agencia: $('#agencia').val(),
            puntopartida: $('#puntopartida').val(),
            direccionpartida: $('#direccionpartida').val(),
            destinatario: $('#destinatario').val(),
            destinatario_ubigeo: $('#destinatario_ubigeo').val(),
            destinatario_direccion: $('#destinatario_direccion').val(),
            motivo: $('#motivo').val(),
            fechatraslado: $('#fechatraslado').val(),
            transportista: $('#transportista').val(),
            observacion: $('#observacion').val(),
            subtotal: $('#subtotal').val(),
            igv: $('#igv').val(),
            total: $('#total').val(),
        }
    },
    function(resultado){
        //location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionguiaremision') }}';                                    
    })
}
  
$("#idagencia").select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1
}).on("change", function(e) {
    $('#cont-agenciaserie').css('display','none');
    $.ajax({
            url:"{{url('backoffice/tienda/nuevosistema/'.$tienda->id.'/facturacionguiaremision/show-selecionarserie')}}",
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
              
              $('#agencia').html('<option value="'+respuesta['agencia'].id+'">'+respuesta['agencia'].ruc+' - '+respuesta['agencia'].nombrecomercial+'</option>');
              $("#puntopartida").html('<option value="'+respuesta['agencia'].idubigeo+'">'+respuesta['agencia'].ubigeonombre+'</option>');
              $('#direccionpartida').val(respuesta['agencia'].direccion);
              
            }
        })
}).val({{ $configuracion_facturacion!=''?($configuracion_facturacion['idempresapordefecto!']=''?$configuracion_facturacion['idempresapordefecto']:'null'):'null' }}).trigger("change");

$('#iddocumento').select2({
    placeholder: "--  Seleccionar --",
    minimumResultsForSearch: -1,
}).on("change", function (e) {
    $('#div-buscador-compra').css('display','none');
    $('#div-buscador-venta').css('display','none');
    $('.div-buscador-facturaboleta').css('display','none');
    if (e.currentTarget.value == 'venta') {
        $('#div-buscador-venta').css('display','block');
    }else if (e.currentTarget.value== 'boletafactura') {
        $('.div-buscador-facturaboleta').css('display'  ,'block');
    }else if (e.currentTarget.value == 'compra') {
        $('#div-buscador-compra').css('display','block');
    }
}); 

$('#facturador_serie').select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
})
$("#agencia").select2({
    placeholder: '-- Seleccionar --',
    minimumResultsForSearch: -1
});
    
$("#puntopartida").select2({
    @include('app.select2_ubigeo')
});
  
$("#destinatario").select2({
    @include('app.select2_cliente')
}).on("change", function(e) {
    $.ajax({
        url:  "{{url('backoffice/tienda/nuevosistema/'.$tienda->id.'/facturacionguiaremision/showseleccionarusuario')}}",
        type: 'GET',
        data: {
            idusuario : e.currentTarget.value
        },
        success: function (respuesta) {
            $('#destinatario_direccion').val(respuesta['usuario'].direccion);
            if (respuesta['usuario'].idubigeo != 0) {
                $("#destinatario_ubigeo").html('<option value="'+respuesta['usuario'].idubigeo+'">'+respuesta['usuario'].ubigeonombre+'</option>');
            } else {
                $("#destinatario_ubigeo").html('<option></option>');
            }
        }
    })
});
 
$("#destinatario_ubigeo").select2({
    @include('app.select2_ubigeo')
});
  
$("#motivo").select2({
    placeholder: "-- Seleccionar Motivo --"
});
  
$("#transportista").select2({
    @include('app.select2_cliente')
});

$('#codigo_venta').keyup(function(e) {
    var code = (e.keyCode ? e.keyCode : e.which);
    if (code==13) {
        cargarventa_venta( $('#iddocumento').val(), $('#codigo_venta').val() );
    }
})
$('#codigo_compra').keyup(function(e) {
    var code = (e.keyCode ? e.keyCode : e.which);
    if (code==13) {
        cargarventa_compra( $('#iddocumento').val(), $('#codigo_compra').val() );
    }
})
$('#facturador_serie, #facturador_correlativo').keyup(function(e) {
    var code = (e.keyCode ? e.keyCode : e.which);
    if (code==13) {
        cargarventa_boletafactura( $('#iddocumento').val(), $('#facturador_serie').val(), $('#facturador_correlativo').val() );
    }
})
        // Venta
        function cargarventa_venta(iddocumento, codigo_venta) {
            load('#load-agenciaserie');
            $('#cont-form-facturacionguiaremision').css('display', 'none');
          
            $('#tabla-contenido > tbody').html('');
            $('#button-carga').css('display', 'none');

            $.ajax({
                url:  "{{url('backoffice/tienda/nuevosistema/'.$tienda->id.'/facturacionguiaremision/show-seleccionarventa')}}",
                type: 'GET',
                data: {
                    iddocumento:  iddocumento,
                    codigo_venta: codigo_venta
                },
                success: function (respuesta){
                    if (respuesta["venta"] != undefined) {
                        $('#load-agenciaserie').html('');
                        $('#cont-form-facturacionguiaremision').css('display', 'block');

                        $('#idventa, #idfacturacion, #idcompra').val('');
                        $('#idventa').val(respuesta['venta'].id);
                        
                        $("#cliente").html('<option value="'+respuesta['cliente'].id+'">'+respuesta['cliente'].nombreCompleto+'</option>');
                        $("#destinatario_ubigeo").html('<option value="'+respuesta['cliente'].idubigeo+'">'+respuesta['cliente'].ubigeonombre+'</option>');
                        $('#destinatario_direccion').val(respuesta['cliente'].direccion);
                      
               
                        respuesta['detalle'].forEach((value) => {
                            agregarproducto(
                                value.s_idproducto,
                                value.codigoProduct,
                                value.nombreProduct,
                                value.imagen,
                                respuesta["tienda"].id,
                            );
                        });
                      
                        $('#button-carga').css('display', 'block');
                    } else {
                        $('#load-agenciaserie').html(`<div class="alert alert-danger" style="font-size: 20px;
                                                                                                              padding-top: 10px;
                                                                                                              padding-bottom: 10px;
                                                                                                              margin-bottom: 15px;
                                                                                                              margin-top: 5px;">
                                                                          No existe la Boleta/Factura!
                                                                       </div>`);
                        $('#button-carga').css('display', 'none');
                    } 
                }
            })
        }
        // Compra
        function cargarventa_compra(iddocumento, codigo_compra) {
            load('#load-agenciaserie');
            $('#cont-form-facturacionguiaremision').css('display', 'none');
          
            $('#tabla-contenido > tbody').html('');
            $('#button-carga').css('display', 'none');

            $.ajax({
                url:  "{{url('backoffice/tienda/nuevosistema/'.$tienda->id.'/facturacionguiaremision/show-seleccionarcompra')}}",
                type: 'GET',
                data: {
                    iddocumento:   iddocumento,
                    codigo_compra: codigo_compra
                },
                success: function (respuesta){
                    if(respuesta["compra"] != undefined) {
                        $('#load-agenciaserie').html('');
                        $('#cont-form-facturacionguiaremision').css('display', 'block');
                      
                        $('#idventa, #idfacturacion, #idcompra').val('');
                        $('#idcompra').val(respuesta['compra'].id);
                        $("#cliente").html('<option value="'+respuesta['cliente'].id+'">'+respuesta['cliente'].nombreCompleto+'</option>');
                        $("#destinatario_ubigeo").html('<option value="'+respuesta['cliente'].idubigeo+'">'+respuesta['cliente'].ubigeonombre+'</option>');
                        $('#destinatario_direccion').val(respuesta['cliente'].direccion);
                      
                        respuesta['detalle'].forEach((value) => {
                            agregarproducto(
                                value.s_idproducto,
                                value.codigoProduct,
                                value.nombreProduct,
                                value.imagen,
                                respuesta["tienda"].id,
                            );
                        });
                      
                        $('#button-carga').css('display', 'block');
                    }else{
                        $('#load-agenciaserie').html(`<div class="alert alert-danger" style="font-size: 20px;
                                                                                                              padding-top: 10px;
                                                                                                              padding-bottom: 10px;
                                                                                                              margin-bottom: 15px;
                                                                                                              margin-top: 5px;">
                                                                          No existe la Boleta/Factura!
                                                                       </div>`);
                        $('#button-carga').css('display', 'none');
                    } 
                }
            })
        }
        // Boleta-Factura
        function cargarventa_boletafactura(iddocumento, facturador_serie, facturador_correlativo){
            load('#load-agenciaserie');
            $('#cont-form-facturacionguiaremision').css('display', 'none');
          
            $('#tabla-contenido > tbody').html('');
            $('#button-carga').css('display', 'none');

            $.ajax({
                url:  "{{url('backoffice/tienda/nuevosistema/'.$tienda->id.'/facturacionguiaremision/show-seleccionarboletafactura')}}",
                type: 'GET',
                data: {
                    iddocumento:            iddocumento,
                    facturador_serie:       facturador_serie,
                    facturador_correlativo: facturador_correlativo
                },
                success: function (respuesta){
                    if(respuesta["boletafactura"] != undefined) {
                        $('#load-agenciaserie').html('');
                        $('#cont-form-facturacionguiaremision').css('display', 'block');
                      
                        $('#idventa, #idfacturacion, #idcompra').val('');
                        $('#idfacturacion').val(respuesta['boletafactura'].id);
                        $("#cliente").html('<option value="'+respuesta['cliente'].id+'">'+respuesta['cliente'].nombreCompleto+'</option>');
                        $("#destinatario_ubigeo").html('<option value="'+respuesta['cliente'].idubigeo+'">'+respuesta['cliente'].ubigeonombre+'</option>');
                        $('#destinatario_direccion').val(respuesta['cliente'].direccion);
                      
                        respuesta['detalle'].forEach((value) => {
                            agregarproducto(
                                value.s_idproducto,
                                value.codigoProduct,
                                value.nombreProduct,
                                value.imagen,
                                respuesta["tienda"].id,
                            );
                        });
                      
                        $('#button-carga').css('display', 'block');
                    }else{
                        $('#load-agenciaserie').html(`<div class="alert alert-danger" style="font-size: 20px;
                                                                                                              padding-top: 10px;
                                                                                                              padding-bottom: 10px;
                                                                                                              margin-bottom: 15px;
                                                                                                              margin-top: 5px;">
                                                                          No existe la Boleta/Factura!
                                                                       </div>`);
                        $('#button-carga').css('display', 'none');
                    } 
                }
            })
        }

// PRODUCTOS
$('#buscarcodigoproducto').keyup( function(e) {
    if(e.keyCode == 13){
        buscarcodigo('#buscarcodigoproducto');
    }
})
  
function buscarcodigo(pthis){
    if($(pthis).val()!=''){
        $.ajax({
            url:"{{url('backoffice/tienda/nuevosistema/'.$tienda->id.'/venta/showseleccionarproductocodigo')}}",
            type:'GET',
            data: {
                codigoproducto : $(pthis).val()
            },
            beforeSend: function (data) {
                var nuevaFila='<tr style="background-color: #008cea;color: #fff;">';
                                nuevaFila+='<td id="tdcargaproducto"" colspan="9" class="tdcarga"></td>';
                                nuevaFila+='</tr>';
                $("#tabla-contenido > tbody#tbodycarga").html(nuevaFila);
                load('#tdcargaproducto');
            },
            success: function (respuesta){
              $("#tabla-contenido > tbody#tbodycarga").html('');
              $('#buscarcodigoproducto').val('');
              if(respuesta["resultado"]=='ERROR'){
                  carga({
                      input:'#carga-guiaremision',
                      color:'danger',
                      mensaje: respuesta['mensaje']
                  });
              }else{
                  agregarproducto(
                    respuesta["producto"].id,
                    respuesta["producto"].codigo,
                    respuesta["producto"].nombre,
                    respuesta["producto"].imagen,
                    respuesta["producto"].idtienda,
                  );
                  removecarga({input:'#carga-guiaremision'});
              }
            },
            error:function(respuesta){
                  carga({
                      input:'#carga-guiaremision',
                      color:'danger',
                      mensaje:formerror({dato:respuesta})
                  });
            }
        })
    } 
}

$("#idproducto").select2({
    @include('app.select2_producto',[
        'idtienda'=>$tienda->id
    ])
}).on("change", function(e) {
    $.ajax({
        url:  "{{url('backoffice/tienda/nuevosistema/'.$tienda->id.'/facturacionguiaremision/showseleccionarproducto')}}",
        type: 'GET',
        data: {
            idproducto : e.currentTarget.value
        },
        beforeSend: function (data) {
            var nuevaFila='<tr style="background-color: #008cea;color: #fff;">';
                            nuevaFila+='<td id="tdcargaproducto"" colspan="9" class="tdcarga"></td>';
                            nuevaFila+='</tr>';
            $("#tabla-contenido > tbody#tbodycarga").html(nuevaFila);
            load('#tdcargaproducto');
        },
        success: function (respuesta){
            $("#tabla-contenido > tbody#tbodycarga").html('');
            if (respuesta["producto"]!=null) {
                agregarproducto(
                    respuesta["producto"].id,
                    respuesta["producto"].codigo,
                    respuesta["producto"].nombre,
                    respuesta["producto"].imagen,
                    respuesta["producto"].idtienda,
                );
            }
        }
    })
});

function agregarproducto(idproducto,codigo,nombre,imagen,idtienda) {
    $("#codigoproducto").val('');
    $("#idproducto").html('');
  
    var newimagen = '{{ url('public/backoffice/sistema/sin_imagen_cuadrado.png') }}';
      if(imagen!=null){
          newimagen = '{{ url('public/backoffice/tienda') }}/'+idtienda+'/producto/40/'+imagen;
      }
      var imagentd = '<div style="background-image: url('+newimagen+');'+
                            'background-repeat: no-repeat;'+
                            'background-size: contain;'+
                            'background-position: center;'+
                            'width: 50px;'+
                            'height: 34px;">'+
                          '</div>';
  
    var num = $("#tabla-contenido > tbody#tbody").attr('num');
    var nuevaFila='<tr id="'+num+'" idproducto="'+idproducto+'" style="background-color: #0ec529;color: #fff;">';
        nuevaFila+='<td>'+codigo+'</td>';
        nuevaFila+='<td>'+nombre+'</td><td>'+imagentd+'</td>';
        nuevaFila+='<td class="mx-td-input"><input id="productCant'+num+'" type="number" value="1"></td>';    
        nuevaFila+='<td><a id="del'+num+'" href="javascript:;" onclick="eliminarproducto('+num+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>'
        nuevaFila+='</tr>';
    $("#tabla-contenido > tbody#tbody").append(nuevaFila);
    $("#tabla-contenido > tbody#tbody").attr('num',parseInt(num)+1);
    $('#productCant'+num).select();
    $('#productCant'+num).keyup( function(e) {
        if(e.keyCode == 13){
            $('#buscarcodigoproducto').select();
        }
    })
}

function eliminarproducto(num){
    $("#tabla-contenido > tbody#tbody tr#"+num).remove();
}

function selectproductos(){
    let data = [];
    $("#tabla-contenido > tbody#tbody tr").each(function() {
        let num = $(this).attr('id');
        data.push({
            idproducto:    $(this).attr('idproducto'),
            cantidad:      $("#productCant"+num).val()
        });
    });
    return JSON.stringify(data);
}
</script>
