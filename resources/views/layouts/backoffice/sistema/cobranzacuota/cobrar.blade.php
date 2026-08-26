<form action="javascript:;"
      onsubmit="return enviar_cobro(this)">
    <div class="modal-header">
        <h5 class="modal-title">COBRAR</h5>
        <button type="button" class="btn-close" id="close_opcionescredito" onclick="cerrarventana()" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
      <input type="hidden"
        name="idcredito_cargo_ids"
        id="idcredito_cargo_ids"
        value='@json($idcredito_cargo_ids ?? [])'>
      @if($creditorefinanciado)
          <p class="text-center" 
             style="background-color: #dc3545;
                    padding: 10px;
                    border-radius: 5px;
                    color: #fff;
                    width: 80%;
                    margin: auto;">El Crédito esta En Refinanciamiento.</p>
      @else
          <div class="row">
            @if($opcion_pago=='PAGO_CUOTA' or $opcion_pago=='PAGO_TOTAL')
              <input type="hidden" id="acuenta_anterior" value="0.00">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Cuentas por Cobrar</label>
            <div class="col-sm-4">
                @if ($opcion_pago=='PAGO_TOTAL')
                  <input type="text" value="{{  $monto_cargo }}" 
                    class="form-control campo_moneda" id="cobrar_cargo" valida_input_vacio disabled>
                @else
                  <div class="input-group">
                    <div class="input-group-text">
                      <label class="chk">
                        <input type="checkbox" id="estadocargo" onclick="estado_cargo(this)" checked>
                        <span class="checkmark"></span>
                      </label>
                    </div>
                    <input type="text" value="{{  $monto_cargo }}" 
                      class="form-control campo_moneda" id="cobrar_cargo" valida_input_vacio disabled>
                  </div>
                @endif
            </div>
            <div style="display:none;"> 
            <label class="col-sm-8 col-form-label" style="text-align: right;">Cuota a Pagar</label>
            <div class="col-sm-4">
              <input type="text" value="{{  number_format($monto_cuotaapagar, 2, '.', '')}}" 
              class="form-control campo_moneda" id="cobrar_cuota_pagar" valida_input_vacio disabled>
            </div>
            </div>
            <label class="col-sm-8 col-form-label" style="text-align: right;">Total a Pagar</label>
            <div class="col-sm-4">
              <input type="text" value="{{  number_format(round($monto_totalapagar,1), 2, '.', '')}}" 
              class="form-control campo_moneda" id="cobrar_total_pagar" valida_input_vacio disabled>
            </div>
            <label class="col-sm-8 col-form-label" style="text-align: right;">Total Recibido</label>
            <div class="col-sm-4">
              <input type="text" value="{{  number_format(round($monto_totalapagar,1), 2, '.', '')}}" 
                     class="form-control campo_moneda" id="cobrar_total_recibido" valida_input_vacio onkeyup="cobrartotalpagar()">
            </div>
            <label class="col-sm-8 col-form-label" style="text-align: right;">Vuelto</label>
            <div class="col-sm-4">
              <input type="text" value="0.00" class="form-control campo_moneda" id="cobrar_vuelto" disabled>
            </div>
          @elseif($opcion_pago=='PAGO_ACUENTA' or $opcion_pago=='PAGO_ANTICIPADO')

            <label class="col-sm-8 col-form-label" style="text-align: right;">A cuenta (Anterior)</label>
            <div class="col-sm-4">
              <input type="text" value="{{  number_format(round($total_acuenta,1), 2, '.', '')}}" 
              class="form-control campo_moneda" id="acuenta_anterior" valida_input_vacio disabled>
            </div>

            <label class="col-sm-8 col-form-label" style="text-align: right;">Dinero Recibido</label>
            <div class="col-sm-4">
              <input type="text" value="0.00" 
              class="form-control campo_moneda" id="cobrar_total_pagar" valida_input_vacio onkeyup="calcularvuelto()">
            </div>
            <label class="col-sm-8 col-form-label" style="text-align: right;">Pago a Cuenta</label>
            <div class="col-sm-4">
              <input type="text" value="0.00" class="form-control campo_moneda" id="cobrar_total_recibido" valida_input_vacio onkeyup="calcularvuelto()">
            </div>
            <label class="col-sm-8 col-form-label" style="text-align: right;">Vuelto</label>
            <div class="col-sm-4">
              <input type="text" value="0.00" class="form-control campo_moneda" id="cobrar_vuelto" disabled>
            </div>
            @if($opcion_pago=='PAGO_ANTICIPADO')
              <div class="col-sm-12" id="alerta_monto_cancelacion" style="display:none;">
                  <div class="alert alert-danger" style="padding:6px 10px;margin-bottom:4px;">
                  </div>
              </div>
              <label class="col-sm-12 col-form-label" style="font-weight:bold;">Modalidad</label>
              <div class="col-sm-12">
                <select id="modalidad_pagoanticipado" class="form-control">
                    <option></option>
                    <option value="reduccion_plazo" @if($credito_vencido ?? false) disabled @endif>1. Pago anticipado Parcial (con Reducción de Plazo)</option>
                    <option value="reduccion_cuota" @if($credito_vencido ?? false) disabled @endif>2. Pago anticipado Parcial (con Reducción de Cuota)</option>
                    <option value="cancelacion_total">3. Pago anticipado Total (Cancelación)</option>
                </select>
              </div>
              <div class="col-sm-12 mt-1 py-2" id="info_saldo_pagoanticipado" style="display:none;">
                <div class="alert alert-primary" style="padding:6px 10px">
                  <table class="table table-sm table-bordered" style="margin-bottom:0;border-color: transparent !important;">
                      <tr>
                          <th style="text-align:left;"><b>Total de deuda inicial programada</b></th>
                          <th style="text-align:right;">S/. {{ number_format($total_cancelacion_sin_descuento, 2, '.', '') }}</th>
                      </tr>
                      <tr id="fila_monto_abonar" style="display:none;">
                          <td style="text-align:left;"><b>Monto a Abonar</b></td>
                          <td style="text-align:right;" id="td_monto_abonar">S/. 0.00</td>
                      </tr>
                      <tr>
                          <td style="text-align:left;"><b>Descuento (Interés + Carg. x Cust. G./Ot. + Ss. Recau.)</b></td>
                          <td style="text-align:right;" id="td_descuento_pagoanticipado">S/. {{ number_format($total_cancelacion_descuento, 2, '.', '') }}</td>
                      </tr>
                      <tr style="background-color: #efefef;">
                          <td style="text-align:left;" id="td_label_saldo_pagoanticipado"><b>Monto a cancelar</b></td>
                          <td style="text-align:right;" id="td_saldo_pagoanticipado"><b>S/. {{ number_format($total_cancelacion_saldo, 2, '.', '') }}</b></td>
                      </tr>
                  </table>
                </div>
              </div>
                <div class="col-sm-12 mt-1">
                  <button type="button" class="btn btn-warning btn-sm" onclick="previsualizar_pagoanticipado()">
                    <i class="fa-solid fa-eye"></i> Previsualizar cronograma
                  </button>
                </div>
            @endif
          @else
              <input type="hidden" id="cobrar_total_pagar" value="0.00">
              <input type="hidden" id="cobrar_total_recibido" value="0.00">
              <input type="hidden" id="cobrar_vuelto" value="0.00">
            <label class="col-sm-8 col-form-label" style="text-align: right;">Cuentas por Cobrar</label>
            <div class="col-sm-4">
              <input type="text" value="{{  number_format(round($monto_cargo,1), 2, '.', '')}}" 
              class="form-control campo_moneda" id="cobrar_cargo" valida_input_vacio disabled>
            </div>
          @endif
          </div>
          <div class="row">
            <label class="col-sm-12 col-form-label">cobrar por:</label>
            <div class="col-sm-12">
              <select id="idformapago" class="form-control">
                  <option></option>
                  <option value="1">CAJA</option>
                  <option value="2">BANCO</option>
              </select>
            </div>
          </div>
          <div id="cont_banco_n" style="display:none;">
          <div class="row">
            <label class="col-sm-12 col-form-label">Bancos:</label>
            <div class="col-sm-12">
              <select id="idbanco" class="form-control" disabled>
                  <option></option>
                  @foreach($bancos as $value)
                  <option value="{{ $value->id }}">{{ $value->nombre }}: ***{{ substr($value->cuenta, -5) }}</option>
                  @endforeach
              </select>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-12 col-form-label">Nro Operación:</label>
            <div class="col-sm-12">
              <input type="text" id="numerooperacion" class="form-control" disabled>
            </div>
          </div>
          </div>
          @if($select_numerocuota_fin==$credito->cuotas && $credito->idforma_credito==1)
              <label class="chk" style="color: red;font-size: 14px;font-weight: bold;margin-top:3px;">
                <input type="checkbox" name="entregargarantia" id="entregargarantia" checked>
                <span class="checkmark"></span> Entregar Garantia
              </label>
          @endif
          <div class="row mt-1">
            <div class="col" style="flex: 0 0 0%;">
              <button type="submit" class="btn btn-success"><i class="fa-solid fa-check"></i> COBRAR</button>
            </div>
            <div class="col" style="flex: 1 0 0%;">
              <div id="success-message" class="alert alert-success d-none" style="text-align:left;"></div>
            </div>
            <div class="col" style="flex: 0 0 0%;">
              <button type="button" class="btn btn-danger" id="close_confirmacionproceso" onclick="cerrarventana()" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-x"></i> SALIR</button>
            </div>
          </div>
      @endif    
    </div>
    
</form>   

<style>


input::selection {
  background: #d9d8d8;
  color: black;
}
</style>
<script>

  valida_input_vacio();
  sistema_select2({ input:'#idformapago', val: 1 });
  sistema_select2({ input:'#idbanco' });
  sistema_select2({ input:'#modalidad_pagoanticipado' });

  var totalDeudaCancelacion = {{ (float) $total_cancelacion_sin_descuento }};
  var totalDescuentoCancelacion = {{ (float) $total_cancelacion_descuento }};
  var saldoTotalConDescuentoCancelacion = {{ (float) $total_cancelacion_saldo }};
  var creditoVencido = {{ ($credito_vencido ?? false) ? 'true' : 'false' }};

  // Caso 1/2: a diferencia del caso 3 (que siempre paga TODO y por eso siempre alcanza el
  // descuento completo de las cuotas futuras), el abono es parcial: el descuento real solo
  // cubre las cuotas futuras hasta donde el abono realmente llegue. Se recalcula via AJAX
  // (mismo calculo que usa la cascada real de pagos) cada vez que cambia el monto.
  var totalDescuentoCaso12 = 0;
  // Saldo capital restante REAL (exacto al de la previsualizacion, ver abajo): null mientras se
  // recalcula via AJAX -no se estima con una formula aparte, el Caso 1 puede terminar eliminando
  // cuotas extra por el ahorro de interes, algo que "total - monto - descuento" no reproduce-.
  var montoSaldoNuevoCaso12 = null;
  var descuentoCaso12Ajax = null;

  function actualizar_descuento_caso12(monto){
      if(descuentoCaso12Ajax){ descuentoCaso12Ajax.abort(); }
      montoSaldoNuevoCaso12 = null;
      descuentoCaso12Ajax = $.ajax({
          url: '{{ url('backoffice/'.$tienda->id.'/cobranzacuota/show_descuento_pagoanticipado') }}',
          type: 'GET',
          data: { idcredito: {{$credito->id}}, monto: monto, modalidad: $('#modalidad_pagoanticipado').val() },
          success: function(respuesta){
              totalDescuentoCaso12 = parseFloat(respuesta.descuento) || 0;
              montoSaldoNuevoCaso12 = (respuesta.monto_saldo_nuevo !== undefined && respuesta.monto_saldo_nuevo !== null)
                  ? parseFloat(respuesta.monto_saldo_nuevo) : null;
              var modalidadActual = $('#modalidad_pagoanticipado').val();
              if(modalidadActual=='reduccion_plazo' || modalidadActual=='reduccion_cuota'){
                  pintar_tabla_caso12(parseFloat($('#cobrar_total_recibido').val()) || 0);
              }
          }
      });
  }

  function pintar_tabla_caso12(montoIngresado){
      $('#fila_monto_abonar').css('display','table-row');
      $('#td_monto_abonar').text('S/. '+montoIngresado.toFixed(2));
      $('#td_descuento_pagoanticipado').text('S/. '+totalDescuentoCaso12.toFixed(2));
      $('#td_label_saldo_pagoanticipado').html('<b>Saldo capital de deuda a reprogramar</b>');
      // Mismo saldo que calcula la previsualizacion real (Caso 1/2): mientras el AJAX de
      // show_descuento_pagoanticipado no responda todavia, se muestra "Calculando..." en vez de
      // estimar con una formula aparte (que no reproduce, p.ej., las cuotas extra que el Caso 1
      // puede eliminar por el ahorro de interes).
      if(montoSaldoNuevoCaso12 !== null){
          $('#td_saldo_pagoanticipado').html('<b>S/. '+montoSaldoNuevoCaso12.toFixed(2)+'</b>');
      }else{
          $('#td_saldo_pagoanticipado').html('<i>Calculando...</i>');
      }
  }

  function verificar_monto_cancelacion(){
      var modalidad = $('#modalidad_pagoanticipado').val();
      if(creditoVencido && (modalidad=='reduccion_plazo' || modalidad=='reduccion_cuota')){
          $('#info_saldo_pagoanticipado').css('display','none');
          $('#alerta_monto_cancelacion .alert').text('El Crédito esta vencido.');
          $('#alerta_monto_cancelacion').css('display','block');
      }else if(modalidad=='cancelacion_total'){
          $('#fila_monto_abonar').css('display','none');
          $('#td_descuento_pagoanticipado').text('S/. '+totalDescuentoCancelacion.toFixed(2));
          $('#td_label_saldo_pagoanticipado').html('<b>Monto a cancelar</b>');
          $('#td_saldo_pagoanticipado').html('<b>S/. '+saldoTotalConDescuentoCancelacion.toFixed(2)+'</b>');
          $('#info_saldo_pagoanticipado').css('display','block');
          var montoIngresado = parseFloat($('#cobrar_total_recibido').val()) || 0;
          if(montoIngresado.toFixed(2) != saldoTotalConDescuentoCancelacion.toFixed(2)){
              var diferencia = montoIngresado > saldoTotalConDescuentoCancelacion ? 'MAYOR' : 'MENOR';
              $('#alerta_monto_cancelacion .alert').text('Monto ingresado (S/. '+montoIngresado.toFixed(2)+') es '+diferencia+' a la cancelación (S/. '+saldoTotalConDescuentoCancelacion.toFixed(2)+').');
              $('#alerta_monto_cancelacion').css('display','block');
          }else{
              $('#alerta_monto_cancelacion').css('display','none');
          }
      }else if(modalidad=='reduccion_plazo' || modalidad=='reduccion_cuota'){
          var montoIngresado = parseFloat($('#cobrar_total_recibido').val()) || 0;
          pintar_tabla_caso12(montoIngresado);
          $('#info_saldo_pagoanticipado').css('display','block');
          actualizar_descuento_caso12(montoIngresado);
          if(montoIngresado >= saldoTotalConDescuentoCancelacion){
              $('#alerta_monto_cancelacion .alert').text('El monto ingresado (S/. '+montoIngresado.toFixed(2)+') no puede ser igual ni mayor al monto de cancelación total (S/. '+saldoTotalConDescuentoCancelacion.toFixed(2)+'). Si desea cancelar el crédito completo, seleccione el caso 3 (Cancelación Total).');
              $('#alerta_monto_cancelacion').css('display','block');
          }else{
              $('#alerta_monto_cancelacion').css('display','none');
          }
      }else{
          $('#info_saldo_pagoanticipado').css('display','none');
          $('#alerta_monto_cancelacion').css('display','none');
      }
  }

  $('#modalidad_pagoanticipado').on('change', verificar_monto_cancelacion);

  if(creditoVencido){
      $('#alerta_monto_cancelacion .alert').text('El Crédito esta vencido.');
      $('#alerta_monto_cancelacion').css('display','block');
  }

  $("#cobrar_total_recibido").select();
  
  function enviar_cobro(thisForm){
      @if($opcion_pago=='PAGO_ANTICIPADO')
      if($('#modalidad_pagoanticipado').val()==''){
          let mensaje = 'Debe seleccionar una modalidad de pago anticipado.';
          modal({ route:'{{ url('backoffice/'.$tienda->id.'/inicio/create?view=alerta') }}&mensaje='+mensaje, size: 'modal-sm' });
          return false;
      }
      if(creditoVencido && ($('#modalidad_pagoanticipado').val()=='reduccion_plazo' || $('#modalidad_pagoanticipado').val()=='reduccion_cuota')){
          let mensaje = 'El Crédito esta vencido.';
          modal({ route:'{{ url('backoffice/'.$tienda->id.'/inicio/create?view=alerta') }}&mensaje='+mensaje, size: 'modal-sm' });
          return false;
      }
      var saldoTotalPendiente = {{ (float) $saldo_total_pendiente }};
      var montoIngresado = parseFloat($('#cobrar_total_recibido').val()) || 0;
      if(montoIngresado > saldoTotalPendiente){
          let mensaje = 'El monto ingresado no puede superar el saldo total pendiente del crédito (S/. '+saldoTotalPendiente.toFixed(2)+').';
          modal({ route:'{{ url('backoffice/'.$tienda->id.'/inicio/create?view=alerta') }}&mensaje='+mensaje, size: 'modal-sm' });
          return false;
      }
      if(($('#modalidad_pagoanticipado').val()=='reduccion_plazo' || $('#modalidad_pagoanticipado').val()=='reduccion_cuota') && montoIngresado >= saldoTotalConDescuentoCancelacion){
          let mensaje = 'El monto ingresado no puede ser igual ni mayor al monto de cancelación total (S/. '+saldoTotalConDescuentoCancelacion.toFixed(2)+'). Si desea cancelar el crédito completo, seleccione el caso 3 (Cancelación Total).';
          modal({ route:'{{ url('backoffice/'.$tienda->id.'/inicio/create?view=alerta') }}&mensaje='+mensaje, size: 'modal-sm' });
          return false;
      }
      @endif

      callback({
          route: '{{ url('backoffice/'.$tienda->id.'/cobranzacuota') }}',
          method: 'POST',
          data:{
              view: 'registrar',
              idcredito: {{$credito->id}},
              numerocuota: {{$numerocuota}},
              opcion_pago: '{{$opcion_pago}}',
              idcredito_cargo: {{$idcredito_cargo}},
              idcredito_descuentocuota: {{$idcredito_descuentocuota}}
          }
      },
      function(resultado){
        @if($opcion_pago=='PAGO_CUOTA')
        pagocuota();
        @elseif($opcion_pago=='PAGO_ACUENTA')
        pagoacuenta();
        @elseif($opcion_pago=='PAGO_ANTICIPADO')
        pagoanticipado();
        @elseif($opcion_pago=='PAGO_TOTAL')
        @endif

        if(resultado.cuota_reducida){
            alert('Se recalcularon las cuotas pendientes sobre el saldo restante de S/. '+resultado.monto_saldo_nuevo+'. La nueva cuota es de aproximadamente S/. '+resultado.cuota_pago_nueva+'.');
        }
        if(resultado.plazo_reducido){
            var msjEliminadas = resultado.cuotas_eliminadas > 0 ? (' Se eliminaron '+resultado.cuotas_eliminadas+' cuota(s) ya cubiertas por el abono.') : '';
            alert('Se redujo el plazo del crédito. La cuota se mantiene igual; las cuotas restantes ahora vencen antes, la última queda para el '+resultado.fecha_ultimopago_nueva+'.'+msjEliminadas);
        }

        show_data_credito(resultado.idcredito);

        // sigue mostrando el modal
        ver_opciones(
            resultado.idcobranzacuota,
            resultado.idestadocredito,
            resultado.entregargarantia
        );

        $('#close_opcionescredito').click();
      }, thisForm);

      return false;
  }

  function cerrarventana(){
        @if($opcion_pago=='PAGO_CUOTA')
        //pagocuota();
        @elseif($opcion_pago=='PAGO_ACUENTA')
        pagoacuenta();
        @elseif($opcion_pago=='PAGO_ANTICIPADO')
        pagoanticipado();
        @elseif($opcion_pago=='PAGO_TOTAL')
        @endif
  }
  
  function cobrartotalpagar(){
      var cobrar_total_recibido = parseFloat($('#cobrar_total_recibido').val());
      var cobrar_total_pagar = parseFloat($('#cobrar_total_pagar').val());
      var cobrar_vuelto_efectivo = cobrar_total_recibido-cobrar_total_pagar;
      $('#cobrar_vuelto').val(cobrar_vuelto_efectivo.toFixed(2));
      //var cobrar_cuota_pagar = parseFloat($('#cobrar_cuota_pagar').val());
      //cronograma({{$credito->id}},0,'pagoacuenta',cobrar_cuota_pagar);
      //calcularvuelto();
  }
  
  
  function calcularvuelto(){
      //var acuenta_anterior = parseFloat($('#acuenta_anterior').val());
      var cobrar_total_recibido = parseFloat($('#cobrar_total_recibido').val());
      var cobrar_total_pagar = parseFloat($('#cobrar_total_pagar').val());
      var cobrar_vuelto_efectivo = cobrar_total_pagar-cobrar_total_recibido;
      $('#cobrar_vuelto').val(cobrar_vuelto_efectivo.toFixed(2));
      @if($opcion_pago=='PAGO_ACUENTA')
      cronograma({{$credito->id}},0,'pagoacuenta',cobrar_total_recibido);
      @elseif($opcion_pago=='PAGO_ANTICIPADO')
      cronograma({{$credito->id}},0,'pagoanticipado',cobrar_total_recibido);
      verificar_monto_cancelacion();
      @endif
  }
  
  function previsualizar_pagoanticipado(){
      var monto = parseFloat($('#cobrar_total_recibido').val()) || 0;
      var modalidad = $('#modalidad_pagoanticipado').val();
      modal({ route:'{{ url('backoffice/'.$tienda->id.'/cobranzacuota') }}/{{$credito->id}}/edit?view=preview_pagoanticipado&monto='+monto+'&modalidad='+modalidad, size: 'modal-lg' });
  }

  $("#idformapago").on("change", function(e) {
    
      $('#cont_banco_n').css('display','none');
      $('#numerooperacion').attr('disabled',true);
      $('#idbanco').attr('disabled',true);
      if(e.currentTarget.value==2){
          $('#cont_banco_n').css('display','block');
          $('#numerooperacion').attr('disabled',false);
          $('#idbanco').attr('disabled',false);
      }
  });
  
  function estado_cargo(e){
    
      var cobrar_cargo = parseFloat($('#cobrar_cargo').val());
      var cobrar_total_pagar = parseFloat($('#cobrar_total_pagar').val());
    
      let estado_check = $(e).prop("checked");
      if(estado_check){
          $('#cobrar_total_pagar').val((cobrar_total_pagar+cobrar_cargo).toFixed(2));
          $('#cobrar_total_recibido').val((cobrar_total_pagar+cobrar_cargo).toFixed(2));
      }
      else{
          $('#cobrar_total_pagar').val((cobrar_total_pagar-cobrar_cargo).toFixed(2));
          $('#cobrar_total_recibido').val((cobrar_total_pagar-cobrar_cargo).toFixed(2));
      }
      cobrartotalpagar();
  }
</script>