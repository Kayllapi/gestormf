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
                    <option value="reduccion_plazo">1. Pago anticipado Parcial (con Reducción de Plazo)</option>
                    <option value="reduccion_cuota">2. Pago anticipado Parcial (con Reducción de Cuota)</option>
                    <option value="cancelacion_total">3. Pago anticipado Total (Cancelación)</option>
                </select>
              </div>
              <div class="col-sm-12 mt-1" id="info_saldo_pagoanticipado" style="display:none;">
                  <div class="alert alert-info" style="padding:6px 10px;margin-bottom:0;text-align:left;">
                      <b>Total de deuda</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : S/. {{ number_format($total_cancelacion_sin_descuento, 2, '.', '') }}<br>
                      <b>Descuento (cuotas futuras)</b> &nbsp;: S/. {{ number_format($total_cancelacion_descuento, 2, '.', '') }}<br>
                      <span style="background-color: #dfdf79;"><b>Monto a cancelar</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : S/. {{ number_format($total_cancelacion_sin_descuento - $total_cancelacion_descuento, 2, '.', '') }}</span>
                  </div>
              </div>
              <div class="col-sm-12 mt-1">
                  <button type="button" class="btn btn-primary btn-sm" onclick="previsualizar_pagoanticipado()">
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

  var saldoTotalConDescuentoCancelacion = {{ (float) $total_cancelacion_sin_descuento - (float) $total_cancelacion_descuento }};

  function verificar_monto_cancelacion(){
      var modalidad = $('#modalidad_pagoanticipado').val();
      if(modalidad=='cancelacion_total'){
          $('#info_saldo_pagoanticipado').css('display','block');
          var montoIngresado = parseFloat($('#cobrar_total_recibido').val()) || 0;
          if(montoIngresado.toFixed(2) != saldoTotalConDescuentoCancelacion.toFixed(2)){
              var diferencia = montoIngresado > saldoTotalConDescuentoCancelacion ? 'MAYOR' : 'MENOR';
              $('#alerta_monto_cancelacion .alert').text('Monto ingresado (S/. '+montoIngresado.toFixed(2)+') es '+diferencia+' a la cancelación (S/. '+saldoTotalConDescuentoCancelacion.toFixed(2)+').');
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

  $("#cobrar_total_recibido").select();
  
  function enviar_cobro(thisForm){
      @if($opcion_pago=='PAGO_ANTICIPADO')
      if($('#modalidad_pagoanticipado').val()==''){
          let mensaje = 'Debe seleccionar una modalidad de pago anticipado.';
          modal({ route:'{{ url('backoffice/'.$tienda->id.'/inicio/create?view=alerta') }}&mensaje='+mensaje, size: 'modal-sm' });
          return false;
      }
      var saldoTotalPendiente = {{ (float) $credito->total_pendientepago }};
      var montoIngresado = parseFloat($('#cobrar_total_recibido').val()) || 0;
      if(montoIngresado > saldoTotalPendiente){
          let mensaje = 'El monto ingresado no puede superar el saldo total pendiente del crédito (S/. '+saldoTotalPendiente.toFixed(2)+').';
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
            alert('Se redujo el plazo del crédito. Las cuotas restantes ahora vencen antes; la última cuota queda para el '+resultado.fecha_ultimopago_nueva+'.');
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