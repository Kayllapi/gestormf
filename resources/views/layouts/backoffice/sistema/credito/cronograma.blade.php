<form action="javascript:;" 
      onsubmit="callback({
          route: '{{ url('backoffice/'.$tienda->id.'/credito/'.$credito->id) }}',
          method: 'PUT',
          data:{
              view: 'credito_garantia',
              interes_total: $('#interes_total').html(),
              total_pagar: $('#total_pagar').html(),
              tipotasa : '{{$credito->modalidad_calculo}}' == 'Interes Simple' ? 1 : 2,
              validdiasdegracia : '{{$diasdegracia}}',
          }
      },
      function(resultado){
        lista_credito();
        load_nuevo_credito();
      },this)"> 
    <div class="modal-header" style="border-bottom: 0;">
        <h5 class="modal-title">CRONOGRAMA </h5>
        <button type="button" class="btn-close text-white" id="modal-close-garantia-cliente" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
      <div class="row" style="background-color: #198754;
    color: #fffdfd;
    font-size: 14px;padding: 7px;">
          <div class="col-md-4"><b>CLIENTE:</b> {{ $usuario->nombrecompleto }}</div>
          <div class="col-md-4">@if($credito->idaval != 0) <b>AVAL:</b> {{ $credito->nombreavalcredito }} @endif</div>
          <div class="col-md-4"><b>PRODUCTO:</b> {{ $credito->nombreproductocredito }}</div>
      </div>
    <div class="modal-body">
      <div class="row">
          <div class="col-md-7 mt-3">
              <div class="row">
              <div class="col-md-6">
                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;">Monto de Prestamo</label>
                  <div class="col-sm-7">
                    <input type="number" step="any" class="form-control" {{ $view_detalle=='false' ? 'disabled' : ($credito->idestadorefinanciamiento==1?'disabled':'') }} id="monto_solicitado" value="{{ $credito->monto_solicitado }}" onclick="showtasa()" onkeyup="showtasa()">
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;">Frecuencia de Pago:</label>
                  <div class="col-sm-7">
                    <select class="form-control" {{ $view_detalle=='false' ? 'disabled' : '' }} id="idforma_pago_credito" onchange="showtasa()">
                      <option></option>
                      @foreach($forma_pago_credito as $value)
                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;">Número de Cuotas:</label>
                  <div class="col-sm-7">
                    
                    <input type="number" step="any" class="form-control" {{ $view_detalle=='false' ? 'disabled' : '' }} id="cuotas" value="{{ $credito->cuotas }}" onclick="showtasa()" onkeyup="showtasa()">
                    <input type="hidden" step="any" class="form-control" {{ $view_detalle=='false' ? 'disabled' : '' }} id="coutas_max_credito" value="0">
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;">TEM (%):</label>
                  <div class="col-sm-4">
                    <input type="text" step="any" class="form-control" {{ $view_detalle=='false' ? 'disabled' : '' }} id="tasa_tem" value="{{ $credito->tasa_tem }}">
                  </div>
                  <div class="col-sm-3">
                    <input type="number" step="any" class="form-control" {{ $view_detalle=='false' ? 'disabled' : '' }} id="tasa_tem_minima" value="0" disabled>
                  </div>
                </div>
                <div style="{{ $credito->modalidad_calculo == 'Interes Compuesto' ? 'display:none;' : '' }}">
                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;">TIP (%):</label>
                  <div class="col-sm-7">
                    <input type="number" step="any" class="form-control" {{ $view_detalle=='false' ? 'disabled' : '' }} id="tasa_tip" value="0.00" disabled>
                  </div>
                </div>  
                </div>
                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;">TCEM (%):</label>
                  <div class="col-sm-7">
                    <input type="number" step="any" class="form-control" {{ $view_detalle=='false' ? 'disabled' : '' }} id="tasa_tcem" value="0.00" disabled>
                  </div>
                </div> 
              </div>
              <div class="col-md-6">
                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;">Dia de Gracia:</label>
                  <div class="col-sm-7">
                    <input type="number" step="any" class="form-control" {{ $view_detalle=='false' ? 'disabled' : '' }} id="dia_gracia" value="{{ $credito->dia_gracia }}">
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;">Fecha de Cálculo:</label>
                  <div class="col-sm-7">
                    <input type="date" class="form-control" {{ $view_detalle=='false' ? 'disabled' : '' }} id="fecha_desembolso" 
                           value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" disabled>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;">C. Ss./Otros (%):</label>
                  <div class="col-sm-7">
                    <input type="number" step="any" class="form-control" id="comision" value="0." disabled>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;">Cargo S/.:</label>
                  <div class="col-sm-7">
                    <input type="number" step="any" class="form-control" {{ $view_detalle=='false' ? 'disabled' : '' }} id="cargo" value="{{ $credito->cargo }}">
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;"></label>
                  <div class="col-sm-7 mt-3">
                  </div>
                </div>
                @if($view_detalle!='false')
                <div class="row">
                  <label class="col-sm-5 col-form-label" style="text-align: right;"></label>
                  <div class="col-sm-7 mt-3">
                    <button type="button" class="btn btn-primary" id="generar_cronograma" onclick="cronograma()">CALCULAR</button>
                  </div>
                </div>
                @endif
              </div>
              </div>
          </div>
          <div class="col-md-5">
          <h6>LISTA DE GARANTIAS</h6>
          <div class="table-responsive">
            <table class="table table-striped" id="table-garantia-cliente">
              <thead>
                <tr>
                  <th>Descripción</th>
                  <th>V. Mercado</th>
                  <th>V. Comercial</th>
                  <th>Cobertura</th>
                  <th>Garantia</th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
          </div>
      </div>
      <div class="row d-none">
        <div class="col-sm-12">
          <table class="table">
              <tr>
                <td>MONTO</td>
                <td>CUOTAS</td>
                <td>TEM</td>
              </tr>
            @foreach($tarifario_producto as $value)
              <tr>
                <td>{{ $value->monto }}</td>
                <td>{{ $value->cuotas }}</td>
                <td>{{ $value->tem }}</td>
              </tr>
            @endforeach
          </table>
        </div>
      </div>
      
      <div class="row mt-3" style="background-color: #198754;">
        <div class="col-sm-12 col-md-3">
          <div class="row">
            <input type="hidden" id="monto_cobertura_garantia" value="{{ $credito->monto_cobertura_garantia }}">
            @if($credito->idforma_credito==1)
            <label class="col-sm-12 col-form-label" ><span class="bg-light text-dark" style="padding: 4px;
    border-radius: 4px;"><b>COBERTURA GARANTIA:</b> {{ $credito->monto_cobertura_garantia }} SOLES</span></label>
            @endif
          </div>
        </div>
        <div class="col-sm-12 col-md-3 d-none">
          <div class="row">
            <label class="col-sm-12 col-form-label text-white text-center"><b>CRONOGRAMA DE PAGOS</b></label>
          </div>
          
        </div>
        <div class="col-sm-12 col-md-9">
          <div class="row">
            <label class="col-sm-2 col-form-label text-white" style="text-align: right;"><b>Interes Total (S/):</b></label>
            <div class="col-sm-2 col-form-label text-white" id="interes_total">
              {{ $credito->interes_total }}
            </div>
            <label class="col-sm-2 col-form-label text-white" style="text-align: right;"><b>Cargos y otros (S/):</b></label>
            <div class="col-sm-2 col-form-label text-white" id="cargo_total">
              0.00
            </div>
            <label class="col-sm-2 col-form-label text-white" style="text-align: right;"><b>Total a Pagar (S/):</b></label>
            <div class="col-sm-2 col-form-label text-white" id="total_pagar">
              {{ $credito->total_pagar }}
            </div>
          </div>
        </div>
      </div>
      <script>
        
        function cronograma(){
          
          let monto       = parseFloat($('#monto_solicitado').val());
          let numerocuota = parseFloat($('#cuotas').val());
          let fechainicio = $('#fecha_desembolso').val();
          let frecuencia  = $('#idforma_pago_credito').val();
          let dia_gracia  = $('#dia_gracia').val();
          
          let cargo       = $('#cargo').val();
          
          let tasa        = $('#tasa_tem').val();
          let tipotasa    = "{{$credito->modalidad_calculo}}" == 'Interes Simple' ? 1 : 2;
          
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
          if(dia_gracia > {{$diasdegracia}}){
              alert("Máximo puede poner {{$diasdegracia}} días de gracia!!.");
              return false;
          }

          if(monto=='' || numerocuota=='' || fechainicio=='' || frecuencia==''){
              return false;
          }
          
          $('#table-cronograma > tbody').html('<tr><td colspan="8"><div style="width:100px;height:100px;"></div></td></tr>');
          load('#table-cronograma > tbody tr td');

          $.ajax({
              url:"{{url('backoffice/0/credito/cronograma')}}",
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
                  idcredito: '{{ $credito->id }}'
              },
              success: function (res) {
                  if(res.resultado=='ERROR'){
                      $('#table-cronograma > tbody').html('<tr><td colspan="8"><div style="width:100px;height:100px;"></div></td></tr>');
                      //$('#tasa_tem_minima').val('0.00');
                      //$('#tasa_tem').val('0.00');
                      $('#tasa_tip').val('0.00');
                      $('#tasa_tcem').val('0.00');
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
                      $('#tasa_tcem').val(res.tasa_tcem);
                      $('#comision').val(res.cargootros);
                  }
              }
          });
        }

      </script>
      <div class="row mt-3 justify-content-center">
        <div class="col-sm-12 col-md-10">
          <table class="table table-striped" id="table-cronograma">
            <thead>
              <th>Cuota N°</th>
              <th>Fecha de Pago</th>
              <th>Capital</th>
              <th>Amortización</th>
              <th>Interes</th>
              <th>C. Ss./Otros (%)</th>
              <th>Cargo</th>
              <th>Cuota</th>
            </thead>
            <tbody>
              
              <tr>
                <td colspan="8">
                  <div style="width:100px;height:100px;"></div>
                </td>
              </tr>
            </tbody>
          </table>
          @if($view_detalle!='false')
          <br>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> GUARDAR CRONOGRAMA</button>
          @endif
        </div>
      </div>
      

      <div class="row mt-1 d-none">
        <div class="col-sm-12">
          <button type="button" onclick="modal_credito('fuente_ingreso')" class="btn btn-warning"><i class="fa-solid fa-money-bill-trend-up"></i> Fuentes de Ingreso</button>
          <button type="button" onclick="modal_credito('datos_cliente')" class="btn btn-warning"><i class="fa-solid fa-user-tie"></i> Datos de Cliente</button>
          <button type="button" onclick="modal_credito('garantia_cliente')" class="btn btn-warning"><i class="fa-solid fa-building-user"></i> Garantias de Cliente</button>
          <button type="button" onclick="modal_credito('garantia_aval')" class="btn btn-warning"><i class="fa-solid fa-building-user"></i> Garantias de Aval</button>
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
        </div>
      </div>
    </div>
</form>  
<script>
  
  
    setTimeout(function() {
      cronograma();
    }, 1000);
  
  $('#tasa_tem').inputmask("decimal", {
    digits  : 2,
    placeholder : "0.00",
    allowMinus : false,
    allowPlus : false,
    max : 9999999999999991,
    digitsOptional : false
  });
  $('#tasa_tem').on('blur', function() {
    valida_tem()
  });
  $('#tasa_tem').on('keydown', function() {
    setTimeout(function() {
      valida_tem();
    }, 1000);
  });
  function valida_tem(){
    let minimo = parseFloat($('#tasa_tem_minima').val());
    if ($('#tasa_tem').val() === "" || parseFloat($('#tasa_tem').val()) < minimo) {
      $('#tasa_tem').val(minimo.toFixed(2));
    }
  }
  @include('app.nuevosistema.select2',['input'=>'#idforma_pago_credito', 'val' => $credito->idforma_pago_credito ])
  function modal_credito(vista){
    modal({ route:"{{url('backoffice/'.$tienda->id.'/credito/'.$credito->id.'/edit?view=')}}"+vista+'&detalle=false' });  
  }

  carga_garantia_table();
  function carga_garantia_table(){
    $.ajax({
      url:"{{url('backoffice/0/credito/showgarantiacliente')}}",
      type:'GET',
      data: {
          idcredito : '{{ $credito->id }}',
      },
      success: function (res){
        $('#table-garantia-cliente > tbody').html(res);
        plugins_popover();
      }
    })
  }
  function showtasa(){
    
    //$('#tasa_tem').val('');
    $('#tasa_tem_minima').val('');
    $('#tasa_tip').val('');
    $('#comision').val('');
    
    let monto       = $('#monto_solicitado').val();
    let numerocuota = $('#cuotas').val();
    let frecuencia  = $('#idforma_pago_credito').val();
    if(monto==''){
        return false;
    }
    if(numerocuota==''){
        return false;
    }
    $.ajax({
      url:"{{url('backoffice/0/credito/showtasa')}}",
      type:'GET',
      data: {
          monto: monto,
          numerocuota: numerocuota,
          tasa: $('#tasa_tem').val(),
          frecuencia: frecuencia,
          idcredito: '{{ $credito->id }}'
      },
      success: function (res){
        $('#tasa_tem_minima').val(res.tasa_tem_minima);
        $('#tasa_tip').val(res.tasa_tip);
        $('#comision').val(res.cargootros);
      }
    })
  }
  
</script>    

