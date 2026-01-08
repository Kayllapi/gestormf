@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
    <div class="list-single-main-wrapper fl-wrap">
        <div class="breadcrumbs gradient-bg fl-wrap">
          <span>Registrar Cobranza</span>
          <a class="btn btn-success" href="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza') }}"><i class="fa fa-angle-left"></i> Ir Atras</a></a>
        </div>
    </div>
    <div id="carga-cobranza">
                <div class="row">
                    <div class="col-sm-12">
                        <label>Cliente</label>
                        <select id="idcliente">
                            <option></option>
                        </select>
                    </div>
                    <div id="cont-load-clientecredito"></div>
                    <div id="cont-clientecredito" style="display: none;">
                        <div class="col-sm-6">
                          <div class="list-single-main-wrapper fl-wrap">
                              <div class="breadcrumbs gradient-bg fl-wrap">
                                <span>Detalle</span>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-sm-4">
                                    <label>Tipo de Pago *</label>
                                    <select id="idtipopago">
                                        <option></option>
                                        <option value="1">POR CUOTAS</option>
                                        <option value="2">COMPLETO</option>
                                    </select>
                                    <div id="cont-tipopago-porcuotas" style="display:none;">
                                    <label>Hasta Cuota *</label>
                                    <select id="hastacuota" disabled>
                                    </select>
                                    </div>
                                    <div id="cont-tipopago-completo" style="display:none;">
                                    <label>Monto Recibido *</label>
                                    <input type="number" id="montocompleto" min="0" step="0.01" onkeyup="calcular_vuelto_completo()" disabled>
                                    </select>
                                    </div>
                                    <label>Total de Cuotas</label>
                                    <input type="number" id="cuotas_total_cuota" value="0.00" min="0" step="0.01" disabled>
                                    <label>A Cuenta</label>
                                    <input type="number" id="acuenta" value="0.00" min="0" step="0.01" disabled>
                              </div>
                              <div class="col-sm-4">
                                    <label>Total de Moras</label>
                                    <input type="number" id="cuotas_total_mora" value="0.00" min="0" step="0.01" disabled>
                                    <table style="margin-bottom: 5px;">
                                      <tr>
                                        <td style="text-align: left;padding-right: 10px;">Descontar Mora *</td>
                                        <td>
                                          <div class="onoffswitch">
                                              <input type="checkbox" class="onoffswitch-checkbox check_moradescuento" id="check_moradescuento">
                                              <label class="onoffswitch-label" for="check_moradescuento">
                                                  <span class="onoffswitch-inner"></span>
                                                  <span class="onoffswitch-switch"></span>
                                              </label> 
                                          </div>
                                        </td>
                                      </tr>
                                    </table>
                                    <div style="display: block;" id="cont-moradescuento">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <input type="number" id="moradescuento" min="0" step="0.01" disabled>
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="number" id="total_moraapagar" value="0.00" min="0" step="0.01" disabled>
                                            </div>
                                        </div>
                                        <label>Motivo de descuento *</label>
                                        <textarea id="moradescuento_detalle" style="height:85px;" disabled></textarea>
                                    </div>
                              </div>
                              <div class="col-sm-4">
                                    <label>Total</label>
                                    <input type="number" id="cuotas_total" value="0.00" min="0" step="0.01" disabled>
                                    <label>Redondeado</label>
                                    <input type="number" id="cuotas_totalredondeado" value="0.00" min="0" step="0.01" disabled>
                                    <div id="cont-montorecibido" style="display:none;">
                                        <label>Monto recibido *</label>
                                        <input type="number" id="montorecibido" min="0" step="0.01" onkeyup="calcular_vuelto_cuota()" disabled>
                                    </div>
                                    <label>Vuelto</label>
                                    <input type="number" id="vuelto" value="0.00" min="0" step="0.01" disabled>
                              </div>
                          </div>
                            <button type="button" class="btn mx-btn-post" onclick="registrar_prestamo()" style="margin-bottom: 5px;">Registrar Cobranza</button>
                            <div class="list-single-main-wrapper fl-wrap">
                                <div class="breadcrumbs gradient-bg fl-wrap">
                                  <span>Resumen de Crédito</span>
                                </div>
                            </div>
                            <div class="tabs-container" id="tab-resumencredito">
                                <ul class="tabs-menu">
                                    <li class="current"><a href="#tab-resumencredito-0" id="tab-resumen">Desembolso</a></li>
                                    <li><a href="#tab-resumencredito-1" id="tab-actual">Deuda Vencida</a></li>
                                    <li><a href="#tab-resumencredito-2" id="tab-restante">Deuda Restante</a></li>
                                    <li><a href="#tab-resumencredito-3" id="tab-pagado">Deuda Cancelada</a></li>
                                </ul>
                                <div class="tab">
                                    <div id="tab-resumencredito-0" class="tab-content" style="display: block;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="resumen-fechadesembolso">Fecha de Desembolso: </label>
                                                <input type="text" id="resumen-desembolso-fecha" disabled>
                                                <label for="resumen-monto">Monto Desembolsado: </label>
                                                <input type="text" id="resumen-desembolso-monto" value="0.00" disabled>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="resumen-interes">Interes: </label>
                                                <input type="text" id="resumen-desembolso-interes" value="0.00" disabled>
                                                <label for="resumen-montototal">Monto a Pagar: </label>
                                                <input type="text" id="resumen-desembolso-montototal" value="0.00" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="tab-resumencredito-1" class="tab-content" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="resumen-deudaactual">Deuda Vencida: </label>
                                                <input type="text" id="resumen-vencida-deudaactual" value="0.00" disabled>
                                                <label for="resumen-mora">Moras: </label>
                                                <input type="text" id="resumen-vencida-moraactual" value="0.00" disabled>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="resumen-total">Total: </label>
                                                <input type="text" id="resumen-vencida-totalactual" value="0.00" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="tab-resumencredito-2" class="tab-content" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="resumen-deudarestante">Deuda Restante: </label>
                                                <input type="text" id="resumen-pendiente-deudarestante" value="0.00" disabled>
                                                <label for="resumen-mora">Moras: </label>
                                                <input type="text" id="resumen-pendiente-morarestante" value="0.00" disabled>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="resumen-total">Total: </label>
                                                <input type="text" id="resumen-pendiente-totalrestante" value="0.00" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="tab-resumencredito-3" class="tab-content" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="resumen-deudapagada">Deuda Cancelada: </label>
                                                <input type="text" id="resumen-cancelada-deudapagada" value="0.00" disabled>
                                                <label for="resumen-mora">Moras: </label>
                                                <input type="text" id="resumen-cancelada-morapagada" value="0.00" disabled>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="resumen-total">Total: </label>
                                                <input type="text" id="resumen-cancelada-totalpagada" value="0.00" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="tabs-container" id="tab-tablecobranza">
                                <ul class="tabs-menu">
                                    <li class="current"><a href="#tab-tablecobranza-0" id="tab-cobranza-pendiente">Cuotas Pendientes</a></li>
                                    <li><a href="#tab-tablecobranza-1" id="tab-cobranza-cancelados">Cuotas Cancelados</a></li>
                                    <li><a href="#tab-tablecobranza-2" id="tab-cobranza-pagos">Pagos Realizados</a></li>
                                </ul>
                                <div class="tab">
                                    <div id="tab-tablecobranza-0" class="tab-content" style="display: block;">
                                        <div id='cont-cobranzapendiente'>
                                        </div>
                                    </div>
                                    <div id="tab-tablecobranza-1" class="tab-content" style="display: none;">
                                        <div id='cont-cobranzacancelada'>
                                        </div>
                                    </div>
                                    <div id="tab-tablecobranza-2" class="tab-content" style="display: none;">
                                        <div class="row">
                                          <div id="cont-load-pagorealizado"></div>
                                          <div id='cont-pagorealizado' style="display: block">
                                          </div>
                                          <div id="cont-detalle_pagorealizado" style="display: none;">
                                          </div>
                                          <div id="cont-ticket_pagorealizado" style="display: none;">
                                          </div>
                                          <div id="cont-anular_pagorealizado" style="display: none;">
                                          </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    </div>      
    <style>
    .mx-tableselect {
      background-image: url({{url('public/backoffice/sistema/text3.png')}}) !important;
    }
    </style>
@endsection
@section('subscripts')
<script>
    // Tabulador de pestañas
    tab({click:'#tab-resumencredito'});
    tab({click:'#tab-tablecobranza'});

    $('#idcliente').select2({
        ajax: {
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza/show-creditocliente')}}",
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
        placeholder: "-- Seleccionar Cliente --",
        minimumInputLength: 2
    }).on("change", function(e) {
        $('#cont-clientecredito').css({'display':'none'});
        actualizar_pago_credito('#cont-load-clientecredito','on');
    });
    $('#idtipopago').select2({
        placeholder: '-- Seleccionar Tipo Pago --',
        minimumResultsForSearch: -1
    }).on("change", function(e) {
        $('#cont-tipopago-porcuotas').css('display','none');
        $('#cont-tipopago-completo').css('display','none');
        $('#cont-montorecibido').css('display','none');
        if(e.currentTarget.value == 1) {
            $('#cont-tipopago-porcuotas').css('display','block');
            $('#cont-montorecibido').css('display','block');
        }
        else if(e.currentTarget.value == 2) {
            $('#cont-tipopago-completo').css('display','block');
        } 
        $('#montocompleto').val('');
        $('#hastacuota').select2({
            placeholder: '-- Seleccionar Cuota --',
            minimumResultsForSearch: -1
        }).val(null).trigger("change");
        actualizar_pago_credito('#cont-cobranzapendiente');
    });
    $('#hastacuota').select2({
        placeholder: '-- Seleccionar Cuota --',
        minimumResultsForSearch: -1
    }).on("change", function(e) {
        actualizar_pago_credito('#cont-cobranzapendiente');
    });

    $("#check_moradescuento").click(function(){
        //$('#cont-moradescuento').css('display','none');
        $('#moradescuento').prop('disabled', true);
        $('#moradescuento_detalle').prop('disabled', true);
        $('#moradescuento').val('');
        $('#moradescuento_detalle').val('');
        var checked = $("#check_moradescuento:checked").val();
        if(checked=='on'){
            $('#moradescuento').prop('disabled', false);
            $('#moradescuento_detalle').prop('disabled', false);
            //$('#cont-moradescuento').css('display','block');
        }else{
            actualizar_pago_credito('#cont-cobranzapendiente');
        }
    });

    let time_moradescuento;
    document.getElementById("moradescuento").addEventListener('keydown', () => {
      clearTimeout(time_moradescuento)
      time_moradescuento = setTimeout(() => {
        actualizar_pago_credito('#cont-cobranzapendiente');
        clearTimeout(time_moradescuento)
      },700)
    });

    let time_montocompleto;
    document.getElementById("montocompleto").addEventListener('keydown', () => {
      clearTimeout(time_montocompleto)
      time_montocompleto = setTimeout(() => {
        actualizar_pago_credito('#cont-cobranzapendiente');
        clearTimeout(time_montocompleto)
      },700)
    });
  
    function registrar_prestamo() {
        callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamocobranza',
            method: 'POST',
            carga: '#carga-cobranza',
            data:   {
                view: 'registrar',
                idprestamo_credito: $('#idcliente').val(),
                idtipopago: $('#idtipopago').val(),
                check_moradescuento: $("#check_moradescuento:checked").val(),
                moradescuento: $('#moradescuento').val(),
                moradescuento_detalle: $('#moradescuento_detalle').val(),
                montocompleto: $('#montocompleto').val(),
                hastacuota: $('#hastacuota').val(),
                montorecibido: $('#montorecibido').val(),
                vuelto: $('#vuelto').val()
            }
        },
        function(resultado){
            location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza/create') }}';
        })
    }
  
    function actualizar_pago_credito(carga,hastacuota='') {
        
        $('#hastacuota').prop('disabled', true);
        $('#montocompleto').prop('disabled', true);
        $('#montorecibido').prop('disabled', true);
      
        $.ajax({
            url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza/show-creditosolicitud') }}",
            type: 'GET',
            data: {
                idprestamo_credito: $('#idcliente').val(),
                idtipopago: $('#idtipopago').val(),
                moradescuento: $('#moradescuento').val(),
                montocompleto: $('#montocompleto').val(),
                hastacuota: hastacuota=='on'?'':$('#hastacuota').val()
            },
            beforeSend: function (data) {
                load(carga);
            },
            success: function (res) {
                $(carga).html('');
                $('#cont-pagorealizado').css('display', 'block');
                $('#cont-detalle_pagorealizado, #cont-ticket_pagorealizado, #cont-anular_pagorealizado').css('display', 'none');
                $('#cont-detalle_pagorealizado, #cont-ticket_pagorealizado, #cont-anular_pagorealizado, #cont-load-pagorealizado').html('');

                $('#cont-clientecredito').css({'display':'block'});
                $('#hastacuota').prop('disabled', false);
                $('#montocompleto').prop('disabled', false);
                $('#montorecibido').prop('disabled', false);

                if(hastacuota=='on'){
                    $('#hastacuota').html(res['hastacuota']);
                }
                $('#cuotas_total_cuota').val(res['select_cuota']);
                $('#cuotas_total_mora').val(res['select_mora']);
                $('#cuotas_total').val(res['select_cuotaapagar']);
                $('#cuotas_totalredondeado').val(res['select_cuotaapagarredondeado']);
                $('#total_moraapagar').val(res['select_moraapagar']);
                $('#acuenta').val(res['select_acuenta']);
              
                $('#cont-cobranzapendiente').html(res['cobranzapendiente']);
                $('#cont-cobranzacancelada').html(res['cobranzacancelada']);
                pagorealizado_index();
                /*$('#cont-pagorealizado').html(res['pagorealizado']);
              
                $("div#menu-opcion").on("click", function () {
                    $("ul",this).toggleClass("hu-menu-vis");
                    $("i",this).toggleClass("fa-angle-up");
                });*/
              
                // vuelto completo
                var montocompleto = parseFloat($('#montocompleto').val());
                var cuotas_totalredondeado = parseFloat($('#cuotas_totalredondeado').val());
                var vuelto = (montocompleto-cuotas_totalredondeado).toFixed(2);
                if(vuelto<0){
                    vuelto = '0.00';
                }
                $('#vuelto').val(vuelto);
                
          
                // Resumen Credito
                $('#resumen-desembolso-fecha').val(res['credito-fechadesembolso']);
                $('#resumen-desembolso-monto').val(res['credito-monto']);
                $('#resumen-desembolso-interes').val(res['credito-interes']);
                $('#resumen-desembolso-montototal').val(res['credito-montototal']);

                $('#resumen-vencida-deudaactual').val(res['credito-deudaactual']);
                $('#resumen-vencida-moraactual').val(res['credito-moraactual']);
                $('#resumen-vencida-totalactual').val(res['credito-totalactual']);
              
                $('#resumen-pendiente-deudarestante').val(res['credito-deudarestante']);
                $('#resumen-pendiente-morarestante').val(res['credito-morarestante']);
                $('#resumen-pendiente-totalrestante').val(res['credito-totalrestante']);
              
                $('#resumen-cancelada-deudapagada').val(res['credito-deudapagada']);
                $('#resumen-cancelada-morapagada').val(res['credito-morapagada']);
                $('#resumen-cancelada-totalpagada').val(res['credito-totalpagada']);
            }
        });
    }
    
    function calcular_vuelto_cuota(){
        var montorecibido = parseFloat($('#montorecibido').val());
        var cuotas_totalredondeado = parseFloat($('#cuotas_totalredondeado').val());
        var vuelto = (montorecibido-cuotas_totalredondeado).toFixed(2);
        $('#vuelto').val(vuelto);
    }
</script>
<script>
    
    function pagorealizado_index(){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamocobranza/{{ $prestamocredito->id }}/edit?view=pagorealizado',result:'#cont-pagorealizados'});
    }
</script>
<script>
  function index_pagorealizado() {
    $('#cont-pagorealizado').css('display', 'block');
    $('#cont-detalle_pagorealizado, #cont-ticket_pagorealizado, #cont-anular_pagorealizado').css('display', 'none');
    $('#cont-detalle_pagorealizado, #cont-ticket_pagorealizado, #cont-anular_pagorealizado, #cont-load-pagorealizado').html('');
  }
  function detalle_pagorealizado(idcobranza) {
    $('#cont-detalle_pagorealizado, #cont-ticket_pagorealizado, #cont-anular_pagorealizado').html('');
    $('#cont-pagorealizado, #cont-ticket_pagorealizado, #cont-anular_pagorealizado').css('display', 'none');
    $('#cont-detalle_pagorealizado').css('display', 'block');
    $.ajax({
      url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza/show-detalle_pagorealizado') }}",
      type: 'GET',
      data: {
          idcobranza: idcobranza,
      },
      beforeSend: function (data) {
          load('#cont-load-pagorealizado');
      },
      success: function (res) {
        $('#cont-load-pagorealizado').html('');
        $('#cont-detalle_pagorealizado').html(res);
      }
    });
  }
  function ticket_pagorealizado(idcobranza) {
    $('#cont-detalle_pagorealizado, #cont-ticket_pagorealizado, #cont-anular_pagorealizado').html('');
    $('#cont-pagorealizado, #cont-detalle_pagorealizado, #cont-anular_pagorealizado').css('display', 'none');
    $('#cont-ticket_pagorealizado').css('display', 'block');
    $.ajax({
      url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza/show-ticket_pagorealizado') }}",
      type: 'GET',
      data: {
          idcobranza: idcobranza
      },
      beforeSend: function (data) {
          load('#cont-load-pagorealizado');
      },
      success: function (res) {
        $('#cont-load-pagorealizado').html('');
        $('#cont-ticket_pagorealizado').html(res);
      }
    });
  }
  function anular_pagorealizado(idcobranza) {
    $('#cont-detalle_pagorealizado, #cont-ticket_pagorealizado, #cont-anular_pagorealizado').html('');
    $('#cont-pagorealizado, #cont-detalle_pagorealizado, #cont-ticket_pagorealizado').css('display', 'none');
    $('#cont-anular_pagorealizado').css('display', 'block');
    $.ajax({
      url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza/show-anular_pagorealizado') }}",
      type: 'GET',
      data: {
          idcobranza: idcobranza
      },
      beforeSend: function (data) {
          load('#cont-load-pagorealizado');
      },
      success: function (res) {
        $('#cont-load-pagorealizado').html('');
        $('#cont-anular_pagorealizado').html(res);
      }
    });
  }
</script>
@endsection