@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Comprobante</span>
      <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/venta') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
    </div>
</div>
<?php
$facturacionboletafactura = DB::table('s_facturacionboletafactura')
            ->join('users as responsable','responsable.id','s_facturacionboletafactura.idusuarioresponsable')
            ->join('s_agencia','s_agencia.id','s_facturacionboletafactura.idagencia')
            ->leftJoin('s_facturacionrespuesta','s_facturacionrespuesta.id','s_facturacionboletafactura.idfacturacionrespuesta')
            ->where('s_facturacionboletafactura.idventa',$venta->id)
            ->select(
                's_facturacionboletafactura.*',
                's_facturacionrespuesta.codigo as respuestacodigo',
                's_facturacionrespuesta.estado as respuestaestado',
                's_facturacionrespuesta.mensaje as respuestamensaje',
                's_facturacionrespuesta.nombre as respuestanombre',
              )
            ->first();
?>
@if($facturacionboletafactura!='')
    @if($facturacionboletafactura->respuestaestado=='ACEPTADA' or $facturacionboletafactura->respuestacodigo == 1033)
        <div class="custom-form" style="margin-bottom: 5px;">
            <a href="{{ url('public/backoffice/tienda/'.$tienda->id.'/sunat/produccion/boletafactura/'.$facturacionboletafactura->respuestanombre.'.xml') }}" download class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
            <i class="fa fa-download"></i> Descargar XML</a>
            <a href="{{ url('public/backoffice/tienda/'.$tienda->id.'/sunat/produccion/boletafactura/R-'.$facturacionboletafactura->respuestanombre.'.zip') }}" download class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
            <i class="fa fa-download"></i> Descargar CDR</a>
            <a href="javascript:;" onclick="openDocumento('ticketpdf')" class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
            <i class="fa fa-file-pdf-o"></i> Ver PDF Ticket</a>
            <a href="javascript:;" onclick="openDocumento('a4pdf')" class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;">
            <i class="fa fa-file-pdf-o"></i> Ver PDF A4</a>
            <a href="javascript:;" class="btn big-btn color-bg flat-btn" style="float: left;margin-right: 5px;" id="modal-enviarcorreo" onclick="enviarcorreo()">
            <i class="fa fa-paper-plane"></i> Enviar a Correo</a>
        </div>
        <iframe id="content-ticketpdf" style="display: block" src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionboletafactura/'.$facturacionboletafactura->id.'/edit?view=ticketpdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>
        <iframe id="content-a4pdf" style="display: none" src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionboletafactura/'.$facturacionboletafactura->id.'/edit?view=a4pdf') }}#zoom=130" frameborder="0" width="100%" height="600px"></iframe>
    @else
        @if($facturacionboletafactura->respuestaestado=='OBSERVACIONES')
          <div class="mensaje-warning"><?php echo $facturacionboletafactura->respuestacodigo ?> - <?php echo $facturacionboletafactura->respuestamensaje ?></div>
        @elseif($facturacionboletafactura->respuestaestado=='RECHAZADA')
          <div class="mensaje-warning"><?php echo $facturacionboletafactura->respuestacodigo ?> - <?php echo $facturacionboletafactura->respuestamensaje ?></div>
        @elseif($facturacionboletafactura->respuestaestado=='EXCEPCION')
          <div class="mensaje-warning"><?php echo $facturacionboletafactura->respuestacodigo ?> - <?php echo $facturacionboletafactura->respuestamensaje ?></div>
        @elseif($facturacionboletafactura->respuestaestado=='NOENVIADO')
          <div class="mensaje-warning"><?php echo $facturacionboletafactura->respuestacodigo ?> - <?php echo $facturacionboletafactura->respuestamensaje ?></div>
        @elseif($facturacionboletafactura->respuestaestado=='ERROR')
          <div class="mensaje-warning"><?php echo $facturacionboletafactura->respuestacodigo ?> - <?php echo $facturacionboletafactura->respuestamensaje ?></div>
        @endif
          <form class="js-validation-signin px-30" 
                action="javascript:;" 
                onsubmit="callback({
                  route: 'backoffice/tienda/sistema/{{ $tienda->id }}/facturacionboletafactura/{{$facturacionboletafactura->id}}',
                  method: 'PUT',
                  data:{
                      view: 'reenviarcomprobante'
                  } 
              },
              function(resultado){
                 location.reload(); 
              },this)">
            <div class="row">
                <div class="col-md-6">
                  <label>Cambiar Fecha de Emisión</label>
                  <input type="date" id="fechaemision" value="{{date_format(date_create($facturacionboletafactura->venta_fechaemision), 'Y-m-d')}}"/>
                </div>
                <div class="col-md-6">
                  <label>Cambiar Hora de Emisión</label>
                  <input type="time" id="horaemision" value="{{date_format(date_create($facturacionboletafactura->venta_fechaemision), 'h:i:s')}}"/>
                </div>
            </div>
              <div class="profile-edit-container">
                      <div class="custom-form" >
                          <button type="submit" class="btn  big-btn  color-bg flat-btn" id="button-carga" style="width: 100%;">Reenviar a SUNAT</button>
                      </div>
              </div>
          </form>
    @endif
<script>
  function openDocumento(documento) {
    $('#content-a4pdf').css('display', 'none');
    $('#content-ticketpdf').css('display', 'none');
    if (documento == 'ticketpdf') {
      $('#content-ticketpdf').css('display', 'block');
    } else if (documento == 'a4pdf') {
      $('#content-a4pdf').css('display', 'block');
    }
  }
</script>
@else
<form class="js-validation-signin px-30" 
      action="javascript:;" 
      onsubmit="callback({
        route: 'backoffice/tienda/sistema/{{ $tienda->id }}/venta/{{$venta->id}}',
        method: 'PUT',
        data:{
            view: 'facturarventa'
        } 
    },
    function(resultado){
       location.reload(); 
    },this)">
  <div id="carga-comprobante">
    <div class="profile-edit-container">
        <div class="custom-form">
            <div class="row">
                <div class="col-sm-4">
                </div>
                <div class="col-sm-4">
                    <label style="text-align: center;">Código de Venta</label>
                    <input type="text" value="{{ str_pad($venta->codigo, 8, "0", STR_PAD_LEFT) }}" style="text-align: center;font-size: 16px;font-weight: bold;" disabled/> 
                </div>
            </div>
            <!-- Formulario para solicitar datos para el comprobante -->
            <div class="row">
                <div class="col-sm-6">
                    <label>Cliente</label>
                    <div class="row">
                       <div class="col-md-12">
                          <select id="idcliente" disabled>
                              <option value="{{ $venta->idcliente }}">{{ $venta->cliente }}</option>
                          </select>
                       </div>
                    </div> 
                    <label>Dirección</label>
                      <input type="text" id="direccion" value="{{$venta->clientedireccion}}" disabled/>  
                    <label>Ubigeo</label>
                      <select id="idubigeo" disabled>
                          <option value="{{ $venta->idubigeo }}">{{ $venta->ubigeonombre }}</option>
                      </select>
                </div>
                <div class="col-sm-6">
                    <label>Empresa *</label>
                    <select id="idagencia">
                        <option></option>
                         @foreach($agencias  as $value)
                            <option value="{{ $value->id }}"?>{{ $value->ruc }} - {{ $value->nombrecomercial }}</option>
                         @endforeach
                    </select>
                    <label>Moneda</label>
                    <select id="idmoneda" disabled>
                        @foreach( $monedas as $item_moneda )
                        <option value="{{ $item_moneda->id }}">{{ $item_moneda->nombre }}</option>
                        @endforeach
                    </select> 
                    <label>Comprobante *</label>
                    <select id="idtipocomprobante">
                          <option value=""></option>
                         @foreach($comprobante as $value)
                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
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
                                <th >Producto</th>      
                                <th width="60px">Cantidad</th>
                                <th width="110px">P. Unitario</th>
                                <th width="110px">P. Total</th> 
          
                              </tr>
                            </thead>
                    <tbody>
                      @foreach($ventadetalles as $value)
                                <tr style="background-color: #008cea;color: #fff;height: 40px;">
                                <td>{{$value->productocodigo}}</td>
                                <td>{{$value->productonombre}} {{ $value->detalle!=''?'('.strtoupper($value->detalle).')':'' }}</td>
                                <td>{{$value->cantidad}}</td>
                                <td>{{$value->preciounitario}}</td>
                                <td>{{$value->total}}</td>       
                                </tr>
                       @endforeach 
                  </tbody>
                            <tbody num="0"></tbody>
                  </table>
                  
            </div>
          <!-- Seccion mostrando el total, subtotal, igv -->
            <div class="row">
                <div class="col-md-4">
                </div>
                <div class="col-md-4">
                    @if(configuracion($tienda->id,'facturacion_igv')['resultado']=='CORRECTO')
                    <?php
                    $total = number_format($venta->total, 2, '.', '');
                    $igv = (configuracion($tienda->id,'facturacion_igv')['valor']/100)+1;
                    $subtotal = number_format($total/$igv, 2, '.', '');
                    $impuesto = number_format($total-$subtotal, 2, '.', '');  
                    ?>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Sub Total</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text"  id="subtotal" value="{{ $subtotal }}" placeholder="0.00" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">IGV ({{configuracion($tienda->id,'facturacion_igv')['valor']}}%)</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text"  id="igv" value="{{ $impuesto }}" placeholder="0.00" disabled>
                        </div>
                    </div>
                    @endif
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Total</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="total"    value="{{ $venta->total }}" placeholder="0.00" disabled>
                        </div>
                    </div>
                    @if($venta->total!=$venta->totalredondeado)
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Total redondeado</label>
                        <div class="col-sm-9">
                            <input class="form-control" type="text" id="total"    value="{{ $venta->totalredondeado }}" placeholder="0.00" disabled>
                        </div>
                    </div>
                    @endif
                </div>
                </div> 
          
            </div> 
        </div>
    </div>
    <div class="profile-edit-container">
            <div class="custom-form" >
                <button type="submit" class="btn  big-btn  color-bg flat-btn" id="button-carga" style="width: 100%;">Enviar a SUNAT</button>
            </div>
        </div> 
    </div>
</form>
@endif


@endsection
@section('htmls')
@if(isset($s_facturacionboletafactura))
<!--  modal enviarcorreo --> 
<div class="main-register-wrap modal-enviarcorreo">
    <div class="main-overlay"></div>
    <div class="main-register-holder">
        <div class="main-register fl-wrap">
            <div class="close-reg"><i class="fa fa-times"></i></div>
            <h3>Enviar a Correo Electrónico</h3>
            <div class="mx-modal-cuerpo" id="contenido-enviarcorreo">
              <div id="mx-carga-enviarcorreo">
              <form class="js-validation-signin px-30" 
                  action="javascript:;" 
                  onsubmit="callback({
                    route: 'backoffice/tienda/sistema/{{ $tienda->id }}/facturacionboletafactura/0',
                    method: 'PUT',
                    carga: '#mx-carga-enviarcorreo',
                    data:{
                        view: 'enviarcorreo',
                        idfacturacionboletafactura: {{$s_facturacionboletafactura->id}}
                    }
                },
                function(resultado){
                    $('#contenido-enviarcorreo').css('display','none');
                    confirm({
                        input:'#contenido-confirmar-enviarcorreo',
                        resultado:'CORRECTO',
                        mensaje:'Se ha enviado correctamente!.',
                        cerrarmodal:'.modal-enviarcorreo'
                    });       
                },this)">
                <div class="profile-edit-container">
                    <div class="mensaje-info">
                      Enviar estos documentos: 
                      <b>XML y PDF.</b>
                    </div>
                    <div class="custom-form">
                              <label>Correo Electrónico *</label>
                              <input type="text" id="enviarcorreo_email"/>
                    </div>
                </div>
                <div class="profile-edit-container">
                    <div class="custom-form">
                        <button type="submit" class="btn  big-btn  color-bg flat-btn" style="width:100%;">Enviar</button>
                    </div>
                </div> 
            </form> 
            </div>
            </div>
            <div class="mx-modal-cuerpo" id="contenido-confirmar-enviarcorreo"></div>
        </div>
    </div>
</div>
<!--  fin modal enviarcorreo --> 
@endif
@endsection
@section('subscripts')
<script>
  modal({click:'#modal-enviarcorreo'});
  $('#idmoneda').select2({
        placeholder: '---  Seleccionar ---',
        allowClear: true
  });
  function enviarcorreo(){
      $('#contenido-enviarcorreo').css('display','block'); 
      $('#contenido-confirmar-enviarcorreo').html(''); 
      $('#enviarcorreo_email').val(''); 
      removecarga({input:'#mx-carga-enviarcorreo'});
  }
   // Buscador de Clientes
   $('#idcliente').select2({
        ajax: {
            url:      "{{url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionboletafactura/showlistarusuario')}}",
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
        $.ajax({
            url:  "{{url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionboletafactura/showseleccionarusuario')}}",
            type: 'GET',
            data: {
                idusuario : e.currentTarget.value
            },
            success: function (respuesta){
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
            url:      "{{url('backoffice/tienda/sistema/'.$tienda->id.'/facturacionboletafactura/showlistarubigeo')}}",
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
      minimumResultsForSearch: -1
  })
  @if(count($agencias)>0)
    .val( {{ $venta->s_idagencia }} ).trigger('change');
  @endif
  //Tipo de moneda
  $('#idmoneda').select2({
      placeholder: '---  Seleccionar ---',
      minimumResultsForSearch: -1
  });
  //Tipo de comproban
  $('#idtipocomprobante').select2({
      placeholder: '---  Seleccionar ---',
      minimumResultsForSearch: -1
  }); 
</script>
@endsection