                  <div class="list-single-main-wrapper fl-wrap">
                        <div class="breadcrumbs gradient-bg fl-wrap">
                          <span>Editar Domicilio</span>
                          <a class="btn btn-success" href="javascript:;" onclick="domicilio_index()"><i class="fa fa-angle-left"></i> Atras</a></a>
                        </div>
                    </div>
                        <form action="javascript:;" 
                              onsubmit="callback({
                                    route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}',
                                    method: 'PUT',
                                    data:   {
                                        view: 'editar-domicilio',
                                        idprestamo_creditodomicilio: {{$prestamodomicilio->id}}
                                    }
                                },
                                function(resultado){
                                    domicilio_index();
                                },this)">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label>Dirección</label>
                                    <input type="text" value="{{$prestamodomicilio->direccion}}" id="domicilio_editar_direccion">
                                    <label>Ubigeo</label>
                                    <select id="domicilio_editar_idubigeo">
                                        <option></option>
                                        @foreach ($ubigeo as $value)
                                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <label>Referencia</label>
                                    <input type="text" value="{{$prestamodomicilio->referencia}}" id="domicilio_editar_referencia">
                                </div>
                                <div class="col-sm-6">
                                    <label>Reside Desde (mes/año)*</label>
                                    <div class="row">
                                      <div class="col-sm-6">
                                          <select id="domicilio_editar_reside_desdemes">
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
                                          <input type="number" value="{{$prestamodomicilio->reside_desdeanio}}" id="domicilio_editar_reside_desdeanio" min="1" step="1">
                                      </div>
                                    </div>
                                    <label>Hora Ubicación (Desde - Hasta)</label>
                                    <div class="row">
                                      <div class="col-sm-6">
                                        <input type="time" value="{{$prestamodomicilio->horaubicacion_de}}" id="domicilio_editar_horaubicacion_de">
                                      </div>
                                      <div class="col-sm-6">
                                        <input type="time" value="{{$prestamodomicilio->horaubicacion_hasta}}" id="domicilio_editar_horaubicacion_hasta">
                                      </div>
                                    </div>

                                    <label>Tipo de Propiedad</label>
                                    <select id="domicilio_editar_idtipopropiedad">
                                        <option></option>
                                        <option value="1">Alquilado</option>
                                        <option value="2">Familiar</option>
                                        <option value="3">Propio</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label>Ubicación (Mover Marcador)</label>
                                    <div id="domicilio_editar_mapa" style="height: 386px;width: 100%;margin-bottom: 5px;"></div>
                                    <input type="hidden" value="{{$prestamodomicilio->mapa_latitud}}" id="domicilio_editar_mapa_latitud"/>
                                    <input type="hidden" value="{{$prestamodomicilio->mapa_longitud}}" id="domicilio_editar_mapa_longitud"/>
                                </div>
                            </div>
                            <button type="submit" class="btn mx-btn-post">Actualizar Dirección</button>
                        </form>

<script>
              $('#domicilio_editar_idubigeo').select2({
                  placeholder: '-- Seleccionar Ubigeo --',
                  minimumResultsForSearch: -1,
                  minimumInputLength: 2
              }).val({{$prestamodomicilio->idubigeo}}).trigger('change');
              $('#domicilio_editar_idtipopropiedad').select2({
                  placeholder: '-- Seleccionar Tipo de Propiedad --',
                  minimumResultsForSearch: -1
              }).val({{$prestamodomicilio->idtipopropiedad}}).trigger('change');
              $('#domicilio_editar_reside_desdemes').select2({
                  placeholder: '-- Seleccionar Mes --',
                  minimumResultsForSearch: -1
              }).val({{$prestamodomicilio->reside_desdemes}}).trigger('change');
  
              singleMap({
                  'map' : '#domicilio_editar_mapa',
                  'lat' : parseFloat({{$prestamodomicilio->mapa_latitud}}),
                  'lng' : parseFloat({{$prestamodomicilio->mapa_longitud}}),
                  'result_lat' : '#domicilio_editar_mapa_latitud',
                  'result_lng' : '#domicilio_editar_mapa_longitud'
              });
</script>