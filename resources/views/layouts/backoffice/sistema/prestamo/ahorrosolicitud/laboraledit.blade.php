<form action="javascript:;" 
                      class="form-laboraledit"
                      onsubmit="callback({
                                route:  'backoffice/tienda/sistema/{{ $tienda->id }}/prestamo/ahorrosolicitud/{{ $prestamoahorro->id }}',
                                method: 'PUT',
                                carga: '#carga-ahorro',
                                data:   {
                                    view: 'editar-laboral',
                                }
                            },
                            function(resultado){
                                removecarga({input:'#carga-ahorro'});
                                laboral_edit();
                                resultado_index();
                            },this)">
                    <div class="row">
                            <div class="col-sm-6">
                                <label>Fuente de Ingreso *</label>
                                <select id="laboral_editar_idfuenteingreso">
                                    <option></option>
                                    @foreach ($fuenteingreso as $value)
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                    @endforeach
                                </select>
                                <label>Giro *</label>
                                <select id="laboral_editar_idprestamo_giro">
                                    <option></option>
                                    @foreach ($giro as $value)
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                    @endforeach
                                </select>
                                <label>Actividad *</label>
                                <input type="text" value="{{$prestamolaboral!=''?$prestamolaboral->actividad:''}}" id="laboral_editar_idprestamo_actividad" onkeyup="texto_mayucula(this)">
                                <label>Nombre de Negocio *</label>
                                <input type="text" value="{{$prestamolaboral!=''?$prestamolaboral->nombrenegocio:''}}" id="laboral_editar_idprestamo_nombrenegocio" onkeyup="texto_mayucula(this)">
                                
                                <label>Labora Desde (mes / año) *</label>
                                <div class="row">
                                  <div class="col-sm-6">
                                      <select id="laboral_editar_labora_desdemes">
                                              <option></option>
                                              <option value="1">ENERO</option>
                                              <option value="2">FEBRERO</option>
                                              <option value="3">MARZO</option>
                                              <option value="4">ABRIL</option>
                                              <option value="5">MAYO</option>
                                              <option value="6">JUNIO</option>
                                              <option value="7">JULIO</option>
                                              <option value="8">AGOSTO</option>
                                              <option value="9">SEPTIEMBRE</option>
                                              <option value="10">OCTUBRE</option>
                                              <option value="11">NOVIEMBRE</option>
                                              <option value="12">DICIEMBRE</option>
                                      </select>
                                  </div>
                                  <div class="col-sm-6">
                                          <select id="laboral_editar_labora_desdeanio">
                                              <option></option>
                                              <?php $fecha_reside = Carbon\Carbon::now()->format('Y') ?>
                                              @for($i=$fecha_reside;$i>1950;$i--)
                                              <option value="{{$fecha_reside}}">{{$fecha_reside}}</option>
                                              <?php $fecha_reside=$fecha_reside-1 ?>
                                              @endfor
                                          </select>
                                  </div>
                                </div>
                                <label>Días Laborables:</label>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <table style="width: 100%;">
                                              <tr>
                                                <td style="text-align: right;padding: 10px;font-weight: bold;">Lunes</td>
                                                <td>
                                                  <div class="onoffswitch">
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_lunes" id="seleccionar_lunes" <?php echo $prestamolaboral!=''?($prestamolaboral->labora_lunes == "si"? 'checked':''):'checked' ?> onclick="calcularmontoventa(),calcularmontocompra(),calcularmontoservicio()">
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
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_martes" id="seleccionar_martes" <?php echo  $prestamolaboral!=''?($prestamolaboral->labora_martes == "si"? 'checked':''):'checked' ?> onclick="calcularmontoventa(),calcularmontocompra(),calcularmontoservicio()">
                                                      <label class="onoffswitch-label" for="seleccionar_martes">
                                                          <span class="onoffswitch-inner"></span>
                                                          <span class="onoffswitch-switch"></span>
                                                      </label> 
                                                  </div>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td style="text-align: right;padding: 10px;font-weight: bold;">Miercoles</td>
                                                <td>
                                                  <div class="onoffswitch">
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_miercoles" id="seleccionar_miercoles" <?php echo $prestamolaboral!=''?($prestamolaboral->labora_miercoles == "si"? 'checked':''):'checked' ?> onclick="calcularmontoventa(),calcularmontocompra(),calcularmontoservicio()">
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
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_jueves" id="seleccionar_jueves" <?php echo $prestamolaboral!=''?($prestamolaboral->labora_jueves == "si"? 'checked':''):'checked' ?> onclick="calcularmontoventa(),calcularmontocompra(),calcularmontoservicio()">
                                                      <label class="onoffswitch-label" for="seleccionar_jueves">
                                                          <span class="onoffswitch-inner"></span>
                                                          <span class="onoffswitch-switch"></span>
                                                      </label> 
                                                  </div>
                                                </td>
                                              </tr>
                                            </table>
                                    </div>
                                    <div class="col-sm-6">
                                        <table style="width: 100%;">
                                              <tr>
                                                <td style="text-align: right;padding: 10px;font-weight: bold;">Viernes</td>
                                                <td>
                                                  <div class="onoffswitch">
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_viernes" id="seleccionar_viernes" <?php echo $prestamolaboral!=''?($prestamolaboral->labora_viernes == "si"? 'checked':''):'checked' ?> onclick="calcularmontoventa(),calcularmontocompra(),calcularmontoservicio()">
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
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_sabados" id="seleccionar_sabados" <?php echo $prestamolaboral!=''?($prestamolaboral->labora_sabados == "si"? 'checked':''):'checked' ?> onclick="calcularmontoventa(),calcularmontocompra(),calcularmontoservicio()">
                                                      <label class="onoffswitch-label" for="seleccionar_sabados">
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
                                                      <input type="checkbox" class="onoffswitch-checkbox seleccionar_domingos" id="seleccionar_domingos" <?php echo $prestamolaboral!=''?($prestamolaboral->labora_domingos == "si"? 'checked':''):'checked' ?> onclick="calcularmontoventa(),calcularmontocompra(),calcularmontoservicio()">
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
                            </div>
                            <div class="col-sm-6">
                                <label>Ubigeo *</label>
                                <select id="laboral_editar_idubigeo" onchange="seleccionar_ubicacion($('#laboral_editar_idubigeo').select2('data')[0].ubicacion)">
                                    <option value="{{$prestamolaboral!=''?$prestamolaboral->idubigeo:$prestamoahorro->clienteidubigeo}}">{{$prestamolaboral!=''?$prestamolaboral->nombre_ubigeo:$prestamoahorro->clienteubigeonombre}}</option>
                                </select>
                                <label>Dirección *</label>
                                <input type="text" value="{{$prestamolaboral!=''?$prestamolaboral->direccion:$prestamoahorro->clientedireccion}}" id="laboral_editar_direccion" onkeyup="texto_mayucula(this)">
                                <label>Referencia</label>
                                <input type="text" value="{{$prestamolaboral!=''?$prestamolaboral->referencia:$prestamoahorro->clientereferencia}}" id="laboral_editar_referencia" onkeyup="texto_mayucula(this)">
                                <label>Ubicación (Mapa)</label>
                                <div id="laboral_editar_mapa" style="height: 355px;width: 100%;margin-bottom: 5px;border-radius: 5px;border: 1px solid #aaaaaa;"></div>
                                <input type="hidden" value="{{$prestamolaboral!=''?$prestamolaboral->mapa_latitud:'-12.071871667822409'}}" id="laboral_editar_mapa_latitud"/>
                                <input type="hidden" value="{{$prestamolaboral!=''?$prestamolaboral->mapa_longitud:'-75.21026847919165'}}" id="laboral_editar_mapa_longitud"/>
                            </div>
                    </div> 
                </form>  
    <button type="button" class="btn mx-btn-post" onclick="guardar_laboral();">Guardar Cambios</button>   

<script>
  
  function guardar_laboral(){
      $( ".form-laboraledit" ).submit();
  }
  
  $('#laboral_editar_idubigeo').select2({
    @include('app.select2_ubigeo')
  });

@if($prestamolaboral!='')
  $('#laboral_editar_idfuenteingreso').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1
  }).val({{$prestamolaboral->idfuenteingreso}}).trigger('change');
@else
  $('#laboral_editar_idfuenteingreso').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1
  });
@endif

@if($prestamolaboral!='')
  $('#laboral_editar_idprestamo_giro').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1
  }).val({{$prestamolaboral->idprestamo_giro}}).trigger('change');
@else
  $('#laboral_editar_idprestamo_giro').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1
  });
@endif
  


@if($prestamolaboral!='')  
  $('#laboral_editar_labora_desdemes').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1
  }).val({{$prestamolaboral->labora_desdemes}}).trigger('change');
@else  
  $('#laboral_editar_labora_desdemes').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1
  });
@endif  

@if($prestamolaboral!='')  
  $('#laboral_editar_labora_desdeanio').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1
  }).val({{$prestamolaboral->labora_desdeanio}}).trigger('change');
@else  
  $('#laboral_editar_labora_desdeanio').select2({
      placeholder: '-- Seleccionar --',
      minimumResultsForSearch: -1
  });
@endif  

  
  
@if($prestamolaboral!='')
    singleMap({
        'map' : '#laboral_editar_mapa',
        'lat' : '{{$prestamolaboral->mapa_latitud}}',
        'lng' : '{{$prestamolaboral->mapa_longitud}}',
        'result_lat' : '#laboral_editar_mapa_latitud',
        'result_lng' : '#laboral_editar_mapa_longitud'
    });
@else
    seleccionar_ubicacion('{{$prestamoahorro->clienteubigeoubicacion}}');
@endif
function seleccionar_ubicacion(address) {
    singleMap_address({
        'map' : '#laboral_editar_mapa',
        'address' : address,
        'result_lat' : '#laboral_editar_mapa_latitud',
        'result_lng' : '#laboral_editar_mapa_longitud'
    });
}

</script>