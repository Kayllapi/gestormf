<div class="modal-header">
    <h5 class="modal-title">
      Simulador de Crédito (Cálculo Compuesto)
    </h5>
    <button type="button" class="btn-close" onclick="ir_inicio()" style="font-size: 20px;"></button>
</div>
<div class="modal-body">
  <div class="row justify-content-center">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-body p-2" id="form-garantias-result">
           <div class="row">
            <div class="col-md-12 mt-3">
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
                    <label class="col-sm-5 col-form-label" style="text-align: right;">Monto de Prestamo</label>
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
                </div>
                <div class="col-md-6">
                  <div class="row">
                    <label class="col-sm-5 col-form-label" style="text-align: right;">Dia de Gracia:</label>
                    <div class="col-sm-7">
                      <input type="number" step="any" class="form-control" id="dia_gracia" value="0">
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-5 col-form-label" style="text-align: right;">Fecha Desembolso:</label>
                    <div class="col-sm-7">
                      <input type="date" class="form-control" id="fecha_desembolso" value="{{ date('Y-m-d') }}">
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-5 col-form-label" style="text-align: right;">Servicios/Otros (%):</label>
                    <div class="col-sm-7">
                      <input type="number" step="any" class="form-control" id="comision" value="" disabled>
                    </div>
                    <div class="col-sm-4 d-none">
                      <input type="number" step="any" class="form-control" id="monto_cargos_otros" value="0.00" disabled>
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-5 col-form-label" style="text-align: right;">Cargo:</label>
                    <div class="col-sm-7">
                      <input type="number" step="any" class="form-control" id="cargo" value="0.00">
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-5 col-form-label" style="text-align: right;"></label>
                    <div class="col-sm-7 mt-3">
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-5 col-form-label" style="text-align: right;"></label>
                    <div class="col-sm-7 mt-3">
                      <button type="button" class="btn btn-primary" id="generar_cronograma" onclick="cronograma()">CALCULAR</button>
                    </div>
                  </div>
                </div>
                </div>
            </div>
            
        </div>

        <div class="row mt-3" style="background-color: #198754;">
          <div class="col-sm-12 col-md-3">
            
          </div>
          
          <div class="col-sm-12 col-md-9">
            <div class="row">
              <label class="col-sm-2 col-form-label text-white" style="text-align: right;"><b>Interes Total (S/):</b></label>
              <div class="col-sm-2 col-form-label text-white" id="interes_total">
                0.00
              </div>
              <label class="col-sm-2 col-form-label text-white" style="text-align: right;"><b>Comisión de Ss. (S/):</b></label>
              <div class="col-sm-2 col-form-label text-white" id="cargo_total">
                0.00
              </div>
              <label class="col-sm-2 col-form-label text-white" style="text-align: right;"><b>Total a Pagar (S/):</b></label>
              <div class="col-sm-2 col-form-label text-white" id="total_pagar">
                0.00
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-3 justify-content-center">
          <div class="col-sm-12 col-md-7">
            <table class="table table-striped" id="table-cronograma">
              <thead>
                <th>Cuota N°</th>
                <th>Fecha de Pago</th>
                <th>Capital</th>
                <th>Amortización</th>
                <th>Interes</th>
                <th>Comisión de Ss.</th>
                <th>Cargo</th>
                <th>Cuota</th>
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
          <div class="col-sm-12 col-md-5">
            <table class="table" id="table-tarifario-producto">
              <thead class="text-white">
                <tr>
                  <td>FRECUENCIA</td>
                  <td>MONTO</td>
                  <td>CUOTAS</td>
                  <td>TEM</td>
                  <td>PRODUCTO</td>
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

    let cargo       = $('#cargo').val();

    let tasa        = $('#tasa_tem').val();
    let tipotasa    = 2;

    if(monto<=0){
        alert("Monto de Prestamo debe ser mayor a 0.00.");
        return false;
    }
    if(numerocuota<=0){
        alert("El Número de Cuotas debe ser mayor a 0.");
        return false;
    }

    if(dia_gracia<0){
        alert("El día de gracia debe ser mayor o igual a 0!!.");
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
            producto: idproducto

        },
        success: function (res) {
            if(res.resultado=='ERROR'){
                $('#table-cronograma > tbody').html('<tr><td colspan="8"><div style="width:100px;height:100px;"></div></td></tr>');
                //$('#tasa_tem_minima').val('0.00');
                //$('#tasa_tem').val('0.00');
                $('#tasa_tip').val('0.00');
                $('#comision').val('0.00');
                alert(res.mensaje);
            }else{
                $('#table-cronograma > tbody').html(res.cronograma);
                $('#interes_total').html(res.interes_total);
                $('#cargo_total').html(res.cargo_total);
                $('#total_pagar').html(res.total_pagar);

                $('#tasa_tem_minima').val(res.tasa_tem_minima);
                $('#tasa_tem').val(res.tasa_tem);
                $('#tasa_tip').val(res.tasa_tip);
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
    let monto       = parseFloat($('#monto_solicitado').val());
    let numerocuota = parseFloat($('#cuotas').val());
    let frecuencia  = $('#idforma_pago_credito').val();
    let idproducto = $('#idcredito_prendatario').val();
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
        $('#table-tarifario-producto > tbody').html(res);
      }
    })
  }

</script>   

