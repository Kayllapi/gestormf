                    <div class="list-single-main-wrapper fl-wrap">
                        <div class="breadcrumbs gradient-bg fl-wrap">
                          <span>Registrar Domicilio</span>
                          <a class="btn btn-success" href="javascript:;" onclick="domicilio_index()"><i class="fa fa-angle-left"></i> Atras</a></a>
                        </div>
                    </div>
                    <form action="javascript:;" 
                          onsubmit="callback({
                                route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud',
                                method: 'POST',
                                data:   {
                                    view: 'registrar-domicilio',
                                    idprestamo_credito: {{ $prestamocredito->id }}
                                }
                            },
                            function(resultado){
                                domicilio_index();
                            },this)">
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Ubigeo *</label>
                                <select id="domicilio_idubigeo">
                                    <option></option>
                                    @foreach ($ubigeo as $value)
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                    @endforeach
                                </select>
                                <label>Dirección *</label>
                                <input type="text" id="domicilio_direccion" value="{{ $prestamocredito->clientedireccion }}">
                                <label>Referencia</label>
                                <input type="text" id="domicilio_referencia" value="{{ $prestamocredito->clientereferencia }}">
                            </div>
                            <div class="col-sm-6">
                                <label>Reside Desde (mes/año)*</label>
                                <div class="row">
                                  <div class="col-sm-6">
                                      <select id="domicilio_reside_desdemes">
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
                                      <input type="number" id="domicilio_reside_desdeanio" min="1" step="1">
                                  </div>
                                </div>
                                <label>Hora Ubicación (Desde - Hasta) *</label>
                                <div class="row">
                                  <div class="col-sm-6">
                                    <input type="time" id="domicilio_horaubicacion_de">
                                  </div>
                                  <div class="col-sm-6">
                                    <input type="time" id="domicilio_horaubicacion_hasta">
                                  </div>
                                </div>

                                <label>Tipo de Propiedad *</label>
                                <select id="domicilio_idtipopropiedad">
                                    <option></option>
                                    <option value="1">Alquilado</option>
                                    <option value="2">Familiar</option>
                                    <option value="3">Propio</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label>Ubicación (Mover Marcador)</label>
                                <div id="domicilio_mapa" style="height: 386px;width: 100%;margin-bottom: 5px;"></div>
                                <input type="hidden" value="-12.071871667822409" id="domicilio_mapa_latitud"/>
                                <input type="hidden" value="-75.21026847919165" id="domicilio_mapa_longitud"/>
                            </div>
                        </div>
                        <button type="submit" class="btn mx-btn-post">Guardar Dirección</button>
                  </form>
<script>
    $('#domicilio_idubigeo').select2({
        placeholder: '-- Seleccionar Ubigeo --',
        minimumResultsForSearch: -1,
        minimumInputLength: 2
    }).val("{{ $prestamocredito->clienteidubigeo }}").trigger('change');
    $('#domicilio_idtipopropiedad').select2({
        placeholder: '-- Seleccionar Ubigeo --',
        minimumResultsForSearch: -1
    });
    $('#domicilio_reside_desdemes').select2({
        placeholder: '-- Seleccionar Mes --',
        minimumResultsForSearch: -1
    });
  
    singleMap({
        'map' : '#domicilio_mapa',
        'lat' : -12.071871667822409,
        'lng' : -75.21026847919165,
        'result_lat' : '#domicilio_mapa_latitud',
        'result_lng' : '#domicilio_mapa_longitud'
    });
</script>