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
                        <!--div class="col-md-3">
                            <a href="javascript:;" id="modal-registrarcliente" class="btn btn-warning"><i class="fa fa-plus"></i> Agregar</a>
                        </div-->
                    </div>
                    <table style="margin-bottom: 5px;">
                      <tr>
                        <td style="text-align: left;padding-right: 10px;">Participar con Cónyuge *</td>
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
                    <div class="list-single-main-wrapper fl-wrap">
                        <div class="breadcrumbs gradient-bg fl-wrap">
                          <span>Crédito</span>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
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
                            <label>Tasa *</label>
                            <select id="idtasa" onchange="creditoCalendario()" <?php echo $configuracion_prestamo['idestadotasa']==2?'disabled':'' ?>>
                                <option></option>
                                <option value="1">Interes Fija</option>
                                <option value="2">Interes Efectiva</option>
                            </select>
                            <label>Interes % *</label>
                            <input type="number" id="tasa" min="0" step="0.01" onclick="creditoCalendario()" onkeyup="creditoCalendario()"/>
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
@endsection

@section('htmls')
@include('app.modal_usuario_registrar',[
    'nombre'    =>'Registrar Cliente',
    'modal'     =>'registrarcliente',
    'idusuario' =>'idcliente',
])
@endsection

@section('subscripts')
<script>
    $('#idfrecuencia').select2({
        placeholder: '-- Seleccionar Frecuencia --',
        minimumResultsForSearch: -1,
    });
    $('#idtasa').select2({
        placeholder: '-- Seleccionar Tasa --',
        minimumResultsForSearch: -1,
    }).val({{$configuracion_prestamo['idtasapordefecto']}}).trigger('change');
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
    $("#check_idconyuge").click(function() {
        $('#cont-conyuge').css('display','none');
        var checked = $("#check_idconyuge:checked").val();
        if(checked=='on'){
            $('#cont-conyuge').css('display','block');
            $('#idconyuge').html('<option></option>');
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