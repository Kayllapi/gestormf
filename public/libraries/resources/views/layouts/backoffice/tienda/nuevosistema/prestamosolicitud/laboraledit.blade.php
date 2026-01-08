<div class="list-single-main-wrapper fl-wrap">
    <div class="breadcrumbs gradient-bg fl-wrap">
      <span>Editar Ingreso</span>
      <a class="btn btn-success" href="javascript:;" onclick="laboral_index()"><i class="fa fa-angle-left"></i> Atras</a></a>
    </div>
</div>
<div id="carga-laboral">
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
                          <form action="javascript:;" 
                              class="form-laboraledit"
                              onsubmit="callback({
                                        route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamosolicitud/{{ $prestamocredito->id }}',
                                        method: 'PUT',
                                        carga: '#carga-laboral',
                                        data:   {
                                            view: 'editar-laboral',
                                            idprestamo_creditolaboral: {{$prestamolaboral->id}},
                                            ventas: seleccinar_ventas(),
                                            compras: seleccinar_compras(),
                                            ingresos: seleccinar_ingresos(),
                                            egresogastos: seleccinar_egresogastos(),
                                            egresopagos: seleccinar_egresopagos(),
                                            servicios: seleccinar_servicios()
                                        }
                                    },
                                    function(resultado){
                                        laboral_index();
                                        resultado_index();
                                    },this)">
                            <div class="col-sm-6">
                                <label>Fuente de Ingreso *</label>
                                <select id="laboral_editar_idfuenteingreso" disabled>
                                    <option></option>
                                    <option value="1">Dependiente</option>
                                    <option value="2">Independiente</option>
                                </select>
                                <label>Giro *</label>
                                <select id="laboral_editar_idprestamo_giro" onchange="cargarActividad('laboral_editar_idprestamo_giro', 'laboral_editar_idprestamo_actividad')" disabled>
                                    <option></option>
                                    @foreach ($giro as $value)
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                    @endforeach
                                </select>
                                <label>Actividad *</label>
                                <select id="laboral_editar_idprestamo_actividad" disabled>
                                    <option></option>
                                </select>
                                <label>Labora Desde (mes / año) *</label>
                                <div class="row">
                                  <div class="col-sm-6">
                                      <select id="laboral_editar_labora_desdemes">
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
                                      <input type="number" value="{{$prestamolaboral->labora_desdeanio}}" id="laboral_editar_labora_desdeanio" min="1" step="1">
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
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_lunes" id="seleccionar_lunes" <?php echo $prestamolaboral->labora_lunes == "si"? 'checked':'' ?> onclick="calcularmontoventa(),calcularmontocompra(),calcularmontoservicio()">
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
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_martes" id="seleccionar_martes" <?php echo $prestamolaboral->labora_martes == "si"? 'checked':'' ?> onclick="calcularmontoventa(),calcularmontocompra(),calcularmontoservicio()">
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
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_miercoles" id="seleccionar_miercoles" <?php echo $prestamolaboral->labora_miercoles == "si"? 'checked':'' ?> onclick="calcularmontoventa(),calcularmontocompra(),calcularmontoservicio()">
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
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_jueves" id="seleccionar_jueves" <?php echo $prestamolaboral->labora_jueves == "si"? 'checked':'' ?> onclick="calcularmontoventa(),calcularmontocompra(),calcularmontoservicio()">
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
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_viernes" id="seleccionar_viernes" <?php echo $prestamolaboral->labora_viernes == "si"? 'checked':'' ?> onclick="calcularmontoventa(),calcularmontocompra(),calcularmontoservicio()">
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
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_sabados" id="seleccionar_sabados" <?php echo $prestamolaboral->labora_sabados == "si"? 'checked':'' ?> onclick="calcularmontoventa(),calcularmontocompra(),calcularmontoservicio()">
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
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_domingos" id="seleccionar_domingos" <?php echo $prestamolaboral->labora_domingos == "si"? 'checked':'' ?> onclick="calcularmontoventa(),calcularmontocompra(),calcularmontoservicio()">
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
                                <select id="laboral_editar_idubigeo">
                                    <option></option>
                                    @foreach ($ubigeo as $value)
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                    @endforeach
                                </select>
                                <label>Dirección *</label>
                                <input type="text" value="{{$prestamolaboral->direccion}}" id="laboral_editar_direccion">
                                <label>Referencia</label>
                                <input type="text" value="{{$prestamolaboral->referencia}}" id="laboral_editar_referencia">
                            </div>
                            <div class="col-sm-6">
                                <label>Ubicación (Mapa) *</label>
                                <div id="laboral_editar_mapa" style="height: 550px;width: 100%;margin-bottom: 5px;border-radius: 5px;border: 1px solid #aaaaaa;"></div>
                                <input type="hidden" value="{{$prestamolaboral->mapa_latitud}}" id="laboral_editar_mapa_latitud"/>
                                <input type="hidden" value="{{$prestamolaboral->mapa_longitud}}" id="laboral_editar_mapa_longitud"/>
                            </div>
                        </form>
            </div>
            @if($prestamolaboral->idfuenteingreso==1 && $prestamolaboral->idprestamo_giro==1)
            <div id="tab-laboral-4" class="tab-content" style="display: none;">
                <div class="table-responsive">
                  <table class="table" id="tabla-contenidoproducto-ingreso">
                      <thead class="thead-dark">
                        <tr>
                          <th>Concepto</th>
                          <th width="110px">Monto</th>
                          <th width="10px" style="padding: 0px;padding-right: 1px;">
                          <a href="javascript:;" class="btn  color-bg flat-btn" onclick="laboral_agregarconcepto_ingreso()"><i class="fa fa-angle-right"></i> Agregar</a>
                          </th>
                        </tr>
                      </thead>
                      <?php $numIngreso = 0; ?>
                      <tbody num="{{ $numIngreso }}">
                        @foreach ($laboralingreso as $value)
                          <?php $numIngreso++; ?>
                          <tr id="{{ $numIngreso }}">
                            <td class="mx-td-input">
                              <select id="laboral_idconceptoingreso{{ $numIngreso }}" style="width: 100%;padding: 9px;border: 1px solid #d3d8de;border-radius: 5px;">
                                @foreach($conceptoingresos as $item)
                                  <option id="{{ $item->id }}" <?php echo $item->id==$value->s_idprestamo_conceptoingreso? 'selected':'' ?>>{{ $item->nombre }}</option>
                                @endforeach
                              </select>
                            </td>
                            <td class="mx-td-input">
                              <input id="montoIngreso{{ $numIngreso }}" type="number" value="{{ $value->monto }}" step="0.01" min="0" onkeyup="calcularmontoingreso()" onclick="calcularmontoingreso()">
                            </td>
                            <td>
                              <a id="del{{ $numIngreso }}" href="javascript:;" onclick="eliminarproductoingreso({{ $numIngreso }})" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a>
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
                        <input type="text" id="totalsinredondear-ingreso" placeholder="0.00" disabled>
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
                          <th width="10px" style="padding: 0px;padding-right: 1px;">
                          <a href="javascript:;" class="btn  color-bg flat-btn" onclick="laboral_agregarproducto_venta(),laboral_agregarproducto_compra()"><i class="fa fa-angle-right"></i> Agregar</a>
                          </th>
                        </tr>
                      </thead>
                      <tbody num="{{ count($laboralventa) }}">
                      <?php $numVenta = 0; ?>
                        @foreach ($laboralventa as $value)
                          <tr id="{{ $numVenta }}">
                            <td class="mx-td-input">
                              <select id="laboral_idproductoventa{{ $numVenta }}" style="width: 100%;padding: 9px;border: 1px solid #d3d8de;border-radius: 5px;" onchange="calcularmontoventa()">
                                @foreach($productos as $item)
                                  <option id="{{ $item->id }}" <?php echo $item->id==$value->s_idprestamo_producto? 'selected':'' ?>>{{ $item->nombre }}</option>
                                @endforeach
                              </select>
                            </td>
                            <td class="mx-td-input">
                              <input id="productCantVenta{{ $numVenta }}" type="number" value="{{ $value->cantidad }}" onkeyup="calcularmontoventa()" onclick="calcularmontoventa()">
                            </td>
                            <td class="mx-td-input">
                              <input id="productUnidadVenta{{ $numVenta }}" type="number" value="{{ $value->preciounitario }}" step="0.001" min="0" onkeyup="calcularmontoventa()" onclick="calcularmontoventa()">
                            </td>
                            <td class="mx-td-input">
                              <input id="productTotalVenta{{ $numVenta }}" type="number" value="{{ $value->preciototal }}" step="0.01" min="0" disabled>
                            </td>
                            <td class="mx-td-input">
                              <input id="productTotalVenta_Semanal{{ $numVenta }}" type="number" value="{{ $value->preciototal_semanal }}" step="0.01" min="0" disabled>
                            </td>
                            <td class="mx-td-input">
                              <input id="productTotalVenta_Quincenal{{ $numVenta }}" type="number" value="{{ $value->preciototal_quincenal }}" step="0.01" min="0" disabled>
                            </td>
                            <td class="mx-td-input">
                              <input id="productTotalVenta_Mensual{{ $numVenta }}" type="number" value="{{ $value->preciototal_mensual }}" step="0.01" min="0" disabled>
                            </td>
                            <td>
                              <a id="del{{ $numVenta }}" href="javascript:;" onclick="eliminarproductoventa({{ $numVenta }}),eliminarproductocompra({{ $numVenta }})" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a>
                            </td>
                          </tr>
                          <?php $numVenta++; ?>
                        @endforeach
                      </tbody>
                      <tfoot>
                        <tr>
                          <th></th>
                          <th></th>
                          <th><input id="productUnidadVenta_total" type="number" step="0.01" min="0" disabled></th>
                          <th><input id="productTotalVenta_total" type="number" step="0.01" min="0" disabled></th>
                          <th><input id="productTotalVenta_Semanal_total" type="number" step="0.01" min="0" disabled></th>
                          <th><input id="productTotalVenta_Quincenal_total" type="number" step="0.01" min="0" disabled></th>
                          <th><input id="productTotalVenta_Mensual_total" type="number" step="0.01" min="0" disabled></th>
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
                        </tr>
                      </thead>
                      <tbody num="{{ count($laboralcompra)  }}">
                      <?php $numCompra = 0; ?>
                        @foreach ($laboralcompra as $value)
                          <tr id="{{ $numCompra }}">
                            <td class="mx-td-input">
                              <select id="laboral_idproductocompra{{ $numCompra }}" style="width: 100%;padding: 9px;border: 1px solid #d3d8de;border-radius: 5px;" disabled>
                                @foreach($productos as $item)
                                  <option id="{{ $item->id }}" <?php echo $item->id==$value->s_idprestamo_producto? 'selected':'' ?>>{{ $item->nombre }}</option>
                                @endforeach
                              </select>
                            </td>
                            <td class="mx-td-input">
                              <input id="productCantCompra{{ $numCompra }}" type="number" value="{{ $value->cantidad }}" onkeyup="calcularmontocompra()" onclick="calcularmontocompra()" disabled>
                            </td>
                            <td class="mx-td-input">
                              <input id="productUnidadCompra{{ $numCompra }}" type="number" value="{{ $value->preciounitario }}" step="0.001" min="0" onkeyup="calcularmontocompra()" onclick="calcularmontocompra()">
                            </td>
                            <td class="mx-td-input">
                              <input id="productTotalCompra{{ $numCompra }}" type="number" value="{{ $value->preciototal }}" step="0.01" min="0" disabled>
                            </td>
                            <td class="mx-td-input">
                              <input id="productTotalCompra_Semanal{{ $numCompra }}" type="number" value="{{ $value->preciototal_semanal }}" step="0.01" min="0" disabled>
                            </td>
                            <td class="mx-td-input">
                              <input id="productTotalCompra_Quincenal{{ $numCompra }}" type="number" value="{{ $value->preciototal_quincenal }}" step="0.01" min="0" disabled>
                            </td>
                            <td class="mx-td-input">
                              <input id="productTotalCompra_Mensual{{ $numCompra }}" type="number" value="{{ $value->preciototal_mensual }}" step="0.01" min="0" disabled>
                            </td>
                          </tr>
                          <?php $numCompra++; ?>
                        @endforeach
                      </tbody>
                      <tfoot>
                        <tr>
                          <th></th>
                          <th></th>
                          <th><input id="productUnidadCompra_total" type="number" step="0.01" min="0" disabled></th>
                          <th><input id="productTotalCompra_total" type="number" step="0.01" min="0" disabled></th>
                          <th><input id="productTotalCompra_Semanal_total" type="number" step="0.01" min="0" disabled></th>
                          <th><input id="productTotalCompra_Quincenal_total" type="number" step="0.01" min="0" disabled></th>
                          <th><input id="productTotalCompra_Mensual_total" type="number" step="0.01" min="0" disabled></th>
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
                          <th><input id="laboral_servicio_bueno" type="number" value="{{$laboralservicio!=''?$laboralservicio->bueno:'0.00'}}" step="0.01" min="0" onkeyup="calcularmontoservicio()"></th>
                        </tr>
                        <tr>
                          <th style="background-color: #343a40;border-color: #343a40;color: #eee;padding: 8px;">Regular</th>
                          <th><input id="laboral_servicio_regular" type="number" value="{{$laboralservicio!=''?$laboralservicio->regular:'0.00'}}" step="0.01" min="0" onkeyup="calcularmontoservicio()"></th>
                        </tr>
                        <tr>
                          <th style="background-color: #343a40;border-color: #343a40;color: #eee;padding: 8px;">Malo</th>
                          <th><input id="laboral_servicio_malo" type="number" value="{{$laboralservicio!=''?$laboralservicio->malo:'0.00'}}" step="0.01" min="0" onkeyup="calcularmontoservicio()"></th>
                        </tr>
                        <tr>
                          <th style="background-color: #343a40;border-color: #343a40;color: #eee;padding: 8px;">Promedio</th>
                          <th><input id="laboral_servicio_promedio" type="number" value="{{$laboralservicio!=''?$laboralservicio->promedio:'0.00'}}" step="0.01" min="0" disabled></th>
                        </tr>
                        <tr>
                          <th style="background-color: #343a40;border-color: #343a40;color: #eee;padding: 8px;">Venta Semanal</th>
                          <th><input id="laboral_servicio_semanal" type="number" value="{{$laboralservicio!=''?$laboralservicio->semanal:'0.00'}}" step="0.01" min="0" disabled></th>
                        </tr>
                        <tr>
                          <th style="background-color: #343a40;border-color: #343a40;color: #eee;padding: 8px;">Venta Quincenal</th>
                          <th><input id="laboral_servicio_quincenal" type="number" value="{{$laboralservicio!=''?$laboralservicio->quincenal:'0.00'}}" step="0.01" min="0" disabled></th>
                        </tr>
                        <tr>
                          <th style="background-color: #343a40;border-color: #343a40;color: #eee;padding: 8px;">Venta Mensual</th>
                          <th><input id="laboral_servicio_mensual" type="number" value="{{$laboralservicio!=''?$laboralservicio->mensual:'0.00'}}" step="0.01" min="0" disabled></th>
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
                          <th width="10px" style="padding: 0px;padding-right: 1px;">
                          <a href="javascript:;" class="btn  color-bg flat-btn" onclick="laboral_agregarconcepto_egresogasto()"><i class="fa fa-angle-right"></i> Agregar</a>
                          </th>
                        </tr>
                      </thead>
                      <?php $numEgresogasto = 0; ?>
                      <tbody num="{{ $numEgresogasto }}">
                        @foreach ($laboralegresogasto as $value)
                          <?php $numEgresogasto++; ?>
                          <tr id="{{ $numEgresogasto }}">
                            <td class="mx-td-input">
                              <select id="laboral_idconceptoegresogasto{{ $numEgresogasto }}" style="width: 100%;padding: 9px;border: 1px solid #d3d8de;border-radius: 5px;">
                                @foreach($conceptoegresogastos as $item)
                                  <option id="{{ $item->id }}" <?php echo $item->id==$value->s_idprestamo_conceptoegresogasto? 'selected':'' ?>>{{ $item->nombre }}</option>
                                @endforeach
                              </select>
                            </td>
                            <td class="mx-td-input">
                              <input id="montoEgresogasto{{ $numEgresogasto }}" type="number" value="{{ $value->monto }}" step="0.01" min="0" onkeyup="calcularmontoegresogasto()" onclick="calcularmontoegresogasto()">
                            </td>
                            <td>
                              <a id="del{{ $numEgresogasto }}" href="javascript:;" onclick="eliminarproductoegresogasto({{ $numEgresogasto }})" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a>
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
                        <input type="text" id="totalsinredondear-egresogasto" placeholder="0.00" disabled>
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
                          <th width="10px" style="padding: 0px;padding-right: 1px;">
                          <a href="javascript:;" class="btn  color-bg flat-btn" onclick="laboral_agregarconcepto_egresopago()"><i class="fa fa-angle-right"></i> Agregar</a>
                          </th>
                        </tr>
                      </thead>
                      <?php $numEgresopago = 0; ?>
                      <tbody num="{{ $numEgresopago }}">
                        @foreach ($laboralegresopago as $value)
                          <?php $numEgresopago++; ?>
                          <tr id="{{ $numEgresopago }}">
                            <td class="mx-td-input">
                              <select id="laboral_idconceptoegresopago{{ $numEgresopago }}" style="width: 100%;padding: 9px;border: 1px solid #d3d8de;border-radius: 5px;">
                                @foreach($conceptoegresopagos as $item)
                                  <option id="{{ $item->id }}" <?php echo $item->id==$value->s_idprestamo_conceptoegresopago? 'selected':'' ?>>{{ $item->nombre }}</option>
                                @endforeach
                              </select>
                            </td>
                            <td class="mx-td-input">
                              <input id="montoEgresopago{{ $numEgresopago }}" type="number" value="{{ $value->monto }}" step="0.01" min="0" onkeyup="calcularmontoegresopago()" onclick="calcularmontoegresopago()">
                            </td>
                            <td>
                              <a id="del{{ $numEgresopago }}" href="javascript:;" onclick="eliminarproductoegresopago({{ $numEgresopago }})" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a>
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
                        <input type="text" id="totalsinredondear-egresopago" placeholder="0.00" disabled>
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
                                    <td style="padding: 10px;"><div id="resultado_total_ingreso"></div></td>
                                  </tr>
                                  @elseif($prestamolaboral->idfuenteingreso==2 && $prestamolaboral->idprestamo_giro==1)
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;width:200px;"><b>(+) Ventas</b></td>
                                    <td style="padding: 10px;"><div id="resultado_total_venta"></div></td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>(-) Compras</b></td>
                                    <td style="padding: 10px;"><div id="resultado_total_compra"></div></td>
                                  </tr>
                                  @elseif(($prestamolaboral->idfuenteingreso==2 && $prestamolaboral->idprestamo_giro==2) || ($prestamolaboral->idfuenteingreso==1 && $prestamolaboral->idprestamo_giro==2))
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>(+) Servicios</b></td>
                                    <td style="padding: 10px;"><div id="resultado_total_servicio"></div></td>
                                  </tr>
                                  @endif
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>(-) Gastos</b></td>
                                    <td style="padding: 10px;"><div id="resultado_total_egresogasto"></div></td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>(-) Pagos</b></td>
                                    <td style="padding: 10px;"><div id="resultado_total_egresopago"></div></td>
                                  </tr>
                                  <tr>
                                    <td style="background-color: #eae7e7;padding: 10px;"><b>Ingreso Mensual</b></td>
                                    <td style="padding: 10px;"><div id="resultado_total_ingresomensual"></div></td>
                                  </tr>
                            </tbody>
                          </table>
                        </div>
                    </div>
                </div>
                        
            </div>
        </div>
    </div>    
    <button type="button" class="btn mx-btn-post" onclick="guardar_laboral();">Guardar Laboral</button>   
</div>        
<script>
  tab({click:'#tab-laboral'});

  function guardar_laboral(){
      $( ".form-laboraledit" ).submit();
  }
  
  $('#laboral_editar_idubigeo').select2({
      placeholder: '-- Seleccionar Ubigeo --',
      minimumResultsForSearch: -1,
      minimumInputLength: 2
  }).val({{$prestamolaboral->idubigeo}}).trigger('change');

  $('#laboral_editar_idfuenteingreso').select2({
      placeholder: '-- Seleccionar Fuente de Ingreso --',
      minimumResultsForSearch: -1
  }).val({{$prestamolaboral->idfuenteingreso}}).trigger('change');

  $('#laboral_editar_idprestamo_giro').select2({
      placeholder: '-- Seleccionar Giro --',
      minimumResultsForSearch: -1
  }).val({{$prestamolaboral->idprestamo_giro}}).trigger('change');

  $('#laboral_editar_idprestamo_actividad').select2({
      placeholder: '-- Seleccionar Actividad --',
      minimumResultsForSearch: -1
  });
  cargarActividad('laboral_editar_idprestamo_giro', 'laboral_editar_idprestamo_actividad', {{$prestamolaboral->idprestamo_actividad}});

  $('#laboral_editar_labora_desdemes').select2({
      placeholder: '-- Seleccionar Ubigeo --',
      minimumResultsForSearch: -1
  }).val({{$prestamolaboral->labora_desdemes}}).trigger('change');

  singleMap({
      'map' : '#laboral_editar_mapa',
      'lat' : parseFloat({{$prestamolaboral->mapa_latitud}}),
      'lng' : parseFloat({{$prestamolaboral->mapa_longitud}}),
      'result_lat' : '#laboral_editar_mapa_latitud',
      'result_lng' : '#laboral_editar_mapa_longitud'
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
</script>
<script>
  // Ventas
  
  function contar_semana(){
      var cant = 0;
      var seleccionar_lunes = $('#seleccionar_lunes:checked').val();
      var seleccionar_martes = $('#seleccionar_martes:checked').val();
      var seleccionar_miercoles = $('#seleccionar_miercoles:checked').val();
      var seleccionar_jueves = $('#seleccionar_jueves:checked').val();
      var seleccionar_viernes = $('#seleccionar_viernes:checked').val();
      var seleccionar_sabados = $('#seleccionar_sabados:checked').val();
      var seleccionar_domingos = $('#seleccionar_domingos:checked').val();
      if(seleccionar_lunes=='on'){
          var cant = cant+1;;
      }
      if(seleccionar_martes=='on'){
          var cant = cant+1;;
      }
      if(seleccionar_miercoles=='on'){
          var cant = cant+1;;
      }
      if(seleccionar_jueves=='on'){
          var cant = cant+1;;
      }
      if(seleccionar_viernes=='on'){
          var cant = cant+1;;
      }
      if(seleccionar_sabados=='on'){
          var cant = cant+1;;
      }
      if(seleccionar_domingos=='on'){
          var cant = cant+1;;
      }
      return parseFloat(cant);
  }
  
  calcularmontoventa();
  function seleccinar_ventas(){
    var data = '';
    $("#tabla-contenidoproducto-venta tbody tr").each(function() {
        var num = $(this).attr('id');        
        var idproducto = $("#laboral_idproductoventa"+num+" option:selected").attr('id');
        var productCantVenta = $("#productCantVenta"+num).val();
        var productUnidadVenta = $("#productUnidadVenta"+num).val();
        var productTotalVenta = $("#productTotalVenta"+num).val();
        var productTotalVenta_Semanal = $("#productTotalVenta_Semanal"+num).val();
        var productTotalVenta_Quincenal = $("#productTotalVenta_Quincenal"+num).val();
        var productTotalVenta_Mensual = $("#productTotalVenta_Mensual"+num).val();
        data = data+'/&/'+idproducto+'/,/'+productCantVenta+'/,/'+productUnidadVenta+'/,/'+productTotalVenta+'/,/'+productTotalVenta_Semanal+'/,/'+productTotalVenta_Quincenal+'/,/'+productTotalVenta_Mensual;
    });
    return data;
  }
  function laboral_agregarproducto_venta(){
    var productos = '';
    @foreach($productos as $value)
        productos = productos+'<option id="{{$value->id}}">{{$value->nombre}}</option>';
    @endforeach
    var num = $("#tabla-contenidoproducto-venta > tbody").attr('num');
    $('#tabla-contenidoproducto-venta > tbody').append('<tr id="'+num+'">'+
                                                 '<td class="mx-td-input">'+
                                                   '<select id="laboral_idproductoventa'+num+'" style="width: 100%;padding: 9px;border: 1px solid #d3d8de;border-radius: 5px;" onchange="calcularmontoventa()">'+
                                                       '<option></option>'+
                                                        productos+
                                                   '</select>'+
                                                 '</td>'+
                                                 '<td class="mx-td-input"><input id="productCantVenta'+num+'" type="number" value="1" onkeyup="calcularmontoventa()" onclick="calcularmontoventa()"></td>'+
                                                 '<td class="mx-td-input"><input id="productUnidadVenta'+num+'" type="number" value="0.00" step="0.001" min="0" onkeyup="calcularmontoventa()" onclick="calcularmontoventa()"></td>'+
                                                 '<td class="mx-td-input"><input id="productTotalVenta'+num+'" type="number" value="0.00" step="0.01" min="0" disabled></td>'+
                                                 '<td class="mx-td-input"><input id="productTotalVenta_Semanal'+num+'" type="number" value="0.00" step="0.01" min="0" disabled></td>'+
                                                 '<td class="mx-td-input"><input id="productTotalVenta_Quincenal'+num+'" type="number" value="0.00" step="0.01" min="0" disabled></td>'+
                                                 '<td class="mx-td-input"><input id="productTotalVenta_Mensual'+num+'" type="number" value="0.00" step="0.01" min="0" disabled></td>'+
                                                 '<td><a id="del'+num+'" href="javascript:;" onclick="eliminarproductoventa('+num+'),eliminarproductocompra('+num+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>'+
                                             '</tr>');
    /*$('#laboral_idproductoventa'+num).select2({
      placeholder: '-- Seleccionar --'
    });*/
    $("#tabla-contenidoproducto-venta > tbody").attr('num',parseInt(num)+1);
    calcularmontoventa();
  }
  function calcularmontoventa(){
    var total_unitario = 0;
    var total = 0;
    var total_semanal = 0;
    var total_quincenal = 0;
    var total_mensual = 0;
    $("#tabla-contenidoproducto-venta > tbody > tr").each(function() {
        var num = $(this).attr('id');        
        var productCantVenta = parseFloat($("#productCantVenta"+num).val());
        var productUnidadVenta = parseFloat($("#productUnidadVenta"+num).val());
        var subtotal = (productCantVenta*productUnidadVenta).toFixed(2);
        if(contar_semana()>0){
            var subtotal_semanal = (subtotal*contar_semana()).toFixed(2);
            var subtotal_quincenal = (subtotal*(contar_semana()*2)).toFixed(2);
            var subtotal_mensual = (subtotal*(contar_semana()*4)).toFixed(2);
        }else{
            subtotal = 0.00;
            var subtotal_semanal = subtotal.toFixed(2);
            var subtotal_quincenal = subtotal.toFixed(2);
            var subtotal_mensual = subtotal.toFixed(2);
        }
            
        $("#productTotalVenta"+num).val(parseFloat(subtotal).toFixed(2));
        $("#productTotalVenta_Semanal"+num).val(subtotal_semanal);
        $("#productTotalVenta_Quincenal"+num).val(subtotal_quincenal);
        $("#productTotalVenta_Mensual"+num).val(subtotal_mensual);
        total_unitario = total_unitario+parseFloat(productUnidadVenta);
        total = total+parseFloat(subtotal);
        total_semanal = total_semanal+parseFloat(subtotal_semanal);
        total_quincenal = total_quincenal+parseFloat(subtotal_quincenal);
        total_mensual = total_mensual+parseFloat(subtotal_mensual);
      
        // duplicar en compras
        $("#productCantCompra"+num).val(productCantVenta);
        var idproducto = $("#laboral_idproductoventa"+num+" option:selected").attr('id');
        var productonombre = $("#laboral_idproductoventa"+num+" option:selected").html();
        $("#laboral_idproductocompra"+num).html('<option id="'+idproducto+'}">'+productonombre+'</option>');
    });
    $("#productUnidadVenta_total").val(total_unitario.toFixed(2)); 
    $("#productTotalVenta_total").val(total.toFixed(2));  
    $("#productTotalVenta_Semanal_total").val(total_semanal.toFixed(2));  
    $("#productTotalVenta_Quincenal_total").val(total_quincenal.toFixed(2));  
    $("#productTotalVenta_Mensual_total").val(total_mensual.toFixed(2));   
    calcularresultado();
    calcularmontocompra();
  }
  function eliminarproductoventa(num){
    $("#tabla-contenidoproducto-venta > tbody > tr#"+num).remove();
    calcularmontoventa();
  }
  
  // Resultado
  function calcularresultado(){
      var total_venta = $("#productTotalVenta_Mensual_total").val()!=undefined?$("#productTotalVenta_Mensual_total").val():0;
      var total_compra = $("#productTotalCompra_Mensual_total").val()!=undefined?$("#productTotalCompra_Mensual_total").val():0;
      var total_gasto = $("#totalsinredondear-egresogasto").val()!=undefined?$("#totalsinredondear-egresogasto").val():0;
      var total_pago = $("#totalsinredondear-egresopago").val()!=undefined?$("#totalsinredondear-egresopago").val():0;
      var total_ingreso = $("#totalsinredondear-ingreso").val()!=undefined?$("#totalsinredondear-ingreso").val():0;
      var total_servicio = $("#laboral_servicio_mensual").val()!=undefined?$("#laboral_servicio_mensual").val():0;
      var total_netomensual = parseFloat(total_venta)-parseFloat(total_compra)-parseFloat(total_gasto)-parseFloat(total_pago)+parseFloat(total_ingreso)+parseFloat(total_servicio);
    
      total_netomensual = total_netomensual.toFixed(2);
    
      $('#resultado_total_venta').html(total_venta);
      $('#resultado_total_compra').html(total_compra);
      $('#resultado_total_egresogasto').html(total_gasto);
      $('#resultado_total_egresopago').html(total_pago);
      $('#resultado_total_ingreso').html(total_ingreso);
      $('#resultado_total_servicio').html(total_servicio);
    
      $('#resultado_total_ingresomensual').html(total_netomensual);
    
  }

  // Compras
  calcularmontocompra();
  function seleccinar_compras(){
    var data = '';
    $("#tabla-contenidoproducto-compra tbody tr").each(function() {
        var num = $(this).attr('id');        
        var idproducto = $("#laboral_idproductocompra"+num+" option:selected").attr('id');
        var productCantCompra = $("#productCantCompra"+num).val();
        var productUnidadCompra = $("#productUnidadCompra"+num).val();
        var productTotalCompra = $("#productTotalCompra"+num).val();
        var productTotalCompra_Semanal = $("#productTotalCompra_Semanal"+num).val();
        var productTotalCompra_Quincenal = $("#productTotalCompra_Quincenal"+num).val();
        var productTotalCompra_Mensual = $("#productTotalCompra_Mensual"+num).val();
        data = data+'/&/'+idproducto+'/,/'+productCantCompra+'/,/'+productUnidadCompra+'/,/'+productTotalCompra+'/,/'+productTotalCompra_Semanal+'/,/'+productTotalCompra_Quincenal+'/,/'+productTotalCompra_Mensual;
    });
    return data;
  }
  function laboral_agregarproducto_compra(){
    var productos = '';

    var num = $("#tabla-contenidoproducto-compra > tbody").attr('num');
    $('#tabla-contenidoproducto-compra > tbody').append('<tr id="'+num+'">'+
                                                 '<td class="mx-td-input">'+
                                                   '<select id="laboral_idproductocompra'+num+'" style="width: 100%;padding: 9px;border: 1px solid #d3d8de;border-radius: 5px;" disabled>'+
                                                       '<option></option>'+
                                                   '</select>'+
                                                 '</td>'+
                                                 '<td class="mx-td-input"><input id="productCantCompra'+num+'" type="number" value="1" onkeyup="calcularmontocompra()" onclick="calcularmontocompra()" disabled></td>'+
                                                 '<td class="mx-td-input"><input id="productUnidadCompra'+num+'" type="number" value="0.00" step="0.001" min="0" onkeyup="calcularmontocompra()" onclick="calcularmontocompra()"></td>'+
                                                 '<td class="mx-td-input"><input id="productTotalCompra'+num+'" type="number" value="0.00" step="0.01" min="0" disabled></td>'+
                                                 '<td class="mx-td-input"><input id="productTotalCompra_Semanal'+num+'" type="number" value="0.00" step="0.01" min="0" disabled></td>'+
                                                 '<td class="mx-td-input"><input id="productTotalCompra_Quincenal'+num+'" type="number" value="0.00" step="0.01" min="0" disabled></td>'+
                                                 '<td class="mx-td-input"><input id="productTotalCompra_Mensual'+num+'" type="number" value="0.00" step="0.01" min="0" disabled></td>'+
                                             '</tr>');
    $("#tabla-contenidoproducto-compra > tbody").attr('num',parseInt(num)+1);
    calcularmontocompra();
  }
  function calcularmontocompra(){
    var total_unitario = 0;
    var total = 0;
    var total_semanal = 0;
    var total_quincenal = 0;
    var total_mensual = 0;
    $("#tabla-contenidoproducto-compra > tbody > tr").each(function() {
        var num = $(this).attr('id');        
        var productCantCompra = parseFloat($("#productCantCompra"+num).val());
        var productUnidadCompra = parseFloat($("#productUnidadCompra"+num).val());
        var subtotal = (productCantCompra*productUnidadCompra).toFixed(2);
      
        if(contar_semana()>0){
            var subtotal_semanal = (subtotal*contar_semana()).toFixed(2);
            var subtotal_quincenal = (subtotal*(contar_semana()*2)).toFixed(2);
            var subtotal_mensual = (subtotal*(contar_semana()*4)).toFixed(2);
        }else{
            subtotal = 0.00;
            var subtotal_semanal = subtotal.toFixed(2);
            var subtotal_quincenal = subtotal.toFixed(2);
            var subtotal_mensual = subtotal.toFixed(2);
        }
      
        $("#productTotalCompra"+num).val(parseFloat(subtotal).toFixed(2));
        $("#productTotalCompra_Semanal"+num).val(subtotal_semanal);
        $("#productTotalCompra_Quincenal"+num).val(subtotal_quincenal);
        $("#productTotalCompra_Mensual"+num).val(subtotal_mensual);
        total_unitario = total_unitario+parseFloat(productUnidadCompra);
        total = total+parseFloat(subtotal);
        total_semanal = total_semanal+parseFloat(subtotal_semanal);
        total_quincenal = total_quincenal+parseFloat(subtotal_quincenal);
        total_mensual = total_mensual+parseFloat(subtotal_mensual);
    });
    $("#productUnidadCompra_total").val(total_unitario.toFixed(2)); 
    $("#productTotalCompra_total").val(total.toFixed(2));  
    $("#productTotalCompra_Semanal_total").val(total_semanal.toFixed(2));  
    $("#productTotalCompra_Quincenal_total").val(total_quincenal.toFixed(2));  
    $("#productTotalCompra_Mensual_total").val(total_mensual.toFixed(2));   
    calcularresultado();
  }
  function eliminarproductocompra(num){
    $("#tabla-contenidoproducto-compra > tbody > tr#"+num).remove();
    calcularmontocompra();
  }
</script>
<script>
  // Servicios
  
  function seleccinar_servicios(){
        var data='';
        @if(($prestamolaboral->idfuenteingreso==2 && $prestamolaboral->idprestamo_giro==2) || ($prestamolaboral->idfuenteingreso==1 && $prestamolaboral->idprestamo_giro==2))
        var laboral_servicio_bueno = $("#laboral_servicio_bueno").val();
        var laboral_servicio_regular = $("#laboral_servicio_regular").val();
        var laboral_servicio_malo = $("#laboral_servicio_malo").val();
        var laboral_servicio_promedio = $("#laboral_servicio_promedio").val();
        var laboral_servicio_semanal = $("#laboral_servicio_semanal").val();
        var laboral_servicio_quincenal = $("#laboral_servicio_quincenal").val();
        var laboral_servicio_mensual = $("#laboral_servicio_mensual").val();
        var data = '/,/'+laboral_servicio_bueno
                  +'/,/'+laboral_servicio_regular
                  +'/,/'+laboral_servicio_malo
                  +'/,/'+laboral_servicio_promedio
                  +'/,/'+laboral_servicio_semanal
                  +'/,/'+laboral_servicio_quincenal
                  +'/,/'+laboral_servicio_mensual;
        @endif
        return data;
  }

  function calcularmontoservicio(){
    
    var laboral_servicio_bueno = parseFloat($("#laboral_servicio_bueno").val());
    var laboral_servicio_regular = parseFloat($("#laboral_servicio_regular").val());
    var laboral_servicio_malo = parseFloat($("#laboral_servicio_malo").val());
    
    var suma_laboral = laboral_servicio_bueno+laboral_servicio_regular+laboral_servicio_malo;
    var laboral_servicio_promedio = suma_laboral/contar_semana();
    var laboral_servicio_semanal = laboral_servicio_promedio*contar_semana();
    var laboral_servicio_quincenal = laboral_servicio_promedio*(contar_semana()*2);
    var laboral_servicio_mensual = laboral_servicio_promedio*(contar_semana()*4);
    $("#laboral_servicio_promedio").val(laboral_servicio_promedio.toFixed(2)); 
    $("#laboral_servicio_semanal").val(laboral_servicio_semanal.toFixed(2));  
    $("#laboral_servicio_quincenal").val(laboral_servicio_quincenal.toFixed(2));  
    $("#laboral_servicio_mensual").val(laboral_servicio_mensual.toFixed(2));  
    
    calcularresultado();
  }
</script>
<script>
  // Ingreso
  calcularmontoingreso();
  function seleccinar_ingresos(){
    var data = '';
    $("#tabla-contenidoproducto-ingreso tbody tr").each(function() {
        var num = $(this).attr('id');        
        var idconcepto = $("#laboral_idconceptoingreso"+num+" option:selected").attr('id');
        var montoIngreso = $("#montoIngreso"+num).val();
        data = data+'/&/'+idconcepto+'/,/'+montoIngreso;
    });
    return data;
  }
  function laboral_agregarconcepto_ingreso(){
    var conceptos = '';
    @foreach($conceptoingresos as $value)
        conceptos = conceptos+'<option id="{{$value->id}}">{{$value->nombre}}</option>';
    @endforeach
    var num = $("#tabla-contenidoproducto-ingreso > tbody").attr('num');
    $('#tabla-contenidoproducto-ingreso > tbody').append('<tr id="'+num+'">'+
                                                 '<td class="mx-td-input">'+
                                                   '<select id="laboral_idconceptoingreso'+num+'" style="width: 100%;padding: 9px;border: 1px solid #d3d8de;border-radius: 5px;">'+
                                                       '<option></option>'+
                                                        conceptos+
                                                   '</select>'+
                                                 '</td>'+
                                                 '<td class="mx-td-input"><input id="montoIngreso'+num+'" type="number" value="1" onkeyup="calcularmontoingreso()" onclick="calcularmontoingreso()"></td>'+
                                                 '<td><a id="del'+num+'" href="javascript:;" onclick="eliminarproductoingreso('+num+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>'+
                                             '</tr>');
    $("#tabla-contenidoproducto-ingreso > tbody").attr('num',parseInt(num)+1);
    calcularmontoingreso();
  }
  function calcularmontoingreso(){
    var total = 0;
    $("#tabla-contenidoproducto-ingreso > tbody > tr").each(function() {
        var num = $(this).attr('id');        
        var montoIngreso = parseFloat($("#montoIngreso"+num).val());
        total = total+parseFloat(montoIngreso);
    });
    $("#totalsinredondear-ingreso").val((Math.round10(total, -1)).toFixed(2));  
    calcularresultado(); 
  }
  function eliminarproductoingreso(num){
    $("#tabla-contenidoproducto-ingreso > tbody > tr#"+num).remove();
    calcularmontoingreso();
  }
</script>
<script>
  // Egresogasto
  calcularmontoegresogasto();
  function seleccinar_egresogastos(){
    var data = '';
    $("#tabla-contenidoproducto-egresogasto tbody tr").each(function() {
        var num = $(this).attr('id');        
        var idconcepto = $("#laboral_idconceptoegresogasto"+num+" option:selected").attr('id');
        var montoEgresogasto = $("#montoEgresogasto"+num).val();
        data = data+'/&/'+idconcepto+'/,/'+montoEgresogasto;
    });
    return data;
  }
  function laboral_agregarconcepto_egresogasto(){
    var conceptos = '';
    @foreach($conceptoegresogastos as $value)
        conceptos = conceptos+'<option id="{{$value->id}}">{{$value->nombre}}</option>';
    @endforeach
    var num = $("#tabla-contenidoproducto-egresogasto > tbody").attr('num');
    $('#tabla-contenidoproducto-egresogasto > tbody').append('<tr id="'+num+'">'+
                                                 '<td class="mx-td-input">'+
                                                   '<select id="laboral_idconceptoegresogasto'+num+'" style="width: 100%;padding: 9px;border: 1px solid #d3d8de;border-radius: 5px;">'+
                                                       '<option></option>'+
                                                        conceptos+
                                                   '</select>'+
                                                 '</td>'+
                                                 '<td class="mx-td-input"><input id="montoEgresogasto'+num+'" type="number" value="1" onkeyup="calcularmontoegresogasto()" onclick="calcularmontoegresogasto()"></td>'+
                                                 '<td><a id="del'+num+'" href="javascript:;" onclick="eliminarproductoegresogasto('+num+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>'+
                                             '</tr>');
    $("#tabla-contenidoproducto-egresogasto > tbody").attr('num',parseInt(num)+1);
    calcularmontoegresogasto();
  }
  function calcularmontoegresogasto(){
    var total = 0;
    $("#tabla-contenidoproducto-egresogasto > tbody > tr").each(function() {
        var num = $(this).attr('id');        
        var montoEgresogasto = parseFloat($("#montoEgresogasto"+num).val());
        total = total+parseFloat(montoEgresogasto);
    });
    $("#totalsinredondear-egresogasto").val((Math.round10(total, -1)).toFixed(2));  
    calcularresultado();
  }
  function eliminarproductoegresogasto(num){
    $("#tabla-contenidoproducto-egresogasto > tbody > tr#"+num).remove();
    calcularmontoegresogasto();
  }
</script>
<script>
  // Egresopago
  calcularmontoegresopago();
  function seleccinar_egresopagos(){
    var data = '';
    $("#tabla-contenidoproducto-egresopago tbody tr").each(function() {
        var num = $(this).attr('id');        
        var idconcepto = $("#laboral_idconceptoegresopago"+num+" option:selected").attr('id');
        var montoEgresopago = $("#montoEgresopago"+num).val();
        data = data+'/&/'+idconcepto+'/,/'+montoEgresopago;
    });
    return data;
  }
  function laboral_agregarconcepto_egresopago(){
    var conceptos = '';
    @foreach($conceptoegresopagos as $value)
        conceptos = conceptos+'<option id="{{$value->id}}">{{$value->nombre}}</option>';
    @endforeach
    var num = $("#tabla-contenidoproducto-egresopago > tbody").attr('num');
    $('#tabla-contenidoproducto-egresopago > tbody').append('<tr id="'+num+'">'+
                                                 '<td class="mx-td-input">'+
                                                   '<select id="laboral_idconceptoegresopago'+num+'" style="width: 100%;padding: 9px;border: 1px solid #d3d8de;border-radius: 5px;">'+
                                                       '<option></option>'+
                                                        conceptos+
                                                   '</select>'+
                                                 '</td>'+
                                                 '<td class="mx-td-input"><input id="montoEgresopago'+num+'" type="number" value="1" onkeyup="calcularmontoegresopago()" onclick="calcularmontoegresopago()"></td>'+
                                                 '<td><a id="del'+num+'" href="javascript:;" onclick="eliminarproductoegresopago('+num+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a></td>'+
                                             '</tr>');
    $("#tabla-contenidoproducto-egresopago > tbody").attr('num',parseInt(num)+1);
    calcularmontoegresopago();
  }
  function calcularmontoegresopago(){
    var total = 0;
    $("#tabla-contenidoproducto-egresopago > tbody > tr").each(function() {
        var num = $(this).attr('id');        
        var montoEgresopago = parseFloat($("#montoEgresopago"+num).val());
        total = total+parseFloat(montoEgresopago);
    });
    $("#totalsinredondear-egresopago").val((Math.round10(total, -1)).toFixed(2)); 
    calcularresultado();
  }
  function eliminarproductoegresopago(num){
    $("#tabla-contenidoproducto-egresopago > tbody > tr#"+num).remove();
    calcularmontoegresopago();
  }
</script>
<script>
  
</script>