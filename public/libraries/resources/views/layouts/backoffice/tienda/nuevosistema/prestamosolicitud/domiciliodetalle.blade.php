<div class="list-single-main-wrapper fl-wrap">
                        <div class="breadcrumbs gradient-bg fl-wrap">
                          <span>Detalle de Domicilio</span>
                          <a class="btn btn-success" href="javascript:;" onclick="domicilio_index()"><i class="fa fa-angle-left"></i> Atras</a></a>
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Dirección</label>
                                <input type="text" value="{{$prestamodomicilio->direccion}}" id="domicilio_detalle_direccion" disabled>
                                <label>Ubigeo</label>
                                <input type="text" value="{{$prestamodomicilio->nombre_ubigeo}}" id="domicilio_detalle_nombre_ubigeo" disabled>
                                <label>Referencia</label>
                                <input type="text" value="{{$prestamodomicilio->referencia}}" id="domicilio_detalle_referencia" disabled>
                            </div>
                            <div class="col-sm-6">
                                <label>Reside Desde (mes/año)*</label>
                                <div class="row">
                                  <div class="col-sm-6">
                                      <select id="domicilio_detalle_reside_desdemes" disabled>
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
                                      <input type="number" value="{{$prestamodomicilio->reside_desdeanio}}" id="domicilio_detalle_reside_desdeanio" min="1" step="1" disabled>
                                  </div>
                                </div>
                                <label>Hora Ubicación (Desde - Hasta)</label>
                                <div class="row">
                                  <div class="col-sm-6">
                                    <input type="time" value="{{$prestamodomicilio->horaubicacion_de}}" id="domicilio_detalle_horaubicacion_de" disabled>
                                  </div>
                                  <div class="col-sm-6">
                                    <input type="time" value="{{$prestamodomicilio->horaubicacion_hasta}}" id="domicilio_detalle_horaubicacion_hasta" disabled>
                                  </div>
                                </div>

                                <label>Tipo de Propiedad</label>
                                <select id="domicilio_detalle_idtipopropiedad" disabled>
                                    <option></option>
                                    <option value="1">Alquilado</option>
                                    <option value="2">Familiar</option>
                                    <option value="3">Propio</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label>Ubicación (Mover Marcador)</label>
                                <div id="domicilio_detalle_mapa" style="height: 386px;width: 100%;margin-bottom: 5px;"></div>
                            </div>
                        </div>
           <script>
              $('#domicilio_detalle_idtipopropiedad').select2({
                  placeholder: '-- Seleccionar Tipo de Propiedad --',
                  minimumResultsForSearch: -1
              }).val({{$prestamodomicilio->idtipopropiedad}}).trigger('change');
              $('#domicilio_detalle_reside_desdemes').select2({
                  placeholder: '-- Seleccionar Mes --',
                  minimumResultsForSearch: -1
              }).val({{$prestamodomicilio->reside_desdemes}}).trigger('change');
              
              singleMap({
                  'map' : '#domicilio_detalle_mapa',
                  'lat' : parseFloat({{$prestamodomicilio->mapa_latitud}}),
                  'lng' : parseFloat({{$prestamodomicilio->mapa_longitud}}),
                  'result_lat' : '#domicilio_detalle_mapa_latitud',
                  'draggable' : false
              });
</script>