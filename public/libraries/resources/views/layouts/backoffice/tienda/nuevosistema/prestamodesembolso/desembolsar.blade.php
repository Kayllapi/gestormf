<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Desembolsar Crédito</span>
    <a class="btn btn-success" href="javascript:;" onclick="index()"><i class="fa fa-angle-left"></i> Atras</a></a>
  </div>
</div>              
<div class="tabs-container" id="tab-desembolso">
    <ul class="tabs-menu">
        <li class="current"><a href="#tab-desembolso-0">Facturación</a></li>
        <li><a href="#tab-desembolso-1">Crédito</a></li>
    </ul>
    <div class="tab">
        <div id="tab-desembolso-0" class="tab-content" style="display: block;">
          <form class="js-validation-signin px-30" action="javascript:;"
                onsubmit="callback({
                                    route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamodesembolso/{{ $prestamodesembolso->id }}',
                                    method: 'PUT',
                                    data:   {
                                      view: 'desembolsar'
                                    }
                                  },
                                  function(resultado){
                                    //index();
                                  },this)">


              <div class="row">
                  <div class="col-sm-4">
                  <label>Cliente *</label>
                      <div class="row">
                         <div class="col-md-12">
                            <select id="idcliente" disabled>
                                <option value="{{ $prestamodesembolso->idcliente }}">{{ $prestamodesembolso->cliente_nombre }}</option>
                            </select>
                         </div>
                      </div>
                      <label>Dirección *</label>
                      <input type="text" id="cliente_direccion" value="{{ $prestamodesembolso->cliente_direccion }}"/>
                      <label>Ubicación (Ubigeo) *</label>
                      <select id="idubigeo">
                          <option value="{{ $prestamodesembolso->cliente_idubigeo }}">{{ $prestamodesembolso->cliente_ubigeonombre }}</option>
                      </select>
                  </div>
                  <div class="col-sm-4">
                    <label>Agencia *</label>
                    <select id="idagencia">
                      <option></option>
                      @foreach ($agencias as $value)
                      <option value="{{ $value->id }}">{{ $value->ruc }} - {{ $value->nombrecomercial }}</option>
                      @endforeach
                    </select>
                    <label>Moneda *</label>
                    <select id="idmoneda">
                      <option></option>
                      @foreach ($monedas as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                      @endforeach
                    </select>
                    <label>Tipo de Comprobante *</label>
                    <select id="idtipocomprobante">
                      <option></option>
                      @foreach ($tipocomprobante as $value)
                      <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-sm-4">
                  <?php $total_gastoadministrativo = '0.00'; ?>
                  @if($configuracion_prestamo['idestadogasto_administrativo']==1)
                      <div class="row">
                          <div class="col-sm-6">
                            <label>Gasto Administrativo</label>
                            <?php 
                            if($prestamodesembolso->idprestamo_frecuencia==1){
                                $total_gastoadministrativo = $configuracion_prestamo['gasto_administrativo_uno'];
                            }elseif($prestamodesembolso->idprestamo_frecuencia==2){
                                $total_gastoadministrativo = $configuracion_prestamo['gasto_administrativo_dos'];
                            }elseif($prestamodesembolso->idprestamo_frecuencia==3){
                                $total_gastoadministrativo = $configuracion_prestamo['gasto_administrativo_tres'];
                            }elseif($prestamodesembolso->idprestamo_frecuencia==4){
                                $total_gastoadministrativo = $configuracion_prestamo['gasto_administrativo_cuatro'];
                            }elseif($prestamodesembolso->idprestamo_frecuencia==5){
                                $total_gastoadministrativo = $configuracion_prestamo['gasto_administrativo_cinco'];
                            }
                            ?>
                            <input type="text" id="total_gastoadministrativo" value="{{ $total_gastoadministrativo }}" disabled/>
                          </div>
                          <div class="col-sm-6">
                            <label>Agregar a Crédito</label>
                            
                                  <div class="onoffswitch">
                                      <input type="checkbox" class="onoffswitch-checkbox check_gastoadministrativo" id="check_gastoadministrativo">
                                      <label class="onoffswitch-label" for="check_gastoadministrativo">
                                          <span class="onoffswitch-inner"></span>
                                          <span class="onoffswitch-switch"></span>
                                      </label> 
                                  </div>
                          </div>
                      </div>
                      <div id="cont-gastoadministrativo">
                          <label>Monto recibido *</label>
                          <input type="number" id="facturacion_montorecibido" min="0" step="0.01" onkeyup="calcular_vuelto_cuota()"/>
                          <label>Vuelto</label>
                          <input type="number" id="facturacion_vuelto" min="0" step="0.01" disabled/>
                      </div>
                  @endif
                  </div>
              </div>
              <button type="submit" class="btn mx-btn-post">Desembolsar Crédito</button>
          </form>       
        </div>
        <div id="tab-desembolso-1" class="tab-content" style="display: none;">
          <div class="row">
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-md-4">
                        <label>Monto</label>
                        <input type="number" value="{{$prestamodesembolso->monto}}" min="0" step="0.01" disabled>
                        <label>Número de Cuotas</label>
                        <input type="number" value="{{$prestamodesembolso->numerocuota}}" min="1" step="1" disabled>
                        <label>Fecha de Inicio</label>
                        <input type="date" value="{{$prestamodesembolso->fechainicio}}" disabled>
                        <label>Frecuencia</label>
                        <input type="text" value="{{$prestamodesembolso->frecuencia_nombre}}" disabled>
                        <div id="cont-numerodias" <?php echo $prestamodesembolso->idprestamo_frecuencia==5?'': 'style="display: none"' ?>>
                            <label>Número de Días</label>
                            <input type="number" value="{{$prestamodesembolso->numerodias}}" id="numerodias" value="0" min="0" step="1" disabled>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Tasa</label>
                        <select id="idtasa" disabled>
                            <option></option>
                            <option value="1">Interes Fija</option>
                            <option value="2">Interes Efectiva</option>
                        </select>
                        <label>Interes %</label>
                        <input type="number" min="0" step="0.01" id="tasa" value="{{ $prestamodesembolso->tasa }}" disabled>
                        <label>Interes Total</label>
                        <input type="text" id="total_interes" value="{{ $prestamodesembolso->total_interes }}" disabled/>
                        @if($prestamodesembolso->total_segurodesgravamen>0)
                        <label>Seguro Desgravamen</label>
                        <input type="text" id="total_segurodesgravamen" value="{{ $prestamodesembolso->total_segurodesgravamen }}" disabled/>
                        @endif
                        <label>Total a Pagar</label>
                        <input type="text" id="total_cuotafinal" value="{{ $prestamodesembolso->total_cuotafinal }}" disabled/>
                    </div>
                    <div class="col-md-4">
                        <label>Excluir Días:</label>

                        <table style="width: 100%;">
                          <tr>
                            <td style="text-align: right;padding: 10px;font-weight: bold;">Sábados</td>
                            <td>
                              <div class="onoffswitch">
                                  <input type="checkbox" class="onoffswitch-checkbox excluirsabado" id="excluirsabado" disabled>
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
                                  <input type="checkbox" class="onoffswitch-checkbox excluirdomingo" id="excluirdomingo" disabled>
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
                                  <input type="checkbox" class="onoffswitch-checkbox excluirferiado" id="excluirferiado" disabled>
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
            <div class="col-sm-6">
              <div id="cont-creditocalendario">
              <table class="table" id="tabla-preaprobado-creditocalendario">
                <thead style="background: #31353d; color: #fff;">
                  <tr>
                    <td style="padding: 8px;text-align: right;">Nº</td>
                    <td style="padding: 8px;text-align: right;">Fecha de Pago</td>
                    <td style="padding: 8px;text-align: right;">Saldo Capital</td>
                    <td style="padding: 8px;text-align: right;">Amortización</td>
                    <td style="padding: 8px;text-align: right;">Interes</td>
                    @if ($prestamodesembolso->total_segurodesgravamen>0)
                    <td style="padding: 8px;text-align: right;">Seguro Desgravamen</td>
                    @endif
                    <td style="padding: 8px;text-align: right;">Cuota</td>
                    <td style="padding: 8px;text-align: right;">Total</td>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($prestamodesembolsodetalle as $value)
                    <tr>
                      <td style="padding: 8px;text-align: right;width: 50px;">{{ $value->numero }}</td>
                      <td style="padding: 8px;text-align: right;width: 120px;">{{ $value->fechavencimiento }}</td>
                      <td style="padding: 8px;text-align: right;">{{ $value->saldocapital }}</td>
                      <td style="padding: 8px;text-align: right;">{{ $value->amortizacion }}</td>
                      <td style="padding: 8px;text-align: right;">{{ $value->interes }}</td>
                      @if ($prestamodesembolso->total_segurodesgravamen>0)
                      <td style="padding: 8px;text-align: right;">{{ $value->seguro }}</td>
                      @endif
                      <td style="padding: 8px;text-align: right;">{{ $value->cuota }}</td>
                      <td style="padding: 8px;text-align: right;">{{ $value->total }}</td>
                    </tr>
                  @endforeach
                  <tr style="background-color: #31353c;color: white;">
                    <td style="padding: 8px;text-align: right;width: 50px;" colspan="3">TOTAL</td>
                    <td style="padding: 8px;text-align: right;">{{ $prestamodesembolso->total_amortizacion }}</td>
                    <td style="padding: 8px;text-align: right;">{{ $prestamodesembolso->total_interes }}</td>
                    @if ($prestamodesembolso->total_segurodesgravamen>0)
                    <td style="padding: 8px;text-align: right;">{{ $prestamodesembolso->total_segurodesgravamen }}</td>
                    @endif
                    <td style="padding: 8px;text-align: right;">{{ $prestamodesembolso->total_cuota }}</td>
                    <td style="padding: 8px;text-align: right;">{{ $prestamodesembolso->total_cuotafinal }}</td>
                  </tr>
                </tbody>
              </table>
              </div>
              <div id="cont-load-creditocalendario">
              </div>
            </div>
          </div>   
        </div>
    </div>
</div>
        
<script>
  tab({click:'#tab-desembolso'});
  
    $("#check_gastoadministrativo").click(function() {
        $('#cont-gastoadministrativo').css('display','block');
        $('#cont-creditocalendario').css('display','block');
        $('#cont-load-creditocalendario').html('');
        var checked = $("#check_gastoadministrativo:checked").val();
        if(checked=='on'){
            $('#cont-gastoadministrativo').css('display','none');
            $('#cont-creditocalendario').css('display','none');
            creditoCalendario();
        }
    });
  
  @if ($prestamodesembolso->excluirsabado == "on")
      $('#excluirsabado').prop("checked", true);
  @endif
  @if ($prestamodesembolso->excluirdomingo == "on")
      $('#excluirdomingo').prop("checked", true);
  @endif
  @if ($prestamodesembolso->excluirferiado == "on")
      $('#excluirferiado').prop("checked", true);
  @endif

  $('#idcliente').select2({
    @include('app.select2_cliente')
  });
  
  $('#idubigeo').select2({
      @include('app.select2_ubigeo')
  });
  
  @if($configuracion_facturacion['idempresapordefecto']!=null)
      $("#idagencia").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      }).val({{ $configuracion_facturacion['idempresapordefecto'] }}).trigger("change");    
  @else
      $("#idagencia").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      });
  @endif

  @if($configuracion_facturacion['idmonedapordefecto']!=null)
      $("#idmoneda").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      }).val({{ $configuracion_facturacion['idmonedapordefecto'] }}).trigger("change");
  @else
      $("#idmoneda").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      });
  @endif
  
  @if($configuracion_facturacion['idcomprobantepordefecto']!=null)
      $("#idtipocomprobante").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      }).val({{ $configuracion_facturacion['idcomprobantepordefecto'] }}).trigger("change");   
  @else
      $("#idtipocomprobante").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      });
  @endif

  function updateDatos() {
    var ubigeonombre = $('#idubigeo option:selected').attr('nombre');
    var ubigeocodigo = $('#idubigeo option:selected').attr('codigo');
    $('#cliente_ubigeo').val(ubigeonombre);
    $('#cliente_ubigeocodigo').val(ubigeocodigo);
  }
  
  $('#idtasa').select2({
    placeholder: '-- Seleccionar Tasa --',
    minimumResultsForSearch: -1
  }).val({{ $prestamodesembolso->idprestamo_tipotasa }}).trigger('change');
  
   function calcular_vuelto_cuota(){
        var montorecibido = parseFloat($('#facturacion_montorecibido').val());
        var total_gastoadministrativo = parseFloat($('#total_gastoadministrativo').val());
        var vuelto = (montorecibido-total_gastoadministrativo).toFixed(2);
        $('#facturacion_vuelto').val(vuelto);
    }
  
  function creditoCalendario() {
        $.ajax({
            url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-creditocalendario') }}",
            type: 'GET',
            data: {
                monto: '{{$prestamodesembolso->monto}}',
                numerocuota: '{{$prestamodesembolso->numerocuota}}',
                fechainicio: '{{$prestamodesembolso->fechainicio}}',
                frecuencia: '{{$prestamodesembolso->idprestamo_frecuencia}}',
                numerodias: '{{$prestamodesembolso->numerodias}}',
                tipotasa: '{{$prestamodesembolso->idprestamo_tipotasa}}',
                tasa: '{{$prestamodesembolso->tasa}}',
                gastoadministrativo: '{{$total_gastoadministrativo}}',
                excluirsabado: '{{$prestamodesembolso->excluirsabado}}',
                excluirdomingo: '{{$prestamodesembolso->excluirdomingo}}',
                excluirferiado: '{{$prestamodesembolso->excluirferiado}}',
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
</script>