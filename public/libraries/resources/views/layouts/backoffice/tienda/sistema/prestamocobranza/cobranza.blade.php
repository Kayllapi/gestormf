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
                          <input type="number" id="montocompleto" min="0" step="0.01" disabled>
                          </select>
                          </div>
                          <label>Total Cuotas</label>
                          <input type="text" id="cuotas_total_cuota" value="0.00" disabled> 
                          @if(configuracion($tienda->id,'prestamo_estadodescuentointeres')['valor']==1)
                          <div id="cont-descontarinteres" style="display:none;">
                          <label>Descuento de Interes</label>
                          <input type="text" id="cuotas_total_interes" value="0.00" style="background-color: #93f2bb;border-color: #08911a;" disabled>
                          </div>
                          @endif
                    </div>
                    <div class="col-sm-4">
                          <label>Total de Moras</label>
                          <input type="text" id="cuotas_total_mora" value="0.00" disabled>
                          <div id="cont-descontarmora" style="display:none;">
                          <label>Descontar Mora</label>
                          <div class="onoffswitch" style="margin-bottom: 10px;margin-top: 2px;float: left;">
                              <input type="checkbox" class="onoffswitch-checkbox check_moradescuento" id="check_moradescuento">
                              <label class="onoffswitch-label" for="check_moradescuento">
                                  <span class="onoffswitch-inner"></span>
                                  <span class="onoffswitch-switch"></span>
                              </label> 
                          </div>
                          </div>
                          <div id="cont-moradescuento" style="display: none;">
                   
                                      <label>Mora a Descontar *</label>
                                      <input type="number" id="moradescuento" placeholder="0.00" min="0" step="0.01" disabled>
               
                                      <label>Mora a Pagar</label>
                                      <input type="number" id="total_moraapagar" value="0.00" min="0" step="0.01" disabled>
                             
                          </div>
                          <div id="cont-morapendiente" style="display: none;">
                          <label>Mora Pendiente</label>
                          <input type="text" id="cuotas_total_mora_pendiente" value="0.00" style="background-color: #ffb0b0;border-color: #ff1f44;" disabled>
                          </div>
                    </div>
                    <div class="col-sm-4">
                          <label>Total</label>
                          <input type="text" id="cuotas_cuotapago" value="0.00" disabled>
                          <div id="cont-acuentaanterior" style="display: none;"> 
                          <label>A Cuenta (Anterior)</label>
                          <input type="text" id="acuentaanterior" value="0.00" disabled>
                          </div>
                          <label>Pago</label>
                          <input type="text" id="cuotas_total" value="0.00" disabled>
                          <div id="cont-redondeado" style="display: none;">
                          <label>Redondeado</label>
                          <input type="text" id="cuotas_totalredondeado" value="0.00" disabled>
                          </div>
                          <div id="cont-abono" style="display: none;">
                          <label>Abono</label>
                          <input type="text" id="cuotas_totalabono" value="0.00" disabled>
                          </div>
                    </div>
                </div>
                  <table class="table" id="tabla-formapago" style="margin-bottom: 3px;">
                    <thead>
                      <tr style="background: #31353d; color: #fff;">
                        <th style="padding: 8px;">Depósito</th>
                        <th width="10px"><a href="javascript:;" onclick="agregar_formapago()" class="btn btn-warning"><i class="fa fa-plus"></i> Agregar</a></th>
                      </tr>
                    </thead>
                    <tbody num="0" id="tbody-formapago">
                    </tbody>
                  </table>
                  <div class="row">
                    <div class="col-sm-6">
                        <div style="background-color: #e0b609;padding: 10px;border-radius: 5px;font-size: 20px;font-weight: bold;text-align: center;color: #fff;margin-bottom: 5px;">
                          Depósito: <span id="totalmonto_deposito">0.00</span> 
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div style="background-color: #11c529;padding: 10px;border-radius: 5px;font-size: 20px;font-weight: bold;text-align: center;color: #fff;margin-bottom: 5px;">
                          Efectivo: <span id="totalmonto_efectivo">0.00</span>
                        </div>
                    </div>
                  </div>
                  <div id="cont-montorecibido" style="display:none;">
                      <label>Monto recibido en efectivo *</label>
                      <input type="number" id="montorecibido" placeholder="0.00" min="0" step="0.01" onkeyup="calcular_vuelto_cuota()" disabled>
                  </div>
                  <div id="cont-vuelto" style="display:none;">
                      <label>Vuelto</label>
                      <input type="number" id="vuelto" value="0.00" min="0" step="0.01" disabled>
                  </div>
                  <div id="cont-acuentaproxima" style="display:none;">
                  <label>A Cuenta (Próxima Cuota)</label>
                  <input type="number" id="acuenta" value="0.00" min="0" step="0.01" disabled>
                  </div>
                  <div id="cont-vueltopagocompleto" style="display:none;">
                      <label>Vuelto</label>
                      <input type="number" id="vueltopagocompleto" value="0.00" min="0" step="0.01" disabled>
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
    <div id="cont-morapendiente-resultado" style="display:none;">
      <div class="mensaje-danger">
            Hay Moras pendientes sin sustentar de <span id="cuotas_total_mora_pendiente_span"></span>, se esta agregando al Pago Total.
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
                          <li><a href="#tab-resumencredito-1" id="tab-actual">Deuda Pagada</a></li>
                          <li><a href="#tab-resumencredito-2" id="tab-restante">Deuda Vencida</a></li>
                          <li><a href="#tab-resumencredito-3" id="tab-pagado">Deuda Restante</a></li>
                          <li><a href="#tab-resumencredito-4" id="tab-pagado">Deuda Pendiente</a></li>
                      </ul>
                      <div class="tab">
                          <div id="tab-resumencredito-0" class="tab-content" style="display: block;">
                              <div class="row">
                                  <div class="col-md-6">
                                      <label>Frecuencia </label>
                                      <input type="text" id="resumen-desembolso-frecuencia" disabled>
                                      <label>Monto Desembolsado </label>
                                      <input type="text" id="resumen-desembolso-monto" value="0.00" disabled>
                                      <label>Interes </label>
                                      <input type="text" id="resumen-desembolso-interes" value="0.00" disabled>
                                      @if($cronograma['creditosolicitud']->idestadogastoadministrativo==2)
                                      <label>Gasto Administrativo </label>
                                      <input type="text" id="resumen-desembolso-gastoadministrativo" value="0.00" disabled>
                                      @endif
                                      <label>Total a Pagar</label>
                                      <input type="text" id="resumen-desembolso-montototal" value="0.00" disabled>
                                  </div>
                                  <div class="col-md-6">
                                      <label>Fecha de Inicio </label>
                                      <input type="text" id="resumen-desembolso-fechainicio" disabled>
                                      <label>Ultima Fecha </label>
                                      <input type="text" id="resumen-desembolso-ultimafecha" disabled>
                                      <label>Cuota Fija</label>
                                      <input type="text" id="resumen-desembolso-cuotafija" disabled>
                                      <label>Atraso Vencido</label>
                                      <input type="text" id="resumen-desembolso-atraso" disabled>
                                  </div>
                              </div>
                          </div>
                          <div id="tab-resumencredito-1" class="tab-content" style="display none;">
                              <div class="row">
                                  <div class="col-md-6">
                                      <label>Total Atrasos</label>
                                      <input type="text" id="resumen-cancelada-atraso" value="0.00" disabled>
                                      <label>Total Cuotas</label>
                                      <input type="text" id="resumen-cancelada-cuota" value="0.00" disabled>
                                      <label>Total Moras</label>
                                      <input type="text" id="resumen-cancelada-mora" value="0.00" disabled>
                                  </div>
                                  <div class="col-md-6">
                                      <label>Total Moras Descontados</label>
                                      <input type="text" id="resumen-cancelada-moradescontado" value="0.00" disabled>
                                      <label>Total Moras a Pagar</label>
                                      <input type="text" id="resumen-cancelada-moraapagar" value="0.00" disabled>
                                      <label>Total Acuenta (Próxima Cuota)</label>
                                      <input type="text" id="resumen-cancelada-acuenta" value="0.00" disabled>
                                  </div>
                                  <label style="text-align: center;">Total Pagado</label>
                                  <input type="text" id="resumen-cancelada-total" value="0.00" style="text-align: center;font-size: 15px;font-weight: bold;" disabled>
                              </div>
                          </div>
                        
                          <div id="tab-resumencredito-2" class="tab-content" style="display none;">
                              <div class="row">
                                  <div class="col-md-6">
                                      <label>Total Atrasos</label>
                                      <input type="text" id="resumen-vencida-atraso" value="0.00" disabled>
                                      <label>Total Cuotas</label>
                                      <input type="text" id="resumen-vencida-cuota" value="0.00" disabled>
                                      <label>Total Moras</label>
                                      <input type="text" id="resumen-vencida-mora" value="0.00" disabled>
                                  </div>
                                  <div class="col-md-6">
                                      <label>Total Moras Descontados</label>
                                      <input type="text" id="resumen-vencida-moradescontado" value="0.00" disabled>
                                      <label>Total Moras a Pagar</label>
                                      <input type="text" id="resumen-vencida-moraapagar" value="0.00" disabled>
                                      <label>Total Acuenta (Próxima Cuota)</label>
                                      <input type="text" id="resumen-vencida-acuenta" value="0.00" disabled>
                                  </div>
                                  <label style="text-align: center;">Total Pagado</label>
                                  <input type="text" id="resumen-vencida-total" value="0.00" style="text-align: center;font-size: 15px;font-weight: bold;" disabled>
                              </div>
                          </div>
                          <div id="tab-resumencredito-3" class="tab-content" style="display none;">
                              <div class="row">
                                  <div class="col-md-6">
                                      <label>Total Atrasos</label>
                                      <input type="text" id="resumen-restante-atraso" value="0.00" disabled>
                                      <label>Total Cuotas</label>
                                      <input type="text" id="resumen-restante-cuota" value="0.00" disabled>
                                      <label>Total Moras</label>
                                      <input type="text" id="resumen-restante-mora" value="0.00" disabled>
                                  </div>
                                  <div class="col-md-6">
                                      <label>Total Moras Descontados</label>
                                      <input type="text" id="resumen-restante-moradescontado" value="0.00" disabled>
                                      <label>Total Moras a Pagar</label>
                                      <input type="text" id="resumen-restante-moraapagar" value="0.00" disabled>
                                      <label>Total Acuenta (Próxima Cuota)</label>
                                      <input type="text" id="resumen-restante-acuenta" value="0.00" disabled>
                                  </div>
                                  <label style="text-align: center;">Total Pagado</label>
                                  <input type="text" id="resumen-restante-total" value="0.00" style="text-align: center;font-size: 15px;font-weight: bold;" disabled>
                              </div>
                          </div>
                          <div id="tab-resumencredito-4" class="tab-content" style="display none;">
                              <div class="row">
                                  <div class="col-md-6">
                                      <label>Total Atrasos</label>
                                      <input type="text" id="resumen-pendiente-atraso" value="0.00" disabled>
                                      <label>Total Cuotas</label>
                                      <input type="text" id="resumen-pendiente-cuota" value="0.00" disabled>
                                      <label>Total Moras</label>
                                      <input type="text" id="resumen-pendiente-mora" value="0.00" disabled>
                                  </div>
                                  <div class="col-md-6">
                                      <label>Total Moras Descontados</label>
                                      <input type="text" id="resumen-pendiente-moradescontado" value="0.00" disabled>
                                      <label>Total Moras a Pagar</label>
                                      <input type="text" id="resumen-pendiente-moraapagar" value="0.00" disabled>
                                      <label>Total Acuenta (Próxima Cuota)</label>
                                      <input type="text" id="resumen-pendiente-acuenta" value="0.00" disabled>
                                  </div>
                                  <label style="text-align: center;">Total Pagado</label>
                                  <input type="text" id="resumen-pendiente-total" value="0.00" style="text-align: center;font-size: 15px;font-weight: bold;" disabled>
                              </div>
                          </div>
                      </div>
                  </div>
            </div>
        </div>
    </div>
</div>
<style>
  .table .error-input {
    margin-top:0px !important;
  }
</style>
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
  

    @if(configuracion($tienda->id,'facturacion_empresapordefecto')['resultado']=='CORRECTO')
        $("#facturacion_idagencia").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val({{ configuracion($tienda->id,'facturacion_empresapordefecto')['valor'] }}).trigger("change");    
    @else
        $("#facturacion_idagencia").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @endif

    @if(configuracion($tienda->id,'facturacion_monedapordefecto')['resultado']=='CORRECTO')
        $("#facturacion_idmoneda").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val({{ configuracion($tienda->id,'facturacion_monedapordefecto')['valor'] }}).trigger("change");
    @else
        $("#facturacion_idmoneda").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @endif

    @if(configuracion($tienda->id,'facturacion_comprobantepordefecto')['resultado']=='CORRECTO')
        $("#facturacion_idtipocomprobante").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val({{ configuracion($tienda->id,'facturacion_comprobantepordefecto')['valor'] }}).trigger("change");   
    @else
        $("#facturacion_idtipocomprobante").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @endif
  
    $('#idtipopago').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1
    }).on("change", function(e) {
        $('#cont-descontarmora').css('display', 'none');
        $('#cont-descontarinteres').css('display', 'none');
        $('#cont-tipopago-porcuotas').css('display','none');
        $('#cont-tipopago-completo').css('display','none');
        $('#cont-montorecibido').css('display','none');
        $('#moradescuento').val('');
        $('#moradescuento_detalle').val('');
        $('#vuelto').val('0.00');
        $('#cont-vuelto').css('display','none');
        $('#cont-acuentaproxima').css('display','none');
        $('#cont-redondeado').css('display','none');
        if(e.currentTarget.value == 1) {
            $('#cont-tipopago-porcuotas').css('display','block');
            $('#cont-montorecibido').css('display','block');
            $('#cont-vuelto').css('display','block');
            $('#cont-redondeado').css('display','block');
            mostrar_creditocliente();
        }
        else if(e.currentTarget.value == 2) {
            $('#cont-tipopago-completo').css('display','block');
            $('#cont-acuentaproxima').css('display','block');
            mostrar_creditocliente();
        } 
        $('#montocompleto').val('');
  
    }).val(1).trigger("change");
  
    $('#hastacuota').select2({
        placeholder: '-- Seleccionar Cuota --',
        minimumResultsForSearch: -1
    }).on("change", function(e) {
        $('#cont-descontarmora').css('display', 'none');
        $('#cont-descontarinteres').css('display', 'none');
        mostrar_creditocliente();
    });

    $("#check_moradescuento").click(function(){
        $('#moradescuento').prop('disabled', true);
        $('#moradescuento_detalle').prop('disabled', true);
        $('#moradescuento').val('');
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
  
    //agregar_formapago();
  
    function agregar_formapago(){
      var num = $("#tabla-formapago > tbody#tbody-formapago").attr('num');
      var nuevaFila='<tr id="'+num+'">'+
                         '<td>'+
                           '<table class="table">'+
                             '<tbody>'+
                               '<tr>'+
                                 '<td width="150px">Cuenta Bancaria *</td>'+
                                 '<td colspan="2">'+
                                   '<select id="formapago_idcuentabancaria'+num+'">'+
                                     '<option></option>'+
                                     '@foreach ($cuentabancarias as $value)'+
                                     '<option value="{{ $value->id }}" formapago_banco="{{ $value->banco }}" formapago_numerocuenta="{{ $value->numerocuenta }}">{{ $value->banco }}: {{ $value->numerocuenta }}</option>'+
                                     '@endforeach'+
                                   '</select>'+
                                 '</td>'+
                               '</tr>'+
                               '<tr>'+
                                 '<td>Nro de Operación *</td>'+
                                 '<td colspan="2"><input type="number" id="formapago_numerooperacion'+num+'"/></td>'+
                               '</tr>'+
                               '<tr>'+
                                 '<td>Fecha / Hora *</td>'+
                                 '<td><input type="date" id="formapago_fecha'+num+'"/></td>'+
                                 '<td><input type="time" value="00:00" id="formapago_hora'+num+'"/></td>'+
                               '</tr>'+
                               '<tr>'+
                                 '<td>Monto *</td>'+
                                 '<td colspan="2"><input type="number" id="formapago_montodeposito'+num+'" onkeyup="calcular_montoformapago();" step="0.01"/></td>'+
                               '</tr>'+
                               '<tr>'+
                                 '<td>Voucher *</td>'+
                                 '<td colspan="2">'+
                                   '<div class="file-input">'+
                                     '<label for="formapago_voucher'+num+'">'+
                                       '<i class="fa fa-upload"></i> Subir Voucher'+
                                       '<p id="file-result-formapago_voucher'+num+'"></p>'+
                                     '</label>'+
                                     '<input type="file" id="formapago_voucher'+num+'" class="file">'+
                                   '</div>'+
                                 '</td>'+
                               '</tr>'+
                             '</tbody>'+
                           '</table>'+
                         '</td><td><a href="javascript:;" onclick="eliminar_formapago('+num+')" class="btn btn-danger"><i class="fa fa-trash"></i> Quitar</a></td>'+
                       '</tr>';

        $("#tabla-formapago > tbody#tbody-formapago").append(nuevaFila);
        $("#tabla-formapago > tbody#tbody-formapago").attr('num',parseInt(num)+1);  
      
        $('#formapago_idcuentabancaria'+num).select2({
            placeholder: '-- Seleccionar Cuenta Bancaria --',
            minimumResultsForSearch: -1
        });
      
        // subir voucher
        file({click:'#formapago_voucher'+num});
      
        
    }
  
    function eliminar_formapago(num){
        $("#tabla-formapago > tbody#tbody-formapago > tr#"+num).remove();
        calcular_montoformapago();
    }
  
    
    function seleccionar_formapago(){
        var data = [];
        $("#tabla-formapago > tbody#tbody-formapago > tr").each(function() {
            var num = $(this).attr('id');      
            data.push({
                'num' : num,
                'formapago_idcuentabancaria' : $('#formapago_idcuentabancaria'+num+' :selected').val(),
                'formapago_banco' : $('#formapago_idcuentabancaria'+num+' :selected').attr('formapago_banco'),
                'formapago_numerocuenta' : $('#formapago_idcuentabancaria'+num+' :selected').attr('formapago_numerocuenta'),
                'formapago_numerooperacion' : $('#formapago_numerooperacion'+num).val(),
                'formapago_fecha' : $('#formapago_fecha'+num).val(),
                'formapago_hora' : $('#formapago_hora'+num).val(),
                'formapago_montodeposito' : $('#formapago_montodeposito'+num).val(),
                'formapago_montocontado' : $('#formapago_montocontado'+num).val(),
                'formapago_voucher' : $('#formapago_voucher'+num).prop("files")[0],
            });
        });
      
        //return JSON.stringify(data);
        return data;
    } 
  
    function calcular_montoformapago(){
        var total = 0;
        $("#tabla-formapago > tbody#tbody-formapago > tr").each(function() {
            var num = $(this).attr('id');      
            var formapago_montodeposito =  $('#formapago_montodeposito'+num).val()!=''?$('#formapago_montodeposito'+num).val():0;
            total = total+parseFloat(formapago_montodeposito);
        });
        
        /*$('#totalmonto_efectivo').css('background-color','#11c529');
        if(total!=parseFloat($('#cuotas_totalredondeado').val())){
            $('#totalmonto_efectivo').css('background-color','#ff1f44');
        }*/
        $('#totalmonto_deposito').html(total.toFixed(2));
        var cuotas_totalredondeado = parseFloat($('#cuotas_totalredondeado').val());
        var cuotas_totalabono = parseFloat($('#cuotas_totalabono').val());
        var idtipopago = $('#idtipopago :selected').val();
        var efectivo = cuotas_totalredondeado+cuotas_totalabono;
        if(idtipopago==2){
            var montocompleto = $('#montocompleto').val()!=''?parseFloat($('#montocompleto').val()):0;
            efectivo = montocompleto;
        }
        var total = efectivo-total;
        $('#totalmonto_efectivo').html(parseFloat(total).toFixed(2));
        $('#montorecibido').val('');
        calcular_vuelto_cuota();
    }
  
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
                calcular_montoformapago();
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
                hastacuota: $('#hastacuota').val(),
                montocompleto: $('#montocompleto').val(),
                acuenta: $('#acuenta').val(),
                check_moradescuento: $("#check_moradescuento:checked").val(),
                moradescuento: $('#moradescuento').val(),
                moradescuento_detalle: $('#moradescuento_detalle').val(),
                totalmonto_efectivo: $('#totalmonto_efectivo').html(),
                montorecibido: $('#montorecibido').val(),
                vuelto: $('#vuelto').val(),
                vueltopagocompleto: $('#vueltopagocompleto').val(),
              
                facturacion_idcliente: $('#facturacion_idcliente').val(),
                facturacion_direccion: $('#facturacion_direccion').val(),
                facturacion_idubigeo: $('#facturacion_idubigeo').val(),
                facturacion_idagencia: $('#facturacion_idagencia').val(),
                facturacion_idmoneda: $('#facturacion_idmoneda').val(),
                facturacion_idtipocomprobante: $('#facturacion_idtipocomprobante').val(),
              
                seleccionar_formapago: seleccionar_formapago()
            }
        },
        function(resultado){
          $('#modal-cobranzarealizada').css('display','block');
          var imprimir = '';
          imprimir = '<div id="iframeventa"><iframe src="{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza') }}/'+resultado['idprestamocobranza']+'/edit?view=ticketpdf&idcobranza='+resultado['idprestamocobranza']+'#zoom=130" frameborder="0" width="100%" height="600px"></iframe></div>';
          $('#contenido-cobranzarealizada').html('<div class="cont-confirm" style="margin-top: 15px;">'+
                             '<div class="confirm"><i class="fa fa-check"></i></div>'+
                             '<div class="confirm-texto">¡Correcto!</div>'+
                             '<div class="confirm-subtexto">Se ha registrado correctamente.</div></div>'+
                             '<div class="custom-form" style="text-align: center;margin-bottom: 5px;">'+
                             '<button type="button" class="btn big-btn color-bg flat-btn mx-realizar-pago" style="margin: auto;float: none;" onclick="realizar_nueva_cobranza()">'+
                             '<i class="fa fa-check"></i> Realizar Nueva Cobranza</button></div>'+
                             '<div class="custom-form" style="text-align: center;margin-bottom: 5px;">'+
                             '<button type="button" class="btn big-btn color-bg flat-btn" style="margin: auto;float: none;" onclick="iracobranzas()">'+
                             '<i class="fa fa-check"></i> Ir a Cobranzas</button></div>'+
                             imprimir);
          removecarga({input:'#carga-cobranza'});
        })
    }
    function realizar_nueva_cobranza() {
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamocobranza/'+$('#idcliente').val()+'/edit?view=cobranza',result:'#cont-clientecredito'});
      $('#modal-cobranzarealizada').css('display','none');
    }
    function iracobranzas() {
      location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamocobranza') }}';
    }
    function calcular_vuelto_cuota(){
        var montorecibido = parseFloat($('#montorecibido').val());
        var totalmonto_efectivo = parseFloat($('#totalmonto_efectivo').html());
        var vuelto = (montorecibido-totalmonto_efectivo).toFixed(2);
      
        var idtipopago = $('#idtipopago :selected').val();
        if(idtipopago==2){
            $('#cont-montorecibido').css('display','none');
            $('#cont-vuelto').css('display','none');
        }else{
            $('#vuelto').val(vuelto);
            $('#cont-montorecibido').css('display','block');
            $('#cont-vuelto').css('display','block');
            if(totalmonto_efectivo<=0){
                $('#cont-montorecibido').css('display','none');
                $('#cont-vuelto').css('display','none');
            }
        }
    }
</script>