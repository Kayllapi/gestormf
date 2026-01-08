@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Configuración de Crédito',
    'botones'=>[
        'atras:/'.$tienda->id.'/configuracion/:Ir Atras'
    ]
])
<form action="javascript:;" 
      onsubmit="callback({
        route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/0',
        method: 'PUT',
        data:   {
            view: 'config_credito',
            prestamo_tipocredito: seleccinar_tipocredito()
        }
    },
    function(resultado){
        location.reload();                                                                        
    },this)">
  <div class="tabs-container" id="tab-modulo">
      <ul class="tabs-menu">
          <li class="current"><a href="#tab-modulo-1">Solicitud</a></li>
          <li><a href="#tab-modulo-2">Aprobación</a></li>
          <li><a href="#tab-modulo-3">Refinanciación</a></li>
          <li><a href="#tab-modulo-4">Reprogramación</a></li>
          <li><a href="#tab-modulo-5">Mora</a></li>
          <li><a href="#tab-modulo-6">Transferencia de Cartera</a></li>
      </ul>
      <div class="tab">
          <div id="tab-modulo-1" class="tab-content" style="display: block;">
              <div class="tabs-container" id="tab-modulosolicitud">
                  <ul class="tabs-menu">
                      <li class="current"><a href="#tab-modulosolicitud-1">General</a></li>
                      <li><a href="#tab-modulosolicitud-2">Días de Gracia</a></li>
                      <li><a href="#tab-modulosolicitud-3">Seguro Desgravamen</a></li>
                      <li><a href="#tab-modulosolicitud-5">Abono</a></li>
                      <li><a href="#tab-modulosolicitud-4">Días Feriados</a></li>
                  </ul>
                  <div class="tab">
                      <div id="tab-modulosolicitud-1" class="tab-content" style="display: block;">
                          <div class="row">
                              <div class="col-sm-6">
                                  <label>Tipo de Interes</label>
                                  <select id="prestamo_tasapordefecto">
                                    <option></option>
                                    @foreach($prestamotipotasas as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                  </select>
                                  <label>Estado de Acumulado</label>
                                  <select id="prestamo_estadoacumulado">
                                    <option></option>
                                    <option value="1">Habilitado</option>
                                    <option value="2">Bloqueado</option>
                                  </select>
                                  <label>Estado de Crédito Grupal</label>
                                  <select id="prestamo_estadocreditogrupal">
                                    <option></option>
                                    <option value="1">Habilitado</option>
                                    <option value="2">Bloqueado</option>
                                  </select>
                                  <label>Estado de Crédito Prendario</label>
                                  <select id="prestamo_estadocreditoprendario">
                                    <option></option>
                                    <option value="1">Habilitado</option>
                                    <option value="2">Bloqueado</option>
                                  </select>
                              </div>
                              <div class="col-sm-6">
                                  <label>Tipos de Crédito</label>
                                  <table class="table" id="tabla-tipocredito">
                                      <thead class="thead-dark">
                                        <tr>
                                          <th>Nombre</th>
                                          <th width="10px" style="padding: 0px;padding-right: 1px;">
                                          <a href="javascript:;" class="btn  color-bg flat-btn" onclick="tipocredito_agregar()"><i class="fa fa-plus"></i></a>
                                          </th>
                                        </tr>
                                      </thead>
                                      <tbody num="0"></tbody>
                                  </table>
                              </div>
                          </div>
                      </div>
                      <div id="tab-modulosolicitud-2" class="tab-content" style="display: none;">
                         <div class="mensaje-info">
                           <i class="fa fa-warning"></i> ¿Esta seguro de activar los "Días de Gracia"</b>?
                         </div>
                         <div style="width: 100%;text-align: center;float: left;margin-bottom: 5px;">
                         <div class="onoffswitch" style="margin: auto;">
                             <input type="checkbox" class="onoffswitch-checkbox prestamo_estadodias_gracia" id="prestamo_estadodias_gracia"  {{configuracion($tienda->id,'prestamo_estadodias_gracia')['valor']=='on'?'checked':''}}>
                             <label class="onoffswitch-label" for="prestamo_estadodias_gracia">
                             <span class="onoffswitch-inner"></span>
                             <span class="onoffswitch-switch"></span>
                             </label>
                         </div>
                         </div>
                         <div id="cont-prestamo_estadodias_gracia" <?php echo configuracion($tienda->id,'prestamo_estadodias_gracia')['valor']=='on'?'':'style="display:none;"'?>>
                            <div class="row">
                                <div class="col-sm-12">
                                      <table class="table">
                                          <tbody>
                                              <tr>
                                                  <td style="background-color: #31353c;padding: 10px;width: 50%;font-weight: bold;color: #fff;">Frecuencia</td>
                                                  <td style="background-color: #31353c;padding: 10px;width: 50%;font-weight: bold;color: #fff;">Dias</td>
                                              </tr>
                                              <tr>
                                                  <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">Diario</td>
                                                  <td><input type="number" value="{{ configuracion($tienda->id,'prestamo_dias_gracia_diario')['valor'] }}" id="prestamo_dias_gracia_diario"></td>
                                              </tr>
                                              <tr>
                                                  <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">Semanal</td>
                                                  <td><input type="number" value="{{ configuracion($tienda->id,'prestamo_dias_gracia_semanal')['valor'] }}" id="prestamo_dias_gracia_semanal"></td>
                                              </tr>
                                              <tr>
                                                  <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">Quincenal</td>
                                                  <td><input type="number" value="{{ configuracion($tienda->id,'prestamo_dias_gracia_quincenal')['valor'] }}" id="prestamo_dias_gracia_quincenal"></td>
                                              </tr>
                                              <tr>
                                                  <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">Mensual</td>
                                                  <td><input type="number" value="{{ configuracion($tienda->id,'prestamo_dias_gracia_mensual')['valor'] }}" id="prestamo_dias_gracia_mensual"></td>
                                              </tr>
                                              <tr>
                                                  <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">Programado</td>
                                                  <td><input type="number" value="{{ configuracion($tienda->id,'prestamo_dias_gracia_programado')['valor'] }}" id="prestamo_dias_gracia_programado"></td>
                                              </tr>
                                          </tbody>
                                      </table>
                                </div>
                            </div>
                         </div>
                      </div>
                      <div id="tab-modulosolicitud-3" class="tab-content" style="display: none;">
                         <div class="mensaje-info">
                           <i class="fa fa-warning"></i> ¿Esta seguro de activar el "Seguro Desgravamen"</b>?
                         </div>
                         <div style="width: 100%;text-align: center;float: left;margin-bottom: 5px;">
                         <div class="onoffswitch" style="margin: auto;">
                             <input type="checkbox" class="onoffswitch-checkbox prestamo_estadoseguro_degravamen" id="prestamo_estadoseguro_degravamen"  {{configuracion($tienda->id,'prestamo_estadoseguro_degravamen')['valor']=='on'?'checked':''}}>
                             <label class="onoffswitch-label" for="prestamo_estadoseguro_degravamen">
                             <span class="onoffswitch-inner"></span>
                             <span class="onoffswitch-switch"></span>
                             </label>
                         </div>
                         </div>
                         <div id="cont-prestamo_estadoseguro_degravamen" <?php echo configuracion($tienda->id,'prestamo_estadoseguro_degravamen')['valor']=='on'?'':'style="display:none;"'?>>
                            <div class="row">
                                <div class="col-sm-12">
                                      <table class="table">
                                          <tbody>
                                              <tr>
                                                  <td style="background-color: #31353c;padding: 10px;width: 50%;font-weight: bold;color: #fff;">Frecuencia</td>
                                                  <td style="background-color: #31353c;padding: 10px;width: 50%;font-weight: bold;color: #fff;">Monto</td>
                                              </tr>
                                              <tr>
                                                  <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">Diario</td>
                                                  <td><input type="number" value="{{ configuracion($tienda->id,'prestamo_seguro_degravamen_diario')['valor'] }}" id="prestamo_seguro_degravamen_diario" step="0.01"></td>
                                              </tr>
                                              <tr>
                                                  <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">Semanal</td>
                                                  <td><input type="number" value="{{ configuracion($tienda->id,'prestamo_seguro_degravamen_semanal')['valor'] }}" id="prestamo_seguro_degravamen_semanal" step="0.01"></td>
                                              </tr>
                                              <tr>
                                                  <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">Quincenal</td>
                                                  <td><input type="number" value="{{ configuracion($tienda->id,'prestamo_seguro_degravamen_quincenal')['valor'] }}" id="prestamo_seguro_degravamen_quincenal" step="0.01"></td>
                                              </tr>
                                              <tr>
                                                  <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">Mensual</td>
                                                  <td><input type="number" value="{{ configuracion($tienda->id,'prestamo_seguro_degravamen_mensual')['valor'] }}" id="prestamo_seguro_degravamen_mensual" step="0.01"></td>
                                              </tr>
                                              <tr>
                                                  <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">Programado</td>
                                                  <td><input type="number" value="{{ configuracion($tienda->id,'prestamo_seguro_degravamen_programado')['valor'] }}" id="prestamo_seguro_degravamen_programado" step="0.01"></td>
                                              </tr>
                                          </tbody>
                                      </table>
                                </div>
                            </div>
                         </div>
                      </div>
                      <div id="tab-modulosolicitud-5" class="tab-content" style="display: none;">
                         <div class="mensaje-info">
                           <i class="fa fa-warning"></i> ¿Esta seguro de activar el "Abono"</b>?
                         </div>
                         <div style="width: 100%;text-align: center;float: left;margin-bottom: 5px;">
                         <div class="onoffswitch" style="margin: auto;">
                             <input type="checkbox" class="onoffswitch-checkbox prestamo_estadoabono" id="prestamo_estadoabono"  {{configuracion($tienda->id,'prestamo_estadoabono')['valor']=='on'?'checked':''}}>
                             <label class="onoffswitch-label" for="prestamo_estadoabono">
                             <span class="onoffswitch-inner"></span>
                             <span class="onoffswitch-switch"></span>
                             </label>
                         </div>
                         </div>
                      </div>
                      <div id="tab-modulosolicitud-4" class="tab-content" style="display: none;">
                          <div id="cont-tabla-diaferiado">
                            <div class="list-single-main-wrapper fl-wrap">
                                <div class="breadcrumbs gradient-bg fl-wrap">
                                  <span>Días Feriados</span>
                                  <a class="btn btn-warning" href="javascript:;" onclick="registrar_diaferiado({{ $tienda->id }})"><i class="fa fa-angle-right"></i> Registrar</a></a>
                                </div>
                            </div>
                            @include('app.sistema.tabla',[
                                'tabla' => 'tabla-diaferiados',
                                'script' => 'scriptsapp1',
                                'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/configuracion/show-indexdiaferiado'),
                                'thead' => [
                                    ['data' => 'Fecha (Día / Año)'],
                                    ['data' => 'Motivo'],
                                    ['data' => '', 'width' => '10px']
                                ],
                                'tbody' => [
                                    ['data' => 'diaferiado'],
                                    ['data' => 'motivo'],
                                    ['render' => 'opcion'],
                                ],
                                'tfoot' => [
                                    ['input' => ''],
                                    ['input' => ''],
                                    ['input' => '']
                                ]
                            ])
                          </div>
                          <div id="cont-resultado-diaferiado"></div>
                      </div>
                  </div>
              </div>
          </div>
          <div id="tab-modulo-2" class="tab-content" style="display: none;">
          </div>
          <div id="tab-modulo-3" class="tab-content" style="display: none;">
          </div>
          <div id="tab-modulo-4" class="tab-content" style="display: none;">
          </div>
          <div id="tab-modulo-5" class="tab-content" style="display: none;">
              <div class="mensaje-info">
                <i class="fa fa-warning"></i> ¿Esta seguro de activar la "Mora"</b>?
              </div>
              <div style="width: 100%;text-align: center;float: left;margin-bottom: 5px;">
              <div class="onoffswitch" style="margin: auto;">
                  <input type="checkbox" class="onoffswitch-checkbox prestamo_estadomora" id="prestamo_estadomora"  {{configuracion($tienda->id,'prestamo_estadomora')['valor']=='on'?'checked':''}}>
                  <label class="onoffswitch-label" for="prestamo_estadomora">
                  <span class="onoffswitch-inner"></span>
                  <span class="onoffswitch-switch"></span>
                  </label>
              </div>
              </div>
              <div id="cont-prestamo_estadomora" <?php echo configuracion($tienda->id,'prestamo_estadomora')['valor']=='on'?'':'style="display:none;"'?>>
                 <div class="row">
                     <div class="col-sm-12">
                         <label>Mora por Defecto *</label>
                         <select id="prestamo_morapordefecto">
                           <option></option>
                           <option value="1">Mora Fija</option>
                           <option value="2">Mora Efectiva (%)</option>
                         </select>
                        <div id="cont-morapordefecto1" style="display:none;">
                         <label>Tipo *</label>
                         <select id="prestamo_moratipo">
                           <option></option>
                           <option value="1">Por Frecuencia de Pagos</option>
                           <option value="2">Por Rango de Montos</option>
                         </select>
                        <div id="cont-moratipo1" style="display:none;">
                        <table class="table">
                             <tbody>
                                   <tr>
                                       <td style="background-color: #31353c;padding: 10px;width: 50%;font-weight: bold;color: #fff;">Frecuencia</td>
                                       <td style="background-color: #31353c;padding: 10px;width: 50%;font-weight: bold;color: #fff;">Mora x Día (Monto)</td>
                                   </tr>
                                 <tr>
                                     <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Diario</td>
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_mora_diario')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_mora_diario')['valor']:0, 2, '.', '') }}" id="prestamo_mora_diario" step="0.01"></td>
                                 </tr>
                                 <tr>
                                     <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Semanal</td>
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_mora_semanal')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_mora_semanal')['valor']:0, 2, '.', '') }}" id="prestamo_mora_semanal" step="0.01"></td>
                                 </tr>
                                 <tr>
                                     <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Quincenal</td>
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_mora_quincenal')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_mora_quincenal')['valor']:0, 2, '.', '') }}" id="prestamo_mora_quincenal" step="0.01"></td>
                                 </tr>
                                 <tr>
                                     <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Mensual</td>
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_mora_mensual')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_mora_mensual')['valor']:0, 2, '.', '') }}" id="prestamo_mora_mensual" step="0.01"></td>
                                 </tr>
                                 <tr>
                                     <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Programado</td>
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_mora_programado')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_mora_programado')['valor']:0, 2, '.', '') }}" id="prestamo_mora_programado" step="0.01"></td>
                                 </tr>
                             </tbody>
                         </table>
                        </div>
                        <div id="cont-moratipo2" style="display:none;">
                        <input type="hidden" id="prestamo_morarango">
                        <div class="table-responsive">
                            <table class="table" id="tabla-moraporrango">
                                <thead class="thead-dark">
                                  <tr>
                                    <th>Rango de Monto</th>
                                    <th>Mora x Día (Monto)</th>
                                    <th width="10px" style="padding: 0px;padding-right: 1px;">
                                    <a href="javascript:;" class="btn  color-bg flat-btn" onclick="moraporrango_agregar()"><i class="fa fa-angle-right"></i> Agregar</a>
                                    </th>
                                  </tr>
                                </thead>
                                <tbody num="0"></tbody>
                            </table>
                        </div>
                        </div>
                        </div>
                        <div id="cont-morapordefecto2" style="display:none;">
                        <table class="table">
                             <tbody>
                                   <tr>
                                       <td style="background-color: #31353c;padding: 10px;width: 50%;font-weight: bold;color: #fff;">Frecuencia</td>
                                       <td style="background-color: #31353c;padding: 10px;width: 50%;font-weight: bold;color: #fff;">Mora x Día (%)</td>
                                   </tr>
                                 <tr>
                                     <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Diario</td>
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_mora_diario_efectiva')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_mora_diario_efectiva')['valor']:0, 2, '.', '') }}" id="prestamo_mora_diario_efectiva" step="0.01"></td>
                                 </tr>
                                 <tr>
                                     <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Semanal</td>
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_mora_semanal_efectiva')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_mora_semanal_efectiva')['valor']:0, 2, '.', '') }}" id="prestamo_mora_semanal_efectiva" step="0.01"></td>
                                 </tr>
                                 <tr>
                                     <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Quincenal</td>
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_mora_quincenal_efectiva')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_mora_quincenal_efectiva')['valor']:0, 2, '.', '') }}" id="prestamo_mora_quincenal_efectiva" step="0.01"></td>
                                 </tr>
                                 <tr>
                                     <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Mensual</td>
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_mora_mensual_efectiva')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_mora_mensual_efectiva')['valor']:0, 2, '.', '') }}" id="prestamo_mora_mensual_efectiva" step="0.01"></td>
                                 </tr>
                                 <tr>
                                     <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Programado</td>
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_mora_programado_efectiva')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_mora_programado_efectiva')['valor']:0, 2, '.', '') }}" id="prestamo_mora_programado_efectiva" step="0.01"></td>
                                 </tr>
                             </tbody>
                         </table>
                        </div>
                     </div>
                 </div>
              </div>
          </div>
          <div id="tab-modulo-6" class="tab-content" style="display: none;">
          </div>
      </div>
  </div>
  <button type="submit" class="btn mx-btn-post">Guardar Cambios</button>
</form>
@endsection
@section('subscripts')
<script>
  tab({click:'#tab-modulo'});
  tab({click:'#tab-modulosolicitud'});
</script>
<script>
  // -----------------------------> Solicitud
  
  @if(configuracion($tienda->id,'prestamo_tipocredito')['resultado']=='CORRECTO')
  @foreach(json_decode(configuracion($tienda->id,'prestamo_tipocredito')['valor']) as $value)
      tipocredito_agregar('{{$value->tipocredito}}');
  @endforeach
  @endif
  function seleccinar_tipocredito(){
     var data = [];
     $("#tabla-tipocredito > tbody > tr").each(function() {
          var num = $(this).attr('id');  
          data.push({
            tipocredito:      $("#tipocredito"+num).val(),
          });
      });
      if(data.length==0){
          return '';
      }else{
          return JSON.stringify(data);
      }
  }
  function tipocredito_agregar(tipocredito=''){
      var num = $("#tabla-tipocredito > tbody").attr('num');
      $('#tabla-tipocredito > tbody').append('<tr id="'+num+'">'+
                                                   '<td class="mx-td-input"><input id="tipocredito'+num+'" type="text" value="'+tipocredito+'" onkeyup="texto_mayucula(this)"></td>'+
                                                   '<td class="mx-td-input"><a id="del'+num+'" href="javascript:;" onclick="eliminartipocredito('+num+')" class="btn btn-danger big-btn" style="padding: 12px 15px;"><i class="fa fa-close"></i></a></td>'+
                                               '</tr>');
      $("#tabla-tipocredito > tbody").attr('num',parseInt(num)+1);
  }
  function eliminartipocredito(num){
    $("#tabla-tipocredito > tbody > tr#"+num).remove();
  }
  
  @if(configuracion($tienda->id,'prestamo_tasapordefecto')['resultado']=='CORRECTO')
      $("#prestamo_tasapordefecto").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1,
          allowClear: true
      }).val({{ configuracion($tienda->id,'prestamo_tasapordefecto')['valor'] }}).trigger("change");    
  @else
      $("#prestamo_tasapordefecto").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1,
          allowClear: true
      });
  @endif
  
  @if(configuracion($tienda->id,'prestamo_estadoacumulado')['resultado']=='CORRECTO')
      $("#prestamo_estadoacumulado").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1,
          allowClear: true
      }).val({{ configuracion($tienda->id,'prestamo_estadoacumulado')['valor'] }}).trigger("change");    
  @else
      $("#prestamo_estadoacumulado").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1,
          allowClear: true
      });
  @endif
  @if(configuracion($tienda->id,'prestamo_estadocreditogrupal')['resultado']=='CORRECTO')
      $("#prestamo_estadocreditogrupal").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1,
          allowClear: true
      }).val({{ configuracion($tienda->id,'prestamo_estadocreditogrupal')['valor'] }}).trigger("change");    
  @else
      $("#prestamo_estadocreditogrupal").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1,
          allowClear: true
      });
  @endif
  @if(configuracion($tienda->id,'prestamo_estadocreditoprendario')['resultado']=='CORRECTO')
      $("#prestamo_estadocreditoprendario").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1,
          allowClear: true
      }).val({{ configuracion($tienda->id,'prestamo_estadocreditoprendario')['valor'] }}).trigger("change");    
  @else
      $("#prestamo_estadocreditoprendario").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1,
          allowClear: true
      });
  @endif
  $("#prestamo_estadodias_gracia").click(function(){
      $('#cont-prestamo_estadodias_gracia').css('display','none');
      var checked = $("#prestamo_estadodias_gracia:checked").val();
      if(checked=='on'){
          $('#cont-prestamo_estadodias_gracia').css('display','block');
      }
  });
  $("#prestamo_estadoseguro_degravamen").click(function(){
      $('#cont-prestamo_estadoseguro_degravamen').css('display','none');
      var checked = $("#prestamo_estadoseguro_degravamen:checked").val();
      if(checked=='on'){
          $('#cont-prestamo_estadoseguro_degravamen').css('display','block');
      }
  });
  $("#prestamo_estadoabono").click(function(){
      $('#cont-prestamo_estadoabono').css('display','none');
      var checked = $("#prestamo_estadoabono:checked").val();
      if(checked=='on'){
          $('#cont-prestamo_estadoabono').css('display','block');
      }
  });
  function index_diaferiado() {
    $('#tabla-diaferiados').DataTable().ajax.reload();
    $('#cont-tabla-diaferiado').css('display','block');
    $('#cont-resultado-diaferiado').html('');
  }
  function registrar_diaferiado(idtienda) {
      $('#cont-tabla-diaferiado').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/configuracion/create?view=prestamo_registrardiaferiado',result:'#cont-resultado-diaferiado'});
  }
  function editar_diaferiado(idtienda,iddiaferiado) {
      $('#cont-tabla-diaferiado').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/configuracion/'+iddiaferiado+'/edit?view=prestamo_editardiaferiado',result:'#cont-resultado-diaferiado'});
  }
  function eliminar_diaferiado(idtienda,iddiaferiado) {
      $('#cont-tabla-diaferiado').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/configuracion/'+iddiaferiado+'/edit?view=prestamo_eliminardiaferiado',result:'#cont-resultado-diaferiado'});
  }
</script>
<script>
  // -----------------------------> Aprobación
</script>
<script>
  // -----------------------------> Refinanciación
</script>
<script>
  // -----------------------------> Reprogramación
</script>
<script>
  // -----------------------------> Mora
  $("#prestamo_estadomora").click(function(){
      $('#cont-prestamo_estadomora').css('display','none');
      var checked = $("#prestamo_estadomora:checked").val();
      if(checked=='on'){
          $('#cont-prestamo_estadomora').css('display','block');
      }
  });
  @if(configuracion($tienda->id,'prestamo_morapordefecto')['resultado']=='CORRECTO')
      $("#prestamo_morapordefecto").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      }).on("change", function (e) {
          $('#cont-morapordefecto1').css('display','none');
          $('#cont-morapordefecto2').css('display','none');
          if (e.currentTarget.value == 1) {
              $('#cont-morapordefecto1').css('display','block');
          }
          else if (e.currentTarget.value == 2) {
              $('#cont-morapordefecto2').css('display','block');
          }
      }).val({{ configuracion($tienda->id,'prestamo_morapordefecto')['valor'] }}).trigger("change");    
  @else
      $("#prestamo_morapordefecto").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      }).on("change", function (e) {
          $('#cont-morapordefecto1').css('display','none');
          $('#cont-morapordefecto2').css('display','none');
          if (e.currentTarget.value == 1) {
              $('#cont-morapordefecto1').css('display','block');
          }
          else if (e.currentTarget.value == 2) {
              $('#cont-morapordefecto2').css('display','block');
          }
      });
  @endif
  @if(configuracion($tienda->id,'prestamo_moratipo')['resultado']=='CORRECTO')
      $("#prestamo_moratipo").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      }).on("change", function (e) {
          $('#cont-moratipo1').css('display','none');
          $('#cont-moratipo2').css('display','none');
          if (e.currentTarget.value == 1) {
              $('#cont-moratipo1').css('display','block');
          }
          else if (e.currentTarget.value == 2) {
              $('#cont-moratipo2').css('display','block');
          }
      }).val({{ configuracion($tienda->id,'prestamo_moratipo')['valor'] }}).trigger("change");    
  @else
      $("#prestamo_moratipo").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1
      }).on("change", function (e) {
          $('#cont-moratipo1').css('display','none');
          $('#cont-moratipo2').css('display','none');
          if (e.currentTarget.value == 1) {
              $('#cont-moratipo1').css('display','block');
          }
          else if (e.currentTarget.value == 2) {
              $('#cont-moratipo2').css('display','block');
          }
      });
  @endif
  
  @if(configuracion($tienda->id,'prestamo_morarango')['resultado']=='CORRECTO')
  <?php $prestamo_morarango = json_decode(configuracion($tienda->id,'prestamo_morarango')['valor']) ?>
  @if(count($prestamo_morarango)>0)
  @foreach($prestamo_morarango as $value)
      moraporrango_agregar('{{number_format($value->morarango, 2, '.', '')}}','{{number_format($value->morarangomonto, 2, '.', '')}}');
  @endforeach
  @else
      moraporrango_agregar();
  @endif
  @else
      moraporrango_agregar();
  @endif
  
  function seleccinar_moraporrango(){
    var data = [];
    $("#tabla-moraporrango tbody tr").each(function() {
        var num = $(this).attr('id');        
        var morarango = $("#morarango"+num).val();
        var morarangomonto = $("#morarangomonto"+num).val();
        data.push({
            num : num,
            morarango : morarango,
            morarangomonto : morarangomonto
        });
    });
    $("#prestamo_morarango").val(JSON.stringify(data));
  }
  function moraporrango_agregar(morarango='',morarangomonto=''){
      var num = $("#tabla-moraporrango > tbody").attr('num');
      var btneliminar = '<a id="del'+num+'" href="javascript:;" onclick="eliminarmoraporrango('+num+')" class="btn btn-danger big-btn" style="padding: 10px 15px;"><i class="fa fa-close"></i></a>';
      if(num==0){
          btneliminar = '';
      }
      $('#tabla-moraporrango > tbody').append('<tr id="'+num+'">'+
                                                   '<td class="mx-td-input"><input id="morarango'+num+'" type="number" value="'+morarango+'" step="0.01" onkeyup="seleccinar_moraporrango()" onclick="seleccinar_moraporrango()"></td>'+
                                                   '<td class="mx-td-input"><input id="morarangomonto'+num+'" type="number" value="'+morarangomonto+'" step="0.01" onkeyup="seleccinar_moraporrango()" onclick="seleccinar_moraporrango()"></td>'+
                                                   '<td>'+btneliminar+'</td>'+
                                               '</tr>');
      $("#tabla-moraporrango > tbody").attr('num',parseInt(num)+1);
      seleccinar_moraporrango();
  }
  function eliminarmoraporrango(num){
    $("#tabla-moraporrango > tbody > tr#"+num).remove();
  }
</script>
<script>
  // -----------------------------> Transferencia de Cartera
</script>
@endsection