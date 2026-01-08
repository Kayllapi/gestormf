@extends('layouts.backoffice.tienda.sistema.master')
@section('cuerpotiendasistema')
@include('app.sistema.cabecera',[
    'nombre' => 'Configuración de Prestamo',
    'botones'=>[
        'atras:/'.$tienda->id.'/configuracion/:Ir Atras'
    ]
])
<div id="carga-prestamo">
<div class="tabs-container" id="tab-menuconfiguracionprestamo">
    <ul class="tabs-menu">
        <li class="current"><a href="#tab-menuconfiguracionprestamo-0">General</a></li>
        <li><a href="#tab-menuconfiguracionprestamo-2">Dias Feriados</a></li>
        <li><a href="#tab-menuconfiguracionprestamo-3">Documentos</a></li>
    </ul>
    <div class="tab">
        <div id="tab-menuconfiguracionprestamo-0" class="tab-content" style="display: block;">
            
            <div class="tabs-container" id="tab-menuconfiguracionprestamo-general">
                <ul class="tabs-menu">
                    <li class="current"><a href="#tab-menuconfiguracionprestamo-general-0">General</a></li>
                    <li><a href="#tab-menuconfiguracionprestamo-general-1">Días de Gracia</a></li>
                    <li><a href="#tab-menuconfiguracionprestamo-general-2">Seguro Desgravamen</a></li>
                    <li><a href="#tab-menuconfiguracionprestamo-general-3">Gastos Administrativos</a></li>
                    <li><a href="#tab-menuconfiguracionprestamo-general-4">Moras</a></li>
                    <li><a href="#tab-menuconfiguracionprestamo-general-5">Tarje de Pago</a></li>
                </ul>
                <div class="tab">
                    <div id="tab-menuconfiguracionprestamo-general-0" class="tab-content" style="display: block;">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label>Estado de Tasa *</label>
                                        <select id="idestadotasa">
                                          <option></option>
                                          <option value="1">Habilitado</option>
                                          <option value="2">Bloqueado</option>
                                        </select>
                                        <label>Tasa por Defecto *</label>
                                        <select id="idtasapordefecto">
                                          <option></option>
                                          <option value="1">Interes Fija</option>
                                          <option value="2">Interes Efectiva (%)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="tab-menuconfiguracionprestamo-general-1" class="tab-content" style="display: none;">

                       <div class="mensaje-info">
                         <i class="fa fa-warning"></i> ¿Esta seguro de activar los "Días de Gracia"</b>?
                       </div>
                       <div style="width: 100%;text-align: center;float: left;margin-bottom: 5px;">
                       <div class="onoffswitch" style="margin: auto;">
                           <input type="checkbox" class="onoffswitch-checkbox idestadodias_gracia" id="idestadodias_gracia"  {{$configuracion['idestadodias_gracia']==1?'checked':''}}>
                           <label class="onoffswitch-label" for="idestadodias_gracia">
                           <span class="onoffswitch-inner"></span>
                           <span class="onoffswitch-switch"></span>
                           </label>
                       </div>
                       </div>
                       <div id="cont-idestadodias_gracia" <?php echo $configuracion['idestadodias_gracia']==1?'':'style="display:none;"'?>>
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
                                                <td><input type="number" value="{{ $configuracion['dias_gracia_diario']!=null?$configuracion['dias_gracia_diario']:'0' }}" id="dias_gracia_diario"></td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">Semanal</td>
                                                <td><input type="number" value="{{ $configuracion['dias_gracia_semanal']!=null?$configuracion['dias_gracia_semanal']:'0' }}" id="dias_gracia_semanal"></td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">Quincenal</td>
                                                <td><input type="number" value="{{ $configuracion['dias_gracia_quincenal']!=null?$configuracion['dias_gracia_quincenal']:'0' }}" id="dias_gracia_quincenal"></td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">Mensual</td>
                                                <td><input type="number" value="{{ $configuracion['dias_gracia_mensual']!=null?$configuracion['dias_gracia_mensual']:'0' }}" id="dias_gracia_mensual"></td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">Programado</td>
                                                <td><input type="number" value="{{ $configuracion['dias_gracia_programado']!=null?$configuracion['dias_gracia_programado']:'0' }}" id="dias_gracia_programado"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                              </div>
                          </div>
                       </div>
                    </div>
                    <div id="tab-menuconfiguracionprestamo-general-2" class="tab-content" style="display: none;">

                       <div class="mensaje-info">
                         <i class="fa fa-warning"></i> ¿Esta seguro de activar el "Seguro Desgravamen"</b>?
                       </div>
                       <div style="width: 100%;text-align: center;float: left;margin-bottom: 5px;">
                       <div class="onoffswitch" style="margin: auto;">
                           <input type="checkbox" class="onoffswitch-checkbox idestadoseguro_degravamen" id="idestadoseguro_degravamen"  {{$configuracion['idestadoseguro_degravamen']==1?'checked':''}}>
                           <label class="onoffswitch-label" for="idestadoseguro_degravamen">
                           <span class="onoffswitch-inner"></span>
                           <span class="onoffswitch-switch"></span>
                           </label>
                       </div>
                       </div>
                       <div id="cont-idestadoseguro_degravamen" <?php echo $configuracion['idestadoseguro_degravamen']==1?'':'style="display:none;"'?>>
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
                                                <td><input type="number" value="{{ $configuracion['seguro_degravamen']!=null?$configuracion['seguro_degravamen']:'0' }}" id="seguro_degravamen" step="0.01"></td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">Semanal</td>
                                                <td><input type="number" value="{{ $configuracion['seguro_degravamen_semanal']!=null?$configuracion['seguro_degravamen_semanal']:'0' }}" id="seguro_degravamen_semanal" step="0.01"></td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">Quincenal</td>
                                                <td><input type="number" value="{{ $configuracion['seguro_degravamen_quincenal']!=null?$configuracion['seguro_degravamen_quincenal']:'0' }}" id="seguro_degravamen_quincenal" step="0.01"></td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">Mensual</td>
                                                <td><input type="number" value="{{ $configuracion['seguro_degravamen_mensual']!=null?$configuracion['seguro_degravamen_mensual']:'0' }}" id="seguro_degravamen_mensual" step="0.01"></td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">Programado</td>
                                                <td><input type="number" value="{{ $configuracion['seguro_degravamen_programado']!=null?$configuracion['seguro_degravamen_programado']:'0' }}" id="seguro_degravamen_programado" step="0.01"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                              </div>
                          </div>
                       </div>
                    </div>
                    <div id="tab-menuconfiguracionprestamo-general-3" class="tab-content" style="display: none;">
                      <div class="mensaje-info">
                         <i class="fa fa-warning"></i> ¿Esta seguro de activar los "Gastos Administrativos"</b>?
                       </div>
                       <div style="width: 100%;text-align: center;float: left;margin-bottom: 5px;">
                       <div class="onoffswitch" style="margin: auto;">
                           <input type="checkbox" class="onoffswitch-checkbox idestadogasto_administrativo" id="idestadogasto_administrativo"  {{$configuracion['idestadogasto_administrativo']==1?'checked':''}}>
                           <label class="onoffswitch-label" for="idestadogasto_administrativo">
                           <span class="onoffswitch-inner"></span>
                           <span class="onoffswitch-switch"></span>
                           </label>
                       </div>
                       </div>
                       <div id="cont-idestadogasto_administrativo" <?php echo $configuracion['idestadogasto_administrativo']==1?'':'style="display:none;"'?>>
                          <div class="row">
                              <div class="col-sm-12">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td style="background-color: #31353c;padding: 10px;width: 50%;font-weight: bold;color: #fff;">Rango de Crédito</td>
                                            <td style="background-color: #31353c;padding: 10px;width: 50%;font-weight: bold;color: #fff;">Monto</td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">De S/ 0 - S/ 500.00</td>
                                            <td><input type="number" value="{{ $configuracion['gasto_administrativo_uno']!=null?$configuracion['gasto_administrativo_uno']:'0' }}" id="gasto_administrativo_uno" step="0.01"></td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">De S/ 500.01 - S/ 1000.00</td>
                                            <td><input type="number" value="{{ $configuracion['gasto_administrativo_dos']!=null?$configuracion['gasto_administrativo_dos']:'0' }}" id="gasto_administrativo_dos" step="0.01"></td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">De S/ 1000.01 - S/ 2000.00</td>
                                            <td><input type="number" value="{{ $configuracion['gasto_administrativo_tres']!=null?$configuracion['gasto_administrativo_tres']:'0' }}" id="gasto_administrativo_tres" step="0.01"></td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">De S/ 2000.01 - S/ 5000.00</td>
                                            <td><input type="number" value="{{ $configuracion['gasto_administrativo_cuatro']!=null?$configuracion['gasto_administrativo_cuatro']:'0' }}" id="gasto_administrativo_cuatro" step="0.01"></td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #eae7e7;padding: 10px;width: 50%;font-weight: bold;">De S/ 5000.01 a más</td>
                                            <td><input type="number" value="{{ $configuracion['gasto_administrativo_cinco']!=null?$configuracion['gasto_administrativo_cinco']:'0' }}" id="gasto_administrativo_cinco" step="0.01"></td>
                                        </tr>
                                    </tbody>
                                </table>
                              </div>
                          </div>
                      </div>
                    </div>
                    <div id="tab-menuconfiguracionprestamo-general-4" class="tab-content" style="display: none;">
                       <div class="mensaje-info">
                         <i class="fa fa-warning"></i> ¿Esta seguro de activar la "Mora"</b>?
                       </div>
                       <div style="width: 100%;text-align: center;float: left;margin-bottom: 5px;">
                       <div class="onoffswitch" style="margin: auto;">
                           <input type="checkbox" class="onoffswitch-checkbox idestadomora" id="idestadomora"  {{$configuracion['idestadomora']==1?'checked':''}}>
                           <label class="onoffswitch-label" for="idestadomora">
                           <span class="onoffswitch-inner"></span>
                           <span class="onoffswitch-switch"></span>
                           </label>
                       </div>
                       </div>
                       <div id="cont-idestadomora" <?php echo $configuracion['idestadomora']==1?'':'style="display:none;"'?>>
                          <div class="row">
                              <div class="col-sm-12">
                                  <label>Mora por Defecto *</label>
                                  <select id="idmorapordefecto">
                                    <option></option>
                                    <option value="1">Mora Fija</option>
                                    <option value="2">Mora Efectiva (%)</option>
                                  </select>

                                  <table class="table">
                                      <tbody>
                                            <tr>
                                                <td style="background-color: #31353c;padding: 10px;width: 50%;font-weight: bold;color: #fff;">Frecuencia</td>
                                                <td style="background-color: #31353c;padding: 10px;width: 50%;font-weight: bold;color: #fff;">Monto</td>
                                            </tr>
                                          <tr>
                                              <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Diario</td>
                                              <td><input type="number" value="{{ $configuracion['mora_diario']!=null?$configuracion['mora_diario']:'0' }}" id="mora_diario" step="0.01"></td>
                                          </tr>
                                          <tr>
                                              <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Semanal</td>
                                              <td><input type="number" value="{{ $configuracion['mora_semanal']!=null?$configuracion['mora_semanal']:'0' }}" id="mora_semanal" step="0.01"></td>
                                          </tr>
                                          <tr>
                                              <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Quincenal</td>
                                              <td><input type="number" value="{{ $configuracion['mora_quincenal']!=null?$configuracion['mora_quincenal']:'0' }}" id="mora_quincenal" step="0.01"></td>
                                          </tr>
                                          <tr>
                                              <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Mensual</td>
                                              <td><input type="number" value="{{ $configuracion['mora_mensual']!=null?$configuracion['mora_mensual']:'0' }}" id="mora_mensual" step="0.01"></td>
                                          </tr>
                                          <tr>
                                              <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Programado</td>
                                              <td><input type="number" value="{{ $configuracion['mora_programado']!=null?$configuracion['mora_programado']:'0' }}" id="mora_programado" step="0.01"></td>
                                          </tr>
                                      </tbody>
                                  </table>
                              </div>
                          </div>
                       </div>
                    </div>
                    <div id="tab-menuconfiguracionprestamo-general-5" class="tab-content" style="display: none;">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label>Ubicación de Logo *</label>
                                        <select id="tarjetapago_ubicacionlogo">
                                          <option></option>
                                          <option value="1">Izquierda</option>
                                          <option value="2">Derecha</option>
                                          <option value="3">Centro</option>
                                        </select>
                                        <label>Ancho de Impresión (cm) *</label>
                                        <input type="text" value="{{ $configuracion['tarjetapago_anchoimpresion']!=null?$configuracion['tarjetapago_anchoimpresion']:'0' }}" id="tarjetapago_anchoimpresion">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn mx-btn-post" onclick="registrar_general()">Guardar Cambios</button>
            </div>
        </div>
        <div id="tab-menuconfiguracionprestamo-2" class="tab-content" style="display: none;">
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
        <div id="tab-menuconfiguracionprestamo-3" class="tab-content" style="display: none;">
            <div id="cont-tabla-documento">
                <div class="list-single-main-wrapper fl-wrap">
                    <div class="breadcrumbs gradient-bg fl-wrap">
                      <span>Documentos</span>
                      <a class="btn btn-warning" href="javascript:;" onclick="registrar_documento({{ $tienda->id }})"><i class="fa fa-angle-right"></i> Registrar</a></a>
                    </div>
                </div>
                @include('app.sistema.tabla',[
                    'tabla' => 'tabla-documentos',
                    'script' => 'scriptsapp3',
                    'route' => url('backoffice/tienda/sistema/'.$tienda->id.'/configuracion/show-indexdocumento'),
                    'thead' => [
                        ['data' => 'Nombre'],
                        ['data' => '', 'width' => '10px']
                    ],
                    'tbody' => [
                        ['data' => 'nombre'],
                        ['render' => 'opcion']
                    ],
                    'tfoot' => [
                        ['input' => ''],
                        ['input' => '']
                    ]
                ])
            </div>
            <div id="cont-resultado-documento"></div>
        </div>
    </div>
</div>
</div>
@endsection
@section('subscripts')
<script>
  // Tabulador de pestañas
  tab({click:'#tab-menuconfiguracionprestamo'});
  tab({click:'#tab-menuconfiguracionprestamo-general'});
  
  $("#idestadodias_gracia").click(function(){
      $('#cont-idestadodias_gracia').css('display','none');
      var checked = $("#idestadodias_gracia:checked").val();
      if(checked=='on'){
          $('#cont-idestadodias_gracia').css('display','block');
      }
  });
  $("#idestadoseguro_degravamen").click(function(){
      $('#cont-idestadoseguro_degravamen').css('display','none');
      var checked = $("#idestadoseguro_degravamen:checked").val();
      if(checked=='on'){
          $('#cont-idestadoseguro_degravamen').css('display','block');
      }
  });
  $("#idestadogasto_administrativo").click(function(){
      $('#cont-idestadogasto_administrativo').css('display','none');
      var checked = $("#idestadogasto_administrativo:checked").val();
      if(checked=='on'){
          $('#cont-idestadogasto_administrativo').css('display','block');
      }
  });
  $("#idestadomora").click(function(){
      $('#cont-idestadomora').css('display','none');
      var checked = $("#idestadomora:checked").val();
      if(checked=='on'){
          $('#cont-idestadomora').css('display','block');
      }
  });
</script>
<script>
  // dia feriados
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
  function registrar_general() {
      callback({
          route:  'backoffice/tienda/sistema/{{ $tienda->id }}/configuracion/{{ $configuracion["idprestamo"] }}',
          method: 'PUT',
          carga:  '#carga-prestamo',
          data:   {
              view: 'config_prestamo',
              idestadotasa: $('#idestadotasa').val(),
              idtasapordefecto: $('#idtasapordefecto').val(),
              idestadodias_gracia: $('#idestadodias_gracia:checked').val(),
              idestadoseguro_degravamen: $('#idestadoseguro_degravamen:checked').val(),
              idestadogasto_administrativo: $('#idestadogasto_administrativo:checked').val(),
              idestadomora: $('#idestadomora:checked').val(),
              tarjetapago_ubicacionlogo: $('#tarjetapago_ubicacionlogo').val(),
              tarjetapago_anchoimpresion: $('#tarjetapago_anchoimpresion').val(),
              dias_gracia_diario: $('#dias_gracia_diario').val(),
              dias_gracia_semanal: $('#dias_gracia_semanal').val(),
              dias_gracia_quincenal: $('#dias_gracia_quincenal').val(),
              dias_gracia_mensual: $('#dias_gracia_mensual').val(),
              dias_gracia_programado: $('#dias_gracia_programado').val(),
              seguro_degravamen: $('#seguro_degravamen').val(),
              seguro_degravamen_semanal: $('#seguro_degravamen_semanal').val(),
              seguro_degravamen_quincenal: $('#seguro_degravamen_quincenal').val(),
              seguro_degravamen_mensual: $('#seguro_degravamen_mensual').val(),
              seguro_degravamen_programado: $('#seguro_degravamen_programado').val(),
              gasto_administrativo_uno: $('#gasto_administrativo_uno').val(),
              gasto_administrativo_dos: $('#gasto_administrativo_dos').val(),
              gasto_administrativo_tres: $('#gasto_administrativo_tres').val(),
              gasto_administrativo_cuatro: $('#gasto_administrativo_cuatro').val(),
              gasto_administrativo_cinco: $('#gasto_administrativo_cinco').val(),
              idmorapordefecto: $('#idmorapordefecto').val(),
              mora_diario: $('#mora_diario').val(),
              mora_semanal: $('#mora_semanal').val(),
              mora_quincenal: $('#mora_quincenal').val(),
              mora_mensual: $('#mora_mensual').val(),
              mora_programado: $('#mora_programado').val(),
          }
      },
      function(resultado){
        location.reload();
      })
  }
</script>
<script>
  // documentos
  function index_documento() {
    $('#tabla-documentos').DataTable().ajax.reload();
    $('#cont-tabla-documento').css('display','block');
    $('#cont-resultado-documento').html('');
  }
  function registrar_documento(idtienda) {
      $('#cont-tabla-documento').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/configuracion/create?view=prestamo_registrardocumento',result:'#cont-resultado-documento'});
  }
  function editar_documento(idtienda,iddocumento) {
      $('#cont-tabla-documento').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/configuracion/'+iddocumento+'/edit?view=prestamo_editardocumento',result:'#cont-resultado-documento'});
  }
  function eliminar_documento(idtienda,iddocumento) {
      $('#cont-tabla-documento').css('display','none');
      pagina({route:'{{ url('backoffice/tienda/sistema') }}/'+idtienda+'/configuracion/'+iddocumento+'/edit?view=prestamo_eliminardocumento',result:'#cont-resultado-documento'});
  }
  
  
</script>
<script>
    @if($configuracion['idtasapordefecto']!=null)
        $("#idtasapordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val({{ $configuracion['idtasapordefecto'] }}).trigger("change");    
    @else
        $("#idtasapordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @endif
  
    
  
    @if($configuracion['idmorapordefecto']!=null)
        $("#idmorapordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val({{ $configuracion['idmorapordefecto'] }}).trigger("change");    
    @else
        $("#idmorapordefecto").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @endif
  
    @if($configuracion['idestadotasa']!=null)
        $("#idestadotasa").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val({{ $configuracion['idestadotasa'] }}).trigger("change");    
    @else
        $("#idestadotasa").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @endif
    @if($configuracion['tarjetapago_ubicacionlogo']!=null)
        $("#tarjetapago_ubicacionlogo").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        }).val({{ $configuracion['tarjetapago_ubicacionlogo'] }}).trigger("change");    
    @else
        $("#tarjetapago_ubicacionlogo").select2({
            placeholder: "--  Seleccionar --",
            minimumResultsForSearch: -1
        });
    @endif
</script>
<link href="https://kothing.github.io/editor/dist/css/kothing-editor.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.11.1/dist/katex.min.css"/>
<script src="https://kothing.github.io/editor/dist/kothing-editor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.11.1/dist/katex.min.js"></script>
@endsection