@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Registrar Solicitud de Crédito',
    'botones'=>[
        'atras:/'.$tienda->id.'/prestamosolicitud: Ir Atras'
    ]
])
    <form action="javascript:;"
          onsubmit="callback({
                                route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud',
                                method: 'POST',
                                data:   {
                                    view: 'registrar'
                                }
                            },
                            function(resultado){
                                location.href = '{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud') }}';
                            },this)">
              <div class="row">
                <div class="col-sm-6">
                    <label>Cliente *</label>
                    <div class="row">
                        <div class="col-md-12">
                            <select id="idcliente">
                                <option></option>
                            </select>
                        </div>
                    </div>
                    <table style="margin-bottom: 5px;">
                      <tr>
                        <td style="text-align: left;padding-right: 10px;">Participar con Cónyugue *</td>
                        <td>
                          <div class="onoffswitch">
                              <input type="checkbox" class="onoffswitch-checkbox check_idconyuge" id="check_idconyuge">
                              <label class="onoffswitch-label" for="check_idconyuge">
                                  <span class="onoffswitch-inner"></span>
                                  <span class="onoffswitch-switch"></span>
                              </label> 
                          </div>
                        </td>
                      </tr>
                    </table>
                    <div style="display: none;" id="cont-conyuge">
                        <select id="idconyuge">
                                <option></option>
                        </select>
                    </div>
                    <table style="margin-bottom: 5px;">
                      <tr>
                        <td style="text-align: left;padding-right: 10px;">Participar con Garanteo ó Aval *</td>
                        <td>
                          <div class="onoffswitch">
                              <input type="checkbox" class="onoffswitch-checkbox check_idgarante" id="check_idgarante">
                              <label class="onoffswitch-label" for="check_idgarante">
                                  <span class="onoffswitch-inner"></span>
                                  <span class="onoffswitch-switch"></span>
                              </label> 
                          </div>
                        </td>
                      </tr>
                    </table>
                    <div style="display: none;" id="cont-garante">
                        <select id="idgarante">
                                <option></option>
                        </select>
                    </div>
                    @if(configuracion($tienda->id,'prestamo_estadocreditogrupal')['valor']==1)
                    <table style="margin-bottom: 5px;">
                      <tr>
                        <td style="text-align: left;padding-right: 10px;">Crédito Grupal *</td>
                        <td>
                          <div class="onoffswitch">
                              <input type="checkbox" class="onoffswitch-checkbox check_estadocreditogrupal" id="check_estadocreditogrupal">
                              <label class="onoffswitch-label" for="check_estadocreditogrupal">
                                  <span class="onoffswitch-inner"></span>
                                  <span class="onoffswitch-switch"></span>
                              </label> 
                          </div>
                        </td>
                      </tr>
                    </table>
                    @endif
                    <div class="list-single-main-wrapper fl-wrap">
                        <div class="breadcrumbs gradient-bg fl-wrap">
                          <span>Crédito</span>
                        </div>
                    </div>
                    <div id="cont-estadocreditogrupal0" style="display: none;">
                    <div class="row">
                        <div class="col-md-4">
                            <label>Monto *</label>
                            <input type="number" id="montogrupal" min="0" step="0.01"/>
                      </div>
                    </div>
                    </div> 
                    <div id="cont-estadocreditogrupal1">
                    <div class="row">
                        <div class="col-md-4">
                            @if(configuracion($tienda->id,'prestamo_tipocredito')['resultado']=='CORRECTO')
                            <label>Tipo de Crédito *</label>
                            <select id="tipocreditonombre">
                                <option value="NORMAL">NORMAL</option>
                                @foreach(json_decode(configuracion($tienda->id,'prestamo_tipocredito')['valor']) as $value)
                                    <option value="{{$value->tipocredito}}">{{$value->tipocredito}}</option>
                                @endforeach
                            </select>
                            @endif
                            <label>Monto *</label>
                            <input type="number" id="monto" min="0" step="0.01" onkeyup="creditoCalendario()" onclick="creditoCalendario()"/>
                            <label>Número de Cuotas *</label>
                            <input type="number" id="numerocuota" min="1" step="1" onkeyup="creditoCalendario()" onclick="creditoCalendario()"/>
                            <label>Fecha de Inicio *</label>
                            <input type="date" id="fechainicio" onchange="creditoCalendario()"/>
                            <label>Frecuencia *</label>
                            <select id="idfrecuencia" onchange="creditoCalendario()">
                                <option></option>
                                @foreach($frecuencias as $value)
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                            <div id="cont-numerodias" style="display: none">
                                <label>Número de Días *</label>
                                <input type="number" id="numerodias" value="0" min="0" step="1" onkeyup="creditoCalendario()" onclick="creditoCalendario()"/>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Tipo de Interes</label>
                            <select id="idtipotasa" disabled>
                                <option value="1">INTERES FIJA</option>
                                <option value="2">INTERES EFECTIVA</option>
                            </select>
                            <label>Interes % *</label>
                            <input type="number" id="tasa" min="0" step="0.001" onclick="creditoCalendario()" onkeyup="creditoCalendario()"/>
                            @if(configuracion($tienda->id,'prestamo_estadoabono')['valor']=='on')
                            <label>Abono *</label>
                            <input type="number" id="abono" min="0" step="0.01" onclick="creditoCalendario()" onkeyup="creditoCalendario()"/>
                            @endif
                            <label>Interes Total</label>
                            <input type="text" id="total_interes" value="0.00" disabled/>
                            @if(configuracion($tienda->id,'prestamo_estadoseguro_degravamen')['valor']=='on')
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
                </div>
                <div class="col-sm-6">
                  <div id="cont-creditoparalelo"></div>
                  <div id="cont-estadocreditogrupal2">
                  <div id="cont-load-creditocalendario"></div>
                  </div>
                </div>
              </div>
              <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
    </form>                             
@endsection

@section('subscripts')
<script>
  @if(configuracion($tienda->id,'prestamo_tipocredito')['resultado']=='CORRECTO')
    $('#tipocreditonombre').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1,
    }).val('NORMAL').trigger("change");
  @endif
  
    $('#idfrecuencia').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1,
    });
    $('#idtipotasa').select2({
        placeholder: '-- Seleccionar --',
        minimumResultsForSearch: -1,
    }).val({{configuracion($tienda->id,'prestamo_tasapordefecto')['valor']!=''?configuracion($tienda->id,'prestamo_tasapordefecto')['valor']:1}}).trigger("change");
  
    $('#idconyuge').select2({
    @include('app.select2_cliente')
    });
    $('#idgarante').select2({
    @include('app.select2_cliente')
    });
    $('#idcliente').select2({
        @include('app.prestamo_select2_cliente',['idasesor' => Auth::user()->id])
    }).on("change", function(e) {
        load('#cont-creditoparalelo');
        $.ajax({
            url:"{{url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-creditoparalelo')}}",
            type:'GET',
            data: {
                idcliente : e.currentTarget.value
            },
            success: function (respuesta){
                $('#cont-creditoparalelo').html(respuesta['html']);
            }
        })
    });

    $("#check_idconyuge").click(function() {
        $('#cont-conyuge').css('display','none');
        var checked = $("#check_idconyuge:checked").val();
            $('#idconyuge').html('<option></option>');
        if(checked=='on'){
            $('#cont-conyuge').css('display','block');
        }
    });
    $("#check_idgarante").click(function() {
        $('#cont-garante').css('display','none');
        var checked = $("#check_idgarante:checked").val();
            $('#idgarante').html('<option></option>');
        if(checked=='on'){
            $('#cont-garante').css('display','block');
        }
    });
    $("#check_estadocreditogrupal").click(function() {
        $('#cont-estadocreditogrupal0').css('display','none');
        $('#cont-estadocreditogrupal1').css('display','block');
        $('#cont-estadocreditogrupal2').css('display','block');
        var checked = $("#check_estadocreditogrupal:checked").val();
        if(checked=='on'){
            $('#cont-estadocreditogrupal0').css('display','block');
            $('#cont-estadocreditogrupal1').css('display','none');
            $('#cont-estadocreditogrupal2').css('display','none');
        }
    });
    $("#idfrecuencia").change(function(){
        var frecuencia = $('#idfrecuencia').val();
        $('#numerodias').val(0);
        $('#cont-numerodias').css('display', 'none');
        if (frecuencia == 5) {
            $('#cont-numerodias').css({'display':'block'});
        }
    });

    function creditoCalendario() {
      
        var monto = $('#monto').val();
        var numerocuota = $('#numerocuota').val();
        var fechainicio = $('#fechainicio').val();
        var frecuencia = $('#idfrecuencia').val();
        var numerodias = $('#numerodias').val();
        var tasa = $('#tasa').val();
        var abono = $('#abono').val();
      
        if(abono==undefined){
            abono = 0;
        }
      
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
                tasa: tasa,
                abono: abono,
                excluirsabado: $('#excluirsabado:checked').val(),
                excluirdomingo: $('#excluirdomingo:checked').val(),
                excluirferiado: $('#excluirferiado:checked').val(),
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
    // fin
</script>
@endsection