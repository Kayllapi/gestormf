<div class="col-sm-6">
    <div class="tabs-container" id="tab-credito">
        <ul class="tabs-menu">
            <li class="current"><a href="#tab-credito-0" onclick="openTabla('creditoactual')">Crédito Actual</a></li>
            <li><a href="#tab-credito-1" onclick="openTabla('creditorefinanciado')">Crédito Refinanciado</a></li>
        </ul>
        <div class="tab">
            <div id="tab-credito-0" class="tab-content" style="display: block;">
                  <div class="row">
                    <div class="col-sm-4">
                          <label>Total de Cuotas</label>
                          <input type="number" id="cuotas_total_cuota" value="0.00" min="0" step="0.01" disabled>
                          <label>A Cuenta</label>
                          <input type="number" id="acuenta" value="0.00" min="0" step="0.01" disabled>
                    </div>
                    <div class="col-sm-4">
                          <label>Total de Moras</label>
                          <input type="number" id="cuotas_total_mora" value="0.00" min="0" step="0.01" disabled>
                          <div style="display: block;" id="cont-moradescuento">
                              <div class="row">
                                  <div class="col-sm-6">
                                      <input type="number" id="moradescuento" value="0.00" min="0" step="0.01">
                                  </div>
                                  <div class="col-sm-6">
                                      <input type="number" id="total_moraapagar" value="0.00" min="0" step="0.01" disabled>
                                  </div>
                              </div>
                              <label>Motivo de descuento *</label>
                              <textarea id="moradescuento_detalle" style="height:85px;"></textarea>
                          </div>
                    </div>
                    <div class="col-sm-4">
                          <label>Total</label>
                          <input type="number" id="cuotas_total" value="0.00" min="0" step="0.01" disabled>
                          <label>Redondeado</label>
                          <input type="number" id="cuotas_totalredondeado" value="0.00" min="0" step="0.01" disabled>
                    </div>
                </div>
            </div>
      
            <div id="tab-credito-1" class="tab-content" style="display: none;">
              <div class="row">
                <div class="col-sm-12">
                    <table style="margin-bottom: 5px;">
                      <tr>
                        <td style="text-align: left;padding-right: 10px;">Participar con Cónyuge *</td>
                        <td>
                          <div class="onoffswitch">
                              <input type="checkbox" class="onoffswitch-checkbox check_idconyuge" id="check_idconyuge">
                              <label class="onoffswitch-label" for="check_idconyuge">
                                  <span class="onoffswitch-inner"></span>
                                  <span class="onoffswitch-switch"></span>
                              </label> 
                          </div>
                        </td>
                      </tr>
                    </table>
                    <div style="display: none;" id="cont-conyuge">
                        <select id="idconyuge">
                            <option></option>
                        </select>
                    </div>
                    <div class="list-single-main-wrapper fl-wrap">
                        <div class="breadcrumbs gradient-bg fl-wrap">
                          <span>Crédito</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <label>Monto *</label>
                            <input type="number" id="monto" min="0" step="0.01" onkeyup="creditoCalendario()" onclick="creditoCalendario()" disabled/>
                            <label>Número de Cuotas *</label>
                            <input type="number" id="numerocuota" min="1" step="1" onkeyup="creditoCalendario()" onclick="creditoCalendario()"/>
                            <label>Fecha de Inicio *</label>
                            <input type="date" id="fechainicio" onchange="creditoCalendario()"/>
                            <label>Frecuencia *</label>
                            <select id="idfrecuencia" onchange="creditoCalendario()">
                                <option></option>
                                @foreach($frecuencias as $value)
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                            <div id="cont-numerodias" style="display: none">
                                <label>Número de Días *</label>
                                <input type="number" id="numerodias" value="0" min="0" step="1" onkeyup="creditoCalendario()" onclick="creditoCalendario()"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Tasa *</label>
                            <select id="idtasa" onchange="creditoCalendario()" <?php echo $configuracion['idestadotasa']==2?'disabled':'' ?>>
                                <option></option>
                                <option value="1">Interes Fija</option>
                                <option value="2">Interes Efectiva</option>
                            </select>
                            <label>Interes % *</label>
                            <input type="number" id="tasa" min="0" step="0.01" onclick="creditoCalendario()" onkeyup="creditoCalendario()"/>
                            <label>Interes Total</label>
                            <input type="text" id="total_interes" value="0.00" disabled/>
                            @if($configuracion['idestadoseguro_degravamen']==1)
                            <label>Seguro Desgravamen</label>
                            <input type="text" id="total_segurodesgravamen" value="0.00" disabled/>
                            @endif
                            <label>Total a Pagar</label>
                            <input type="text" id="total_cuotafinal" value="0.00" disabled/>
                        </div>
                        <div class="col-md-4">
                            <label>Excluir Días:</label>
                                  
                            <table style="width: 100%;">
                              <tr>
                                <td style="text-align: right;padding: 10px;font-weight: bold;">Sábados</td>
                                <td>
                                  <div class="onoffswitch">
                                      <input type="checkbox" class="onoffswitch-checkbox excluirsabado" id="excluirsabado" onclick="creditoCalendario()">
                                      <label class="onoffswitch-label" for="excluirsabado">
                                          <span class="onoffswitch-inner"></span>
                                          <span class="onoffswitch-switch"></span>
                                      </label> 
                                  </div>
                                </td>
                              </tr>
                              <tr>
                                <td style="text-align: right;padding: 10px;font-weight: bold;">Domingos</td>
                                <td>
                                  <div class="onoffswitch">
                                      <input type="checkbox" class="onoffswitch-checkbox excluirdomingo" id="excluirdomingo" onclick="creditoCalendario()">
                                      <label class="onoffswitch-label" for="excluirdomingo">
                                          <span class="onoffswitch-inner"></span>
                                          <span class="onoffswitch-switch"></span>
                                      </label> 
                                  </div>
                                </td>
                              </tr>
                              <tr>
                                <td style="text-align: right;padding: 10px;font-weight: bold;">Feriados</td>
                                <td>
                                  <div class="onoffswitch">
                                      <input type="checkbox" class="onoffswitch-checkbox excluirferiado" id="excluirferiado" onclick="creditoCalendario()">
                                      <label class="onoffswitch-label" for="excluirferiado">
                                          <span class="onoffswitch-inner"></span>
                                          <span class="onoffswitch-switch"></span>
                                      </label> 
                                  </div>
                                </td>
                              </tr>
                            </table>
                        </div>
                    </div> 
                </div>
              </div>
            </div>
        </div>
    </div>
    <button type="button" class="btn mx-btn-post" onclick="registrar_refinanciacion()" style="margin-bottom: 5px;">Registrar Refinanciacion</button>
</div>  

<div class="col-sm-6">
    <div id="cont-cobranzapendiente" style="display: block;"></div>
    <div id="cont-load-creditocalendario" style="display: none;"></div>
</div>

<!-- Detalle  -->
<script>
    // Tabulador de pestañas
    tab({click:'#tab-credito'});
  
    mostrar_cuotapendiente({{ $ultima_cuota->numero }});
  
    let time_moradescuento;
    document.getElementById("moradescuento").addEventListener('keydown', () => {
      clearTimeout(time_moradescuento)
      time_moradescuento = setTimeout(() => {
        mostrar_cuotapendiente({{ $ultima_cuota->numero }});
        clearTimeout(time_moradescuento)
      },700)
    });
  
    function mostrar_cuotapendiente(hastacuota){
        $.ajax({
            url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamorefinanciacion/'.$s_prestamo_credito->id.'/edit') }}",
            type: 'GET',
            data: {
                view: 'cuotapendiente',
                idtipopago: 1,
                moradescuento: $('#moradescuento').val(),
                montocompleto: 0,
                hastacuota: hastacuota,
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
  
    function registrar_refinanciacion() {
        callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamorefinanciacion',
            method: 'POST',
            carga:  '#carga-refinanciacion',
            data:   {
                view: 'registrar-refinanciacion',
                idprestamo_credito: $('#idcliente').val(),
                moradescuento: $('#moradescuento').val(),
                moradescuento_maximo: $('#cuotas_total_mora').val(),
                motivo: $('#moradescuento_detalle').val(),
                check_idconyuge: $("#check_idconyuge:checked").val(),
                idconyuge: $('#idconyuge').val(),
                monto: $('#monto').val(),
                numerocuota: $('#numerocuota').val(),
                fechainicio: $('#fechainicio').val(),
                idfrecuencia: $('#idfrecuencia').val(),
                numerodias: $('#numerodias').val(),
                idtasa: $('#idtasa').val(),
                tasa: $('#tasa').val(),
                total_segurodesgravamen: $('#total_segurodesgravamen').val(),
                total_cuotafinal: $('#total_cuotafinal').val(),
                excluirsabado: $("#excluirsabado:checked").val(),
                excluirdomingo: $("#excluirdomingo:checked").val(),
                excluirferiado: $("#excluirferiado:checked").val(),
                hastacuota: {{ $ultima_cuota->numero }}
            }
        },
        function(resultado){
          location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamorefinanciacion') }}';
        })
    }
  
    function openTabla(input) {
      $('#cont-cobranzapendiente, #cont-load-creditocalendario').css({'display':'none'});
      if (input == 'creditoactual') {
        $('#cont-cobranzapendiente').css('display', 'block');
      } else if (input == 'creditorefinanciado') {
        $('#cont-load-creditocalendario').css('display', 'block');
      }
    }
</script>

<!-- Crédito -->
<script>
    $('#idfrecuencia').select2({
        placeholder: '-- Seleccionar Frecuencia --',
        minimumResultsForSearch: -1,
    });
    $('#idtasa').select2({
        placeholder: '-- Seleccionar Tasa --',
        minimumResultsForSearch: -1,
    }).val({{$configuracion['idtasapordefecto']}}).trigger('change');
    $('#idconyuge').select2({
    @include('app.select2_cliente')
    });
    $('#idgarante').select2({
    @include('app.select2_cliente')
    });

    // Mostrando avales en el credito
    $("#check_idconyuge").click(function(){
        $('#cont-conyuge').css('display','none');
        var checked = $("#check_idconyuge:checked").val();
        if(checked=='on'){
            $('#cont-conyuge').css('display','block');
            $('#idconyuge').html('<option></option>');
        }
    });
    $("#idfrecuencia").change(function(){
        var frecuencia = $('#idfrecuencia').val();
        $('#numerodias').val(0);
        $('#cont-numerodias').css('display', 'none');
        if (frecuencia == 5) {
            $('#cont-numerodias').css({'display':'block'});
        }
    });

    function creditoCalendario() {
        var monto = $('#monto').val();
        var numerocuota = $('#numerocuota').val();
        var fechainicio = $('#fechainicio').val();
        var frecuencia = $('#idfrecuencia').val();
        var numerodias = $('#numerodias').val();
        var tipotasa = $('#idtasa').val();
        var tasa = $('#tasa').val();
      
        if(monto=='' || numerocuota=='' || fechainicio=='' || frecuencia=='' || tasa==''){
            return false;
        }
        else if(frecuencia==5 && numerodias==''){
            return false;
        }
        else if(frecuencia==5 && numerodias==0){
            return false;
        }
      
        $.ajax({
            url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-creditocalendario') }}",
            type: 'GET',
            data: {
                monto: monto,
                numerocuota: numerocuota,
                fechainicio: fechainicio,
                frecuencia: frecuencia,
                numerodias: numerodias,
                tipotasa: tipotasa,
                tasa: tasa,
                excluirsabado: $('#excluirsabado:checked').val(),
                excluirdomingo: $('#excluirdomingo:checked').val(),
                excluirferiado: $('#excluirferiado:checked').val(),
            },
            beforeSend: function (data) {
                load('#cont-load-creditocalendario');
            },
            success: function (res) {
                if(res['resultado']=='CORRECTO'){
                    $('#total_interes').val(res['total_interes']);
                    $('#total_segurodesgravamen').val(res['total_segurodesgravamen']);
                    $('#total_cuotafinal').val(res['total_cuotafinal']);
                    $('#cont-load-creditocalendario').html(res['html']);
                }else{
                    $('#cont-load-creditocalendario').html(res['html']);
                }
            }
        });
    }
    // fin
</script>