<div id="carga_reporteconsolidadoopecaja">
<form action="javascript:;" 
    id="form_reporteconsolidadoopecaja">
    <input type="hidden" id="idresponsable_registro">
    <input type="hidden" id="idresponsable_registro_idpermiso">
    <div class="modal-header">
        <h5 class="modal-title">Arqueo de Caja</h5>
        <button type="button" class="btn-close" id="modal-close-reporteconsolidadoopecaja-valid" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <input type="hidden" value="{{$consolidadooperaciones['validacion_operaciones_cuenta_banco']}}" id="validacion_arqueocaja">
        <input type="hidden" value="0.00" id="total_arqueocaja">
        <input type="hidden" value="{{$corte}}" id="corte_arqueocaja">
          <div class="row">
              <label for="fecha_inicio" class="col-sm-3 col-form-label">AGENCIA:</label>
              <div class="col-sm-5">
                  <select class="form-control" id="idagencia_arqueocaja" disabled>
                    <option></option>
                    @foreach($agencias as $value)
                        <option value="{{$value->id}}">{{$value->nombreagencia}}</option>
                    @endforeach
                  </select>
              </div>
          </div>
          <div class="row">
              <label for="fecha_inicio" class="col-sm-3 col-form-label">Fecha de Corte:</label>
              <div class="col-sm-4">
                  <input type="date" class="form-control" 
                         value="{{$corte}}" id="fecha_arqueocaja" disabled>
              </div>
              <div class="col-sm-3"></div>
              @if($arqueocaja)
              
              <label for="fecha_inicio" class="col-sm-3 col-form-label">Fecha y Hora de Registro:</label>
              <div class="col-sm-4">
                  <input type="text" class="form-control" 
                         value="{{ date_format(date_create($arqueocaja->fecharegistro),'d/m/Y H:i:s A') }}" disabled>
              </div>
              <div class="col-sm-3"></div>
              <label for="fecha_inicio" class="col-sm-3 col-form-label">Responsable:</label>
              <div class="col-sm-6">
                  <input type="text" class="form-control" 
                         value="{{$resposanble->nombrecompleto}}" disabled>
              </div>
              <div class="col-sm-3"></div>
              <label for="fecha_inicio" class="col-sm-3 col-form-label">Total S/.:</label>
              <div class="col-sm-3">
                  <input type="text" class="form-control" 
                         value="{{$arqueocaja->total}}" disabled>
              </div>
              @else
              <label for="fecha_inicio" class="col-sm-3 col-form-label">Saldo Contable en Caja (S/.):</label>
              <div class="col-sm-4">
                  <input type="text" class="form-control" 
                         value="{{ number_format(round($consolidadooperaciones['saldos_caja'], 1), 2, '.', '') }}" id="saldocaja_arqueocaja" disabled>
              </div>
              @endif
              @if($arqueocaja)
              <div class="col-sm-5" style="text-align: right;">
                  <?php 
                    $co_actual = consolidadooperaciones($tienda,$arqueocaja->idagencia,$arqueocaja->corte);
                    $date = Carbon\Carbon::createFromFormat('Y-m-d', $arqueocaja->corte);
                    $date->addDay(); // 1 day
                    $arqueocaja_diasiguiente = DB::table('arqueocaja')
                        ->where('idagencia',$arqueocaja->idagencia)
                        ->where('corte',$date->format('Y-m-d'))
                        ->first();
                    ?>
                    @if(!$arqueocaja_diasiguiente)
                    <button type="button" class="btn btn-danger" onclick="valid_eliminar_arqueocaja()">
                      <i class="fa fa-trash"></i>
                      ELIMINAR ARQUEO DE CAJA</button>
                    @endif
              </div>
              @endif
          </div>
          <div class="row" style="margin-top:10px;">
              <div class="col-sm-10">
              @if($arqueocaja)
                      <p class="text-center" 
                           style="background-color: #dc3545;
                                  padding: 15px;
                                  border-radius: 5px;
                                  color: #fff;
                                  width: 90%;
                                  margin: auto;">Ya esta arqueado la CAJA!!</p>
              @else
                @if($consolidadooperaciones['validacion_operaciones_cuenta_banco']=='VERIFICADO' ||
                  $consolidadooperaciones['validacion_operaciones_cuenta_banco']=='SIN OPERACIONES')
                  @if($consolidadooperaciones['saldos_operaciones_efectivo_validacion_recepcionado']==0)
                    <div style="float:left;width:48%;">
                      <table class="table table-bordered" id="table-lista-asignacioncapital">
                        <thead class="table-dark" style="position: sticky;top: 0;">
                          <tr>
                            <td colspan="3" style="text-align: center;">MONEDAS</td>
                          </tr>
                          <tr>
                            <td style="text-align: center;">Denominación</td>
                            <td style="text-align: center;">Cantidad</td>
                            <td style="text-align: center;">Total</td>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td style="text-align: right;">S/ 0.10</td>
                            <td style="width:33%;"><input type="text" class="form-control campo_moneda color_cajatexto" value="0" id="moneda_1" onclick="calculo_moneda()" onkeyup="calculo_moneda()"></td>
                            <td style="width:33%;text-align:right;">S/. <span id="moneda_1_total">0.00</span></td>
                          </tr>
                          <tr>
                            <td style="text-align: right;">S/ 0.20</td>
                            <td><input type="text" class="form-control campo_moneda color_cajatexto" value="0" id="moneda_2" onclick="calculo_moneda()" onkeyup="calculo_moneda()"></td>
                            <td style="text-align:right;">S/. <span id="moneda_2_total">0.00</span></td>
                          </tr>
                          <tr>
                            <td style="text-align: right;">S/ 0.50</td>
                            <td><input type="text" class="form-control campo_moneda color_cajatexto" value="0" id="moneda_3" onclick="calculo_moneda()" onkeyup="calculo_moneda()"></td>
                            <td style="text-align:right;">S/. <span id="moneda_3_total">0.00</span></td>
                          </tr>
                          <tr>
                            <td style="text-align: right;">S/ 1.00</td>
                            <td><input type="text" class="form-control campo_moneda color_cajatexto" value="0" id="moneda_4" onclick="calculo_moneda()" onkeyup="calculo_moneda()"></td>
                            <td style="text-align:right;">S/. <span id="moneda_4_total">0.00</span></td>
                          </tr>
                          <tr>
                            <td style="text-align: right;">S/ 2.00</td>
                            <td><input type="text" class="form-control campo_moneda color_cajatexto" value="0" id="moneda_5" onclick="calculo_moneda()" onkeyup="calculo_moneda()"></td>
                            <td style="text-align:right;">S/. <span id="moneda_5_total">0.00</span></td>
                          </tr>
                          <tr>
                            <td style="text-align: right;">S/ 5.00</td>
                            <td><input type="text" class="form-control campo_moneda color_cajatexto" value="0" id="moneda_6" onclick="calculo_moneda()" onkeyup="calculo_moneda()"></td>
                            <td style="text-align:right;">S/. <span id="moneda_6_total">0.00</span></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div style="float:left;width:4%;height:10px;">
                    </div>
                    <div style="float:left;width:48%;">
                      <table class="table table-bordered" id="table-lista-asignacioncapital">
                        <thead class="table-dark" style="position: sticky;top: 0;">
                          <tr>
                            <td colspan="3" style="text-align: center;">BILLETES</td>
                          </tr>
                          <tr>
                            <td style="text-align: center;">Denominación</td>
                            <td style="text-align: center;">Cantidad</td>
                            <td style="text-align: center;">Total</td>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td style="text-align: right;">S/ 10.00</td>
                            <td style="width:33%;"><input type="text" class="form-control campo_moneda color_cajatexto" value="0" id="moneda_7" onclick="calculo_moneda()" onkeyup="calculo_moneda()"></td>
                            <td style="width:33%;text-align:right;">S/. <span id="moneda_7_total">0.00</span></td>
                          </tr>
                          <tr>
                            <td style="text-align: right;">S/ 20.00</td>
                            <td><input type="text" class="form-control campo_moneda color_cajatexto" value="0" id="moneda_8" onclick="calculo_moneda()" onkeyup="calculo_moneda()"></td>
                            <td style="text-align:right;">S/. <span id="moneda_8_total">0.00</span></td>
                          </tr>
                          <tr>
                            <td style="text-align: right;">S/ 50.00</td>
                            <td><input type="text" class="form-control campo_moneda color_cajatexto" value="0" id="moneda_9" onclick="calculo_moneda()" onkeyup="calculo_moneda()"></td>
                            <td style="text-align:right;">S/. <span id="moneda_9_total">0.00</span></td>
                          </tr>
                          <tr>
                            <td style="text-align: right;">S/ 100.00</td>
                            <td><input type="text" class="form-control campo_moneda color_cajatexto" value="0" id="moneda_10" onclick="calculo_moneda()" onkeyup="calculo_moneda()"></td>
                            <td style="text-align:right;">S/. <span id="moneda_10_total">0.00</span></td>
                          </tr>
                          <tr>
                            <td style="text-align: right;">S/ 200.00</td>
                            <td><input type="text" class="form-control campo_moneda color_cajatexto" value="0" id="moneda_11" onclick="calculo_moneda()" onkeyup="calculo_moneda()"></td>
                            <td style="text-align:right;">S/. <span id="moneda_11_total">0.00</span></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  @else
                    @if($co_actual['saldos_operaciones_efectivo_validacion_existe'])
                      <p class="text-center" 
                        style="background-color: #dc3545;
                              padding: 15px;
                              border-radius: 5px;
                              color: #fff;
                              width: 90%;
                              margin: auto;">Para realizar el arqueo de caja, debe recepcionar el saldo de operaciones!!</p>
                    @endif
                  @endif
                @else
                  <p class="text-center" 
                    style="background-color: #dc3545;
                          padding: 15px;
                          border-radius: 5px;
                          color: #fff;
                          width: 90%;
                          margin: auto;">Para realizar el arqueo de caja, la validación debe estar VERIFICADO!!</p>
                @endif
              @endif
              </div>
              <div class="col-sm-2">
                    <button type="button" class="btn btn-warning" onclick="reporte_arqueocaja()">
                      REPORTE DE <br>ARQUEO</button>
                      <br><br>
                      <span style="color: #e64343">Excedente Efec. Físico Registrar en Ingr. Extraordinario Opción Remanente Cdre. para regularización</span>
              </div>
          </div>
          @if(!$arqueocaja)
          <div class="row" style="margin-top:10px;">
              <div class="col-sm-10">
                  <div class="row">
                      <label for="fecha_inicio" class="col-sm-2 col-form-label"></label>
                      <div class="col-sm-7">
                          I. Validación de Operaciones por Cuenta Banco: 
                          <img src="{{url('public/backoffice/sistema/icono_check.png')}}" width="20px" style="margin-top:-5px;">
                      </div>
                      <div class="col-sm-3">
                        @if ($consolidadooperaciones['validacion_operaciones_cuenta_banco']=='VERIFICADO')
                          <span style="background-color: #aaffa7;">
                            {{$consolidadooperaciones['validacion_operaciones_cuenta_banco']}}
                          </span>
                        @elseif($consolidadooperaciones['validacion_operaciones_cuenta_banco']=='PENDIENTE')
                          <span style="background-color: #ff8a8a;">
                            {{$consolidadooperaciones['validacion_operaciones_cuenta_banco']}}
                          </span>
                        @elseif($consolidadooperaciones['validacion_operaciones_cuenta_banco']=='SIN OPERACIONES')
                          <span style="background-color: #fce092;">
                            {{$consolidadooperaciones['validacion_operaciones_cuenta_banco']}}
                          </span>
                        @endif
                      </div>
                  </div>
                  <div class="row">
                      <label for="fecha_inicio" class="col-sm-2 col-form-label"></label>
                      <div class="col-sm-7">
                          II. Total de Efectivo Físico en Caja al Arqueo:
                      </div>
                      <div class="col-sm-3">
                          <input type="text" style="border: 1px solid #000;" id="total_efectivo_caja_arqueo" class="form-control" value="S/. 0.00" disabled>
                      </div>
                  </div>
              </div>
              <div class="col-sm-2">
                @if($consolidadooperaciones['validacion_operaciones_cuenta_banco']=='VERIFICADO' ||
                $consolidadooperaciones['validacion_operaciones_cuenta_banco']=='SIN OPERACIONES')
                    <button type="button" class="btn btn-success" onclick="valid_registro_arqueocaja()">
                      REGISTRAR</button>
                @endif
              </div>
          </div>
          @endif
    </div>
</form>  
</div>
<script>
    sistema_select2({ input:'#idagencia_arqueocaja',val:'{{$agencia->id}}' });

    function valid_registro_arqueocaja(){
        callback({
            route: '{{ url('backoffice/'.$tienda->id.'/cvreporteconsolidadoopecaja') }}',
            method: 'POST',
            form: '#form_reporteconsolidadoopecaja',
            carga: '#carga_reporteconsolidadoopecaja',
            data:{
                view: 'valid_registro_arqueocaja'
            }
        },
        function(resultado){
            removecarga({input:'#carga_reporteconsolidadoopecaja'});
            modal({ route:"{{url('backoffice/'.$tienda->id.'/cvreporteconsolidadoopecaja/0/edit?view=valid_registro_arqueocaja')}}",  size: 'modal-sm'  });
        })        
    }
  
    function submit_registro_arqueocaja(){
        callback({
            route: '{{ url('backoffice/'.$tienda->id.'/cvreporteconsolidadoopecaja') }}',
            method: 'POST',
            form: '#form_reporteconsolidadoopecaja',
            data:{
                view: 'submit_registro_arqueocaja'
            }
        },
        function(resultado){
            $('#modal-close-reporteconsolidadoopecaja-valid').click();
         
            verpdf();
        }) 
    }
  
    function valid_eliminar_arqueocaja(){
        let idagencia = $('#idagencia_arqueocaja').val();
        let corte = $('#fecha_arqueocaja').val();
        let url = "{{ url('backoffice/'.$tienda->id) }}/cvreporteconsolidadoopecaja/0/edit?view=valid_eliminar_arqueocaja&idagencia="+idagencia+"&corte="+corte;
        modal({ route: url,  size: 'modal-sm' })
    }
  
    function reporte_arqueocaja(){
        let idagencia = $('#idagencia').val();
        let url = "{{ url('backoffice/'.$tienda->id) }}/cvreporteconsolidadoopecaja/0/edit?view=reporte_arqueocaja&idagencia="+idagencia;
        modal({ route: url })
    }
  
    function calculo_moneda(){
      var moneda_1 = parseInt($('#moneda_1').val())*0.10;
      var moneda_2 = parseInt($('#moneda_2').val())*0.20;
      var moneda_3 = parseInt($('#moneda_3').val())*0.50;
      var moneda_4 = parseInt($('#moneda_4').val())*1.00;
      var moneda_5 = parseInt($('#moneda_5').val())*2.00;
      var moneda_6 = parseInt($('#moneda_6').val())*5.00;
      var moneda_7 = parseInt($('#moneda_7').val())*10.00;
      var moneda_8 = parseInt($('#moneda_8').val())*20.00;
      var moneda_9 = parseInt($('#moneda_9').val())*50.00;
      var moneda_10 = parseInt($('#moneda_10').val())*100.00;
      var moneda_11 = parseInt($('#moneda_11').val())*200.00;
      var total_efectivo_caja_arqueo = moneda_1+
                                        moneda_2+
                                        moneda_3+
                                        moneda_4+
                                        moneda_5+
                                        moneda_6+
                                        moneda_7+
                                        moneda_8+
                                        moneda_9+
                                        moneda_10+
                                        moneda_11;
      $('#moneda_1_total').html(moneda_1.toFixed(2));
      $('#moneda_2_total').html(moneda_2.toFixed(2));
      $('#moneda_3_total').html(moneda_3.toFixed(2));
      $('#moneda_4_total').html(moneda_4.toFixed(2));
      $('#moneda_5_total').html(moneda_5.toFixed(2));
      $('#moneda_6_total').html(moneda_6.toFixed(2));
      $('#moneda_7_total').html(moneda_7.toFixed(2));
      $('#moneda_8_total').html(moneda_8.toFixed(2));
      $('#moneda_9_total').html(moneda_9.toFixed(2));
      $('#moneda_10_total').html(moneda_10.toFixed(2));
      $('#moneda_11_total').html(moneda_11.toFixed(2));
      $('#total_efectivo_caja_arqueo').val('S/. '+total_efectivo_caja_arqueo.toFixed(2));
      $('#total_arqueocaja').val(total_efectivo_caja_arqueo.toFixed(2));
    }
</script> 