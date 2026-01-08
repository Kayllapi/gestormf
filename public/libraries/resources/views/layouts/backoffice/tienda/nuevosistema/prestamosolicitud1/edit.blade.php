<div class="list-single-main-wrapper fl-wrap">
  <div class="breadcrumbs gradient-bg fl-wrap">
    <span>Editar Crédito</span>
    <a class="btn btn-success" href="javascript:;" onclick="index()"><i class="fa fa-angle-left"></i> Atras</a></a>
  </div>
</div>
    <div class="tabs-container" id="tab-detalle-general-cliente">
        <ul class="tabs-menu">
          <li><a href="#tab-detalle-general-cliente-0">Cronograma</a></li>
          <li><a href="#tab-detalle-general-cliente-1">Detalle</a></li>
        </ul>
        <div class="tab">
          <!-- Cronograma -->
          <div id="tab-detalle-general-cliente-0" class="tab-content" style="display: block;">
              <form action="javascript:;"
                    onsubmit="callback({
                                route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud/{{$prestamocredito->id}}',
                                method: 'PUT',
                                data:   {
                                    view: 'editar'
                                }
                            },
                            function(resultado){
                              index();
                            },this)">
                  <div class="row">
                    <div class="col-sm-6">
                        <label>Cliente *</label>
                        <div class="row">
                          <div class="col-md-12">
                              <select id="idcliente" disabled>
                                  <option value="{{$prestamocredito->idcliente}}">{{$prestamocredito->cliente_nombre}}</option>
                              </select>
                          </div>
                        </div>
                        <table style="margin-bottom: 5px;">
                          <tr>
                            <td style="text-align: left;padding-right: 10px;">Participar con Cónyuge *</td>
                            <td>
                              <div class="onoffswitch">
                                  <input type="checkbox" class="onoffswitch-checkbox check_idconyuge" id="check_idconyuge" <?php echo $prestamocredito->idconyuge!=0?'checked="true"':'' ?>>
                                  <label class="onoffswitch-label" for="check_idconyuge">
                                      <span class="onoffswitch-inner"></span>
                                      <span class="onoffswitch-switch"></span>
                                  </label> 
                              </div>
                            </td>
                          </tr>
                        </table>
                        @if($prestamocredito->idconyuge!=0)
                        <div id="cont-conyuge">
                            <select id="idconyuge">
                                    <option value="{{$prestamocredito->idconyuge}}">{{$prestamocredito->conyuge_nombre}}</option>
                            </select>
                        </div>
                        @else
                        <div style="display: none;" id="cont-conyuge">
                            <select id="idconyuge">
                                    <option></option>
                            </select>
                        </div>
                        @endif
                        <table style="margin-bottom: 5px;">
                          <tr>
                            <td style="text-align: left;padding-right: 10px;">Participar con Aval o Garante *</td>
                            <td>
                              <div class="onoffswitch">
                                  <input type="checkbox" class="onoffswitch-checkbox check_idgarante" id="check_idgarante" <?php echo $prestamocredito->idgarante!=0?'checked="true"':'' ?>>
                                  <label class="onoffswitch-label" for="check_idgarante">
                                      <span class="onoffswitch-inner"></span>
                                      <span class="onoffswitch-switch"></span>
                                  </label> 
                              </div>
                            </td>
                          </tr>
                        </table>
                        @if($prestamocredito->idgarante!=0)
                        <div id="cont-garante">
                            <select id="idgarante">
                                    <option value="{{$prestamocredito->idgarante}}">{{$prestamocredito->garante_nombre}}</option>
                            </select>
                        </div>
                        @else
                        <div style="display: none;" id="cont-garante">
                            <select id="idgarante">
                                    <option></option>
                            </select>
                        </div>
                        @endif
                        <div class="list-single-main-wrapper fl-wrap">
                            <div class="breadcrumbs gradient-bg fl-wrap">
                              <span>Crédito</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <label>Monto *</label>
                                <input type="number" value="{{$prestamocredito->monto}}" id="monto" min="0" step="0.01" onkeyup="creditoCalendario()" onclick="creditoCalendario()"/>
                                <label>Número de Cuotas *</label>
                                <input type="number" value="{{$prestamocredito->numerocuota}}" id="numerocuota" min="1" step="1" onkeyup="creditoCalendario()" onclick="creditoCalendario()"/>
                                <label>Fecha de Inicio *</label>
                                <input type="date" value="{{$prestamocredito->fechainicio}}" id="fechainicio" onchange="creditoCalendario()" onclick="creditoCalendario()"/>
                                <label>Frecuencia *</label>
                                <select id="idfrecuencia" onchange="creditoCalendario()">
                                    <option></option>
                                    @foreach($frecuencias as $value)
                                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                    @endforeach
                                </select>
                                <div id="cont-numerodias" style="display: none">
                                    <label>Número de Días *</label>
                                    <input type="number" value="{{$prestamocredito->numerodias}}" id="numerodias" value="0" min="0" step="1" onkeyup="creditoCalendario()" onclick="creditoCalendario()"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>Tasa *</label>
                                <select id="idtasa" onchange="creditoCalendario()" <?php echo $configuracion_prestamo['idestadotasa']==2?'disabled':'' ?>>
                                    <option></option>
                                    <option value="1">Interes Fija</option>
                                    <option value="2">Interes Efectiva</option>
                                </select>
                                <label>Interes % *</label>
                                <input type="number" value="{{$prestamocredito->tasa}}" id="tasa" min="0" step="0.01" onclick="creditoCalendario()" onkeyup="creditoCalendario()"/>
                                <label>Interes Total</label>
                                <input type="text" id="total_interes" value="0.00" disabled/>
                                @if($configuracion_prestamo['idestadoseguro_degravamen']==1)
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
                    </div>
                    <div class="col-sm-6">
                      <div id="cont-load-creditocalendario"></div>
                    </div>
                  </div>
                  <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
              </form>
          </div>
          <!-- Detalles -->
          <div id="tab-detalle-general-cliente-1" class="tab-content" style="display: none;">
            <div class="tabs-container" id="tab-detalle-cliente">
              <ul class="tabs-menu">
                <li><a href="#tab-detalle-cliente-0">Domicilios</a></li>
                <li><a href="#tab-detalle-cliente-1">Labores</a></li>
                <li><a href="#tab-detalle-cliente-2">Bienes</a></li>
                <li><a href="#tab-detalle-cliente-3">Relaciones</a></li>
              </ul>
              <div class="tab">
                <div id="tab-detalle-cliente-0" class="tab-content" style="display: block;">
                  <div id="cont-domicilios"></div>
                </div>
                <div id="tab-detalle-cliente-1" class="tab-content" style="display: none;">
                  <div id="cont-laborals"></div>
                </div>
                <div id="tab-detalle-cliente-2" class="tab-content" style="display: none;">
                  <div id="cont-biens"></div>
                </div>
                <div id="tab-detalle-cliente-3" class="tab-content" style="display: none;">
                  <div id="cont-relacions"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
<!-- Tabulador de pestañas -->
<script>
  tab({click:'#tab-detalle-general-cliente'});
  tab({click:'#tab-detalle-cliente'});
</script>

<!-- Detalle de Credito y Cronograma -->
<script>
  @if ($prestamocredito->excluirsabado == "on")
      $('#excluirsabado').prop("checked", true);
  @endif
  @if ($prestamocredito->excluirdomingo == "on")
      $('#excluirdomingo').prop("checked", true);
  @endif
  @if ($prestamocredito->excluirferiado == "on")
      $('#excluirferiado').prop("checked", true);
  @endif
  
    $('#idfrecuencia').select2({
        placeholder: '-- Seleccionar Frecuencia --',
        minimumResultsForSearch: -1,
    });
    $('#idtasa').select2({
        placeholder: '-- Seleccionar Tasa --',
        minimumResultsForSearch: -1,
    }).val({{ $prestamocredito->idprestamo_tipotasa }}).trigger('change');
    $('#idconyuge').select2({
    @include('app.select2_cliente')
    });
    $('#idgarante').select2({
    @include('app.select2_cliente')
    });
    $('#idcliente').select2({
    @include('app.select2_cliente')
    });

    // Mostrando avales en el credito
    $("#check_idconyuge").click(function(){
        $('#cont-conyuge').css('display','none');
        var checked = $("#check_idconyuge:checked").val();
        if(checked=='on'){
            $('#cont-conyuge').css('display','block');
            $('#idconyuge').html('<option></option>');
        }
    });
    $("#check_idgarante").click(function(){
        $('#cont-garante').css('display','none');
        var checked = $("#check_idgarante:checked").val();
        if(checked=='on'){
            $('#cont-garante').css('display','block');
            $('#idgarante').html('<option></option>');
        }
    });
    $("#idfrecuencia").change(function(){
        var frecuencia = $('#idfrecuencia').val();
        $('#numerodias').val('{{$prestamocredito->numerodias}}');
        $('#cont-numerodias').css('display', 'none');
        if (frecuencia == 5) {
            $('#cont-numerodias').css({'display':'block'});
        }
    }).val({{ $prestamocredito->idprestamo_frecuencia }}).trigger('change');

    function creditoCalendario() {
      
        var monto = $('#monto').val();
        var numerocuota = $('#numerocuota').val();
        var fechainicio = $('#fechainicio').val();
        var frecuencia = $('#idfrecuencia').val();
        var numerodias = $('#numerodias').val();
        var tipotasa = $('#idtasa').val();
        var tasa = $('#tasa').val();
      
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
                tipotasa: tipotasa,
                tasa: tasa,
                excluirsabado: $('#excluirsabado:checked').val(),
                excluirdomingo: $('#excluirdomingo:checked').val(),
                excluirferiado: $('#excluirferiado:checked').val(),
            },
            beforeSend: function (data) {
                load('#cont-load-creditocalendario');
            },
            success: function (res) {
                $('#total_interes').val(res['total_interes']);
                $('#total_segurodesgravamen').val(res['total_segurodesgravamen']);
                $('#total_cuotafinal').val(res['total_cuotafinal']);
                $('#cont-load-creditocalendario').html(res['html']);
            }
        });
    }
    // fin
</script>

<!-- Datos generales del cliente -->
<script>
  // Domicilios
  domicilio_generalindex();
  function domicilio_generalindex() {
    pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=domiciliogeneral',result:'#cont-domicilios'});
  }
  function domicilio_index(){
    pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=domicilio',result:'#cont-domicilios'});
  }
  function domicilio_create(){
    pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=domiciliocreate',result:'#cont-domicilios'});
  }

  // Labores
  laboral_generalindex();
  function laboral_generalindex() {
    pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=laboralgeneral',result:'#cont-laborals'});
  }
  function laboral_index(){
    pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=laboral',result:'#cont-laborals'});
  }
  function laboral_create(){
    pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=laboralcreate',result:'#cont-laborals'});
  }

  // Bienes
  bien_generalindex();
  function bien_generalindex() {
    pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=biengeneral',result:'#cont-biens'});
  }
  function bien_index(){
    pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=bien',result:'#cont-biens'});
  }
  function bien_create(){
    pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=biencreate',result:'#cont-biens'});
  }

  // Familiares
  relacion_generalindex();
  function relacion_generalindex() {
    pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=relaciongeneral',result:'#cont-relacions'});
  }
  function relacion_index(){
    pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=relacion',result:'#cont-relacions'});
  }
  function relacion_create(){
    pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}/edit?view=relacioncreate',result:'#cont-relacions'});
  }

</script>