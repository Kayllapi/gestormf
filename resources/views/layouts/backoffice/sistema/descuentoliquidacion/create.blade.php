<form action="javascript:;" 
    onsubmit="callback({
        route: '{{ url('backoffice/'.$tienda->id.'/descuentoliquidacion') }}',
        method: 'POST',
        data:{
            view: 'registrar',
            idcredito: '{{$credito->id}}',
        }
    },
    function(resultado){
        show_data_cronograma();
        show_data_descuentodecuotas();
        $('#close_opcionescredito').click();
    },this)"> 
    <div class="modal-header">
        <h5 class="modal-title">Registrar Cuota Descuento</h5>
        <button type="button" class="btn-close" id="close_opcionescredito" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <table class="table table-bordered">
          <tr>
              <td><b>N° Cuota</b></td>
              <td><input type="text" class="form-control" style="text-align:center;" 
                         value="{{ $cronograma['select_numerocuota_inicio']==$cronograma['select_numerocuota_fin']?$cronograma['select_numerocuota_inicio']:($cronograma['select_numerocuota_inicio'].' - '.$cronograma['select_numerocuota_fin']) }}" id="data_numerocuota" disabled>
                         <input type="hidden" value="{{ $cronograma['select_numerocuota_inicio'] }}" id="data_numerocuota_inicio">
                         <input type="hidden" value="{{ $cronograma['select_numerocuota_fin'] }}" id="data_numerocuota_fin">
              </td>
              <td><b>Monto Descuento</b></td>
              <td><b>Saldo</b></td>
          </tr>
          <tr>
              <td><b>Capital</b></td>
              <td><input type="text" class="form-control campo_moneda" value="{{ $cronograma['select_amortizacion'] }}" id="data_capital" disabled></td>
              <td><input type="text" valida_input_vacio class="form-control campo_moneda" value="0.00" id="descuento_capital" onkeyup="calcular_descuento(2)"></td>
              <td><input type="text" class="form-control campo_moneda" id="saldo_capital" disabled></td>
          </tr>
          <tr>
              <td><b>Interes</b></td>
              <td><input type="text" class="form-control campo_moneda" value="{{ $cronograma['select_interes'] }}" id="data_interes" disabled></td>
              <td><input type="text" valida_input_vacio  class="form-control campo_moneda" value="0.00" id="descuento_interes" onkeyup="calcular_descuento(2)"></td>
              <td><input type="text" class="form-control campo_moneda" id="saldo_interes" disabled></td>
          </tr>
          <tr>
              <td><b>Comisión</b></td>
              <td><input type="text" class="form-control campo_moneda" value="{{ $cronograma['select_comision'] }}" id="data_comision" disabled></td>
              <td><input type="text" valida_input_vacio class="form-control campo_moneda" value="0.00" id="descuento_comision" onkeyup="calcular_descuento(2)"></td>
              <td><input type="text" class="form-control campo_moneda" id="saldo_comision" disabled></td>
          </tr>
          <tr>
              <td><b>Cargo</b></td>
              <td><input type="text" class="form-control campo_moneda" value="{{ $cronograma['select_cargo'] }}" id="data_cargo" disabled></td>
              <td><input type="text" valida_input_vacio class="form-control campo_moneda" value="0.00" id="descuento_cargo" onkeyup="calcular_descuento(2)"></td>
              <td><input type="text" class="form-control campo_moneda" id="saldo_cargo" disabled></td>
          </tr>
          <tr>
              <td><b>Penalidad</b></td>
              <td><input type="text" class="form-control campo_moneda" value="{{ $cronograma['select_penalidad'] }}" id="data_penalidad" disabled></td>
              <td><input type="text" valida_input_vacio class="form-control campo_moneda" value="0.00" id="descuento_penalidad" onkeyup="calcular_descuento(2)"></td>
              <td><input type="text" class="form-control campo_moneda" id="saldo_penalidad" disabled></td>
          </tr>
          <tr>
              <td><b>Tenencia</b></td>
              <td><input type="text" class="form-control campo_moneda" value="{{ $cronograma['select_tenencia'] }}" id="data_tenencia" disabled></td>
              <td><input type="text" valida_input_vacio class="form-control campo_moneda" value="0.00" id="descuento_tenencia" onkeyup="calcular_descuento(2)"></td>
              <td><input type="text" class="form-control campo_moneda" id="saldo_tenencia" disabled></td>
          </tr>
          <tr>
              <td><b>Interes Moratorio</b></td>
              <td><input type="text" class="form-control campo_moneda" value="{{ $cronograma['select_compensatorio'] }}" id="data_compensatorio" disabled></td>
              <td><input type="text" valida_input_vacio  class="form-control campo_moneda" value="0.00" id="descuento_compensatorio" onkeyup="calcular_descuento(2)"></td>
              <td><input type="text" class="form-control campo_moneda" id="saldo_compensatorio" disabled></td>
          </tr>
          <tr>
              <td><b>Totales</b></td>
              <td><input type="text" class="form-control campo_moneda" value="0.00" id="data_total" disabled></td>
              <td><input type="text" class="form-control campo_moneda" id="descuento_total" disabled></td>
              <td><input type="text" class="form-control campo_moneda" id="saldo_total" disabled></td>
          </tr>
        </table>
        <label class="mt-1" style="background-color: #636363;
          color: #fff;
          width: 100%;
          border-radius: 5px;
          padding: 0px 5px;
          margin-bottom: 5px;">Aprobación</label>
              <div class="mb-1">
                  <label>Responsable (Administración) *</label>
                  <select class="form-select" id="idresponsable">
                      <option value=""></option>
                      @foreach($usuarios as $value)
                      <option value="{{$value->id}}">{{$value->nombrecompleto}} ({{$value->nombrepermiso}})</option>
                      @endforeach
                  </select>
              </div>
              <div class="mb-1">
                  <label>Contraseña *</label>
                  <input type="password" class="form-control" id="responsableclave">
              </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Generar Descuento</button>
    </div>
</form>  
<script>
  
  
  valida_input_vacio();
  calcular_descuento();
  

    sistema_select2({ input:'#idresponsable' });

  
  function calcular_descuento(){
    
      var data_capital = parseFloat($('#data_capital').val()!=''?$('#data_capital').val():0);
      var data_comision = parseFloat($('#data_comision').val()!=''?$('#data_comision').val():0);
      var data_cargo = parseFloat($('#data_cargo').val()!=''?$('#data_cargo').val():0);
      var data_interes = parseFloat($('#data_interes').val()!=''?$('#data_interes').val():0);
      var data_penalidad = parseFloat($('#data_penalidad').val()!=''?$('#data_penalidad').val():0);
      var data_tenencia = parseFloat($('#data_tenencia').val()!=''?$('#data_tenencia').val():0);
      var data_compensatorio = parseFloat($('#data_compensatorio').val()!=''?$('#data_compensatorio').val():0);
   
      var descuento_capital = parseFloat($('#descuento_capital').val()!=''?$('#descuento_capital').val():0);
      var descuento_comision = parseFloat($('#descuento_comision').val()!=''?$('#descuento_comision').val():0);
      var descuento_cargo = parseFloat($('#descuento_cargo').val()!=''?$('#descuento_cargo').val():0);
      var descuento_interes = parseFloat($('#descuento_interes').val()!=''?$('#descuento_interes').val():0);
      var descuento_penalidad = parseFloat($('#descuento_penalidad').val()!=''?$('#descuento_penalidad').val():0);
      var descuento_tenencia = parseFloat($('#descuento_tenencia').val()!=''?$('#descuento_tenencia').val():0);
      var descuento_compensatorio = parseFloat($('#descuento_compensatorio').val()!=''?$('#descuento_compensatorio').val():0);
    
      var saldo_capital = parseFloat(data_capital-descuento_capital);
      var saldo_comision = parseFloat(data_comision-descuento_comision);
      var saldo_cargo = parseFloat(data_cargo-descuento_cargo);
      var saldo_interes = parseFloat(data_interes-descuento_interes);
      var saldo_penalidad = parseFloat(data_penalidad-descuento_penalidad);
      var saldo_tenencia = parseFloat(data_tenencia-descuento_tenencia);
      var saldo_compensatorio = parseFloat(data_compensatorio-descuento_compensatorio);
    
      var data_total = parseFloat(data_capital+data_comision+data_cargo+data_interes+data_penalidad+data_tenencia+data_compensatorio);
      var descuento_total = parseFloat(descuento_capital+descuento_comision+descuento_cargo+descuento_interes+descuento_penalidad+descuento_tenencia+descuento_compensatorio);
      var saldo_total = parseFloat(saldo_capital+saldo_comision+saldo_cargo+saldo_interes+saldo_penalidad+saldo_tenencia+saldo_compensatorio);
    
      $('#saldo_capital').val(saldo_capital.toFixed(2));
      $('#saldo_comision').val(saldo_comision.toFixed(2));
      $('#saldo_cargo').val(saldo_cargo.toFixed(2));
      $('#saldo_interes').val(saldo_interes.toFixed(2));
      $('#saldo_penalidad').val(saldo_penalidad.toFixed(2));
      $('#saldo_tenencia').val(saldo_tenencia.toFixed(2));
      $('#saldo_compensatorio').val(saldo_compensatorio.toFixed(2));
    
      $('#data_total').val(data_total.toFixed(2));
      $('#descuento_total').val(descuento_total.toFixed(2));
      $('#saldo_total').val(saldo_total.toFixed(2));
  }
</script>    