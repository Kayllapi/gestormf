<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Detalle de Laboral</span>
      <a class="btn btn-success" href="javascript:;" onclick="laboral_index()"><i class="fa fa-angle-left"></i> Atras</a></a>
    </div>
</div>
    <div class="tabs-container" id="tab-laboral">
        <ul class="tabs-menu">
            <li class="current"><a href="#tab-laboral-0">General</a></li>
            @if($prestamolaboral->idfuenteingreso==1 && $prestamolaboral->idprestamo_giro==1)
            <li><a href="#tab-laboral-4">Ingresos</a></li>
            @elseif($prestamolaboral->idfuenteingreso==2 && $prestamolaboral->idprestamo_giro==1)
            <li><a href="#tab-laboral-2">Ventas</a></li>
            <li><a href="#tab-laboral-3">Compras</a></li>
            @elseif(($prestamolaboral->idfuenteingreso==2 && $prestamolaboral->idprestamo_giro==2) || ($prestamolaboral->idfuenteingreso==1 && $prestamolaboral->idprestamo_giro==2))
            <li><a href="#tab-laboral-8">Servicios</a></li>
            @endif
            <li><a href="#tab-laboral-5">Gastos</a></li>
            <li><a href="#tab-laboral-6">Pagos</a></li>
            <li><a href="#tab-laboral-7">Resultado</a></li>
        </ul>
        <div class="tab">
            <div id="tab-laboral-0" class="tab-content" style="display: block;">
                          <div class="col-sm-6">
                            <label>Fuente de Ingreso</label>
                            <input type="text" value="{{$prestamolaboral->fuenteingreso}}" id="laboral_detalle_idfuenteingreso" disabled>
                            <label>Giro</label>
                            <input type="text" value="{{$prestamolaboral->nombre_giro}}" id="laboral_detalle_idprestamo_giro" disabled>
                            <label>Actividad</label>
                            <input type="text" value="{{$prestamolaboral->nombre_actividad}}" id="laboral_detalle_idprestamo_actividad" disabled>
                            <label>Labora Desde (mes / año)</label>
                            <div class="row">
                              <div class="col-sm-6">
                                  <select id="laboral_detalle_labora_desdemes" disabled>
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
                                  <input type="number" value="{{$prestamolaboral->labora_desdeanio}}" id="laboral_detalle_labora_desdeanio" min="1" step="1" disabled>
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
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_lunes" id="seleccionar_lunes" <?php echo $prestamolaboral->labora_lunes == "si"? 'checked':'' ?> disabled>
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
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_martes" id="seleccionar_martes" <?php echo $prestamolaboral->labora_martes == "si"? 'checked':'' ?> disabled>
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
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_miercoles" id="seleccionar_miercoles" <?php echo $prestamolaboral->labora_miercoles == "si"? 'checked':'' ?> disabled>
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
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_jueves" id="seleccionar_jueves" <?php echo $prestamolaboral->labora_jueves == "si"? 'checked':'' ?> disabled>
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
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_viernes" id="seleccionar_viernes" <?php echo $prestamolaboral->labora_viernes == "si"? 'checked':'' ?> disabled>
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
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_sabados" id="seleccionar_sabados" <?php echo $prestamolaboral->labora_sabados == "si"? 'checked':'' ?> disabled>
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
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_domingos" id="seleccionar_domingos" <?php echo $prestamolaboral->labora_domingos == "si"? 'checked':'' ?> disabled>
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
                            <label>Ubigeo</label>
                            <input type="text" value="{{$prestamolaboral->nombre_ubigeo}}" id="laboral_detalle_idubigeo" disabled>
                            <label>Dirección</label>
                            <input type="text" value="{{$prestamolaboral->direccion}}" id="laboral_detalle_direccion" disabled>
                            <label>Referencia</label>
                            <input type="text" value="{{$prestamolaboral->labora_desdeanio}}" id="laboral_detalle_referencia" disabled>
                        </div>
                        <div class="col-sm-6">
                            <label>Ubicación (Mapa)</label>
                            <div id="laboral_detalle_mapa" style="height: 550px;width: 100%;margin-bottom: 5px;border-radius: 5px;border: 1px solid #aaaaaa;"></div>
                        </div>
            </div>
            @if($prestamolaboral->idfuenteingreso==1 && $prestamolaboral->idprestamo_giro==1)
            <div id="tab-laboral-4" class="tab-content" style="display: none;">
                <div class="table-responsive">
                  <table class="table" id="tabla-contenidoproducto-ingreso">
                      <thead class="thead-dark">
                        <tr>
                          <th>Concepto</th>
                          <th width="110px">Monto</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($laboralingreso as $value)
                          <tr>
                            <td style="padding: 10px;">
                             {{ $value->conceptoingresonombre }}
                            </td>
                            <td style="padding: 10px;">
                              {{ $value->monto }}
                            </td>
                          </tr>
                        @endforeach
                    </tbody>
                  </table>
                </div>
                <div class="profile-edit-container">
                  <div class="custom-form">
                    <div class="row">  
                      <div class="col-md-8">
                      </div> 
                      <div class="col-md-4">
                        <label for="">Total</label>
                        <input type="text" value="{{ $prestamolaboral->ingreso }}" disabled>
                      </div>         
                    </div> 
                  </div>
                </div>
            </div>
            @elseif($prestamolaboral->idfuenteingreso==2 && $prestamolaboral->idprestamo_giro==1)
            <div id="tab-laboral-2" class="tab-content" style="display: none;">
                <div class="table-responsive">
                  <table class="table" id="tabla-contenidoproducto-venta">
                      <thead class="thead-dark">
                        <tr>
                          <th>Producto</th>
                          <th width="60px">Cantidad</th>
                          <th width="110px">P. Unitario</th>
                          <th width="110px">Venta Diaria</th>
                          <th width="110px">Venta Semanal</th>
                          <th width="110px">Venta Quincenal</th>
                          <th width="110px">Venta Mensual</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $productUnidadVenta_total = 0;
                        $productTotalVenta_total = 0;
                        $productTotalVenta_Semanal_total = 0;
                        $productTotalVenta_Quincenal_total = 0;
                        $productTotalVenta_Mensual_total = 0;
                        ?>
                        @foreach ($laboralventa as $value)
                          <tr>
                            <td style="padding: 10px;">{{ $value->productonombre }}</td>
                            <td style="padding: 10px;">{{ $value->cantidad }}</td>
                            <td style="padding: 10px;">{{ $value->preciounitario }}</td>
                            <td style="padding: 10px;">{{ $value->preciototal }}</td>
                            <td style="padding: 10px;">{{ $value->preciototal_semanal }}</td>
                            <td style="padding: 10px;">{{ $value->preciototal_quincenal }}</td>
                            <td style="padding: 10px;">{{ $value->preciototal_mensual }}</td>
                          </tr>
                          <?php
                          $productUnidadVenta_total = $productUnidadVenta_total+$value->preciounitario;
                          $productTotalVenta_total = $productTotalVenta_total+$value->preciototal;
                          $productTotalVenta_Semanal_total = $productTotalVenta_Semanal_total+$value->preciototal_semanal;
                          $productTotalVenta_Quincenal_total = $productTotalVenta_Quincenal_total+$value->preciototal_quincenal;
                          $productTotalVenta_Mensual_total = $productTotalVenta_Mensual_total+$value->preciototal_mensual;
                          ?>
                        @endforeach
                      </tbody>
                      <tfoot>
                        <tr>
                          <th></th>
                          <th></th>
                          <th><input value="{{ $productUnidadVenta_total }}" type="number" step="0.01" min="0" disabled></th>
                          <th><input value="{{ $productTotalVenta_total }}" type="number" step="0.01" min="0" disabled></th>
                          <th><input value="{{ $productTotalVenta_Semanal_total }}" type="number" step="0.01" min="0" disabled></th>
                          <th><input value="{{ $productTotalVenta_Quincenal_total }}" type="number" step="0.01" min="0" disabled></th>
                          <th><input value="{{ $productTotalVenta_Mensual_total }}" type="number" step="0.01" min="0" disabled></th>
                          <th></th>
                        </tr>
                      </tfoot>
                  </table>
                </div>
            </div>
            <div id="tab-laboral-3" class="tab-content" style="display: none;">
                <div class="table-responsive">
                  <table class="table" id="tabla-contenidoproducto-compra">
                      <thead class="thead-dark">
                        <tr>
                          <th>Producto</th>
                          <th width="60px">Cantidad</th>
                          <th width="110px">P. Unitario</th>
                          <th width="110px">Venta Diaria</th>
                          <th width="110px">Venta Semanal</th>
                          <th width="110px">Venta Quincenal</th>
                          <th width="110px">Venta Mensual</th>
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $productUnidadCompra_total = 0;
                        $productTotalCompra_total = 0;
                        $productTotalCompra_Semanal_total = 0;
                        $productTotalCompra_Quincenal_total = 0;
                        $productTotalCompra_Mensual_total = 0;
                        ?>
                        @foreach ($laboralcompra as $value)
                          <tr>
                            <td style="padding: 10px;">{{ $value->productonombre }}</td>
                            <td style="padding: 10px;">{{ $value->cantidad }}</td>
                            <td style="padding: 10px;">{{ $value->preciounitario }}</td>
                            <td style="padding: 10px;">{{ $value->preciototal }}</td>
                            <td style="padding: 10px;">{{ $value->preciototal_semanal }}</td>
                            <td style="padding: 10px;">{{ $value->preciototal_quincenal }}</td>
                            <td style="padding: 10px;">{{ $value->preciototal_mensual }}</td>
                          </tr>
                          <?php
                          $productUnidadCompra_total = $productUnidadCompra_total+$value->preciounitario;
                          $productTotalCompra_total = $productTotalCompra_total+$value->preciototal;
                          $productTotalCompra_Semanal_total = $productTotalCompra_Semanal_total+$value->preciototal_semanal;
                          $productTotalCompra_Quincenal_total = $productTotalCompra_Quincenal_total+$value->preciototal_quincenal;
                          $productTotalCompra_Mensual_total = $productTotalCompra_Mensual_total+$value->preciototal_mensual;
                          ?>
                        @endforeach
                      </tbody>
                      <tfoot>
                        <tr>
                          <th></th>
                          <th></th>
                          <th><input value="{{ $productUnidadCompra_total }}" type="number" step="0.01" min="0" disabled></th>
                          <th><input value="{{ $productTotalCompra_total }}" type="number" step="0.01" min="0" disabled></th>
                          <th><input value="{{ $productTotalCompra_Semanal_total }}" type="number" step="0.01" min="0" disabled></th>
                          <th><input value="{{ $productTotalCompra_Quincenal_total }}" type="number" step="0.01" min="0" disabled></th>
                          <th><input value="{{ $productTotalCompra_Mensual_total }}" type="number" step="0.01" min="0" disabled></th>
                          <th></th>
                        </tr>
                      </tfoot>
                  </table>
                </div>
            </div>
            @elseif(($prestamolaboral->idfuenteingreso==2 && $prestamolaboral->idprestamo_giro==2) || ($prestamolaboral->idfuenteingreso==1 && $prestamolaboral->idprestamo_giro==2))
            <div id="tab-laboral-8" class="tab-content" style="display: none;">
                <div class="table-responsive">
                  <table class="table" id="tabla-contenidoproducto-servicio">
                      <thead>
                        <tr>
                          <th style="background-color: #343a40;width:200px;border-color: #343a40;color: #eee;padding: 8px;">Bueno</th>
                          <th style="padding: 8px;">{{$laboralservicio!=''?$laboralservicio->bueno:'0.00'}}</th>
                        </tr>
                        <tr>
                          <th style="background-color: #343a40;border-color: #343a40;color: #eee;padding: 8px;">Regular</th>
                          <th style="padding: 8px;">{{$laboralservicio!=''?$laboralservicio->regular:'0.00'}}</th>
                        </tr>
                        <tr>
                          <th style="background-color: #343a40;border-color: #343a40;color: #eee;padding: 8px;">Malo</th>
                          <th style="padding: 8px;">{{$laboralservicio!=''?$laboralservicio->malo:'0.00'}}</th>
                        </tr>
                        <tr>
                          <th style="background-color: #343a40;border-color: #343a40;color: #eee;padding: 8px;">Promedio</th>
                          <th style="padding: 8px;">{{$laboralservicio!=''?$laboralservicio->promedio:'0.00'}}</th>
                        </tr>
                        <tr>
                          <th style="background-color: #343a40;border-color: #343a40;color: #eee;padding: 8px;">Venta Semanal</th>
                          <th style="padding: 8px;">{{$laboralservicio!=''?$laboralservicio->semanal:'0.00'}}</th>
                        </tr>
                        <tr>
                          <th style="background-color: #343a40;border-color: #343a40;color: #eee;padding: 8px;">Venta Quincenal</th>
                          <th style="padding: 8px;">{{$laboralservicio!=''?$laboralservicio->quincenal:'0.00'}}</th>
                        </tr>
                        <tr>
                          <th style="background-color: #343a40;border-color: #343a40;color: #eee;padding: 8px;">Venta Mensual</th>
                          <th style="padding: 8px;">{{$laboralservicio!=''?$laboralservicio->mensual:'0.00'}}</th>
                        </tr>
                      </thead>
                  </table>
                </div>
            </div>
            @endif
            <div id="tab-laboral-5" class="tab-content" style="display: none;">
                <div class="table-responsive">
                  <table class="table" id="tabla-contenidoproducto-egresogasto">
                      <thead class="thead-dark">
                        <tr>
                          <th>Concepto</th>
                          <th width="110px">Monto</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($laboralegresogasto as $value)
                          <tr>
                            <td style="padding: 8px;">
                              {{ $value->conceptoegresogastonombre }}
                            </td>
                            <td style="padding: 8px;">
                              {{ $value->monto }}
                            </td>
                          </tr>
                        @endforeach
                    </tbody>
                  </table>
                </div>
                <div class="profile-edit-container">
                  <div class="custom-form">
                    <div class="row">  
                      <div class="col-md-8">
                      </div> 
                      <div class="col-md-4">
                        <label for="">Total</label>
                        <input type="text" value="{{ $prestamolaboral->egresogasto }}" disabled>
                      </div>         
                    </div> 
                  </div>
                </div>
            </div>
            <div id="tab-laboral-6" class="tab-content" style="display: none;">
                <div class="table-responsive">
                  <table class="table" id="tabla-contenidoproducto-egresopago">
                      <thead class="thead-dark">
                        <tr>
                          <th>Concepto</th>
                          <th width="110px">Monto</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach ($laboralegresopago as $value)
                          <tr>
                            <td style="padding: 8px;">
                              {{ $value->conceptoegresopagonombre }}
                            </td>
                            <td style="padding: 8px;">
                              {{ $value->monto }}
                            </td>
                          </tr>
                        @endforeach
                    </tbody>
                  </table>
                </div>
                <div class="profile-edit-container">
                  <div class="custom-form">
                    <div class="row">  
                      <div class="col-md-8">
                      </div> 
                      <div class="col-md-4">
                        <label for="">Total</label>
                        <input type="text" value="{{ $prestamolaboral->egresopago }}" disabled>
                      </div>         
                    </div> 
                  </div>
                </div>
            </div>
            <div id="tab-laboral-7" class="tab-content" style="display: none;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                          <table class="table" id="tabla-contenidoproducto-resultado">
                              <tbody>
                                  @if($prestamolaboral->idfuenteingreso==1 && $prestamolaboral->idprestamo_giro==1)
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>(+) Ingresos</b></td>
                                    <td style="padding: 10px;">{{ $prestamolaboral->ingreso }}</td>
                                  </tr>
                                  @elseif($prestamolaboral->idfuenteingreso==2 && $prestamolaboral->idprestamo_giro==1)
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;width:200px;"><b>(+) Ventas</b></td>
                                    <td style="padding: 10px;">{{ $prestamolaboral->venta }}</td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>(-) Compras</b></td>
                                    <td style="padding: 10px;">{{ $prestamolaboral->compra }}</td>
                                  </tr>
                                  @elseif(($prestamolaboral->idfuenteingreso==2 && $prestamolaboral->idprestamo_giro==2) || ($prestamolaboral->idfuenteingreso==1 && $prestamolaboral->idprestamo_giro==2))
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>(+) Servicios</b></td>
                                    <td style="padding: 10px;">{{ $prestamolaboral->servicio }}</td>
                                  </tr>
                                  @endif
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>(-) Gastos</b></td>
                                    <td style="padding: 10px;">{{ $prestamolaboral->egresogasto }}</td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>(-) Pagos</b></td>
                                    <td style="padding: 10px;">{{ $prestamolaboral->egresopago }}</td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>Ingreso Mensual</b></td>
                                    <td style="padding: 10px;">{{ $prestamolaboral->ingresomensual }}</div></td>
                                  </tr>
                            </tbody>
                          </table>
                        </div>
                    </div>
                </div>
                        
            </div>
        </div>
    </div>    
<script>
  tab({click:'#tab-laboral'});
  
  $('#laboral_detalle_labora_desdemes').select2({
                    placeholder: '-- Seleccionar Ubigeo --',
                    minimumResultsForSearch: -1
                }).val({{$prestamolaboral->labora_desdemes}}).trigger('change');
  singleMap({
      'map' : '#laboral_detalle_mapa',
      'lat' : parseFloat({{$prestamolaboral->mapa_latitud}}),
      'lng' : parseFloat({{$prestamolaboral->mapa_longitud}}),
      'result_lat' : '#laboral_detalle_mapa_latitud',
      'result_lng' : '#laboral_detalle_mapa_longitud',
      'draggable' : false
  });
</script>