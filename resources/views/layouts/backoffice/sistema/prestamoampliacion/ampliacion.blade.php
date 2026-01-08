<div class="col-sm-6">
    <div class="row">
        <div class="col-sm-4">
              <label>Total Cuotas</label>
              <input type="text" value="{{$cronograma['select_cuota']}}" id="cuotas_total_cuota" disabled>
              <label>A Cuenta (Anterior)</label>
              <input type="text" value="{{$cronograma['select_acuentaanterior']}}" id="acuenta" disabled>
        </div>
        <div class="col-sm-4">
              <label>Total de Moras</label>
              <input type="text" value="{{$cronograma['select_mora']}}" id="cuotas_total_mora" disabled>
              <label>Mora a Descontar</label>
              <input type="text" value="{{$cronograma['select_moradescontado']}}" id="moradescuento" disabled>
              <label>Mora a Pagar</label>
              <input type="text" value="{{$cronograma['select_moraapagar']}}" id="total_moraapagar" disabled>
        </div>
        <div class="col-sm-4">
              <label>Total</label>
              <input type="text" value="{{$cronograma['select_cuotaapagar']}}" id="cuotas_total" disabled>
              <label>Total Abono (-)</label>
              <input type="text" value="{{$cronograma['total_cancelada_abono']}}" id="abono_total" disabled>
              <label>Deuda Total</label>
              <input type="text" value="{{number_format($cronograma['select_cuotaapagar']-$cronograma['total_cancelada_abono'], 2, '.', '')}}" id="cuotas_total" disabled>
        </div>
    </div>
  
      <?php
      $promedio_numerocuota = ($s_prestamo_credito->numerocuota/2)-1;
      ?>
      @if($promedio_numerocuota<count($cronograma['cuotas_pendientes']))
              <div class="mensaje-danger">
                    Para Solicitar Ampliación, debe pagar mínimo hasta la Cuota Número {{($s_prestamo_credito->numerocuota/2)+1}}.
              </div>
      @else
                    @if($cronograma['morapendiente']>0)
                        @if($cronograma['ultimacuota']=='ok')
                            <div class="mensaje-danger">
                                  Hay Moras pendientes sin sustentar de {{$cronograma['morapendiente']}}, se esta agregando a la Deuda Total.
                            </div>
                        @endif
                    @endif
                    <div class="list-single-main-wrapper fl-wrap">
                        <div class="breadcrumbs gradient-bg fl-wrap">
                          <span>Solicitar Crédito</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label>Monto *</label>
                            <input type="number" id="monto" min="0" step="0.01" onkeyup="creditoCalendario()" onclick="creditoCalendario()"/>
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
                            <label>Tipo de Interes</label>
                            <select id="idtipotasa" disabled>
                                <option value="1">INTERES FIJA</option>
                                <option value="2">INTERES EFECTIVA</option>
                            </select>
                            <label>Interes % *</label>
                            <input type="number" id="tasa" min="0" step="0.001" onclick="creditoCalendario()" onkeyup="creditoCalendario()"/>
                            @if(configuracion($tienda->id,'prestamo_estadoabono')['valor']=='on')
                            <label>Abono *</label>
                            <input type="number" id="abono" min="0" step="0.01" onclick="creditoCalendario()" onkeyup="creditoCalendario()"/>
                            @endif
                            <label>Interes Total</label>
                            <input type="text" id="total_interes" value="0.00" disabled/>
                            @if(configuracion($tienda->id,'prestamo_estadoseguro_degravamen')['valor']=='on')
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
                    <button type="button" class="btn mx-btn-post" onclick="registrar_ampliacion()" style="margin-bottom: 5px;">Registrar Ampliación</button>
      @endif
                    
      
    
</div>
<div class="col-sm-6">
    <div class="tabs-container" id="tab-credito">
        <ul class="tabs-menu">
            <li class="current"><a href="#tab-credito-0">Nuevo Cronograma</a></li>
            <li><a href="#tab-credito-1">Cronograma de Crédito</a></li>
        </ul>
        <div class="tab">
            <div id="tab-credito-0" class="tab-content" style="display: block;">
              <div id="cont-load-creditocalendario"></div>
            </div>
            <div id="tab-credito-1" class="tab-content" style="display: none;">
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
              @foreach($cronograma['cuotas_pendientes'] as $value)
                  <tr style="{{$value['tabla_colortr']}};" {{$value['tabla_class']}}>
                      <td style="padding: 8px;text-align: right;width: 10px;">{{$value['tabla_numero']}}</td>
                      <td style="padding: 8px;text-align: right;width: 90px;">{{$value['tabla_fechavencimiento']}}</td>
                      <td style="padding: 8px;text-align: right;">{{$value['tabla_cuota']}}</td>
                      <td style="padding: 8px;text-align: right;">{{$value['tabla_atraso']}} días</td>
                      <td style="padding: 8px;text-align: right;">{{$value['tabla_mora']}}</td>
                      <td style="padding: 8px;text-align: right;{{$value['tabla_style_mora']}}">{{$value['tabla_moradescontado']}}</td>
                      <td style="padding: 8px;text-align: right;{{$value['tabla_style_mora']}}">{{$value['tabla_moraapagar']}}</td>
                      <td style="padding: 8px;text-align: right;background-color: orange;color: white;">{{$value['tabla_cuotatotal']}}</td>
                      <td style="padding: 8px;text-align: right;">{{$value['tabla_acuenta']}}</td>
                      <td style="padding: 8px;text-align: right;">{{$value['tabla_cuotaapagar']}}</td>
                  </tr>
              @endforeach
                  </tbody>
                     <tfoot style="background: #31353d; color: #fff;">
                        <tr>
                            <td style="padding: 8px;text-align: right;" colspan="2">TOTAL</td>
                            <td style="padding: 8px;text-align: right;">{{$cronograma['total_pendiente_cuota']}}</td>
                            <td style="padding: 8px;text-align: right;">{{$cronograma['total_pendiente_atraso']}} días</td>
                            <td style="padding: 8px;text-align: right;">{{$cronograma['total_pendiente_mora']}}</td>
                            <td style="padding: 8px;text-align: right;">{{$cronograma['total_pendiente_moradescontado']}}</td>
                            <td style="padding: 8px;text-align: right;">{{$cronograma['total_pendiente_moraapagar']}}</td>
                            <td style="padding: 8px;text-align: right;">{{$cronograma['total_pendiente_cuotapago']}}</td>
                            <td style="padding: 8px;text-align: right;">{{$cronograma['total_pendiente_acuenta']}}</td>
                            <td style="padding: 8px;text-align: right;">{{$cronograma['total_pendiente_cuotaapagar']}}</td>
                        </tr>
                     </tfoot>
                  </table>
            </div>
        </div>
    </div>
</div>  

<!-- Detalle  -->
<script>
    // Tabulador de pestañas
    tab({click:'#tab-credito'});

    function registrar_ampliacion() {
        callback({
            route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamoampliacion',
            method: 'POST',
            carga:  '#carga-ampliacion',
            data:   {
                view: 'registrar-ampliacion',
                idprestamo_credito: $('#idcliente').val(),
                monto: $('#monto').val(),
                numerocuota: $('#numerocuota').val(),
                fechainicio: $('#fechainicio').val(),
                idfrecuencia: $('#idfrecuencia').val(),
                numerodias: $('#numerodias').val(),
                tasa: $('#tasa').val(),
                total_segurodesgravamen: $('#total_segurodesgravamen').val(),
                total_cuotafinal: $('#total_cuotafinal').val(),
                abono: $('#abono').val(),
                excluirsabado: $("#excluirsabado:checked").val(),
                excluirdomingo: $("#excluirdomingo:checked").val(),
                excluirferiado: $("#excluirferiado:checked").val(),
            }
        },
        function(resultado){
          location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamoampliacion') }}';
        })
    }
</script>

<!-- Crédito -->
<script>
    $('#idtipotasa').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1,
    }).val({{configuracion($tienda->id,'prestamo_tasapordefecto')['valor']!=''?configuracion($tienda->id,'prestamo_tasapordefecto')['valor']:1}}).trigger("change");
    $('#idfrecuencia').select2({
        placeholder: '-- Seleccionar Frecuencia --',
        minimumResultsForSearch: -1,
    });
    $('#idfrecuencia').select2({
        placeholder: '-- Seleccionar Frecuencia --',
        minimumResultsForSearch: -1,
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
        var tasa = $('#tasa').val();
        var abono = $('#abono').val();
      
        if(abono==undefined){
            abono = 0;
        }
      
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
                tasa: tasa,
                abono: abono,
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