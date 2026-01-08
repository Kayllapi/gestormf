@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Editar Ahorro',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamo/ahorrosolicitud: Ir Atras'
    ]
])
<div id="carga-credito">
    <div class="tabs-container" id="tab-detalle-cliente">
        <ul class="tabs-menu">
          <li class="current"><a href="#tab-detalle-cliente-0">Cronograma</a></li>
          <!--li><a href="#tab-detalle-cliente-7">Expediente</a></li-->
        </ul>
        <div class="tab">
          <div id="tab-detalle-cliente-0" class="tab-content" style="display: block;">
              <form action="javascript:;"
                    class="form-guardar_creditoexpediente"
                    onsubmit="callback({
                                route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamo/ahorrosolicitud/{{$prestamoahorro->id}}',
                                method: 'PUT',
                                carga: '#carga-credito',
                                data:   {
                                    view: 'editar',
                                    idestadoexpediente: $('#idestadoexpediente:checked').val()
                                }
                            },
                            function(resultado){
                                removecarga({input:'#carga-credito'});
                                //location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrosolicitud') }}';
                            },this)">
                  <div class="row">
                    <div class="col-sm-6">
                        <label>Cliente</label>
                        <div class="row">
                          <div class="col-md-12">
                              <select id="idcliente" disabled>
                                  <option value="{{$prestamoahorro->idcliente}}">{{$prestamoahorro->cliente_nombre}}</option>
                              </select>
                          </div>
                        </div>
                        <table style="margin-bottom: 5px;">
                          <tr>
                            <td style="text-align: left;padding-right: 10px;">Participar con Cónyugue *</td>
                            <td>
                              <div class="onoffswitch">
                                  <input type="checkbox" class="onoffswitch-checkbox check_idconyuge" id="check_idconyuge" <?php echo $prestamoahorro->idconyuge!=0?'checked="true"':'' ?>>
                                  <label class="onoffswitch-label" for="check_idconyuge">
                                      <span class="onoffswitch-inner"></span>
                                      <span class="onoffswitch-switch"></span>
                                  </label> 
                              </div>
                            </td>
                          </tr>
                        </table>
                        @if($prestamoahorro->idconyuge!=0)
                        <div id="cont-conyuge">
                            <select id="idconyuge">
                                    <option value="{{$prestamoahorro->idconyuge}}">{{$prestamoahorro->conyuge_nombre}}</option>
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
                            <td style="text-align: left;padding-right: 10px;">Participar con Beneficiario *</td>
                            <td>
                              <div class="onoffswitch">
                                  <input type="checkbox" class="onoffswitch-checkbox check_idbeneficiario" id="check_idbeneficiario" <?php echo $prestamoahorro->idbeneficiario!=0?'checked="true"':'' ?>>
                                  <label class="onoffswitch-label" for="check_idbeneficiario">
                                      <span class="onoffswitch-inner"></span>
                                      <span class="onoffswitch-switch"></span>
                                  </label> 
                              </div>
                            </td>
                          </tr>
                        </table>
                        @if($prestamoahorro->idbeneficiario!=0)
                        <div id="cont-beneficiario">
                            <select id="idbeneficiario">
                                    <option value="{{$prestamoahorro->idbeneficiario}}">{{$prestamoahorro->beneficiario_nombre}}</option>
                            </select>
                        </div>
                        @else
                        <div style="display: none;" id="cont-beneficiario">
                            <select id="idbeneficiario">
                                    <option></option>
                            </select>
                        </div>
                        @endif
                        <div class="list-single-main-wrapper fl-wrap">
                            <div class="breadcrumbs gradient-bg fl-wrap">
                              <span>Ahorro</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <label>Tipo de Ahorro *</label>
                                <select id="tipocreditonombre" disabled>
                                    <option></option>
                                        @foreach($tipoahorros as $value)
                                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                        @endforeach
                                </select>
                                <div id="cont-ahorrofijo1">
                                    <label>Monto *</label>
                                    <input type="number" value="{{$prestamoahorro->monto}}" id="ahorrofijo_monto" min="0" step="0.01" onkeyup="creditoCalendario()" onclick="creditoCalendario()"/>
                                    <label>Tiempo (Meses) *</label>
                                    <select id="ahorrofijo_tiempo" onchange="creditoCalendario()">
                                        <option></option>
                                        <option value="6">6 MESES</option>
                                        <option value="7">7 MESES</option>
                                        <option value="8">8 MESES</option>
                                        <option value="9">9 MESES</option>
                                        <option value="10">10 MESES</option>
                                        <option value="11">11 MESES</option>
                                        <option value="12">12 MESES</option>
                                        <option value="13">13 MESES</option>
                                        <option value="14">14 MESES</option>
                                        <option value="15">15 MESES</option>
                                        <option value="16">16 MESES</option>
                                        <option value="17">17 MESES</option>
                                        <option value="18">18 MESES</option>
                                        <option value="19">19 MESES</option>
                                        <option value="20">20 MESES</option>
                                        <option value="21">21 MESES</option>
                                        <option value="22">22 MESES</option>
                                        <option value="23">23 MESES</option>
                                        <option value="24">24 MESES</option>
                                        <option value="25">25 MESES</option>
                                        <option value="26">26 MESES</option>
                                        <option value="27">27 MESES</option>
                                        <option value="28">28 MESES</option>
                                        <option value="29">29 MESES</option>
                                        <option value="30">30 MESES</option>
                                        <option value="31">31 MESES</option>
                                        <option value="32">32 MESES</option>
                                        <option value="33">33 MESES</option>
                                        <option value="34">34 MESES</option>
                                        <option value="35">35 MESES</option>
                                        <option value="36">36 MESES</option>
                                    </select>
                                </div>
                                <div id="cont-ahorroprogramado1" style="display:none;">
                                    <label>Cuota *</label>
                                    <input type="number" value="{{$prestamoahorro->monto}}" id="ahorroprogramado_monto" min="0" step="0.01" onkeyup="creditoCalendario()" onclick="creditoCalendario()"/>
                                    <label>Frecuencia *</label>
                                    <select id="ahorroprogramado_idfrecuencia" onchange="creditoCalendario()">
                                        <option></option>
                                        @foreach($frecuencias as $value)
                                            <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <div id="cont-numerodias" style="display: none">
                                        <label>Número de Días *</label>
                                        <input type="number" value="{{$prestamoahorro->numerodias}}" id="ahorroprogramado_numerodias" value="0" min="0" step="1" onkeyup="creditoCalendario()" onclick="creditoCalendario()"/>
                                    </div>
                                    <label>Número de Cuotas *</label>
                                    <input type="number" value="{{$prestamoahorro->numerocuota}}" id="ahorroprogramado_numerocuota" min="0" step="1" onkeyup="creditoCalendario()" onclick="creditoCalendario()"/>
                                    <label>Fecha de Inicio *</label>
                                    <input type="date" value="{{$prestamoahorro->fechainicio}}" id="ahorroprogramado_fechainicio" onchange="creditoCalendario()"/>
                                </div>
                                <div id="cont-ahorrolibre1" style="display:none;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div id="cont-ahorrofijo2">
                                    <label>Fecha de Inicio *</label>
                                    <input type="date" value="{{$prestamoahorro->fechainicio}}" id="ahorrofijo_fechainicio" onchange="creditoCalendario()"/>
                                    <label>Fecha de Retiro</label>
                                    <input type="date" value="{{$prestamoahorro->fecharetiro}}" id="ahorrofijo_fecharetiro" disabled/>
                                    <label>Tipo de Interes</label>
                                    <select id="ahorrofijo_idtipotasa" disabled>
                                        <option value="1">INTERES FIJA</option>
                                        <option value="2">INTERES EFECTIVA</option>
                                    </select>
                                    <label>Interes % *</label>
                                    <input type="number" value="{{$prestamoahorro->tasa}}" id="ahorrofijo_tasa" min="0" step="0.001" onclick="creditoCalendario()" onkeyup="creditoCalendario()"/>
                                </div>
                                <div id="cont-ahorroprogramado2" style="display:none;">
                                    <label>Fecha de Retiro</label>
                                    <input type="date" value="{{$prestamoahorro->fecharetiro}}" id="ahorroprogramado_fecharetiro" disabled/>
                                    <label>Tipo de Interes</label>
                                    <select id="ahorroprogramado_idtipotasa" disabled>
                                        <option value="1">INTERES FIJA</option>
                                        <option value="2">INTERES EFECTIVA</option>
                                    </select>
                                    <label>Interes % *</label>
                                    <input type="number" value="{{$prestamoahorro->tasa}}" id="ahorroprogramado_tasa" min="0" step="0.001" onclick="creditoCalendario()" onkeyup="creditoCalendario()"/>
                                    <label>Interes Total</label>
                                    <input type="text" value="{{$prestamoahorro->total_interesganado}}" id="ahorroprogramado_total_interesganado" value="0.00" disabled/>
                                    <label>Total a Ahorrar</label>
                                    <input type="text" value="{{$prestamoahorro->total_total}}" id="ahorroprogramado_total" value="0.00" disabled/>
                                </div>
                                <div id="cont-ahorrolibre2" style="display:none;">
                                    <label>Fecha de Inicio *</label>
                                    <input type="date" value="{{$prestamoahorro->fechainicio}}" id="ahorrolibre_fechainicio"/>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div id="cont-ahorrofijo3">
                                    <label>Interes Total</label>
                                    <input type="text" value="{{$prestamoahorro->total_interesganado}}" id="ahorrofijo_total_interesganado" value="0.00" disabled/>
                                    <label>Total a Ahorrar</label>
                                    <input type="text" value="{{$prestamoahorro->total_total}}" id="ahorrofijo_total" value="0.00" disabled/>
                                </div>
                                <div id="cont-ahorroprogramado3" style="display:none;">
                                    <label>Excluir Días:</label>   
                                    <table style="width: 100%;">
                                      <tr>
                                        <td style="text-align: right;padding: 10px;font-weight: bold;">Sábados</td>
                                        <td>
                                          <div class="onoffswitch">
                                              <input type="checkbox" class="onoffswitch-checkbox ahorroprogramado_excluirsabado" id="ahorroprogramado_excluirsabado" onclick="creditoCalendario()">
                                              <label class="onoffswitch-label" for="ahorroprogramado_excluirsabado">
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
                                              <input type="checkbox" class="onoffswitch-checkbox ahorroprogramado_excluirdomingo" id="ahorroprogramado_excluirdomingo" onclick="creditoCalendario()">
                                              <label class="onoffswitch-label" for="ahorroprogramado_excluirdomingo">
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
                                              <input type="checkbox" class="onoffswitch-checkbox ahorroprogramado_excluirferiado" id="ahorroprogramado_excluirferiado" onclick="creditoCalendario()">
                                              <label class="onoffswitch-label" for="ahorroprogramado_excluirferiado">
                                                  <span class="onoffswitch-inner"></span>
                                                  <span class="onoffswitch-switch"></span>
                                              </label> 
                                          </div>
                                        </td>
                                      </tr>
                                    </table>
                                </div>
                                <div id="cont-ahorrolibre3" style="display:none;">
                                @if(configuracion($tienda->id,'prestamo_ahorro_tipoahorrolibre')['resultado']=='CORRECTO')
                                <label>Tipo de Ahorro Libre *</label>
                                <select id="ahorrolibre_tiponombre">
                                    <option></option>
                                    <option value="NINGUNO">NINGUNO</option>
                                    @foreach(json_decode(configuracion($tienda->id,'prestamo_ahorro_tipoahorrolibre')['valor']) as $value)
                                        <option value="{{$value->tipoahorrolibre}}">{{$value->tipoahorrolibre}}</option>
                                    @endforeach
                                </select>
                                <div id="cont-ahorrolibre_tiponombre" style="display:none;">
                                <label>Monto a Ahorrar *</label>
                                <input type="number" value="{{$prestamoahorro->ahorrolibre_monto}}" step="0.001" id="ahorrolibre_monto"/>
                                <label>Producto a Ahorrar *</label>
                                <input type="text" value="{{$prestamoahorro->ahorrolibre_producto}}" id="ahorrolibre_producto" onkeyup="texto_mayucula(this)"/>
                                </div>
                                @endif
                                </div>
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
          <div id="tab-detalle-cliente-7" class="tab-content" style="display: none;">
            <div id="cont-expediente"></div>
          </div>
        </div>
      </div>  
</div>       
<style>

  .resultado-aprobado {
    background-color: #179a4f;
    padding: 5px;
    border-radius: 5px;
    color: rgb(255 255 255);
    font-weight: bold;
    font-size: 20px;
    margin-bottom: 5px;
    float: left;
    width: 100%;
  }
</style>
@endsection

@section('subscripts')
<!-- Tabulador de pestañas -->
<script>
  tab({click:'#tab-detalle-cliente'});
</script>

<!-- Detalle de Credito y Cronograma -->
<script>
    $('#tipocreditonombre').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1,
    }).on("change", function(e) {
        $('#cont-ahorrofijo1').css('display','none');
        $('#cont-ahorrofijo2').css('display','none');
        $('#cont-ahorrofijo3').css('display','none');
        $('#cont-ahorroprogramado1').css('display','none');
        $('#cont-ahorroprogramado2').css('display','none');
        $('#cont-ahorroprogramado3').css('display','none');
        $('#cont-ahorrolibre1').css('display','none');
        $('#cont-ahorrolibre2').css('display','none');
        $('#cont-ahorrolibre3').css('display','none');
        if(e.currentTarget.value==1){
        $('#cont-ahorrofijo1').css('display','block');
        $('#cont-ahorrofijo2').css('display','block');
        $('#cont-ahorrofijo3').css('display','block');
        }
        else if(e.currentTarget.value==2){
        $('#cont-ahorroprogramado1').css('display','block');
        $('#cont-ahorroprogramado2').css('display','block');
        $('#cont-ahorroprogramado3').css('display','block');
        }
        else if(e.currentTarget.value==3){
        $('#cont-ahorrolibre1').css('display','block');
        $('#cont-ahorrolibre2').css('display','block');
        $('#cont-ahorrolibre3').css('display','block');
        }
    }).val('{{$prestamoahorro->idprestamo_tipoahorro}}').trigger("change");
  
    // AHORRO LIBRE
    @if($prestamoahorro->ahorrolibre_tiponombre!='')
    $('#ahorrolibre_tiponombre').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1,
    }).on("change", function(e) {
        if (e.currentTarget.value == 'NINGUNO') {
            $('#cont-ahorrolibre_tiponombre').css({'display':'none'});
        }else{
            $('#cont-ahorrolibre_tiponombre').css('display', 'block');
        }
    }).val('{{$prestamoahorro->ahorrolibre_tiponombre}}').trigger("change");
    @else
    $('#ahorrolibre_tiponombre').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1,
    }).on("change", function(e) {
        if (e.currentTarget.value == 'NINGUNO') {
            $('#cont-ahorrolibre_tiponombre').css({'display':'none'});
        }else{
            $('#cont-ahorrolibre_tiponombre').css('display', 'block');
        }
    });
    @endif
  
    $('#idconyuge').select2({
        @include('app.select2_cliente')
    });
    $('#idbeneficiario').select2({
        @include('app.select2_cliente')
    });
    $('#idcliente').select2({
        @include('app.prestamo_select2_cliente')
    })
  
@if($prestamoahorro->idprestamo_tipoahorro==1)
  
    $('#ahorrofijo_tiempo').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1,
    }).val({{ $prestamoahorro->tiempo }}).trigger("change");
  
    $('#ahorrofijo_idtipotasa').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1,
    }).val({{ $prestamoahorro->idprestamo_tipotasa }}).trigger("change");
  
@elseif($prestamoahorro->idprestamo_tipoahorro==2)
  @if ($prestamoahorro->excluirsabado == "on")
      $('#ahorroprogramado_excluirsabado').prop("checked", true);
  @endif
  @if ($prestamoahorro->excluirdomingo == "on")
      $('#ahorroprogramado_excluirdomingo').prop("checked", true);
  @endif
  @if ($prestamoahorro->excluirferiado == "on")
      $('#ahorroprogramado_excluirferiado').prop("checked", true);
  @endif
  
    $('#ahorroprogramado_idfrecuencia').select2({
        placeholder: '-- Seleccionar Frecuencia --',
        minimumResultsForSearch: -1,
    }).on("change", function(e) {
        var frecuencia = $('#ahorroprogramado_idfrecuencia').val();
        $('#numerodias').val('{{$prestamoahorro->numerodias}}');
        $('#cont-numerodias').css('display', 'none');
        if (frecuencia == 5) {
            $('#cont-numerodias').css({'display':'block'});
        }
    }).val('{{$prestamoahorro->idprestamo_frecuencia}}').trigger("change");
  
    $('#ahorroprogramado_idtipotasa').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1,
    }).val({{ $prestamoahorro->idprestamo_tipotasa }}).trigger("change");

  
@elseif($prestamoahorro->idprestamo_tipoahorro==3)
@endif

  // Mostrando avales en el credito
  $("#check_idconyuge").click(function(){
      $('#cont-conyuge').css('display','none');
          $('#idconyuge').html('<option></option>');
      var checked = $("#check_idconyuge:checked").val();
      if(checked=='on'){
          $('#cont-conyuge').css('display','block');
      }
  });
  $("#check_idbeneficiario").click(function(){
      $('#cont-beneficiario').css('display','none');
          $('#idbeneficiario').html('<option></option>');
      var checked = $("#check_idbeneficiario:checked").val();
      if(checked=='on'){
          $('#cont-beneficiario').css('display','block');
      }
  });

  function creditoCalendario() {
      
        var tipocreditonombre = $('#tipocreditonombre').val();
        if(tipocreditonombre==1){
            var monto           = $('#ahorrofijo_monto').val();
            var numerocuota     = $('#ahorrofijo_tiempo').val();
            var fechainicio     = $('#ahorrofijo_fechainicio').val();
            var frecuencia      = 4;
            var numerodias      = 0;
            var tasa            = $('#ahorrofijo_tasa').val();
            var excluirsabado   = '';
            var excluirdomingo  = '';
            var excluirferiado  = '';

            if(monto=='' || numerocuota=='' || fechainicio=='' || tasa==''){
                return false;
            }
        } 
        else if(tipocreditonombre==2){
            var monto           = $('#ahorroprogramado_monto').val();
            var numerocuota     = $('#ahorroprogramado_numerocuota').val();
            var fechainicio     = $('#ahorroprogramado_fechainicio').val();
            var frecuencia      = $('#ahorroprogramado_idfrecuencia').val();
            var numerodias      = $('#ahorroprogramado_numerodias').val();
            var tasa            = $('#ahorroprogramado_tasa').val();
            var excluirsabado   = $('#ahorroprogramado_excluirsabado:checked').val();
            var excluirdomingo  = $('#ahorroprogramado_excluirdomingo:checked').val();
            var excluirferiado  = $('#ahorroprogramado_excluirferiado:checked').val();

            if(monto=='' || numerocuota=='' || fechainicio=='' || frecuencia=='' || tasa==''){
                return false;
            }
            else if(frecuencia==5 && numerodias==''){
                return false;
            }
            else if(frecuencia==5 && numerodias==0){
                return false;
            }
        }
        else if(tipocreditonombre==3){
        }
            
        if(tipocreditonombre==1 || tipocreditonombre==2){
            $.ajax({
                url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamo/ahorrosolicitud/show-creditocalendario') }}",
                type: 'GET',
                data: {
                    tipoahorro: tipocreditonombre,
                    monto: monto,
                    numerocuota: numerocuota,
                    fechainicio: fechainicio,
                    frecuencia: frecuencia,
                    numerodias: numerodias,
                    tasa: tasa,
                    excluirsabado: excluirsabado,
                    excluirdomingo: excluirdomingo,
                    excluirferiado: excluirferiado,
                },
                beforeSend: function (data) {
                    load('#cont-load-creditocalendario');
                },
                success: function (res) {
                    if(res['resultado']=='CORRECTO'){
                        $('#ahorrofijo_fecharetiro').val(res['fecharetiro']);
                        $('#ahorrofijo_total_interesganado').val(res['total_interesganado']);
                        $('#ahorrofijo_total').val(res['total_total']);
                        $('#ahorroprogramado_fecharetiro').val(res['fecharetiro']);
                        $('#ahorroprogramado_total_interesganado').val(res['total_interesganado']);
                        $('#ahorroprogramado_total').val(res['total_total']);
                        $('#cont-load-creditocalendario').html(res['html']);
                    }else{
                        $('#cont-load-creditocalendario').html(res['html']);
                    }

                }
            });
        }
            
    }
  // fin
</script>

<!-- Datos generales del cliente -->
<script>
    expediente_index();
    function expediente_index(){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorrosolicitud/{{ $prestamoahorro->id }}/edit?view=expediente',result:'#cont-expediente'});
    }
    function expedientedetalle_index(idcredito){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorrosolicitud/'+idcredito+'/edit?view=expedientedetalle',result:'#cont-expedientedetalle'});
    }
    function expedientedetalle_editar(idcredito){
        pagina({route:'{{ url('backoffice/tienda/sistema') }}/{{$tienda->id}}/prestamo/ahorrosolicitud/'+idcredito+'/edit?view=expedientedetalleeditar',result:'#cont-expedientedetalle'});
    }
</script>       
@endsection