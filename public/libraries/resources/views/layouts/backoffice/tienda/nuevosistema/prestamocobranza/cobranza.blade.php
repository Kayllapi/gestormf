<div class="col-sm-6">
    <div class="tabs-container" id="tab-credito">
        <ul class="tabs-menu">
            <li class="current"><a href="#tab-credito-0">Detalle</a></li>
            <li><a href="#tab-credito-1">Facturación</a></li>
        </ul>
        <div class="tab">
            <div id="tab-credito-0" class="tab-content" style="display: block;">
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
                            <?php echo $cronograma['html_cuotasrestantes'] ?>
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
                          <label>Descontar Mora *</label>
                          <div class="onoffswitch" style="margin-bottom: 10px;margin-top: 2px;float: left;">
                              <input type="checkbox" class="onoffswitch-checkbox check_moradescuento" id="check_moradescuento">
                              <label class="onoffswitch-label" for="check_moradescuento">
                                  <span class="onoffswitch-inner"></span>
                                  <span class="onoffswitch-switch"></span>
                              </label> 
                          </div>
                          <div style="display: block;" id="cont-moradescuento">
                              <div class="row">
                                  <div class="col-sm-12">
                                      <label>Mora a Descontar *</label>
                                      <input type="number" id="moradescuento" placeholder="0.00" min="0" step="0.01" disabled>
                                  </div>
                                  <div class="col-sm-12">
                                      <label>Mora a Pagar</label>
                                      <input type="number" id="total_moraapagar" value="0.00" min="0" step="0.01" disabled>
                                  </div>
                              </div>
                          </div>
                    </div>
                    <div class="col-sm-4">
                          <label>Total</label>
                          <input type="number" id="cuotas_total" value="0.00" min="0" step="0.01" disabled>
                          <label>Redondeado</label>
                          <input type="number" id="cuotas_totalredondeado" value="0.00" min="0" step="0.01" disabled>
                          <div id="cont-montorecibido" style="display:none;">
                              <label>Monto recibido *</label>
                              <input type="number" id="montorecibido" value="0.00" min="0" step="0.01" onkeyup="calcular_vuelto_cuota()" disabled>
                          </div>
                          <label>Vuelto</label>
                          <input type="number" id="vuelto" value="0.00" min="0" step="0.01" disabled>
                    </div>
                </div>
            </div>
            <div id="tab-credito-1" class="tab-content" style="display: none;">
               
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
            </div>
        </div>
    </div>
    <button type="button" class="btn mx-btn-post" onclick="registrar_prestamo()" style="margin-bottom: 5px;">Registrar Cobranza</button>
</div>  

<div class="col-sm-6">
    <div class="tabs-container" id="tab-tablecobranza">
        <ul class="tabs-menu">
            <li class="current"><a href="#tab-tablecobranza-0">Pendientes</a></li>
            <li><a href="#tab-tablecobranza-1">Cancelados</a></li>
            <li><a href="#tab-tablecobranza-2">Pagados</a></li>
            <li><a href="#tab-tablecobranza-3">Resumen</a></li>
        </ul>
        <div class="tab">
            <div id="tab-tablecobranza-0" class="tab-content" style="display: block;">
                <div id="cont-cobranzapendiente"></div>
            </div>
            <div id="tab-tablecobranza-1" class="tab-content" style="display: none;">
                <div id="cont-cobranzacancelada"></div>
            </div>
            <div id="tab-tablecobranza-2" class="tab-content" style="display: none;">
                <div id="cont-pagorealizado"></div>
            </div>
            <div id="tab-tablecobranza-3" class="tab-content" style="display: none;">
                <div class="tabs-container" id="tab-resumencredito">
                      <ul class="tabs-menu">
                          <li class="current"><a href="#tab-resumencredito-0" id="tab-resumen">Desembolso</a></li>
                          <li><a href="#tab-resumencredito-1" id="tab-actual">Deuda Vencida</a></li>
                          <li><a href="#tab-resumencredito-2" id="tab-restante">Deuda Pendiente</a></li>
                          <li><a href="#tab-resumencredito-3" id="tab-pagado">Deuda Cancelada</a></li>
                      </ul>
                      <div class="tab">
                          <div id="tab-resumencredito-0" class="tab-content" style="display: block;">
                              <div class="row">
                                  <div class="col-md-6">
                                      <label for="resumen-fechadesembolso">Frecuencia: </label>
                                      <input type="text" id="resumen-desembolso-frecuencia" value="---" disabled>
                                      <label for="resumen-fechadesembolso">Fecha de Desembolso: </label>
                                      <input type="text" id="resumen-desembolso-fecha" value="---" disabled>
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
        </div>
    </div>
</div>
<script>
    // Tabulador de pestañas
    tab({click:'#tab-credito'});
    tab({click:'#tab-resumencredito'});
    tab({click:'#tab-tablecobranza'});

    $('#facturacion_idcliente').select2({
      @include('app.select2_cliente')
    });
  
    $('#facturacion_idubigeo').select2({
      @include('app.select2_ubigeo')
    });
  

    @if($configuracion_facturacion['idempresapordefecto']!=null)
        $("#facturacion_idagencia").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val({{ $configuracion_facturacion['idempresapordefecto'] }}).trigger("change");    
    @else
        $("#facturacion_idagencia").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @endif

    @if($configuracion_facturacion['idcomprobantepordefecto']!=null)
        $("#facturacion_idtipocomprobante").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val({{ $configuracion_facturacion['idcomprobantepordefecto'] }}).trigger("change");   
    @else
        $("#facturacion_idtipocomprobante").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @endif

    @if($configuracion_facturacion['idmonedapordefecto']!=null)
        $("#facturacion_idmoneda").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val({{ $configuracion_facturacion['idmonedapordefecto'] }}).trigger("change");
    @else
        $("#facturacion_idmoneda").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @endif
  
    $('#idtipopago').select2({
        placeholder: '-- Seleccionar Tipo Pago --',
        minimumResultsForSearch: -1
    }).on("change", function(e) {
        $('#cont-tipopago-porcuotas').css('display','none');
        $('#cont-tipopago-completo').css('display','none');
        $('#cont-montorecibido').css('display','none');
        $('#moradescuento').val('0.00');
        $('#moradescuento_detalle').val('');
        $('#vuelto').val('0.00');
        if(e.currentTarget.value == 1) {
            $('#cont-tipopago-porcuotas').css('display','block');
            $('#cont-montorecibido').css('display','block');
            mostrar_creditocliente();
        }
        else if(e.currentTarget.value == 2) {
            $('#cont-tipopago-completo').css('display','block');
            mostrar_creditocliente();
        } 
        $('#montocompleto').val('');
  
    }).val(1).trigger("change");
  
    $('#hastacuota').select2({
        placeholder: '-- Seleccionar Cuota --',
        minimumResultsForSearch: -1
    }).on("change", function(e) {
        mostrar_creditocliente();
    });

    $("#check_moradescuento").click(function(){
        $('#moradescuento').prop('disabled', true);
        $('#moradescuento_detalle').prop('disabled', true);
        $('#moradescuento').val('0.00');
        $('#moradescuento_detalle').val('');
        var checked = $("#check_moradescuento:checked").val();
        if(checked=='on'){
            $('#moradescuento').prop('disabled', false);
            $('#moradescuento_detalle').prop('disabled', false);
        }else{
            mostrar_creditocliente();
        }
    });

    let time_moradescuento;
    document.getElementById("moradescuento").addEventListener('keydown', () => {
      clearTimeout(time_moradescuento)
      time_moradescuento = setTimeout(() => {
        mostrar_creditocliente();
        clearTimeout(time_moradescuento)
      },700)
    });

    let time_montocompleto;
    document.getElementById("montocompleto").addEventListener('keydown', () => {
      clearTimeout(time_montocompleto)
      time_montocompleto = setTimeout(() => {
        mostrar_creditocliente();
        clearTimeout(time_montocompleto)
      },700)
    });
  
    function mostrar_creditocliente(){
        $('#idtipopago').prop('disabled', true);
        $('#hastacuota').prop('disabled', true);
        $('#montocompleto').prop('disabled', true);
        $('#montorecibido').prop('disabled', true);
        $('#moradescuento').prop('disabled', true);
        $('#moradescuento_detalle').prop('disabled', true);
        mostrar_cuotapendiente();
        mostrar_cuotacancelada();
        mostrar_pagorealizado();
    }
  
    function mostrar_cuotapendiente(){
        $.ajax({
            url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza/'.$s_prestamo_credito->id.'/edit') }}",
            type: 'GET',
            data: {
                view: 'cuotapendiente',
                idtipopago: $('#idtipopago').val(),
                moradescuento: $('#moradescuento').val(),
                montocompleto: $('#montocompleto').val(),
                hastacuota: $('#hastacuota').val(),
                checked_moradescuento: $("#check_moradescuento:checked").val()
            },
            beforeSend: function (data) {
                load('#cont-cobranzapendiente');
            },
            success: function (res) {
                $('#cont-cobranzapendiente').html(res);
            }
        });
    }
    function mostrar_cuotacancelada(){
        $.ajax({
            url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza/'.$s_prestamo_credito->id.'/edit') }}",
            type: 'GET',
            data: {
                view: 'cuotacancelada',
                idtipopago: $('#idtipopago').val(),
                moradescuento: $('#moradescuento').val(),
                montocompleto: $('#montocompleto').val(),
                hastacuota: $('#hastacuota').val()
            },
            beforeSend: function (data) {
                load('#cont-cobranzacancelada');
            },
            success: function (res) {
                $('#cont-cobranzacancelada').html(res);
            }
        });
    }
    function mostrar_pagorealizado(){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamocobranza/{{ $s_prestamo_credito->id }}/edit?view=pagorealizado',result:'#cont-pagorealizado'});
    }
    function ticket_pagorealizado(idcobranza){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamocobranza/{{ $s_prestamo_credito->id }}/edit?view=pagorealizadoticket&idcobranza='+idcobranza,result:'#cont-pagorealizado'});
    }
    function detalle_pagorealizado(idcobranza){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamocobranza/{{ $s_prestamo_credito->id }}/edit?view=pagorealizadodetalle&idcobranza='+idcobranza,result:'#cont-pagorealizado'});
    }
    function anular_pagorealizado(idcobranza){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamocobranza/{{ $s_prestamo_credito->id }}/edit?view=pagorealizadoanular&idcobranza='+idcobranza,result:'#cont-pagorealizado'});
    }
  
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
                vuelto: $('#vuelto').val(),
              
                facturacion_idcliente: $('#facturacion_idcliente').val(),
                facturacion_direccion: $('#facturacion_direccion').val(),
                facturacion_idubigeo: $('#facturacion_idubigeo').val(),
                facturacion_idagencia: $('#facturacion_idagencia').val(),
                facturacion_idmoneda: $('#facturacion_idmoneda').val(),
                facturacion_idtipocomprobante: $('#facturacion_idtipocomprobante').val()
            }
        },
        function(resultado){
            pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamocobranza/'+$('#idcliente').val()+'/edit?view=cobranza',result:'#cont-clientecredito'});
            removecarga({input:'#carga-cobranza'});
        })
    }
    
    function calcular_vuelto_cuota(){
        var montorecibido = parseFloat($('#montorecibido').val());
        var cuotas_totalredondeado = parseFloat($('#cuotas_totalredondeado').val());
        var vuelto = (montorecibido-cuotas_totalredondeado).toFixed(2);
        $('#vuelto').val(vuelto);
    }
</script>