@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
    <div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
          <span>Confirmar Cobranza</span>
          <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
        </div>
    </div>
    <div id="carga-cobranza">
      <div class="row">
          <div class="col-sm-12">
              <label>Crédito del Cliente</label>
              <select id="idcliente" disabled>
                  <option value="{{ $cobranza->idcliente }}">{{ $cobranza->cliente }}</option>
              </select>
          </div>
          <div class="col-sm-6">
              <div class="list-single-main-wrapper fl-wrap">
                  <div class="breadcrumbs gradient-bg fl-wrap">
                    <span>Crédito</span>
                  </div>
              </div>
              <div class="row">
                <div class="col-sm-4">
                      <label>Tipo de Pago</label>
                      <select id="idtipopago" disabled>
                          <option></option>
                          <option value="1">POR CUOTAS</option>
                          <option value="2">COMPLETO</option>
                      </select>
                      @if($cobranza->cronograma_idtipopago==1)
                      <label>Hasta Cuota</label>
                      <input type="text" id="hastacuota" value="{{ $cobranza->cronograma_hastacuota }}" disabled>
                      @elseif($cobranza->cronograma_idtipopago==2)
                      <label>Monto Recibido</label>
                      <input type="number" id="montocompleto" min="0" step="0.01" value="{{ $cobranza->cronograma_montorecibido }}" disabled>
                      </select>
                      @endif
                      <label>Total de Cuotas</label>
                      <input type="number" id="cuotas_total_cuota" value="{{ $cobranza->cronograma_totalcuota }}" disabled>
                </div>
                <div class="col-sm-4">
                      <label>A Cuenta (Anterior)</label>
                      <input type="number" id="acuenta" value="{{ $cobranza->cronograma_acuentaanterior }}" disabled>
                      <label>Total de Moras</label>
                      <input type="number" id="cuotas_total_mora" value="{{ $cobranza->cronograma_moratotal }}" value="0.00" min="0" step="0.01" disabled>
                      <label>Mora a Descontar</label>
                      <input type="number" id="moradescuento" value="{{ $cobranza->cronograma_moradescuento }}" min="0" step="0.01" disabled>
                </div>
                <div class="col-sm-4">
                      <label>Mora a Pagar</label>
                      <input type="number" id="total_moraapagar" value="{{ $cobranza->cronograma_morapagar }}" min="0" step="0.01" disabled>
                      <label>Total</label>
                      <input type="number" id="cuotas_total" value="{{ $cobranza->cronograma_total }}" disabled>
                      <label>Redondeado</label>
                      <input type="number" id="cuotas_totalredondeado" value="{{ $cobranza->cronograma_totalredondeado }}" disabled>
                </div>
            </div>
              <div class="list-single-main-wrapper fl-wrap">
                  <div class="breadcrumbs gradient-bg fl-wrap">
                    <span>Facturación</span>
                  </div>
              </div>
              <div class="row">
                <div class="col-sm-6">
                <label>Cliente *</label>
                    <div class="row">
                       <div class="col-md-12">
                          <select id="facturacion_idcliente">
                              <option value="{{ $s_prestamo_credito->idcliente }}">{{ $s_prestamo_credito->cliente }}</option>
                          </select>
                       </div>
                    </div>
                    <label>Dirección *</label>
                    <input type="text" id="facturacion_direccion" value="{{ $s_prestamo_credito->cliente_direccion }}"/>
                    <label>Ubicación (Ubigeo) *</label>
                    <select id="facturacion_idubigeo">
                        <option value="{{ $s_prestamo_credito->idubigeo }}">{{ $s_prestamo_credito->ubigeo }}</option>
                    </select>
                </div>
                <div class="col-sm-6">
                  <label>Agencia *</label>
                  <select id="facturacion_idagencia">
                    <option></option>
                    @foreach ($agencias as $value)
                    <option value="{{ $value->id }}">{{ $value->ruc }} - {{ $value->nombrecomercial }}</option>
                    @endforeach
                  </select>
                  <label>Moneda *</label>
                  <select id="facturacion_idmoneda">
                    <option></option>
                    @foreach ($monedas as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                  </select>
                  <label>Tipo de Comprobante *</label>
                  <select id="facturacion_idtipocomprobante">
                    <option></option>
                    @foreach ($tipocomprobantes as $value)
                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <button type="button" class="btn mx-btn-post" onclick="confirmar_prestamo()" style="margin-bottom: 5px;">Confirmar Cobranza</button>
          </div> 
          <div class="col-sm-6">
              <table class="table" id="table-cobranzapendiente">
                <thead style="background: #31353d; color: #fff;">
                    <tr>
                        <td style="padding: 8px;text-align: right;">Nº</td>
                        <td style="padding: 8px;text-align: right;">Vencimiento</td>
                        <td style="padding: 8px;text-align: right;">Cuota</td>
                        <td style="padding: 8px;text-align: right;">Atraso</td>
                        <td style="padding: 8px;text-align: right;">Mora</td>
                        <td style="padding: 8px;text-align: right;">Mora D.</td>
                        <td style="padding: 8px;text-align: right;">Mora P.</td>
                        <td style="padding: 8px;text-align: right;">Total</td>
                        <td style="padding: 8px;text-align: right;">A cuenta</td>
                        <td style="padding: 8px;text-align: right;">Pagar</td>
                    </tr>
                </thead>
                <tbody>
                <?php
                $total_pendiente_cuota = 0;
                $total_pendiente_atraso = 0;
                $total_pendiente_mora = 0;
                $total_pendiente_moradescontado = 0;
                $total_pendiente_moraapagar = 0;
                $total_pendiente_cuotapago = 0;
                $total_pendiente_acuenta = 0;
                $total_pendiente_cuotaapagar = 0;
                ?>
                @foreach($creditosolicituddetalle as $value)
                <tr>
                    <td style="padding: 8px;text-align: right;width: 10px;">{{$value->numero}}</td>
                    <td style="padding: 8px;text-align: right;width: 90px;">{{$value->fechavencimiento}}</td>
                    <td style="padding: 8px;text-align: right;">{{$value->cuota}}</td>
                    <td style="padding: 8px;text-align: right;">{{$value->atraso}} días</td>
                    <td style="padding: 8px;text-align: right;">{{$value->mora}}</td>
                    <td style="padding: 8px;text-align: right;background-color: #ff1f43;color: white;">{{$value->moradescuento}}</td>
                    <td style="padding: 8px;text-align: right;background-color: #0ec529;color: white;">{{$value->moraapagar}}</td>
                    <td style="padding: 8px;text-align: right;background-color: orange;color: white;">{{$value->cuotapago}}</td>
                    <td style="padding: 8px;text-align: right;">{{$value->acuenta}}</td>
                    <td style="padding: 8px;text-align: right;">{{$value->cuotaapagar}}</td>
                </tr>
                <?php
                $total_pendiente_cuota = $total_pendiente_cuota+$value->cuota;
                $total_pendiente_atraso = $total_pendiente_atraso+$value->atraso;
                $total_pendiente_mora = $total_pendiente_mora+$value->mora;
                $total_pendiente_moradescontado = $total_pendiente_moradescontado+$value->moradescuento;
                $total_pendiente_moraapagar = $total_pendiente_moraapagar+$value->moraapagar;
                $total_pendiente_cuotapago = $total_pendiente_cuotapago+$value->cuotapago;
                $total_pendiente_acuenta = $total_pendiente_acuenta+$value->acuenta;
                $total_pendiente_cuotaapagar = $total_pendiente_cuotaapagar+$value->cuotaapagar;
                ?>
                @endforeach
                </tbody>
                   <tfoot style="background: #31353d; color: #fff;">
                      <tr>
                          <td style="padding: 8px;text-align: right;" colspan="2">TOTAL</td>
                          <td style="padding: 8px;text-align: right;">{{number_format($total_pendiente_cuota, 2, '.', '')}}</td>
                          <td style="padding: 8px;text-align: right;">{{number_format($total_pendiente_atraso, 2, '.', '')}} días</td>
                          <td style="padding: 8px;text-align: right;">{{number_format($total_pendiente_mora, 2, '.', '')}}</td>
                          <td style="padding: 8px;text-align: right;">{{number_format($total_pendiente_moradescontado, 2, '.', '')}}</td>
                          <td style="padding: 8px;text-align: right;">{{number_format($total_pendiente_moraapagar, 2, '.', '')}}</td>
                          <td style="padding: 8px;text-align: right;">{{number_format($total_pendiente_cuotapago, 2, '.', '')}}</td>
                          <td style="padding: 8px;text-align: right;">{{number_format($total_pendiente_acuenta, 2, '.', '')}}</td>
                          <td style="padding: 8px;text-align: right;">{{number_format($total_pendiente_cuotaapagar, 2, '.', '')}}</td>
                      </tr>
                   </tfoot>
                </table>
          </div>
      </div>
    </div>     
@endsection
@section('htmls')
<!--  modal cobranzarealizada --> 
<div class="main-register-wrap modal-cobranzarealizada" id="modal-cobranzarealizada">
    <div class="main-overlay"></div>
    <div class="main-register-holder" style="margin: 10px auto 50px;">
        <div class="main-register fl-wrap">
            <div id="contenido-cobranzarealizada"></div>
        </div>
    </div>
</div>
<!--  fin modal cobranzarealizada --> 
@endsection
@section('subscripts')
<script>
 
    $('#idcliente').select2({
      placeholder: '-- Seleccionar --'
    });
  
    $('#facturacion_idcliente').select2({
      @include('app.select2_cliente')
    });
  
    $('#facturacion_idubigeo').select2({
      @include('app.select2_ubigeo')
    });

    @if(configuracion($tienda->id,'facturacion_empresapordefecto')['resultado']=='CORRECTO')
        $("#facturacion_idagencia").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val({{ configuracion($tienda->id,'facturacion_empresapordefecto')['valor'] }}).trigger("change");    
    @else
        $("#facturacion_idagencia").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @endif

    @if(configuracion($tienda->id,'facturacion_monedapordefecto')['resultado']=='CORRECTO')
        $("#facturacion_idmoneda").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val({{ configuracion($tienda->id,'facturacion_monedapordefecto')['valor'] }}).trigger("change");
    @else
        $("#facturacion_idmoneda").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @endif

    @if(configuracion($tienda->id,'facturacion_comprobantepordefecto')['resultado']=='CORRECTO')
        $("#facturacion_idtipocomprobante").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val({{ configuracion($tienda->id,'facturacion_comprobantepordefecto')['valor'] }}).trigger("change");   
    @else
        $("#facturacion_idtipocomprobante").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @endif
  
    $('#idtipopago').select2({
        placeholder: '-- Seleccionar Tipo Pago --',
        minimumResultsForSearch: -1
    }).val({{$cobranza->cronograma_idtipopago}}).trigger("change");
  
    function confirmar_prestamo() {
        callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamocobranza/{{$cobranza->id}}',
            method: 'PUT',
            carga: '#carga-cobranza',
            data:   {
                view: 'confirmar',
                idprestamo_credito: $('#idcliente').val(),
                facturacion_idcliente: $('#facturacion_idcliente').val(),
                facturacion_direccion: $('#facturacion_direccion').val(),
                facturacion_idubigeo: $('#facturacion_idubigeo').val(),
                facturacion_idagencia: $('#facturacion_idagencia').val(),
                facturacion_idmoneda: $('#facturacion_idmoneda').val(),
                facturacion_idtipocomprobante: $('#facturacion_idtipocomprobante').val(),
            }
        },
        function(resultado){
          $('#modal-cobranzarealizada').css('display','block');
          var imprimir = '';
          imprimir = '<div id="iframeventa"><iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza') }}/'+resultado['idprestamocobranza']+'/edit?view=ticketpdf&idcobranza='+resultado['idprestamocobranza']+'#zoom=130" frameborder="0" width="100%" height="600px"></iframe></div>';
          $('#contenido-cobranzarealizada').html('<div class="cont-confirm" style="margin-top: 15px;">'+
                             '<div class="confirm"><i class="fa fa-check"></i></div>'+
                             '<div class="confirm-texto">¡Correcto!</div>'+
                             '<div class="confirm-subtexto">Se ha registrado correctamente.</div></div>'+
                             '<div class="custom-form" style="text-align: center;margin-bottom: 5px;">'+
                             '<button type="button" class="btn big-btn color-bg flat-btn mx-realizar-pago" style="margin: auto;float: none;" onclick="realizar_nueva_cobranza()">'+
                             '<i class="fa fa-check"></i> Realizar Nueva Cobranza</button></div>'+
                             '<div class="custom-form" style="text-align: center;margin-bottom: 5px;">'+
                             '<button type="button" class="btn big-btn color-bg flat-btn" style="margin: auto;float: none;" onclick="iracobranzas()">'+
                             '<i class="fa fa-check"></i> Ir a Cobranzas</button></div>'+
                             imprimir);
          removecarga({input:'#carga-cobranza'});
        })
    }
    function realizar_nueva_cobranza() {
      location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza/create') }}';
    }
    function iracobranzas() {
      location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza') }}';
    }
    function calcular_vuelto_cuota(){
        var montorecibido = parseFloat($('#montorecibido').val());
        var cuotas_totalredondeado = parseFloat($('#cuotas_totalredondeado').val());
        var vuelto = (montorecibido-cuotas_totalredondeado).toFixed(2);
        $('#vuelto').val(vuelto);
    }
</script>
@endsection