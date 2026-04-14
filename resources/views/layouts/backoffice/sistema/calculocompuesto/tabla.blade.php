<div class="modal-header">
    <h5 class="modal-title">
      Simulador de Crédito (Cálculo Compuesto)
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()"></button>
</div>
<div class="modal-body">
  <div class="row justify-content-center">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-body" id="form-garantias-result">
           <div class="row">
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-6">
                  <div class="row">
                    <label class="col-sm-5 col-form-label" style="text-align: right;">Producto:</label>
                    <div class="col-sm-7">
                      <select class="form-control" id="idcredito_prendatario" onchange="show_tarifario_producto()">
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-5 col-form-label" style="text-align: right;">Monto de Préstamo</label>
                    <div class="col-sm-7">
                      <input type="number" step="any" class="form-control" id="monto_solicitado" value="0.00" onkeyup="showtasa();" onkeydown="showtasa();">
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-5 col-form-label" style="text-align: right;">Frecuencia de Pago:</label>
                    <div class="col-sm-7">
                      <select class="form-control" id="idforma_pago_credito" onchange="mostrar_tarifario_tasa();">
                        
                        @foreach($forma_pago_credito as $value)
                          <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-5 col-form-label" style="text-align: right;">Número de Cuotas:</label>
                    <div class="col-sm-7">
                      <input type="number" step="any" class="form-control" id="cuotas" value="0" onkeyup="showtasa();" onkeydown="showtasa();">
                      <input type="hidden" step="any" class="form-control" id="coutas_max_credito" value="0">
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-5 col-form-label" style="text-align: right;">TEM (%):</label>
                    <div class="col-sm-4">
                      <input type="number" step="any" class="form-control" id="tasa_tem" value="0.00" onkeyup="calcula_tip();" onkeydown="calcula_tip();">
                    </div>
                    <div class="col-sm-3">
                      <input type="number" step="any" class="form-control" id="tasa_tem_minima" value="0" disabled>
                    </div>
                  </div>
                  <div>
                  <div class="row d-none">
                    <label class="col-sm-5 col-form-label" style="text-align: right;">Tasa TIP (%):</label>
                    <div class="col-sm-7">
                      <input type="number" step="any" class="form-control" id="tasa_tip" value="0.00" disabled>
                      <input type="hidden" step="any" class="form-control" id="tasa_tip_2" value="0.00" disabled>
                    </div>
                  </div>  
                  </div>
                  <div class="row">
                    <label class="col-sm-5 col-form-label" style="text-align: right;">TCEM (%):</label>
                    <div class="col-sm-7">
                      <input type="number" step="any" class="form-control" id="tasa_tcem" value="0.00" disabled>
                    </div>
                  </div> 
                </div>
                <div class="col-md-6">
                  <div class="row">
                    <label class="col-sm-4 col-form-label" style="text-align: right;">Dia de Gracia:</label>
                    <div class="col-sm-8">
                      <input type="number" step="any" class="form-control" id="dia_gracia" value="0">
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-4 col-form-label" style="text-align: right;">Fecha de Cálculo:</label>
                    <div class="col-sm-8">
                      <input type="date" class="form-control" id="fecha_desembolso" value="{{ date('Y-m-d') }}" disabled>
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-4 col-form-label" style="text-align: right;">Ss. Recaudo S/.:</label>
                    <div class="col-sm-8">
                      <input type="number" step="any" class="form-control" id="comision" value="" disabled>
                    </div>
                    <div class="col-sm-4 d-none">
                      <input type="number" step="any" class="form-control" id="monto_cargos_otros" value="0.00" disabled>
                    </div>
                  </div>
                  <div class="row d-none">
                    <label class="col-sm-4 col-form-label" style="text-align: right;">Cargo Mes S/.:</label>
                    <div class="col-sm-8">
                      <input type="number" step="any" class="form-control" id="cargomes" value="0.00">
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-4 col-form-label" style="text-align: right;">Cargo x Custodia S/.:</label>
                    <div class="col-sm-8">
                      <input type="number" step="any" class="form-control" id="cargo" value="0.00" disabled>
                    </div>
                  </div>
                  <div class="row d-none" id="cont_mensaje_custodia">
                    <label class="col-sm-4"></label>
                    <label class="col-sm-8">
                        <label class="custom-radio" style="color: #b32121;">
                            <input type="radio" name="cargo_check" id="cargo_check" value="1" checked>
                            <span></span>
                            Gasto x custodia de garantía para cargo: (Acreedor)
                        </label>
                        <label class="custom-radio" style="color: #b32121;">
                            <input type="radio" name="cargo_check" id="cargo_check" value="2">
                            <span></span>
                            Gasto x custodia de garantía para cargo: (Convenio con Acreedor)
                        </label>
                        <label class="custom-radio" style="color: #b32121;">
                            <input type="radio" name="cargo_check" id="cargo_check" value="3">
                            <span></span>
                            Externo
                        </label>
                    </label>
                  </div>
                  <div class="row">
                    <div class="col-sm-8 mt-2">
                      <button type="button" class="btn btn-primary" id="generar_cronograma" onclick="cronograma()">CALCULAR</button>
                    </div>
                  </div>
                </div>
                </div>
            </div>
            
        </div>

        <div class="row mt-3" style="background-color: #cfecc5 !important;
              border: 1px solid #326222 !important;
              color: #000;">
          <div class="col-sm-12 col-md-2">
            
          </div>
          
          <div class="col-sm-12 col-md-10">
            <div class="row">
              <label class="col-sm-2 col-form-label" style="text-align: right;"><b>Interes Total (S/):</b></label>
              <div class="col-sm-1 col-form-label" id="interes_total" style="font-weight: normal;">
                0.00
              </div>
              <label class="col-sm-2 col-form-label" style="text-align: right;"><b>Ss. Recaudo (S/):</b></label>
              <div class="col-sm-1 col-form-label" id="total_comision" style="font-weight: normal;">
                0.00
              </div>
              <label class="col-sm-2 col-form-label" style="text-align: right;"><b>Cargos (S/):</b></label>
              <div class="col-sm-1 col-form-label" id="total_cargo" style="font-weight: normal;">
                0.00
              </div>
              <label class="col-sm-2 col-form-label" style="text-align: right;"><b>Total a Pagar (S/):</b></label>
              <div class="col-sm-1 col-form-label" id="total_pagar" style="font-weight: normal;">
                0.00
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-3 justify-content-center">
          <div class="col-sm-12 col-md-8">
            <table class="table table-striped" id="table-cronograma">
              <thead>
                <th>Cuota N°</th>
                <th>Fecha de Pago</th>
                <th class="text-end">Capital</th>
                <th class="text-end">Amortización</th>
                <th class="text-end">Interés</th>
                <th class="text-end">Cargo x Custodia Garant.</th>
                <th class="text-end">Cuota de Préstamo <br> (Int. + Cap. + Cust.)</th>
                <th class="text-end">Ss. Recaudo</th>
                <th class="text-end">Total a Pagar</th>
              </thead>
              <tbody>
                <tr>
                  <td colspan="9">
                    <div style="width:100px;height:100px;"></div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-sm-12 col-md-4">
            <table class="table table-striped" id="table-tarifario-producto">
              <thead>
                <tr>
                  <th>FRECUENCIA</th>
                  <th>MONTO</th>
                  <th>CUOTAS</th>
                  <th>TEM</th>
                  <th>PRODUCTO</th>
                </tr>
              </thead>
              <tbody>

              </tbody>

            </table>
          </div>
        </div>
        </div>
      </div>
    </div>
  </div>
</div>  

<script>
  @include('app.nuevosistema.select2',['input'=>'#idforma_pago_credito'])

  $('input[name="cargo_check"]').on('change', function () {
      let tipo = $('input[name="cargo_check"]:checked').val();
      if (tipo == 1) {
          $('#cargo').prop('disabled', true);
          showtasa();
      } else if (tipo == 2) {
          $('#cargo').prop('disabled', true);
          showtasa();
      } else if (tipo == 3) {
          $('#cargo').val(0.00);
          $('#cargo').prop('disabled', false);
      }
  });

  show_producto_credito()
  function show_producto_credito(){
    $.ajax({
      url:"{{url('backoffice/0/calculocompuesto/show_producto_credito')}}",
      type:'GET',
      data: {
        modalidad: 'Interes Compuesto'
      },
      success: function (res){

        let option_select = `<option></option>`;
        $.each(res, function( key, value ) {
          option_select += `<option value="${value.id}">${value.nombre}</option>`;
        });
        $('#idcredito_prendatario').html(option_select);
        sistema_select2({ input:'#idcredito_prendatario'});

      }
    })
  }
  
  function cronograma(){

    let monto       = parseFloat($('#monto_solicitado').val());
    let numerocuota = parseFloat($('#cuotas').val());
    let fechainicio = $('#fecha_desembolso').val();
    let frecuencia  = $('#idforma_pago_credito').val();
    let dia_gracia  = $('#dia_gracia').val();

    if (dia_gracia == '') {
        $('#dia_gracia').val(0);
        dia_gracia = 0;
    }

    let cargo       = $('#cargo').val();
    let cargomes  = $('#cargomes').val();

    let tasa        = $('#tasa_tem').val();
    let tipotasa    = 2;

    if(monto<=0){
        mensaje = 'Monto de Préstamo debe ser mayor a 0.00.';
        modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });
        return false;
    }
    if(numerocuota<=0){
        mensaje = 'El Número de Cuotas debe ser mayor a 0.';
        modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });
        return false;
    }

    if(dia_gracia<0){
        mensaje = 'El día de gracia debe ser mayor o igual a 0!!.';
        modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+mensaje, size: 'modal-sm' });
        return false;
    }


    if(monto=='' || numerocuota=='' || fechainicio=='' || frecuencia==''){
        return false;
    }

    $('#table-cronograma > tbody').html('<tr><td colspan="8"><div style="width:100px;height:100px;"></div></td></tr>');
    load('#table-cronograma > tbody tr td');
    let idproducto = $('#idcredito_prendatario').val();
    $.ajax({
        url:"{{url('backoffice/0/calculocompuesto/cronograma')}}",
        type: 'GET',
        data: {
            monto: monto,
            numerocuota: numerocuota,
            fechainicio: fechainicio,
            frecuencia: frecuencia,
            tasa: tasa,
            tipotasa: tipotasa,
            dia_gracia: dia_gracia,
            cargo: cargo,
            cargomes: cargomes,
            producto: idproducto

        },
        success: function (res) {
            if(res.resultado=='ERROR'){
                $('#table-cronograma > tbody').html('<tr><td colspan="8"><div style="width:100px;height:100px;"></div></td></tr>');
                //$('#tasa_tem_minima').val('0.00');
                //$('#tasa_tem').val('0.00');
                $('#tasa_tip').val('0.00');
                $('#tasa_tcem').val('0.00');
                $('#comision').val('0.00');
                modal({ route:"{{url('backoffice/'.$tienda->id.'/inicio/create?view=alerta')}}&mensaje="+res.mensaje, size: 'modal-sm' });
            }else{
                $('#table-cronograma > tbody').html(res.cronograma);
                $('#interes_total').html(res.interes_total);
                $('#total_cargo').html(res.total_cargo);
                $('#total_comision').html(res.total_comision);
                $('#total_pagar').html(res.total_pagar);

                $('#tasa_tem_minima').val(res.tasa_tem_minima);
                $('#tasa_tem').val(res.tasa_tem);
                $('#tasa_tip').val(res.tasa_tip);
                $('#tasa_tcem').val(res.tasa_tcem);
                $('#comision').val(res.cargootros);
            }
        }
    });
  }
  function calcula_tip(){
    let numerocuota = parseFloat($('#cuotas').val());
    let tasa_tem    = parseFloat($('#tasa_tem').val());
    let frecuencia  = $('#idforma_pago_credito').val();
    const frecuenciaDiasMap = {
      1: 30,
      2: 4,
      3: 2,
      4: 1
    };

    let dias = frecuenciaDiasMap[frecuencia] || 0;

    let tip = (tasa_tem / dias) * numerocuota;
    $('#tasa_tip').val(tip.toFixed(2))
  }
  function showtasa(){
    $('#tasa_tem_minima').val('');
    $('#tasa_tip').val('');
    $('#tasa_tcem').val('0.00');
    $('#comision').val('');
    
    let monto       = parseFloat($('#monto_solicitado').val());
    let numerocuota = parseFloat($('#cuotas').val());
    let frecuencia  = $('#idforma_pago_credito').val();
    let idproducto = $('#idcredito_prendatario').val();

    var comision_gestion_garantia_cargo = parseFloat({{ configuracion($tienda->id,'comision_gestion_garantia_cargo')['valor'] }});
    var cargocom = 0;
    var cargocom_mes = 0;

    if ($('#cont_mensaje_custodia').hasClass('d-none')) {
      comision_gestion_garantia_cargo = 0;
      cargocom = $('#cargo').val();
      cargocom_mes = $('#cargomes').val();
    } else {
      if(frecuencia==1){
          cargocom = ((comision_gestion_garantia_cargo/26)*numerocuota)/100;
          cargocom_mes = ((comision_gestion_garantia_cargo/26)*26)/100;
      }
      else if(frecuencia==2){
          cargocom = ((comision_gestion_garantia_cargo/4)*numerocuota)/100;
          cargocom_mes = ((comision_gestion_garantia_cargo/4)*4)/100;
      }
      else if(frecuencia==3){
          cargocom = ((comision_gestion_garantia_cargo/2)*numerocuota)/100;
          cargocom_mes = ((comision_gestion_garantia_cargo/2)*2)/100;
      }
      else if(frecuencia==4){
          cargocom = ((comision_gestion_garantia_cargo/1)*numerocuota)/100;
          cargocom_mes = ((comision_gestion_garantia_cargo/1)*1)/100;
      }
    }
    var cargo = cargocom*monto;
    var cargomes = cargocom_mes*monto;
    $('#cargo').val(cargo.toFixed(2));
    $('#cargomes').val(cargomes.toFixed(2));

    $.ajax({
      url:"{{url('backoffice/0/calculocompuesto/showtasa')}}",
      type:'GET',
      data: {
          producto: idproducto,
          monto: monto,
          numerocuota: numerocuota,
          frecuencia: frecuencia,
      },
      success: function (res){
        let { monto, cuotas, tem , cargos_otros } =  res;
        $('#monto_max_credito').val(monto);
        $('#coutas_max_credito').val(cuotas);
        $('#tasa_tem_minima').val(tem);
        $('#comision').val(cargos_otros);
      }
    })
  }
  function mostrar_tarifario_tasa(){
    showtasa();
    show_tarifario_producto();
  }
  function show_tarifario_producto(){
    let idproducto = $('#idcredito_prendatario').val();
    let idforma_pago_credito = $('#idforma_pago_credito').val();
    $.ajax({
      url:"{{url('backoffice/0/calculocompuesto/showtarifarioproducto')}}",
      type:'GET',
      data: {
          idproducto: idproducto,
          idforma_pago_credito: idforma_pago_credito,
      },
      success: function (res){
        $('#table-tarifario-producto > tbody').html(res.data);
        if(res.credito_prendatario){
          if (res.credito_prendatario.garantiaprendatario == "SI") {
            $('#cont_mensaje_custodia').removeClass('d-none');
            $('#cargo').prop('disabled', true);
          } else {
            $('#cont_mensaje_custodia').addClass('d-none');
            $('#cargo').prop('disabled', false);
          }
        }
      }
    })
  }

</script>   

