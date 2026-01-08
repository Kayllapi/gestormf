<div class="list-single-main-wrapper fl-wrap">
                        <div class="breadcrumbs gradient-bg fl-wrap">
                          <span>Registrar Laboral</span>
                          <a class="btn btn-success" href="javascript:;" onclick="laboral_index()"><i class="fa fa-angle-left"></i> Atras</a></a>
                        </div>
                    </div>
                    <form action="javascript:;" 
                          onsubmit="callback({
                                    route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud',
                                    method: 'POST',
                                    data:   {
                                        view: 'registrar-laboral',
                                        idprestamo_credito: {{ $prestamocredito->id }}
                                    }
                                },
                                function(resultado){
                                    laboral_index();
                                    resultado_index();
                                },this)">
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Fuente de Ingreso *</label>
                            <select id="laboral_idfuenteingreso">
                                <option></option>
                                <option value="1">Dependiente</option>
                                <option value="2">Independiente</option>
                            </select>

                            <label>Giro *</label>
                            <select id="laboral_idprestamo_giro" onchange="cargarActividad('laboral_idprestamo_giro', 'laboral_idprestamo_actividad')">
                                <option></option>
                                @foreach ($giro as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>

                            <label>Actividad *</label>
                            <select id="laboral_idprestamo_actividad" disabled>
                                <option></option>
                            </select>

                            
                            <label>Labora Desde (mes / año) *</label>
                            <div class="row">
                              <div class="col-sm-6">
                                  <select id="laboral_labora_desdemes">
                                      <option></option>
                                      <option value="1">Enero</option>
                                      <option value="2">Febrero</option>
                                      <option value="3">Marzo</option>
                                      <option value="4">Abril</option>
                                      <option value="5">Mayo</option>
                                      <option value="6">Junio</option>
                                      <option value="7">Julio</option>
                                      <option value="8">Agosto</option>
                                      <option value="9">Septiembre</option>
                                      <option value="10">Octubre</option>
                                      <option value="11">Noviembre</option>
                                      <option value="12">Diciembre</option>
                                  </select>
                              </div>
                              <div class="col-sm-6">
                                  <input type="number" id="laboral_labora_desdeanio" min="1" step="1">
                              </div>
                            </div>
                          
                            <label>Días Laborables:</label>
                            <div class="row">
                                <div class="col-sm-3">
                                    <table style="width: 100%;">
                                          <tr>
                                            <td style="text-align: right;padding: 10px;font-weight: bold;">Lunes</td>
                                            <td>
                                              <div class="onoffswitch">
                                                  <input type="checkbox" class="onoffswitch-checkbox seleccionar_lunes" id="seleccionar_lunes" checked>
                                                  <label class="onoffswitch-label" for="seleccionar_lunes">
                                                      <span class="onoffswitch-inner"></span>
                                                      <span class="onoffswitch-switch"></span>
                                                  </label> 
                                              </div>
                                            </td>
                                          </tr>
                                          <tr>
                                            <td style="text-align: right;padding: 10px;font-weight: bold;">Martes</td>
                                            <td>
                                              <div class="onoffswitch">
                                                  <input type="checkbox" class="onoffswitch-checkbox seleccionar_martes" id="seleccionar_martes" checked>
                                                  <label class="onoffswitch-label" for="seleccionar_martes">
                                                      <span class="onoffswitch-inner"></span>
                                                      <span class="onoffswitch-switch"></span>
                                                  </label> 
                                              </div>
                                            </td>
                                          </tr>
                                        </table>
                                </div>
                                <div class="col-sm-3">
                                    <table style="width: 100%;">
                                          <tr>
                                            <td style="text-align: right;padding: 10px;font-weight: bold;">Miercoles</td>
                                            <td>
                                              <div class="onoffswitch">
                                                  <input type="checkbox" class="onoffswitch-checkbox seleccionar_miercoles" id="seleccionar_miercoles" checked>
                                                  <label class="onoffswitch-label" for="seleccionar_miercoles">
                                                      <span class="onoffswitch-inner"></span>
                                                      <span class="onoffswitch-switch"></span>
                                                  </label> 
                                              </div>
                                            </td>
                                          </tr>
                                          <tr>
                                            <td style="text-align: right;padding: 10px;font-weight: bold;">Jueves</td>
                                            <td>
                                              <div class="onoffswitch">
                                                  <input type="checkbox" class="onoffswitch-checkbox seleccionar_jueves" id="seleccionar_jueves" checked>
                                                  <label class="onoffswitch-label" for="seleccionar_jueves">
                                                      <span class="onoffswitch-inner"></span>
                                                      <span class="onoffswitch-switch"></span>
                                                  </label> 
                                              </div>
                                            </td>
                                          </tr>
                                        </table>
                                </div>
                                <div class="col-sm-3">
                                    <table style="width: 100%;">
                                          <tr>
                                            <td style="text-align: right;padding: 10px;font-weight: bold;">Viernes</td>
                                            <td>
                                              <div class="onoffswitch">
                                                  <input type="checkbox" class="onoffswitch-checkbox seleccionar_viernes" id="seleccionar_viernes" checked>
                                                  <label class="onoffswitch-label" for="seleccionar_viernes">
                                                      <span class="onoffswitch-inner"></span>
                                                      <span class="onoffswitch-switch"></span>
                                                  </label> 
                                              </div>
                                            </td>
                                          </tr>
                                          <tr>
                                            <td style="text-align: right;padding: 10px;font-weight: bold;">Sábados</td>
                                            <td>
                                              <div class="onoffswitch">
                                                  <input type="checkbox" class="onoffswitch-checkbox seleccionar_sabados" id="seleccionar_sabados" checked>
                                                  <label class="onoffswitch-label" for="seleccionar_sabados">
                                                      <span class="onoffswitch-inner"></span>
                                                      <span class="onoffswitch-switch"></span>
                                                  </label> 
                                              </div>
                                            </td>
                                          </tr>
                                        </table>
                                </div>
                                <div class="col-sm-3">
                                    <table style="width: 100%;">
                                          <tr>
                                            <td style="text-align: right;padding: 10px;font-weight: bold;">Domingos</td>
                                            <td>
                                              <div class="onoffswitch">
                                                  <input type="checkbox" class="onoffswitch-checkbox seleccionar_domingos" id="seleccionar_domingos" checked>
                                                  <label class="onoffswitch-label" for="seleccionar_domingos">
                                                      <span class="onoffswitch-inner"></span>
                                                      <span class="onoffswitch-switch"></span>
                                                  </label> 
                                              </div>
                                            </td>
                                          </tr>
                                        </table>
                                </div>
                            </div>
                            <label>Ubigeo *</label>
                            <select id="laboral_idubigeo">
                                <option></option>
                                @foreach ($ubigeo as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                            <label>Dirección *</label>
                            <input type="text" id="laboral_direccion" value="{{ $prestamocredito->clientedireccion ?? '' }}">
                            <label>Referencia</label>
                            <input type="text" id="laboral_referencia" value="{{ $prestamocredito->clientereferencia ?? '' }}">
                        </div>
                        <div class="col-sm-6">
                            <label>Ubicación (Mapa)</label>
                            <div id="laboral_mapa" style="height: 550px;width: 100%;margin-bottom: 5px;border-radius: 5px;border: 1px solid #aaaaaa;"></div>
                            <input type="hidden" value="-12.071871667822409" id="laboral_mapa_latitud"/>
                            <input type="hidden" value="-75.21026847919165" id="laboral_mapa_longitud"/>
                        </div>
                    </div>
                    <button type="submit" class="btn mx-btn-post">Guardar Laboral</button>
                    </form>

<script>
$('#laboral_idfuenteingreso').select2({
            placeholder: '-- Seleccionar Ubigeo --',
            minimumResultsForSearch: -1
        });
        $('#laboral_idprestamo_giro').select2({
            placeholder: '-- Seleccionar Ubigeo --',
            minimumResultsForSearch: -1
        });
        $('#laboral_idprestamo_actividad').select2({
            placeholder: '-- Seleccionar Ubigeo --',
            minimumResultsForSearch: -1
        });      
        $('#laboral_idubigeo').select2({
            placeholder: '-- Seleccionar Ubigeo --',
            minimumResultsForSearch: -1,
            minimumInputLength: 2
        }).val({{ $prestamocredito->clienteidubigeo }}).trigger('change');
        $('#laboral_labora_desdemes').select2({
            placeholder: '-- Seleccionar Ubigeo --',
            minimumResultsForSearch: -1
        });
  function cargarActividad(inputGiro, inputActividad, idactividad = null) {
        $('#'+inputActividad).prop('disabled', true);
        if($('#'+inputGiro).val()!=''){
            $('#'+inputActividad).html('');
            $.ajax({
                url:  "{{ url('backoffice/tienda/sistema/'.$tienda->id.'/prestamosolicitud/show-actividad') }}",
                type: 'GET',
                data: {
                    idprestamo_giro: $('#'+inputGiro).val(),
                },
                success: function (res) {
                    $('#'+inputActividad).prop('disabled', false);
                    $('#'+inputActividad).html(res['actividades']);
                  
                    if (idactividad != null) {
                        $('#'+inputActividad).select2({
                            placeholder: '-- Seleccionar Actividad --',
                            minimumResultsForSearch: -1
                        }).val(idactividad).trigger('change');
                    }
                }
            });
        }
            
    }
  
    singleMap({
        'map' : '#laboral_mapa',
        'lat' : -12.071871667822409,
        'lng' : -75.21026847919165,
        'result_lat' : '#laboral_mapa_latitud',
        'result_lng' : '#laboral_mapa_longitud'
    });
</script>