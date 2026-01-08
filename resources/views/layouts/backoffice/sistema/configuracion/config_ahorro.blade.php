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
            view: 'config_ahorro',
            prestamo_ahorro_tipoahorrolibre: seleccinar_tipoahorrolibre()
        }
    },
    function(resultado){
        location.reload();                                                                        
    },this)">
  <div class="tabs-container" id="tab-modulo">
      <ul class="tabs-menu">
          <li class="current"><a href="#tab-modulo-1">Solicitud</a></li>
          <li><a href="#tab-modulo-2">Aprobación</a></li>
          <li><a href="#tab-modulo-5">Mora</a></li>
      </ul>
      <div class="tab">
          <div id="tab-modulo-1" class="tab-content" style="display: block;">
              <div class="tabs-container" id="tab-modulosolicitud">
                  <ul class="tabs-menu">
                      <li class="current"><a href="#tab-modulosolicitud-1">General</a></li>
                  </ul>
                  <div class="tab">
                      <div id="tab-modulosolicitud-1" class="tab-content" style="display: block;">
                          <div class="row">
                              <div class="col-sm-6">
                                  <label>Tipo de Interes</label>
                                  <select id="prestamo_ahorro_tasapordefecto">
                                    <option></option>
                                    @foreach($prestamotipotasas as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                  </select>
                                  <label>Estado de Ahorro</label>
                                  <select id="prestamo_ahorro_estadoahorro">
                                    <option></option>
                                    <option value="1">Habilitado</option>
                                    <option value="2">Bloqueado</option>
                                  </select>
                              </div>
                              <div class="col-sm-6">
                                  <label>Tipos Ahorro Libre</label>
                                  <table class="table" id="tabla-tipoahorrolibre">
                                      <thead class="thead-dark">
                                        <tr>
                                          <th>Nombre</th>
                                          <th width="10px">Producto</th>
                                          <th width="10px" style="padding: 0px;padding-right: 1px;">
                                          <a href="javascript:;" class="btn  color-bg flat-btn" onclick="tipoahorrolibre_agregar()"><i class="fa fa-plus"></i></a>
                                          </th>
                                        </tr>
                                      </thead>
                                      <tbody num="0"></tbody>
                                  </table>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          <div id="tab-modulo-2" class="tab-content" style="display: none;">
          </div>
          <div id="tab-modulo-5" class="tab-content" style="display: none;">
              <div class="mensaje-info">
                <i class="fa fa-warning"></i> ¿Esta seguro de activar la "Mora"</b>?
              </div>
              <div style="width: 100%;text-align: center;float: left;margin-bottom: 5px;">
              <div class="onoffswitch" style="margin: auto;">
                  <input type="checkbox" class="onoffswitch-checkbox prestamo_ahorro_estadomora" id="prestamo_ahorro_estadomora"  {{configuracion($tienda->id,'prestamo_ahorro_estadomora')['valor']=='on'?'checked':''}}>
                  <label class="onoffswitch-label" for="prestamo_ahorro_estadomora">
                  <span class="onoffswitch-inner"></span>
                  <span class="onoffswitch-switch"></span>
                  </label>
              </div>
              </div>
              <div id="cont-prestamo_ahorro_estadomora" <?php echo configuracion($tienda->id,'prestamo_ahorro_estadomora')['valor']=='on'?'':'style="display:none;"'?>>
                 <div class="row">
                     <div class="col-sm-12">
                         <label>Mora por Defecto *</label>
                         <select id="prestamo_ahorro_morapordefecto">
                           <option></option>
                           <option value="1">Mora Fija</option>
                           <option value="2">Mora Efectiva (%)</option>
                         </select>
                        <div id="cont-morapordefecto1" style="display:none;">
                         <label>Tipo *</label>
                         <select id="prestamo_ahorro_moratipo">
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
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_ahorro_mora_diario')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_ahorro_mora_diario')['valor']:0, 2, '.', '') }}" id="prestamo_ahorro_mora_diario" step="0.01"></td>
                                 </tr>
                                 <tr>
                                     <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Semanal</td>
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_ahorro_mora_semanal')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_ahorro_mora_semanal')['valor']:0, 2, '.', '') }}" id="prestamo_ahorro_mora_semanal" step="0.01"></td>
                                 </tr>
                                 <tr>
                                     <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Quincenal</td>
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_ahorro_mora_quincenal')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_ahorro_mora_quincenal')['valor']:0, 2, '.', '') }}" id="prestamo_ahorro_mora_quincenal" step="0.01"></td>
                                 </tr>
                                 <tr>
                                     <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Mensual</td>
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_ahorro_mora_mensual')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_ahorro_mora_mensual')['valor']:0, 2, '.', '') }}" id="prestamo_ahorro_mora_mensual" step="0.01"></td>
                                 </tr>
                                 <tr>
                                     <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Programado</td>
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_ahorro_mora_programado')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_ahorro_mora_programado')['valor']:0, 2, '.', '') }}" id="prestamo_ahorro_mora_programado" step="0.01"></td>
                                 </tr>
                             </tbody>
                         </table>
                        </div>
                        <div id="cont-moratipo2" style="display:none;">
                        <input type="hidden" id="prestamo_ahorro_morarango">
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
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_ahorro_mora_diario_efectiva')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_ahorro_mora_diario_efectiva')['valor']:0, 2, '.', '') }}" id="prestamo_ahorro_mora_diario_efectiva" step="0.01"></td>
                                 </tr>
                                 <tr>
                                     <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Semanal</td>
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_ahorro_mora_semanal_efectiva')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_ahorro_mora_semanal_efectiva')['valor']:0, 2, '.', '') }}" id="prestamo_ahorro_mora_semanal_efectiva" step="0.01"></td>
                                 </tr>
                                 <tr>
                                     <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Quincenal</td>
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_ahorro_mora_quincenal_efectiva')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_ahorro_mora_quincenal_efectiva')['valor']:0, 2, '.', '') }}" id="prestamo_ahorro_mora_quincenal_efectiva" step="0.01"></td>
                                 </tr>
                                 <tr>
                                     <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Mensual</td>
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_ahorro_mora_mensual_efectiva')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_ahorro_mora_mensual_efectiva')['valor']:0, 2, '.', '') }}" id="prestamo_ahorro_mora_mensual_efectiva" step="0.01"></td>
                                 </tr>
                                 <tr>
                                     <td style="background-color: #eae7e7;padding: 10px;width: 150px;font-weight: bold;">Programado</td>
                                     <td><input type="number" value="{{ number_format(configuracion($tienda->id,'prestamo_ahorro_mora_programado_efectiva')['resultado']=='CORRECTO'?configuracion($tienda->id,'prestamo_ahorro_mora_programado_efectiva')['valor']:0, 2, '.', '') }}" id="prestamo_mora_programado_efectiva" step="0.01"></td>
                                 </tr>
                             </tbody>
                         </table>
                        </div>
                     </div>
                 </div>
              </div>
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
  @if(configuracion($tienda->id,'prestamo_ahorro_tipoahorrolibre')['resultado']=='CORRECTO')
  @foreach(json_decode(configuracion($tienda->id,'prestamo_ahorro_tipoahorrolibre')['valor']) as $value)
      tipoahorrolibre_agregar('{{$value->tipoahorrolibre}}','{{isset($value->tipoahorrolibreproducto)?$value->tipoahorrolibreproducto:''}}');
  @endforeach
  @endif
  function seleccinar_tipoahorrolibre(){
     var data = [];
     $("#tabla-tipoahorrolibre > tbody > tr").each(function() {
          var num = $(this).attr('id');  
          data.push({
            tipoahorrolibre:  $("#tipoahorrolibre"+num).val(),
            tipoahorrolibreproducto:    $('#tipoahorrolibreproducto'+num+':checked').val()
          });
      });
      if(data.length==0){
          return '';
      }else{
          return JSON.stringify(data);
      }
  }
  function tipoahorrolibre_agregar(tipoahorrolibre='',tipoahorrolibreproducto=''){
      var num = $("#tabla-tipoahorrolibre > tbody").attr('num');
      var checked = '';
      if(tipoahorrolibreproducto=='on'){
          var checked = 'checked';
      }
      $('#tabla-tipoahorrolibre > tbody').append('<tr id="'+num+'">'+
                                                   '<td class="mx-td-input"><input id="tipoahorrolibre'+num+'" type="text" value="'+tipoahorrolibre+'" onkeyup="texto_mayucula(this)"></td>'+
                                                   '<td>'+
                                                     '<div class="onoffswitch">'+
                                                         '<input type="checkbox" class="onoffswitch-checkbox tipoahorrolibreproducto'+num+'" id="tipoahorrolibreproducto'+num+'" onclick="creditoCalendario()" '+checked+'>'+
                                                         '<label class="onoffswitch-label" for="tipoahorrolibreproducto'+num+'">'+
                                                             '<span class="onoffswitch-inner"></span>'+
                                                             '<span class="onoffswitch-switch"></span>'+
                                                         '</label> '+
                                                     '</div>'+
                                                   '</td>'+
                                                   '<td class="mx-td-input"><a id="del'+num+'" href="javascript:;" onclick="eliminartipoahorrolibre('+num+')" class="btn btn-danger big-btn" style="padding: 12px 15px;"><i class="fa fa-close"></i></a></td>'+
                                               '</tr>');
      $("#tabla-tipoahorrolibre > tbody").attr('num',parseInt(num)+1);
  }
  function eliminartipoahorrolibre(num){
    $("#tabla-tipoahorrolibre > tbody > tr#"+num).remove();
  }
  
  @if(configuracion($tienda->id,'prestamo_ahorro_tasapordefecto')['resultado']=='CORRECTO')
      $("#prestamo_ahorro_tasapordefecto").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1,
          allowClear: true
      }).val({{ configuracion($tienda->id,'prestamo_ahorro_tasapordefecto')['valor'] }}).trigger("change");    
  @else
      $("#prestamo_ahorro_tasapordefecto").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1,
          allowClear: true
      });
  @endif
  @if(configuracion($tienda->id,'prestamo_ahorro_estadoahorro')['resultado']=='CORRECTO')
      $("#prestamo_ahorro_estadoahorro").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1,
          allowClear: true
      }).val({{ configuracion($tienda->id,'prestamo_ahorro_estadoahorro')['valor'] }}).trigger("change");    
  @else
      $("#prestamo_ahorro_estadoahorro").select2({
          placeholder: "--  Seleccionar --",
          minimumResultsForSearch: -1,
          allowClear: true
      });
  @endif
</script>
<script>
  // -----------------------------> Aprobación
</script>
<script>
  // -----------------------------> Mora
  $("#prestamo_ahorro_estadomora").click(function(){
      $('#cont-prestamo_ahorro_estadomora').css('display','none');
      var checked = $("#prestamo_ahorro_estadomora:checked").val();
      if(checked=='on'){
          $('#cont-prestamo_ahorro_estadomora').css('display','block');
      }
  });
  @if(configuracion($tienda->id,'prestamo_ahorro_morapordefecto')['resultado']=='CORRECTO')
      $("#prestamo_ahorro_morapordefecto").select2({
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
      }).val({{ configuracion($tienda->id,'prestamo_ahorro_morapordefecto')['valor'] }}).trigger("change");    
  @else
      $("#prestamo_ahorro_morapordefecto").select2({
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
  @if(configuracion($tienda->id,'prestamo_ahorro_moratipo')['resultado']=='CORRECTO')
      $("#prestamo_ahorro_moratipo").select2({
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
      }).val({{ configuracion($tienda->id,'prestamo_ahorro_moratipo')['valor'] }}).trigger("change");    
  @else
      $("#prestamo_ahorro_moratipo").select2({
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
@endsection